<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetFundholdersData () {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $result = $mysql->query("SELECT * FROM fundholders_directory");
    while($row = $result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row["id"],
            "name" => $row["name"],
            "short_name" => $row["additional_name"],
            "delete_mode" => $row["delete_mode"],
        ]);
    }
    $mysql->close();
    return $dataArray;
}

function CreateTableDOM ($admin_role) {
    $data = GetFundholdersData();

    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id'>№</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-name'>Назва фондоутримувача</th>";
    echo "<th class='main-table-th table-column-shortname'>Скорочена назва фондоутримувача</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";

    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        //проверка на наличие договоров
        $disabled = "disabled"; //  $data[$i]["delete_mode"] ? "" :
        echo "<tr>";
        echo "<td class='table-column-id'>" . $id . "</td>";
        echo "<td class='table-column-actions sticky-table-column'>";
        echo "<div class='td-toolbar'>";
        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditFundholderOnClick(" . $data[$i]["id"] . ")'/>";
        if($admin_role === true)
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteFundholderOnClick(" . $data[$i]["id"] . ")' " . $disabled . "/>";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-name'>"; 
        echo $data[$i]['name'];
        echo "</td>";
        echo "<td class='table-column-name'>"; 
        echo $data[$i]['short_name'];
        echo "</td>";
    }
    echo "</tbody>";

    echo "</table>";
}

function ModalSaveAddFundholder ($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $words_array = explode(" ", $data["name"]);
    $short_name = "";
    foreach($words_array as $key => $value) 
        $short_name = $short_name . ucfirst(mb_substr($value, 0, 1));
    $result = $mysql->query("INSERT INTO fundholders_directory (name, short_name, additional_name) 
                                VALUES ('" . mysqli_real_escape_string($mysql, $data['name']) . "', '" . $short_name . "', '" . mysqli_real_escape_string($mysql, $data['shortname']) . "')");
    $mysql->query("INSERT INTO fundholders_directory_action_log (fundholders_directory_id	, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function GetFundholderInfo ($id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM fundholders_directory WHERE id = '" . $id . "'");
    $data = $result->fetch_assoc();
    $data_array = json_encode([
        'id' => $data["id"],
        'name' => $data["name"],
        'short_name' => $data["additional_name"],
    ]);
    echo $data_array;
    $mysql->close();
}


function ModalSaveEditFundholder($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $words_array = explode(" ", $data["name"]);
    $short_name = "";
    foreach($words_array as $key => $value) 
        $short_name = $short_name . ucfirst(mb_substr($value, 0, 1));
    $result = $mysql->query("UPDATE fundholders_directory SET name = '" . mysqli_real_escape_string($mysql, $data['name']) . "',
                                                                 short_name = '" . $short_name . "', 
                                                                 additional_name = '" . mysqli_real_escape_string($mysql, $data['shortname']) . "' 
                                        WHERE id = '" . $data['id'] . "'");
    
    $mysql->query("INSERT INTO fundholders_directory_action_log (fundholders_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteFundholder($id, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM fundholders_directory WHERE id = '" . $id . "'");
    
    $mysql->query("INSERT INTO fundholders_directory_action_log (fundholders_directory_id, user_id, action, datetime) 
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
    case "modalSaveAddFundholderRequest":
        ModalSaveAddFundholder($data, $user_id);
        break;
    case "getEditInfoFundholderInfoRequest":
        GetFundholderInfo($data["id"]);
        break;
    case "modalSaveEditFundholderRequest":
        ModalSaveEditFundholder($data, $user_id);
        break;
    case "deleteFundholderRequest":
        DeleteFundholder($data["id"], $user_id);
        break;
    default:
        break;
}
?>