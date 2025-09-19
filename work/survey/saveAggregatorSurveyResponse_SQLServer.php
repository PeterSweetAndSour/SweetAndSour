<?php
/* saveAggregatorSurveyResponse.php

Saves to a database and also .csv text file for download to allow user to import to Excel

=>| Form.foundInfo
=>| Form.comments
=>| Form.IPAddress

*/
$foundInfo = htmlspecialchars(substr($_POST["foundInfo"], 0, 3));
$comments = htmlspecialchars(substr($_POST["comments"], 0, 1000));
$IPAddress = htmlspecialchars(substr($_POST["IPAddress"], 0, 15));

// Save to database
include './includes/getSQLServerDBConnection.php';

//try {
	$conn = OpenConnection();

	// Insert query
	$tsql  = "SET NOCOUNT ON ";
	$tsql .= "INSERT INTO survey_responses (foundInfo, comments, IPAddress, siteID) ";
	$tsql .= "VALUES ('" . $foundInfo . "', '" . $comments . "', '" . $IPAddress . "', 2) ";

	// Now get the record that was inserted using SCOPE_IDENTITY() so that we can get saved timestamp to write to a file
	$tsql .= "SET NOCOUNT OFF ";
	$tsql .= "SELECT timestamp, comments, IPAddress ";
	$tsql .= "FROM survey_responses ";
	$tsql .= "WHERE responseID=SCOPE_IDENTITY()";
	
	$tsqlResponse = sqlsrv_query($conn, $tsql);
	if($tsqlResponse == FALSE) {
		var_dump(sqlsrv_errors());

		$timestamp = gmdate("Y-m-d H:i:s");
		$comments = "DATABASE INSERT FAILED!<br>" . $comments;
	}
	else {
		while($row = sqlsrv_fetch_array($tsqlResponse, SQLSRV_FETCH_ASSOC)) {
			$timestamp = $row["timestamp"];
		}
		sqlsrv_free_stmt($tsqlResponse);
	}
	sqlsrv_close($conn);

	//Create file with headers if it does not already exist
	$file = "./logs/survey-responses-aggregator.csv";
	if (!file_exists($file)) {
		$headers = "Timestamp,Found Info,Comments,IP Address\n";
		file_put_contents($file, $headers); // does not seem to work
	}

	// Write to file
	$comments = str_replace(array("\r\n", "\r", "\n"), " ", $comments); // new lines in comments mess with .csv files so remove them
	$responseString = $timestamp . ",\"" . $foundInfo . ",\"" . $comments . "\"," . $IPAddress . "\n";
	file_put_contents($file, $responseString, FILE_APPEND | LOCK_EX);

//}
//catch(Exception $e) {
//	echo("Error inserting response into database!");
//}
?>


