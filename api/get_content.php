<?php
include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

header("Content-Type:application/json");

if (isset($_GET['path']) && $_GET['path'] != "") {
	$path = $_GET["path"];

	$stmt = $mysqli->prepare("SELECT * FROM content WHERE path=?");
	$stmt -> bind_param('s', $path);
	$stmt -> execute();
	$result = $stmt->get_result();

	if ($mysqli->connect_errno) {
		$responseText = "No connection to DB. Error " . $mysqli->connect_errno . ": " . $mysqli->connect_error;
		response(NULL, NULL, 500, $responseText);
    exit();
	}
	else if ($mysqli->errno) {
		$responseText = "Database failure. Error: " . $mysqli->errno . ": " . $mysqli->error;
		response(NULL, NULL, 500, $responseText);
	}
	else if($result -> num_rows === 1) {
    while ($arr = $result->fetch_assoc()) {
			response($arr["path"], $arr["html"], 200, "Success");
		}
	
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

function response($path, $html, $response_code, $response_desc){
	$response["path"] = $path;
	$response["html"] = $html;
	$response['response_code'] = $response_code;
	$response['response_desc'] = $response_desc;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>