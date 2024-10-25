<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetBanksData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $result = $mysql->query("SELECT * FROM banks_directory");
    while($row = $result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row["id"],
            "code" => $row["code"],
            "current_account" => $row["current_account"],
            "name" => $row["name"],
            "mfo" => $row["mfo"],
            "iban" => $row["iban"],
        ]);
    }
    $mysql->close();
    return $dataArray;
}

function CreateTableDOM($admin_role)
{
    $data = GetBanksData();

    //шапка таблицы
    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id sticky-table-column'>№</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-code sticky-table-column'>Код</th>";
    echo "<th class='main-table-th table-column-current-account sticky-table-column'>Розрахунковий рахунок</th>";
    echo "<th class='main-table-th table-column-name sticky-table-column'>Найменування банку</th>";
    echo "<th class='main-table-th table-column-mfo sticky-table-column'>МФО</th>";
    echo "<th class='main-table-th table-column-iban sticky-table-column'>Розрахунковий рахунок IBAN</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";

    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        $disabled = "";
        echo "<tr>";
        echo "<td class='table-column-id'>" . $id . "</td>";
        echo "<td class='table-column-actions sticky-table-column'>";
        echo "<div class='td-toolbar'>";
        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditBankOnClick(" . $data[$i]["id"] . ")'/>";
        if($admin_role)
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteBankOnClick(" . $data[$i]["id"] . ")' " . $disabled . "/>";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-code'>"; 
        echo $data[$i]['code'];
        echo "</td>";
        echo "<td class='table-column-current-account'>"; 
        echo $data[$i]['current_account'];
        echo "</td>";
        echo "<td class='table-column-name'>"; 
        echo $data[$i]['name'];
        echo "</td>";
        echo "<td class='table-column-mfo'>"; 
        echo $data[$i]['mfo'];
        echo "</td>";
        echo "<td class='table-column-iban'>"; 
        echo $data[$i]['iban'];
        echo "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function ModalSaveAddBank($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO banks_directory (code, current_account, name, mfo, iban) 
                                VALUES ('" . $data['code'] . "', 
                                        '" . $data['currentAccount'] . "', 
                                        '" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                        '" . $data['mfo'] . "', 
                                        '" . mysqli_real_escape_string($mysql, $data['iban']) . "')");
    $mysql->query("INSERT INTO banks_directory_action_log (banks_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function GetBankInfo ($id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM banks_directory WHERE id = '" . $id . "'");
    $row = $result->fetch_assoc();
    $mysql->close();
    echo json_encode([
        "id" => $row["id"],
        "code" => $row["code"],
        "current_account" => $row["current_account"],
        "name" => $row["name"],
        "mfo" => $row["mfo"],
        "iban" => $row["iban"],
    ]);
}

function ModalSaveEditBank($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE banks_directory SET code = '" . $data['code'] . "', 
                                                        current_account = '" . $data['currentAccount'] . "', 
                                                        name = '" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                                        mfo = '" . $data['mfo'] . "', 
                                                        iban = '" . mysqli_real_escape_string($mysql, $data['iban']) . "' WHERE id = '" . $data['id'] . "'");
    $mysql->query("INSERT INTO banks_directory_action_log (banks_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteBank($id, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM banks_directory WHERE id = '" . $id . "'");
    
    $mysql->query("INSERT INTO banks_directory_action_log (banks_directory_id, user_id, action, datetime) 
                                VALUES ('" . $id . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);
switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM($admin_role);
        break;
    case "modalSaveAddBankRequest":
        ModalSaveAddBank($data, $user_id);
        break;
    case "getEditBankInfoRequest":
        GetBankInfo($data["id"]);
        break;
    case "modalSaveEditBankRequest":
        ModalSaveEditBank($data, $user_id);
        break;
    case "deleteBankRequest":
        DeleteBank($data["id"], $user_id);
        break;
    default:
        break;
}
?>