<?php require_once('../../sessions_api/check_session.php') ?>
<?php require_once('../../sessions_api/variable_session.php') ?>
<!DOCTYPE html>
<html lang='ua'>
<?php require_once('../../templates/layout/head.php') ?>

<body>
    <div class='wrapper'>
        <?php require_once('../../templates/layout/header.php') ?>
        <div class='main '>
            <div class='main-header'>
                <div class='main-header-title main-header-item text-1'>Довідник контрагентів</div>
            </div>
            <div class='main-content'>
                <div class='above-table-bar '>
                    <?php
                    if ($director_role) {
                        echo "<div class='add-button-table-bar'>
                                    <label>Додати контрагента</label><br />
                                    <div class='above-table-bar-element'>
                                        <button class='above-table-add__button'
                                            onclick='modalAddCounterpartyOnClick()'>Додати</button>
                                    </div>
                              </div>";
                        echo "<div class='add-button-table-bar'>
                              <label>Додати договір</label><br />
                              <div class='above-table-bar-element'>
                                  <button class='above-table-add__button'
                                      onclick='modalAddContractOnClick()'>Додати</button>
                              </div>
                        </div>";
                        echo "<div class='add-button-table-bar'>
                              <div class='above-table-bar-element archive-contract-wrapper'>
                                <div class='archive-label'>Архівні договори</div>
                                <div><input type='checkbox' id='archive-contract' class='above-table-add__checkbox' onclick='archiveContractOnClick()'></div>
                              </div>
                        </div>";
                        echo "<div class='upper-save-panel'>
                                <label id='label-save-indicator' hidden></label>
                            </div>";
                    }
                    ?>
                </div>
                <div id='main-table'></div>
            </div>
        </div>
        <?php require_once('../../templates/layout/footer.php') ?>
        <?php require('modal-windows/modal-window-add-counterparty.php') ?>
        <?php require('modal-windows/modal-window-edit-counterparty.php') ?>
        <?php require('modal-windows/modal-window-add-contract.php') ?>
        <?php require('modal-windows/modal-window-edit-contract.php') ?>
    </div>
    <link href='../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css' rel='stylesheet'>
    <script src='../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js'></script>
    <script>
    $('select').select2();
    </script>
    <script src='index.js'></script>
    <script src='modal-windows/modal-window-add-counterparty.js'></script>
    <script src='modal-windows/modal-window-edit-counterparty.js'></script>
    <script src='modal-windows/modal-window-add-contract.js'></script>
    <script src='modal-windows/modal-window-edit-contract.js'></script>

</body>

</html>