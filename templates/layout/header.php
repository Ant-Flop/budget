<?php require_once("../../sessions_api/variable_session.php") ?>
<div class="header unselectable">
    <?php 
		if(defineURL() == "budget-planned-indicators" || defineURL() ==  "budget-payments-directory") 
			echo "<div class='header-navbar'>
					<button class='header-navbar__button' onclick='goBack()'>← Назад</button>
				  </div>";
	?>
    <div class="header-navbar">
        <button class="header-navbar__button">Довідники
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="header-navbar-content">
            <?php 
				if($financier_role) 
					echo "<a href='../budget-codes-directory/'>Коду</a>";
				if($financier_role) 
					echo "<a href='../budget-fundholders-directory/'>Фондоутримувачів</a>";
				if($financier_role) 
					echo "<a href='../budget-services-directory/'>Служб Бюджету</a>";
				if($financier_role) 
					echo "<a href='../budget-events-name-directory/'>Найменувань Заходів</a>";
				if($director_role || $act_viewer_role || $admin_role || ($report_viewer_role && !$financier_role)) 
					echo "<a href='../budget-counterparties-directory/'>Контрагентів</a>";
				if($financier_role || $director_role) 
					echo "<a href='../budget-articles-directory/'>Статті Бюджету</a>";
				if($financier_role) 
					echo "<a href='../budget-banks-directory/'>Банків</a>";
			?>
        </div>
    </div>
    <div class='header-navbar'>
        <?php
			if($director_role || $act_viewer_role || $admin_role || $financier_role) {
				echo "<button class='header-navbar__button'>Меню
						<i class='fa fa-caret-down'></i>
					  </button>";
			};
		 ?>
        <div class="header-navbar-content">
            <?php 
				if($financier_role || $director_role) 
					echo "<a href='../budget-plan/'>План Бюджету</a>";
				if($financier_role || $director_role) 
					echo "<a href='../budget-plan-implementation/'>Виконання Планового Бюджету</a>";
				if($financier_role) 
					echo "<a href='../budget-banks-register/'>Реєстр Банку</a>";
				//if($act_viewer_role) 
					//echo "<a href='../budget-writing-off-costs/'>Списання витрат</a>";
			?>
        </div>
    </div>
    <div class="header-navbar">
        <?php 
			if($report_viewer_role)
				echo "<a href='../budget-reports/'>Звіти</a>";
		?>
    </div>
    <div class="header-navbar">
        <a target="_blank" href="../budget-instruction/">Інструкція</a>
    </div>
    <div class="header-logo">
        <img class="header-logo__img" src="../../templates/images/favicon.png" alt="Budget">
        <text class="header-logo__text">ПК Бюджет</text>
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
<script src='../../templates/layout/header.js'></script>