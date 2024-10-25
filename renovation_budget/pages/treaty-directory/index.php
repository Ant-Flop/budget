<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require("../../templates/layout/header.php"); ?>
        <div class="main">
            <div class="text-1 unselectable">Довідник договору</div>
            <div class="above-table-bar unselectable">
                <div id="add-service-id">
                    <label for='treaty-service-add-button'>Додати послугу</label><br />
                    <div class="above-table-bar-element">
                        <button id='treaty-service-add' onclick='addServiceOnClick()'>Додати</button>
                    </div>
                </div>
                <div id="counterparty-treaty-id">
                    <label for='treaty-counterparty-select'>Контрагент</label><br />
                    <div class="above-table-bar-element">
                        <select id='treaty-counterparty-select'
                            onchange="selectCounterpartiesOnChange('treaty-number-contract-select')">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="term-of-contract-treaty-id">
                    <label for='term-of-contract-select'>Рік терміну дії договору</label><br />
                    <div class="above-table-bar-element">
                        <select id='term-of-contract-select'>
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div id="number-contract-treaty-id">
                    <label for='treaty-number-contract-select'>Номер договору</label><br />
                    <div class="above-table-bar-element">
                        <select id='treaty-number-contract-select' onchange="selectTreatyOnChange(this)">
                            <option selected>Обрати</option>
                        </select>
                    </div>
                </div>
                <div class="upper-treaty-directory-save-panel">
                    <label id="label-treaty-directory-save-indicator" hidden></label>
                </div>
            </div>
            <div id="treaty-directory-table"></div>
            <div class="under-table-bar unselectable">
                <div id="edit-mode-id" hidden>
                    <div class="under-table-bar-element">
                        <button id='treaty-edit-mode' onclick="blokingEditModeOnClick()">Заблокувати
                            редагування</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require("../../templates/layout/footer.php") ?>
        <?php require("modal-windows/modal-window-add-treaty.php") ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script>
        $('select').select2();
        </script>
        <script src="index.js"></script>
        <script src="modal-windows/modal-window.js"></script>
    </div>
</body>

</html>