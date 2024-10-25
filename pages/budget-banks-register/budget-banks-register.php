<?php
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/check_session.php");
require_once("../../templates/classes/db_finansist.php");


function GetBankRegisterData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT if((SELECT count(b.id) FROM banks_register b 
                                        WHERE b.ACC_NUMB = a.ACC_NUMB and b.operDate LIKE '" . $data['date'] . "%'
                                              and ((b.new_codes_directory_id is null or b.new_codes_directory_id = 0) and (b.additional_purpose = 0 or b.additional_purpose is null))) > 0, false, true) as status, 
                                    SUBSTRING(a.operDate, 1, 10) as operDate, a.ACC_NUMB, a.BANK_MFO, a.BANK_NAME, a.ACC_NUMB_IBAN, 
                                    round(sum(a.operSum), 2) as sum FROM banks_register a WHERE a.operDate LIKE '" . $data['date'] . "%' GROUP BY BANK_NAME");
    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "date" => $row["operDate"],
            "checking_account" => $row["ACC_NUMB"],
            "iban" => $row["ACC_NUMB_IBAN"],
            "mfo" => $row["BANK_MFO"],
            "bank" => $row["BANK_NAME"], 
            "sum" => $row["sum"],
            "status" => $row["status"], 
        ]);
    }
    $mysql->close();
    return $data_array;
}



function CreateTableDOM($data, $admin_role, $financier_role, $director_role) {
    $data_array = GetBankRegisterData($data);
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th id='main-table-th-id' class='main-table-th table-column-id'>№</th>";
    echo "<th id=''main-table-th-actions' class='main-table-th table-column-actions'>Обробка</th>";
    echo "<th id=''main-table-th-date' class='main-table-th table-column-date'>Дата</th>";
    echo "<th id=''main-table-th-checking-account' class='main-table-th table-column-checking-account'>Розрахунковий рахунок</th>";
    echo "<th id=''main-table-th-mfo' class='main-table-th table-column-mfo'>МФО</th>";
    echo "<th id=''main-table-th-bank' class='main-table-th table-column-bank'>Банк</th>";
    echo "<th id=''main-table-th-sum' class='main-table-th table-column-sum'>Сума, грн</th>";
    echo "<th id=''main-table-th-status' class='main-table-th table-column-status'>Статус</th>";
    echo "</tr>";
    echo "<tbody>";
    foreach($data_array as $key => $value) {    
        echo "<tr>";
        echo "<td class='table-column-id'>" . ($key + 1) ."</td>";
        echo "<td class='table-column-actions'><button onclick='bankStatementsProcessing(" . json_encode($value['iban']) . ")'>Обробка</button></td>";
        echo "<td class='table-column-date'>" . $value['date'] . "</td>";
        echo "<td class='table-column-checking-account'>" . $value["checking_account"] . "</td>";
        echo "<td class='table-column-mfo'>" . $value["mfo"] . "</td>";
        echo "<td class='table-column-bank'>" . $value["bank"] . "</td>";
        echo "<td class='table-column-sum'>" . number_format($value["sum"], 2, '.', ' ') . "</td>";
        echo "<td class='table-column-status'>";
        echo "<span id='sign-planned-indicator'>";
        echo $value["status"] == true ? "<div class='check-mark-element-1'></div><div class='check-mark-element-2'></div>" : 
                                        "<div class='cross-element-1'><div class='cross-element-2'></div></div>";
        echo "</span>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function GetNewCodes() {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT b.id, b.new_code FROM `budget_articles_directory` a
                             LEFT JOIN new_codes_directory b ON b.id = a.new_codes_directory_id
                             LEFT JOIN old_codes_directory c ON c.id = b.old_codes_directory_id");
    while($row = $result->fetch_assoc()) {  
        array_push($data_array, [
            "id" => $row["id"],
            "new_code" => $row["new_code"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetPaymentsData($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    if(!isset($data['newCodeId']) || $data['startDate'] == "" || $data['endDate'] == ""){
        $mysql->close();
        return [];
    }
    $result = $mysql->query("SELECT * FROM banks_register WHERE new_codes_directory_id = '" . $data['newCodeId'] . "' and 
                                                                 operDate BETWEEN '" . $data['startDate'] . "' and '" . $data['endDate'] . "'");
    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "oper_number" => $row["operNumber"],
            "date" => substr($row["operDate"], 0, 10),
            "old_code" => $row["old_code"],
            "new_code" => $row["new_code"],
            "sum" => $row["operSum"],
            "counterparty" => $row["recipientNameFromExtract"], 
            "payment_type" => $row["paymentType"],
        ]);
    }

    $result = $mysql->query("SELECT b.operNumber, b.recipientNameFromExtract, a.oper_date, a.purpose, a.sum, a.old_code, a.new_code FROM banks_register_additional_purpose a
                             LEFT JOIN banks_register b ON b.id = a.banks_register_id
                             WHERE a.new_codes_directory_id = '" . $data['newCodeId'] . "' and 
                                                                 a.oper_date BETWEEN '" . $data['startDate'] . "' and '" . $data['endDate'] . "'");
    while($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "oper_number" => $row["operNumber"],
            "date" => substr($row["oper_date"], 0, 10),
            "old_code" => $row["old_code"],
            "new_code" => $row["new_code"],
            "sum" => $row["sum"],
            "counterparty" => $row["recipientNameFromExtract"], 
            "payment_type" => $row["purpose"],
        ]);
    }                                                             

    $mysql->close();
    return $data_array;
}

function CreateSearchTableDOM($data, $admin_role, $financier_role, $director_role) {
    $data_array = GetPaymentsData($data);
    
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id'>№</th>";
    echo "<th class='main-table-th table-column-oper-number'>Номер доручення</th>";
    echo "<th class='main-table-th table-column-date'>Дата</th>";
    echo "<th class='main-table-th table-column-old-code'>Код статті (старий)</th>";
    echo "<th class='main-table-th table-column-new-code'>Код статті (новий)</th>";
    echo "<th class='main-table-th table-column-sum'>Сума, грн</th>";
    echo "<th class='main-table-th table-column-counterparty'>Контрагент</th>";
    echo "<th class='main-table-th table-column-payment-type'>Призначення платежу</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach($data_array as $key => $value) {    
        echo "<tr>";
        echo "<td class='table-column-id'>" . ($key + 1) ."</td>";
        echo "<td class='table-column-oper-number'>" . $value['oper_number'] . "</td>";
        echo "<td class='table-column-date'>" . $value['date'] . "</td>";
        echo "<td class='table-column-old-code'>" . $value["old_code"] . "</td>";
        echo "<td class='table-column-new-code'>" . $value["new_code"] . "</td>";
        echo "<td class='table-column-sum'>" . $value["sum"] . "</td>";
        echo "<td class='table-column-counterparty'>" . $value["counterparty"] . "</td>";
        echo "<td class='table-column-payment-type'>" . $value["payment_type"] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
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
    case "updateBanksRegisterRequest":
        $finansist = new DB_Finansist();
        $finansist -> UpdateBanksRegister($data);
        break;
    case "renderTableRequest":
        CreateTableDOM($data, $admin_role, $financier_role, $director_role);
        break;
    case "getNewCodesRequest":
        GetNewCodes();
        break;
    case "renderSearchTableRequest":
        CreateSearchTableDOM($data, $admin_role, $financier_role, $director_role);
        break;
    default:
        break;
}

?>