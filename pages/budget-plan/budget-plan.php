<?php
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/check_session.php");


function GetBudgetData($data, $financier_role, $fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    if (!$financier_role)
        $data['fundholderId'] = $fundholder_id;
    $result = $mysql->query("
        SELECT 
            a.id as p_i_id, 
            a.article_name, 
            a.datetime, 
            a.year, 
            SUBSTRING(datetime, 1, 10) as date_create,
            IF(pise.sections_directory_id IS NULL, false, true) as exception_section,
            c.name as subsection_name, 
            d.name as section_name, 
            e.name as main_section_name, 
            f.additional_name as fundholder, 
            g.name as service,
            h.new_code, 
            h.id as new_code_id, 
            i.old_code, 
            GROUP_CONCAT(k.name SEPARATOR ', ') as counterparty, 
            l.*, 
            m.*
        FROM `planned_indicators` a
        LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
        LEFT JOIN subsections_directory c ON c.id = b.subsections_directory_id
        LEFT JOIN sections_directory d ON d.id = c.sections_directory_id
        LEFT JOIN main_sections_directory e ON e.id = d.main_sections_directory_id
        LEFT JOIN fundholders_directory f ON f.id = b.fundholders_directory_id
        LEFT JOIN services_directory g ON g.id = b.services_directory_id
        LEFT JOIN new_codes_directory h ON h.id = a.new_codes_directory_id
        LEFT JOIN old_codes_directory i ON i.id = h.old_codes_directory_id
        LEFT JOIN planned_indicators_counterparties pic ON pic.planned_indicators_id = a.id 
        LEFT JOIN counterparties_directory k ON k.id = pic.counterparties_directory_id
        LEFT JOIN planned_indicators_amounts l ON l.planned_indicators_id = a.id 
        LEFT JOIN planned_indicators_amounts_vat_sign m ON m.planned_indicators_id = a.id
        LEFT JOIN planned_indicators_sections_exceptions pise ON pise.sections_directory_id = d.id
        WHERE f.id = '" . $data['fundholderId'] . "' 
        AND a.year = '" . $data['year'] . "' 
        GROUP BY a.id
        ORDER BY IF(e.name LIKE 'Інвест. діяльність', c.name, a.id)
    ");


    $dateString1 = date("Y-m-d");
    $date1 = new DateTime($dateString1);





    while ($row = $result->fetch_assoc()) {
        $dateString2 = $row["date_create"];
        $date2 = new DateTime($dateString2);
        $interval = $date1->diff($date2);

        $totalDays = $interval->format('%a');
        array_push($data_array, [
            "id" => $row["p_i_id"],
            "new_code" => $row["new_code"],
            "new_code_id" => $row["new_code_id"],
            "main_section" => $row["main_section_name"],
            "section" => $row["section_name"],
            "subsection" => $row["subsection_name"],
            "fundholder" => $row["fundholder"],
            "service" => $row["service"],
            "article" => $row["article_name"],
            "year" => $row["year"],
            "counterparty" => $row["counterparty"],
            "01_with_vat" => $row["01_with_vat"],
            "01_no_vat" => $row["01_no_vat"],
            "02_with_vat" => $row["02_with_vat"],
            "02_no_vat" => $row["02_no_vat"],
            "03_with_vat" => $row["03_with_vat"],
            "03_no_vat" => $row["03_no_vat"],
            "04_with_vat" => $row["04_with_vat"],
            "04_no_vat" => $row["04_no_vat"],
            "05_with_vat" => $row["05_with_vat"],
            "05_no_vat" => $row["05_no_vat"],
            "06_with_vat" => $row["06_with_vat"],
            "06_no_vat" => $row["06_no_vat"],
            "07_with_vat" => $row["07_with_vat"],
            "07_no_vat" => $row["07_no_vat"],
            "08_with_vat" => $row["08_with_vat"],
            "08_no_vat" => $row["08_no_vat"],
            "09_with_vat" => $row["09_with_vat"],
            "09_no_vat" => $row["09_no_vat"],
            "10_with_vat" => $row["10_with_vat"],
            "10_no_vat" => $row["10_no_vat"],
            "11_with_vat" => $row["11_with_vat"],
            "11_no_vat" => $row["11_no_vat"],
            "12_with_vat" => $row["12_with_vat"],
            "12_no_vat" => $row["12_no_vat"],
            "sum_with_vat" => $row["sum_with_vat"],
            "sum_no_vat" => $row["sum_no_vat"],
            "edited" => $row["edited"],
            "edit_mode" => $row["edit_mode"],
            "editable" =>  $row["exception_section"] ? ($totalDays < 3 ? true : false) : true,
            "set_after_transfer_mode" => $row["set_after_transfer_mode"],
            "01" => $row["01"] == (1 || null) ? "" : "sign_vat",
            "02" => $row["02"] == (1 || null) ? "" : "sign_vat",
            "03" => $row["03"] == (1 || null) ? "" : "sign_vat",
            "04" => $row["04"] == (1 || null) ? "" : "sign_vat",
            "05" => $row["05"] == (1 || null) ? "" : "sign_vat",
            "06" => $row["06"] == (1 || null) ? "" : "sign_vat",
            "07" => $row["07"] == (1 || null) ? "" : "sign_vat",
            "08" => $row["08"] == (1 || null) ? "" : "sign_vat",
            "09" => $row["09"] == (1 || null) ? "" : "sign_vat",
            "10" => $row["10"] == (1 || null) ? "" : "sign_vat",
            "11" => $row["11"] == (1 || null) ? "" : "sign_vat",
            "12" => $row["12"] == (1 || null) ? "" : "sign_vat",
        ]);
    }
    $mysql->close();
    return $data_array;
}

function GetFundholdersData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM fundholders_directory");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "additional_name" => $row["additional_name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function CreateTableDOM($data, $admin_role, $financier_role, $director_role, $fundholder_id)
{
    $data_array = GetBudgetData($data, $financier_role, $fundholder_id);
    $hidden = ($director_role) ? "" : "hidden";

    echo "<table>";
    echo "<thead>
           <tr>
              <th id='main-table-th-id' class='main-table-th table-column-id sticky-table-column' rowspan='2'>№</th>";

    echo "<th id='main-table-th-actions' class='main-table-th table-column-actions sticky-table-column' rowspan='2' " . $hidden . ">";
    if ($admin_role || $director_role) {
        echo "<img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'>";
    }
    echo "</th>";
    echo "<th id='main-table-th-new-code' class='main-table-th table-column-new-code sticky-table-column' rowspan='2'>Код статті</th>
              <th id='main-table-th-fundholder' class='main-table-th table-column-fundholder sticky-table-column' rowspan='2'>ФУ</th>
              <th id='main-table-th-service' class='main-table-th table-column-service sticky-table-column' rowspan='2'>Служба</th>
              <th id='main-table-th-service' class='main-table-th table-column-article-name sticky-table-column' rowspan='2'>Найменування ТМЦ, робіт, послуг</th>
              <th id='main-table-th-counterparty' class='main-table-th table-column-counterparty sticky-table-column' rowspan='2'>Контрагент</th>
              <th id='main-table-th-header-plan-year' class='table-column-plan-year' colspan='2'>План на рік</th>

              <th id='header-plan-first-quater__table__th' class='first-quarter__table quarter' colspan='2'>План на 1 квартал</th>
              <th class='first-quarter__table' colspan='2'>План на січень</th>
              <th class='first-quarter__table' colspan='2'>План на лютий</th>
              <th class='first-quarter__table' colspan='2'>План на березень</th>
              <th id='header-plan-second-quater__table__th' class='second-quarter__table quarter' colspan='2'>План на 2 квартал</th>
              <th class='second-quarter__table' colspan='2'>План на квітень</th>
              <th class='second-quarter__table' colspan='2'>План на травень</th>
              <th class='second-quarter__table' colspan='2'>План на червень</th>
              <th id='header-plan-third-quater__table__th' class='third-quarter__table quarter' colspan='2'>План на 3 квартал</th>
              <th class='third-quarter__table' colspan='2'>План на липень</th>
              <th class='third-quarter__table' colspan='2'>План на серпень</th>
              <th class='third-quarter__table' colspan='2'>План на вересень</th>
              <th id='header-plan-fourth-quater__table__th' class='fourth-quarter__table quarter' colspan='2'>План на 4 квартал</th>
              <th class='fourth-quarter__table' colspan='2'>План на жовтень</th>
              <th class='fourth-quarter__table' colspan='2'>План на листопад</th>
              <th class='fourth-quarter__table' colspan='2'>План на грудень</th>
          </tr>
          <tr>
              <th id='main-table-th-footer-plan-year-sum-vat' class='table-column-plan-year'>Оплата, тис. грн. з ПДВ</th>
              <th id='main-table-th-footer-plan-year-sum-no-vat' class='table-column-plan-year'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th> 
          </tr></thead>";
    echo "<tbody>";
    foreach ($data_array as $key => $value) {

        $edited = $value["edited"] ? "<img src='../../templates/images/edit_head.png' alt='edit' class='sign-edit__image'>" : "";
        $disabled = ($value["edit_mode"] && !$value['set_after_transfer_mode'] && $value['editable']) ? "" : "disabled";
        if ($key === 0 || ($value['subsection'] !== $data_array[$key - 1]['subsection'])) {
            $colspan = ($director_role) ? 7 : 6;
            echo "<tr>";
            echo "<td class='main-section__table hierarchy-sections sticky-table-column' colspan='" . $colspan . "'>" . $value['main_section'] . "</td>";
            echo "<td class='hierarchy-sections' colspan='44'></td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class='section__table hierarchy-sections sticky-table-column' colspan='" . $colspan . "'>" . $value['section'] . "</td>";
            echo "<td class='hierarchy-sections' colspan='44'></td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class='subsection__table hierarchy-sections sticky-table-column' colspan='" . $colspan . "'>" . $value['subsection'] . "</td>";
            echo "<td class='hierarchy-sections' colspan='44'></td>";
            echo "</tr>";
        }
        echo "<tr class='hover__table__tr '>";
        echo "<td class='id__table sticky-table-column'>" . ($key + 1) .  "</td>";
        echo "<td class='edit__table sticky-table-column' " . $hidden . ">";
        if ($director_role) {
            echo "<div class='td-toolbar'>";
            if ($value['set_after_transfer_mode']) {
                echo "<input type='image' src='../../templates/images/add.png' alt='add' class='action__td__input' onclick='modalSetBudgetPlanOnClick(" . $value["id"] . ")' />";
            } else {
                echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input $disabled' onclick='modalEditBudgetPlanOnClick(" . $value["id"] . ")' $disabled/>";
                echo "<div>" . $edited . "</div>";
            }

            echo "</div>";
        }
        echo "</td>";
        echo "<td class='article-code__table sticky-table-column'>" . $value['new_code'] . "</td>";
        echo "<td class='fundholder__table sticky-table-column'>" . $value['fundholder'] . "</td>";
        echo "<td class='service__table sticky-table-column'>" . $value['service'] . "</td>";
        echo "<td class='article-name__table sticky-table-column'>" . $value['article'] . "</td>";
        echo "<td class='counterparty__table sticky-table-column'>" . $value['counterparty'] . "</td>";
        echo "<td class='plan-year__table'>" . number_format($value['sum_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='plan-year__table'>" . number_format($value['sum_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['01_with_vat'] + $value['02_with_vat'] + $value['03_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['01_no_vat'] + $value['02_no_vat'] + $value['03_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table " . $value['01'] . "'>" . number_format($value['01_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table'>" . number_format($value['01_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table " . $value['02'] . "'>" . number_format($value['02_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table'>" . number_format($value['02_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table " . $value['03'] . "'>" . number_format($value['03_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table'>" . number_format($value['03_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['04_with_vat'] + $value['05_with_vat'] + $value['06_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['04_no_vat'] + $value['05_no_vat'] + $value['06_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table " . $value['04'] . "'>" . number_format($value['04_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table'>" . number_format($value['04_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table " . $value['05'] . "'>" . number_format($value['05_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table'>" . number_format($value['05_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table " . $value['06'] . "'>" . number_format($value['06_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table'>" . number_format($value['06_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['07_with_vat'] + $value['08_with_vat'] + $value['09_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['07_no_vat'] + $value['08_no_vat'] + $value['09_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table " . $value['07'] . "'>" . number_format($value['07_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table'>" . number_format($value['07_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table " . $value['08'] . "'>" . number_format($value['08_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table'>" . number_format($value['08_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table " . $value['09'] . "'>" . number_format($value['09_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table'>" . number_format($value['09_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['10_with_vat'] + $value['11_with_vat'] + $value['12_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['10_no_vat'] + $value['11_no_vat'] + $value['12_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table " . $value['10'] . "'>" . number_format($value['10_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table'>" . number_format($value['10_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table " . $value['11'] . "'>" . number_format($value['11_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table'>" . number_format($value['11_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table " . $value['12'] . "'>" . number_format($value['12_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table'>" . number_format($value['12_no_vat'], 5, '.', ' ') . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
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
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
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
    return "UPDATE planned_indicators_amounts SET " . $values . " WHERE id = '" . $data['budgetPlanId'] . "'";
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

function GetPlanIndicatorsYearsInfo($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(a.year) as year FROM planned_indicators a
                             LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id"); // WHERE b.fundholders_directory_id = '" . $data['fundholderId'] . "'
    $index = 0;
    while ($row = $result->fetch_assoc()) {
        array_push($data_array,  $row["year"]);
        $index = $row["year"];
    }

    for ($i = 0, $currYear = strval(intval($index) + 1); $i < 1; $i++) {
        array_push($data_array,  $currYear);
        $currYear = strval(intval($currYear) + 1);
    }
    $mysql->close();
    echo json_encode($data_array);
}

// function CheckFactByArticle($new_code_id, $year) {
//     $connect = new DB_connect();
//     $mysql = $connect->Connect();
//     $mysql->set_charset("utf8");
//     $data_array = array();
//     $result = $mysql->query("SELECT ((SELECT count(b.id) 
//                                       FROM banks_register_additional_purpose b 
//                                       WHERE b.new_codes_directory_id = '" . $new_code_id . "' and SUBSTRING(b.oper_date, 1, 4) = '" . $year . "') + 
//                                      count(a.id)) as countFact 
//                              FROM banks_register a WHERE a.new_codes_directory_id = '" . $new_code_id . "' and SUBSTRING(a.operDate, 1, 4) = '" . $year . "'");
//     $row = $result->fetch_assoc();
//     $mysql->close();
//     return $row["countFact"] > 0 ? false : true;
// }

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
        CreateTableDOM($data, $admin_role, $financier_role, $director_role, $fundholder_id);
        break;
    case "getFundholdersRequest":
        GetFundholdersData();
        break;
    case "modalGetBudgetPlanRequest":
        ModalGetBudgetPlanData($data);
        break;
    case "modalEditBudgetPlanSaveRequest":
        ModalEditBudgetPlanSave($data, $user_id, $director_role);
        break;
    case "getPlanIndicatorsYearsInfoRequest":
        GetPlanIndicatorsYearsInfo($data);
        break;
    default:
        break;
}
