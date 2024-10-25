<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require_once("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="main-header">
                <div class="main-header-title main-header-item text-1">Оплата </div>
            </div>
            <div class="main-content">
                <div class="above-table-bar unselectable">
                    <div class="add-button-table-bar">
                        <label>Пакетна обробка</label><br>
                        <div class="above-table-bar-element">
                            <button class="above-table-add__button disabled" id="payments-batch-processing__button" onclick="paymentsBatchProcessingOnClick()" disabled>Обробити</button>
                        </div>
                    </div>
                    <div class="add-select-table-bar">
                        <label>Старий код</label><br>
                        <div class="above-table-bar-element above-table-bar-select budget-payment-directory-old-code-wrapper__select">
                            <select id="budget-payment-directory-old-code__select"></select>
                        </div>
                    </div>
                    <div class="add-select-table-bar">
                        <label>Новий код</label><br>
                        <div class="above-table-bar-element above-table-bar-select budget-payment-directory-new-code-wrapper__select">
                            <select id="budget-payment-directory-new-code__select"></select>
                        </div>
                    </div>
                    <div id="spinner-loader-id">
                        <svg id="svg-loader" class="loader-rotate">
                            <circle id="grow-circle-loader" r="10" cx="25" cy="25" fill="transparent"></circle>
                            <circle id="blue-circle-loader" r="10" cx="25" cy="25" fill="transparent"></circle>
                        </svg>
                    </div>
                    <div class="upper-save-panel">
                        <label id="label-save-indicator" hidden></label>
                    </div>
                </div>
                <div id="main-table"></div>
                <hr>
                <div class="paginator"></div>
                <hr>
            </div>
        </div>
        <?php require_once("../../templates/layout/footer.php") ?>
        <?php require_once("modal-windows/modal-window-additional-purpose.php") ?>
    </div>
    <link href="../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
    <script src="modal-windows/modal-window-additional-purpose.js"></script>
</body>

</html>