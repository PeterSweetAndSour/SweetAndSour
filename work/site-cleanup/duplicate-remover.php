<?php
// Specify files in current directory to process
$agency = "dob";
$fileNameInput = $agency . ".dc.gov analytics.csv";
$fileNameOutput = strtoupper($agency) . "_duplicates_removed.csv";

$metaData = [];
$headers = ""; // Page title, URL, number of views
$first3Fields = [];
$firstRowThisURL = [];
$rawDataArray = [];
$lineNumber = 1;
$processedDataArray = [];

// Open file
$fileHandle = fopen($fileNameInput, "r") or die("ERROR: Cannot open the file.");;

function get_all_lines($fileHandle) { 
	while (!feof($fileHandle)) {
			yield fgets($fileHandle);
	}
}


/* 
Find next comma that will define a field in a .csv file. 
Must cope with escaped double-quote marks:
   "New look, new protection, same you! | dmv",/node/1660916,0,1,0,12,1,0,0
   "Diện mạo mới, An ninh mới, Vẫn là bạn! | dmv",/service/new-look-new-security-same-you,0,1,0,33,1,0,0
   Services | dmv,"/services/,contest-tickets",1,1,1,0,1,0,0
   Services | dmv,"/services/ajax/page_view_timing/page_action/API/modules/comment/API/start/url(""https",1,1,1,12,3,0,0
	 Services | Page 2 | dmv,"/services/API/start/API/noticeError/url(""",26,21,1.2380952380952381,23.428571428571427,72,0,0
	 "Services | dmv,"/services/,contest-tickets",1,1,1,0,1,0,0
*/
function positionOfNextCommaOutsideQuotes($line, $startIndex) {
	$posNextComma = 0;

	if(substr($line, $startIndex, 1) === '"') { // Field is double-quoted because of commas or double-quotes within
		$matches = [];
		preg_match('/[^"]"[^"]|"""/', $line, $matches, PREG_OFFSET_CAPTURE, $startIndex);
		$posNextComma = strpos($line, ",", $matches[0][1] + 1); // Match could be on ," so have to move one forweard
	}
	else {
		$posNextComma = strpos($line, ",", $startIndex);
	}

	return $posNextComma;
}


// Cycle through lines in the file
foreach (get_all_lines($fileHandle) as $line) {
	// Copy lines 7, 8 and 9 - start with # to indicate comments.
	if($lineNumber >= 7 and $lineNumber <= 9) {
		array_push($metaData, $line);
	}

	// Copy the headers
	else if($lineNumber == 10) {
		$matches = [];
		preg_match_all('/,/', $line, $matches, PREG_OFFSET_CAPTURE);
		$posThirdComma = $matches[0][2][1];
		$headers = substr($line, 0, $posThirdComma);
	}

	// Dump text after 3rd comma, ignoring commas within double-quotes
	else if ($lineNumber >= 11) {
		$posFirstComma = positionOfNextCommaOutsideQuotes($line, 0);
		$posSecondComma = positionOfNextCommaOutsideQuotes($line, $posFirstComma + 1);
		$posThirdComma = positionOfNextCommaOutsideQuotes($line, $posSecondComma + 1);

		$pageTitle = substr($line, 0, $posFirstComma);
		$url = substr($line, $posFirstComma + 1, $posSecondComma - $posFirstComma - 1);
		$views  = substr($line, $posSecondComma + 1, $posThirdComma - $posSecondComma - 1);

		$first3FieldsArray = array($pageTitle, $url, $views);
		$rawDataArray[] = $first3FieldsArray;
	}

	$lineNumber++;
}
// Close the file
fclose($fileHandle);

// Sort $rawDataArray on URL
usort($rawDataArray, function ($a, $b) {
	return $a[1] <=> $b[1]; // The spaceship operator (<=>)
});

// Cycle through rows looking for consecutive matching URLs.
// If the same as the last, combine the views and move on;
// if different, copy the last to the $processedDataArray array
$lastRow = $rawDataArray[0];
for($j=1; $j < count($rawDataArray); $j++) {
	$thisRow = $rawDataArray[$j];
	$thisUrl = $thisRow[1];
	$lastUrl = $lastRow[1];

	if($thisUrl === $lastUrl) {
		$viewsThisRow = $thisRow[2];
		if(is_string($lastRow[2]) or is_string($viewsThisRow)){
			$a=3;
		}
		$lastRow[2] += $viewsThisRow;
		
		if($j == count($rawDataArray) - 2) {
			$processedDataArray[] = $lastRow;
		}
	}
	else {
		$processedDataArray[] = $lastRow;
		$lastRow = $thisRow;

		if($j == count($rawDataArray) - 2) {
			$processedDataArray[] = $thisRow;
		}
	}
}

$countRaw = count($rawDataArray);
$countProcessed = count($processedDataArray);

// Write out new file
$outputStr = "";
$newLine = "\n";

$outputStr .= implode($newLine, $metaData) . $newLine;
$outputStr .= $headers . $newLine . $newLine;
foreach($processedDataArray as $thisRow) {
	$outputStr .= implode(",", $thisRow) . $newLine;
}
file_put_contents($fileNameOutput, $outputStr);
?>

<?= $countRaw ?>  <?= $countProcessed ?> 


