<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class="wrapper">
        <?php require_once("../../templates/layout/header.php") ?>
        <div class="main">
            <div class="main-header">
                <div class="main-header-title main-header-item text-1">Довідник статті бюджета</div>
            </div>
            <div class="main-content">
                <div class="above-table-bar unselectable">
                    <div class="add-button-table-bar">
                        <label>Додати статтю бюджета</label><br />
                        <div class="above-table-bar-element">
                            <button class="above-table-add__button"
                                onclick='modalAddBudgetArticleOnClick()'>Додати</button>
                        </div>
                    </div>
                    <div class='add-button-table-bar'>
                        <div class='above-table-bar-element archive-article-wrapper'>
                            <div class='archive-label'>Архівні статті</div>
                            <div><input type='checkbox' id='archive-article' class='above-table-add__checkbox'
                                    onclick='archiveContractOnClick()'>
                            </div>
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
        <?php require("modal-windows/modal-window-add-article.php") ?>
        <?php require("modal-windows/modal-window-edit-article.php") ?>
    </div>
    <link href="../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $('select').select2();
    </script>
    <script src="index.js"></script>
    <script src="modal-windows/modal-window-add-article.js"></script>
    <script src="modal-windows/modal-window-edit-article.js"></script>
</body>

</html>