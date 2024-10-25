<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require_once("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="main-header">
                <div class="main-header-title main-header-item text-1">Виконання планового бюджету</div>
                <div class="main-header-filter">
                    <select id="budget-plan-implementation-years__select" class="main-header-item unselectable"
                        onchange='budgetPlanImplementationYearSelectOnChange()'>
                    </select>
                </div>
                <?php
                    if($financier_role)
                        echo "<div class='main-header-filter budget-plan-implementation-fundolders-wrapper-select'>
                                <select id='budget-plan-implementation-fundholders__select' class='main-header-item unselectable' onchange='budgetPlanImplementationFundholderSelectOnChange()'>
                                </select>
                              </div>";
                 ?>
                <div class='main-header-link budget-plan-implementation-fundolders-wrapper-search'>
                    <button onclick="modalSearchInRegistatBanksOnClick()">Пошук в реєстрі банків</button>
                </div>
                <div class='upper-save-panel'>
                    <label id='label-save-indicator' hidden></label>
                </div>
            </div>

            <div class="main-content">
                <div id="switch-panel" class="switch-panel unselectable">
                    <div class="switch-panel-text">Квартал 1</div>
                    <div class="switch-element">
                        <input type="checkbox" id="switch-first-quarter" class="switch-element__input"
                            data-column-class="first-quarter__table"
                            <?php echo intval(date('m')) > 0 ? "checked" : "" ?> />
                        <label for="switch-first-quarter" class="switch-element__lable"></label>
                    </div>
                    <div class="switch-panel-text">Квартал 2</div>
                    <div class="switch-element">
                        <input type="checkbox" id="switch-second-quarter" class="switch-element__input"
                            data-column-class="second-quarter__table"
                            <?php echo intval(date('m')) > 3 ? "checked" : "" ?> />
                        <label for="switch-second-quarter" class="switch-element__lable"></label>
                    </div>
                    <div class="switch-panel-text">Квартал 3</div>
                    <div class="switch-element">
                        <input type="checkbox" id="switch-third-quarter" class="switch-element__input"
                            data-column-class="third-quarter__table"
                            <?php echo intval(date('m')) > 6 ? "checked" : "" ?> />
                        <label for="switch-third-quarter" class="switch-element__lable"></label>
                    </div>
                    <div class="switch-panel-text">Квартал 4</div>
                    <div class="switch-element">
                        <input type="checkbox" id="switch-fourth-quarter" class="switch-element__input"
                            data-column-class="fourth-quarter__table"
                            <?php echo intval(date('m')) > 9 ? "checked" : "" ?> />
                        <label for="switch-fourth-quarter" class="switch-element__lable"></label>
                    </div>
                </div>
                <div id="main-table"></div>
            </div>
        </div>
        <?php require_once("../../templates/layout/footer.php") ?>
        <?php require('modal-windows/modal-window-edit-budget-plan-implementation.php') ?>
        <?php require('modal-windows/modal-window-search-bank-register.php') ?>
    </div>
    <link href="../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
    <script src='modal-windows/modal-window-edit-budget-plan-implementation.js'></script>
    <script src='modal-windows/modal-window-search-bank-register.js'></script>
</body>

</html>