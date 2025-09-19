<?php
/* saveSurveyResponse.php

Saves to a database and also .csv text file for download to allow user to import to Excel

=>| Form.taskID
=>| Form.otherTask
=>| Form.difficultyID
=>| Form.comments
=>| Form.IPAddress

|=> ?
*/

$taskID = htmlspecialchars(substr($_POST["taskID"], 0, 1));
$otherTask = htmlspecialchars(substr($_POST["otherTask"], 0, 64));
$difficultyID = htmlspecialchars(substr($_POST["difficultyID"], 0, 1));
$IPAddress = htmlspecialchars(substr($_POST["IPAddress"], 0, 15));
$comments = htmlspecialchars(substr($_POST["comments"], 0, 1000));

// Save to database
include './includes/getSQLServerDBConnection.php';

//try {
	$conn = OpenConnection();

	// Insert query
	$tsql  = "SET NOCOUNT ON ";
	$tsql .= "INSERT INTO survey_responses (taskID, otherTask, difficultyID, comments, IPAddress, siteID) ";
	$tsql .= "VALUES (". $taskID . ", '" . $otherTask . "', " . $difficultyID . ", '" . $comments . "', '" . $IPAddress . "', 1) ";

	// Now get the record that was inserted with descriptions from joined tables using SCOPE_IDENTITY() that gets ID of newly inserted record so that we can write to a file
	$tsql .= "SET NOCOUNT OFF ";
	$tsql .= "SELECT timestamp, survey_responses.taskID AS taskID, tasks.taskDesc AS taskDesc, otherTask, survey_responses.difficultyID AS difficultyID, difficulty.difficultyDesc as difficultyDesc, comments, IPAddress ";
	$tsql .= "FROM survey_responses ";
	$tsql .= "INNER JOIN tasks ON tasks.taskID = survey_responses.taskID ";
	$tsql .= "INNER JOIN difficulty ON difficulty.difficultyID = survey_responses.difficultyID ";
	$tsql .= "WHERE responseID=SCOPE_IDENTITY()";
	
	$tsqlResponse = sqlsrv_query($conn, $tsql);
	if($tsqlResponse == FALSE) {
		var_dump(sqlsrv_errors());

		$taskDesc = "";
		$difficultyDesc = "";
		$timestamp = gmdate("Y-m-d H:i:s");
		$comments = "DATABASE INSERT FAILED!<br>" . $comments;
	}
	else {
		while($row = sqlsrv_fetch_array(      $tsqlResponse                  , SQLSRV_FETCH_ASSOC)) {
			$taskDesc = $row["taskDesc"];
			$difficultyDesc = $row["difficultyDesc"];
			$timestamp = $row["timestamp"];
		}
	}
	sqlsrv_free_stmt($tsqlResponse);
	sqlsrv_close($conn);

	//Create file with headers if it does not already exist
	$file = "./logs/survey-responses.csv";
	if (!file_exists($file)) {
		$headers = "Timestamp,Task,Other task,Difficulty,Comments,IP Address\n";
		file_put_contents($file, $headers);
	}

	// Write to file
	$comments = str_replace(array("\r\n", "\r", "\n"), " ", $comments); // new lines in comments mess with .csv files so remove them
	$responseString = $timestamp . "," . $taskID . " (\"" . $taskDesc . "\")," . $otherTask . "," . $difficultyID . " (" . $difficultyDesc . "),\"" . $comments . "\"," . $IPAddress . "\n";
	file_put_contents($file, $responseString, FILE_APPEND | LOCK_EX);

//}
//catch(Exception $e) {
//	echo("Error inserting response into database!");
//}
?>


