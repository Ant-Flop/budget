<?php 
session_start();
require_once("../../templates/classes/db_local.php");
require_once("./variable_session.php");
$connect = new DB_connect();
$mysql = $connect->Connect();
$mysql->set_charset("utf8");
$mysql->query("INSERT INTO users_session_log (user_id, name_app, session, action, datetime) VALUES ('" . $user_id . "', '" . $name_app . "', 0, 'logout', '" . date("Y-m-d H:i:s") . "')");
$mysql->close();
unset($_SESSION["renovation_budget_session"]);
?>
<script src='logout.js'></script>