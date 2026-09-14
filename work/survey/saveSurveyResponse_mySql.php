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
include '../../../sweetandsour_conf.php';
include '../../includes/act_getDBConnection.php';

$sqlSaveResponse = "INSERT INTO survey_responses (taskID, otherTask, difficultyID, comments, IPAddress) VALUES (". $taskID . ", '" . $otherTask . "', " . $difficultyID . ", '" . $comments . "', '" . $IPAddress . "')";
$rs_response = $mysqli->query($sqlSaveResponse);
if($rs_response) {
	$responseID = $mysqli->insert_id;

	$sql  = "SELECT timestamp, survey_responses.taskID AS taskID, tasks.taskDesc AS taskDesc, otherTask, survey_responses.difficultyID AS difficultyID, difficulty.difficultyDesc as difficultyDesc, comments, IPAddress ";
	$sql .= "FROM survey_responses ";
	$sql .= "INNER JOIN tasks ON tasks.taskID = survey_responses.taskID ";
	$sql .= "INNER JOIN difficulty ON difficulty.difficultyID = survey_responses.difficultyID ";
	$sql .= "WHERE responseID=" . $responseID;

	$rs_surveyResponse = $mysqli->query($sql);
	$row = $rs_surveyResponse->fetch_array(MYSQLI_ASSOC);
	$timestamp = $row["timestamp"];
	$taskDesc = $row["taskDesc"];
	$difficultyDesc = $row["difficultyDesc"];
}
else {
	$taskDesc = "";
	$difficultyDesc = "";
	$timestamp = gmdate("Y-m-d H:i:s");
	$comments = "DATABASE INSERT FAILED!<br>" . $comments;
}
// Close the database connection
$mysqli->close();

//Create file with headers if it does not already exist
$file = "./logs/survey-responses.csv";
if (!file_exists($file)) {
	$headers = "Timestamp,Task,Other task,Difficulty,Comments,IP Address\n";
	file_put_contents($file, $headers);
}

// Write to file
$comments = str_replace(array("\r\n", "\r", "\n"), " ", $comments); // new lines mess with .csv files
$responseString = $timestamp . "," . $taskID . " (\"" . $taskDesc . "\")," . $otherTask . "," . $difficultyID . " (" . $difficultyDesc . "),\"" . $comments . "\"," . $IPAddress . "\n";
file_put_contents($file, $responseString, FILE_APPEND | LOCK_EX);
?>


