<?php
if(isset($_GET["limit"])) {
	// Block inputs larger than 3 characters to guard against SQLinjection but allow limit to 999.
	$limit = substr($_GET["limit"], 0, 3);
}
else {
	$limit = "250";
}

if(isset($_GET["offset"])) {
	// Block inputs larger than 3 characters to guard against SQLinjection but allow offset to 99,999.
	$offset = substr($_GET["offset"], 0, 5);
}
else {
	$offset = "0";
}

include './includes/getSQLServerDBConnection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Careers page intercept survey results</title>
	<link rel="shortcut icon" href="https://dc.gov/sites/default/files/favicon_0.ico" type="image/vnd.microsoft.icon" />
  <style>
		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 14px;
			--darkBlue: #395bc2;
			--lightBlue: #ebeff9;
		}
		table {
			border-collapse: collapse;
		}
		th, td {
			text-align: left;
			padding: 6px;
			border-right: 1px solid white;
		}
		th {
			background-color: var(--darkBlue);
			color: white;
			font-weight: normal;
		}
		tbody > tr {
			border-bottom: 1px solid #666;
		}
		tr:nth-child(even) > td {
			background-color: var(--lightBlue);
		}

	</style>
</head>

<body>
  <h1>Careers page intercept survey results</h1>

	<p>This page shows the most recent 250 survey responses with the newest at the top and you can download
		all results as a .csv file to import into Excel for detailed analysis. You can also change the displayed
		results by changing the URL: add &ldquo;?offset=250&rdquo; to see the next 250, &ldquo;?offset=500&rdquo;
		for the next set after that, and so on. (You can change the number displayed to 100 by adding 
		&ldquo;?offset=250&amp;limit=100&rdquo; for example.)
	</p>

	<p>
		The IP address identifies your computer to the outside world although since we are inside
		a network, I think all computers inside the building will have the same IP address.
	</p>

	<p>
		<a href="../logs/survey-responses.csv" download>Download survey responses</a> (in .csv format) to open in Excel.
		Unfortunately, Excel does not understand the timestamp format so, by default, will display "2025-05-27 10:49:28.220" as "49:28.2"! 
		In theory, setting the column to data type "Text" should make Excel display the data as-is but that does not work. 
		The best solution is to:
	</p>
	<ol>
		<li>Select column A</li>
		<li>Click the "expand" arrow at the bottom-right of the data format section of the header</li>
		<li>Select "Custom"</li>
		<li>Under "Type", click any of the suggested formats under "General" and then specify the new format as "yyyy-MM-dd hh:mm:ss".</li>
		<li>Click "OK".</li>
	</ol>
	
	<?php
	$sql  = "SELECT timestamp, survey_responses.taskID AS taskID, tasks.taskDesc AS taskDesc, otherTask, survey_responses.difficultyID AS difficultyID, difficulty.difficultyDesc as difficultyDesc, comments, IPAddress ";
	$sql .= "FROM survey_responses ";
	$sql .= "INNER JOIN tasks ON tasks.taskID = survey_responses.taskID ";
	$sql .= "INNER JOIN difficulty ON difficulty.difficultyID = survey_responses.difficultyID ";
	$sql .= "ORDER BY timestamp DESC ";
	$sql .= "OFFSET " . $offset . " ROWS ";
	$sql .= "FETCH NEXT " . $limit . " ROWS ONLY";


	//try {
		$conn = OpenConnection();
		$getSurveyResponses = sqlsrv_query($conn, $sql);
		if ($getSurveyResponses == FALSE) {
			//var_dump(sqlsrv_errors());
			exit("Sorry. Something went drastically wrong.");
		}
		?>
			<table>
				<thead>
					<tr>
						<th>Timestamp</th>
						<th>Task</th>
						<th>Other task</th>
						<th>Difficulty</th>
						<th>Comments</th>
						<th>IP Address</th>
					</tr>
				</thead>
				<tbody>			
					<?php
						while($row = sqlsrv_fetch_array($getSurveyResponses, SQLSRV_FETCH_ASSOC)) {
							$timestamp = $row["timestamp"];
							$taskID = $row["taskID"];
							$taskDesc = $row["taskDesc"];
							$otherTask = $row["otherTask"];
							$difficultyID = $row["difficultyID"];
							$difficultyDesc = $row["difficultyDesc"];
							$comments = $row["comments"];
							$comments = str_replace(array("\r\n", "\r", "\n"), "<br>", $comments); // new lines need break tag for html display
							$IPAddress = $row["IPAddress"];
							?>
								<tr>
									<td><?= $timestamp ?></td>
									<td><?= $taskID ?> (<?= $taskDesc ?>)</td>
									<td><?= $otherTask ?></td>
									<td><?= $difficultyID ?> (<?= $difficultyDesc ?>)</td>
									<td><?= $comments ?></td>
									<td><?= $IPAddress ?></td>
								</tr>
							<?php
						}
						sqlsrv_free_stmt($getSurveyResponses);
						sqlsrv_close($conn);
					?>
				</tbody>
			</table>
		<?php
	//}
	//catch(Exception $e) {
	//		echo("Error!");
	//}
	?>
</body>
</html>