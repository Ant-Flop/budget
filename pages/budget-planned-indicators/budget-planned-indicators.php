<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetPlannedIndicatorsData($data)
{

    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();

    $result = $mysql->query(
        "SELECT 
            a.id, 
            a.article_name, 
            a.datetime, 
            a.sum_with_vat, 
            a.sum_with_vat_edited, 
            a.date_edit, 
            a.year,
            c.name as subsection_name, 
            d.name as section_name, 
            e.name as main_section_name, 
            f.additional_name as fundholder, 
            g.name as service,
            h.new_code, 
            i.old_code, 
            GROUP_CONCAT(k.name SEPARATOR ', ') as counterparties,
            IF(l.sum_with_vat = l.sum_no_vat, true, false) as sign_similarity_sum,
            if((SELECT count(*) 
                FROM renovation_treaty_directory m 
                WHERE m.planned_indicators_id = a.id) > 0, false, true) as delete_mode
                FROM `planned_indicators` a
                LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                LEFT JOIN subsections_directory c ON c.id = b.subsections_directory_id
                LEFT JOIN sections_directory d ON d.id = c.sections_directory_id
                LEFT JOIN main_sections_directory e ON e.id = d.main_sections_directory_id
                LEFT JOIN fundholders_directory f ON f.id = b.fundholders_directory_id
                LEFT JOIN services_directory g ON g.id = b.services_directory_id
                LEFT JOIN new_codes_directory h ON h.id = a.new_codes_directory_id
                LEFT JOIN old_codes_directory i ON i.id = h.old_codes_directory_id
                LEFT JOIN planned_indicators_amounts l ON l.planned_indicators_id = a.id
                LEFT JOIN planned_indicators_counterparties pic ON pic.planned_indicators_id = a.id 
                LEFT JOIN counterparties_directory k ON k.id = pic.counterparties_directory_id 
                WHERE f.id = '" . $data['fundholderId'] . "' 
                AND a.year = '" . $data['year'] . "' 
                GROUP BY a.id
                ORDER BY if(e.name LIKE 'Інвест. діяльність', c.name, a.id), " . $data['orderColumn'] . " " . $data['orderStatus']
    );

    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "new_code" => $row["new_code"],
            "main_section" => $row["main_section_name"],
            "section" => $row["section_name"],
            "subsection" => $row["subsection_name"],
            "fundholder" => $row["fundholder"],
            "service" => $row["service"],
            "article" => $row["article_name"],
            "sum_with_vat" => $row["sum_with_vat"],
            "counterparty" => $row["counterparties"],
            "date_edit" => $row["date_edit"],
            "sum_with_vat_edited" => $row["sum_with_vat_edited"],
            "order_column" => $data['orderColumn'],
            "order_status" => $data['orderStatus'],
            "sign_similarity_sum" => $row["sign_similarity_sum"],
            "delete_mode" => $row["delete_mode"],
            "edit_mode" => $row["year"] !== date("Y") ? false : true,
        ]);
    }
    $mysql->close();
    return $data_array;
}

function CreateTableDOM($admin_role, $financier_role, $director_role, $data)
{
    $data_array = GetPlannedIndicatorsData($data);
    $order_column = (count($data_array) > 0) ? $data_array[0]["order_column"] : null;
    $order_status = (count($data_array) > 0) ? $data_array[0]["order_status"] : null;
    $hidden = ($director_role) ?  'hidden' : '';
    //шапка таблицы
    echo "<table>";
    echo "<thead id='main-table-thead' class='unselectable' data-sort='" . $order_column . " " . $order_status . "'>";
    echo "<tr>";
    echo "<th id='main-table-th-id' class='main-table-th table-column-id sticky-table-column sort-th' data-column='a.id'>№</th>";
    echo "<th id='main-table-th-actions' class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th id='main-table-th-new-code' class='main-table-th table-column-new-code sticky-table-column sort-th' data-column='h.new_code'>Код статті бюджету</th>";
    echo "<th id='main-table-th-main-section' class='main-table-th table-column-main-section sticky-table-column sort-th' data-column='e.name'>Головний розділ бюджету</th>";
    echo "<th id='main-table-th-section' class='main-table-th table-column-section sticky-table-column sort-th' data-column='d.name'>Розділ бюджету</th>";
    echo "<th id='main-table-th-subsection' class='main-table-th table-column-subsection sticky-table-column sort-th' data-column='c.name'>Підрозділ бюджету</th>";
    echo "<th id='main-table-th-service' class='main-table-th table-column-service sticky-table-column sort-th' data-column='g.name'>Служба</th>";
    echo "<th id='main-table-th-article' class='main-table-th table-column-article sticky-table-column sort-th' data-column='a.article_name'>Назва статті бюджету</th>";
    echo "<th id='main-table-th-planned-indicator' class='main-table-th table-column-planned-indicator sticky-table-column sort-th' data-column='a.sum_with_vat'>Плановий показник, тис. грн. з ПДВ</th>";
    echo "<th id='main-table-th-counterparty' class='main-table-th table-column-counterparty sticky-table-column sort-th' data-column='k.name'>Контрагент</th>";
    if ($director_role)
        echo "<th class='main-table-th table-column-checkbox sticky-table-column'>
                    <div class='switch-panel unselectable'>
                        <div class='switch-element'>
                            <input type='checkbox' id='switch' class='switch-element__input' onchange='displayAdditionalColumn()'/>
                            <label for='switch' class='switch-element__lable'></label>
                        </div>
                    </div>
             </th>";
    echo "<th id='main-table-th-date-edit' class='main-table-th add-column table-column-date-edit sticky-table-column' data-column='a.date_edit' $hidden>Дата коригування</th>";
    echo "<th id='main-table-th-planned-indicator-edited' class='main-table-th add-column table-column-planned-indicator-edited sticky-table-column' data-column='a.sum_with_vat_edited' $hidden>Плановий показник, тис. грн. з ПДВ</th>";
    echo "<th id='main-table-th-planned-indicator-difference' class='main-table-th add-column table-column-planned-indicator-difference sticky-table-column' data-column='a.sum_with_vat_difference' $hidden>Відхилення від плану, тис. грн. з ПДВ</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";

    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data_array); $i++, $id++) {
        $red = $data_array[$i]["sign_similarity_sum"] ? "red" : "";
        $disabled_edit = $data_array[$i]["edit_mode"] ? "" : "disabled";
        $disabled_delete = $data_array[$i]["delete_mode"] ? "" : "disabled";
        echo "<tr>";
        echo "<td class='table-column-id'>" . $id . "</td>";
        echo "<td class='table-column-actions sticky-table-column'>";
        echo "<div class='td-toolbar'>";
        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input " . $disabled_edit . "' onclick='modalEditPlannedIndicatorOnClick(" . $data_array[$i]["id"] . ")' " . $disabled_edit . "/>";
        if ($admin_role || $director_role)
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled_delete  . "' onclick='modalDeletePlannedIndicatorOnClick(" . $data_array[$i]["id"] . ")' " . $disabled_delete . "/>";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-new-code'>";
        echo $data_array[$i]['new_code'];
        echo "</td>";
        echo "<td class='table-column-main-section'>";
        echo $data_array[$i]['main_section'];
        echo "</td>";
        echo "<td class='table-column-section'>";
        echo $data_array[$i]['section'];
        echo "</td>";
        echo "<td class='table-column-subsection'>";
        echo $data_array[$i]['subsection'];
        echo "</td>";
        echo "<td class='table-column-service'>";
        echo $data_array[$i]['service'];
        echo "</td>";
        echo "<td class='table-column-article'>";
        echo $data_array[$i]['article'];
        echo "</td>";
        echo "<td class='table-column-planned-indicator " . $red . "'>";
        echo number_format($data_array[$i]['sum_with_vat'], 5, '.', ' ');
        echo "</td>";
        echo "<td class='table-column-counterparty'>";
        echo $data_array[$i]['counterparty'];
        echo "</td>";
        if ($admin_role || $director_role) {
            echo "<td class='table-column-checkbox'>";
            echo "</td>";
        }
        echo "<td class='table-column-date-edit add-column' $hidden>";
        echo $data_array[$i]['date_edit'];
        echo "</td>";
        echo "<td class='table-column-planned-indicator-edited add-column' $hidden>";
        echo number_format($data_array[$i]['sum_with_vat_edited'], 5, '.', ' ');
        echo "</td>";
        echo "<td class='table-column-planned-indicator-difference add-column' $hidden>";
        echo number_format($data_array[$i]['sum_with_vat_edited'] != null ? $data_array[$i]['sum_with_vat_edited'] - $data_array[$i]['sum_with_vat'] : 0, 5, '.', ' ');
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function GetMainSectionsData($fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(d.id), d.name FROM `budget_articles_directory` a
                                LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id
                                LEFT JOIN sections_directory c ON c.id = b.sections_directory_id
                                LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id
                                LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id
                                LEFT JOIN services_directory f ON f.id = a.services_directory_id
                                LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id
                                LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id
                                WHERE a.fundholders_directory_id = '" . $fundholder_id . "'");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetSectionsData($data, $fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(c.id), c.name FROM `budget_articles_directory` a
                                LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id
                                LEFT JOIN sections_directory c ON c.id = b.sections_directory_id
                                LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id
                                LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id
                                LEFT JOIN services_directory f ON f.id = a.services_directory_id
                                LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id
                                LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id
                                WHERE a.fundholders_directory_id = '" . $fundholder_id . "' and d.id = '" . $data['mainSectionId'] . "'");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetSubsectionsData($data, $fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(b.id), b.name FROM `budget_articles_directory` a
                                LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id
                                LEFT JOIN sections_directory c ON c.id = b.sections_directory_id
                                LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id
                                LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id
                                LEFT JOIN services_directory f ON f.id = a.services_directory_id
                                LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id
                                LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id
                                WHERE a.fundholders_directory_id = '" . $fundholder_id . "' and c.id = '" . $data['sectionId'] . "'");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetServicesData($data, $fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(f.id), f.name FROM `budget_articles_directory` a
                                LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id
                                LEFT JOIN sections_directory c ON c.id = b.sections_directory_id
                                LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id
                                LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id
                                JOIN services_directory f ON f.id = a.services_directory_id
                                LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id
                                LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id
                                WHERE a.fundholders_directory_id = '" . $fundholder_id . "' and b.id = '" . $data['subsectionId'] . "'");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetArticlesData($data, $fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $exception_array = array();
    $service_condition = isset($data['serviceId']) ?  $data['serviceId']  : 0;
    $exception_result = $mysql->query("SELECT a.budget_articles_directory_id as id FROM planned_indicators a
                                       LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id 
                                        
                                        WHERE b.fundholders_directory_id = '" . $fundholder_id . "' and b.subsections_directory_id = '" . $data['subsectionId'] . "' 
                                        and b.services_directory_id = '" . $service_condition . "' and a.year = '" . date('Y') . "'");

    while ($exception_row = $exception_result->fetch_assoc()) {
        array_push($exception_array, [
            'id' => $exception_row['id'],
        ]);
    }
    $data_array = array();
    $result = $mysql->query("SELECT a.id, a.name FROM `budget_articles_directory` a
                             LEFT JOIN subsections_directory b ON b.id = a.subsections_directory_id
                             LEFT JOIN sections_directory c ON c.id = b.sections_directory_id
                             LEFT JOIN main_sections_directory d ON d.id = c.main_sections_directory_id
                             LEFT JOIN fundholders_directory e ON e.id = a.fundholders_directory_id
                             LEFT JOIN services_directory f ON f.id = a.services_directory_id
                             LEFT JOIN new_codes_directory g ON g.id = a.new_codes_directory_id
                             LEFT JOIN old_codes_directory h ON h.id = g.old_codes_directory_id
                             WHERE a.fundholders_directory_id = '" . $fundholder_id . "' and b.id = '" . $data['subsectionId'] . "'
                                   and a.services_directory_id = '" . $service_condition . "' and a.new_codes_directory_id != 0");
    while ($row = $result->fetch_assoc()) {
        $exception_sign = true;
        foreach ($exception_array as $key => $exception) {
            if ($exception['id'] == $row["id"])
                $exception_sign = false;
        }
        if ($exception_sign)
            array_push($data_array, [
                "id" => $row["id"],
                "name" => $row["name"],
            ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetCounterpartiesData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM `counterparties_directory`");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetPlannedIndicatorData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();

    // Обновляем запрос: получаем id и name контрагентов отдельно
    $result = $mysql->query("SELECT a.id, a.new_codes_directory_id, c.id as old_codes_directory_id, a.article_name, 
                             a.sum_with_vat, a.sum_with_vat_edited, if(pise.sections_directory_id is null, false, true) as exception_section, 
                             substring(datetime, 1, 10) as date_create, k.id as counterparty_id, k.name as counterparty_name
                             FROM `planned_indicators` a 
                             LEFT JOIN new_codes_directory b ON b.id = a.new_codes_directory_id
                             LEFT JOIN old_codes_directory c ON c.id = b.old_codes_directory_id
                             LEFT JOIN budget_articles_directory bad ON bad.id = a.budget_articles_directory_id
                             LEFT JOIN subsections_directory subsd ON subsd.id = bad.subsections_directory_id
                             LEFT JOIN sections_directory sd ON sd.id = subsd.sections_directory_id
                             LEFT JOIN planned_indicators_sections_exceptions pise ON pise.sections_directory_id = sd.id
                             LEFT JOIN planned_indicators_counterparties pic ON pic.planned_indicators_id = a.id 
                             LEFT JOIN counterparties_directory k ON k.id = pic.counterparties_directory_id 
                             WHERE a.id = '" . $data['id'] . "'");

    // Инициализируем массив для хранения контрагентов
    $counterparties = [];
    $row = $result->fetch_assoc();

    // Сохраняем общую информацию
    $id = $row['id'];
    $new_code_id = $row['new_codes_directory_id'];
    $old_code_id = $row['old_codes_directory_id'];
    $article_name = $row['article_name'];
    $sum_with_vat = floatval($row['sum_with_vat']);
    $sum_with_vat_edited = floatval($row['sum_with_vat_edited']);
    $date_create = $row['date_create'];
    $exception_section = intval($row['exception_section']);

    // Снова перебираем результат для сбора контрагентов
    do {
        // Добавляем каждого контрагента в массив
        $counterparties[] = [
            "id" => $row["counterparty_id"],
            "name" => $row["counterparty_name"]
        ];
    } while ($row = $result->fetch_assoc());

    // Вычисление разницы в днях
    $dateString1 = date("Y-m-d");
    $date1 = new DateTime($dateString1);
    $date2 = new DateTime($date_create);
    $interval = $date1->diff($date2);
    $totalDays = $interval->format('%a');

    // Формируем JSON ответ
    echo json_encode([
        "id" => $id,
        "counterparties" => $counterparties,  // Возвращаем массив контрагентов
        "new_code_id" => $new_code_id,
        "old_code_id" => $old_code_id,
        "article_name" => $article_name,
        "sum_with_vat" => $sum_with_vat,
        "sum_with_vat_edited" => $sum_with_vat_edited,
        "date_create" => $date_create,
        "date_current" => $dateString1,
        "editable" => $exception_section === 1 ? ($totalDays < 4 ? 1 : 0) : 0,
        "exception_section" => $exception_section,
    ]);

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

function IsSectionException($pi_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT sections_directory_id, section_name FROM planned_indicators_sections_exceptions");
    $data_array = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, $row['sections_directory_id']);
    }

    $result_section = $mysql->query("SELECT subsd.sections_directory_id as sectionId, pi.budget_articles_directory_id as budgetArticleId 
                                        FROM planned_indicators pi
                                        LEFT JOIN budget_articles_directory bad ON bad.id = pi.budget_articles_directory_id 
                                        LEFT JOIN subsections_directory subsd ON subsd.id = bad.subsections_directory_id
                                        WHERE pi.id = '" . $pi_id . "' and pi.year = '" . (date('Y')) . "'");
    $section = $result_section->fetch_assoc();

    $mysql->close();
    $check_section = false;

    foreach ($data_array as $value) {
        if ($section["sectionId"] == $value)
            $check_section = true;
    }

    if ($check_section) {

        return ["id" => $section["sectionId"]];
    } else {
        return false;
    }
}

function GetClearContractsData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");


    $data["counterpartyId"] = isset($data["counterpartyId"]) && is_array($data["counterpartyId"]) ? $data["counterpartyId"] : [];

    // Initialize conditions based on the presence of counterpartyId
    $conditions = count($data["counterpartyId"]) > 0 ? "WHERE cd.counterparties_directory_id IN (" : "WHERE cd.counterparties_directory_id = 0";


    // Build the conditions string
    $counterpartyIds = [];
    foreach ($data["counterpartyId"] as $counterparty) {
        // Assuming counterpartyId is stored in an array like ['counterparty_id' => X]
        if (isset($counterparty['counterparty_id'])) {
            $counterpartyIds[] = $counterparty['counterparty_id']; // Collect valid ids
        } elseif (isset($counterparty['id'])) {
            $counterpartyIds[] = $counterparty['id'];
        }
    }

    // If there are counterparty IDs, create the query condition
    if (!empty($counterpartyIds)) {
        $conditions .= implode(", ", $counterpartyIds) . ") AND cd.status = 'Відкритий'";
    } else {
        $conditions .= " AND cd.status = 'Відкритий'";
    }


    // Perform the query
    $result = $mysql->query("SELECT DISTINCT cd.id AS contract_id, cd.number AS contract_number, cd.counterparties_directory_id AS counterparties_directory_id
                             FROM contracts_directory cd 
                             LEFT OUTER JOIN planned_indicators_contracts_directory picd 
                             ON cd.id = picd.contracts_directory_id " . $conditions);


    // Fetch results
    $data_array = [];
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "contract_id" => $row["contract_id"],
            "contract_number" => $row["contract_number"],
            "counterparty_id" => $row["counterparties_directory_id"],
        ]);
    }

    $mysql->close();
    echo json_encode($data_array); // Return the data as JSON
}


function GetContracts($pi_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT picd.planned_indicators_id as pi_id, cd.id as contract_id, cd.number as contract_number  
                             FROM planned_indicators_contracts_directory picd 
                             LEFT JOIN contracts_directory cd ON cd.id = picd.contracts_directory_id
                             WHERE picd.planned_indicators_id = '" . $pi_id . "'");
    $data_array = array();
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "pi_id" => $row["pi_id"],
            "contract_id" => $row["contract_id"],
            "contract_number" => $row["contract_number"],
        ]);
    }
    $mysql->close();
    return $data_array;
}

function GetContractsDirectoryWithExceptionsData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");




    // Ensure counterpartyId is set and is an array
    $counterpartyIds = [];
    if (isset($data['counterpartyId']) && is_array($data['counterpartyId'])) {
        $counterpartyIds = array_map(function ($item) {
            if (isset($item['id']))
                return $item['id'];
        }, $data['counterpartyId']);
    }




    // Check if the array is not empty before using implode
    if (!empty($counterpartyIds)) {
        $counterpartyIdsList = implode(',', $counterpartyIds);
    } else {
        $counterpartyIdsList = 'NULL'; // Handle the empty case appropriately
    }

    // Execute the contracts query
    $result_contracts = $mysql->query("SELECT id, counterparties_directory_id, number FROM contracts_directory 
                                 WHERE counterparties_directory_id IN (" . $counterpartyIdsList . ") 
                                 AND status = 'Відкритий'");

    if (!$result_contracts) {
        die('Error in contracts query: ' . $mysql->error);
    }

    // Execute the exceptions query
    $result_exceptions = $mysql->query("SELECT a.id, a.contracts_directory_id, a.planned_indicators_id, b.counterparties_directory_id
                                        FROM planned_indicators_contracts_directory a
                                        LEFT JOIN planned_indicators b ON b.id = a.planned_indicators_id
                                        WHERE a.planned_indicators_id = '" . $data['plannedIndicatorId'] . "'");

    if (!$result_exceptions) {
        die('Error in exceptions query: ' . $mysql->error);
    }

    $data_contract_array = array();
    $data_exceptions_array = array();
    $new_code_id = null;

    // Fetch data from the result sets
    while ($row = $result_contracts->fetch_assoc()) {
        array_push($data_contract_array, [
            "contract_id" => $row["id"],
            "counterparty_id" => $row["counterparties_directory_id"],
            "contract_number" => $row["number"],
        ]);
    }

    while ($row = $result_exceptions->fetch_assoc()) {
        array_push($data_exceptions_array, [
            "contract_id" => $row["contracts_directory_id"],
            "planned_indicators_id" => $row["planned_indicators_id"],
            "counterparty_id" => $row["counterparties_directory_id"],
        ]);
    }

    $mysql->close();
    echo json_encode([
        "contract_array" => $data_contract_array,
        "exceptions_array" => $data_exceptions_array,
        "new_code_id" => $new_code_id
    ]);
}


function GetNewCodesData($data)
{

    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $exception_condition = " WHERE new_codes_directory_id != '" . $data['newCodeId'] . "'";
    $exception_array = array();
    $exception_result = $mysql->query("SELECT new_codes_directory_id FROM planned_indicators" . $exception_condition);


    if (!IsSectionException($data['id']))
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


function GetBudgetArticlesInfo($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM budget_articles_directory WHERE id = '" . $id . "'");
    $row = $result->fetch_assoc();
    $mysql->close();
    return [
        "new_code_id" => $row["new_codes_directory_id"],
    ];
}

function ModalSaveAddPlannedIndicator($data, $user_id, $director_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");

    // Sanitize inputs to avoid SQL injection
    $budgetArticleId = $mysql->real_escape_string($data['budgetArticleId']);
    $sumMonthWithVAT = $mysql->real_escape_string($data['sumMonthWithVAT']);
    $counterparties = isset($data['counterparties']) ? $data['counterparties'] : 0;

    if (is_array($counterparties)) {
        // Check if the first element is an array and extract the ID

        // if (count($counterparties) > 0) {
        //     $counterpartyId = !empty($counterparties[0]) ? $mysql->real_escape_string($counterparties[0]['counterparties']) : 0;
        // } else {
        //     $counterparties = !empty($counterparties) ? $mysql->real_escape_string($counterparties[0]) : 0;
        // }
    } else {
        // If it's not an array, sanitize it directly
        $counterparties = $mysql->real_escape_string($counterparties);
    }



    if ($director_role) {
        // Build the insert query for planned_indicators
        $query = "
            INSERT INTO planned_indicators (
                unique_number, 
                budget_articles_directory_id, 
                new_codes_directory_id, 
                article_name, 
                datetime, 
                year, 
                sum_with_vat
            ) 
            VALUES (
                CONCAT('" . date('Y') . "', '" . $budgetArticleId . "'), 
                '" . $budgetArticleId . "', 
                (SELECT a.new_codes_directory_id FROM budget_articles_directory a WHERE id = '" . $budgetArticleId . "'), 
                (SELECT a.name FROM budget_articles_directory a WHERE id = '" . $budgetArticleId . "'), 
                '" . date('Y-m-d H:i:s') . "', 
                '" . date('Y') . "', 
                '" . $sumMonthWithVAT . "'
            )";

        // Execute the query and check for errors
        if (!$mysql->query($query)) {
            echo json_encode(["status" => false, "text" => "Error: " . $mysql->error]);
            return;
        }

        // Get the inserted planned indicator's ID
        $planned_indicators_id = $mysql->insert_id;

        if (count($data["counterparties"]) > 0) {
            InsertPlannedIndicatorCounterparties($planned_indicators_id, $data['counterparties']);
        }

        // Insert into planned_indicators_contracts if there are contracts
        if (count($data['contracts']) > 0) {
            InsertPlannedIndicatorContracts($planned_indicators_id, $data['contracts']);
        }

        // Log the action
        $mysql->query("INSERT INTO planned_indicators_action_log (planned_indicators_id, user_id, action, datetime) 
                        VALUES ('$planned_indicators_id', '$user_id', 'add', '" . date('Y-m-d H:i:s') . "')");

        // Insert planned indicators amounts
        $mysql->query(InsertPlannedIndicatorsAmountQuery($planned_indicators_id, $data, "planned_indicators_amounts"));
        $planned_indicators_amounts_id = $mysql->insert_id;

        // Insert VAT sign details
        $mysql->query(InsertPlannedIndicatorsAmountVatSignQuery(
            $planned_indicators_id,
            $planned_indicators_amounts_id,
            $data,
            "planned_indicators_amounts_vat_sign",
            "planned_indicators_amounts_id"
        ));

        // Log amounts action
        $mysql->query("INSERT INTO planned_indicators_amounts_action_log (planned_indicators_amounts_id, user_id, action, datetime) 
                        VALUES ('$planned_indicators_amounts_id', '$user_id', 'add', '" . date('Y-m-d H:i:s') . "')");

        // Insert into implementation table
        $mysql->query(InsertPlannedIndicatorsAmountQuery($planned_indicators_id, $data, "planned_indicators_amounts_implementation"));
        $planned_indicators_amounts_implementation_id = $mysql->insert_id;
        $mysql->query(InsertPlannedIndicatorsAmountVatSignQuery(
            $planned_indicators_id,
            $planned_indicators_amounts_implementation_id,
            $data,
            "planned_indicators_amounts_implementation_vat_sign",
            "planned_indicators_amounts_implementation_id"
        ));

        // Log implementation amounts action
        $mysql->query("INSERT INTO planned_indicators_amounts_implementation_action_log (planned_indicators_amounts_implementation_id, user_id, action, datetime) 
                        VALUES ('$planned_indicators_amounts_implementation_id', '$user_id', 'add', '" . date('Y-m-d H:i:s') . "')");

        // Insert into fact indicators amounts
        $mysql->query("INSERT INTO fact_indicators_amounts (planned_indicators_id) VALUES ('$planned_indicators_id')");
        $fact_indicators_amounts_id = $mysql->insert_id;

        // Log fact indicators amounts action
        $mysql->query("INSERT INTO fact_indicators_amounts_action_log (fact_indicators_amounts_id, user_id, action, datetime) 
                        VALUES ('$fact_indicators_amounts_id', '$user_id', 'add', '" . date('Y-m-d H:i:s') . "')");

        // Set fact indicators amounts
        SetFactIndicatorsAmounts($planned_indicators_id, GetBudgetArticlesInfo($budgetArticleId)["new_code_id"]);

        // Final success message
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    } else {
        echo json_encode(["status" => false, "text" => "No access rights!"]);
    }

    $mysql->close();
}

function InsertPlannedIndicatorCounterparties($planned_indicators_id, $counterparties)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    for ($i = 0; $i < count($counterparties); $i++) {
        $mysql->query("INSERT INTO planned_indicators_counterparties (planned_indicators_id, counterparties_directory_id)
                        SELECT
                        '" . $planned_indicators_id . "' AS planned_indicators_id,
                        '" . $counterparties[$i]['counterparty_id'] . "' AS counterparties_directory_id
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1
                            FROM planned_indicators_counterparties
                            WHERE planned_indicators_id = '" . $planned_indicators_id . "'
                            AND counterparties_directory_id = '" . $counterparties[$i]['counterparty_id'] . "'
                        );");
    }
    $mysql->close();
}

function InsertPlannedIndicatorContracts($planned_indicators_id, $contracts)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    for ($i = 0; $i < count($contracts); $i++) {
        $mysql->query("INSERT INTO planned_indicators_contracts_directory (planned_indicators_id, contracts_directory_id)
                        SELECT
                        '" . $planned_indicators_id . "' AS planned_indicators_id,
                        '" . $contracts[$i]['contract_id'] . "' AS contracts_directory_id
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1
                            FROM planned_indicators_contracts_directory
                            WHERE planned_indicators_id = '" . $planned_indicators_id . "'
                            AND contracts_directory_id = '" . $contracts[$i]['contract_id'] . "'
                        );");
    }

    $mysql->close();
}

function InsertPlannedIndicatorsAmountQuery($planned_indicators_id, $data, $table)
{
    $values = "";
    for ($i = 0; $i < count($data['monthPlannedIndicatorWithVAT']); $i++) {
        $values = ($i === 0) ? "'" . $data['monthPlannedIndicatorWithVAT'][$i] . "', '" . $data['monthPlannedIndicatorNoVAT'][$i] . "'" :
            $values . ", '" . $data['monthPlannedIndicatorWithVAT'][$i] . "', '" . $data['monthPlannedIndicatorNoVAT'][$i] . "'";
    }
    return "INSERT INTO " . $table . " (planned_indicators_id, 01_with_vat, 01_no_vat, 02_with_vat, 02_no_vat, 
                                                      03_with_vat, 03_no_vat, 04_with_vat, 04_no_vat, 05_with_vat, 05_no_vat, 06_with_vat, 
                                                      06_no_vat, 07_with_vat, 07_no_vat, 08_with_vat, 08_no_vat, 09_with_vat, 09_no_vat, 
                                                      10_with_vat, 10_no_vat, 11_with_vat, 11_no_vat, 12_with_vat, 12_no_vat, sum_with_vat, sum_no_vat, edited, edit_mode)
                                                      VALUES ('" . $planned_indicators_id . "', $values, '" . $data['sumMonthWithVAT'] . "', '" . $data['sumMonthNoVAT'] . "', false, true)";
}

function InsertPlannedIndicatorsAmountVatSignQuery($planned_indicators_id, $amounts_id, $data, $table, $parent_table_id)
{
    $values = "";
    for ($i = 0; $i < count($data['plannedIndicatorVatSign']) - 1; $i++) {
        $values = ($i === 0) ? "'" . $data['plannedIndicatorVatSign'][$i] . "'" :
            $values . ", '" . $data['plannedIndicatorVatSign'][$i] . "'";
    }
    return "INSERT INTO " . $table . " (planned_indicators_id, `01`, `02`, `03`, `04`, `05`, `06`, `07`, `08`, `09`, `10`, `11`, `12`)
                                                      VALUES ('" . $planned_indicators_id . "', $values)";
}

function TakeOffFactIndicatorsAmounts($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("UPDATE fact_indicators_amounts SET 01_with_vat = 0, 02_with_vat = 0, 03_with_vat = 0, 04_with_vat = 0, 05_with_vat = 0, 
                                                      06_with_vat = 0, 07_with_vat = 0, 08_with_vat = 0, 09_with_vat = 0, 10_with_vat = 0, 
                                                      11_with_vat = 0, 12_with_vat = 0, 
                                                      sum_with_vat = ROUND(01_with_vat + 02_with_vat + 03_with_vat + 04_with_vat + 
                                                                     05_with_vat + 06_with_vat + 07_with_vat + 08_with_vat +
                                                                     09_with_vat + 10_with_vat + 11_with_vat + 12_with_vat, 2) 
                   WHERE planned_indicators_id = '" . $id . "'");
    $mysql->close();
}

function SetFactIndicatorsAmounts($id, $new_code_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");

    // проверка относится ли статья к "Инвестпрограмме" или к другим исключениям (разделам)
    $section = IsSectionException($id);


    if (!$section) {
        for ($date = date("Y") . "-01", $i = 0; $i < 12; $i++, $date = date('Y-m', strtotime('+1 MONTH', strtotime($date)))) {
            $month_sum = 0;
            $month_with_vat = substr($date, 5, 2) . "_with_vat";


            $result_bank_register = $mysql->query("SELECT SUM(br.operSum) AS sum, pi.article_name, cd.number
                                                    FROM `banks_register` br
                                                    LEFT JOIN planned_indicators pi ON pi.new_codes_directory_id = br.new_codes_directory_id
                                                    LEFT JOIN planned_indicators_contracts_directory picd ON picd.planned_indicators_id = pi.id
                                                    LEFT JOIN contracts_directory cd ON cd.id = picd.contracts_directory_id
                                                    WHERE br.new_codes_directory_id = '" . $new_code_id . "'
                                                    AND SUBSTRING(br.operDate, 1, 7) LIKE '" . $date . "'
                                                    AND pi.year = '" . date('Y') . "'
                                                    AND br.paymentType NOT LIKE CONCAT('%', cd.number, '%')
                                                    GROUP BY pi.article_name, cd.number;");


            $result_additional_purpose = $mysql->query("SELECT sum(a.sum) as sum FROM banks_register_additional_purpose a 
                                                        LEFT JOIN banks_register br ON br.id = a.banks_register_id 
                                                        LEFT JOIN planned_indicators pi ON pi.new_codes_directory_id = br.new_codes_directory_id
                                                        LEFT JOIN planned_indicators_contracts_directory picd ON picd.planned_indicators_id = pi.id
                                                        LEFT JOIN contracts_directory cd ON cd.id = picd.contracts_directory_id
                                                        WHERE a.new_codes_directory_id = '" . $new_code_id . "'
                                                            AND SUBSTRING(a.oper_date, 1, 7) LIKE '" . $date . "'
                                                            AND pi.year = '" . date('Y') . "'
                                                            AND br.paymentType  LIKE CONCAT('%', cd.number, '%')
                                                        GROUP BY pi.article_name, cd.number;");
            while ($row_bank_register = $result_bank_register->fetch_assoc()) {
                $month_sum += $row_bank_register["sum"];
            }
            while ($row_additional_purpose = $result_additional_purpose->fetch_assoc()) {
                $month_sum += $row_additional_purpose["sum"];
            }
            $mysql->query("UPDATE fact_indicators_amounts SET " . $month_with_vat . " = '" . floatval($month_sum) . "', 
                                                          sum_with_vat = ROUND(01_with_vat + 02_with_vat + 03_with_vat + 04_with_vat + 
                                                          05_with_vat + 06_with_vat + 07_with_vat + 08_with_vat +
                                                          09_with_vat + 10_with_vat + 11_with_vat + 12_with_vat, 2)
                       WHERE planned_indicators_id = '" . $id . "'");
        }
    } else {

        // поиск оплат по договорам
        $contracts_info = GetContracts($id);


        if (count($contracts_info) === 0)
            return;


        $cond_payment_type = count($contracts_info) > 0 ? " and (" : "";
        for ($i = 0; $i < count($contracts_info); $i++) {
            $cond_payment_type = $cond_payment_type . "paymentType LIKE '%" . $contracts_info[$i]['contract_number'] . "%'";
            if ($i + 1 != count($contracts_info))
                $cond_payment_type = $cond_payment_type . " or ";
        }
        $cond_payment_type = $cond_payment_type . (count($contracts_info) > 0 ? ")" : "");

        for ($date = date("Y") . "-01", $i = 0; $i < 12; $i++, $date = date('Y-m', strtotime('+1 MONTH', strtotime($date)))) {
            $month_sum = 0;
            $month_with_vat = substr($date, 5, 2) . "_with_vat";

            $result_bank_register = $mysql->query("SELECT sum(operSum) as sum FROM `banks_register` 
                                               WHERE new_codes_directory_id = '" . $new_code_id . "' and SUBSTRING(operDate, 1, 7) LIKE '" . $date . "'" . $cond_payment_type);


            $result_additional_purpose = $mysql->query("SELECT sum(a.sum) as sum FROM banks_register_additional_purpose a 
                                      LEFT JOIN banks_register b ON b.id = a.banks_register_id 
                                      WHERE a.new_codes_directory_id = '" . $new_code_id . "' and SUBSTRING(a.oper_date, 1, 7) LIKE '" . $date . "'" . $cond_payment_type);

            while ($row_bank_register = $result_bank_register->fetch_assoc()) {
                $month_sum += $row_bank_register["sum"];
            }


            while ($row_additional_purpose = $result_additional_purpose->fetch_assoc()) {
                $month_sum += $row_additional_purpose["sum"];
            }
            $mysql->query("UPDATE fact_indicators_amounts SET " . $month_with_vat . " = '" . floatval($month_sum) . "', 
                                                          sum_with_vat = ROUND(01_with_vat + 02_with_vat + 03_with_vat + 04_with_vat + 
                                                          05_with_vat + 06_with_vat + 07_with_vat + 08_with_vat +
                                                          09_with_vat + 10_with_vat + 11_with_vat + 12_with_vat, 2)
                       WHERE planned_indicators_id = '" . $id . "'");
        }
    }
    $mysql->close();
}

function SetFactTransferIndicatorsAmounts($id, $new_code_id, $year)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $counter = 1;
    for ($date = $year . "-01", $i = 0; $i < 12; $i++, $date = $year . "-" . date('m', strtotime('+1 MONTH', strtotime($date)))) {
        $month_sum = 0;
        $month_with_vat = substr($date, 5, 2) . "_with_vat";
        $result_bank_register = $mysql->query("SELECT sum(operSum) as sum FROM `banks_register` 
                                               WHERE new_codes_directory_id = '" . $new_code_id . "' and SUBSTRING(operDate, 1, 7) LIKE '" . $date . "'");
        $result_additional_purpose = $mysql->query("SELECT sum(a.sum) as sum FROM banks_register_additional_purpose a 
                                      LEFT JOIN banks_register b ON b.id = a.banks_register_id 
                                      WHERE a.new_codes_directory_id = '" . $new_code_id . "' and SUBSTRING(a.oper_date, 1, 7) LIKE '" . $date . "'");
        while ($row_bank_register = $result_bank_register->fetch_assoc()) {
            $month_sum += $row_bank_register["sum"];
        }
        while ($row_additional_purpose = $result_additional_purpose->fetch_assoc()) {
            $month_sum += $row_additional_purpose["sum"];
        }
        $mysql->query("UPDATE fact_indicators_amounts SET " . $month_with_vat . " = '" . floatval($month_sum) . "', 
                                                          sum_with_vat = ROUND(01_with_vat + 02_with_vat + 03_with_vat + 04_with_vat + 
                                                          05_with_vat + 06_with_vat + 07_with_vat + 08_with_vat +
                                                          09_with_vat + 10_with_vat + 11_with_vat + 12_with_vat, 2)
                       WHERE planned_indicators_id = '" . $id . "'");

        if (floatval($month_sum) > 0)
            $counter = 0;
    }
    $mysql->query("UPDATE planned_indicators_amounts SET edit_mode = '" . $counter . "', set_after_transfer_mode = 1 WHERE planned_indicators_id = '" . $id . "'");
    $mysql->close();
}

function ModalSaveEditPlannedIndicator($data, $user_id, $admin_role, $director_role, $financier_role, $fin_dir_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $query = "";


    // SetFactIndicatorsAmounts
    if ($director_role) {
        $query = "UPDATE planned_indicators SET article_name = '" . mysqli_real_escape_string($mysql, $data['name']) . "' 
                  WHERE id = '" . $data['id'] . "'";

        $result_new_code = $mysql->query("SELECT new_codes_directory_id as new_code_id FROM planned_indicators WHERE id = '" . $data['id'] . "'");
        $new_code = $result_new_code->fetch_assoc();

        // Удаляем контракты, если все удалены
        if ($data['contracts']['deletedAll']) {
            $mysql->query("DELETE FROM planned_indicators_contracts_directory WHERE planned_indicators_id = '" . $data['id'] . "'");
        } else {
            // Удаляем только отмеченные контракты
            foreach ($data['contracts']['deleted'] as $element) {
                $mysql->query("DELETE FROM planned_indicators_contracts_directory WHERE contracts_directory_id = '" . $element['contract_id'] . "'");
            }
        }

        // Добавляем новые контракты
        foreach ($data['contracts']['added'] as $element) {
            $mysql->query("INSERT INTO planned_indicators_contracts_directory (planned_indicators_id, contracts_directory_id) 
                           VALUES ('" . $data['id'] . "', '" . $element['contract_id'] . "')");
        }

        // Обновляем контрагентов (удаляем всех и вставляем новых)
        $mysql->query("DELETE FROM planned_indicators_counterparties WHERE planned_indicators_id = '" . $data['id'] . "'");
        foreach ($data['counterpartyId'] as $counterparty) {
            $mysql->query("INSERT INTO planned_indicators_counterparties (planned_indicators_id, counterparties_directory_id) 
                           VALUES ('" . $data['id'] . "', '" . $counterparty['id'] . "')");
        }

        SetFactIndicatorsAmounts($data['id'], $new_code['new_code_id']);

        // Выполнение основного запроса на обновление
        $result = $mysql->query($query);

        $mysql->query("INSERT INTO planned_indicators_action_log (planned_indicators_id, user_id, action, datetime) 
                       VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    }
    if ($financier_role) {
        TakeOffFactIndicatorsAmounts($data['id']);
        SetFactIndicatorsAmounts($data['id'], $data['newCodeId']);
        $query = "UPDATE planned_indicators SET new_codes_directory_id = '" . $data['newCodeId'] . "', 
                                                sum_with_vat_edited = '" . $data['sumWithVATEdited'] . "', date_edit = '" . date("Y-m-d H:i:s") . "' 
                                                WHERE id = '" . $data['id'] . "'";
        $result = $mysql->query($query);
        $mysql->query("INSERT INTO planned_indicators_action_log (planned_indicators_id, user_id, action, datetime) 
                       VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
        $mysql->query("UPDATE planned_indicators_amounts_implementation SET edit_mode = false WHERE planned_indicators_id = '" . $data['id'] . "'");
    }

    if ($result == 1) {
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    } else {
        echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    }
    $mysql->close();
}


function DeletePlannedIndicator($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("INSERT INTO planned_indicators_action_log (planned_indicators_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    $mysql->query("INSERT INTO planned_indicators_amounts_action_log (planned_indicators_amounts_id, user_id, action, datetime) 
                                VALUES ((SELECT id FROM planned_indicators_amounts WHERE planned_indicators_id = '" . $data['id'] . "'), '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    $mysql->query("INSERT INTO planned_indicators_amounts_implementation_action_log (planned_indicators_amounts_implementation_id, user_id, action, datetime) 
                                VALUES ((SELECT id FROM planned_indicators_amounts_implementation WHERE planned_indicators_id = '" . $data['id'] . "'), '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    $mysql->query("INSERT INTO fact_indicators_amounts_action_log (fact_indicators_amounts_id, user_id, action, datetime) 
                                VALUES ((SELECT id FROM fact_indicators_amounts WHERE planned_indicators_id = '" . $data['id'] . "'), '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    $result = $mysql->query("DELETE FROM planned_indicators WHERE id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM planned_indicators_amounts WHERE planned_indicators_id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM planned_indicators_amounts_implementation WHERE planned_indicators_id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM fact_indicators_amounts WHERE planned_indicators_id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM planned_indicators_amounts_vat_sign WHERE planned_indicators_id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM planned_indicators_amounts_implementation_vat_sign WHERE planned_indicators_id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM planned_indicators_contracts_directory WHERE planned_indicators_id = '" . $data['id'] . "'");
    $result = $mysql->query("DELETE FROM planned_indicators_counterparties WHERE planned_indicators_id = '" . $data['id'] . "'");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function GetTransferYear($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(a.year) as year FROM planned_indicators a
                             LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                             WHERE b.fundholders_directory_id = '" . $data['fundholderId'] . "'");

    while ($row = $result->fetch_assoc()) {
        array_push($data_array,  $row["year"]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function SaveTransferPlannedIndicator($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_entries = [];
    $result = $mysql->query("SELECT a.* FROM `planned_indicators` a 
                             LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id 
                             WHERE b.fundholders_directory_id = '" . $data['fundholderId'] . "' and a.year = '" .  $data['fromYear'] . "'");
    $save_result = null;
    while ($row = $result->fetch_assoc()) {
        $save_result = $mysql->query("INSERT INTO planned_indicators (unique_number, budget_articles_directory_id, counterparties_directory_id, 
                                                       new_codes_directory_id, article_name, datetime, year)
                                                VALUE ('" . intval($data['toYear'] . $row['budget_articles_directory_id']) . "', '" . $row['budget_articles_directory_id'] . "',
                                                       '" . $row['counterparties_directory_id'] . "', '" . $row['new_codes_directory_id'] . "', '" . mysqli_real_escape_string($mysql, $row['article_name']) . "',
                                                       '" . date('Y-m-d H:i:s') . "', '" . $data['toYear'] . "')");
        $planned_indicator_id = mysqli_insert_id($mysql);
        $mysql->query("INSERT INTO planned_indicators_amounts (planned_indicators_id, edit_mode) VALUES ('" . $planned_indicator_id . "', 1)");
        $mysql->query("INSERT INTO planned_indicators_amounts_implementation (planned_indicators_id, edit_mode) VALUES ('" . $planned_indicator_id . "', 1)");
        $mysql->query("INSERT INTO fact_indicators_amounts (planned_indicators_id) VALUES ('" . $planned_indicator_id . "')");
        $mysql->query("INSERT INTO planned_indicators_amounts_vat_sign (planned_indicators_id) VALUES ('" . $planned_indicator_id . "')");
        $mysql->query("INSERT INTO planned_indicators_amounts_implementation_vat_sign (planned_indicators_id) VALUES ('" . $planned_indicator_id . "')");
        SetFactTransferIndicatorsAmounts($planned_indicator_id, $row['new_codes_directory_id'], $data['toYear']);
    }
    if ($save_result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalGetBudgetPlanData($data)
{

    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM `planned_indicators_amounts` WHERE planned_indicators_id = '" . $data['id'] . "'");
    $row = $result->fetch_assoc();
    echo json_encode([
        "id" => $row["id"],
        "planned_indicators_id" => $row["planned_indicators_id"],
        "month_array_with_vat" => [
            floatval($row["01_with_vat"]),
            floatval($row["02_with_vat"]),
            floatval($row["03_with_vat"]),
            floatval($row["04_with_vat"]),
            floatval($row["05_with_vat"]),
            floatval($row["06_with_vat"]),
            floatval($row["07_with_vat"]),
            floatval($row["08_with_vat"]),
            floatval($row["09_with_vat"]),
            floatval($row["10_with_vat"]),
            floatval($row["11_with_vat"]),
            floatval($row["12_with_vat"]),
        ],
        "month_array_no_vat" => [
            floatval($row["01_no_vat"]),
            floatval($row["02_no_vat"]),
            floatval($row["03_no_vat"]),
            floatval($row["04_no_vat"]),
            floatval($row["05_no_vat"]),
            floatval($row["06_no_vat"]),
            floatval($row["07_no_vat"]),
            floatval($row["08_no_vat"]),
            floatval($row["09_no_vat"]),
            floatval($row["10_no_vat"]),
            floatval($row["11_no_vat"]),
            floatval($row["12_no_vat"]),
        ],
        "sum_with_vat" => floatval($row["sum_with_vat"]),
        "sum_no_vat" => floatval($row["sum_no_vat"]),
    ]);
    $mysql->close();
}

function ModalEditBudgetPlanSave($data, $user_id, $director_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = null;
    if ($director_role) {
        $result = $mysql->query(UpdatePlannedIndicatorsAmountQuery($data));
        $result = $mysql->query(UpdatePlannedIndicatorsAmountImplementationQuery($data));
        $mysql->query("UPDATE planned_indicators SET sum_with_vat = '" . $data['sumMonthWithVAT'] . "' WHERE id = '" . $data['id'] . "'");
        $mysql->query("INSERT INTO planned_indicators_amounts_action_log (planned_indicators_amounts_id, user_id, action, datetime) 
                                VALUES ('" . $data['budgetPlanId'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
        $mysql->query("INSERT INTO planned_indicators_action_log (planned_indicators_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    }
    return;
}

function UpdatePlannedIndicatorsAmountQuery($data)
{
    $values = "01_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][0] . "', 01_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][0] . "', " .
        "02_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][1] . "', 02_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][1] . "', " .
        "03_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][2] . "', 03_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][2] . "', " .
        "04_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][3] . "', 04_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][3] . "', " .
        "05_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][4] . "', 05_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][4] . "', " .
        "06_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][5] . "', 06_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][5] . "', " .
        "07_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][6] . "', 07_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][6] . "', " .
        "08_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][7] . "', 08_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][7] . "', " .
        "09_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][8] . "', 09_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][8] . "', " .
        "10_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][9] . "', 10_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][9] . "', " .
        "11_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][10] . "', 11_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][10] . "', " .
        "12_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][11] . "', 12_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][11] . "', " .
        "sum_with_vat = '" . $data['sumMonthWithVAT'] . "', sum_no_vat = '" . $data['sumMonthNoVAT'] . "'"
        . ($data['action'] === 'edit' ? " , edited = true " : ($data['action'] === 'set' ? ", set_after_transfer_mode = null" : ""));

    return "UPDATE planned_indicators_amounts SET " . $values . " WHERE planned_indicators_id = '" . $data['id'] . "'";
}

function UpdatePlannedIndicatorsAmountImplementationQuery($data)
{
    $values = "01_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][0] . "', 01_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][0] . "', " .
        "02_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][1] . "', 02_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][1] . "', " .
        "03_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][2] . "', 03_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][2] . "', " .
        "04_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][3] . "', 04_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][3] . "', " .
        "05_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][4] . "', 05_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][4] . "', " .
        "06_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][5] . "', 06_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][5] . "', " .
        "07_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][6] . "', 07_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][6] . "', " .
        "08_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][7] . "', 08_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][7] . "', " .
        "09_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][8] . "', 09_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][8] . "', " .
        "10_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][9] . "', 10_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][9] . "', " .
        "11_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][10] . "', 11_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][10] . "', " .
        "12_with_vat = '" . $data['monthPlannedIndicatorWithVAT'][11] . "', 12_no_vat = '" . $data['monthPlannedIndicatorNoVAT'][11] . "', " .
        "sum_with_vat = '" . $data['sumMonthWithVAT'] . "', sum_no_vat = '" . $data['sumMonthNoVAT'] . "'";

    return "UPDATE planned_indicators_amounts_implementation SET " . $values . " WHERE planned_indicators_id = '" . $data['id'] . "'";
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
        CreateTableDOM($admin_role, $financier_role, $director_role, $data);
        break;
    case "getMainSectionsRequest":
        GetMainSectionsData($fundholder_id);
        break;
    case "getSectionsRequest":
        GetSectionsData($data, $fundholder_id);
        break;
    case "getSubsectionsRequest":
        GetSubsectionsData($data, $fundholder_id);
        break;
    case "getServicesRequest":
        GetServicesData($data, $fundholder_id);
        break;
    case "getArticlesRequest":
        GetArticlesData($data, $fundholder_id);
        break;
    case "getCounterpartiesRequest":
        GetCounterpartiesData();
        break;
    case "getPlannedIndicatorRequest":
        GetPlannedIndicatorData($data);
        break;
    case "getOldCodesRequest":
        GetOldCodesData();
        break;
    case "getNewCodesRequest":
        GetNewCodesData($data);
        break;
    case "getContractsRequest":
        GetContractsDirectoryWithExceptionsData($data);
        break;
    case "getClearContractsRequest":
        GetClearContractsData($data);
        break;
    case "modalSaveAddPlannedIndicatorRequest":
        ModalSaveAddPlannedIndicator($data, $user_id, $director_role);
        break;
    case "modalSaveEditPlannedIndicatorRequest":
        ModalSaveEditPlannedIndicator($data, $user_id, $admin_role, $director_role, $financier_role, $fin_dir_role);
        if ($director_role) {
            ModalEditBudgetPlanSave($data["plannedIndicatorsEditData"], $user_id, $director_role);
        }
        break;
    case "deletePlannedIndicatorRequest":
        DeletePlannedIndicator($data, $user_id);
        break;
    case "modalGetTransferYearRequest":
        GetTransferYear($data);
        break;
    case "modalSaveTransferPlannedIndicatorRequest":
        SaveTransferPlannedIndicator($data);
        break;
    case "modalGetBudgetPlanRequest":
        ModalGetBudgetPlanData($data);
        break;
    default:
        break;
}
