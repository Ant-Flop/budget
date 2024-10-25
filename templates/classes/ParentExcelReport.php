<?php

error_reporting(E_ERROR);
require_once('../libs/php-excel/PHPExcel.php');
require_once("../../templates/classes/db_local.php");
define('PATH_TO_FILE', '../files/everyday.xls');

class ParentExcelReport
{
    private $object;
    private $date;
    private $file;
    private $sections = [
        "Інші платежі по операційній діяльності",
        "Інвест. діяльність",
        "Фінансова діяльність",
        "6. Купівля послуг",
    ];
    private $payment_article_data = [];

    function __construct($date)
    {
        $this->file = '../files/everyday.xls';
        $this->date = $date;
        $this->object = PHPExcel_IOFactory::load($this->file);
    }

    function form_condition() {
        $condition = "( ";
        foreach($this->sections as $key => $value) {
            if($key + 1 === count($this->sections))
                $condition =  $condition . " e.name = '" . $value . "' )";
            else {
                $condition =  $condition . " e.name = '" . $value . "' or ";
            }
        }   

        return $condition;
    }

    function payment_article_query() {
        $connect = new DB_connect();
        $mysql = $connect->Connect();
        $mysql->set_charset("utf8");
        $query = "SELECT DISTINCT(b.id), e.name as main_section_name, d.name as section_name, c.name as subsection_name,  a.article_name,
                    f.additional_name as fundholder, f.id as fundholder_id, g.name as service, h.new_code, i.old_code, 
                    SUM(CASE WHEN DAY(l.operDate) = 1 THEN l.operSum ELSE 0 END) AS '01',
                    SUM(CASE WHEN DAY(l.operDate) = 2 THEN l.operSum ELSE 0 END) AS '02',
                    SUM(CASE WHEN DAY(l.operDate) = 3 THEN l.operSum ELSE 0 END) AS '03',
                    SUM(CASE WHEN DAY(l.operDate) = 4 THEN l.operSum ELSE 0 END) AS '04',
                    SUM(CASE WHEN DAY(l.operDate) = 5 THEN l.operSum ELSE 0 END) AS '05',
                    SUM(CASE WHEN DAY(l.operDate) = 6 THEN l.operSum ELSE 0 END) AS '06',
                    SUM(CASE WHEN DAY(l.operDate) = 7 THEN l.operSum ELSE 0 END) AS '07',
                    SUM(CASE WHEN DAY(l.operDate) = 8 THEN l.operSum ELSE 0 END) AS '08',
                    SUM(CASE WHEN DAY(l.operDate) = 9 THEN l.operSum ELSE 0 END) AS '09',
                    SUM(CASE WHEN DAY(l.operDate) = 10 THEN l.operSum ELSE 0 END) AS '10',
                    SUM(CASE WHEN DAY(l.operDate) = 11 THEN l.operSum ELSE 0 END) AS '11',
                    SUM(CASE WHEN DAY(l.operDate) = 12 THEN l.operSum ELSE 0 END) AS '12',
                    SUM(CASE WHEN DAY(l.operDate) = 13 THEN l.operSum ELSE 0 END) AS '13',
                    SUM(CASE WHEN DAY(l.operDate) = 14 THEN l.operSum ELSE 0 END) AS '14',
                    SUM(CASE WHEN DAY(l.operDate) = 15 THEN l.operSum ELSE 0 END) AS '15',
                    SUM(CASE WHEN DAY(l.operDate) = 16 THEN l.operSum ELSE 0 END) AS '16',
                    SUM(CASE WHEN DAY(l.operDate) = 17 THEN l.operSum ELSE 0 END) AS '17',
                    SUM(CASE WHEN DAY(l.operDate) = 18 THEN l.operSum ELSE 0 END) AS '18',
                    SUM(CASE WHEN DAY(l.operDate) = 19 THEN l.operSum ELSE 0 END) AS '19',
                    SUM(CASE WHEN DAY(l.operDate) = 20 THEN l.operSum ELSE 0 END) AS '20',
                    SUM(CASE WHEN DAY(l.operDate) = 21 THEN l.operSum ELSE 0 END) AS '21',
                    SUM(CASE WHEN DAY(l.operDate) = 22 THEN l.operSum ELSE 0 END) AS '22',
                    SUM(CASE WHEN DAY(l.operDate) = 23 THEN l.operSum ELSE 0 END) AS '23',
                    SUM(CASE WHEN DAY(l.operDate) = 24 THEN l.operSum ELSE 0 END) AS '24',
                    SUM(CASE WHEN DAY(l.operDate) = 25 THEN l.operSum ELSE 0 END) AS '25',
                    SUM(CASE WHEN DAY(l.operDate) = 26 THEN l.operSum ELSE 0 END) AS '26',
                    SUM(CASE WHEN DAY(l.operDate) = 27 THEN l.operSum ELSE 0 END) AS '27',
                    SUM(CASE WHEN DAY(l.operDate) = 28 THEN l.operSum ELSE 0 END) AS '28',
                    SUM(CASE WHEN DAY(l.operDate) = 29 THEN l.operSum ELSE 0 END) AS '29',
                    SUM(CASE WHEN DAY(l.operDate) = 30 THEN l.operSum ELSE 0 END) AS '30',
                    SUM(CASE WHEN DAY(l.operDate) = 31 THEN l.operSum ELSE 0 END) AS '31'
                    FROM `planned_indicators` a
                    LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                    LEFT JOIN subsections_directory c ON c.id = b.subsections_directory_id
                    LEFT JOIN sections_directory d ON d.id = c.sections_directory_id
                    LEFT JOIN main_sections_directory e ON e.id = d.main_sections_directory_id
                    LEFT JOIN fundholders_directory f ON f.id = b.fundholders_directory_id
                    LEFT JOIN services_directory g ON g.id = b.services_directory_id
                    LEFT JOIN new_codes_directory h ON h.id = a.new_codes_directory_id
                    LEFT JOIN old_codes_directory i ON i.id = h.old_codes_directory_id
                    INNER JOIN banks_register l ON h.id = l.new_codes_directory_id
                    WHERE l.operDate LIKE '" . ($this->date) . "%' and " . $this->form_condition() . "
                    GROUP BY a.id
                    ORDER BY e.name, d.name, c.name";

        $result = $mysql->query($query);

        $check_main_section = "";

        while($row = $result->fetch_assoc()) {
                array_push($this->payment_article_data, [
                    "main_section" => $row["main_section_name"],
                    "fundholder" => $row["fundholder"],
                    "section" => $row["section_name"],
                    "subsection" => $row["subsection_name"],
                    "article" => $row["article_name"],
                    "01" => $row["01"],
                    "02" => $row["02"],
                    "03" => $row["03"],
                    "04" => $row["04"],
                    "05" => $row["05"],
                    "06" => $row["06"],
                    "07" => $row["07"],
                    "08" => $row["08"],
                    "09" => $row["09"],
                    "10" => $row["10"],
                    "11" => $row["11"],
                    "12" => $row["12"],
                    "13" => $row["13"],
                    "14" => $row["14"],
                    "15" => $row["15"],
                    "16" => $row["16"],
                    "17" => $row["17"],
                    "18" => $row["18"],
                    "19" => $row["19"],
                    "20" => $row["20"],
                    "21" => $row["21"],
                    "22" => $row["22"],
                    "23" => $row["23"],
                    "24" => $row["24"],
                    "25" => $row["25"],
                    "26" => $row["26"],
                    "27" => $row["27"],
                    "28" => $row["28"],
                    "29" => $row["29"],
                    "30" => $row["30"],
                    "31" => $row["31"],
                ]);
        }
        $mysql->close();
    }

    function object_fill() {
        $activeSheet = $this->object->getActiveSheet();
        $this->payment_article_query();

        $set_fact = function ($array, $indexRow) use ($activeSheet) {
            for ($i = 1; $i <= 31; $i++) {
                $activeSheet->getCellByColumnAndRow(6 + $i, $indexRow)->setValue($array[sprintf("%02d", $i)]);
            }
            $cellStart = PHPExcel_Cell::stringFromColumnIndex(7) . $indexRow;
            $cellEnd = PHPExcel_Cell::stringFromColumnIndex(37) . $indexRow;
            $activeSheet->getCellByColumnAndRow(38, $indexRow)->setValue("=SUM(" . $cellStart . ":" . $cellEnd . ")");
        };

        $arrBuff = [];

        // for($i = 1, $sec = [], $subsec = [], $articles = [], $sec_index = 0; $i < count($this->payment_article_data); $i++) {
        //     if($this->payment_article_data[$i]["main_section"] === $this->payment_article_data[$i - 1]["main_section"]) {
        //         if($this->payment_article_data[$i]["section"] !== $this->payment_article_data[$i - 1]["section"]) {
        //             array_push($sec, [ 
        //                 $this->payment_article_data[$i - 1]["section"] => $subsec, 
        //             ]);
        //             $subsec = [];
        //         } else {
        //             if($this->payment_article_data[$i]["subsection"] !== $this->payment_article_data[$i - 1]["subsection"]) {
        //                 array_push($subsec, [ 
        //                     $this->payment_article_data[$i - 1]["subsection"] => $articles, 
        //                 ]);
        //                 $articles = [];
        //             } else {
        //                 array_push($articles, [ 
        //                     "article" => $this->payment_article_data[$i - 1]["article"], 
        //                 ]);
        //             }
        //         }
        //     } else {
        //         array_push($arrBuff, [ 
        //             $this->payment_article_data[$i - 1]["main_section"] => $sec,
        //         ]);
        //         $sec = [];
        //     }
        // }

        //print_r($arrBuff);
    
        for ($i = 0, $indexColumn = 1, $indexRow = 10; $i < count($this->payment_article_data); $i++) {
            if ($this->payment_article_data[$i]["main_section"] === $this->payment_article_data[$i - 1]["main_section"] && $i > 0) {
                if ($this->payment_article_data[$i]["section"] === $this->payment_article_data[$i - 1]["section"]) {
                    if ($this->payment_article_data[$i]["subsection"] === $this->payment_article_data[$i - 1]["subsection"]) {
                        while ($this->payment_article_data[$i]["subsection"] !== $this->payment_article_data[$i - 1]["subsection"]) {
                            $activeSheet->getCellByColumnAndRow(3, $indexRow)->setValue($this->payment_article_data[$i]["article"]);
                            $set_fact($this->payment_article_data[$i], $indexRow++);
                            $i++;
                        }
                    } else {
                        $activeSheet->getCellByColumnAndRow(3, $indexRow++)->setValue($this->payment_article_data[$i]["subsection"]);
                        $activeSheet->getCellByColumnAndRow(3, $indexRow)->setValue($this->payment_article_data[$i]["article"]);
                        $set_fact($this->payment_article_data[$i], $indexRow++);
                    }
                } else {
                    $activeSheet->getCellByColumnAndRow(2, $indexRow++)->setValue($this->payment_article_data[$i]["section"]);
                    $activeSheet->getCellByColumnAndRow(3, $indexRow++)->setValue($this->payment_article_data[$i]["subsection"]);
                    $activeSheet->getCellByColumnAndRow(3, $indexRow)->setValue($this->payment_article_data[$i]["article"]);
                    $set_fact($this->payment_article_data[$i], $indexRow++);
                }
            } else {
                $activeSheet->getCellByColumnAndRow(1, $indexRow++)->setValue($this->payment_article_data[$i]["main_section"]);
                $activeSheet->getCellByColumnAndRow(2, $indexRow++)->setValue($this->payment_article_data[$i]["section"]);
                $activeSheet->getCellByColumnAndRow(3, $indexRow++)->setValue($this->payment_article_data[$i]["subsection"]);
                $activeSheet->getCellByColumnAndRow(3, $indexRow)->setValue($this->payment_article_data[$i]["article"]);
                $set_fact($this->payment_article_data[$i], $indexRow++);
            }
        }
    }

    function object_writer() {
        $this->object_fill();
        $objectWriter = PHPExcel_IOFactory::createWriter($this->object, 'Excel2007');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $this->file. '"');
        header('Cache-Control: max-age=0');
        $objectWriter->save('php://output');
        exit();
    }
}


$object = new ParentExcelReport("2022-01");
$object->object_writer();





?>