<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetActsData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT b.act_number, b.act_date, sum((b.price_of_service_no_pdv + b.cost_of_materials_no_pdv) * b.amount) as sum FROM renovation_treaty_directory a
                             LEFT JOIN renovation_treaty_act_card b ON b.renovation_treaty_directory_id = a.id
                             WHERE a.counterparties_directory_id = '" . $data['counterpartyId'] . "' and a.contracts_directory_id = '" . $data['contractId'] . "' and 
                                   a.planned_indicators_id = '" . $data['plannedIndicatorsId'] . "' and 
                                   b.act_number is not null GROUP BY b.act_number");
    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "act_number" => $row["act_number"],
            "act_date" => $row["act_date"],
            "sum" => $row["sum"],
        ]);
    }
    $mysql->close();
    return $data_array;
}

function CreateTableDOM($data)
{
    $data_array = GetActsData($data);
    //шапка таблицы
    echo "<table>";
    echo "</tbody>";
    
    foreach($data_array as $key => $element) {
        if($key === 0)
            echo "<tr><th>№ акту</th>";
        echo "<td>" . $element['act_number'] . "</td>";
        if($key === count($data_array) - 1) 
            echo "<td class='last-td'></td></tr>";
    }

    foreach($data_array as $key => $element) {
        if($key === 0)
            echo "<tr><th>Дата акту</th>";
        echo "<td>" . $element['act_date'] . "</td>";
        if($key === count($data_array) - 1) 
            echo "<td class='last-td'></td></tr>";
    }

    foreach($data_array as $key => $element) {
        if($key === 0)
            echo "<tr><th>Сума акту, тис. без ПДВ</th>";
        echo "<td>" . number_format($element['sum'], 2, '.', ' ') . "</td>";
        if($key === count($data_array) - 1) 
            echo "<td class='last-td'></td></tr>";
    }

    if(count($data_array) === 0) {
        echo "<tr><th>№ акту</th><td class='last-td'></td></tr>";
        echo "<tr><th>Дата акту</th><td class='last-td'></td></tr>";
        echo "<tr><th>Сума акту, тис. без ПДВ</th><td class='last-td'></td></tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function GetServiceData($fundholder_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM services_directory WHERE fundholders_directory_id = '" . $fundholder_id . "'");
    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetBudgetArticleData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    if(!isset($data["serviceId"])) {
        $mysql->close();
        echo json_encode($data_array);
        return;
    }
    $result = $mysql->query("SELECT a.id, a.article_name as name, c.id as counterparty_id, c.name as counterparty_name  FROM planned_indicators a
                             LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                             LEFT JOIN counterparties_directory c ON c.id = a.counterparties_directory_id
                             WHERE b.services_directory_id = '" . $data['serviceId'] . "' and a.year = '" . $data['year'] . "' and a.counterparties_directory_id != 0");
    

    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "name" => $row["name"],
            "counterparty_id" => $row["counterparty_id"],
            "counterparty_name" => $row["counterparty_name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function getContractData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(contracts_directory_id), contract_number, counterparties_directory_id, planned_indicators_id  
                             FROM renovation_treaty_directory 
                             WHERE counterparties_directory_id = '" . $data['counterpartyId'] . "' and 
                                   planned_indicators_id = '" . $data['plannedIndicatorsId'] . "'");
    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["contracts_directory_id"],
            "number" => $row["contract_number"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}



$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM($data);
        break;
    case "getServiceDataRequest":
        GetServiceData($fundholder_id);
        break;
    case "getBudgetArticleDataRequest":
        GetBudgetArticleData($data);
        break;
    case "getCounterpartyDataRequest":
        break;
    case "getContractDataRequest":
        getContractData($data);
        break;
    default:
        break;
}
?>