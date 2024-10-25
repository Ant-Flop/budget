<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="text-1 unselectable">Виконання ремонтних робіт підрядним способом по договорах</div>
            <div class="above-table-bar unselectable">
                <div id="start-date-filter-id">
                    <label for="start-date-id">Початоковий місяць</label><br />
                    <div class="above-table-bar-element">
                        <input type="month" id="start-date-id" value="<?php echo date('Y-m') ?>" onchange="inputStartMonthChange(this)" />
                    </div>
                </div>
                <div id="end-date-filter-id">
                    <label for="end-date-id">Кінцевий місяць</label><br />
                    <div class="above-table-bar-element">
                        <input type="month" id="end-date-id" value="<?php echo date('Y-m') ?>" onchange="inputEndMonthChange(this)" />
                    </div>
                </div>

                <!-- </div>
            <div class="above-table-bar unselectable"> -->
                <!-- <div id="name-article-fact-of-contracts-id">
                    <label for='name-article-select-id'>Назва статті</label><br />
                    <div class="above-table-bar-element">
                        <select id='name-article-select-id' onchange="selectArticlesBarOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div> -->
                <div id="counterparty-fact-of-contract-id">
                    <label for='counterparty-select-id'>Контрагент</label><br />
                    <div class="above-table-bar-element">
                        <select id='counterparty-select-id' onchange="selectCounterpartiesBarOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="contract-number-fact-of-contract-id">
                    <label for='contract-number-select-id'>Номер договору</label><br />
                    <div class="above-table-bar-element">
                        <select id='contract-number-select-id' onchange="selectContractsBarOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="sum-of-contract-with-pdv-fact-of-contract-id">
                    <label for='sum-of-contract-with-pdv-input-id' id='sum-of-contract-with-pdv-label-id'>Сума,
                        договору, грн. з ПДВ</label><br />
                    <div class="above-table-bar-element">
                        <input id="sum-of-contract-with-pdv-input-id" disabled />
                    </div>
                </div>
                <div id="balance-of-contract-with-pdv-fact-of-contract-id">
                    <label for='balance-of-contract-with-pdv-input-id' id='balance-of-contract-with-pdv-label-id'>Залишок по договору, грн. з ПДВ</label><br />
                    <div class="above-table-bar-element">
                        <input id="balance-of-contract-with-pdv-input-id" disabled />
                    </div>
                </div>
                <div id="spinner-loader-id">
                    <svg id="svg-loader" class="loader-rotate" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <circle id="grow-circle-loader" r="10" cx="25" cy="25" fill="transparent" />
                        <circle id="blue-circle-loader" r="10" cx="25" cy="25" fill="transparent" />
                    </svg>
                </div>
            </div>
            <div id="fact-of-contracts-table"></div>
        </div>
        <?php require("../../templates/layout/footer.php") ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script>
            $('select').select2();
        </script>
        <script src="index.js"></script>
        <script>

        </script>
    </div>
</body>

</html>