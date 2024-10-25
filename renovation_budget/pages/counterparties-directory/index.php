<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require("../../templates/layout/header.php")?>
        <div class="main">
            <div class="text-1 unselectable">Довідник контрагентів</div>
            <div id="counterparties-directory-table"></div>
            <div class="upper-counterparties-save-panel">
                <label id="label-counterparties-save-indicator" hidden></label>
            </div>
        </div>
        <?php require("../../templates/layout/footer.php")?>
        <?php require("modal-windows/modal-window-add-counterparties.php") ?>
        <link href="../../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <script src="../../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $('select').select2();
        </script>
        <script src="index.js"></script>
        <script src="modal-windows/modal-window.js"></script>
    </div>
</body>

</html>