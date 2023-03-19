<?php
/* 
  First version to returns HTML. May do JSON version after and move act_constructMenu logic to client.
   Will be getting requested with URL like /api/get_menu.php?path=home/welcome
*/


// Capture the fuseaction to give to qry_getMenuID
if(isset($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "";
}

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';
include '../includes/qry_getMenuID.php'; 
include '../includes/qry_menu.php'; 
include '../includes/act_constructMenu.php';

$response['responseCode'] = $responseCode;   // qry_getMenuID.php
$response['responseDesc'] = $responseDesc;   // qry_getMenuID.php
$response["selectedID"] = $selectedID;       // qry_getMenuID.php
$response["parentID"] = $parentID;           // qry_getMenuID.php
$response["grandparentID"] = $grandparentID; // qry_getMenuID.php
//$response["menuHTML"] = $str_menuHTML;       // act_constructMenu.php - not used
$response["menuData"] = $arr_menuData;       // qry_menu.php

$jsonResponse = json_encode($response, JSON_FORCE_OBJECT);

header("Content-Type:application/json");
echo $jsonResponse;
?>