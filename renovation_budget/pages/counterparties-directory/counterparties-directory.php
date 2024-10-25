<?php
require_once("../../../templates/classes/db_local.php");

function GetCounterpartyData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $dataArray = array();
    

    //запрос на контрагентов с договорами
    $counterparty_data_result = $mysql->query("SELECT distinct(a.id) as counterparty_id, a.name as counterparty_name, 
                                                (SELECT count(e.id) FROM contracts_directory e WHERE e.counterparties_directory_id = a.id and e.status = 'Відкритий') as count_contracts
                                                    FROM `counterparties_directory` a 
                                                    LEFT JOIN contracts_directory b ON a.id = b.counterparties_directory_id
                                                    LEFT JOIN planned_indicators c ON c.counterparties_directory_id = a.id
                                                    LEFT JOIN budget_articles_directory d ON d.id = c.budget_articles_directory_id
                                                        WHERE (d.services_directory_id = 10 or d.services_directory_id = 11) and b.status = 'Відкритий' 
                                                        and (d.subsections_directory_id = 51 or d.subsections_directory_id = 53)");//  WHERE a.`id` = b.counterparties_directory_id and b.status = 'Відкритий' and exists (SELECT * FROM renovation_treaty_directory c  WHERE c.contracts_directory_id = b.id) order by a.id

    while ($row = $counterparty_data_result->fetch_assoc()) {
        $contract_array = array();
        $count_contracts_data_result = $mysql->query("SELECT distinct(b.id) as contract_id, b.name as contract_name, b.term, a.id as counterparty_id, a.name as counterparty_name, b.number, b.status, b.vat_sign FROM `counterparties_directory` a 
                                                        LEFT JOIN contracts_directory b ON a.id = b.counterparties_directory_id 
                                                        LEFT JOIN planned_indicators c ON c.counterparties_directory_id = a.id 
                                                        LEFT JOIN budget_articles_directory d ON d.id = c.budget_articles_directory_id 
                                                        WHERE a.id = '" . $row["counterparty_id"] . "' and b.status = 'Відкритий'  order by a.id");
        while($sub_row = $count_contracts_data_result->fetch_assoc()) {
            array_push($contract_array, [
                "contract_id" => $sub_row["contract_id"],
                "contract_number" => $sub_row["number"],
                "contract_name" => $sub_row["contract_name"],
                "contract_term" => $sub_row["term"],
                "status" => $sub_row["status"],
                "sign" => $sub_row["vat_sign"],
            ]);
        }
        array_push($dataArray, [
            "counterparty_id" => $row["counterparty_id"],
            "counterparty_name" => $row["counterparty_name"],
            "count_contracts" => $row["count_contracts"],
            "contracts_data" => $contract_array,
        ]);
            
    }

    //возвращается общий массив
    
    return $dataArray;
}

function CreateTableDOM()
{
    $data = GetCounterpartyData();


    $max_count = 0;
    for ($i = 0; $i < count($data); $i++){
        $max_count = max($data[$i]["count_contracts"], $max_count);
    }
    //шапка таблицы
    echo "<table>";
    echo "<thead class='unselectable'>";
    echo "<tr>";
    echo "<th class='main-table-th table-column-id sticky-table-column'>№</th>";
    
    echo "<th class='main-table-th table-column-name sticky-table-column  main-table-th-counterparties'>Назва</th>";
    echo "<th class='main-table-th table-column-add sticky-table-column'>Додати договір</th>";
    for ($i = 0; $i < $max_count; $i++)
        echo "<th class='main-table-th main-table-th-contracts'>Інформація договору</th>";
    echo "<th></th>";
    echo "</tr>";
    echo "</thead>";
    echo "</tbody>";
    //отрисовка остальной таблицы
    for ($i = 0, $id = 1; $i < count($data); $i++, $id++) {
        //print_r($data[$i]); echo "<br/>";echo "<br/>";
        echo "<tr>";
        echo "<td class='table-column-id sticky-table-column'>" . $id . "</td>";
        
        echo "<td class='table-column-name sticky-table-column'>" . $data[$i]['counterparty_name'] . "</td>";
        echo "<td class='table-column-add sticky-table-column'><input type='image' src='../../templates/images/add.png' alt='add' class='td-add-image' onclick='addEntryOnClick(" . $data[$i]['counterparty_id'] . ")'/></td>";
        //проверка на наличие договоров
        if (isset($data[$i]["count_contracts"]))
            $count = $data[$i]["count_contracts"];
        else $count = 0;
        

        //отрисовка ячеек при наличии договоров 
        for ($j = 0; $j < $count; $j++) {
            echo "<td>";
            echo "<div class='font-treaty-numbers'>";
            echo "<span class='header_text__td'>Номер: </span> " . $data[$i]["contracts_data"][$j]["contract_number"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='font-treaty-numbers'>";
            echo "<span class='header_text__td'>Назва: </span> " . $data[$i]["contracts_data"][$j]["contract_name"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='font-treaty-numbers'>";
            echo "<span class='header_text__td'>Термін дії: </span> " . $data[$i]["contracts_data"][$j]["contract_term"];
            echo "</div><hr class='hr__td'>";
            echo "<div class='select-form'>";
            echo "Статус: ";
            echo "<select class='status__select' onchange='changeStatusOnClick(this.value, " . $data[$i]["contracts_data"][$j]['contract_id'] . ")'>";
            echo "<option selected hidden>" . $data[$i]["contracts_data"][$j]["status"] . "</option>";
            echo "<option value='Відкритий'>Відкритий</option>";
            echo "<option value='Закритий'>Закритий</option>";
            echo "</select><br/>";
            echo "Ознака ПДВ: ";
            echo "<select class='sign__select' onchange='changeSignOnClick(this.value, " . $data[$i]["contracts_data"][$j]['contract_id'] . ")'>";
            echo "<option selected hidden>"; 
            echo $data[$i]["contracts_data"][$j]["sign"] == '' ? 'Обрати': $data[$i]["contracts_data"][$j]["sign"];
            echo "</option>";
            echo "<option value='З ПДВ'>З ПДВ</option>";
            echo "<option value='Без ПДВ'>Без ПДВ</option>";
            echo "</select>";
            echo "</div>";
            // if ($j + 1 != $count)
            //     $i++;
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

function modalSaveAddContract($data) {
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = $mysql->query("INSERT INTO contracts_directory (counterparties_directory_id, number, status, vat_sign, name, term, edit_mode) 
                             VALUES ('" . $data['counterpartyId'] . "', '" . $data['numberContract'] . "', '" . $data['statusContract'] . "', 
                             '" . $data['signVATContract'] . "', '" . $data['nameContract'] . "','" . $data['termContract'] . "', 1)");
    if($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function ChangeStatusContract($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $status = $data['status'];
    $id = $data['id'];
    $mysql->query("UPDATE `contracts_directory` SET `status` = '$status' WHERE `id` = '$id'");
    $mysql->close();
}

function ChangeSignContract($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $sign = $data['sign'];
    $id = $data['id'];
    $mysql->query("UPDATE `contracts_directory` SET `vat_sign` = '$sign' WHERE `id` = '$id'");
    $mysql->close();
}




$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "renderTableRequest":
        CreateTableDOM();
        break;
    case "changeStatusRequest":
        ChangeStatusContract($data);
        break;
    case "modalSaveAddContractRequest":
        modalSaveAddContract($data);
        break;
    case "changeSignRequest":
        ChangeSignContract($data);
        break;
    default:
        break;
}