<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetServicesData () {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $result = $mysql->query("SELECT a.id, a.name, b.id as fundholder_id, b.additional_name,
                             if((SELECT count(*) FROM budget_articles_directory c WHERE c.services_directory_id = a.id) > 0, false, true) as delete_mode
                             FROM services_directory a
                             LEFT JOIN fundholders_directory b ON b.id = a.fundholders_directory_id");
    while($row = $result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row["id"],
            "name" => $row["name"],
            "fundholder_id" => $row["fundholder_id"],
            "fundholder" => $row["additional_name"],
            "delete_mode" => $row["delete_mode"],
        ]);
    }
    $mysql->close();
    return $dataArray;
}

function CreateTableDOM ($admin_role) {
    $data = GetServicesData();

    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id'>№</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-fundholder'>Фондоутримувач</th>";
    echo "<th class='main-table-th table-column-name'>Назва служби</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";

    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        $disabled = $data[$i]["delete_mode"] ? "" : "disabled";
        echo "<tr>";
        echo "<td class='table-column-id'>" . $id . "</td>";
        echo "<td class='table-column-actions sticky-table-column'>";
        echo "<div class='td-toolbar'>";
        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditServiceOnClick(" . $data[$i]["id"] . ")'/>";
        if($admin_role === true)
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteServiceOnClick(" . $data[$i]["id"] . ")' " . $disabled . "/>";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-fundholder'>"; 
        echo $data[$i]['fundholder'];
        echo "</td>";
        echo "<td class='table-column-name'>"; 
        echo $data[$i]['name'];
        echo "</td>";
    }
    echo "</tbody>";

    echo "</table>";
}

function GetFundholdersInfo () {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    $result = $mysql->query("SELECT id, additional_name FROM fundholders_directory");
    while($row = $result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row["id"],
            "name" => $row["additional_name"],
        ]);
    }
    $mysql->close();
    echo json_encode($dataArray);
}

function ModalSaveAddService ($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO services_directory (fundholders_directory_id, name) 
                                VALUES ('" . $data['fundholderId'] . "', '" . mysqli_real_escape_string($mysql, $data['service']) . "')");
    $mysql->query("UPDATE fundholders_directory SET delete_mode = 0 WHERE id = '" . $data['fundholderId'] . "'");
    $mysql->query("INSERT INTO services_directory_action_log (services_directory_id	, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function GetServiceInfo ($id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT id, name FROM services_directory WHERE id = '" . $id . "'");
    $row = $result->fetch_assoc();
    $mysql->close();
    echo json_encode([
        "id" => $row["id"],
        "name" => $row["name"]
    ]);
}

function ModalSaveEditService($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE services_directory SET name = '" . mysqli_real_escape_string($mysql, $data['name']) . "'
                                        WHERE id = '" . $data['id'] . "'");
    $mysql->query("INSERT INTO services_directory_action_log (services_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteService($id, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM services_directory WHERE id = '" . $id . "'");
    
    $mysql->query("INSERT INTO services_directory_action_log (services_directory_id, user_id, action, datetime) 
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
    case "getAddFundholdersRequest":
        GetFundholdersInfo();
        break;
    case "modalSaveAddServiceRequest":
        ModalSaveAddService($data, $user_id);
        break;
    case "getEditServiceInfoRequest":
        GetServiceInfo($data["id"]);
        break;
    case "modalSaveEditServiceRequest":
        ModalSaveEditService($data, $user_id);
        break;
    case "deleteServiceRequest":
        DeleteService($data["id"], $user_id);
        break;
    default:
        break;
}
?>