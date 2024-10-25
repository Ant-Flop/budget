<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="text-1 unselectable">Виконання ремонтних робіт підрядним способом загальний</div>
            <div class="above-table-bar unselectable">
                <div id="start-date-filter-id">
                    <label for="start-date-id">Початоковий місяць</label><br />
                    <div class="above-table-bar-element">
                        <input type='month' id='start-date-id' value='<?php echo date("Y-01") ?>'
                            onchange='inputStartMonthChange(this)' />
                    </div>
                </div>
                <div id="end-date-filter-id">
                    <label for="end-date-id">Кінцевий місяць</label><br />
                    <div class="above-table-bar-element">
                        <input type="month" id="end-date-id" value="<?php echo date('Y-m') ?>"
                            onchange="inputEndMonthChange(this)" />
                    </div>
                </div>
                <div id="spinner-loader-id">
                    <svg id="svg-loader" class="loader-rotate" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <circle id="grow-circle-loader" r="10" cx="25" cy="25" fill="transparent" />
                            <circle id="blue-circle-loader" r="10" cx="25" cy="25" fill="transparent" />
                    </svg>
                </div>
            </div>
            <div class="above-table-bar unselectable">
                <table id="subsection-info__table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Підрозділ</th>
                            <th>тис. грн. без ПДВ</th>
                            <th>тис. грн. з ПДВ</th>
                        </tr>
                    </thead>
                    <tbody id="subsection-info__table__tbody"></tbody>
                </table>
            </div><br />
            <div id="main-report-table">
                <table>
                    <thead class="unselectable">
                        <tr>
                            <th class="th-low-border th-article-name" rowspan=2>Стаття витрат згідно бюжету</th>
                            <th class="th-low-border" rowspan=2>Номер договору</th>
                            <th class="th-low-border" rowspan=2>Детальний звіт по договору</th>
                            <th class="th-low-border" rowspan=2>Проведено оплат, тис.грн. без ПДВ</th>
                            <th class="th-low-border" rowspan=2>Проведено оплат, тис.грн. з ПДВ</th>
                            <th colspan=5>Списання витрат (Акти
                                виконаних
                                робіт)</th>
                            <th colspan=2>План бюджету річний</th>
                            <th colspan=2>Залишок по бюджету</th>
                        </tr>
                        <tr>
                            <th class="table-th-sticky-second-row">К-сть</th>
                            <th class="table-th-sticky-second-row">Сума акту, тис.грн. без ПДВ
                            </th>
                            <th class="table-th-sticky-second-row">Сума акту, тис.грн. з ПДВ
                            </th>
                            <th class="table-th-sticky-second-row">Разом по договорам, тис.
                                грн.
                                без ПДВ</th>
                            <th class="table-th-sticky-second-row">Разом по договорам, тис.
                                грн. з
                                ПДВ</th>
                            <th class="table-th-sticky-second-row">тис. грн. без ПДВ</th>
                            <th class="table-th-sticky-second-row">тис. грн. з ПДВ</th>
                            <th class="table-th-sticky-second-row">тис. грн. без ПДВ</th>
                            <th class="table-th-sticky-second-row">тис. грн. з ПДВ</th>
                        </tr>
                    </thead>
                    <tbody id="main-report__table__tbody" class="unselectable">
                    </tbody>
                </table>
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