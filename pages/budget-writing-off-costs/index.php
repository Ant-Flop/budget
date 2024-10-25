<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require_once("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="main-header">
                <div class="main-header-title main-header-item text-1">Списання витрат</div>
            </div>
            <div class="main-content">
                <div class="above-table-bar unselectable">
                    <div class="add-select-table-bar">
                        <label>Служба</label><br />
                        <div id="budget-writing-off-costs-service-wrapper__select" class="above-table-bar-element">
                            <select id="budget-writing-off-costs-service__select">
                            </select>
                        </div>
                    </div>
                    <div class="add-select-table-bar">
                        <label>Стаття бюджету</label><br />
                        <div id="budget-writing-off-costs-budget-article-wrapper__select" class="above-table-bar-element">
                            <select id="budget-writing-off-costs-budget-article__select">
                            <option>Обрати</option>
                            </select>
                        </div>
                    </div>
                    <div class="add-select-table-bar">
                        <label>Контрагент</label><br />
                        <div id="budget-writing-off-costs-counterparty-wrapper__select" class="above-table-bar-element">
                            <select id="budget-writing-off-costs-counterparty__select">
                            <option>Обрати</option>
                            </select>
                        </div>
                    </div>
                    <div class="add-select-table-bar">
                        <label>Номер договору</label><br />
                        <div id="budget-writing-off-costs-contract-wrapper__select" class="above-table-bar-element">
                            <select id="budget-writing-off-costs-contract__select">
                            <option>Обрати</option>
                            </select>
                        </div>
                    </div>
                    <div class="upper-save-panel">
                        <label id="label-save-indicator" hidden></label>
                    </div>
                </div>
                <div id="main-table"></div>
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