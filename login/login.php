<?php

ini_set('session.gc_maxlifetime', 14400); // 4 часа в секундах
ini_set('session.cookie_lifetime', 14400); // 4 часа в секундах
ini_set('session.gc_probability', 1); // Вероятность запуска GC
ini_set('session.gc_divisor', 100); // Д
session_start();
error_reporting(E_ERROR | E_PARSE);
require_once("../templates/classes/db_local.php");
$request = file_get_contents("php://input");
$data = json_decode($request, true);
$login = $data["login"];
$password = $data["password"];
$name_app = $data["nameApp"];

$local_verify = GetUserFromLocalDB($login, $password, false, false);
if (!$local_verify)
    $local_verify = CheckUserInAD($login, $password);
RoutUser($name_app, $local_verify);

function GetUserFromLocalDB($login, $password, $check_password, $msdb_info)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();

    $mysql->set_charset("utf8");
    $condition_password = "";
    if (!$check_password)
        $condition_password =  "and u.password = '$password'";

    $result = $mysql->query("SELECT u.id as user_id, u.user, f.id as fundholder_id, f.name as fundholder, f.short_name, f.additional_name, 
                                    ur.admin_role, ur.financier_role, ur.director_role, ur.fin_dir_role, ur.act_viewer_role, ur.report_viewer_role, ur.start_page_budget 
                                FROM users_roles ur JOIN users u ON u.users_roles_id = ur.id 
                                JOIN fundholders_directory f ON f.id = u.fundholder_directory_id 
                                WHERE u.user = '$login' " . $condition_password);
    $result_fetch = $result->fetch_assoc();


    if ($result_fetch == "") {
        return false;
    } else
        return [
            "user_id" => $result_fetch["user_id"],
            "user" => $result_fetch["user"],
            "user_name" => $msdb_info === false ? $result_fetch["user"] : $msdb_info["name"],
            "fundholder_id" => $result_fetch["fundholder_id"],
            "fundholder" => $result_fetch["fundholder"],
            "fundholder_sn" => $result_fetch["short_name"],
            "fundholder_an" => $result_fetch["additional_name"],
            "admin_role" => $result_fetch["admin_role"] === "0" ? false : true,
            "financier_role" => $result_fetch["financier_role"] === "0" ? false : true,
            "director_role" => $result_fetch["director_role"] === "0" ? false : true,
            "fin_dir_role" => $result_fetch["fin_dir_role"] === "0" ? false : true,
            "act_viewer_role" => $result_fetch["act_viewer_role"] === "0" ? false : true,
            "report_viewer_role" => $result_fetch["report_viewer_role"] === "0" ? false : true,
            "start_page" => $result_fetch["start_page_budget"],
        ];
    $mysql->close();
}

function CheckUserInAD($login, $password)
{
    $adServer = "ldap://10.109.78.98";
    //$adServer = "ldap://10.109.68.99";
    $portNum = 389;

    $ldap = ldap_connect($adServer, $portNum);
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

    $options_login = [$login, 'soe/' . $login, $login . "@soe"];

    foreach ($options_login as $element) {
        $bind = @ldap_bind($ldap, $element, $password);
        if ($bind) break;
    }



    if (!$bind) {
        echo json_encode(["status" => false, "text_error" => "Невірний логін або пароль!"]);
        exit();
    }

    if (strpos($login, '/')) {
        $login = stristr($login, "/");
        $login = str_replace("/", '', $login);
    } elseif (strpos($login, '@')) {
        $login = stristr($login, '@', true);
    }
    return GetUserFromLocalDB($login, false, true, GetUserFromMSDB($login));
}

function GetUserFromMSDB($login)
{
    $dsn = "sqlsrv:Server=sql-x125;Database=bar; Encrypt=false; TrustServerCertificate=true";
    $server_user = "site";
    $server_password = "5867sM393ms93in5w8";
    try {
        $conn = new PDO($dsn, $server_user, $server_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        die(print_r($e->getMessage()));
    }

    $user_email = $login . "@soe.com.ua";
    $info_user_query = "SELECT Crd.SNAME AS name, crd.email0 AS email, crd.ActiveDirLogin AS ad, CRD.SPOST AS post, sFullSubDiv AS section
                FROM StfCrd AS Crd
                LEFT JOIN StfListStaff AS LS
                ON LS.Code = Crd.LPost
                LEFT JOIN StfSubDiv AS ssd
                ON ssd.CODE = Crd.PARENT
                WHERE
                ISNULL(Crd.IDISMISS,0) <> 1
                AND ISNULL(Crd.DTDISMISS,0) = 0 AND crd.email0 = '$user_email'
                ORDER BY Crd.SNAME";
    $getResults = $conn->prepare($info_user_query);
    $getResults->execute();
    $results = $getResults->fetch(PDO::FETCH_BOTH);
    return [
        "name" => $results["name"],
        "email" => $results["email"],
        "ad_login" => $results["ad"],
        "post" => $results['post'],
        "section" => $results["section"]
    ];
}

function CreateSessionLogInDB($name_app, $user_id)
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $mysql->query("INSERT INTO users_session_log (user_id, name_app, session, action, datetime) VALUES ('" . $user_id . "', '" . $name_app . "', 1, 'login', '" . date("Y-m-d H:i:s") . "')");
    $mysql->close();
}

function GetAppsInfo()
{
    $connect = new DB_connect();
    $mysql = $connect->Connect();
    $mysql->set_charset("utf8");
    $array_data = [];
    $result = $mysql->query("SELECT a.id, a.name, b.fundholders_directory_id FROM apps a
                             LEFT JOIN apps_access_rights b ON b.apps_id = a.id
                             LEFT JOIN fundholders_directory c ON c.id = b.fundholders_directory_id");
    while ($row = $result->fetch_assoc()) {
        array_push($array_data, [
            "id" => $row["id"],
            "name" => $row["name"],
            "fundholder_id" => $row["fundholders_directory_id"],
        ]);
    }
    $mysql->close();
    return $array_data;
}

function CheckingAppAccess($app_name, $fundholder_id)
{
    $apps_info = GetAppsInfo();
    foreach ($apps_info as $key => $element) {
        if ($element["name"] == $app_name && $element["fundholder_id"] == $fundholder_id)
            return true;
    }
    return false;
}

function RoutUser($name_app, $local_verify)
{
    if (!$local_verify) {
        echo json_encode(["status" => false, "text_error" => "Невірний логін або пароль!"]);
        exit();
    } else $user_info = $local_verify;
    switch ($name_app) {
        case "budget":
            if (CheckingAppAccess($name_app, $user_info["fundholder_id"])) {
                $_SESSION["budget_session"] = [
                    "session" => true,
                    "name_app" => $name_app,
                    "route" => "../pages/" . $user_info['start_page'] . "/",
                    "user" => [
                        "user_id" => $user_info["user_id"],
                        "user" => $user_info["user"],
                        "user_name" => $user_info["user_name"],
                        "fundholder_id" => $user_info["fundholder_id"],
                        "fundholder" => $user_info["fundholder"],
                        "fundholder_sn" => $user_info["fundholder_sn"],
                        "fundholder_an" => $user_info["fundholder_an"],
                        "admin_role" => $user_info["admin_role"],
                        "financier_role" => $user_info["financier_role"],
                        "director_role" => $user_info["director_role"],
                        "fin_dir_role" => $user_info["fin_dir_role"],
                        "act_viewer_role" => $user_info["act_viewer_role"],
                        "report_viewer_role" => $user_info["report_viewer_role"],
                    ]
                ];
                CreateSessionLogInDB($name_app, $user_info["user_id"]);
                echo json_encode(["status" => true, "session_info" => $_SESSION["budget_session"]]);
            } else echo json_encode(["status" => false, "text_error" => "Відмовленно в доступі!"]);
            break;
        case "renovation_budget":
            if (CheckingAppAccess($name_app, $user_info["fundholder_id"]) && $user_info["director_role"]) {
                $_SESSION["renovation_budget_session"] = [
                    "session" => true,
                    "name_app" => $name_app,
                    "route" => "../renovation_budget/pages/fact-of-contracts/",
                    "user" => [
                        "user_id" => $user_info["user_id"],
                        "user" => $user_info["user"],
                        "user_name" => $user_info["user_name"],
                        "fundholder_id" => $user_info["fundholder_id"],
                        "fundholder" => $user_info["fundholder"],
                        "fundholder_sn" => $user_info["fundholder_sn"],
                        "fundholder_an" => $user_info["fundholder_an"],
                        "admin_role" => $user_info["admin_role"],
                        "director_role" => $user_info["director_role"],
                    ]
                ];
                CreateSessionLogInDB($name_app, $user_info["user_id"]);
                echo json_encode(["status" => true, "session_info" => $_SESSION["renovation_budget_session"]]);
            } else echo json_encode(["status" => false, "text_error" => "Відмовленно в доступі!"]);
            break;
        default:
            break;
    }
}