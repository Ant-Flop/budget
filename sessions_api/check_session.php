<?php


session_start();

if (isset($_SESSION['LAST_ACTIVITY_BUDGET']) && (time() - $_SESSION['LAST_ACTIVITY_BUDGET'] > 14400)) {
    // Последняя активность была более 4 часов назад
    session_unset();     // удалить переменные сессии
    session_destroy();   // уничтожить сессию

    unset($_SESSION["budget_session"]);
}
$_SESSION['LAST_ACTIVITY_BUDGET'] = time(); // обновить время последней активности


//$_SESSION['TIME_FIRST_ACTIVITY'] = 
//error_reporting(0);
if (isset($_SESSION["budget_session"])) {
    require_once("../../sessions_api/variable_session.php");
    switch (defineURL()) {
        case "budget-plan":
            if (!$financier_role && !$director_role)
                header('Location: ../../login');
            break;
        case "budget-plan-implementation":
            if (!$financier_role && !$director_role)
                header('Location: ../../login');
            break;
        case "budget-articles-directory":
            if (!$financier_role && !$director_role)
                header('Location: ../../login');
            break;
        case "budget-counterparties-directory":
            if (!$director_role && !$act_viewer_role && !$report_viewer_role)
                header('Location: ../../login');
            break;
        case "budget-planned-indicators":
            if (!$financier_role && !$director_role)
                header('Location: ../../login');
            break;
        case "budget-services-directory":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-events-name-directory":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-codes-directory":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-banks-directory":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-banks-register":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-fundholders-directory":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-payments-directory":
            if (!$financier_role)
                header('Location: ../../login');
            break;
        case "budget-reports":
            if (!$report_viewer_role)
                header('Location: ../../login');
            break;
            // case "budget-writing-off-costs":
            //     if(!$act_viewer_role)
            //         header('Location: ../../login');
            //     break;
        case "budget-instruction":
            break;
        default:
            header('Location: ../../login');
            break;
    }
} else {
    header('Location: ../../login');
}

function defineURL()
{
    $url_array = explode("/", $_SERVER['REQUEST_URI']);
    if ($url_array[count($url_array) - 1] === "")
        unset($url_array[count($url_array) - 1]);
    $name_page = $url_array[count($url_array) - 1];
    if (stristr($name_page, "."))
        $name_page =  stristr($name_page, ".", true);
    return $name_page;
}