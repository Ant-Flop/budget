<?php
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/check_session.php");
require_once("../../templates/classes/db_finansist.php");


function GetBankRegisterData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM banks_register WHERE operDate = '" . $data['date'] . "' and ACC_NUMB_IBAN = '" . $data['iban'] . "' ORDER BY operNumber ASC " . $data['limit']);
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "order_number" => $row["operNumber"],
            "date" => substr($row["operDate"], 0, 10),
            "check_current_year" => substr($row["operDate"], 0, 4) == date("Y") ? true : false,
            "old_code" => $row["old_code"],
            "old_code_id" => $row["old_codes_directory_id"],
            "new_code" => $row["new_code"],
            "new_code_id" => $row["new_codes_directory_id"],
            "sum" => $row["operSum"],
            "counterparty" => $row["recipientNameFromExtract"],
            "payment_type" => $row["paymentType"],
            "checking_account" => $row["ACC_NUMB"],
            "mfo" => $row["BANK_MFO"],
            "bank" => $row["BANK_NAME"],
            "additional_purpose" => $row["additional_purpose"],
        ]);
    }
    $mysql->close();
    return $data_array;
}

function GetOldCodesData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(c.id), c.old_code FROM `budget_articles_directory` a
                             LEFT JOIN new_codes_directory b ON b.id = a.new_codes_directory_id
                             LEFT JOIN old_codes_directory c ON c.id = b.old_codes_directory_id WHERE c.id IS NOT NULL");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "old_code" => $row["old_code"],
        ]);
    }
    $mysql->close();
    return $data_array;
}

function GetNewCodesData($old_code_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(b.id), b.new_code FROM `budget_articles_directory` a
                             LEFT JOIN new_codes_directory b ON b.id = a.new_codes_directory_id
                             LEFT JOIN old_codes_directory c ON c.id = b.old_codes_directory_id WHERE c.id = '" . $old_code_id . "'");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "new_code" => $row["new_code"],
        ]);
    }
    $mysql->close();
    return $data_array;
}

function FillOldCodesSelect($old_code_id)
{
    $old_codes_array = GetOldCodesData();
    $options = isset($old_code_id) ? "<option>Обрати</option>" : "<option selected hidden>Обрати</option>";
    foreach ((array)$old_codes_array as $key => $element) {
        if ($element['id'] == $old_code_id)
            $options = $options . "<option data-old-code-id='" . $element['id'] . "' data-old-code='" . $element['old_code'] . "' selected hidden>" . $element['old_code'] . "</option>";
        else
            $options = $options . "<option data-old-code-id='" . $element['id'] . "' data-old-code='" . $element['old_code'] . "'>" . $element['old_code'] . "</option>";
    }
    return $options;
}

function FillNewCodesSelect($old_code_id, $new_code_id)
{
    $new_codes_array = GetNewCodesData($old_code_id);
    $options = isset($new_code_id) ? "<option>Обрати</option>" : "<option selected hidden>Обрати</option>";;
    foreach ((array)$new_codes_array as $key => $element) {
        if ($element['id'] == $new_code_id)
            $options = $options . "<option data-new-code-id='" . $element['id'] . "' data-new-code='" . $element['new_code'] . "' selected hidden>" . $element['new_code'] . "</option>";
        else
            $options = $options . "<option data-new-code-id='" . $element['id'] . "' data-new-code='" . $element['new_code'] . "'>" . $element['new_code'] . "</option>";
    }
    return $options;
}



function CreateTableDOM($data, $admin_role, $financier_role, $director_role)
{
    $data_array = GetBankRegisterData($data);
    if (count($data_array) > 0)
        $th_checkbox_disabled =   ""; // $data_array[0]["check_current_year"] === false ? 
    else $th_checkbox_disabled = "disabled";
    $old_code_data_array = GetOldCodesData();

    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th id='main-table-th-id' class='main-table-th table-column-id'>№</th>";
    echo "<th id='main-table-th-checkbox' class='main-table-th table-column-checkbox'><input type='checkbox' class='" . $th_checkbox_disabled . "'  onchange='batchProcessingCheckboxOnChange(this, true)' " . $th_checkbox_disabled . "></th>";
    echo "<th id='main-table-th-order-number' class='main-table-th table-column-order-number'>Номер доручення</th>";
    echo "<th id='main-table-th-date' class='main-table-th table-column-date'>Дата</th>";
    echo "<th id='main-table-th-old-code' class='main-table-th table-column-old-code'>Код статті (старий)</th>";
    echo "<th id='main-table-th-new-code' class='main-table-th table-column-new-code'>Код статті (новий)</th>";
    echo "<th id='main-table-th-sum' class='main-table-th table-column-sum'>Сума, грн</th>";
    echo "<th id='main-table-th-counterparty' class='main-table-th table-column-counterparty'>Контрагент</th>";
    echo "<th id='main-table-th-payment-type' class='main-table-th table-column-payment-type'>Призначення платежу</th>";
    echo "</tr>";
    echo "<tbody>";
    foreach ($data_array as $key => $value) {
        $option_old_codes = FillOldCodesSelect($value["old_code_id"]);
        $option_new_codes = FillNewCodesSelect($value["old_code_id"], $value["new_code_id"]);
        $additional_purpose_disabled = $value["additional_purpose"] != 0  ? "disabled" : ""; // || $value["check_current_year"] === false
        $codes_disabled = $value['old_code_id'] != 0  ? "disabled" : ""; // || $value["check_current_year"] === false
        echo "<tr>";
        echo "<td class='table-column-id'>" . ($key + 1) . "</td>";
        echo "<td class='table-column-checkbox'><input type='checkbox' class='batch-processing-checkbox " . $additional_purpose_disabled . "' 
                                                       data-payment-id='" . $value['id'] . "' data-previous-new-code-id='" . $value['new_code_id'] . "' 
                                                       data-previous-new-code='" . $value['new_code'] . "'
                                                       data-previous-old-code='" . $value['old_code']  . "' 
                                                       onchange='batchProcessingCheckboxOnChange(this, false)' " . $additional_purpose_disabled . "></td>";
        echo "<td class='table-column-order-number'>" . $value['order_number'] . "</td>";
        echo "<td class='table-column-date'>" . $value['date'] . "</td>";
        echo "<td class='table-column-old-code " . $additional_purpose_disabled . "'><select data-payment-id='" . $value['id'] . "' data-previous-new-code-id='" . $value['new_code_id'] . "' onchange='oldCodeSelectOnChange(this, " . $value['new_code_id'] . ")' " . $additional_purpose_disabled . ">" . $option_old_codes . "</select></td>";
        echo "<td class='table-column-new-code " . $additional_purpose_disabled . "'><select data-payment-id='" . $value['id'] . "' data-previous-new-code-id='" . $value['new_code_id'] . "' data-old-code='" . $value['old_code'] . "' onchange='newCodeSelectOnChange(this, " . $value['new_code_id'] . ")' " . $additional_purpose_disabled . ">" . $option_new_codes . "</select></td>";
        echo "<td class='table-column-sum'><button class='additional-purpose__button " . $codes_disabled . "' onclick='modalAdditionalPurposeOnClick(" . $value['id'] . ")' " . $codes_disabled . ">+</button>" . number_format($value["sum"], 2, '.', ' ') . "</td>";
        echo "<td class='table-column-counterparty'>" . $value["counterparty"] . "</td>";
        echo "<td class='table-column-payment-type'>" . $value["payment_type"] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function GetPlannedIndicators($new_code_id, $year)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT id FROM planned_indicators WHERE new_codes_directory_id = '" . $new_code_id . "' and year = '" . $year . "'");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, $row['id']);
    }
    $mysql->close();
    return $data_array;
}


function GetSimilarPayments($iban, $date, $id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result_payment = $mysql->query("SELECT paymentType, old_codes_directory_id, new_codes_directory_id, recipientNameFromExtract FROM banks_register WHERE id = '" . $id . "'");
    $payment_info = $result_payment->fetch_assoc();
    $result = $mysql->query("SELECT id, substring(operDate, 1, 10) as date, operSum as sum, new_codes_directory_id, old_code, new_code, paymentType  FROM banks_register 
                             WHERE paymentType LIKE '" . mysqli_real_escape_string($mysql, $payment_info['paymentType']) . "' and 
                                   operDate = '" . $date . "' and ACC_NUMB_IBAN = '" . $iban . "' and 
                                   recipientNameFromExtract = '" . mysqli_real_escape_string($mysql, $payment_info['recipientNameFromExtract']) . "' and
                                   (additional_purpose = 0 or additional_purpose is null) and 
                                   old_codes_directory_id " . (isset($payment_info['old_codes_directory_id']) ? " = '" . $payment_info['old_codes_directory_id'] . "'" :  "is null") . " and
                                   new_codes_directory_id " . (isset($payment_info['new_codes_directory_id']) ? " = '" . $payment_info['new_codes_directory_id'] . "'" :  "is null"));

    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "date" => $row["date"],
            "sum" => $row["sum"],
            "old_code" => $row["old_code"],
            "new_code" => $row["new_code"],
            "payment_type" => $row["paymentType"],
        ]);
    }
    $mysql->close();
    return $data_array;
}

function SaveOldCodesData($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = 0;
    $data_array = GetSimilarPayments($data["iban"], $data["date"], $data["paymentId"]);
    foreach ($data_array as $key => $element) {
        TakeOffFactIndicatorsAmounts($element["date"], $data['previousNewCodeId'], $element["sum"], $element["payment_type"]);
        if ($data['oldCodeId'] == '')
            $result = $mysql->query("UPDATE banks_register SET old_code = null, old_codes_directory_id = null WHERE id = '" . $element['id'] . "'");
        else
            $result = $mysql->query("UPDATE banks_register SET old_code = '" . $data['oldCode'] . "', old_codes_directory_id = '" . $data['oldCodeId'] . "' WHERE id = '" . $element['id'] . "'");
        $result = $mysql->query("UPDATE banks_register SET new_code = null, new_codes_directory_id = null WHERE id = '" . $element['id'] . "'");
        BankRegisterLog($user_id, $element['id'], $element['old_code'], $data['oldCode'], $element['new_code'], null);
    }

    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function SaveNewCodesData($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = GetSimilarPayments($data["iban"], $data["date"], $data["paymentId"]);
    foreach ($data_array as $key => $element) {
        $result = $mysql->query("UPDATE banks_register SET new_code = '" . $data['newCode'] . "', new_codes_directory_id = '" . $data['newCodeId'] . "' WHERE id = '" . $element['id'] . "'");

        TakeOffFactIndicatorsAmounts($element["date"], $data['previousNewCodeId'], $element["sum"], $element["payment_type"]);
        if (isset($data['newCodeId']))
            SetFactIndicatorsAmounts($element["date"], $data['newCodeId'], $element["sum"], $element["payment_type"]);
        BankRegisterLog($user_id, $element['id'], $element['old_code'], $data['oldCode'], $element['new_code'], $data['newCode']);
    }
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
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

    return $check_section;
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

function SetFactIndicatorsAmounts($date, $new_code_id, $sum, $payment_type)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);
    $month_with_vat = $month . "_with_vat";
    $month_no_vat = $month . "_no_vat";

    $pi_id_array = GetPlannedIndicators($new_code_id, $year);
    $is_payment_contract = [];
    $pi_id_clear = null;
    foreach ($pi_id_array as $pi_id) {
        $is_section = IsSectionException($pi_id);
        if ($is_section) {
            $contracts = GetContracts($pi_id);
            foreach ($contracts as $contract) {
                if (strpos($payment_type, $contract['contract_number']) !== false) {
                    $is_payment_contract = $contract;
                }
            }
        } else $pi_id_clear = $pi_id;
    }

    if (count($is_payment_contract) > 0)
        $mysql->query("UPDATE fact_indicators_amounts SET $month_with_vat = ROUND(if($month_with_vat is null, 0, $month_with_vat) + $sum, 2), 
                             sum_with_vat = ROUND(if(sum_with_vat is null, 0, sum_with_vat) + $sum, 2) 
                             WHERE planned_indicators_id = '" . $is_payment_contract['pi_id'] . "'");
    else {
        $mysql->query("UPDATE fact_indicators_amounts SET $month_with_vat = ROUND(if($month_with_vat is null, 0, $month_with_vat) + $sum, 2), 
                        sum_with_vat = ROUND(if(sum_with_vat is null, 0, sum_with_vat) + $sum, 2)  
                         WHERE planned_indicators_id = '" . $pi_id_clear . "'");
    }

    // $result_pi = $mysql->query("SELECT id FROM planned_indicators WHERE new_codes_directory_id = '" . $new_code_id . "' and year = '" . $year . "'");


    // while ($row_pi = $result_pi->fetch_assoc()) {
    //     $mysql->query("UPDATE fact_indicators_amounts SET $month_with_vat = ROUND(if($month_with_vat is null, 0, $month_with_vat) + $sum, 2), 
    //                         sum_with_vat = ROUND(if(sum_with_vat is null, 0, sum_with_vat) + $sum, 2) 
    //                         WHERE planned_indicators_id IN (SELECT id FROM planned_indicators 
    //                         WHERE new_codes_directory_id = '" . $new_code_id . "' and year = '" . $year . "')");
    // }


    //$mysql->query("UPDATE planned_indicators_amounts SET edit_mode = false WHERE planned_indicators_id = (SELECT id FROM planned_indicators WHERE new_codes_directory_id = '" . $new_code_id . "' and year = '" . $year . "')");  
    $mysql->close();
}

function TakeOffFactIndicatorsAmounts($date, $new_code_id, $sum, $payment_type)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);
    $month_with_vat = $month . "_with_vat";
    $month_no_vat = $month . "_no_vat";

    $pi_id_array = GetPlannedIndicators($new_code_id, $year);
    $is_payment_contract = [];
    $pi_id_clear = null;
    foreach ($pi_id_array as $pi_id) {
        $is_section = IsSectionException($pi_id);
        if ($is_section) {
            $contracts = GetContracts($pi_id);
            foreach ($contracts as $contract) {
                if (strpos($payment_type, $contract['contract_number']) !== false) {
                    $is_payment_contract = $contract;
                }
            }
        } else $pi_id_clear = $pi_id;
    }

    if (count($is_payment_contract) > 0)
        $mysql->query("UPDATE fact_indicators_amounts SET $month_with_vat = ROUND($month_with_vat - $sum, 2), 
                                                      sum_with_vat = ROUND(sum_with_vat - $sum, 2) 
                   WHERE planned_indicators_id = '" . $is_payment_contract['pi_id'] . "'");
    else {
        //IN (SELECT id FROM planned_indicators WHERE new_codes_directory_id = '" . $new_code_id . "' and year = '" . $year . "')
        $mysql->query("UPDATE fact_indicators_amounts SET $month_with_vat = ROUND($month_with_vat - $sum, 2), 
                                                      sum_with_vat = ROUND(sum_with_vat - $sum, 2) 
                   WHERE planned_indicators_id = '" . $pi_id_clear . "'");
    }
    $mysql->close();
}

function BankRegisterLog($user_id, $payment_id, $previous_old_code, $current_old_code, $previous_new_code, $current_new_code)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("INSERT INTO banks_register_action_log (banks_register_id, user_id, previous_old_code, current_old_code, previous_new_code, current_new_code, action, datetime)
                               VALUES ('" . $payment_id . "', '" . $user_id . "', '" . $previous_old_code . "', '" . $current_old_code . "', '" .
        $previous_new_code . "', '" . $current_new_code . "', 'edit', '" . date('Y-m-d H:i:s') . "')");
    $mysql->close();
}

function BankRegisterAdditionalPurposeLog($user_id, $id, $old_code, $new_code, $action)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("INSERT INTO banks_register_additional_purpose_action_log (banks_register_additional_purpose_id, user_id, old_code, new_code, action, datetime)
                                VALUES ('" . $id . "', '" . $user_id . "', '" . $old_code . "', '" . $new_code . "', '" . $action . "', '" . date('Y-m-d H:m:s') . "')");
    $mysql->close();
}

function PaymentsBatchProcessing($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = 0;
    foreach ($data["paymentArray"] as $key => $element) {
        $payment_data = GetPaymentInfo($element['paymentId']);
        TakeOffFactIndicatorsAmounts($payment_data["date"], $element['previousNewCodeId'], $payment_data["sum"], $payment_data["payment_type"]);
        if (isset($data['newCodeId']))
            SetFactIndicatorsAmounts($payment_data["date"], $data['newCodeId'], $payment_data["sum"], $payment_data["payment_type"]);
        $result = $mysql->query("UPDATE banks_register SET old_code = '" . $data['oldCode'] . "', old_codes_directory_id = '" . $data['oldCodeId'] . "', 
                                                           new_code = '" . $data['newCode'] . "', new_codes_directory_id = '" . $data['newCodeId'] . "' 
                                                       WHERE id = '" . $element['paymentId'] . "'");
        BankRegisterLog($user_id, $element['paymentId'], $element['previousOldCode'], $data['oldCode'], $element['previousNewCode'], $data['newCode']);
    }
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function GetPaymentInfo($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM banks_register WHERE id = '" . $id . "'");
    $row = $result->fetch_assoc();
    $mysql->close();
    return [
        "id" => $row["id"],
        "sum" => $row["operSum"],
        "payment_type" => $row["paymentType"],
        "old_code" => $row["old_code"],
        "old_code_id" => $row["old_codes_directory_id"],
        "new_code" => $row["new_code"],
        "new_code_id" => $row["new_codes_directory_id"],
        "oper_number" => $row["operNumber"],
        "date" => substr($row["operDate"], 0, 10),
    ];
}

function GetAdditionalPurpose($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM banks_register_additional_purpose WHERE banks_register_id = '" . $data['id'] . "'");
    $i = 0;
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id_row" => $i,
            "id" => $row["id"],
            "banks_register_id" => $row["banks_register_id"],
            "purpose" => $row["purpose"],
            "sum" => $row["sum"],
            "old_code_id" => $row["old_codes_directory_id"],
            "old_code" => $row["old_code"],
            "new_code_id" => $row["new_codes_directory_id"],
            "new_code" => $row["new_code"],
            "date" => $row["oper_date"],
        ]);
        $i++;
    }
    echo json_encode($data_array);
    $mysql->close();
}

function CheckAdditionalPurposeExists($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT count(*) as count FROM banks_register_additional_purpose WHERE banks_register_id = '" . $id . "'");
    $row = $result->fetch_assoc();
    $mysql->close();
    return $row["count"];
}

function modalSaveAdditionalPurpose($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    // delete addit purpose

    foreach ($data["deletedData"] as $key => $element) {
        TakeOffFactIndicatorsAmounts($element["date"], $element['new_code_id'], $element["sum"], $element["purpose"]);
        if (CheckAdditionalPurposeExists($element['banks_register_id']) != 0)
            $mysql->query("UPDATE banks_register SET additional_purpose = 0 WHERE id = '" . $element['banks_register_id'] . "'");
        $result = $mysql->query("DELETE FROM banks_register_additional_purpose WHERE id = '" . $element['id'] . "'");
        BankRegisterAdditionalPurposeLog($user_id, $element["id"], $element["old_code"], $element["new_code"], "delete");
    }
    // insert addit purpose
    foreach ($data["createdData"] as $key => $element) {
        SetFactIndicatorsAmounts($element["date"], $element['new_code_id'], $element["sum"], $element["purpose"]);
        $result = $mysql->query("INSERT INTO banks_register_additional_purpose (banks_register_id, oper_date, purpose, sum, old_code, new_code, old_codes_directory_id, new_codes_directory_id) 
                       VALUES ('" . $element['banks_register_id'] . "', '" . $element['date'] . "', '" . $element['purpose'] . "', '" . $element['sum'] . "','" . $element['old_code'] . "', 
                               '" . $element['new_code'] . "', '" . $element['old_code_id'] . "', '" . $element['new_code_id'] . "')");
        $result = $mysql->query("UPDATE banks_register SET additional_purpose = 1 WHERE id = '" . $element['banks_register_id'] . "'");
        BankRegisterAdditionalPurposeLog($user_id, $element["id"], $element["old_code"], $element["new_code"], "add");
    }
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
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
        CreateTableDOM($data, $admin_role, $financier_role, $director_role);
        break;
    case "getAmountRecordsPaymentsRequest":
        echo json_encode(count(GetBankRegisterData($data)));
        break;
    case "oldCodesSaveRequest":
        SaveOldCodesData($data, $user_id);
        break;
    case "newCodesSaveRequest":
        SaveNewCodesData($data, $user_id);
        break;
    case "getOldCodesRequest":
        echo json_encode(GetOldCodesData());
        break;
    case "getNewCodesRequest":
        echo json_encode(GetNewCodesData($data["oldCodeId"]));
        break;
    case "paymentsBatchProcessingRequest":
        PaymentsBatchProcessing($data, $user_id);
        break;
    case "modalGetPaymentInfoRequest":
        echo json_encode(GetPaymentInfo($data["id"]));
        break;
    case "modalGetAdditionalPurposeRequest":
        GetAdditionalPurpose($data);
        break;
    case "modalSaveAdditionalPurposeRequest":
        modalSaveAdditionalPurpose($data, $user_id);
        break;
    default:
        break;
}