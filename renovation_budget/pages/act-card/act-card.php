<?php
require_once("../../../templates/classes/db_local.php");

function GetTreatyData($counterparty_id, $contract_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $articles_exceptions_array = GetArticlesExceptions();
    $treaty_data_result = $mysql->query("SELECT * FROM renovation_treaty_directory WHERE counterparties_directory_id = '" . $counterparty_id . "' and 
                                                                                         contracts_directory_id = '" . $contract_id . "'");
    while ($row = $treaty_data_result->fetch_assoc()) {
        $cost = SearchArticleException(floatval($row["article_name"]), $articles_exceptions_array);
        $amount = FormRemainderAmount(floatval($row['id']), floatval($row['amount']));
        array_push($dataArray, [
            "id" => floatval($row['id']),
            "counterparty_id" => floatval($row['counterparties_directory_id']),
            "contract_id" => floatval($row['contracts_directory_id']),
            "article_id" => floatval($row["planned_indicators_id"]),
            "article_name" => $row["article_name"],
            "name_service" =>  $row['name_of_services'],
            "type_equipment" => $row['type_of_equipment'],
            "amount" => $amount,
            "price_of_service_no_pdv" => floatval($row['price_of_service_no_pdv']),
            "cost_of_materials_no_pdv" => floatval($row['cost_of_materials_no_pdv']),
            "sum_treaty_row" => (floatval($row['price_of_service_no_pdv']) + floatval($cost)) * $amount,
            "articles_exceptions" => $articles_exceptions_array
        ]);
    }

    $mysql->close();
    return $dataArray;
}

function FormRemainderAmount($id, $amount)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT sum(if(amount is null, 0, amount)) as sum FROM renovation_treaty_act_card WHERE renovation_treaty_directory_id = '" . $id . "'");
    $row = $result->fetch_assoc();
    return floatval($amount) - floatval($row["sum"]);
    $dataArray = array();
}

function GetArticlesExceptions()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $articles_exceptions_array = array();
    $articles_exceptions_result = $mysql->query("SELECT * FROM renovation_articles_exceptions");
    while ($row = $articles_exceptions_result->fetch_assoc()) {
        array_push($articles_exceptions_array, [
            "id" => floatval($row["id"]),
            "article_id" => floatval($row["planned_indicators_id"]),
            "article_name" => $row["article_name"],
            "new_code" => $row["new_code"]
        ]);
    }
    $mysql->close();
    return $articles_exceptions_array;
}

function SearchArticleException($article_name, $array)
{
    foreach ($array as $key => $row) {
        if ($article_name === $row["article_name"])
            return true;
    }
    return false;
}

function GetCounterpartiesData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $treaty_array = array();
    $counterparties_array = array();
    $treaty_data_result = $mysql->query("SELECT * FROM `contracts_directory` WHERE substring(term, 1, 4) >= '" . date('Y') . "' and status = 'Відкритий'");
    $counterparties_data_result = $mysql->query("SELECT distinct(a.id) as counterparty_id, a.name as counterparty_name
                                                    FROM `counterparties_directory` a 
                                                    LEFT JOIN contracts_directory b ON a.id = b.counterparties_directory_id
                                                    LEFT JOIN planned_indicators_counterparties c ON c.counterparties_directory_id = a.id
                                                    LEFT JOIN planned_indicators e ON e.id = c.planned_indicators_id
                                                    LEFT JOIN budget_articles_directory d ON d.id = e.budget_articles_directory_id
                                                        WHERE (d.services_directory_id = 10 or d.services_directory_id = 11) and b.status = 'Відкритий' and b.edit_mode = 0
                                                        and (d.subsections_directory_id = 51 or d.subsections_directory_id = 53)"); // ид служб СТ и СТП
    while ($row = $treaty_data_result->fetch_assoc()) {
        if ($row["edit_mode"] != "1")
            array_push($treaty_array, [
                "id" => $row["id"],
                "counterparty_id" => $row["counterparties_directory_id"],
                "number_contract" => $row["number"],
                "sign_of_vat" => $row["vat_sign"],
                "status" => $row["status"]
            ]);
    }
    while ($row = $counterparties_data_result->fetch_assoc()) {
        array_push($counterparties_array, [
            "id" => $row["counterparty_id"],
            "name" => $row["counterparty_name"]
        ]);
    }
    $mysql->close();
    echo json_encode(["treaty" => $treaty_array, "counterparties" => $counterparties_array]);
}

function GetAllState($counterparty_id, $treaty_id)
{
    echo json_encode(GetTreatyData($counterparty_id, $treaty_id));
}

function SaveActCard($actNumber, $actDate, $counterpartyId, $contractId, $actCardData, $actAccountValue, $actNumberAccountValue)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM renovation_act_card WHERE act_number = '" . $actNumber . "' and act_date = '" . $actDate . "'");
    $check = $result->fetch_assoc();
    if ($check !== null) {
        echo json_encode(["text" => "Дані не збережено! Акт з таким номером вже існує.", "status" => false]);
        $mysql->close();
        exit();
    }
    $mysql->query("INSERT INTO renovation_act_card (act_number, act_account, act_number_account, act_date) 
                   VALUES ('" . mysqli_real_escape_string($mysql, $actNumber) . "', '" . $actAccountValue . "', '" . $actNumberAccountValue . "', '" . $actDate . "')");
    $act_card_id = $mysql->insert_id;
    foreach ($actCardData as $value) {
        $treaty_result = $mysql->query("SELECT article_name, article_year, planned_indicators_id FROM renovation_treaty_directory WHERE id = '" . $value['id'] . "'");
        $treaty = $treaty_result->fetch_assoc();
        $mysql->query("INSERT INTO renovation_treaty_act_card (renovation_treaty_directory_id, renovation_act_card_id, act_number, act_date, price_of_service_no_pdv, 
                                   cost_of_materials_no_pdv, amount, article_name, planned_indicators_id, article_year) 
                       VALUES ('" . $value['id'] . "', '" . $act_card_id . "', '" . mysqli_real_escape_string($mysql, $actNumber) . "', '" . $actDate . "', 
                               '" . $value['price_of_service_no_pdv'] . "', '" . $value['cost_of_materials_no_pdv'] . "', '" . $value['amount'] . "', '" . mysqli_real_escape_string($mysql, $treaty['article_name']) . "', 
                               '" . $treaty['planned_indicators_id'] . "', '" . $treaty['article_year'] . "')");
    }
    echo json_encode(["text" => "Дані успішно збережено!", "status" => true]);
    $mysql->close();
}

function SaveEditActCard($act_card_data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("UPDATE renovation_act_card SET act_number = '" . $act_card_data['act_number'] . "', 
                          act_account = '" . $act_card_data['act_account'] . "', 
                          act_number_account = '" . $act_card_data['act_number_account'] . "',
                          act_date = '" . $act_card_data['act_date'] . "' WHERE id = '" . $act_card_data['id'] . "'");
    foreach ($act_card_data['rtac'] as $value) {
        $mysql->query("UPDATE renovation_treaty_act_card SET price_of_service_no_pdv = '" . $value['price_of_service_no_pdv'] . "', 
                              cost_of_materials_no_pdv = '" . $value['cost_of_materials_no_pdv'] . "',
                              amount = '" . $value['amount'] . "' WHERE id = '" . $value['id'] . "'");
    }
    echo json_encode(["text" => "Дані успішно збережено!", "status" => true]);
    $mysql->close();
}

function GetNumberAct($treaty_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $rac_result = $mysql->query("SELECT distinct(rac.id) as rac_id, rac.* FROM renovation_treaty_act_card rtac
                                LEFT JOIN renovation_act_card rac ON rac.id = rtac.renovation_act_card_id
                                LEFT JOIN renovation_treaty_directory rtd ON rtd.id = rtac.renovation_treaty_directory_id
                                WHERE rtd.contracts_directory_id = '" . $treaty_id . "' and (rac.conducted is null or rac.conducted = 0 or rac.conducted = '')");

    $rac = [];

    while ($row = $rac_result->fetch_assoc()) {
        $rtac = [];
        $rtac_result = $mysql->query("SELECT distinct(rtac.id), rtac.*, rtd.name_of_services, rtd.type_of_equipment, 
                                            rtd.price_of_service_no_pdv as price_of_service_no_pdv_limit, rtd.cost_of_materials_no_pdv as cost_of_materials_no_pdv_limit , rtd.amount as amount_limit 
                                            FROM renovation_treaty_act_card rtac
                                  LEFT JOIN renovation_act_card rac ON rac.id = rtac.renovation_act_card_id
                                  LEFT JOIN renovation_treaty_directory rtd ON rtd.id = rtac.renovation_treaty_directory_id
                                  WHERE rtac.renovation_act_card_id = '" . ($row['rac_id']) . "'");



        while ($row_rtac = $rtac_result->fetch_assoc()) {
            $rtac_values_max_result = $mysql->query("SELECT sum(rtac.amount) as max_amount, sum(rtac.price_of_service_no_pdv) max_price, sum(rtac.cost_of_materials_no_pdv) as max_cost
                                                     FROM renovation_treaty_act_card rtac 
                                                     LEFT JOIN renovation_act_card rac ON rac.id = rtac.renovation_act_card_id
                                                     WHERE rtac.renovation_treaty_directory_id = '" . ($row_rtac['renovation_treaty_directory_id']) . "' ");
            //             echo "SELECT sum(rtac.amount) as max_amount, sum(rtac.price_of_service_no_pdv) max_price, sum(rtac.cost_of_materials_no_pdv) as max_cost
            // FROM renovation_treaty_act_card rtac 
            // LEFT JOIN renovation_act_card rac ON rac.id = rtac.renovation_act_card_id
            // WHERE rtac.renovation_treaty_directory_id = '" . ($row_rtac['renovation_treaty_directory_id']) . "' ";
            $rtac_values_max = $rtac_values_max_result->fetch_assoc();

            //echo $row_rtac["amount_limit"] . " " . $rtac_values_max["max_amount"];

            array_push($rtac, [
                "id" => $row_rtac["id"],
                "renovation_treaty_directory_id" => $row_rtac["renovation_treaty_directory_id"],
                "renovation_act_card_id" => $row_rtac["renovation_act_card_id"],
                "act_number" => $row_rtac["act_number"],
                "act_date" => $row_rtac["act_date"],
                "price_of_service_no_pdv" => $row_rtac["price_of_service_no_pdv"],
                "cost_of_materials_no_pdv" => $row_rtac["cost_of_materials_no_pdv"],
                "amount" => floatval($row_rtac["amount"]),
                "price_of_service_no_pdv_limit" => $row_rtac["price_of_service_no_pdv_limit"],
                "amount_limit" => floatval($row_rtac["amount_limit"]) - (floatval($rtac_values_max["max_amount"]) - floatval($row_rtac["amount"])), //  
                "article_name" => $row_rtac["article_name"],
                "planned_indicators_id" => $row_rtac["planned_indicators_id"],
                "article_year" => $row_rtac["article_year"],
                "name_of_services" => $row_rtac["name_of_services"],
                "type_of_equipment" => $row_rtac["type_of_equipment"],
            ]);
        }
        array_push($rac, [
            "id" => $row["id"],
            "act_number" => $row["act_number"],
            "act_account" => $row["act_account"],
            "act_number_account" => $row["act_number_account"],
            "act_date" => $row["act_date"],
            "rtac" => $rtac,
        ]);
    }

    echo json_encode($rac);
    $mysql->close();
}

function actCardConduct($actId)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("UPDATE renovation_act_card SET conducted = 1 WHERE id = '" . $actId . "'");
    echo json_encode(["text" => "Дані успішно збережено!", "status" => true]);
    $mysql->close();
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "getAllStateRequest":
        GetAllState($data["counterpartyId"], $data["treatyId"]);
        break;
    case "selectValueRequest":
        GetCounterpartiesData();
        break;
    case "actCardSaveRequest":
        SaveActCard($data["actNumber"], $data["actDate"], $data["counterpartyId"], $data["contractId"], $data["actCardData"], $data["actAccountValue"], $data["actNumberAccountValue"]);
        break;
    case "actCardEditSaveRequest":
        SaveEditActCard($data["actCardData"]);
        break;
    case "selectNumberActEditRequest":
        GetNumberAct($data['treatyId']);
        break;
    case "actCardConductRequest":
        actCardConduct($data['actId']);
        break;
    default:
        break;
}
