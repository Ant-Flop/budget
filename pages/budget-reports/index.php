<?php require_once('../../sessions_api/check_session.php') ?>
<!DOCTYPE html>
<html lang='ua'>
<?php require_once('../../templates/layout/head.php') ?>

<body>
    <div class='wrapper'>
        <?php require_once('../../templates/layout/header.php') ?>
        <div class='main'>
            <div class='title text-2'>Звіти</div>
            <div class='sub-title text-2'></div>
            <div class='main-content'></div>
            <div class='side-bar'>
                <ul id='side-bar-ul'>
                    <?php
                        if($fundholder_id == 7) {
                            echo "<li class='side-bar-item text-3' data-id='1'>Реєстр витрат по договору</li>";
                            echo "<li class='side-bar-item text-3' data-id='2'>Реєстр витрат по статті бюджету</li>";
                        }
                    ?>
                    <li class='side-bar-item text-3' data-id='3'>Щоденка</li>
                </ul>
            </div>
        </div>
        <?php require_once('../../templates/layout/footer.php') ?>
    </div>
    <link href='../../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css' rel='stylesheet'>
    <script src='../../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js'></script>
    <script>
        $('select').select2();
    </script>
    <script src='index.js'></script>
</body>

</html>