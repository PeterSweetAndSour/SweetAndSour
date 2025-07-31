<?php
// Block inputs larger than 3/5 characters to guard against SQLinjection
$limit = substr($_GET["limit"], 0, 3);
$offset = substr($_GET["offset"], 0, 5);

if($limit == "") {
	$limit = "250";
}
if($offset == "") {
	$offset = "0";
}

include '../../../sweetandsour_conf.php';
include '../../includes/act_getDBConnection.php';

$sql  = "SELECT timestamp, survey_responses.taskID AS taskID, tasks.taskDesc AS taskDesc, otherTask, survey_responses.difficultyID AS difficultyID, difficulty.difficultyDesc as difficultyDesc, comments, IPAddress ";
$sql .= "FROM survey_responses ";
$sql .= "INNER JOIN tasks ON tasks.taskID = survey_responses.taskID ";
$sql .= "INNER JOIN difficulty ON difficulty.difficultyID = survey_responses.difficultyID ";
$sql .= "ORDER BY timestamp DESC ";
$sql .= "LIMIT " . $limit . " ";
$sql .= "OFFSET " . $offset;

$rs_surveyResponses = $mysqli->query($sql);
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
	<p><a href="survey-responses.csv" download>Download survey responses</a> (in .csv format)</p>
	
	<?php
	if($rs_surveyResponses) { //Check that there is a result set
		$numRows = $rs_surveyResponses->num_rows;
	
		if($numRows > 0) { //Check that the result set contains more than zero rows.
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
					while ($row = $rs_surveyResponses->fetch_array(MYSQLI_ASSOC)) {
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
					?>
					</tbody>
			</table>
			<?php
		}
	}
	// Close the database connection
	$mysqli->close();
	?>

</body>
</html>