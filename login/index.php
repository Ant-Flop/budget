<!DOCTYPE html>
<html lang="ua">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="../templates/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../templates/layout/header.css" type="text/css">
    <link rel="stylesheet" href="../templates/styles/style.css" type="text/css">
    <link rel="stylesheet" href="style.css" type="text/css">
    <link rel="stylesheet" href="../templates/layout/footer.css" type="text/css">
    <script src="../templates/libs/jquery-3.6.1/dist/jquery.min.js"></script>
    <title>ПК Бюджет</title>
</head>

<body>
    <div class="wrapper">
        <div class="main unselectable">
            <div class="login-form">
                <div class="login-form-header">
                    <img class="login-form__img" src='../templates/images/favicon.png' alt='Budget'>
                    <select class="login-form__select" id="name-app">
                        <option value="budget" selected>Бюджет</option>
                        <option value="renovation_budget">Бюджет ремонти ІТ</option>
                    </select>
                    <div class="login-form-title">АТ Сумиобленерго</div>
                </div>
                <div class="login-form-footer">
                    <input type="text" placeholder="Логін" id="login-input" name="login" required />
                    <input type="password" placeholder="Пароль" id="password-input" name="password" required />
                    <button id="login__button" class="login-form__button" onclick="loginOnClick()">Увійти</button>
                </div>
                <div class="login-form-warning-field" id="warning-field"></div>
            </div>
        </div>
        <div class="footer unselectable">
            <div class="footer-item-1">&copy; АТ Сумиобленерго, 2021-<?php echo date('Y'); ?> <a target="_blank"
                    href="../pages/budget-version-app" class="version-app">ver.1.1.</a></div>
            <div class="footer-item-2">Працює в <a target="_blank" href="https://www.soe.com.ua/"
                    style="text-decoration: none;">SOE</a></div>
        </div>
    </div>
    <link href="../templates/libs/select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="../templates/libs/select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('select').select2();
    </script>
    <script src="index.js"></script>
</body>

</html>