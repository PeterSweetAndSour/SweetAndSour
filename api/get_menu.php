<?php
// First version to returns HTML. May do JSON version after and move act_constructMenu logic to client.
// Will be getting requested with URL like /api/get_menu.php?path=home/welcome

// Capture the fuseaction to give to qry_getMenuID
$url = $_SERVER['REQUEST_URI'];
$urlArray = explode("/", $url);
$fuseAction = end($urlArray); // Combining this line and the one above gives "Only variables should be passed by reference"

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';
include '../includes/qry_getMenuID.php'; 
include '../includes/qry_menu.php'; 
include '../includes/act_constructMenu.php';

$response["selectedID"] = $selectedID;
$response["parentID"] = $parentID;
$response["grandparentID"] = $grandparentID;
$response["menuHTML"] = $str_menuHTML;
$response['response_code'] = $responseCode;
$response['response_desc'] = $responseDesc;

$json_response = json_encode($response);

header("Content-Type:application/json");
echo $json_response;
?>