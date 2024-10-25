<?php require_once('../../sessions_api/check_session.php') ?>
<!DOCTYPE html>
<html lang='ua'>
<?php require_once('../../templates/layout/head.php') ?>

<body>
    <div class='wrapper'>
        <?php require_once('../../templates/layout/header.php') ?>
        <div class='main'>
            <div class='main-header'>
                <!-- <div class="go-back"><a href="">Назад</a></div> -->
                <div class='main-header-title main-header-item text-1'>Планові показники</div>
            </div>
            <div class='main-content'>
                <div class='above-table-bar unselectable'>
                    <?php
                    if ($director_role) {
                        echo "<div class='add-button-table-bar'>
                                    <label>Додати запис</label><br />
                                    <div class='above-table-bar-element'>
                                        <button class='above-table-add__button'
                                            onclick='modalAddPlannedIndicatorOnClick()'>Додати</button>
                                    </div>
                                </div>";
                        echo "<div class='add-button-table-bar'>
                                    <label>Перенесення статей</label><br />
                                    <div class='above-table-bar-element'>
                                        <button class='above-table-add__button'
                                            onclick='modalTransferPlannedIndicatorOnClick()'>Перенести</button>
                                    </div>
                                </div>";
                    }
                    ?>
                    <div id="spinner-loader-id" style="visibility: hidden;">
                        <svg id="svg-loader" class="loader-rotate">
                            <circle id="grow-circle-loader" r="10" cx="25" cy="25" fill="transparent"></circle>
                            <circle id="blue-circle-loader" r="10" cx="25" cy="25" fill="transparent"></circle>
                        </svg>
                    </div>
                    <div class='upper-save-panel'>
                        <label id='label-save-indicator' hidden></label>
                    </div>
                </div>
                <div id='main-table'></div>
            </div>
        </div>
        <?php require_once('../../templates/layout/footer.php') ?>
        <?php require('modal-windows/modal-window-add-planned-indicator.php') ?>
        <?php require('modal-windows/modal-window-edit-planned-indicator.php') ?>
        <?php require('modal-windows/modal-window-transfer-planned-indicator.php') ?>
        <?php require('modal-windows/modal-window-set-planned-indicator.php') ?>
        <?php require('modal-windows/modal-window-edit-budget-plan.php') ?>
    </div>
    <link href='../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css' rel='stylesheet'>
    <script src='../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js'></script>
    <script>
        $('select').select2();
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
    </script>
    <script src='index.js'></script>
    <script src='modal-windows/modal-window-add-planned-indicator.js'></script>
    <script src='modal-windows/modal-window-edit-planned-indicator.js'></script>
    <script src='modal-windows/modal-window-transfer-planned-indicator.js'></script>
    <script src='modal-windows/modal-window-set-planned-indicator.js'></script>
    <script src='modal-windows/modal-window-edit-budget-plan.js'></script>
</body>

</html>