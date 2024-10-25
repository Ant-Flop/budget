<?php
session_start();

if (isset($_SESSION['LAST_ACTIVITY_RENOVATION_BUDGET']) && (time() - $_SESSION['LAST_ACTIVITY_RENOVATION_BUDGET'] > 14400)) {
    // Последняя активность была более 4 часов назад
    session_unset();     // удалить переменные сессии
    session_destroy();   // уничтожить сессию

    unset($_SESSION["renovation_budget_session"]);
}
$_SESSION['LAST_ACTIVITY_RENOVATION_BUDGET'] = time(); // обновить время последней активности

if (isset($_SESSION["renovation_budget_session"])) {
    require_once("../../sessions_api/variable_session.php");
    switch (defineURL()) {
        case "treaty-directory":
            if (!$director_role)
                header('Location: ../../../login');
            break;
        case "counterparties-directory":
            if (!$director_role)
                header('Location: ../../../login');
            break;
        case "act-card":
            if (!$director_role)
                header('Location: ../../../login');
            break;
        case "fact-of-contracts":
            if (!$director_role)
                header('Location: ../../../login');
            break;
        case "fact-of-articles":
            if (!$director_role)
                header('Location: ../../../login');
            break;
        case "common-report":
            if (!$director_role)
                header('Location: ../../../login');
            break;
        case "writing-off-costs-report":
            if (!$director_role)
                header('Location: ../../login');
            break;

        case "renovation-budget-instruction":
            if (!$director_role)
                header('Location: ../../login');
            break;
        default:
            header('Location: ../../../login');
            break;
    }
} else {
    header('Location: ../../../login');
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