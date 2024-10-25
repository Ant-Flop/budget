<!DOCTYPE html>
<html lang="ua">
<?php require_once("../../templates/layout/head.php") ?>

<body>
    <div class='wrapper'>
        <div class='main'>
            <div class='sub-title text-2'>Інформація про зміни у поточній версії</div>
            <div class='main-content'>
                <h2>Версія 1.1</h2>
                <ul>
                    <li><span class="ul-li-point">•</span> Створено звіт "Списання витрат"</li>
                    <li><span class="ul-li-point">•</span> Створено звіти "Реєстр витрат по договору", "Реєстр витрат по
                        статті бюджету"</li>
                    <li><span class="ul-li-point">•</span> Створено нові ролі</li>
                    <li><span class="ul-li-point">•</span> Реалізовано пошук оплат в реєстрі банків на сторінці
                        "Виконання планового бюджету"</li>
                    <li><span class="ul-li-point">•</span> Додано функцію переносу статей бюджету з обраного року на
                        інший на сторінці "Планові показники"</li>
                    <li><span class="ul-li-point">•</span> Додано можливість редагувати списання витрат на сторінці
                        "Виконання планового бюджету"</li>
                    <li><span class="ul-li-point">•</span> Виконано редезайн сторінок</li>
                    <li><span class="ul-li-point">•</span> Створено можливість додати ознаку "З ПДВ" в модульному вікні
                        встановлення планових показників при створенні запису</li>
                    <li><span class="ul-li-point">•</span> На сторінці "Планові показники", реалізовано можливість при
                        створенні або редагуванні запису, підтягувати оплату по договору до статті бюджету, які належать
                        до розділу "Інвестпрограма" </li>
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