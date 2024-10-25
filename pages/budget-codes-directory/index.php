<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>
<body>
    <div class="wrapper">
        <?php require_once("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="main-header">
                <div class="main-header-title main-header-item text-1">Довідник коду</div>
            </div>
            <div class="main-content">
                <div class="above-table-bar unselectable">
                    <div class="add-button-table-bar">
                        <label>Додати старий код</label><br />
                        <div class="above-table-bar-element">
                            <button class="above-table-add__button"
                                onclick='modalAddOldCodeOnClick()'>Додати</button>
                        </div>
                    </div>
                    <div class="add-button-table-bar">
                        <label>Додати новий код</label><br />
                        <div class="above-table-bar-element">
                            <button class="above-table-add__button"
                                onclick='modalAddNewCodeOnClick()'>Додати</button>
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
        <?php require("modal-windows/modal-window-add-old-code.php") ?>
        <?php require("modal-windows/modal-window-edit-old-code.php") ?>
        <?php require("modal-windows/modal-window-add-new-code.php") ?>
        <?php require("modal-windows/modal-window-edit-new-code.php") ?>
    </div>
    <link href="../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
    <script src="modal-windows/modal-window-add-old-code.js"></script>
    <script src="modal-windows/modal-window-edit-old-code.js"></script>
    <script src="modal-windows/modal-window-add-new-code.js"></script>
    <script src="modal-windows/modal-window-edit-new-code.js"></script>
</body>

</html>