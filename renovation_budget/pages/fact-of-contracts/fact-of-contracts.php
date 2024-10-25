<?php
require_once("../../../templates/classes/db_local.php");

function GetTreatyData($counterparty_id, $contract_id, $startMonth, $endMonth, $articleName)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $treaty_data_array = array();
    $treaty_act_card_data_array = array();
    $act_data_array = array();
    $condition = " ";
    $treaty_data_result = $mysql->query("SELECT * FROM renovation_treaty_directory 
                                            WHERE contracts_directory_id = '$contract_id' and counterparties_directory_id = '$counterparty_id' "); // and article_name = '" . mysqli_real_escape_string($mysql, $articleName) . "'
    // echo "SELECT * FROM renovation_treaty_directory 
    //  WHERE contracts_directory_id = '$contract_id' and counterparties_directory_id = '$counterparty_id' and article_name = '$articleName'";
    while ($row = $treaty_data_result->fetch_assoc()) {
        array_push($treaty_data_array, [
            "id" => $row['id'],
            "counterparty_id" => $row["counterparties_directory_id"],
            "id_contract" => $row['contracts_directory_id'],
            //"article_id" => floatval($row["planned_indicators_id"]),
            "article_name" => $row["article_name"],
            "name_service" => $row['name_of_services'],
            "type_equipment" => $row['type_of_equipment'],
            "amount" => $row['amount'],
            "price_of_service_no_pdv" => $row['price_of_service_no_pdv'],
            "cost_of_materials_no_pdv" => $row['cost_of_materials_no_pdv']
        ]);

        $treaty_act_card_result = $mysql->query("SELECT rtac.* FROM `renovation_treaty_act_card` rtac
                                                 LEFT JOIN renovation_act_card rac ON rac.id = rtac.renovation_act_card_id 
                                                 WHERE rtac.renovation_treaty_directory_id = '" . $row['id'] . "'  and 
                                                        substring(rtac.act_date, 1, 7) BETWEEN '" . $startMonth . "' AND '" . $endMonth . "' ORDER BY rtac.act_number ASC");

        while ($treaty_act_card_row = $treaty_act_card_result->fetch_assoc()) {
            array_push($treaty_act_card_data_array, [
                "id" => $treaty_act_card_row["id"],
                "act_card_id" => $treaty_act_card_row["renovation_act_card_id"],
                "act_number" => $treaty_act_card_row["act_number"],
                "act_date" => $treaty_act_card_row["act_date"],
                "treaty_id" => $treaty_act_card_row["renovation_treaty_directory_id"],
                "article_name" => $treaty_act_card_row["article_name"],
                "amount" => $treaty_act_card_row["amount"],
                "price_of_service_no_pdv" => $treaty_act_card_row["price_of_service_no_pdv"],
                "cost_of_materials_no_pdv" => $treaty_act_card_row["cost_of_materials_no_pdv"],
            ]);
        }
    }
    if ($startMonth === 1 || $endMonth === 1)
        $bool_check = false;
    else $bool_check = true;
    if ($bool_check === true) {
        for ($i = 0; $i < count($treaty_data_array); $i++) {
            if ($i + 1 == count($treaty_data_array)) {
                $condition = $condition . "b.renovation_treaty_directory_id = " . $treaty_data_array[$i]['id'];
                $condition = " WHERE (" . $condition;
            } else $condition = $condition . "b.renovation_treaty_directory_id = " . $treaty_data_array[$i]['id'] . " or ";
        }
    }
    if ($condition == " ")
        $condition = " WHERE a.id = 0";
    else $condition = $condition . ") and (SUBSTRING(a.act_date, 1, 7) BETWEEN '$startMonth' AND '$endMonth')";
    $act_card_result = $mysql->query("SELECT Distinct(a.id), a.act_date, a.act_number FROM renovation_act_card a 
                                        JOIN renovation_treaty_act_card b ON a.id = b.renovation_act_card_id" . $condition . "  ORDER BY a.act_date ASC"); // and a.conducted = 1


    while ($act_card_row = $act_card_result->fetch_assoc()) {
        array_push($act_data_array, [
            "id" => $act_card_row["id"],
            "act_number" => $act_card_row["act_number"],
            "act_date" => $act_card_row["act_date"]
        ]);
    }

    $contract_result = $mysql->query("SELECT * FROM contracts_directory WHERE id = '$contract_id'");
    $contract_sign = null;
    while ($contract_row = $contract_result->fetch_assoc()) {
        $contract_sign = $contract_row["vat_sign"];
    }

    return [
        "treaty_data_array" => $treaty_data_array,
        "treaty_act_card_data_array" => $treaty_act_card_data_array,
        "act_card_data_array" => $act_data_array,
        "contract_sign" => $contract_sign
    ];
}

function GetSelectBarData($startMonth, $endMonth)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $articles_array = array();
    $counterparties_array = array();
    $contracts_array = array();
    $renovation_treaty_array = array();
    $renovation_act_card_array = array();
    $renovation_ractd_array = array();
    $year = date("Y");
    // условие: для службы телекоммунікації и технічноі підтримки 
    $articles_data_result = $mysql->query("SELECT distinct(a.article_name), a.counterparties_directory_id, b.name as counterparty FROM planned_indicators a
                                            LEFT JOIN counterparties_directory b ON b.id = a.counterparties_directory_id
                                            LEFT JOIN budget_articles_directory c ON c.id = a.budget_articles_directory_id
                                            LEFT JOIN services_directory d ON d.id = c.services_directory_id
                                            LEFT JOIN contracts_directory e ON e.counterparties_directory_id = b.id
                                            WHERE a.counterparties_directory_id != 0  and
                                            (c.services_directory_id = 10 or c.services_directory_id = 11) and 
                                            (c.subsections_directory_id = 51 or c.subsections_directory_id = 53) and 
                                            a.year BETWEEN '2023' and '2025'"); // e.status = 'Відкритий' and 
    while ($row = $articles_data_result->fetch_assoc()) {
        // $treaty_directory_result = $mysql->query("SELECT * FROM renovation_treaty_directory  
        //                                             WHERE article_name = '" . mysqli_real_escape_string($mysql, $row["article_name"]) . "' and 
        //                                             (substring(contract_term, 1, 4) >= '" . substr($startMonth, 0, 4) . "' or 
        //                                              substring(contract_term, 1, 4) >= '" . substr($endMonth, 0, 4) . "')");
        //if($treaty_directory_result->fetch_assoc() !== null) {
        array_push($articles_array, [
            "name" => $row["article_name"],
            "counterparty_id" => $row["counterparties_directory_id"],
            "counterparty" => $row["counterparty"],
        ]);
        //}
    }

    $counterparty_data_result = $mysql->query("SELECT distinct(a.id) as counterparty_id, a.name as counterparty_name, 
                                                (SELECT count(e.id) FROM contracts_directory e WHERE e.counterparties_directory_id = a.id and e.status = 'Відкритий') as count_contracts
                                                    FROM `counterparties_directory` a 
                                                    LEFT JOIN contracts_directory b ON a.id = b.counterparties_directory_id
                                                    LEFT JOIN planned_indicators c ON c.counterparties_directory_id = a.id
                                                    LEFT JOIN budget_articles_directory d ON d.id = c.budget_articles_directory_id
                                                        WHERE (d.services_directory_id = 10 or d.services_directory_id = 11) and b.status = 'Відкритий' and b.edit_mode = 0
                                                        and (d.subsections_directory_id = 51 or d.subsections_directory_id = 53)");
    while ($row = $counterparty_data_result->fetch_assoc()) {
        array_push($counterparties_array, [
            "id" => $row["counterparty_id"],
            "name" => $row["counterparty_name"]
        ]);
    }
    $contract_data_result = $mysql->query("SELECT * FROM contracts_directory 
                                            WHERE edit_mode = '0' and status = 'Відкритий' and term >= '" . substr($startMonth, 0, 4) . "'");
    while ($row = $contract_data_result->fetch_assoc()) {
        $treaty_articles_array = array();
        $treaty_articles_result = $mysql->query("SELECT DISTINCT(a.`contract_number`) , a.`contracts_directory_id`, a.`article_name`
                                                 FROM `renovation_treaty_directory` a 
                                                 JOIN contracts_directory b ON b.id = a.contracts_directory_id 
                                                 WHERE b.edit_mode = '0' and a.contracts_directory_id = '" . $row["id"] . "'");
        while ($treaty_articles_row = $treaty_articles_result->fetch_assoc()) {
            array_push($treaty_articles_array, [
                "article_name" => $treaty_articles_row["article_name"]
            ]);
        }
        array_push($contracts_array, [
            "id" => $row["id"],
            "counterparty_id" => $row["counterparties_directory_id"],
            "contract_number" => $row["number"],
            "sign_of_vat" => $row["vat_sign"],
            "treaty_articles_array" => $treaty_articles_array
        ]);
    }
    $renovation_treaty_data_result = $mysql->query("SELECT * FROM renovation_treaty_directory");
    while ($row = $renovation_treaty_data_result->fetch_assoc()) {
        array_push($renovation_treaty_array, [
            "id" => $row["id"],
            "contract_id" => $row["contracts_directory_id"],
            "counterparty_id" => $row["counterparties_directory_id"],
            "article_id" => $row["planned_indicators_id"],
            "article_name" => $row["article_name"],
            "articles_exceptions" => GetArticlesExceptions(),
            "amount" => $row["amount"],
            "price_of_service_no_pdv" => $row["price_of_service_no_pdv"],
            "cost_of_materials_no_pdv" => $row["cost_of_materials_no_pdv"]
        ]);
    }
    //conducted = 1 and
    $renovation_act_card_data_result = $mysql->query("SELECT * FROM renovation_act_card WHERE  (SUBSTRING(act_date, 1, 7) BETWEEN '$startMonth' AND '$endMonth') ORDER BY act_date");
    while ($row = $renovation_act_card_data_result->fetch_assoc()) {
        array_push($renovation_act_card_array, [
            "id" => $row["id"],
            "act_number" => $row["act_number"],
            "date_number" => $row["act_date"]
        ]);
    }
    //rac.conducted = 1 and
    $renovation_ractd_data_result = $mysql->query("SELECT * FROM renovation_treaty_act_card rtac
     LEFT JOIN renovation_act_card rac ON rac.id = rtac.renovation_act_card_id  WHERE  (SUBSTRING(rtac.act_date, 1, 7) BETWEEN '$startMonth' AND '$endMonth') ORDER BY rtac.act_date");
    while ($row = $renovation_ractd_data_result->fetch_assoc()) {
        array_push($renovation_ractd_array, [
            "id" => $row["id"],
            "treaty_id" => $row["renovation_treaty_directory_id"],
            "act_card_id" => $row["renovation_act_card_id"],
            "article_id" => $row["planned_indicators_id"],
            "articles_exceptions" => GetArticlesExceptions(),
            "amount" => $row["amount"],
            "price_of_service_no_pdv" => $row["price_of_service_no_pdv"],
            "cost_of_materials_no_pdv" => $row["cost_of_materials_no_pdv"]
        ]);
    }
    echo json_encode([
        "articles" => $articles_array,
        "counterparties" => $counterparties_array,
        "contracts" => $contracts_array,
        "renovationTreaty" => $renovation_treaty_array,
        "renovationActCard" => $renovation_act_card_array,
        "renovationRactd" => $renovation_ractd_array
    ]);
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
            //"article_id" => floatval($row["planned_indicators_id"]),
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
        if ($article_name == $row["article_name"])
            return true;
    }
    return false;
}

function CreateTableDOM($counterparty_id, $contract_id, $startMonth, $endMonth, $articleName)
{

    $data_array = GetTreatyData($counterparty_id, $contract_id, $startMonth, $endMonth, $articleName);
    $articles_exceptions_array = GetArticlesExceptions();
    $treaty_array = $data_array["treaty_data_array"];
    $treaty_act_card_data_array = $data_array["treaty_act_card_data_array"];
    $act_card_array = $data_array["act_card_data_array"];
    $contract_sign = $data_array["contract_sign"];
    if (count($act_card_array) > 0)
        $rowspan = 3;
    else $rowspan = 2;

    echo "<table id='table-id'>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th rowspan='$rowspan' class='table-column-id th-width-auto th-low-border sticky-table-column'>№</th>";
    echo "<th rowspan='$rowspan' class='table-column-article_name th-low-border sticky-table-column'>Стаття бюджету</th>"; // new 
    echo "<th rowspan='$rowspan' class='table-column-name th-low-border sticky-table-column'>Найменування послуг</th>";
    echo "<th rowspan='$rowspan' class='table-column-type th-low-border sticky-table-column'>Тип обладнання</th>";
    echo "<th rowspan='$rowspan' class='table-column-amount-remainder th-width-auto th-low-border sticky-table-column'>Кількість</th>";
    echo "<th colspan='3' class='table-column-cost-service th-nowrap sticky-table-column'>Вартість послуг, грн. без ПДВ</th>";
    echo "<th class='table-column-remainder th-width-auto  sticky-table-column'>Залишок по договору</th>"; //th-nowrap
    if (count($act_card_array) > 0)
        echo "<th colspan='" . (4 * count($act_card_array)) . "' class='th-act-card th-first-head-fact'>Факт списання витрат, грн. без ПДВ</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th class='table-th-sticky-second-row table-column-price th-low-border sticky-table-column' rowspan='" . ($rowspan - 1) . "'>Ціна послуги</th>";
    echo "<th class='table-th-sticky-second-row table-column-cost th-low-border sticky-table-column' rowspan='" . ($rowspan - 1) . "'>Вартість матеріалів</th>";
    echo "<th class='table-th-sticky-second-row table-column-sum th-low-border sticky-table-column' rowspan='" . ($rowspan - 1) . "'>Сума</th>";
    echo "<th class='table-th-sticky-second-row table-column-amount th-width-auto th-low-border sticky-table-column' rowspan='" . ($rowspan - 1) . "'>Кількість</th>";
    for ($i = 0; $i < count($act_card_array); $i++) {
        echo "<th colspan='4' class='table-th-sticky-second-row th-act-card th-second-head-fact'>акт " . $act_card_array[$i]["act_number"] . " від " . $act_card_array[$i]["act_date"] . "</th>";
    }
    echo "</tr>";
    echo "<tr>";
    for ($i = 0; $i < count($act_card_array); $i++) {
        echo "<th class='table-th-sticky-third-row th-act-card  th-width-auto th-low-border th-third-head-fact'>Кількість</th>";
        echo "<th class='table-th-sticky-third-row th-act-card  th-width-auto th-low-border  th-third-head-fact'>Ціна послуги</th>";
        echo "<th class='table-th-sticky-third-row th-act-card  th-width-auto th-low-border  th-third-head-fact'>Вартість матеріалів</th>";
        echo "<th class='table-th-sticky-third-row th-act-card  th-width-auto th-low-border th-third-head-fact'>Сума</th>";
    }
    echo "</tr>";
    echo "</thead>";

    $sum_array = array();
    echo "<tbody>";

    function CreateActRow($treaty_id, $act_card_id, $amount, $treaty_act_card_data_array, $articles_exceptions_array)
    {
        $check = false;
        $sum = 0;
        foreach ($treaty_act_card_data_array as $value) {
            $count_hr = 0;
            $counter = 0;
            $price = 0;
            $cost = 0;
            foreach ($treaty_act_card_data_array as $key => $item) {
                if ($item["treaty_id"] === $treaty_id && $item['act_card_id'] === $act_card_id) {
                    if ($counter < $amount && $key > 0  && $amount > 1) {
                        $count_hr++;
                        $counter++;
                    }
                }
            }
            $counter = 0;
            echo "<td class='th-nowrap'>";
            $sum_act_row_amount_array = array();
            $sum_act_row_price_array = array();
            $sum_act_row_cost_array = array();
            foreach ($treaty_act_card_data_array as $key => $item) {
                if ($item["treaty_id"] === $treaty_id && $item['act_card_id'] === $act_card_id) {
                    if ($counter < $amount && $key > 0  && $amount > 1 && $count_hr > 0) {
                        if ($counter > 0)
                            echo "<hr 'solid'><p>";
                    }
                    $counter++;
                    echo $item["amount"] . "<p>";
                    array_push($sum_act_row_amount_array, $item["amount"]);
                    $check = true;
                }
            }
            echo "</td>";
            $counter = 0;
            echo "<td class='th-nowrap'>";
            foreach ($treaty_act_card_data_array as $key => $item) {
                if ($item["treaty_id"] === $treaty_id && $item['act_card_id'] === $act_card_id) {
                    if ($counter < $amount && $key > 0  && $amount > 1 && $count_hr > 0) {
                        if ($counter > 0)
                            echo "<hr 'solid'><p>";
                    }
                    $counter++;
                    $price = $item["price_of_service_no_pdv"];
                    echo number_format($item["price_of_service_no_pdv"], 2, ".", " ") . "<p>";
                    array_push($sum_act_row_price_array, $price);
                    $check = true;
                }
            }
            echo "</td>";
            $counter = 0;
            echo "<td class='th-nowrap'>";
            foreach ($treaty_act_card_data_array as $key => $item) {
                if ($item["treaty_id"] === $treaty_id && $item['act_card_id'] === $act_card_id) {
                    if ($counter < $amount && $key > 0  && $amount > 1 && $count_hr > 0) {
                        if ($counter > 0)
                            echo "<hr 'solid'><p>";
                    }
                    $counter++;
                    //$cost = SearchArticleException($item["article_id"], $articles_exceptions_array) ? 0: $item["cost_of_materials_no_pdv"];
                    $cost = $item["cost_of_materials_no_pdv"];
                    echo number_format($item["cost_of_materials_no_pdv"], 2, ".", " ") . "<p>";
                    array_push($sum_act_row_cost_array, $cost);
                    $check = true;
                }
            }
            echo "</td>";
            $sum = 0;
            foreach ($sum_act_row_amount_array as $key => $item) {
                $sum += $sum_act_row_amount_array[$key] * ($sum_act_row_price_array[$key] + $sum_act_row_cost_array[$key]);
            }
            echo "<td class='th-nowrap'>" . number_format($sum, 2, '.', ' ') . "</td>";
            break;
        }
        return $sum;
    }
    $sum_treaty = 0;
    $amount_treaty = 0;
    $sum_price = 0;
    $sum_cost = 0;
    $sum_diff_amount = 0;
    foreach ($treaty_array as $key => $treaty_value) {
        //$cost = SearchArticleException($treaty_value["article_name"], $articles_exceptions_array) ? 0 : $treaty_value['cost_of_materials_no_pdv'];
        $cost = $treaty_value['cost_of_materials_no_pdv'];
        $sum_treaty += ($treaty_value['price_of_service_no_pdv'] + $cost) * $treaty_value['amount']; //  + $cost
        $amount_treaty += $treaty_value['amount'];
        $sum_price += $treaty_value['price_of_service_no_pdv'];
        $sum_cost += $treaty_value['cost_of_materials_no_pdv'];
        echo "<tr>";
        echo "<td class='table-column-id sticky-table-column'>" . ($key + 1) . "</td>";
        echo "<td class='table-column-article_name sticky-table-column'>" . $treaty_value['article_name'] . "</td>";
        echo "<td class='table-column-name sticky-table-column'>" . $treaty_value['name_service'] . "</td>";
        echo "<td class='table-column-type sticky-table-column'>" . $treaty_value['type_equipment'] . "</td>";
        echo "<td class='table-column-amount-remainder sticky-table-column'>" . $treaty_value['amount'] . "</td>";
        echo "<td class='table-column-price sticky-table-column th-nowrap'>" . number_format($treaty_value['price_of_service_no_pdv'], 2, ".", " ") . "</td>";
        echo "<td class='table-column-remainder sticky-table-column th-nowrap'>" . number_format($treaty_value['cost_of_materials_no_pdv'], 2, ".", " ") . "</td>";
        echo "<td class='table-column-sum sticky-table-column th-nowrap'>" . number_format(($treaty_value['price_of_service_no_pdv'] + $cost) * $treaty_value['amount'], 2, ".", " ") . "</td>"; //  + $cost
        $diff_amount = 0;
        foreach ($treaty_act_card_data_array as $treaty_act_value) {
            if ($treaty_act_value["treaty_id"] === $treaty_value['id']) {
                $diff_amount += $treaty_act_value['amount'];
            }
        }
        $diff_amount = $treaty_value['amount'] - $diff_amount;
        $sum_diff_amount += $diff_amount;
        echo "<td class='table-column-amount sticky-table-column'>" . $diff_amount . "</td>";
        foreach ($act_card_array as $act_value) {

            CreateActRow($treaty_value["id"], $act_value["id"], $treaty_value['amount'], $treaty_act_card_data_array, $articles_exceptions_array);
        }
        echo "</tr>";
    }

    foreach ($act_card_array as $act_value) {
        $sum_no_val = 0;
        $sum_amount = 0;
        $entries_sum_price = 0;
        $entries_sum_cost = 0;
        foreach ($treaty_act_card_data_array as $treaty_act_value) {
            if ($act_value["id"] === $treaty_act_value["act_card_id"]) {
                //$cost = SearchArticleException($treaty_act_value["article_name"], $articles_exceptions_array) ? 0: $treaty_act_value['cost_of_materials_no_pdv'];
                $sum_amount += $treaty_act_value["amount"];
                $sum_no_val += $treaty_act_value["amount"] * ($treaty_act_value["price_of_service_no_pdv"] + $treaty_act_value["cost_of_materials_no_pdv"]);
                //echo $cost . " ";
                $entries_sum_price += $treaty_act_value["price_of_service_no_pdv"];
                $entries_sum_cost += $treaty_act_value["cost_of_materials_no_pdv"];
            }
        } //echo $sum_amount . " " . $sum_no_val . " " . $entries_sum_price . " " . $entries_sum_cost . "<br/>";
        array_push($sum_array, [
            "sum_amount" => $sum_amount,
            "sum_no_val" => $sum_no_val,
            "sum_price" => $entries_sum_price,
            "sum_cost" => $entries_sum_cost
        ]);
    }
    echo "</tbody>";
    echo "<tfoot class='unselectable'>";
    echo "<tr class='table-footer'>";
    echo "<td colspan='3' class='table-td-colspan-blue-font sticky-table-column table-column-all'>Всього, грн. без ПДВ</td>";
    echo "<td class='sticky-table-column table-column-blank-1'></td>";
    echo "<td class='sticky-table-column table-column-blank-2 table-td-colspan-red-font'>" . $amount_treaty . "</td>";
    echo "<td class='sticky-table-column table-column-blank-3 table-td-colspan-blue-font  th-nowrap'>" . number_format($sum_price, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-4 table-td-colspan-blue-font  th-nowrap'>" . number_format($sum_cost, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-5 table-td-colspan-red-font  th-nowrap'>" . number_format($sum_treaty, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-6 table-td-colspan-red-font th-nowrap'>" . $sum_diff_amount . "</td>";
    foreach ($act_card_array as $key => $value) {
        echo "<td class='table-td-colspan-red-font'>" . $sum_array[$key]["sum_amount"] . "</td>";
        echo "<td class='table-td-colspan-blue-font  th-nowrap'>" . number_format($sum_array[$key]["sum_price"], 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-blue-font  th-nowrap'>" . number_format($sum_array[$key]["sum_cost"], 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-red-font  th-nowrap'>" . number_format($sum_array[$key]["sum_no_val"], 2, ".", " ") . "</td>"; // 408
    }

    echo "</tr>";
    echo "<tr class='table-footer'>";
    echo "<td colspan='3' class='table-td-colspan-blue-font sticky-table-column table-column-all'>ПДВ, 20%</td>";
    echo "<td class='sticky-table-column table-column-blank-1'></td>";
    echo "<td class='sticky-table-column table-column-blank-2 table-td-colspan-red-font'></td>";
    echo "<td class='sticky-table-column table-column-blank-3 table-td-colspan-blue-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? 0 : $sum_price * 0.2, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-4 table-td-colspan-blue-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? 0 : $sum_cost * 0.2, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-5 table-td-colspan-red-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? 0 : $sum_treaty * 0.2, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-6'></td>";
    foreach ($act_card_array as $key => $value) {
        echo "<td></td>";
        echo "<td class='table-td-colspan-blue-font'>" . number_format($contract_sign == 'Без ПДВ' ? 0 : $sum_array[$key]["sum_price"] * 0.2, 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-blue-font'>" . number_format($contract_sign == 'Без ПДВ' ? 0 : $sum_array[$key]["sum_cost"] * 0.2, 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-red-font'>" . number_format($contract_sign == 'Без ПДВ' ? 0 : $sum_array[$key]["sum_no_val"] * 0.2, 2, ".", " ") . "</td>";
    }
    echo "</tr>";
    echo "<tr class='table-footer'>";
    echo "<td colspan='3' class='table-td-colspan-blue-font sticky-table-column table-column-all'>Всього, грн. з ПДВ</td>";
    echo "<td class='sticky-table-column table-column-blank-1'></td>";
    echo "<td class='sticky-table-column table-column-blank-2'></td>";
    echo "<td class='sticky-table-column table-column-blank-3 table-td-colspan-blue-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? $sum_price : $sum_price * 1.2, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-4 table-td-colspan-blue-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? $sum_cost : $sum_cost * 1.2, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-5 table-td-colspan-red-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? $sum_treaty : $sum_treaty * 1.2, 2, ".", " ") . "</td>";
    echo "<td class='sticky-table-column table-column-blank-6'></td>";
    foreach ($act_card_array as $key => $value) {
        echo "<td></td>";
        echo "<td class='table-td-colspan-blue-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? $sum_array[$key]["sum_price"] : $sum_array[$key]["sum_price"] * 1.2, 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-blue-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? $sum_array[$key]["sum_cost"] : $sum_array[$key]["sum_cost"] * 1.2, 2, ".", " ") . "</td>";
        echo "<td class='table-td-colspan-red-font th-nowrap'>" . number_format($contract_sign == 'Без ПДВ' ? $sum_array[$key]["sum_no_val"] : $sum_array[$key]["sum_no_val"] * 1.2, 2, ".", " ") . "</td>";
    }

    echo "</tfoot>";
    echo "</table>";
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM($data["counterpartyId"], $data["contractId"], $data["startMonth"], $data["endMonth"], $data["articleName"]);
        break;
    case "selectBarDataRequest":
        GetSelectBarData($data["startMonth"], $data["endMonth"]);
        break;
    default:
        break;
}
