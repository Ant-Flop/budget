<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetCodesData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();

    $old_codes_data_result = $mysql->query("SELECT * FROM old_codes_directory ORDER BY id ASC"); 

    while ($row_data_old_code = $old_codes_data_result->fetch_assoc()) {
        $new_codes_data_result = $mysql->query("SELECT a.id, a.old_codes_directory_id, a.new_code, 
                                                    if(
                                                        (SELECT count(*) FROM budget_articles_directory b WHERE b.new_codes_directory_id = a.id ) ||
                                                        (SELECT count(*) FROM main_sections_directory c WHERE c.new_codes_directory_id = a.id ) ||
                                                        (SELECT count(*) FROM sections_directory d WHERE d.new_codes_directory_id = a.id ) ||
                                                        (SELECT count(*) FROM subsections_directory e WHERE e.new_codes_directory_id = a.id ) > 0, false, true) as delete_mode
                                                FROM new_codes_directory a 
                                                WHERE old_codes_directory_id = '" . $row_data_old_code['id'] . "' ORDER BY a.new_code");
        $new_codes_array = array();
        while($row_data_new_code = $new_codes_data_result->fetch_assoc()) {
            array_push($new_codes_array, [
                "id" => $row_data_new_code["id"],
                "old_code_id" => $row_data_new_code["old_codes_directory_id"],
                "new_code" => $row_data_new_code["new_code"],
                "delete_mode" => $row_data_new_code["delete_mode"],
            ]);
        }
        if(count($new_codes_array) == 0)
            array_push($new_codes_array, [
                "id" => null,
                "old_code_id" => null,
                "new_code" => null,
                "delete_mode" => null,
            ]);
        array_push($dataArray, [
            "id" => $row_data_old_code['id'],
            "old_code" => $row_data_old_code['old_code'],
            "new_codes_array" => $new_codes_array,
        ]);
    }

    //возвращается общий массив
    return $dataArray;
}

function CreateTableDOM($admin_role)
{   
    $data = GetCodesData();

    //шапка таблицы
    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id sticky-table-column'>№</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-old-code sticky-table-column  main-table-th-counterparties'>Старий код</th>";
    echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-new-code sticky-table-column'>Новий код</th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";
    // //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        $rowspan = count($data[$i]["new_codes_array"]);
        $rowspan__td = $rowspan > 1 && $i + 1 == count($data) ? "rowspan__td" : "";
        $align__td =  $rowspan > 1 ? "align-top__td" : "";
        $disabled = $rowspan >= 1 && $data[$i]["new_codes_array"][0]["id"] != ( null || 0) ? "disabled" : "";
        echo "<tr class='table-old-code__tr'>";
        echo "<td class='table-column-id sticky-table-column " . $rowspan__td . " " . $align__td . "' rowspan='" . $rowspan . "'>" . $id . "</td>";
        echo "<td class='table-column-actions sticky-table-column " . $rowspan__td . " " . $align__td . "' rowspan='" . $rowspan . "'>";
        echo "<div class='td-toolbar'>";
        echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditOldCodeOnClick(" . $data[$i]["id"] . ")'/>";
        echo $admin_role == true ? "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteOldCodeOnClick(" . $data[$i]["id"] . ")' " . $disabled . "/>" : "";
        echo "</div>";
        echo "</td>";
        echo "<td class='table-column-old-code sticky-table-column " . $rowspan__td . " " . $align__td . "'  rowspan='" . $rowspan . "'>"; 
        echo "<div>" . $data[$i]['old_code'] == ( 0 || null ) ? "—" : $data[$i]['old_code'] . "</div>";
        echo "</td>";
        for ($j = 0; $j < $rowspan; $j++) {
            $disabled = "";
            echo $j > 0 ? "<tr>": "";
            echo "<td class='table-column-actions'>";
            echo "<div class='td-toolbar'>";
            echo $data[$i]['new_codes_array'][$j]['id'] != null ? "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input " . $disabled . "' onclick='modalEditNewCodeOnClick(" . $data[$i]['new_codes_array'][$j]['id'] . ")' " . $disabled . "/>" : "—";
            $disabled = $data[$i]['new_codes_array'][$j]["delete_mode"] == 0 ? "disabled" : ""; 
            echo $data[$i]['new_codes_array'][$j]['id'] != null && $admin_role ? "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled . "' onclick='modalDeleteNewCodeOnClick(" . $data[$i]['new_codes_array'][$j]['id'] . ")' " . $disabled . "/>" : "";
            echo "</td>";
            echo "<td class='table-column-old-code sticky-table-column'>"; 
            echo $data[$i]['new_codes_array'][$j]['id'] != null ? "<div>" . $data[$i]['new_codes_array'][$j]['new_code'] . "</div>" : "—";
            echo "</td>";
            echo "</tr>";
        }
        
    }
    echo "</tbody>";
    echo "</table>";
}

function GetOldCodesData() {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM old_codes_directory");
    while($data = $result->fetch_assoc()) {
        array_push($data_array, [
            'id' => $data["id"],
            'old_code' => $data["old_code"],
        ]);
    }
    echo json_encode($data_array);
    $mysql->close();
}

function GetOldCodeInfo($id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM old_codes_directory WHERE id = '" . $id . "'");
    $data = $result->fetch_assoc();
    $data_array = json_encode([
        'id' => $data["id"],
        'old_code' => $data["old_code"],
    ]);
    echo $data_array;
    $mysql->close();
}

function GetNewCodeInfo($id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM new_codes_directory WHERE id = '" . $id . "'");
    $data = $result->fetch_assoc();
    $data_array = json_encode([
        'id' => $data["id"],
        'old_code_id' => $data["old_codes_directory_id"],
        'new_code' => $data["new_code"],
    ]);
    echo $data_array;
    $mysql->close();
}

function ModalSaveAddOldCode($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO old_codes_directory (old_code) 
                                VALUES ('" . $data['oldCode'] . "')");
    $mysql->query("INSERT INTO old_codes_directory_action_log (old_codes_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveAddNewCode($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO new_codes_directory (old_codes_directory_id, new_code, delete_mode) 
                                VALUES ('" . $data['oldCodeId'] . "', '" . $data['newCode'] . "', 1)");
    $mysql->query("INSERT INTO new_codes_directory_action_log (new_codes_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditOldCode($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE old_codes_directory SET old_code = '" . $data['oldCode'] . "' 
                                        WHERE id = '" . $data['id'] . "'");
    
    $mysql->query("INSERT INTO old_codes_directory_action_log (old_codes_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditNewCode($data, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE new_codes_directory SET new_code = '" . $data['newCode'] . "', old_codes_directory_id = '" . $data['oldCodeId'] . "' 
                                        WHERE id = '" . $data['id'] . "'");
    
    $mysql->query("INSERT INTO new_codes_directory_action_log (new_codes_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteOldCode($id, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM old_codes_directory WHERE id = '" . $id . "'");
    
    $mysql->query("INSERT INTO old_codes_directory_action_log (old_codes_directory_id, user_id, action, datetime) 
                                VALUES ('" . $id . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteNewCode($id, $user_id) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM new_codes_directory WHERE id = '" . $id . "'");
    
    $mysql->query("INSERT INTO new_codes_directory_action_log (new_codes_directory_id, user_id, action, datetime) 
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
    case "modalSaveAddOldCodeRequest":
        ModalSaveAddOldCode($data, $user_id);
        break;
    case "getOldCodesRequest":
        GetOldCodesData();
        break;
    case "modalSaveAddNewCodeRequest":
        ModalSaveAddNewCode($data, $user_id);
        break;
    case "getEditInfoOldCodeInfoRequest":
        GetOldCodeInfo($data["id"]);
        break;
    case "getEditInfoNewCodeInfoRequest":
        GetNewCodeInfo($data["id"]);
        break;
    case "modalSaveEditOldCodeRequest":
        ModalSaveEditOldCode($data, $user_id);
        break;
    case "modalSaveEditNewCodeRequest":
        ModalSaveEditNewCode($data, $user_id);
        break;
    case "deleteOldCodeRequest":
        DeleteOldCode($data["id"], $user_id);
        break;
    case "deleteNewCodeRequest":
        DeleteNewCode($data["id"], $user_id);
        break;
    default:
        break;
}
?>