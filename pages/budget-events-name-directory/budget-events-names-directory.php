<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetEventsNameData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $main_section_array = array();
    $main_section_result = $mysql->query("SELECT * FROM main_sections_directory");
    while($main_section_row = $main_section_result->fetch_assoc()) {
        $section_array = array();
        $section_rowspan = 0;
        $section_result = $mysql->query("SELECT * FROM sections_directory WHERE main_sections_directory_id = '" . $main_section_row['id'] . "'");
        while($section_row = $section_result->fetch_assoc()) {
            $subsection_rowspan = 0;
            $subsection_array = array();
            $subsection_directory_result = $mysql->query("SELECT a.id, a.sections_directory_id, a.name, a.new_codes_directory_id, 
                                                                 if((SELECT count(b.id) FROM budget_articles_directory b WHERE b.subsections_directory_id = a.id) > 0, false, true) as delete_mode 
                                                          FROM subsections_directory a
                                                          WHERE sections_directory_id = '" . $section_row['id'] . "'");
            while($subsection_row = $subsection_directory_result->fetch_assoc()) {
                $subsection_rowspan++;
                $section_rowspan++;
                array_push($subsection_array, [
                    "id" => $subsection_row["id"],
                    "sections_directory_id" => $subsection_row["sections_directory_id"],
                    "name" => $subsection_row["name"],
                    "new_codes_directory_id" => $subsection_row["new_codes_directory_id"],
                    "delete_mode" => $subsection_row["delete_mode"],
                ]);
            }
            if(count($subsection_array) == 0) {
                $section_rowspan++;
                array_push($subsection_array, [
                    "id" => null,
                    "sections_directory_id" => null,
                    "name" => null,
                    "new_codes_directory_id" => null,
                    "delete_mode" => null,
                ]);
            }
            array_push($section_array, [
                "id" => $section_row["id"],
                "main_sections_directory_id" => $section_row["main_sections_directory_id"],
                "name" => $section_row["name"],
                "new_codes_directory_id" => $section_row["new_codes_directory_id"],
                "rowspan" => $subsection_rowspan,
                "subsection_array" => $subsection_array,
            ]);
        }
        if(count($section_array) == 0) {
            $section_rowspan++;
            array_push($section_array, [
                "id" => null,
                "main_sections_directory_id" => null,
                "name" => null,
                "new_codes_directory_id" => null,
                "rowspan" => 1,
                "subsection_array" => [[
                    "id" => null,
                    "sections_directory_id" => null,
                    "name" => null,
                    "new_codes_directory_id" => null,
                    "delete_mode" => null,
                ],],
            ]);
        }
        array_push($main_section_array, [
            "id" => $main_section_row["id"],
            "name" => $main_section_row["name"],
            "new_codes_directory_id" => $main_section_row["new_codes_directory_id"],
            "rowspan" => $section_rowspan,
            "section_array" => $section_array,
        ]);
    }
    
    $mysql->close();
    return $main_section_array;
}

function CreateTableDOM($admin_role)
{
    $data = GetEventsNameData();
    //шапка таблицы
    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id sticky-table-column'>№</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-main-section sticky-table-column'>Головний розділ</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-section sticky-table-column'>Розділ</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-subsection sticky-table-column'>Підрозділ</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";

    //отрисовка остальной таблицы
    for ($i = 0, $main_section_id = 1; $i < count($data); $i++, $main_section_id++) {
        
        $rowspan_section = $data[$i]["rowspan"];
        $rowspan__td = $rowspan_section > 1 && $i + 1 == count($data) ? "rowspan__td" : "";
        $align__td =  $rowspan_section > 1 ? "align-top__td" : "";
        $disabled =  $rowspan_section >= 1 && $data[$i]["section_array"][0]["id"] != ( null || 0) ? "disabled" : "";
        echo "<tr>";
        echo "<td class='table-column-id " . $rowspan__td . " " . $align__td . "' rowspan='" . $rowspan_section ."'>" . $main_section_id . "</td>";
        echo "<td class='table-column-actions sticky-table-column " . $rowspan__td . " " . $align__td . "'  rowspan='" . $rowspan_section ."'>";
        echo "<div class='td-toolbar'>";
        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditMainSectionOnClick(" . $data[$i]["id"] . ", " . $data[$i]["new_codes_directory_id"] . ")'/>";
        if($admin_role === true)
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteMainSectionOnClick(" . $data[$i]["id"] . ", " . $data[$i]["new_codes_directory_id"] . ")' " . $disabled . "/>";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-name " . $rowspan__td . " " . $align__td . "'  rowspan='" . $rowspan_section ."'>"; 
        echo $data[$i]['name'];
        echo "</td>";
        for($j = 0, $section_id = 1; $j < count($data[$i]["section_array"]); $j++, $section_id++) {
            $rowspan_subsection = $data[$i]["section_array"][$j]["rowspan"] == 0 ? 1 : $data[$i]["section_array"][$j]["rowspan"];
            $rowspan__td = $rowspan_subsection > 1 && ($i + 1 == count($data) && $j + 1 == count($data[$i]["section_array"])) ? "rowspan__td" : "";
            $align__td =  $rowspan_subsection > 1 ? "align-top__td" : "";
            $disabled = $rowspan_subsection >= 1 && $data[$i]["section_array"][$j]["subsection_array"][0]["id"] != ( null || 0) ? "disabled" : "";
            echo $j > 0 ? "<tr>": "";
            echo "<td class='table-column-actions sticky-table-column " . $rowspan__td . " " . $align__td . "'  rowspan='" . $rowspan_subsection ."'>";
            echo "<div class='td-toolbar'>";
            echo $data[$i]["section_array"][$j]['id'] != null ? "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditSectionOnClick(" . $data[$i]["section_array"][$j]["id"] . ", " . $data[$i]["section_array"][$j]["new_codes_directory_id"] . ")'/>" : "—";
            if($admin_role === true)
                echo $data[$i]["section_array"][$j]['id'] != null ? "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteSectionOnClick(" . $data[$i]["section_array"][$j]["id"] . ", " . $data[$i]["section_array"][$j]["new_codes_directory_id"] . ")' " . $disabled . "/>"  : "";
            echo "</div>";
            echo "</td>";
            echo "<td class='table-column-name " . $rowspan__td . " " . $align__td . "'  rowspan='" . $rowspan_subsection ."'>"; 
            echo $data[$i]["section_array"][$j]['id'] != null ? $data[$i]["section_array"][$j]['name']   : "—";
            echo "</td>";
            $count_subsection_array = count($data[$i]["section_array"][$j]["subsection_array"]);
            for($q = 0, $subsection_id = 1; $q < $count_subsection_array; $q++, $subsection_id++) {
                $disabled = $data[$i]["section_array"][$j]["subsection_array"][$q]["delete_mode"] ? "" : "disabled";
                echo $q > 0 ? "<tr>": "";
                echo "<td class='table-column-actions sticky-table-column'>";
                echo "<div class='td-toolbar'>";
                echo $data[$i]["section_array"][$j]["subsection_array"][$q]['id'] != null ? "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditSubsectionOnClick(" . $data[$i]["section_array"][$j]["subsection_array"][$q]["id"] . ", " . $data[$i]["section_array"][$j]["subsection_array"][$q]["new_codes_directory_id"] . ")'/>" : "—";
                if($admin_role === true)
                    echo $data[$i]["section_array"][$j]["subsection_array"][$q]['id'] != null ? "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteSubsectionOnClick(" . $data[$i]["section_array"][$j]["subsection_array"][$q]["id"] . ", " . $data[$i]["section_array"][$j]["subsection_array"][$q]["new_codes_directory_id"] . ")' " . $disabled . "/>" : "";
                echo "</div>";
                echo "</td>";
                echo "<td class='table-column-name'>"; 
                echo $data[$i]["section_array"][$j]["subsection_array"][$q]['id'] != null ? $data[$i]["section_array"][$j]["subsection_array"][$q]['name']  : "—";
                echo "</td>";
                echo "</tr>";
            }
        }
    }
    echo "</tbody>";
    echo "</table>";
}

function GetOldCodesData() {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM old_codes_directory");
    while($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'old_code' => $data["old_code"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetNewCodesData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $exception_array = array();
    $exception_result = $mysql->query("SELECT new_codes_directory_id FROM " . $data['nameTable']);
    while($exception_row = $exception_result->fetch_assoc()) {
        array_push($exception_array, [
            'new_code_id' => $exception_row['new_codes_directory_id'],
        ]);
    }
    $data_array = array();
    $result = $mysql->query("SELECT ncd.* FROM new_codes_directory ncd 
                                WHERE ncd.old_codes_directory_id = '" . $data['id'] . "'  and LENGTH(ncd.new_code) = '" . $data['countSymbolsNewCode'] . "'");
    while($data = $result->fetch_assoc()) {
        $exception_sign = true;
        foreach($exception_array as $key => $exception) {
            if($exception['new_code_id'] == $data["id"])
                $exception_sign = false;
        }
        if($exception_sign)
            array_push($data_array, [
                'id' => $data["id"],
                "old_code_id" => $data["old_codes_directory_id"],
                'new_code' => $data["new_code"],
            ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetMainSectionsData() {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM main_sections_directory");
    while($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetMainSectionData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM main_sections_directory WHERE id = '" . $data['mainSectionId'] . "'");
    $data = $result->fetch_assoc();
    echo json_encode([
        'id' => $data["id"],
        'name' => $data["name"],
    ]);
    $mysql->close();
}

function GetSectionsData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM sections_directory WHERE main_sections_directory_id = '" . $data['mainSectionId'] . "'");
    while($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'name' => $data["name"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetSectionData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM sections_directory WHERE id = '" . $data['sectionId'] . "'");
    $data = $result->fetch_assoc();
    echo json_encode([
        'id' => $data["id"],
        'name' => $data["name"],
    ]);
    $mysql->close();
}

function GetSubsectionData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM subsections_directory WHERE id = '" . $data['subsectionId'] . "'");
    $data = $result->fetch_assoc();
    echo json_encode([
        'id' => $data["id"],
        'name' => $data["name"],
    ]);
    $mysql->close();
}

function ModalSaveAddMainSection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO main_sections_directory (name, new_codes_directory_id) 
                                VALUES ('" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                        '" . $data['newCodeId'] . "')");
    $mysql->query("UPDATE new_codes_directory SET delete_mode = false WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO main_sections_directory_action_log (main_sections_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveAddSection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO sections_directory (main_sections_directory_id, name, new_codes_directory_id) 
                                VALUES ('" . $data['mainSectionId'] . "', 
                                        '" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                        '" . $data['newCodeId'] . "')");
    $mysql->query("UPDATE new_codes_directory SET delete_mode = false WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO sections_directory_action_log (sections_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveAddSubsection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO subsections_directory (sections_directory_id, name, new_codes_directory_id) 
                                VALUES ('" . $data['sectionId'] . "', 
                                        '" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                        '" . $data['newCodeId'] . "')");
    $mysql->query("UPDATE new_codes_directory SET delete_mode = false WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO subsections_directory_action_log (subsections_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditMainSection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE main_sections_directory SET name = '" . $data['mainSection'] . "' WHERE id = '" . $data['id'] . "'");
    //$mysql->query("UPDATE new_codes_directory SET delete_mode = false WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO main_sections_directory_action_log (main_sections_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditSection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE sections_directory SET name = '" . $data['section'] . "' WHERE id = '" . $data['id'] . "'");
    $mysql->query("INSERT INTO sections_directory_action_log (sections_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditSubsection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE subsections_directory SET name = '" . $data['subsection'] . "' WHERE id = '" . $data['id'] . "'");
    //$mysql->query("UPDATE new_codes_directory SET delete_mode = false WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO subsections_directory_action_log (subsections_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteMainSection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM main_sections_directory WHERE id = '" . $data['id'] . "'");
    $mysql->query("UPDATE new_codes_directory SET delete_mode = 1 WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO main_sections_directory_action_log (main_sections_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteSection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM sections_directory WHERE id = '" . $data['id'] . "'");
    $mysql->query("UPDATE new_codes_directory SET delete_mode = 1 WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO sections_directory_action_log (sections_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteSubsection($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM subsections_directory WHERE id = '" . $data['id'] . "'");
    $mysql->query("UPDATE new_codes_directory SET delete_mode = 1 WHERE id = '" . $data['newCodeId'] . "'");
    $mysql->query("INSERT INTO subsections_directory_action_log (subsections_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);
switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM($admin_role);
        break;
    case "getOldCodesRequest":
        GetOldCodesData();
        break;
    case "getNewCodesRequest":
        GetNewCodesData($data);
        break;
    case "getMainSectionsRequest":
        GetMainSectionsData();
        break;
    case "getMainSectionRequest":
        GetMainSectionData($data);
        break;
    case "getSectionsRequest":
        GetSectionsData($data);
        break;
    case "getSectionRequest":
        GetSectionData($data);
        break;
    case "getSubsectionRequest":
        GetSubsectionData($data);
        break;
    case "modalSaveAddMainSectionRequest":
        ModalSaveAddMainSection($data, $user_id);
        break;
    case "modalSaveAddSectionRequest":
        ModalSaveAddSection($data, $user_id);
        break;
    case "modalSaveAddSubsectionRequest":
        ModalSaveAddSubsection($data, $user_id);
        break;
    case "modalSaveEditMainSectionRequest":
        ModalSaveEditMainSection($data, $user_id);
        break;
    case "modalSaveEditSectionRequest":
        ModalSaveEditSection($data, $user_id);
        break;
    case "modalSaveEditSubsectionRequest":
        ModalSaveEditSubsection($data, $user_id);
        break;
    case "deleteMainSectionRequest":
        DeleteMainSection($data, $user_id);
        break;
    case "deleteSectionRequest":
        DeleteSection($data, $user_id);
        break;
    case "deleteSubsectionRequest":
        DeleteSubsection($data, $user_id);
        break;
    default:
        break;
}
?>