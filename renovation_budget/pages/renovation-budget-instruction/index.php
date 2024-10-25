<?php require_once("../../sessions_api/check_session.php") ?>
<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class='wrapper'>
        <div class='main'>
            <div class='title'>
                <div class="title-logo">
                    <img class="icon-logo__img wrapper__img" src="../../templates/images/favicon.png" alt="Budget">
                </div>
                <div class='text-2'>Інструкція</div>
                <div class='text-3'>користувача ПЗ "Бюджет ремонти ІТ"</div>
            </div>
            <div class='sub-title text-2'></div>
            <div class='main-content'></div>
            <div class='side-bar'>
                <ul id='side-bar-ul'>
                    <li class='side-bar-item text-3 side-bar-item-selected' data-id='1'>Вступ</li>
                    <li class='side-bar-item text-3' data-id='2'>Меню</li>
                    <ul class='side-bar-item-ul' id='side-bar-item-ul-2' hidden>
                        <span>Меню</span>
                        <li class='side-bar-subitem' data-id='2.1'>Довідник договору</li>
                        <li class='side-bar-subitem' data-id='2.2'>Довідник контрагентів</li>
                        <li class='side-bar-subitem' data-id='2.3'>Картка акту</li>
                        <li class='side-bar-subitem' data-id='2.4'>Факт по договорах</li>
                        <li class='side-bar-subitem' data-id='2.5'>Факт по статтям</li>
                    </ul>
                    <li class='side-bar-item text-3' data-id='3'>Звіти</li>
                    <ul class='side-bar-item-ul' id='side-bar-item-ul-3' hidden>
                        <span>Звіти</span>
                        <li class='side-bar-subitem' data-id='3.1'>Загальний звіт</li>
                        <li class='side-bar-subitem' data-id='3.2'>Списання витрат</li>
                    </ul>
                    <li class='side-bar-item text-3' data-id='4'>Очистка фільтрів з перезагрузкою</li>
                </ul>
            </div>
            <div class='foot-content'></div>
        </div>
    </div>
    <link href="../../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $('select').select2();
    </script>
    <script src="index.js"></script>
</body>

</html>