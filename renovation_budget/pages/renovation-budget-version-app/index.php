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
                    <li><span class="ul-li-point">•</span> Змінено відображення інформації в "Довіднику контрагентів".
                        Наразі відображаються лише відкриті договори</li>
                    <li><span class="ul-li-point">•</span> "Довідник договору". Змінено відображення договорів
                        відповідно "Довіднику контрагентів"</li>
                    <li><span class="ul-li-point">•</span> "Довідник договору". Створена можливість змінювати статтю
                        при редагуванні запису</li>
                </ul>
                <h2>Версія 1.2</h2>
                <ul>
                    <li><span class="ul-li-point">•</span>"Довідник договору". Створено новий фільтр - "Термін дії
                        договору".
                        У випадаючому списку - "Номер договору", відображаються договори, які відповідають обраному
                        терміну </li>
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