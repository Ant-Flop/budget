<?php
require_once("../../templates/classes/db_local.php");
require_once("../../sessions_api/check_session.php");


function GetBudgetData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array(); // rac.conducted = 1

    $result = $mysql->query("SELECT distinct(a.id)  as p_i_id , a.article_name, a.datetime, c.name as subsection_name, d.name as section_name, e.name as main_section_name, 
                                    f.additional_name as fundholder, f.id as fundholder_id, g.name as service, h.new_code, i.old_code, GROUP_CONCAT(k.name SEPARATOR ', ') as counterparty, 
                                    l.`01_with_vat` as l_01_with_vat, l.`01_no_vat` as l_01_no_vat, l.`02_with_vat` as l_02_with_vat, 
                                    l.`02_no_vat` as l_02_no_vat, l.`03_with_vat` as l_03_with_vat, l.`03_no_vat` as l_03_no_vat, l.`04_with_vat` as l_04_with_vat, 
                                    l.`04_no_vat` as l_04_no_vat, l.`05_with_vat` as l_05_with_vat, l.`05_no_vat` as l_05_no_vat, l.`06_with_vat` as l_06_with_vat, 
                                    l.`06_no_vat` as l_06_no_vat, l.`07_with_vat` as l_07_with_vat, l.`07_no_vat` as l_07_no_vat, l.`08_with_vat` as l_08_with_vat, 
                                    l.`08_no_vat` as l_08_no_vat, l.`09_with_vat` as l_09_with_vat, l.`09_no_vat` as l_09_no_vat, l.`10_with_vat` as l_10_with_vat, 
                                    l.`10_no_vat` as l_10_no_vat, l.`11_with_vat` as l_11_with_vat, l.`11_no_vat` as l_11_no_vat, l.`12_with_vat` as l_12_with_vat, 
                                    l.`12_no_vat` as l_12_no_vat, l.`sum_with_vat` as l_sum_with_vat, l.`sum_no_vat` as l_sum_no_vat, l.`edited` as l_edited, l.`edit_mode` as l_edit_mode,
                                    m.`01_with_vat` as m_01_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE n.article_name = a.article_name and SUBSTRING(n.act_date, 6, 2) = '01') / 1000, m.`01_no_vat`) as m_01_no_vat, 
                                    m.`02_with_vat` as m_02_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '02') / 1000, m.`02_no_vat`) as m_02_no_vat, 
                                    m.`03_with_vat` as m_03_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '03') / 1000, m.`03_no_vat`) as m_03_no_vat, 
                                    m.`04_with_vat` as m_04_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '04') / 1000, m.`04_no_vat`) as m_04_no_vat, 
                                    m.`05_with_vat` as m_05_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '5') / 1000, m.`05_no_vat`) as m_05_no_vat, 
                                    m.`06_with_vat` as m_06_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '6') / 1000, m.`06_no_vat`) as m_06_no_vat, 
                                    m.`07_with_vat` as m_07_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '7') / 1000, m.`07_no_vat`) as m_07_no_vat, 
                                    m.`08_with_vat` as m_08_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '8') / 1000, m.`08_no_vat`) as m_08_no_vat, 
                                    m.`09_with_vat` as m_09_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '9') / 1000, m.`09_no_vat`) as m_09_no_vat, 
                                    m.`10_with_vat` as m_10_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE rac.conducted = 1 and n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '10') / 1000, m.`10_no_vat`) as m_10_no_vat, 
                                    m.`11_with_vat` as m_11_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '11') / 1000, m.`11_no_vat`) as m_11_no_vat, 
                                    m.`12_with_vat` as m_12_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year and SUBSTRING(n.act_date, 6, 2) = '12') / 1000, m.`12_no_vat`) as m_12_no_vat, 
                                    m.`sum_with_vat` as m_sum_with_vat, 
                                    if(f.id = 7, (SELECT sum((n.price_of_service_no_pdv + n.cost_of_materials_no_pdv) * n.amount) as sum 
                                                  FROM renovation_treaty_act_card n
                                                  LEFT JOIN renovation_act_card rac ON rac.id = n.renovation_act_card_id
                                                  WHERE  n.article_name = a.article_name and SUBSTRING(n.act_date, 1, 4) = a.year) / 1000, m.`sum_no_vat`) as m_sum_no_vat, 
                                    m.`edited` as m_edited, m.`edit_mode` as m_edit_mode, n.*, o.set_after_transfer_mode
                                    FROM `planned_indicators` a
                                    LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id
                                    LEFT JOIN subsections_directory c ON c.id = b.subsections_directory_id
                                    LEFT JOIN sections_directory d ON d.id = c.sections_directory_id
                                    LEFT JOIN main_sections_directory e ON e.id = d.main_sections_directory_id
                                    LEFT JOIN fundholders_directory f ON f.id = b.fundholders_directory_id
                                    LEFT JOIN services_directory g ON g.id = b.services_directory_id
                                    LEFT JOIN new_codes_directory h ON h.id = a.new_codes_directory_id
                                    LEFT JOIN old_codes_directory i ON i.id = h.old_codes_directory_id
                                    LEFT JOIN planned_indicators_counterparties pic ON pic.planned_indicators_id = a.id 
                                    LEFT JOIN counterparties_directory k ON k.id = pic.counterparties_directory_id
                                    LEFT JOIN planned_indicators_amounts_implementation l ON l.planned_indicators_id = a.id
                                    LEFT JOIN fact_indicators_amounts m ON m.planned_indicators_id = a.id 
                                    LEFT JOIN planned_indicators_amounts_vat_sign n ON n.planned_indicators_id = a.id
                                    LEFT JOIN planned_indicators_amounts o ON o.planned_indicators_id = a.id
                             WHERE f.id = '" . $data['fundholderId'] . "' and a.year = '" . $data['year'] . "' GROUP BY a.id
        ORDER BY IF(e.name LIKE 'Інвест. діяльність', c.name, a.id)");

    while ($row = $result->fetch_assoc()) {
        $exc_vat = $row["section_name"] == "6.3 Послуги підрядних організацій" ? true : false;
        array_push($data_array, [
            "id" => $row["p_i_id"],
            "new_code" => $row["new_code"],
            "main_section" => $row["main_section_name"],
            "section" => $row["section_name"],
            "subsection" => $row["subsection_name"],
            "fundholder" => $row["fundholder"],
            "fundholder_id" => $row["fundholder_id"],
            "service" => $row["service"],
            "article" => $row["article_name"],
            "counterparty" => $row["counterparty"],
            "set_after_transfer_mode" => $row["set_after_transfer_mode"],
            "planned_indicators_amounts" => [
                "01_with_vat" => $row["l_01_with_vat"],
                "01_no_vat" => $row["l_01_no_vat"],
                "02_with_vat" => $row["l_02_with_vat"],
                "02_no_vat" => $row["l_02_no_vat"],
                "03_with_vat" => $row["l_03_with_vat"],
                "03_no_vat" => $row["l_03_no_vat"],
                "04_with_vat" => $row["l_04_with_vat"],
                "04_no_vat" => $row["l_04_no_vat"],
                "05_with_vat" => $row["l_05_with_vat"],
                "05_no_vat" => $row["l_05_no_vat"],
                "06_with_vat" => $row["l_06_with_vat"],
                "06_no_vat" => $row["l_06_no_vat"],
                "07_with_vat" => $row["l_07_with_vat"],
                "07_no_vat" => $row["l_07_no_vat"],
                "08_with_vat" => $row["l_08_with_vat"],
                "08_no_vat" => $row["l_08_no_vat"],
                "09_with_vat" => $row["l_09_with_vat"],
                "09_no_vat" => $row["l_09_no_vat"],
                "10_with_vat" => $row["l_10_with_vat"],
                "10_no_vat" => $row["l_10_no_vat"],
                "11_with_vat" => $row["l_11_with_vat"],
                "11_no_vat" => $row["l_11_no_vat"],
                "12_with_vat" => $row["l_12_with_vat"],
                "12_no_vat" => $row["l_12_no_vat"],
                "sum_with_vat" => $row["l_sum_with_vat"],
                "sum_no_vat" => $row["l_sum_no_vat"],
                "edited" => $row["l_edited"],
                "edit_mode" => $row["l_edit_mode"],
            ],
            "fact_indicators_amounts" => [
                "01_with_vat" => $row["m_01_with_vat"],
                "01_no_vat" => $exc_vat ? $row["m_01_no_vat"]  : $row["m_01_with_vat"] / 1200,
                "02_with_vat" => $row["m_02_with_vat"],
                "02_no_vat" => $exc_vat ? $row["m_02_no_vat"]  : $row["m_02_with_vat"] / 1200,
                "03_with_vat" => $row["m_03_with_vat"],
                "03_no_vat" => $exc_vat ? $row["m_03_no_vat"]  : $row["m_03_with_vat"] / 1200,
                "04_with_vat" => $row["m_04_with_vat"],
                "04_no_vat" => $exc_vat ? $row["m_04_no_vat"]  : $row["m_04_with_vat"] / 1200,
                "05_with_vat" => $row["m_05_with_vat"],
                "05_no_vat" => $exc_vat ? $row["m_05_no_vat"]  : $row["m_05_with_vat"] / 1200,
                "06_with_vat" => $row["m_06_with_vat"],
                "06_no_vat" => $exc_vat ? $row["m_06_no_vat"]  : $row["m_06_with_vat"] / 1200,
                "07_with_vat" => $row["m_07_with_vat"],
                "07_no_vat" => $exc_vat ? $row["m_07_no_vat"]  : $row["m_07_with_vat"] / 1200,
                "08_with_vat" => $row["m_08_with_vat"],
                "08_no_vat" => $exc_vat ? $row["m_08_no_vat"]  : $row["m_08_with_vat"] / 1200,
                "09_with_vat" => $row["m_09_with_vat"],
                "09_no_vat" => $exc_vat ? $row["m_09_no_vat"]  : $row["m_09_with_vat"] / 1200,
                "10_with_vat" => $row["m_10_with_vat"],
                "10_no_vat" => $exc_vat ? $row["m_10_no_vat"]  : $row["m_10_with_vat"] / 1200,
                "11_with_vat" => $row["m_11_with_vat"],
                "11_no_vat" => $exc_vat ? $row["m_11_no_vat"]  : $row["m_11_with_vat"] / 1200,
                "12_with_vat" => $row["m_12_with_vat"],
                "12_no_vat" => $exc_vat ? $row["m_12_no_vat"]  : $row["m_12_with_vat"] / 1200,
                "sum_with_vat" => $row["m_sum_with_vat"],
                "sum_no_vat" => $exc_vat ? $row["m_sum_no_vat"]  : $row["m_sum_with_vat"] / 1200,
                "edited" => $row["m_edited"],
                "edit_mode" => $row["m_edit_mode"],

            ],
            "01" => $row["01"] == (1 || null) ? "" : "sign_vat",
            "02" => $row["02"] == (1 || null) ? "" : "sign_vat",
            "03" => $row["03"] == (1 || null) ? "" : "sign_vat",
            "04" => $row["04"] == (1 || null) ? "" : "sign_vat",
            "05" => $row["05"] == (1 || null) ? "" : "sign_vat",
            "06" => $row["06"] == (1 || null) ? "" : "sign_vat",
            "07" => $row["07"] == (1 || null) ? "" : "sign_vat",
            "08" => $row["08"] == (1 || null) ? "" : "sign_vat",
            "09" => $row["09"] == (1 || null) ? "" : "sign_vat",
            "10" => $row["10"] == (1 || null) ? "" : "sign_vat",
            "11" => $row["11"] == (1 || null) ? "" : "sign_vat",
            "12" => $row["12"] == (1 || null) ? "" : "sign_vat",


        ]);
    }
    $mysql->close();
    return $data_array;
}

function GetFundholdersData()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM fundholders_directory");
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "additional_name" => $row["additional_name"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function CreateTableDOM($data, $admin_role, $financier_role, $director_role)
{
    $data_array = GetBudgetData($data);
    $hidden = ($admin_role || $director_role) ? "" : "hidden";
    echo "<table>";
    echo "<thead>
           <tr>
              <th id='main-table-th-id' class='main-table-th table-column-id sticky-table-column' rowspan='2'>№</th>";

    echo "<th id='main-table-th-actions' class='main-table-th table-column-actions sticky-table-column' rowspan='2' " . $hidden . ">";
    if ($admin_role || $director_role)
        echo "<img src='../../templates/images/edit_head.png' alt='edit' class='th-edit__image'>";
    echo "</th>";
    echo "<th id='main-table-th-new-code' class='main-table-th table-column-new-code sticky-table-column' rowspan='2'>Код статті</th>
              <th id='main-table-th-fundholder' class='main-table-th table-column-fundholder sticky-table-column' rowspan='2'>ФУ</th>
              <th id='main-table-th-service' class='main-table-th table-column-service sticky-table-column' rowspan='2'>Служба</th>
              <th id='main-table-th-service' class='main-table-th table-column-article-name sticky-table-column' rowspan='2'>Найменування ТМЦ, робіт, послуг</th>
              <th id='main-table-th-counterparty' class='main-table-th table-column-counterparty sticky-table-column' rowspan='2'>Контрагент</th>
              <th id='main-table-th-header-plan-year' class='table-column-plan-year' colspan='2'>План на рік</th>
              <th id='main-table-th-header-fact-year' class='table-column-fact-year' colspan='2'>Факт на рік</th>

              <th id='header-plan-first-quater__table__th' class='first-quarter__table quarter' colspan='2'>План на 1 квартал</th>
              <th id='header-fact-first-quater__table__th' class='first-quarter__table quarter' colspan='2'>Факт на 1 квартал</th>
              <th class='first-quarter__table' colspan='2'>План на січень</th>
              <th class='first-quarter__table' colspan='2'>Факт на січень</th>
              <th class='first-quarter__table' colspan='2'>План на лютий</th>
              <th class='first-quarter__table' colspan='2'>Факт на лютий</th>
              <th class='first-quarter__table' colspan='2'>План на березень</th>
              <th class='first-quarter__table' colspan='2'>Факт на березень</th>
              <th id='header-plan-second-quater__table__th' class='second-quarter__table quarter' colspan='2'>План на 2 квартал</th>
              <th id='header-fact-second-quater__table__th' class='second-quarter__table quarter' colspan='2'>Факт на 2 квартал</th>
              <th class='second-quarter__table' colspan='2'>План на квітень</th>
              <th class='second-quarter__table' colspan='2'>Факт на квітень</th>
              <th class='second-quarter__table' colspan='2'>План на травень</th>
              <th class='second-quarter__table' colspan='2'>Факт на травень</th>
              <th class='second-quarter__table' colspan='2'>План на червень</th>
              <th class='second-quarter__table' colspan='2'>Факт на червень</th>
              <th id='header-plan-third-quater__table__th' class='third-quarter__table quarter' colspan='2'>План на 3 квартал</th>
              <th id='header-fact-third-quater__table__th' class='third-quarter__table quarter' colspan='2'>Факт на 3 квартал</th>
              <th class='third-quarter__table' colspan='2'>План на липень</th>
              <th class='third-quarter__table' colspan='2'>Факт на липень</th>
              <th class='third-quarter__table' colspan='2'>План на серпень</th>
              <th class='third-quarter__table' colspan='2'>Факт на серпень</th>
              <th class='third-quarter__table' colspan='2'>План на вересень</th>
              <th class='third-quarter__table' colspan='2'>Факт на вересень</th>
              <th id='header-plan-fourth-quater__table__th' class='fourth-quarter__table quarter' colspan='2'>План на 4 квартал</th>
              <th id='header-fact-fourth-quater__table__th' class='fourth-quarter__table quarter' colspan='2'>Факт на 4 квартал</th>
              <th class='fourth-quarter__table' colspan='2'>План на жовтень</th>
              <th class='fourth-quarter__table' colspan='2'>Факт на жовтень</th>
              <th class='fourth-quarter__table' colspan='2'>План на листопад</th>
              <th class='fourth-quarter__table' colspan='2'>Факт на листопад</th>
              <th class='fourth-quarter__table' colspan='2'>План на грудень</th>
              <th class='fourth-quarter__table' colspan='2'>Факт на грудень</th>
          </tr>
          <tr>
              <th id='main-table-th-footer-plan-year-sum-vat' class='table-column-plan-year'>Оплата, тис. грн. з ПДВ</th>
              <th id='main-table-th-footer-plan-year-sum-no-vat' class='table-column-plan-year'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th id='main-table-th-footer-fact-year-sum-vat' class='table-column-fact-year'>Оплата, тис. грн. з ПДВ</th>
              <th id='main-table-th-footer-fact-year-sum-no-vat' class='table-column-fact-year'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>

              <th class='first-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='first-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='first-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>

              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='second-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='second-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>

              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='third-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='third-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>

              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
              <th class='fourth-quarter__table quarter'>Оплата, тис. грн. з ПДВ</th>
              <th class='fourth-quarter__table quarter'>Списання витрат, акт виконаних робіт, тис. грн. без ПДВ</th>
          </tr></thead>";
    echo "<tbody>";
    foreach ($data_array as $key => $value) {
        $edited = $value['planned_indicators_amounts']["edited"] ? "<img src='../../templates/images/edit_head.png' alt='edit' class='sign-edit__image'>" : "";
        $disabled = $value['planned_indicators_amounts']["edit_mode"] && !$value['set_after_transfer_mode'] ? "" : "disabled";
        $disabled_input = $value["fundholder_id"] == 7 ? "disabled" : "";
        if ($key === 0 || ($value['subsection'] !== $data_array[$key - 1]['subsection'])) {
            $colspan = ($admin_role || $director_role) ? 7 : 6;
            echo "<tr>";
            echo "<td class='main-section__table hierarchy-sections sticky-table-column' colspan='" . $colspan . "'>" . $value['main_section'] . "</td>";
            echo "<td class='hierarchy-sections' colspan='68'></td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class='section__table hierarchy-sections sticky-table-column' colspan='" . $colspan . "'>" . $value['section'] . "</td>";
            echo "<td class='hierarchy-sections' colspan='68'></td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td class='subsection__table hierarchy-sections sticky-table-column' colspan='" . $colspan . "'>" . $value['subsection'] . "</td>";
            echo "<td class='hierarchy-sections' colspan='68'></td>";
            echo "</tr>";
        }
        echo "<tr class='hover__table__tr' id='row-tr-" . $key . "'>";
        echo "<td class='id__table sticky-table-column'>" . ($key + 1) .  "</td>";
        echo "<td class='edit__table sticky-table-column' " . $hidden . ">";
        if ($director_role) {
            echo "<div class='td-toolbar'>";

            echo "<input type='image' src='../../templates/images/edit.png' alt='edit' class='action__td__input $disabled' onclick='modalEditBudgetPlanImplementationOnClick(" . $value["id"] . ")' $disabled/>";
            echo "<div>" . $edited . "</div>";
            echo "</div>";
        }
        echo "</td>";
        echo "<td class='article-code__table sticky-table-column'>" . $value['new_code'] . "</td>";
        echo "<td class='fundholder__table sticky-table-column'>" . $value['fundholder'] . "</td>";
        echo "<td class='service__table sticky-table-column'>" . $value['service'] . "</td>";
        echo "<td class='article-name__table sticky-table-column'>" . $value['article'] . "</td>";
        echo "<td class='counterparty__table sticky-table-column'>" . $value['counterparty'] . "</td>";

        echo "<td class='plan-year__table'>" . number_format($value['planned_indicators_amounts']['sum_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='plan-year__table'>" . number_format($value['planned_indicators_amounts']['sum_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fact-year__table'>" . number_format($value['fact_indicators_amounts']['sum_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='fact-year__table writing-off-costs__table__td'>" . number_format($value['fact_indicators_amounts']['sum_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['01_with_vat'] + $value['planned_indicators_amounts']['02_with_vat'] + $value['planned_indicators_amounts']['03_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['01_no_vat'] + $value['planned_indicators_amounts']['02_no_vat'] + $value['planned_indicators_amounts']['03_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['01_with_vat'] / 1000 + $value['fact_indicators_amounts']['02_with_vat'] / 1000 + $value['fact_indicators_amounts']['03_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter writing-off-costs__table__td'>" . number_format($value['fact_indicators_amounts']['01_no_vat']  + $value['fact_indicators_amounts']['02_no_vat']  + $value['fact_indicators_amounts']['03_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table " . $value['01'] . "'>" . number_format($value['planned_indicators_amounts']['01_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table'>" . number_format($value['planned_indicators_amounts']['01_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['01_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter writing-off-costs__table__td'><input data-month-info='01_no_vat' data-id='" . $value['id'] . "' data-row-id='row-tr-" . $key . "' value='" . number_format($value['fact_indicators_amounts']['01_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='first-quarter__table " . $value['02'] . "'>" . number_format($value['planned_indicators_amounts']['02_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table'>" . number_format($value['planned_indicators_amounts']['02_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['02_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter writing-off-costs__table__td'><input data-month-info='02_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "' value='" . number_format($value['fact_indicators_amounts']['02_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='first-quarter__table " . $value['03'] . "'>" . number_format($value['planned_indicators_amounts']['03_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table'>" . number_format($value['planned_indicators_amounts']['03_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['03_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='first-quarter__table quarter writing-off-costs__table__td'><input data-month-info='03_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['03_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";

        echo "<td class='second-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['04_with_vat'] + $value['planned_indicators_amounts']['05_with_vat'] + $value['planned_indicators_amounts']['06_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['04_no_vat'] + $value['planned_indicators_amounts']['05_no_vat'] + $value['planned_indicators_amounts']['06_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['04_with_vat'] / 1000 + $value['fact_indicators_amounts']['05_with_vat'] / 1000 + $value['fact_indicators_amounts']['06_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter writing-off-costs__table__td'>" . number_format($value['fact_indicators_amounts']['04_no_vat']  + $value['fact_indicators_amounts']['05_no_vat']  + $value['fact_indicators_amounts']['06_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table " . $value['04'] . "'>" . number_format($value['planned_indicators_amounts']['04_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table'>" . number_format($value['planned_indicators_amounts']['04_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['04_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter writing-off-costs__table__td'><input data-month-info='04_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['04_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='second-quarter__table " . $value['05'] . "'>" . number_format($value['planned_indicators_amounts']['05_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table'>" . number_format($value['planned_indicators_amounts']['05_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['05_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter writing-off-costs__table__td'><input data-month-info='05_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['05_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='second-quarter__table " . $value['06'] . "'>" . number_format($value['planned_indicators_amounts']['06_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table'>" . number_format($value['planned_indicators_amounts']['06_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['06_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='second-quarter__table quarter writing-off-costs__table__td'><input data-month-info='06_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['06_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";

        echo "<td class='third-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['07_with_vat'] + $value['planned_indicators_amounts']['08_with_vat'] + $value['planned_indicators_amounts']['09_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['07_no_vat'] + $value['planned_indicators_amounts']['08_no_vat'] + $value['planned_indicators_amounts']['09_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['07_with_vat'] / 1000 + $value['fact_indicators_amounts']['08_with_vat'] / 1000 + $value['fact_indicators_amounts']['09_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter writing-off-costs__table__td'>" . number_format($value['fact_indicators_amounts']['07_no_vat']  + $value['fact_indicators_amounts']['08_no_vat']  + $value['fact_indicators_amounts']['09_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table " . $value['07'] . "'>" . number_format($value['planned_indicators_amounts']['07_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table'>" . number_format($value['planned_indicators_amounts']['07_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['07_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter writing-off-costs__table__td'><input data-month-info='07_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['07_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='third-quarter__table " . $value['08'] . "'>" . number_format($value['planned_indicators_amounts']['08_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table'>" . number_format($value['planned_indicators_amounts']['08_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['08_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter writing-off-costs__table__td'><input data-month-info='08_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['08_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='third-quarter__table " . $value['09'] . "'>" . number_format($value['planned_indicators_amounts']['09_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table'>" . number_format($value['planned_indicators_amounts']['09_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='third-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['09_with_vat'] / 1000, 5, '.', ' ') . "</td>";

        echo "<td class='third-quarter__table quarter writing-off-costs__table__td'><input data-month-info='09_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['09_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";

        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['10_with_vat'] + $value['planned_indicators_amounts']['11_with_vat'] + $value['planned_indicators_amounts']['12_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['planned_indicators_amounts']['10_no_vat'] + $value['planned_indicators_amounts']['11_no_vat'] + $value['planned_indicators_amounts']['12_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['10_with_vat'] / 1000 + $value['fact_indicators_amounts']['11_with_vat'] / 1000 + $value['fact_indicators_amounts']['12_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter writing-off-costs__table__td'>" . number_format($value['fact_indicators_amounts']['10_no_vat'] + $value['fact_indicators_amounts']['11_no_vat']  + $value['fact_indicators_amounts']['12_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table " . $value['10'] . "'>" . number_format($value['planned_indicators_amounts']['10_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table'>" . number_format($value['planned_indicators_amounts']['10_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['10_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter writing-off-costs__table__td'><input data-month-info='10_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['10_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='fourth-quarter__table " . $value['11'] . "'>" . number_format($value['planned_indicators_amounts']['11_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table'>" . number_format($value['planned_indicators_amounts']['11_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['11_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter writing-off-costs__table__td'><input data-month-info='11_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['11_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "<td class='fourth-quarter__table " . $value['12'] . "'>" . number_format($value['planned_indicators_amounts']['12_with_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table'>" . number_format($value['planned_indicators_amounts']['12_no_vat'], 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter'>" . number_format($value['fact_indicators_amounts']['12_with_vat'] / 1000, 5, '.', ' ') . "</td>";
        echo "<td class='fourth-quarter__table quarter writing-off-costs__table__td'><input data-month-info='12_no_vat'  data-id='" . $value['id'] . "'  data-row-id='row-tr-" . $key . "'  value='" . number_format($value['fact_indicators_amounts']['12_no_vat'], 5, '.', ' ') . "' onchange='editWritingOffCostsInputOnClick(this)' $disabled_input/></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function ModalGetBudgetPlanImplementationData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT * FROM `planned_indicators_amounts_implementation` WHERE planned_indicators_id = '" . $data['id'] . "'");

    $row = $result->fetch_assoc();
    echo json_encode([
        "id" => $row["id"],
        "planned_indicators_id" => $row["planned_indicators_id"],
        "month_array_with_vat" => [
            floatval($row["01_with_vat"]),
            floatval($row["02_with_vat"]),
            floatval($row["03_with_vat"]),
            floatval($row["04_with_vat"]),
            floatval($row["05_with_vat"]),
            floatval($row["06_with_vat"]),
            floatval($row["07_with_vat"]),
            floatval($row["08_with_vat"]),
            floatval($row["09_with_vat"]),
            floatval($row["10_with_vat"]),
            floatval($row["11_with_vat"]),
            floatval($row["12_with_vat"]),
        ],
        "month_array_no_vat" => [
            floatval($row["01_no_vat"]),
            floatval($row["02_no_vat"]),
            floatval($row["03_no_vat"]),
            floatval($row["04_no_vat"]),
            floatval($row["05_no_vat"]),
            floatval($row["06_no_vat"]),
            floatval($row["07_no_vat"]),
            floatval($row["08_no_vat"]),
            floatval($row["09_no_vat"]),
            floatval($row["10_no_vat"]),
            floatval($row["11_no_vat"]),
            floatval($row["12_no_vat"]),
        ],
        "sum_with_vat" => floatval($row["sum_with_vat"]),
        "sum_no_vat" => floatval($row["sum_no_vat"]),
    ]);
    $mysql->close();
}

function ModalEditBudgetPlanImplementationSave($data, $user_id, $director_role)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = null;
    if ($director_role) {
        $result = $mysql->query(UpdatePlannedIndicatorsAmountImplementationQuery($data));
        $mysql->query("INSERT INTO planned_indicators_amounts_implementation_action_log (planned_indicators_amounts_implementation_id, user_id, action, datetime) 
                                VALUES ('" . $data['budgetPlanImplementationId'] . "', '" . $user_id . "', 'edit', '" . date("Y-m-d H:i:s") . "')");
    }
    if ($result == 1)
        echo json_encode(["status" => true, "text" => "Дані успішно збережено!"]);
    else echo json_encode(["status" => false, "text" => "Дані не збережено!"]);
    $mysql->close();
}

function UpdatePlannedIndicatorsAmountImplementationQuery($data)
{
    $values = "01_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][0] . "', 01_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][0] . "', " .
        "02_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][1] . "', 02_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][1] . "', " .
        "03_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][2] . "', 03_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][2] . "', " .
        "04_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][3] . "', 04_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][3] . "', " .
        "05_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][4] . "', 05_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][4] . "', " .
        "06_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][5] . "', 06_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][5] . "', " .
        "07_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][6] . "', 07_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][6] . "', " .
        "08_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][7] . "', 08_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][7] . "', " .
        "09_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][8] . "', 09_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][8] . "', " .
        "10_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][9] . "', 10_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][9] . "', " .
        "11_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][10] . "', 11_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][10] . "', " .
        "12_with_vat = '" . $data['monthPlannedImplementationIndicatorWithVAT'][11] . "', 12_no_vat = '" . $data['monthPlannedImplementationIndicatorNoVAT'][11] . "', " .
        "sum_with_vat = '" . $data['sumMonthWithVAT'] . "', sum_no_vat = '" . $data['sumMonthNoVAT'] . "', edited = true";
    return "UPDATE planned_indicators_amounts_implementation SET " . $values . " WHERE id = '" . $data['budgetPlanImplementationId'] . "'";
}

function GetPlanIndicatorsYearsInfo($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $result = $mysql->query("SELECT DISTINCT(a.year) as year FROM planned_indicators a
                             LEFT JOIN budget_articles_directory b ON b.id = a.budget_articles_directory_id"); // WHERE b.fundholders_directory_id = '" . $data['fundholderId'] . "'
    $index = 0;
    while ($row = $result->fetch_assoc()) {
        array_push($data_array,  $row["year"]);
        $index = $row["year"];
    }

    for ($i = 0, $currYear = strval(intval($index) + 1); $i < 1; $i++) {
        array_push($data_array,  $currYear);
        $currYear = strval(intval($currYear) + 1);
    }

    $mysql->close();
    echo json_encode($data_array);
}

function GetNewCodes($financier_role, $fundholder_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    $condition = $financier_role ? "" : " and a.fundholders_directory_id = '" . $fundholder_id . "'";
    $result = $mysql->query("SELECT DISTINCT(b.id), b.new_code FROM `budget_articles_directory` a
                             LEFT JOIN new_codes_directory b ON b.id = a.new_codes_directory_id
                             LEFT JOIN old_codes_directory c ON c.id = b.old_codes_directory_id WHERE a.new_codes_directory_id is not null " . $condition);
    while ($row = $result->fetch_assoc()) {
        array_push($data_array, [
            "id" => $row["id"],
            "new_code" => $row["new_code"],
        ]);
    }
    $mysql->close();
    echo json_encode($data_array);
}

function GetPaymentsData($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $data_array = array();
    if (!isset($data['newCodeId'])) { //  || $data['startDate'] == "" || $data['endDate'] == ""
        $mysql->close();
        return [];
    }

    $result = $mysql->query("SELECT * FROM banks_register WHERE new_codes_directory_id = '" . $data['newCodeId'] . "'"
        . ((!empty($data['startDate']) && !empty($data['endDate'])) ? " and operDate BETWEEN '" . $data['startDate'] . "' and '" . $data['endDate'] . "' " : " ") . " "
        . ("ORDER BY " . $data['orderColumn'] . " " . $data['orderStatus'])); //  and operDate BETWEEN '" . $data['startDate'] . "' and '" . $data['endDate'] . "'

    while ($row = $result->fetch_assoc()) {
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

    $data['orderColumn'] = $data['orderColumn'] === 'operDate' ? 'oper_date' : $data['orderColumn'];

    $result = $mysql->query("SELECT b.operNumber, b.recipientNameFromExtract, a.oper_date, a.purpose, a.sum, a.old_code, a.new_code FROM banks_register_additional_purpose a
                             LEFT JOIN banks_register b ON b.id = a.banks_register_id
                             WHERE a.new_codes_directory_id = '" . $data['newCodeId'] . "'"
        . ((!empty($data['startDate']) && !empty($data['endDate'])) ? "and a.oper_date BETWEEN '" . $data['startDate'] . "' and '" . $data['endDate'] . "'" : "")
        . ("ORDER BY a." . $data['orderColumn'] . " " . $data['orderStatus'])); // and a.oper_date BETWEEN '" . $data['startDate'] . "' and '" . $data['endDate'] . "'

    while ($row = $result->fetch_assoc()) {
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

function CreateSearchTableDOM($data, $admin_role, $financier_role, $director_role)
{
    $data_array = GetPaymentsData($data);
    echo "<table id='modal-window-search-banks-register-table'>";
    echo "<thead id='modal-main-table-thead' class='unselectable' data-sort='operDate ASC'>";
    echo "<tr>";
    echo "<th class='main-table-th modal-table-column-id'>№</th>";
    echo "<th class='main-table-th modal-table-column-oper-number'>Номер доручення</th>";
    echo "<th id='main-table-th-date' class='main-table-th modal-table-column-date modal-sort-th' data-column='operDate'>Дата</th>";
    echo "<th class='main-table-th modal-table-column-old-code'>Код статті (старий)</th>";
    echo "<th class='main-table-th modal-table-column-new-code'>Код статті (новий)</th>";
    echo "<th class='main-table-th modal-table-column-sum'>Сума, грн</th>";
    echo "<th class='main-table-th modal-table-column-counterparty'>Контрагент</th>";
    echo "<th class='main-table-th modal-table-column-payment-type'>Призначення платежу</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($data_array as $key => $value) {
        echo "<tr>";
        echo "<td class='modal-table-column-id'>" . ($key + 1) . "</td>";
        echo "<td class='modal-table-column-oper-number'>" . $value['oper_number'] . "</td>";
        echo "<td class='modal-table-column-date'>" . $value['date'] . "</td>";
        echo "<td class='modal-table-column-old-code'>" . $value["old_code"] . "</td>";
        echo "<td class='modal-table-column-new-code'>" . $value["new_code"] . "</td>";
        echo "<td class='modal-table-column-sum'>" . (number_format($value["sum"], 2, '.', ' ')) . "</td>";
        echo "<td class='modal-table-column-counterparty'>" . $value["counterparty"] . "</td>";
        echo "<td class='modal-table-column-payment-type'>" . $value["payment_type"] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

function editWritingOffCostsSave($data)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $result = null;
    $result = $mysql->query("UPDATE fact_indicators_amounts SET " . $data['nameColumn'] . " =  '" . $data['value'] . "' WHERE planned_indicators_id = '" . $data['id'] . "'");
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
    case "getFundholdersRequest":
        GetFundholdersData();
        break;
    case "modalGetBudgetPlanImplementationRequest":
        ModalGetBudgetPlanImplementationData($data);
        break;
    case "modalEditBudgetPlanImplementationSaveRequest":
        ModalEditBudgetPlanImplementationSave($data, $user_id, $director_role);
        break;
    case "getPlanIndicatorsYearsInfoRequest":
        GetPlanIndicatorsYearsInfo($data);
        break;
    case "getNewCodesRequest":
        GetNewCodes($financier_role, $fundholder_id);
        break;
    case "modalSearchBankRegisterRequest":
        CreateSearchTableDOM($data, $admin_role, $financier_role, $director_role);
        break;
    case "editWritingOffCostsSaveRequest":
        editWritingOffCostsSave($data);
        break;
    default:
        break;
}
