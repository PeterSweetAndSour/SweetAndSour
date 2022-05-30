<?php
include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

header("Content-Type:application/json");

if (isset($_GET['path']) && $_GET['path'] != "") {
	$path = $_GET["path"];
	$sql = "SELECT * FROM content WHERE path=?";
	$stmt = $mysqli->prepare($sql);
	$stmt -> bind_param('s', $path);
	$stmt -> execute();
	$result = $stmt->get_result();

	if ($mysqli->connect_errno) {
		$responseDesc = "No connection to DB. Error " . $mysqli->connect_errno . ": " . $mysqli->connect_error;
		response(NULL, NULL, 500, $responseDesc);
    exit();
	}
	else if ($mysqli->errno) {
		$responseDesc = "Database failure. Error: " . $mysqli->errno . ": " . $mysqli->error;
		response(NULL, NULL, 500, $responseDesc);
	}
	else if($result -> num_rows === 1) {
    while ($arr = $result->fetch_assoc()) {
			response($arr["path"], $arr["html"], 200, "Success");
		}
		// this didn't work
		//$stmt->store_result();
		//$stmt->bind_result($path, $html);
		//$stmt->fetch();
	}
	else {
		response(NULL, NULL, 200, "No Record Found");
	}
	$stmt -> close();
}
else {
	response(NULL, NULL, 400, "Invalid Request");
}

function response($path, $html, $responseCode, $responseDesc){
	$response["path"] = $path;
	$response["html"] = $html;
	$response['response_code'] = $responseCode;
	$response['response_desc'] = $responseDesc;
	
	$jsonResponse = json_encode($response);
	echo $jsonResponse;
}
?>