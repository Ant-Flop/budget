<?php
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/check_session.php");


function GetROCUCReportContractData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM `contracts_directory` WHERE edit_mode = 0");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "counterparties_id" => $row["counterparties_directory_id"],
            "number" => $row["number"],
            "name" => $row["name"],
            "status" => $row["status"],
            "vat_sign" => $row["vat_sign"],
            "term" => $row["term"],
            "edit_mode" => $row["edit_mode"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetROCUCReportData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    if (!isset($data))
        return [
            "data_treaty_array" => [],
            "data_treaty_act_card_array" => [],
        ];
    $result_treaty_info = $mysql->query("SELECT a.counterparty_name, a.contract_name, sum((a.price_of_service_no_pdv + a.cost_of_materials_no_pdv) * a.amount) as sum 
                                         FROM `renovation_treaty_directory` a WHERE a.contracts_directory_id = '" . $data . "'");
    $row_treaty_info = $result_treaty_info->fetch_assoc();
    $data_treaty_array = [
        "counterparty_name" => $row_treaty_info["counterparty_name"],
        "contract_name" => $row_treaty_info["contract_name"],
        "sum" => $row_treaty_info["sum"],
    ];
    $data_treaty_act_card_array = array();
    $result_treaty_act_card_info = $mysql->query("SELECT b.act_number, b.act_date, sum((b.price_of_service_no_pdv + b.cost_of_materials_no_pdv) * b.amount) as sum 
                                                  FROM `renovation_treaty_directory` a 
                                                  LEFT JOIN renovation_treaty_act_card b ON b.renovation_treaty_directory_id = a.id
                                                  WHERE a.contracts_directory_id = '" . $data . "' and b.act_number is not null GROUP BY b.act_number");

    $sum_acts = 0;
    while ($row = $result_treaty_act_card_info->fetch_assoc()) {
        array_push($data_treaty_act_card_array, [
            "act_number" => $row["act_number"],
            "act_date" => $row["act_date"],
            "sum" => $row["sum"],
        ]);
        $sum_acts += $row["sum"];
    }
    $mysql->close();
    return [
        "data_treaty_array" => $data_treaty_array,
        "data_treaty_act_card_array" => $data_treaty_act_card_array,
        "sum_acts" => $sum_acts,
    ];
}

function CreateROCUCReportDOM($data)
{
    $data_array = GetROCUCReportData($data);
    $data_treaty_array = $data_array["data_treaty_array"];
    $data_treaty_act_card_array = $data_array["data_treaty_act_card_array"];
    if (count($data_treaty_array) === 0)
        return;
    echo "<div class='above-table-bar'>";
    echo "<div>";
    echo "<span>Контрагент</span><br/>";
    echo "<input value='" . $data_treaty_array['counterparty_name'] . "' disabled>";
    echo "</div>";
    echo "<div>";
    echo "<span>Назва договору</span><br/>";
    echo "<input value='" . $data_treaty_array['contract_name'] . "' disabled>";
    echo "</div>";
    echo "<div>";
    echo "<span>Сума договору, тис.грн без ПДВ</span><br/>";
    echo "<input value='" . number_format($data_treaty_array['sum'] / 1000, 5, '.', ' ') . "' disabled>";
    echo "</div>";
    echo "<div>";
    echo "<span>Всього нарастаючим, тис грн.без ПДВ</span><br/>";
    echo "<input value='" . number_format($data_array["sum_acts"] / 1000, 5, '.', ' ') . "' disabled>";
    echo "</div></div>";

    echo "<div id='main-table'><table>";
    echo "<thead><tr>";
    foreach ($data_treaty_act_card_array as $key => $element) {
        if ($key === 0) {
            echo "<th>";
            echo "</th>";
        }
        echo "<th>" . ($key + 1) . "</th>";
        if ($key === count($data_treaty_act_card_array) - 1) {
            echo "<th class='last-th'>";
            echo "</th>";
        }
    }
    echo "</tr></thead>";
    echo "<tbody>";
    echo "<tr>";
    foreach ($data_treaty_act_card_array as $key => $element) {
        if ($key === 0) {
            echo "<th>";
            echo "№ акту";
            echo "</th>";
        }
        echo "<td>";
        echo $element["act_number"];
        echo "</td>";
        if ($key === count($data_treaty_act_card_array) - 1) {
            echo "<td>";
            echo "</td>";
        }
    }
    echo "</tr>";

    echo "<tr>";
    foreach ($data_treaty_act_card_array as $key => $element) {
        if ($key === 0) {
            echo "<th>";
            echo "Дата акту";
            echo "</th>";
        }
        echo "<td>";
        echo $element["act_date"];
        echo "</td>";
        if ($key === count($data_treaty_act_card_array) - 1) {
            echo "<td>";
            echo "</td>";
        }
    }
    echo "</tr>";

    echo "<tr>";
    foreach ($data_treaty_act_card_array as $key => $element) {
        if ($key === 0) {
            echo "<th>";
            echo "Сума, тис грн. без ПДВ";
            echo "</th>";
        }
        echo "<td>";
        echo number_format($element["sum"] / 1000, 5, '.', ' ');
        echo "</td>";
        if ($key === count($data_treaty_act_card_array) - 1) {
            echo "<td>";
            echo "</td>";
        }
    }
    echo "</tr>";
    echo "</tbody>";
    echo "</table></div>";
}

function GetROEBBAReportBudgetArticleData($fundholder_id, $year)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(b.id), b.name, a.id as planned_indicators_id FROM planned_indicators a
                             LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                             LEFT JOIN renovation_treaty_directory c ON c.planned_indicators_id = a.id 
                             WHERE a.year = '" . $year . "' and b.fundholders_directory_id = '" . $fundholder_id . "' and
                                   c.planned_indicators_id = a.id ");

    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
            "planned_indicators_id" => $row["planned_indicators_id"],
        ]);
    }

    $mysql->close();
    echo json_encode($data_array);
}



function GetROEBBAReportData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_treaty_array = [];
    if (!isset($data))
        return $data_treaty_array;
    $result_treaty_info = $mysql->query("SELECT a.contracts_directory_id, a.contract_number, a.contract_name, b.status, a.counterparty_name, a.contract_name, sum((a.price_of_service_no_pdv + a.cost_of_materials_no_pdv) * a.amount) as sum 
                                         FROM renovation_treaty_directory a
                                         LEFT JOIN contracts_directory b ON b.id = a.contracts_directory_id 
                                         WHERE a.planned_indicators_id = '" . $data . "' and b.edit_mode = 0 GROUP BY a.contract_number ");
    while ($row = $result_treaty_info->fetch_assoc()) {
        array_push($data_treaty_array, [
            "contracts_directory_id" => $row["contracts_directory_id"],
            "contract_number" => $row["contract_number"],
            "contract_name" => $row["contract_name"],
            "status" => $row["status"],
            "counterparty_name" => $row["counterparty_name"],
            "sum" => $row["sum"],
        ]);
    }
    $mysql->close();
    return $data_treaty_array;
}

function CreateROEBBAReportDOM($data)
{
    $data_array =  GetROEBBAReportData($data);
    $sum = 0;
    if (count($data_array) === 0)
        return;
    echo "<div id='main-table'><table>";
    echo "<tbody>";
    foreach ($data_array as $key => $element) {
        $sum += $element['sum'];
        echo "<tr>";
        echo "<th>№ Договору</th>";
        echo "<td><button onclick='ROCUCReportContractNumberButtonOnClick(" . $element['contracts_directory_id'] . ")'>" . $element['contract_number'] . "</button></td>";
        echo "<td>" . $element['status'] . "</td>";
        echo "<th>Контрагент</th>";
        echo "<td>" . $element['counterparty_name'] . "</td>";
        echo "<th>Назва договору</th>";
        echo "<td>" . $element['contract_name'] . "</td>";
        echo "<th>Всього нарастаючим, тис грн.без ПДВ</th>";
        echo "<td>" . number_format($element['sum'] / 1000, 5, '.', ' ') . "</td>";
        if ($key === count($data_array) - 1) {
            echo "<td class='last-th'>";
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "<tfoot>";
    echo "<tr>";
    echo "<th colspan='8'>Всього по статті бюджету, тис грн.без ПДВ</th>";
    echo "<td>" . number_format($sum / 1000, 5, '.', ' ') . "</td>";
    echo "<td class='last-th'></td>";
    echo "<tr>";
    echo "</tfoot>";
    echo "</table></div>";
}

// function GetDailyReportData() {
// }

function CreateDailyReportDOM()
{
    header('Location: ../../templates/classes/ParentExcelReport.php');
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
                "report_viewer_role" => $report_viewer_role,
            ]
        ]);
        break;
    case "register_of_costs_under_the_contract_report":
        CreateROCUCReportDOM($data["conditionInfo"]);
        break;
    case "getROCUCReportContractDataRequest":
        GetROCUCReportContractData();
        break;
    case "register_of_expenditures_by_budget_article_report":
        CreateROEBBAReportDOM($data["conditionInfo"]);
        break;
    case "getROEBBAReportBudgetArticleDataRequest":
        GetROEBBAReportBudgetArticleData($fundholder_id, $data["year"]);
        break;
    case "daily_report":
        CreateDailyReportDOM();
        break;

    default:
        break;
}
