<?php

function OpenConnection() {
	include '../../../interceptSurveyConfig.php';

	$connectionOptions = array("Database"=>"InterceptSurvey", "Uid"=>$uid, "PWD"=>$pwd, "TrustServerCertificate"=>true, "ReturnDatesAsStrings"=>true);
	$conn = sqlsrv_connect($serverName, $connectionOptions);
	if($conn == false) {
		var_dump(sqlsrv_errors());
		exit("Sorry. Unable to connect to database.");
	}

	return $conn;
}
?>