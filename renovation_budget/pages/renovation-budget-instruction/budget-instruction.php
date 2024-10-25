<?php
require_once("../../../templates/classes/db_local.php");
require_once("../../../sessions_api/check_session.php");

function GetMainContent($data)
{
    $path = "main-content-pages/" . $data['path'] . "/";
    include_once($path . "index.php");
}

$request = file_get_contents("php://input");
$data = json_decode($request, true);

switch ($data["typeRequest"]) {
    case "getMainContentRequest":
        GetMainContent($data);
        break;
    default:
        break;
}
