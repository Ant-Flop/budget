<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="text-1 unselectable">Виконання ремонтних робіт підрядним способом по статтям бюджету
            </div><br />
            <div class="above-table-bar unselectable">
            <div id="start-date-fact-of-article-id">
                    <label for="start-date-fact-of-article-input">Початковий місяць</label><br />
                    <div class="above-table-bar-element">
                        <input id="start-date-fact-of-article-input" type="month" onchange="inputStartDateOnChange(this)" />
                    </div>
                </div>
                <div id="date-fact-of-article-id">
                    <label for="date-fact-of-article-input">Кінцевий місяць</label><br />
                    <div class="above-table-bar-element">
                        <input id="end-date-fact-of-article-input" type="month" onchange="inputEndDateOnChange(this)" />
                    </div>
                </div>
                <div id="subsection-fact-of-article-id">
                    <label for="subsection-fact-of-article-select">Підрозділ</label><br />
                    <div class="above-table-bar-element">
                        <select id="subsection-fact-of-article-select" onchange="selectSubsectionOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="article-fact-of-article-id">
                    <label for="article-fact-of-article-select">Стаття витрат згідно бюджету</label><br />
                    <div class="above-table-bar-element">
                        <select id="article-fact-of-article-select" onchange="selectArticleOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="spinner-loader-id">
                    <svg id="svg-loader" class="loader-rotate" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <circle id="grow-circle-loader" r="10" cx="25" cy="25" fill="transparent" />
                            <circle id="blue-circle-loader" r="10" cx="25" cy="25" fill="transparent" />
                    </svg>
                </div>
            </div><br />
            <div class="main-content">
                <div id="info-treaty">
                    <table id="info-treaty__table">
                        <thead class="unselectable">
                            <tr>
                                <th>Номер договору</th>
                                <th>Дата</th>
                                <th>Контрагент</th>
                                <th>Сума всього, тис. грн. без ПДВ</th>
                                <th>Сума всього, тис. грн. з ПДВ</th>
                            </tr>
                        </thead>
                        <tbody id="info-treaty__table__tbody">
                        </tbody>
                    </table>
                </div><br />
                <div class="above-table-bar unselectable">
                    <div id="plane-of-budget-fact-of-article-id">
                        <label for="plane-of-budget-fact-of-article-input">План бюджету, тис. грн. з ПДВ</label><br />
                        <div class="above-table-bar-element">
                            <input id="plane-of-budget-fact-of-article-input" value="0.00000" disabled />
                        </div>
                    </div>
                    <div id="plane-write-off-of-budget-fact-of-article-id">
                        <label for="plane-write-off-of-budget-fact-of-article-input">План списання витрат, тис. грн. без
                            ПДВ</label><br />
                        <div class="above-table-bar-element">
                            <input id="plane-write-off-of-budget-fact-of-article-input" value="0.00000" disabled />
                        </div>
                    </div>
                </div><br />
                <div id="fact-of-articles_table">
                    <table>
                        <thead class="unselectable">
                            <tr>
                                <th>Дата факту оплати</th>
                                <th>Факт оплати, тис. грн. з ПДВ</th>
                                <th>Факт списання витрат, тис.грн. без ПДВ</th>
                                <th>Сума акту, тис грн з ПДВ</th>
                                <th>Акт №</th>
                                <th>Дата акту</th>
                                <th>№ договору</th>
                            </tr>
                        </thead>
                        <tbody id="fact-of-articles_table_tbody">
                        </tbody>
                    </table>
                </div><br />
                <div class="above-table-bar unselectable">
                    <div id="deviation-plan-id">
                        <label for="deviation-plan-input">Відхилення від плану, тис.грн. з ПДВ</label><br />
                        <div class="above-table-bar-element">
                            <input id="deviation-plan-input" value="0.00000" disabled />
                        </div>
                    </div>
                    <div id="percentage-of-completion-budget-id">
                        <label for="percentage-of-completion-budget-input">% виконання бюджету</label><br />
                        <div class="above-table-bar-element">
                            <input id="percentage-of-completion-budget-input" maxlength="5" value="0" disabled />
                        </div>
                    </div>
                </div><br />
                <div id="final-treaty-table">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>За поточний місяць</th>
                                <th>За рік нарастаючим</th>
                            </tr>
                        </thead>
                        <tbody id="final-table-tbody">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Всього витрат, тис.грн. з ПДВ</td>
                                <td id="final-table__tfoot__td-sum-month">0.00000</td>
                                <td id="final-table__tfoot__td-sum-year">0.00000</td>
                            </tr>
                        </tfoot>
                    </table>
                </div><br />
                <div class="above-table-bar unselectable">
                    <div id="all-fact-sum-id">
                        <label for="all-fact-sum-input">Всього факт оплати, тис.грн. з ПДВ / без ПДВ</label><br />
                        <div class="above-table-bar-element">
                            <input id="all-fact-sum-input" value="0.00000" disabled />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require("../../templates/layout/footer.php") ?>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
</body>

</html>