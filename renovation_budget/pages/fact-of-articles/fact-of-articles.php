<?php
require_once("../../../templates/classes/db_local.php");

function GetArticleData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $article_array = array();
    // вывести со службами id которых в объекте $data
    $article_data_result = $mysql->query("SELECT distinct(a.article_name), a.budget_articles_directory_id, a.year, a.id FROM planned_indicators a
                                          LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                                          LEFT JOIN services_directory c ON c.id = b.services_directory_id
                                          WHERE a.year BETWEEN '" . substr($data['startDate'], 0, 4) . "' and  '" . substr($data['endDate'], 0, 4) . "'
                                                    and a.counterparties_directory_id != 0 and c.id = '" . $data['id'] . "'
                                                    and (b.subsections_directory_id = 51 or b.subsections_directory_id = 53)");

    while ($row = $article_data_result->fetch_assoc()) {
        $treaty_result = $mysql->query("SELECT * FROM renovation_treaty_directory WHERE article_name = '" . mysqli_real_escape_string($mysql, $row['article_name']) . "' and 
        substring(contract_term, 1, 4) >= '" . substr($data['startDate'], 0, 4) . "'");
        // echo "SELECT * FROM renovation_treaty_directory WHERE article_name = '" . $row['article_name'] . "' or 
        // substring(contract_term, 1, 4) >= '" . substr($data['startDate'], 0, 4) . "'";
        $treaty_fetch = $treaty_result->fetch_assoc();
        //if($treaty_fetch !== null)
        array_push($article_array, [
            "id" => $row["id"],
            "articleId" => $row["id"],
            "articleName" => $row["article_name"],
            "year" => $row['year'],
        ]);
    }

    echo json_encode($article_array);
    $mysql->close();
}

function GetNumberData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $article_data_result = $mysql->query("SELECT a.article_name, a.year, a.counterparties_directory_id, b.name as counterparty
                                          FROM planned_indicators a 
                                          LEFT JOIN counterparties_directory b ON b.id = a.counterparties_directory_id 
                                          WHERE a.id = " . $data["id"]);
    if ($data["id"] === 0) {
        $mysql->close();
        echo json_encode([]);
        return;
    }
    $article_data = $article_data_result->fetch_assoc();
    $counterparty_id = $article_data["counterparties_directory_id"];
    $info_number_data_result = $mysql->query("SELECT a.name as name, a.id as counterparty_id, b.number as number, b.id as id, 
                                                     b.vat_sign, b.edit_mode as edit_mode 
                                              FROM counterparties_directory a
                                              JOIN contracts_directory b ON b.counterparties_directory_id = a.id 
                                              WHERE a.id = '" . $article_data['counterparties_directory_id'] . "'");
    // $exceptions_articles_result = $mysql->query("SELECT * FROM renovation_articles_exceptions WHERE planned_indicators_id = '" . $data['id'] . "'");
    // $exceptions_article = $exceptions_articles_result->fetch_assoc();
    //if($exceptions_article !== null)
    //$exception_condition = "`price_of_service_no_pdv`";
    // else
    $exception_condition = "`price_of_service_no_pdv` + `cost_of_materials_no_pdv`";
    $treaty_data_array = array();
    $treaty_id_array = array();
    while ($row = $info_number_data_result->fetch_assoc()) {
        if ($row["edit_mode"] != "1") {
            $treaty_data_result = $mysql->query("SELECT substring(date_created, 1, 4) as date, sum(round((" . $exception_condition . ") * amount, 2)) as sum 
                                                 FROM renovation_treaty_directory
                                                 WHERE contracts_directory_id = '" . $row['id'] . "' and 
                                                 counterparties_directory_id = '" . $row['counterparty_id'] . "' and 
                                                 article_name = '" . mysqli_real_escape_string($mysql, $article_data['article_name']) . "' and 
                                                 substring(contract_term, 1, 4) >= '" . $article_data['year'] . "'");
            //  and
            //  article_name = '" . $article_data['article_name'] . "' and
            //  substring(contract_term, 1, 4) = '" . $article_data['year'] . "'
            //    article_year = '" . $article_data['year'] . "' and 
            //    planned_indicators_id = '" . $data["id"] . "'

            while ($row_treaty = $treaty_data_result->fetch_assoc()) {
                if ($row_treaty["date"] !== null && $row_treaty["sum"] !== null)
                    array_push($treaty_data_array, [
                        "contractNumberId" => $row["id"],
                        "contractNumber" => $row['number'],
                        "date" => $row_treaty["date"],
                        "counterparty" => $row['name'],
                        "counterpartyId" => $row['counterparty_id'],
                        "signOfVAT" => $row["vat_sign"],
                        "sumNoVAT" => number_format($row_treaty["sum"] / 1000, 5, ".", " "),
                        "sumWithVAT" => number_format(($row["vat_sign"] == "Без ПДВ" ? $row_treaty["sum"] :  $row_treaty["sum"] * 1.2) / 1000, 5, ".", " "),
                    ]);
            }
            $treaty_id_result = $mysql->query("SELECT id FROM renovation_treaty_directory
                                               WHERE contracts_directory_id = '" . $row['id'] . "' and counterparties_directory_id = '" . $row['counterparty_id'] . "' and 
                                                     article_name = '" . mysqli_real_escape_string($mysql, $article_data['article_name']) . "' and
                                                     substring(contract_term, 1, 4) >= '" . $article_data['year'] . "'");

            // planned_indicators_id = '" . $data["id"] . "'
            while ($row_treaty_id = $treaty_id_result->fetch_assoc()) {
                array_push($treaty_id_array, [
                    "id" => $row_treaty_id["id"]
                ]);
            }
        }
    }

    echo json_encode(["treaty_data" => $treaty_data_array, "treaty_id" => $treaty_id_array]);
    $mysql->close();
}

function GetInfoPlan($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    if ($data['id'] === 0) {
        echo json_encode([
            "planVAT" => 0,
            "planNoVAT" => 0
        ]);
        $mysql->close();
        return;
    }
    $start_month = '01'; //substr($data["startDate"], 5, 2);
    $end_month = '12'; // substr($data["endDate"], 5, 2);
    $year = substr($data["startDate"], 0, 4);
    $month_array_with_vat = array();
    $month_array_no_vat = array();
    if (intval($start_month) < intval($end_month)) {
        for ($i = intval($start_month); $i <= intval($end_month); $i++) {
            array_push($month_array_with_vat, $i < 10 ? "0" . $i . "_with_vat" : $i . "_with_vat");
            array_push($month_array_no_vat, $i < 10 ? "0" . $i . "_no_vat" : $i . "_no_vat");
        }
    } elseif (intval($end_month) > intval($start_month)) {
        for ($i = intval($end_month); $i <= intval($start_month); $i++) {
            array_push($month_array_with_vat, $i < 10 ? "0" . $i . "_with_vat" : $i . "_with_vat");
            array_push($month_array_no_vat, $i < 10 ? "0" . $i . "_no_vat" : $i . "_no_vat");
        }
    } else {
        array_push($month_array_with_vat, $start_month . "_with_vat");
        array_push($month_array_no_vat, $end_month . "_no_vat");
    }
    //print_r($month_array_with_vat);
    $sum_with_vat = 0;
    $sum_no_vat = 0;
    $id_array = array();
    foreach ($month_array_with_vat as $key => $month_element) {
        $plan_data_result = $mysql->query("SELECT $month_element as planVAT, " . $month_array_no_vat[$key] . " as planNoVAT, id 
                                            FROM planned_indicators_amounts_implementation WHERE planned_indicators_id = '" . $data['id'] . "'");
        $plan_data_row = $plan_data_result->fetch_assoc();
        $sum_with_vat += $plan_data_row["planVAT"];
        $sum_no_vat += $plan_data_row["planNoVAT"];
        //array_push($id_array, $plan_data_row["id"]);
    }



    $plan_array = [
        //"id" => $id_array,
        "planVAT" => $sum_with_vat,
        "planNoVAT" => $sum_no_vat
    ];
    echo json_encode($plan_array);
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



function GetFactSum($id, $date)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $info_fact_array = array();
    $new_code_result = $mysql->query("SELECT b.id, b.new_code, b.id as new_codes_directory_id FROM planned_indicators a
                                      LEFT JOIN new_codes_directory b ON b.id = a.new_codes_directory_id
                                      WHERE a.id = '" . $id . "'");
    $new_code = $new_code_result->fetch_assoc();

    $result_fact_sum = $mysql->query("SELECT sum(operSum) as sum, paymentType FROM `banks_register`
                                      WHERE new_codes_directory_id = '" . $new_code['new_codes_directory_id'] . "' and substring(operDate, 1, 4) = '" . substr($date, 0, 4) . "'");
    $fact_sum_row = $result_fact_sum->fetch_assoc();

    $result_fact_additional_pay = $mysql->query("SELECT sum(sum) as sum FROM banks_register_additional_purpose
                                                             WHERE new_codes_directory_id = '" . $new_code['new_codes_directory_id'] . "' and substring(oper_date, 1, 4) = '" . substr($date, 0, 4) . "'");
    $row_fact_additional_pay = $result_fact_additional_pay->fetch_assoc();

    $sum = $fact_sum_row["sum"] + ($row_fact_additional_pay ? $row_fact_additional_pay["sum"] : 0);
    // $sum = 0;
    // while($fact_sum_row = $result_fact_sum->fetch_assoc()) {
    //     // $vat = $fact_sum_row["paymentType"]; // ParseStringOnPDV
    //     // if($vat === 0)
    //     //     $sum += $fact_sum_row["sum"] / 1.2;
    //     // elseif($vat === 1)
    //     $sum += $fact_sum_row["sum"];
    // }
    echo json_encode(number_format($sum / 1000, 5, '.', ' '));
    $mysql->close();
}


function GetInfoFactFromActCard($data, $startDate, $endDate, $plan_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $info_act_card_array = array();
    $result_plan = $mysql->query("SELECT b.new_code, b.id as new_codes_directory_id FROM planned_indicators a
                                  LEFT JOIN new_codes_directory b ON  b.id = a.new_codes_directory_id
                                  WHERE a.id = '" . $plan_id . "'");
    $plan_codes = $result_plan->fetch_assoc();
    for ($i = 0; $i < count($data); $i++) {
        $act_card_array = array();
        $info_act_card = $mysql->query("SELECT a.renovation_act_card_id, a.renovation_treaty_directory_id, a.act_date, c.act_account, c.act_number_account, a.act_number, 
                                        sum((a.price_of_service_no_pdv + a.cost_of_materials_no_pdv) * a.amount) as sum, b.contract_number 
                                        FROM renovation_treaty_act_card a 
                                        LEFT JOIN renovation_treaty_directory b ON b.id = a.renovation_treaty_directory_id 
                                        LEFT JOIN renovation_act_card c ON c.id = a.renovation_act_card_id 
                                        WHERE  a.renovation_treaty_directory_id = '" . $data[$i]['id'] . "' and substring(a.act_date, 1, 7) BETWEEN '" . $startDate . "' and '" . $endDate . "' 
                                        GROUP BY a.renovation_act_card_id"); //  and substring(a.act_date, 1, 7) BETWEEN '" . $startDate . "' and '" . $endDate . "' // c.conducted = 1 and

        while ($row_act_card = $info_act_card->fetch_assoc()) {
            array_push($act_card_array, [
                "act_card_id" => $row_act_card["renovation_act_card_id"],
                "treaty_id" => $row_act_card["renovation_treaty_directory_id"],
                "act_date" => $row_act_card["act_date"],
                "act_number" => $row_act_card["act_number"],
                "act_account" => $row_act_card["act_account"],
                "act_number_account" => $row_act_card["act_number_account"],
                "act_sum" => $row_act_card["sum"],
                "contract_number" => $row_act_card["contract_number"],
            ]);
        }
        if (count($act_card_array) === 0)
            continue;

        foreach ($act_card_array as $key => $element) {
            $add_condition = "";
            $sub_add_condition = "";
            if ($element["act_account"] != "")
                $add_condition = " or (operSum LIKE '%" . $element['act_account'] . "%' 
                                   and substring(operDate, 1, 4) = " . substr($startDate, 0, 4) . ")";
            if ($element["act_number_account"] != "")
                $sub_add_condition = " or ((paymentType LIKE '%№" . $element['act_number_account'] . "%' 
                or paymentType LIKE '%№ " . $element['act_number_account'] . "%')
                and substring(operDate, 1, 7) BETWEEN '" . $startDate . "' and '" . $endDate . "')";

            $result_fact = $mysql->query("SELECT sum(operSum) as sum, max(substring(operDate, 1, 10)) as date  FROM banks_register 
                                            WHERE new_codes_directory_id = '" . $plan_codes['new_codes_directory_id'] . "' and 
                                            ( paymentType LIKE '%№" . $element['act_number'] . "%' or paymentType LIKE '%№ " . $element['act_number'] . "%') and
                                            substring(operDate, 1, 7) BETWEEN '" . $startDate . "' and '" . $endDate . "' " .  $add_condition . $sub_add_condition . " "); //GROUP BY date

            $row_fact = $result_fact->fetch_assoc();

            $result_fact_additional_pay = $mysql->query("SELECT sum(sum) as sum, substring(oper_date, 1, 10) as date FROM banks_register_additional_purpose
                                                             WHERE (purpose LIKE '%№" . $element["act_number"] . "%' or purpose LIKE '%№ " . $element["act_number"] . "%')  
                                                                    and new_codes_directory_id = '" . $plan_codes['new_codes_directory_id'] . "' and SUBSTRING(oper_date, 1, 7) BETWEEN '" . $startDate . "' and '" . $endDate . "'");
            $row_fact_additional_pay = $result_fact_additional_pay->fetch_assoc();

            $sum_fact = (isset($row_fact["sum"]) ? $row_fact["sum"] : 0) + (isset($row_fact_additional_pay["sum"]) ? $row_fact_additional_pay["sum"] : 0);
            $fact_date = isset($row_fact["date"]) ? $row_fact["date"] : $row_fact_additional_pay["date"];

            array_push($info_act_card_array, [
                "actCardId" => intval($element["act_card_id"]),
                "act_date" => $element["act_date"],
                "act_number" => $element["act_number"],
                "sum_act_no_VAT" => number_format($element["act_sum"] / 1000, 5, ".", " "),
                "sum_act_VAT" => number_format($element["act_sum"] * 1.2 / 1000, 5, ".", " "),
                "contract_number" => $element["contract_number"],
                "sum_fact_with_VAT" => number_format($sum_fact / 1000, 5, ".", " "),
                "fact_date" => $fact_date
            ]);
        }
    }
    usort($info_act_card_array, function ($previous, $current) {
        if ($previous["actCardId"] === $current["actCardId"])
            return 0;
        return ($previous["actCardId"] < $current["actCardId"]) ? -1 : 1;
    });

    echo json_encode($info_act_card_array);
    $mysql->close();
}

function GetInfoFactByTreaty($contract_data, $article_id, $startDate, $endDate, $article_name, $article_year)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result_plan = $mysql->query("SELECT c.new_code, a.new_codes_directory_id, b.sum_with_vat  FROM planned_indicators a 
                                  LEFT JOIN fact_indicators_amounts b ON b.planned_indicators_id = a.id
                                  LEFT JOIN new_codes_directory c ON c.id = a.new_codes_directory_id
                                  WHERE a.id = '" . $article_id . "'");
    $plan_codes = $result_plan->fetch_assoc();
    $info_fact_array = array();
    foreach ($contract_data as $key => $contract) {
        $sum_month = 0;
        $sum_year = 0;
        $act_card_result = $mysql->query("SELECT DISTINCT(a.act_number)
                                          FROM renovation_treaty_act_card a 
                                          LEFT JOIN renovation_treaty_directory b ON b.id = a.renovation_treaty_directory_id 
                                          LEFT JOIN renovation_act_card rac ON rac.id = a.renovation_act_card_id
                                          WHERE  b.contract_number = '" . $contract . "' and
                                          b.article_name = '" . mysqli_real_escape_string($mysql, $article_name)  . "' and
                                          b.contract_term >= '" . $article_year . "'"); // rac.conducted = 1 and
        while ($act_card_row = $act_card_result->fetch_assoc()) {

            $act_card_month_result = $mysql->query("SELECT sum((a.price_of_service_no_pdv + a.cost_of_materials_no_pdv) * a.amount) as sum
                                            FROM renovation_treaty_act_card a 
                                            LEFT JOIN renovation_treaty_directory b ON b.id = a.renovation_treaty_directory_id 
                                            WHERE a.act_number = '" . $act_card_row['act_number'] . "' and 
                                            substring(a.act_date, 1, 7) BETWEEN '" . $startDate . "' and '" . $endDate . "'");
            $act_month = $act_card_month_result->fetch_assoc();

            $act_card_year_result = $mysql->query("SELECT sum((a.price_of_service_no_pdv + a.cost_of_materials_no_pdv) * a.amount) as sum
                                            FROM renovation_treaty_act_card a 
                                            LEFT JOIN renovation_treaty_directory b ON b.id = a.renovation_treaty_directory_id 
                                            
                                            WHERE a.act_number = '" . $act_card_row['act_number'] . "' and 
                                            substring(a.act_date, 1, 4) Like '" . substr($startDate, 0, 4) . "'");

            $act_year =  $act_card_year_result->fetch_assoc();

            $sum_month += $act_month["sum"] * 1.2;
            $sum_year += $act_year["sum"] * 1.2;
        }
        array_push($info_fact_array, [
            "number_contract" => $contract,
            "sum_month" => number_format($sum_month / 1000, 5, ".", " "),
            "sum_year" => number_format($sum_year / 1000, 5, ".", " "),
            "all_sum_year" => $plan_codes["sum_with_vat"] / 1000
        ]);
    }


    echo json_encode($info_fact_array);
    $mysql->close();
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "selectArticleRequest":
        GetArticleData($data);
        break;
    case "infoNumberContractRequest":
        GetNumberData($data);
        break;
    case "infoPlanRequest":
        GetInfoPlan($data);
        break;
    case "infoFactFromActCardRequest":
        GetInfoFactFromActCard($data["infoTreaty"], $data["startDate"], $data["endDate"], $data["planId"]);
        break;
    case "infoFactSum":
        GetFactSum($data["id"], $data["date"]);
        break;
    case "infoFactByTreatyRequest":
        GetInfoFactByTreaty($data["numbersContract"], $data["planId"], $data["startDate"], $data["endDate"], $data["articleName"], $data["articleYear"]);
        break;
    default:
        break;
}