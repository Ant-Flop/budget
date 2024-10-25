<?php
require_once("../../../templates/classes/db_local.php");

function GetSubsectionData($startMonth, $endMonth, $subsectionData, $monthRange)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $subsection_info_array = array();
    if ($startMonth === "" || $endMonth === "") {
        echo json_encode([]);
        $mysql->close();
        exit();
    }

    foreach ($subsectionData as $value) { //or a.unique_number = 2023137 or a.unique_number = 2023139
        $plan_id_result = $mysql->query("SELECT DISTINCT(a.id) FROM planned_indicators a 
        LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
        LEFT JOIN services_directory c ON c.id = b.services_directory_id
        WHERE c.id = '" . $value['id'] . "' and
              a.counterparties_directory_id != 0 and 
              a.year = SUBSTRING('" . $startMonth . "', 1, 4) and 
              (a.article_name = 'Ремонт і ТО друкувальних пристроїв' or
              a.article_name = 'Ремонт комп\'ютерного обладнання' or
              a.article_name = 'Ремонт кондиціонерів, встановлення кондиціонерів' or
              a.article_name = 'Ремонт обладнання АСУТП і телемеханіки' or
              a.article_name = 'Ремонт обладнання РРС' or
              a.article_name = 'Ремонт радіостанцій, радіотерміналів, мобільних телефонів, АТС')"); // исправить!
        $sum_with_vat = 0;
        $sum_no_vat = 0;
        $array_id = array();
        while ($plan = $plan_id_result->fetch_assoc()) {
            $sum_plans_result = $mysql->query("SELECT (" . $monthRange[0] . ") as sum_with_vat, (" . $monthRange[1] . ") as sum_no_vat 
                                               FROM planned_indicators_amounts_implementation WHERE planned_indicators_id = '" . $plan['id'] . "'");
            $sum_plans = $sum_plans_result->fetch_assoc();
            $sum_with_vat += floatval($sum_plans["sum_with_vat"]);
            $sum_no_vat += floatval($sum_plans["sum_no_vat"]);
            array_push($array_id, [
                "id" => $plan["id"]
            ]);
        }
        array_push($subsection_info_array, [
            "id" => $value["id"],
            "name" => $value["name"],
            "shortname" => $value["shortname"],
            "array_plan_id" => $array_id,
            "sum_with_vat" => number_format($sum_with_vat, 5, ".", " "),
            "sum_no_vat" => number_format($sum_no_vat, 5, ".", " ")
        ]);
    }
    echo json_encode($subsection_info_array);
    $mysql->close();
}

function GetMainTableData($startMonth, $endMonth, $subsectionData)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $main_data_array = array();
    $condition_treaty = "";
    foreach ($subsectionData as $key => $subsection_value) {
        if ($key > 0 && count($subsection_value["array_plan_id"]) > 0)
            $condition_treaty = $condition_treaty . " or ";
        foreach ($subsection_value["array_plan_id"] as $sub_key => $id) {
            if ($sub_key === 0) {
                $condition_treaty = $condition_treaty . "a.planned_indicators_id = " . $id['id'];
            } else $condition_treaty = $condition_treaty . " or a.planned_indicators_id = " . $id['id'];
        }
    }



    if ($condition_treaty === "") {
        echo json_encode([]);
        $mysql->close();
        return;
    }

    $condition_treaty = $condition_treaty !== "" ? " WHERE (" . $condition_treaty . ") and " : " WHERE ";

    $articles_array = [
        'Ремонт і ТО друкувальних пристроїв',
        'Ремонт комп\'ютерного обладнання',
        'Ремонт кондиціонерів, встановлення кондиціонерів',
        'Ремонт обладнання АСУТП і телемеханіки',
        'Ремонт обладнання РРС',
        'Ремонт радіостанцій, радіотерміналів, мобільних телефонів, АТС',
    ];
    $treaty_data = [];
    foreach ($articles_array as $index => $value) {
        $treaty_result = $mysql->query("SELECT  distinct(a.id), a.article_name, b.contract_number, b.contract_term, b.planned_indicators_id as planned_indicators_id, b.contracts_directory_id,
                                        b.counterparties_directory_id, b.counterparty_name
                                        FROM planned_indicators a 
                                        LEFT JOIN renovation_treaty_directory b ON b.article_name = a.article_name
                                        WHERE (a.article_name = '" . mysqli_real_escape_string($mysql, $value) . "') and
                                        a.year = '" . (substr($startMonth, 0, 4)) . "' and substring(b.contract_term, 1, 4) >= '" . (substr($startMonth, 0, 4)) . "'
                                        and b.article_year <= '" . (substr($startMonth, 0, 4)) . "'
                                        ORDER BY a.article_name");

        $isEmptyTreaty = [];
        while ($treaty_fetch = $treaty_result->fetch_assoc()) {
            array_push($treaty_data, $treaty_fetch);
            $isEmptyTreaty = $treaty_fetch;
        }
        //print_r($isEmptyTreaty);
        if (empty($isEmptyTreaty)) {
            $treaty_result = $mysql->query("SELECT  distinct(a.id) as planned_indicators_id, a.article_name,  
                                         IFNULL(b.contract_number, null) as contract_number, 
                                                     b.contracts_directory_id, b.counterparties_directory_id, b.counterparty_name
                                         FROM planned_indicators a 
                                         LEFT JOIN renovation_treaty_directory b ON b.planned_indicators_id = a.id
                                         WHERE (a.article_name = '" . mysqli_real_escape_string($mysql, $value) . "') and
                                         a.year = '" . (substr($startMonth, 0, 4)) . "' 
                                         ORDER BY a.article_name");

            while ($treaty_fetch = $treaty_result->fetch_assoc()) {
                array_push($treaty_data, $treaty_fetch);
            }
        }
    }

    foreach ($treaty_data as $treaty_row) {

        $act_card_result = $mysql->query("SELECT a.id, a.act_date, (SELECT c.contract_number FROM renovation_treaty_directory c WHERE c.id = a.renovation_treaty_directory_id) as contract_number, 
                                            a.act_number, sum(a.amount) as amount, sum((a.price_of_service_no_pdv + a.cost_of_materials_no_pdv) * a.amount) as sum_act, b.act_account FROM renovation_treaty_act_card a
                                            LEFT JOIN renovation_act_card b ON b.id = a.renovation_act_card_id
                                            WHERE  a.planned_indicators_id = '" . $treaty_row['planned_indicators_id'] . "' and 
                                            (SUBSTRING(a.act_date, 1, 7) BETWEEN '$startMonth' and '$endMonth' or (SUBSTRING(a.act_date, 1, 4) LIKE '" . (intval(substr($startMonth, 0, 4))) . "')) 
                                            GROUP BY a.act_number");
        // запрос на сумму по договору //b.conducted = 1 and

        //  + cost_of_materials_no_pdv
        $treaty_sum_result = $mysql->query("SELECT sum(amount * (price_of_service_no_pdv)) as sum 
                                                FROM renovation_treaty_directory
                                                WHERE planned_indicators_id = '" . $treaty_row['planned_indicators_id'] . "' and 
                                                    contract_number = '" . $treaty_row['contract_number'] . "'"); // LEFT JOIN contracts_directory b ON b.name = '" . $treaty_row['contract_number'] . "'

        // запрос на годовой план бюджета
        $plan_of_budget_result = $mysql->query("SELECT if(a.year != '"  . (substr($startMonth, 0, 4))  . "', 0, b.sum_no_vat) as sum_no_vat, 
                                                if(a.year != '"  . (substr($startMonth, 0, 4))  . "', 0, b.sum_with_vat) as sum_with_vat, c.id 
                                                FROM planned_indicators a 
                                                LEFT JOIN planned_indicators_amounts_implementation b ON b.planned_indicators_id = a.id
                                                LEFT JOIN new_codes_directory c ON c.id = a.new_codes_directory_id
                                                WHERE a.article_name = '" . mysqli_real_escape_string($mysql, $treaty_row['article_name']) . "' and a.year = '" . (substr($startMonth, 0, 4)) . "'");

        // запрос на проведенные оплаты по договору за период времени (один год)
        $plan_of_budget = $plan_of_budget_result->fetch_assoc();
        $sum_treaty = $treaty_sum_result->fetch_assoc();
        $act_card_array = array();
        $sum_amount = 0;
        $sum_acts = 0;
        $sum_facts = 0;
        $previous_act = null;
        $fact_pay_result = $mysql->query("SELECT sum(operSum) as sum FROM banks_register     
                                            WHERE   new_codes_directory_id = '" . $plan_of_budget['id'] . "' and SUBSTRING(operDate, 1, 7) BETWEEN '$startMonth' and '$endMonth'");
        $fact_pay_row = $fact_pay_result->fetch_assoc();
        $fact_additional_pay_result = $mysql->query("SELECT sum(sum) as sum FROM banks_register_additional_purpose
                                                        WHERE  new_codes_directory_id = '" . $plan_of_budget['id'] . "' and SUBSTRING(oper_date, 1, 7) BETWEEN '$startMonth' and '$endMonth'");
        $fact_additional_pay_row = $fact_additional_pay_result->fetch_assoc();
        $sum_facts = $fact_pay_row["sum"] + $fact_additional_pay_row["sum"];

        while ($act_card_row = $act_card_result->fetch_assoc()) {
            if ($treaty_row['contract_number'] === $act_card_row["contract_number"] && $act_card_row["act_number"] != $previous_act) {
                //echo $act_card_row["act_number"];

                $add_condition = "";
                if ($act_card_row["act_account"] != "")
                    $add_condition = " or paymentType LIKE '%" . $act_card_row["act_account"] . "%' ";
                $sum_amount += $act_card_row['amount'];
                $sum_acts += $act_card_row['sum_act']; // * ($act_card_row['price'] + $act_card_row['cost'])
                array_push($act_card_array, [
                    "id" => $act_card_row["id"],
                    "contract_number" => $act_card_row["contract_number"],
                    "act_number" => $act_card_row["act_number"],
                ]);
            }
            $previous_act = $act_card_row["act_number"];
        }

        if (!($sum_acts == 0))
            array_push($main_data_array, [
                "contract_id" => $treaty_row["contracts_directory_id"],
                "contract_number" => $treaty_row["contract_number"],
                "article_id" => $treaty_row['planned_indicators_id'],
                "article_name" => $treaty_row["article_name"],
                "act_card_data" => $act_card_array,
                "sum_treaty" => floatval($sum_treaty["sum"]),
                "sum_amount" => $sum_amount,
                "sum_acts" => $sum_acts,
                "sum_facts_with_vat" => $sum_facts,
                "sum_facts_no_vat" => $sum_facts / 1.2,
                "plan_no_val" => $plan_of_budget["sum_no_vat"],
                "plan_with_val" => $plan_of_budget["sum_with_vat"]
            ]);
    }
    echo json_encode($main_data_array);
    $mysql->close();
}

function ParseStringOnPDV($string)
{
    $pos = strripos($string, 'БЕЗ ПДВ');
    if ($pos === false) {
        $pos = strripos($string, 'т.ч.П');
        if ($pos === false) {
            $pos = strripos($string, 'т.ч.ПДВ');
            if ($pos === false) {
                $pos = strripos($string, 'т.ч.Н');
                if ($pos === false) {
                    return 0;
                } else return 1;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    } else {
        return 0;
    }
}


$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "subsectionsDataRequest":
        GetSubsectionData($data["startMonth"], $data["endMonth"], $data["subsectionData"], $data["monthRange"]);
        break;
    case "mainTableDataRequest":
        GetMainTableData($data["startMonth"], $data["endMonth"], $data["subsectionData"]);
        break;
    default:
        break;
}