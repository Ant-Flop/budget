<?php
require_once("../../templates/classes/db_local.php");

class DB_Finansist
{
    private function ConnectToFinansist()
    {
        try {
            $conn = new PDO("sqlsrv:Server=sql-x125-new;Database=bar", "site", "5867sM393ms93in5w8");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die(print_r($e->getMessage()));
        }
        return $conn;
    }

    private function GetConditionIBAN()
    {
        $connect = new DB_connect();
        $mysql = $connect->Connect();
        $result = $mysql->query("SELECT iban FROM banks_directory");
        $IBAN_condition = '';
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            if ($i == 0)
                $IBAN_condition = "sa.NameIBAN" . " = '" . $row['iban'] . "'";
            else $IBAN_condition = $IBAN_condition . " or " . "sa.NameIBAN" . " = '" . $row['iban'] . "'";
            $i++;
        }
        $mysql->close();
        return $IBAN_condition;
    }

    private function ParseStringOnPDV($string)
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

    public function BankRegisterData($data)
    {
        $current_date = $data["date"];
        $connection = $this->ConnectToFinansist();
        $query = "DECLARE @BegDate DATETIME
                  SET @BegDate = CONVERT(DATETIME, '$current_date', 102)
                  
                  DECLARE @EndDate DATETIME
                  SET @EndDate = CONVERT(DATETIME, '$current_date', 102)
                  
                  
                  
                  SELECT
                     BankOperations.ID AS operID
                    ,BankOperations.OperNum as operNumber -- Номер операции
                    ,BankOperations.OperDate as operDate -- Дата операции
                    ,BankOperations.OperSummaOut as operOutputSum -- Сумма исходящей операции
                    ,BankOperations.RecipientID as recipientID -- ID получателя
                    ,BankOperations.RecipientName as recipientName -- Название получателя из справочника
                    ,BankOperations.RecipientCodeOKPO as recipientCodeOKPO -- Код ЄДРПОУ получателя
                    ,BankOperations.RecipientAccountID as recipientAccID -- Код счета получателя
                    ,BankOperations.RecipientAccount as recipientAcc -- Счет получателя (формат НБУ)
                    ,BankOperations.RecipientAccountIBAN as recipientAccIBAN -- Счет получателя (формат IBAN)
                    ,BankOperations.RecipientBank as recipientBank -- Название банка получателя
                    ,BankOperations.RecipientMFO as recipientMFO -- МФО банка получателя
                    ,BankOperations.Comments as paymentType -- Комментарий (назначение платежа)/распарсить на признак ПДВ
                  
                   ,BankOperations.AccountID AS bankAccID -- ID банковского счета
                   ,sa.Name AS ACC_NUMB -- Номер банковского счета (формат НБУ)
                   ,sa.NameIBAN AS ACC_NUMB_IBAN -- Номер банковского счета (формат IBAN)
                   ,sa.BankName AS BANK_NAME-- Название банка
                   ,BankOperations.OperSummaIn AS operInputSum -- Сумма входящей операции
                   ,BankOperations.OperYear AS operYear -- год операции
                   
                   
                   ,BankOperations.PurposeID -- !+
                   
                   ,BankOperations.RecipientNameFromClientBank AS recipientNameFromExtract -- Название получателя из выписки клиент-банка
                   
                   ,BankOperations.oprID -- !+
                   ,BankOperations.iRules -- !+
                   ,BankOperations.IsGen  -- !+
                   ,BankOperations.ContractId -- ID договора !+
                   ,BankOperations.ContractNo -- № договора !+
                   ,BankOperations.InvoiceId -- ID счета !+
                   ,BankOperations.InvoiceNum -- № счета !+
                   ,BankOperations.IdOrder -- !+
                   ,Spr_Purpose.ctName AS Purpose -- Целевое назначение платежа !+
                   ,TVatDoc.IdTaskDoc -- !+
                   ,CASE
                      WHEN TVatDoc.IdTaskDoc IS NULL THEN 0
                      ELSE 1
                    END AS HasVatDoc -- Есть ли налоговая накладная -- !+
                  
                  FROM (BankOperations -- Реестр банковских операций
                    LEFT JOIN Spr_Purpose -- Справочник целевых назначений платежа
                      ON BankOperations.PurposeID = Spr_Purpose.ctCode
                    )
                  LEFT JOIN (SELECT IdTaskDoc
                    FROM VatDoc -- Реестр налоговых накладных
                    WITH (INDEX (IX_VatDoc_Task)) 
                    WHERE VatDoc.task = 'Bank'
                    GROUP BY IdTaskDoc
                  ) AS TVatDoc
                    ON BankOperations.ID = TVatDoc.IdTaskDoc
                  JOIN Spr_Account AS sa -- Справочник банковских счетов
                    ON BankOperations.AccountID = sa.ID
                  WHERE (1 > 0)
                  --AND BankOperations.AccountId = @AccountId -- Задаем ID банк. счета получателя
                  AND BankOperations.OperDate >= @BegDate
                  AND BankOperations.OperDate <= @EndDate
                  AND BankOperations.OperSummaOut != 0
                  AND (" . $this->GetConditionIBAN() . ")
                  ORDER BY BankOperations.AccountID, BankOperations.ID";
        echo $query;
        $getResults = $connection->prepare($query);
        $getResults->execute();
        $results = $getResults->fetchAll(PDO::FETCH_BOTH);
        $data = array();
        foreach ($results as $row) {
            array_push($data, [
                "operID" => $row['operID'],
                "operNumber" => $row['operNumber'],
                "operDate" => $row['operDate'],
                "operOutputSum" => $row['operOutputSum'],
                "recipientID" => $row['recipientID'] == '' ? 0 : $row['recipientID'],
                "recipientName" => $row['recipientName'],
                "recipientCodeOKPO" => $row['recipientCodeOKPO'],
                "recipientAccID" => $row['recipientAccID'] == '' ? 0 : $row['recipientAccID'],
                "recipientAcc" => $row['recipientAcc'],
                "recipientAccIBAN" => $row['recipientAccIBAN'],
                "recipientBank" => $row['recipientBank'],
                "recipientMFO" => $row['recipientMFO'],
                "paymentType" => $row['paymentType'],
                "bankAccID" => $row['bankAccID'],
                "ACC_NUMB" => $row['ACC_NUMB'],
                "ACC_NUMB_IBAN" => $row['ACC_NUMB_IBAN'],
                "BANK_MFO" => $this->GetBankMFO($row['ACC_NUMB_IBAN']),
                "BANK_NAME" => $row['BANK_NAME'],
                "operInputSum" => $row['operInputSum'], //$this->ParseStringOnPDV()
                "operYear" => $row['operYear'],
                "recipientNameFromExtract" => $row['recipientNameFromExtract'],
                "paymentType" => $row['paymentType'],
            ]);
        }
        return $data;
    }


    private function GetBankMFO($iban)
    {
        $connect = new DB_connect();
        $mysql = $connect->Connect();
        if (!$mysql->set_charset("utf8")) {
            printf("Ошибка при загрузке набора символов utf8: %s\n", $mysql->error);
            exit();
        }
        $result = $mysql->query("SELECT mfo FROM banks_directory WHERE iban = '$iban'");
        $row = $result->fetch_assoc();
        $mysql->close();
        return $row['mfo'];
    }



    public function UpdateBanksRegister($data)
    {
        $connect = new DB_connect();
        $mysql = $connect->Connect();
        $mysql->set_charset("utf8");
        $bank_register_data = $this->BankRegisterData($data);
        foreach ($bank_register_data as $key => $row) {
            $result = $mysql->query("SELECT if((SELECT count(*) FROM banks_register WHERE operID = '" . $row['operID'] . "') > 0, true, false ) as cheker_exist");
            $checker_exist = $result->fetch_assoc(); // проверка на существование записи
            if (!$checker_exist["cheker_exist"]) {
                $mysql->query("INSERT INTO banks_register (operID, operNumber, operDate, operSum, recipientID, recipientName, 
                                            recipientCodeOKPO, recipientAccID, recipientAcc, recipientAccIBAN, 
                                            recipientBank, recipientMFO, paymentType, bankAccID, ACC_NUMB, ACC_NUMB_IBAN, 
                                            BANK_NAME, BANK_MFO, operInputSum, operYear, recipientNameFromExtract) 
                                VALUES ('" . $row['operID'] . "', '" . $row['operNumber'] . "', '" . $row['operDate'] . "', '" . $row['operOutputSum'] . "', 
                                '" . $row['recipientID'] . "', '" . mysqli_real_escape_string($mysql, $row['recipientName']) . "', '" . $row['recipientCodeOKPO'] . "', '" . $row['recipientAccID'] . "', 
                                '" . $row['recipientAcc'] . "', '" . $row['recipientAccIBAN'] . "', '" . $row['recipientBank'] . "', '" . $row['recipientMFO'] . "', 
                                '" . mysqli_real_escape_string($mysql, $row['paymentType']) . "', '" . $row['bankAccID'] . "', '" . $row['ACC_NUMB'] . "', '" . mysqli_real_escape_string($mysql, $row['ACC_NUMB_IBAN']) . "', 
                                '" . mysqli_real_escape_string($mysql, $row['BANK_NAME']) . "', '" . $row['BANK_MFO'] . "', '" . $row['operInputSum'] . "', '" . $row['operYear'] . "', 
                                '" . mysqli_real_escape_string($mysql, $row['recipientNameFromExtract']) . "')");
            }
        }
        $mysql->close();
    }
}