<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetBugetArticlesData($financier_role, $fundholder_id, $withArchiveArticles)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $condition = $financier_role ? "" : " WHERE e.id = '" . $fundholder_id . "'";

    if (!$withArchiveArticles) {
        if ($condition != "") {
            $condition .= " AND d.id != 27";
        } else {
            $condition = " WHERE d.id != 27";
        }
    }
    // инвест программа с закрытыми договорами

    $result = $mysql->query("SELECT a.id, d.name as main_section, c.name as section, b.name as subsection, e.name as fundholder, f.name as service, 
                                    a.name as article, g.id as new_code_id, g.new_code, h.id as old_code_id, h.old_code, 
                                    IF((SELECT count(i.id)  FROM planned_indicators i WHERE i.budget_articles_directory_id = a.id) > 0, false, true) as delete_mode
                                FROM `budget_articles_directory` a 
                                LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id 
                                LEFT JOIN sections_directory c ON c.id = b.sections_directory_id 
                                LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id 
                                LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id 
                                LEFT JOIN services_directory f ON f.id = a.services_directory_id 
                                LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id 
                                LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id" . $condition . "  ORDER BY if(d.name LIKE 'Інвест. діяльність', b.name, a.id) ASC");
    while ($row = $result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row["id"],
            "main_section" => $row["main_section"],
            "section" => $row["section"],
            "subsection" => $row["subsection"],
            "fundholder" => $row["fundholder"],
            "service" => $row["service"],
            "article" => $row["article"],
            "old_code_id" => $row["old_code_id"],
            "old_code" => $row["old_code"],
            "new_code_id" => $row["new_code_id"],
            "new_code" => $row["new_code"],
            "delete_mode" => $row["delete_mode"],
        ]);
    }
    $mysql->close();
    return $dataArray;
}

function CreateTableDOM($admin_role, $financier_role, $fundholder_id, $withArchiveArticles)
{
    $data = GetBugetArticlesData($financier_role, $fundholder_id, $withArchiveArticles);

    //шапка таблицы
    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id sticky-table-column'>№</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-main-section sticky-table-column'>Головний розділ</th>";
    echo "<th class='main-table-th table-column-section sticky-table-column'>Розділ</th>";
    echo "<th class='main-table-th table-column-subsection sticky-table-column'>Підрозділ</th>";
    echo "<th class='main-table-th table-column-fundholder sticky-table-column'>Фондоутримувач</th>";
    echo "<th class='main-table-th table-column-service sticky-table-column'>Служба</th>";
    echo "<th class='main-table-th table-column-article sticky-table-column'>Назва статті</th>";
    echo "<th class='main-table-th table-column-new-code sticky-table-column'>Код статті</th>";
    if ($financier_role)
        echo "<th class='main-table-th table-column-old-code sticky-table-column'>Код статті(старий)</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";

    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        $disabled = $data[$i]["delete_mode"] != 1 ? "disabled" : "";
        echo "<tr>";
        echo "<td class='table-column-id'>" . $id . "</td>";
        echo "<td class='table-column-actions sticky-table-column'>";
        echo "<div class='td-toolbar'>";

        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditBudgetArticleOnClick(" . $data[$i]["id"] . ")'/>";
        if ($admin_role)
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='deleteBudgetArticleOnClick(" . $data[$i]["id"] . ")' " . $disabled . "/>";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-main-section'>";
        echo $data[$i]['main_section'];
        echo "</td>";
        echo "<td class='table-column-section'>";
        echo $data[$i]['section'];
        echo "</td>";
        echo "<td class='table-column-subsection'>";
        echo $data[$i]['subsection'];
        echo "</td>";
        echo "<td class='table-column-fundholder'>";
        echo $data[$i]['fundholder'];
        echo "</td>";
        echo "<td class='table-column-service'>";
        echo $data[$i]['service'];
        echo "</td>";
        echo "<td class='table-column-article'>";
        echo $data[$i]['article'];
        echo "</td>";
        echo "<td class='table-column-new-code'>";
        echo $data[$i]['new_code'];
        echo "</td>";
        if ($financier_role) {
            echo "<td class='table-column-old-code'>";
            echo $data[$i]['old_code'];
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function GetMainSectionsData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM main_sections_directory");
    while ($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetSectionsData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM sections_directory WHERE main_sections_directory_id = '" . $data['mainSectionId'] . "'");
    while ($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetSubsectionsData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM subsections_directory WHERE sections_directory_id = '" . $data['sectionId'] . "'");
    while ($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetOldCodesData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM old_codes_directory");
    while ($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'old_code' => $data["old_code"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetNewCodesData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $exception_condition = $data["action"] == "edit" ? " WHERE new_codes_directory_id != '" . $data['newCodeId'] . "'" : "";
    $exception_array = array();
    $exception_result = $mysql->query("SELECT new_codes_directory_id FROM budget_articles_directory" . $exception_condition);

    //echo "SELECT new_codes_directory_id FROM budget_articles_directory" . $exception_condition;
    if ($data["section"] != "Інвестпрограма")
        while ($exception_row = $exception_result->fetch_assoc()) {

            array_push($exception_array, [
                'new_code_id' => $exception_row['new_codes_directory_id'],
            ]);
        }
    $data_array = array();
    $result = $mysql->query("SELECT ncd.* FROM new_codes_directory ncd 
                                WHERE ncd.old_codes_directory_id = '" . $data['oldCodeId'] . "'  and LENGTH(ncd.new_code) >= '" . $data['lowLimitSymbolsNewCode'] . "'");
    while ($new_codes_data = $result->fetch_assoc()) {
        $exception_sign = true;
        foreach ($exception_array as $key => $exception) {
            if ($exception['new_code_id'] == $new_codes_data["id"])
                $exception_sign = false;
        }
        if ($exception_sign)
            array_push($data_array, [
                'id' => $new_codes_data["id"],
                "old_code_id" => $new_codes_data["old_codes_directory_id"],
                'new_code' => $new_codes_data["new_code"],
            ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetFundholdersData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM fundholders_directory");
    while ($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["additional_name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetServicesData($data, $admin_role, $director_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $condition = $admin_role ? "" : " WHERE fundholders_directory_id = '" . $data['fundholderId'] . "'";
    $result = $mysql->query("SELECT * FROM services_directory" . $condition);
    while ($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetBudgetArticleData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT a.id, a.name, a.fundholders_directory_id, d.name as main_section, c.name as section, b.name as subsection, e.name as fundholder, 
                                    f.id as service_id, f.name as service, a.name as article, g.id as new_code_id, g.new_code, h.id as old_code_id, h.old_code
                                FROM `budget_articles_directory` a
                                LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id
                                LEFT JOIN sections_directory c ON c.id = b.sections_directory_id
                                LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id
                                LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id
                                LEFT JOIN services_directory f ON f.id = a.services_directory_id
                                LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id
                                LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id 
                                WHERE a.id = '" . $data['id'] . "'");

    $data = $result->fetch_assoc();
    echo json_encode([
        "id" => $data["id"],
        "name" => $data["name"],
        "main_section" => $data["main_section"],
        "section" => $data["section"],
        "subsection" => $data["subsection"],
        "fundholder_id" => $data["fundholders_directory_id"],
        "fundholder" => $data["fundholder"],
        "service_id" => $data["service_id"],
        "service" => $data["service"],
        "article" => $data["article"],
        "old_code_id" => $data["old_code_id"],
        "old_code" => $data["old_code"],
        "new_code_id" => $data["new_code_id"],
        "new_code" => $data["new_code"],
    ]);
    $mysql->close();
}

function ModalSaveAddBudgetArticle($data, $user_id, $admin_role, $financier_role, $director_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $query = "";
    if ($financier_role) {
        $query = "INSERT INTO budget_articles_directory (subsections_directory_id, fundholders_directory_id, services_directory_id, new_codes_directory_id, name) 
                    VALUES ('" . $data['subsectionId'] . "', '" . $data['fundholderId'] . "', '" . (isset($data['serviceId']) ? $data['serviceId'] : 0) . "', '" . $data['newCodeId'] . "', 
                    '" . mysqli_real_escape_string($mysql, $data['name']) . "')";
        $result = $mysql->query($query);
        $mysql->query("UPDATE new_codes_directory SET delete_mode = false WHERE id = '" . $data['newCodeId'] . "'");
        $mysql->query("UPDATE fundholders_directory SET delete_mode = false WHERE id = '" . $data['fundholderId'] . "'");
        $mysql->query("UPDATE services_directory SET delete_mode = false WHERE id = '" . $data['serviceId'] . "'");
    } elseif ($director_role) {
        $query = "INSERT INTO budget_articles_directory (subsections_directory_id, fundholders_directory_id, services_directory_id, name) 
                    VALUES ('" . $data['subsectionId'] . "', '" . $data['fundholderId'] . "', '" . (isset($data['serviceId']) ? $data['serviceId'] : 0) . "', 
                    '" . mysqli_real_escape_string($mysql, $data['name']) . "')";
        $result = $mysql->query($query);
    }
    $mysql->query("INSERT INTO budget_articles_directory_action_log (budget_articles_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditBudgetArticle($data, $user_id, $admin_role, $financier_role, $director_role, $fin_dir_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $query = "";
    if ($fin_dir_role) {
        $query = "UPDATE budget_articles_directory  SET new_codes_directory_id = '" . $data['newCodeId'] . "', 
                                                        services_directory_id = '" . $data['serviceId'] . "', 
                                                        name = '" . mysqli_real_escape_string($mysql, $data['name']) . "' 
                                                        WHERE id = '" . $data['id'] . "'";
        $result = $mysql->query($query);
    } elseif ($financier_role) {
        $query = "UPDATE budget_articles_directory  SET new_codes_directory_id = '" . $data['newCodeId'] . "' WHERE id = '" . $data['id'] . "'";
        $result = $mysql->query($query);
    } elseif ($director_role) {
        $query = "UPDATE budget_articles_directory  SET services_directory_id = '" . $data['serviceId'] . "', name = '" . mysqli_real_escape_string($mysql, $data['name']) . "' WHERE id = '" . $data['id'] . "'";
        $result = $mysql->query($query);
    }
    $mysql->query("INSERT INTO budget_articles_directory_action_log (budget_articles_directory_id, user_id, action, datetime) 
                                VALUES ('" .  $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");

    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}


function DeleteBudgetArticle($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM budget_articles_directory WHERE id = '" . $data['id'] . "'");
    if ($data['newCodeId'] != null)
        $mysql->query("UPDATE new_codes_directory SET delete_mode = true WHERE id = '" . $data['newCodeId'] . "'");
    if ($data['fundholderId'] != null)
        $mysql->query("UPDATE fundholders_directory SET delete_mode = true WHERE id = '" . $data['fundholderId'] . "'");
    $mysql->query("INSERT INTO budget_articles_directory_action_log (budget_articles_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);
switch ($data["typeRequest"]) {
    case "getUserInfoRequest":
        echo json_encode([
            "fundholder_id" => $fundholder_id,
            "role" => [
                "admin_role" => $admin_role,
                "financier_role" => $financier_role,
                "director_role" => $director_role,
                "fin_dir_role" => $fin_dir_role,
                "act_viewer_role" => $act_viewer_role,
            ]
        ]);
        break;
    case "renderTableRequest":
        CreateTableDOM($admin_role, $financier_role, $fundholder_id, $data["withArchiveArticles"]);
        break;
    case "getMainSectionsRequest":
        GetMainSectionsData();
        break;
    case "getSectionsRequest":
        GetSectionsData($data);
        break;
    case "getSubsectionsRequest":
        GetSubsectionsData($data);
        break;
    case "getOldCodesRequest":
        GetOldCodesData();
        break;
    case "getNewCodesRequest":
        GetNewCodesData($data);
        break;
    case "getFundholdersRequest":
        GetFundholdersData();
        break;
    case "getServicesRequest":
        GetServicesData($data, $admin_role, $director_role);
        break;
    case "getBudgetArticleRequest":
        GetBudgetArticleData($data);
        break;
    case "modalSaveAddBudgetArticleRequest":
        ModalSaveAddBudgetArticle($data, $user_id, $admin_role, $financier_role, $director_role);
        break;
    case "modalSaveEditBudgetArticleRequest":
        ModalSaveEditBudgetArticle($data, $user_id, $admin_role, $financier_role, $director_role, $fin_dir_role);
        break;
    case "deleteBudgetArticleRequest":
        DeleteBudgetArticle($data, $admin_role);
        break;
    default:
        break;
}