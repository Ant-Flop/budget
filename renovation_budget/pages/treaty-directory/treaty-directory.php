<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once("../../../templates/classes/db_local.php");

function CreateEntry($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO renovation_treaty_directory (counterparties_directory_id, counterparty_name, contracts_directory_id, contract_number, 
                                         contract_name, contract_term, planned_indicators_id, article_name, article_year, name_of_services, type_of_equipment, 
                                         amount, price_of_service_no_pdv, cost_of_materials_no_pdv, date_created) 
                             VALUES ('" . $data['counterpartyId'] . "', '" . mysqli_real_escape_string($mysql, $data['counterpartyName']) . "','" . $data['numberContractId'] . "', 
                                     '" . mysqli_real_escape_string($mysql, $data['numberContract']) . "', '" . mysqli_real_escape_string($mysql, $data['nameContract']) . "', 
                                     '" . $data['termContract'] . "', '" . mysqli_real_escape_string($mysql, $data['kindServiceId']) . "', '" . mysqli_real_escape_string($mysql, $data['kindServiceName']) . "', '" . $data['kindServiceYear'] . "', 
                                     '" . mysqli_real_escape_string($mysql, $data['nameService']) . "', '" . mysqli_real_escape_string($mysql, $data['typeEquipment']) . "', '" . $data['amount'] . "', 
                                     '" . $data['priceService'] . "', '" . $data['costMaterials'] . "', '" . date("Y-m-d") . "')");
    if ($result == 1)
        echo json_encode(["text" => "Дані успішно збережено!", "status" => true]);
    else echo json_encode(["text" => "Дані не збережено!", "status" => false]);
    $mysql->close();
}

function GetTreatyData($counterparty_id, $treaty_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $treaty_data_result = $mysql->query("SELECT a.*, b.vat_sign, b.edit_mode FROM renovation_treaty_directory a 
                                         LEFT JOIN contracts_directory b ON b.id = a.contracts_directory_id 
                                         WHERE a.counterparties_directory_id = '" . $counterparty_id . "' and 
                                         a.contracts_directory_id = '" . $treaty_id . "'"); // 
    while ($row = $treaty_data_result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row['id'],
            "counterparty_id" => $row['counterparties_directory_id'],
            "contract_id" => $row['contracts_directory_id'],
            "contract_name" => $row['contract_name'],
            "contract_term" => $row['contract_term'],
            "kind_service" => $row['article_name'],
            "kind_service_id" => $row['planned_indicators_id'],
            "name_service" => $row['name_of_services'],
            "type_equipment" => $row['type_of_equipment'],
            "amount" => $row['amount'],
            "price_of_service_no_pdv" => $row['price_of_service_no_pdv'],
            "cost_of_materials_no_pdv" => $row['cost_of_materials_no_pdv'],
            "sign_of_vat" => $row['vat_sign'],
            "edit_mode" => $row["edit_mode"]
        ]);
    }
    $mysql->close();
    return $dataArray;
}

function GetContractEntriesData($counterparty_id, $treaty_id)
{
    echo json_encode(GetTreatyData($counterparty_id, $treaty_id));
}

function GetEditContractStatus($treaty_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $status = false;
    $result = $mysql->query("SELECT edit_mode FROM contracts_directory WHERE id = '" . $treaty_id . "'");
    while ($row = $result->fetch_assoc())
        $status = $row["edit_mode"] === "1" ? true : false;
    $mysql->close();
    echo json_encode(["edit_status" => $status]);
}

function GetCounterpartiesData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8"); // vtyznm!
    $treaty_array = array();
    $counterparties_array = array();
    $counterparties_data_result = $mysql->query("SELECT distinct(a.id) as counterparty_id, a.name as counterparty_name, 
                                                (SELECT count(e.id) FROM contracts_directory e WHERE e.counterparties_directory_id = a.id and e.status = 'Відкритий') as count_contracts
                                                    FROM `counterparties_directory` a 
                                                    LEFT JOIN contracts_directory b ON a.id = b.counterparties_directory_id
                                                    LEFT JOIN planned_indicators c ON c.counterparties_directory_id = a.id
                                                    LEFT JOIN budget_articles_directory d ON d.id = c.budget_articles_directory_id
                                                        WHERE (d.services_directory_id = 10 or d.services_directory_id = 11) and b.status = 'Відкритий' 
                                                        and (d.subsections_directory_id = 51 or d.subsections_directory_id = 53)");
    $treaty_data_result = $mysql->query("SELECT * FROM `contracts_directory` WHERE status = 'Відкритий'"); // показывает только открытые договора
    while ($row = $counterparties_data_result->fetch_assoc()) {
        array_push($counterparties_array, [
            "id" => $row["counterparty_id"],
            "name" => $row["counterparty_name"]
        ]);
    }
    while ($row = $treaty_data_result->fetch_assoc()) {
        array_push($treaty_array, [
            "id" => $row["id"],
            "counterparty_id" => $row["counterparties_directory_id"],
            "number_contract" => $row["number"],
            "name_contract" => $row["name"],
            "term_contract" => $row["term"],
            "status" => $row["status"],
            "edit_mode" => $row["edit_mode"] === "1" ? true : false
        ]);
    }
    $mysql->close();
    echo json_encode(["treaties" => $treaty_array, "counterparties" => $counterparties_array]);
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
            "id" => $row["id"],
            "article_id" => $row["planned_indicators_id"],
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

function CreateTableDOM($counterparty_id, $treaty_id)
{
    $treaty_array = GetTreatyData($counterparty_id, $treaty_id);
    $articles_exceptions_array = GetArticlesExceptions();
    $sum_price_of_service_no_pdv = 0;
    $sum_cost_of_materials_no_pdv = 0;
    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th rowspan='2' class='th-width-auto th-low-border'>№</th>";
    if (count($treaty_array) > 0)
        if ($treaty_array[0]['edit_mode'] == '1') echo "<th rowspan='2' class='th-width-auto th-low-border'><image src='../../templates/images/edit_head.png' alt='edit' id='th-edit__image' /></th>";
    echo "<th rowspan='2' class='th-low-border' id='th-name-contract'>Назва договору</th>";
    echo "<th rowspan='2' class='th-low-border' id='th-term'>Термін дії договору<br>(кінцевий термін)</th>";
    echo "<th rowspan='2' class='th-low-border th-head-article-budget'>Стаття бюджету</th>";
    echo "<th rowspan='2' class='th-low-border'>Найменування послуг</th>";
    echo "<th rowspan='2' class='th-low-border'>Тип обладнання</th>";
    echo "<th rowspan='2' class='th-low-border' id='th-amount'>Кількість</th>";
    echo "<th colspan='3' class='table-th-colspan-head-background th-head-cost-services'>Вартість послуг</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th class='table-th-colspan-foot-background table-th-sticky-second-row th-low-border'>Ціна послуги, грн. без ПДВ</th>";
    echo "<th class='table-th-colspan-foot-background table-th-sticky-second-row th-low-border'>Вартість матеріалів, грн. без ПДВ</th>";
    echo "<th class='table-th-colspan-foot-background table-th-sticky-second-row th-low-border'>Сума, грн. без ПДВ</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    $result_sum = 0;
    $amount_sum = 0;
    for ($i = 0; $i < count($treaty_array); $i++) {
        // $cost = SearchArticleException($treaty_array[$i]["kind_service"], $articles_exceptions_array) ? 0 : $treaty_array[$i]['cost_of_materials_no_pdv'];
        $cost = $treaty_array[$i]['cost_of_materials_no_pdv'];
        $sum_price_of_service_no_pdv += $treaty_array[$i]['price_of_service_no_pdv'];
        $sum_cost_of_materials_no_pdv +=  $cost;
        $result_sum += $treaty_array[$i]['amount'] * ($treaty_array[$i]['price_of_service_no_pdv'] + $cost); // + $cost
        $amount_sum += $treaty_array[$i]['amount'];
        echo "<tr class='tbody-tr-row'>";
        echo "<td>" . ($i + 1) . "</td>";
        if (count($treaty_array) > 0)
            if ($treaty_array[$i]['edit_mode'] == '1') echo "<td class='edit__td'><input type='image' src='../../templates/images/edit.png' alt='edit' class='td-edit__image' data-id='" . $treaty_array[$i]['id'] . "' data-index-row='$i' onclick='editEntryOnClick(this)'/></td>";
        echo "<td class='contract-name__td'>" . $treaty_array[$i]['contract_name'] . "</td>";
        echo "<td class='contract-term__td'>" . $treaty_array[$i]['contract_term'] . "</td>";
        echo "<td class='kind-service__td'>" . $treaty_array[$i]['kind_service'] . "</td>";
        echo "<td class='name-service__td'>" . $treaty_array[$i]['name_service'] . "</td>";
        echo "<td class='type-equipment__td'>" . $treaty_array[$i]['type_equipment'] . "</td>";
        echo "<td class='amount__td table-td-colspan-blue-background'>" . $treaty_array[$i]['amount'] . "</td>";
        echo "<td class='price__td table-td-colspan-blue-background'>" . number_format($treaty_array[$i]['price_of_service_no_pdv'], 2, ".", " ") . "</td>";
        echo "<td class='cost__td table-td-colspan-blue-background'>" . number_format($treaty_array[$i]['cost_of_materials_no_pdv'], 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-blue-background' id='sum-row-" . $treaty_array[$i]['id'] . "'>" . number_format($treaty_array[$i]['amount'] * ($treaty_array[$i]['price_of_service_no_pdv'] + $cost), 2, ".", " ") . "</td>"; // + $cost

        echo "</tr>";
    }
    echo "</tbody>";
    // $sum_price_of_service_pdv = $sum_price_of_service_no_pdv * 0.2;
    // $sum_cost_of_materials_pdv = $sum_cost_of_materials_no_pdv * 0.2;
    // $sum_price_of_service_with_pdv = $sum_price_of_service_no_pdv + $sum_price_of_service_pdv;
    // $sum_cost_of_materials_with_pdv = $sum_cost_of_materials_no_pdv + $sum_cost_of_materials_pdv;
    // $common_sum_no_pdv = $sum_price_of_service_no_pdv + $sum_cost_of_materials_no_pdv;
    // $common_sum_with_pdv = $sum_price_of_service_with_pdv + $sum_cost_of_materials_with_pdv;
    if (count($treaty_array) > 0)
        $col_count = $treaty_array[0]['edit_mode'] == '1' ? 4 : 3;
    else $col_count = 3;
    $sign = "";
    if (count($treaty_array) > 0)
        $sign = $treaty_array[0]['sign_of_vat'];
    echo "<tfoot  class='unselectable'>";
    echo "<tr class='table-footer'>";
    echo "<td colspan='$col_count' class='table-td-colspan-blue-font'>Всього, грн. без ПДВ</td>";
    echo "<td colspan='3'></td>";
    echo "<td class='table-td-colspan-red-font'>" . $amount_sum . "</td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td class='table-td-colspan-red-font'>" . number_format($result_sum, 2, ".", " ") . "</td>";
    echo "</tr>";
    echo "<tr class='table-footer'>";
    echo "<td colspan='$col_count' class='table-td-colspan-blue-font'>ПДВ, 20%</td>";
    echo "<td colspan='3'></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td class='table-td-colspan-red-font'>" . number_format($sign === 'Без ПДВ' ? 0 : $result_sum * 0.2, 2, ".", " ") . "</td>";
    echo "</tr>";
    echo "<tr class='table-footer'>";
    echo "<td colspan='$col_count' class='table-td-colspan-blue-font'>Всього, грн. з ПДВ</td>";
    echo "<td colspan='3'></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "<td class='table-td-colspan-red-font'>" . number_format($sign === 'Без ПДВ' ? $result_sum : $result_sum * 1.2, 2, ".", " ") . "</td>";
    echo "</tr>";
    echo "</tfoot>";
    echo "</table>";
}

function GetArticlesData($counterparty_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $articles_array = array();
    $year = date("Y");
    $result_articles = $mysql->query("SELECT * FROM planned_indicators WHERE year = '" . $year . "' and counterparties_directory_id = '" . $counterparty_id . "'");
    while ($articles = $result_articles->fetch_assoc()) {
        array_push($articles_array, [
            "id" => $articles["id"],
            "name" => $articles["article_name"],
            "article_id" => $articles["budget_articles_directory_id"],
            "year" => $articles["year"]
        ]);
    }

    $mysql->close();
    echo json_encode($articles_array);
}

function SaveEditedEntry($entryData)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $contract_term = $entryData['contract_term'] != '' ? " contract_term = '" . $entryData['contract_term'] . "'" : " contract_term = null";
    $mysql->query("UPDATE renovation_treaty_directory SET contract_name = '" . mysqli_real_escape_string($mysql, $entryData['contract_name']) . "', " . $contract_term . ", 
                                    name_of_services = '" . mysqli_real_escape_string($mysql, $entryData['name_service']) . "', 
                                    type_of_equipment = '" . mysqli_real_escape_string($mysql, $entryData['type_equipment']) . "', 
                                    article_name = '" . mysqli_real_escape_string($mysql, $entryData['kind_service']) . "',
                                    planned_indicators_id = '" . mysqli_real_escape_string($mysql, $entryData['kind_service_id']) . "',
                                    amount = '" . $entryData['amount'] . "', 
                                    price_of_service_no_pdv = '" . $entryData['price_of_service_no_pdv'] . "',
                                    cost_of_materials_no_pdv = '" . $entryData['cost_of_materials_no_pdv'] . "' WHERE id = '" . $entryData['id'] . "'");

    $mysql->close();
    echo json_encode(["text" => "Дані успішно збережено!", "status" => true]);
}

function BlockingEditModeInTreaty($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("UPDATE contracts_directory SET edit_mode = '0' WHERE id = '$id'");
    $result = $mysql->query("SELECT number FROM contracts_directory WHERE id = '$id'");
    $row = $result->fetch_assoc();
    $mysql->close();
    echo json_encode(["text" => "Редагування по договору " . $row['number'] . " заблоковано!", "status" => true]);
}



$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM($data["counterpartyId"], $data["treatyId"]);
        break;
    case "selectValueRequest":
        GetCounterpartiesData();
        break;
    case "contractEntriesArrayRequest":
        GetContractEntriesData($data["counterpartyId"], $data["treatyId"]);
        break;
    case "contractEditStatusRequest":
        GetEditContractStatus($data["treatyId"]);
        break;
    case "saveEditedEntryRequest":
        SaveEditedEntry($data["entryData"]);
        break;
    case "blockingEditModeRequest":
        BlockingEditModeInTreaty($data["treatyId"]);
        break;
    case "modalArticleRequest":
        GetArticlesData($data["counterpartyId"]);
        break;
    case "modalSaveRequest":
        CreateEntry($data);
        break;
    case "getArticlesRequest":
        GetArticlesData($data["counterpartyId"]);
        break;
    default:
        break;
}