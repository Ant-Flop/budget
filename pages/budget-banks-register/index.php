<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require_once("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="main-header">
                <div class="main-header-title main-header-item text-1">Реєстр банків за </div>
                <div class="main-header-filter"><input type="date" id="banks-register-date-filter"
                        class="date-filter__input" onchange="banksRegisterDateInputOnChange(this)" /></div>
                <div id="spinner-loader-id">
                    <svg id="svg-loader" class="loader-rotate">
                        <circle id="grow-circle-loader" r="10" cx="25" cy="25" fill="transparent"></circle>
                        <circle id="blue-circle-loader" r="10" cx="25" cy="25" fill="transparent"></circle>
                    </svg>
                </div>
            </div>
            <div class="main-content">
                <div id="main-table"></div>
            </div>
            <hr>
            <div class="main-footer">
                <div class="main-footer-title text-1">Пошук</div>
                <div class="footer-table-bar">
                    <div class="footer-table-bar-item" id="budget-article-code-filter">
                        <div class="footer-span">
                            <span>Код статті</span>
                        </div>
                        <div id="budget-banks-register-budget-article-search-wrapper__select">
                            <select id="budget-banks-register-budget-article-search__select">
                            </select>
                        </div>
                    </div>
                    <div class="footer-table-bar-item" id="first-date-filter">
                        <div class="footer-span">
                            <span>Період з </span>
                        </div>
                        <div id="budget-banks-register-start-date-search-wrapper__input">
                        </div>
                    </div>
                    <div class="footer-table-bar-item" id="last-date-filter">
                        <div class="footer-span">
                            <span>по</span>
                        </div>
                        <div id="budget-banks-register-end-date-search-wrapper__input">
                        </div>

                    </div>
                </div>
                <div id="footer-table">
                    <table>
                        <tr>
                            <th class="main-table-th table-column-id">№</th>
                            <th class="main-table-th table-column-oper-number">Номер доручення</th>
                            <th class="main-table-th table-column-date">Дата</th>
                            <th class="main-table-th table-column-old-code">Код статті (старий)</th>
                            <th class="main-table-th table-column-new-code">Код статті (новий)</th>
                            <th class="main-table-th table-column-sum">Сума, грн</th>
                            <th class="main-table-th table-column-counterparty">Контрагент</th>
                            <th class="main-table-th table-column-payment-type">Призначення платежу</th>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
        <?php require_once("../../templates/layout/footer.php") ?>
    </div>
    <link href="../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
</body>

</html>