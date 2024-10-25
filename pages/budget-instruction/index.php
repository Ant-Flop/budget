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
                <div class='text-3'>користувача ПК "Бюджет"</div>
            </div>
            <div class='sub-title text-2'></div>
            <div class='main-content'></div>
            <div class='side-bar'>
                <ul id='side-bar-ul'>
                    <li class='side-bar-item text-3 side-bar-item-selected' data-id='1'>Вступ</li>

                    <li class='side-bar-item text-3' data-id='2'>Опис ролей користувачів</li>
                    <li class='side-bar-item text-3' data-id='3'>Вхід у програму</li>
                    <li class='side-bar-item text-3' data-id='4'>Головна сторінка</li>
                    <ul class='side-bar-item-ul' id='side-bar-item-ul-4' hidden>
                        <li class='side-bar-subitem' data-id='4.1'>Головна сторінка</li>
                        <li class='side-bar-subitem' data-id='4.2'>Головна сторінка для директора за напрямком</li>
                        <li class='side-bar-subitem' data-id='4.3'>Головна сторінка для фінансиста</li>
                    </ul>
                    <li class='side-bar-item text-3' data-id='5'>Довідники</li>
                    <ul class='side-bar-item-ul' id='side-bar-item-ul-5' hidden>
                        <span>Довідники для директора за напрямком</span>
                        <li class='side-bar-subitem' data-id='5.1'>Довідник контрагентів</li>
                        <li class='side-bar-subitem' data-id='5.2'>Довідник статей бюджету</li>
                        <span>Довідники для фінансиста</span>
                        <li class='side-bar-subitem' data-id='5.3'>Довідник коду</li>
                        <li class='side-bar-subitem' data-id='5.4'>Довідник фондоутримувачів</li>
                        <li class='side-bar-subitem' data-id='5.5'>Довідник служб бюджету</li>
                        <li class='side-bar-subitem' data-id='5.6'>Довідник найменувань заходів</li>
                        <li class='side-bar-subitem' data-id='5.7'>Довідник статей бюджету</li>
                        <li class='side-bar-subitem' data-id='5.8'>Довідник банків</li>
                    </ul>
                    <li class='side-bar-item text-3' data-id='6'>Меню</li>
                    <ul class='side-bar-item-ul' id='side-bar-item-ul-6' hidden>
                        <span>Меню для директора за напрямком</span>
                        <li class='side-bar-subitem' data-id='6.1'>Планові показники</li>
                        <li class='side-bar-subitem' data-id='6.2'>План бюджету</li>
                        <li class='side-bar-subitem' data-id='6.3'>Виконання бюджету</li>
                        <span>Меню для фінансиста</span>
                        <li class='side-bar-subitem' data-id='6.4'>Планові показники</li>
                        <li class='side-bar-subitem' data-id='6.5'>План бюджету</li>
                        <li class='side-bar-subitem' data-id='6.6'>Виконання бюджету</li>
                        <li class='side-bar-subitem' data-id='6.7'>Реєстр банку</li>
                    </ul>
                    <li class='side-bar-item text-3' data-id='7'>Звіти</li>
                    <ul class='side-bar-item-ul' id='side-bar-item-ul-7' hidden>
                        <span>Звіти для директора за напрямком</span>
                        <li class='side-bar-subitem' data-id='7.1'>Реєстр витрат по договору</li>
                        <li class='side-bar-subitem' data-id='7.2'>Реєстр витрат по статті бюджету</li>
                        <span>Звіти для фінансиста</span>
                        <li class='side-bar-subitem' data-id='7.3'>Щоденка</li>
                    </ul>
                </ul>
            </div>
            <div class='foot-content'></div>
        </div>
    </div>
    <link href="../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
</body>

</html>