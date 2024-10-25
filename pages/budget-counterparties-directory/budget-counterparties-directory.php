<?php
session_start();
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/variable_session.php");

function GetCounterpartyData($withArchiveContracts)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();

    $contractStatusCondition = "";
    if (!$withArchiveContracts)
        $contractStatusCondition = " and b.status = 'Відкритий'";


    //запрос на контрагентов с договорами
    $counterparty_data_result = $mysql->query("SELECT distinct(a.id) as counterparty_id, a.name as counterparty_name, b.id as contract_id, b.number as contract_number, 
                                                b.name as contract_name, b.status as contract_status, b.vat_sign as contract_vat_sign, b.term as contract_term, 
                                                if((SELECT count(c.id) FROM renovation_treaty_directory c WHERE c.contracts_directory_id = b.id) > 0, false, true) as contract_edit_mode 
                                                FROM `counterparties_directory` a Left JOIN contracts_directory b ON a.id = b.counterparties_directory_id WHERE b.status is not null " . $contractStatusCondition . " order by a.id"); // WHERE b.status is not null " . $contractStatusCondition . "

    while ($row = $counterparty_data_result->fetch_assoc()) {
        $id = $row["counterparty_id"];
        $count_contracts_data_result = $mysql->query("SELECT count(*) as count FROM `counterparties_directory` a JOIN contracts_directory b ON  a.`id` = b.counterparties_directory_id
                                                            WHERE b.status is not null " . $contractStatusCondition . " and a.id = '" . $id . "'");
        $count_contracts = $count_contracts_data_result->fetch_assoc();

        $check_contract_result = $mysql->query("SELECT if(count(*) > 0, false, true) as sign FROM renovation_treaty_directory WHERE contracts_directory_id = '" . $row["contract_id"] . "'");
        $check_contact =  $check_contract_result->fetch_assoc();

        array_push($dataArray, [
            "counterparty_id" => $row["counterparty_id"],
            "counterparty_name" => $row["counterparty_name"],
            "contract_id" => $row["contract_id"],
            "contract_number" => $row["contract_number"],
            "contract_name" => $row["contract_name"],
            "contract_term" => $row["contract_term"],
            "contract_status" => $row["contract_status"],
            "contract_vat_sign" => $row["contract_vat_sign"],
            "contracts_count" => $count_contracts["count"],
            "conterparty_delete_mode" => $count_contracts["count"],
            "contract_delete_mode" =>  $check_contact["sign"],
            "contract_edit_mode" => $row["contract_edit_mode"] == 0 ? false : true,
        ]);
    }

    //запрос на контрагентов без договоров
    // $counterparty_data_result = $mysql->query("SELECT a.id as counterparty_id, a.name as counterparty_name, 
    // (SELECT count(c.id) FROM planned_indicators c WHERE c.counterparties_directory_id = a.id) as sign_delete_mode 
    // FROM counterparties_directory a WHERE (SELECT if(count(*) > 0, true, false) FROM contracts_directory c WHERE c.counterparties_directory_id = a.id) = false");

    // while ($row = $counterparty_data_result->fetch_assoc()) {
    //     if (isset($row["counterparty_id"]))
    //         array_push($dataArray, [
    //             "counterparty_id" => $row["counterparty_id"],
    //             "counterparty_name" => $row["counterparty_name"],
    //             "contract_id" => null,
    //             "contract_number" => null,
    //             "contract_name" => null,
    //             "contract_term" => null,
    //             "contract_number" => null,
    //             "contract_status" => null,
    //             "contract_vat_sign" => null,
    //             "contracts_count" => null,
    //             "conterparty_delete_mode" => $row["sign_delete_mode"] == 0 ? false : true,
    //             "contract_delete_mode" =>  null,
    //             "contract_edit_mode" => null,
    //         ]);
    // }
    // usort($dataArray, function ($a, $b) {
    //     return $a["counterparty_id"] > $b["counterparty_id"];
    // });
    //возвращается общий массив
    return $dataArray;
}

function CreateTableDOM($director_role, $withArchiveContracts)
{
    $data = GetCounterpartyData($withArchiveContracts);

    $max_count = 0;
    for ($i = 0; $i < count($data); $i++)
        $max_count = max($data[$i]["contracts_count"], $max_count);


    //шапка таблицы
    echo "<table>";
    echo "<thead >";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id sticky-table-column'>№</th>";
    if ($director_role)
        echo "<th class='main-table-th table-column-actions sticky-table-column'><img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'></th>";
    echo "<th class='main-table-th table-column-name sticky-table-column  main-table-th-counterparties'>Назва</th>";
    // if ($director_role) {
    //     echo "<th class='main-table-th table-column-add sticky-table-column'>Створити договір</th>";
    // }

    for ($i = 0; $i < $max_count; $i++)
        echo "<th class='main-table-th main-table-th-contracts'>Інформація договору</th>";
    echo "<th></th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";
    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        //проверка на наличие договоров
        $count = $data[$i]["contracts_count"];
        $sign_disabled = ($data[$i]["contracts_count"] > 0 || $data[$i]["conterparty_delete_mode"]) ? true : false;
        $disabled = $sign_disabled ? "disabled" : "";
        echo "<tr>";
        echo "<td class='table-column-id sticky-table-column'>" . $id . "</td>";
        if ($director_role) {
            echo "<td class='table-column-actions sticky-table-column'>";
            echo "<div class='td-toolbar'>";
            echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input' onclick='modalEditCounterpartyOnClick(" . $data[$i]["counterparty_id"] . ")'/>";
            echo "<br/>";
            echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled  . "' onclick='modalDeleteCounterpartyOnClick(" . $data[$i]["counterparty_id"] . ")' " . $disabled . "/>";
            echo "</div>";
            echo "</td>";
        }

        // print_r($data[$i]);
        // echo "\n";

        echo "<td class='table-column-name sticky-table-column'>";
        echo "<div>" . $data[$i]['counterparty_name'] . "</div>";
        echo "</td>";
        // if ($director_role)
        //     echo "<td class='table-column-add sticky-table-column'><button class='add-contract__button' onclick='modalAddContractOnClick(" . $data[$i]['counterparty_id'] . ")'>Створити</button></td>";

        // var_dump($data[$i]);
        // echo "<br><br>";
        //отрисовка ячеек при наличии договоров
        for ($j = 0; $j < $count; $j++) {
            echo "<td>";
            if ($director_role) {
                echo "<div class='td-toolbar'>";
                $disabled = $data[$i]["contract_edit_mode"] ? "" : "disabled";
                echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input " . $disabled . "' onclick='modalEditContractOnClick(" . $data[$i]["contract_id"] . ")' " . $disabled . "/>";
                $disabled = $data[$i]["contract_delete_mode"] ? "" : "disabled";
                echo "<input type='image' src='../../templates/images/delete.png' alt='delete' class='action__td__input " . $disabled . "' onclick='modalDeleteContractOnClick(" . $data[$i]["contract_id"] . ", " . $data[$i]['contract_edit_mode'] . ")' " . $disabled . "/>";
                echo "</div><hr class='hr__td'>";
            }

            echo "<div class='td-treaty-numbers'>";
            echo "<span class='header_text__td'>Номер: </span> " . $data[$i]["contract_number"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='td-treaty-numbers'>";
            echo "<span class='header_text__td'>Назва: </span> " . $data[$i]["contract_name"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='td-treaty-numbers'>";
            echo "<span class='header_text__td'>Термін дії: </span> " . $data[$i]["contract_term"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='td-treaty-numbers'>";
            echo "<span class='header_text__td'>Статус: </span> " . $data[$i]["contract_status"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='td-treaty-numbers'>";
            echo "<span class='header_text__td'>Ознака ПДВ: </span> " . $data[$i]["contract_vat_sign"];
            echo "</div>";
            if ($j + 1 != $count)
                $i++;
        }

        //отрисовка пустых ячеек при отсутствии договоров
        for ($j = 0; $j < $max_count - $count; $j++)
            echo "<td></td>";
        echo "<td></td>";
        echo "</tr>";
    }
    echo "</tbody>";

    echo "</table>";
}

function GetContractInfo($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM contracts_directory WHERE id = '" . $id . "'");
    $data = $result->fetch_assoc();
    $data_array = json_encode([
        'contract_id' => $data["id"],
        'contract_number' => $data["number"],
        'contract_name' => $data["name"],
        'contract_term' => $data["term"],
        'contract_status' => $data["status"],
        'contract_vat_sign' => $data["vat_sign"],
        'contract_edit_mode' => $data["edit_mode"],
    ]);
    echo $data_array;
    $mysql->close();
}

function GetCounterpartyInfo($id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("SELECT * FROM counterparties_directory WHERE id = '" . $id . "'");
    $data = $result->fetch_assoc();
    $data_array = json_encode([
        'counterparty_id' => $data["id"],
        'counterparty_name' => $data["name"],
    ]);
    echo $data_array;
    $mysql->close();
}

function ModalSaveAddContract($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO contracts_directory (counterparties_directory_id, number, name, status, vat_sign, term, edit_mode) 
                                VALUES ('" . $data['counterpartyId'] . "', '" . mysqli_real_escape_string($mysql, $data['number']) . "', 
                                        '" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                        '" . $data['status'] . "', '" . $data['vatSign'] . "','" . $data['term'] . "', 1)");
    $mysql->query("INSERT INTO contracts_directory_action_log (contracts_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено! \nЗапис с введеним номером договору вже існує. "]);
    $mysql->close();
}

function ModalSaveAddCounterparty($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO counterparties_directory (name) 
                                VALUES ('" . mysqli_real_escape_string($mysql, $data['name']) . "')");
    $mysql->query("INSERT INTO counterparties_directory_action_log (counterparties_directory_id, user_id, action, datetime) 
                                VALUES ('" . mysqli_insert_id($mysql) . "', '" . $user_id . "', 'add', '" . date("Y-m-d H:i:s") . "')");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditContract($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    if ($data["editMode"] == "1") {
        $result = $mysql->query("UPDATE contracts_directory SET number = '" . mysqli_real_escape_string($mysql, $data['number']) . "', 
                                        name = '" . mysqli_real_escape_string($mysql, $data['name']) . "', 
                                        status = '" . $data['status'] . "', vat_sign = '" . $data['vatSign'] . "', term = '" . $data['term'] . "' 
                                        WHERE id = '" . $data['id'] . "' and edit_mode = '1'");

        $mysql->query("INSERT INTO contracts_directory_action_log (contracts_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    } else $result = 0;
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ModalSaveEditCounterparty($data, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("UPDATE counterparties_directory SET name = '" . mysqli_real_escape_string($mysql, $data['name']) . "' 
                                        WHERE id = '" . $data['id'] . "'");

    $mysql->query("INSERT INTO counterparties_directory_action_log (counterparties_directory_id, user_id, action, datetime) 
                                VALUES ('" . $data['id'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteContract($id, $edit_mode, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    if ($edit_mode == "1") {
        $result = $mysql->query("DELETE FROM contracts_directory WHERE id = '" . $id . "'");

        $mysql->query("INSERT INTO contracts_directory_action_log (contracts_directory_id, user_id, action, datetime) 
                                VALUES ('" . $id . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    } else $result = 0;
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function DeleteCounterparty($id, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("DELETE FROM counterparties_directory WHERE id = '" . $id . "'");

    $mysql->query("INSERT INTO counterparties_directory_action_log (counterparties_directory_id, user_id, action, datetime) 
                                VALUES ('" . $id . "', '" . $user_id . "', 'delete', '" . date("Y-m-d H:i:s") . "')");
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function GetCounterparties()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();


    //запрос на контрагентов с договорами
    $counterparty_data_result = $mysql->query("SELECT * FROM `counterparties_directory` ORDER BY `name` ASC"); //  WHERE a.`id` = b.counterparties_directory_id and b.status = 'Відкритий' and exists (SELECT * FROM renovation_treaty_directory c  WHERE c.contracts_directory_id = b.id) order by a.id

    while ($row = $counterparty_data_result->fetch_assoc()) {
        array_push($dataArray, [
            "id" => $row["id"],
            "name" => $row["name"],
        ]);
    }

    //возвращается общий массив

    echo json_encode($dataArray);

    $mysql->close();
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);
switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM($director_role, $data["withArchiveContracts"]); //$data["withArchiveContracts"]
        break;
    case "modalSaveAddContractRequest":
        ModalSaveAddContract($data, $user_id);
        break;
    case "modalSaveAddCounterpartyRequest":
        ModalSaveAddCounterparty($data, $user_id);
        break;
    case "getEditContractInfoRequest":
        GetContractInfo($data["id"]);
        break;
    case "getEditInfoCounterpartyInfoRequest":
        GetCounterpartyInfo($data["id"]);
        break;
    case "modalSaveEditContractRequest":
        ModalSaveEditContract($data, $user_id);
        break;
    case "modalSaveEditCounterpartyRequest":
        ModalSaveEditCounterparty($data, $user_id);
        break;
    case "deleteContractRequest":
        DeleteContract($data["id"], $data["editMode"], $user_id);
        break;
    case "deleteCounterpartyRequest":
        DeleteCounterparty($data["id"], $user_id);
        break;
    case "modalGetCounterpartiesRequest":
        GetCounterparties();
        break;
    default:
        break;
}