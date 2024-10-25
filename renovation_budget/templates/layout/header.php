<?php require_once("../../sessions_api/variable_session.php") ?>
<div class="header unselectable">
    <div class="header-navbar">
        <button class="header-navbar__button">Меню
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="header-navbar-content">
            <?php
            if ($director_role)
                echo "<a href='../treaty-directory/'>Довідник договору</a>";
            if ($director_role)
                echo "<a href='../counterparties-directory/'>Довідник контрагентів</a>";
            if ($director_role)
                echo "<a href='../act-card/'>Картка акту</a>";
            if ($director_role)
                echo "<a href='../fact-of-contracts/'>Факт по договорах</a>";
            if ($director_role)
                echo "<a href='../fact-of-articles/'>Факт по статтям</a>";
            ?>
        </div>
    </div>
    <div class="header-navbar">
        <button class="header-navbar__button">Звіти
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="header-navbar-content">
            <?php
            if ($director_role)
                echo "<a href='../common-report/'>Загальний звіт</a>";
            if ($director_role)
                echo "<a href='../writing-off-costs-report/'>Списання витрат</a>";
            ?>
        </div>
    </div>
    <div class="header-navbar">
        <a target="_blank" href="../renovation-budget-instruction/">Інструкція</a>
    </div>
    <div class="header-navbar">
        <div class="header-clean-cash" onclick="clearFiltersReloadOnClick()">
            <img src="../../templates/images/clean_cash.png" id="header-clean-cash__img" alt="clean_cash" title="Очистка фільтрів сторінки з перезагрузкою" />
        </div>
    </div>
    <div class="header-logo">
        <img class="header-logo__img" src="../../templates/images/favicon.png" alt="Budget">
        <text class="header-logo__text">ПЗ Бюджет ремонтів ІТ</text>
    </div>
    <div class="header-navbar">
        <button class="header-navbar__button"><?php echo $user_name ?>
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="header-navbar-content header-navbar-content-right">
            <a href="../../sessions_api/logout.php">Вийти</a>
        </div>
    </div>
</div>