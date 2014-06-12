<html>
<head>
	<title>Global Rename</title>
</head>
<body>
<h1>Global rename</h1>
<?
// Prepare for upload to Amazon S3. Set pattern in rglob call to give some control over files selected.

// Set date, width and height for images in photos table
// and rename the files to append the file name with the modified date.
// Allows photos to be stored with "version" identifiers which are the file modified dates.

$dbserver   = "localhost";  //Isn't it interesting that this works on the real server?
$dbname     = "sweetand_sour";
$dbuser     = "sweetand_php";
$dbpassword = "U287RHmt";

$path = "C:\Internet\WWWRoot\sweetAndSour.org\wherewelive\images\house";

$connectionOK = false;
$dbconnection = @mysql_connect( $dbserver, $dbuser, $dbpassword );

if($dbconnection) {
	//Confirm connection to particular database is possible
	if(! @mysql_select_db($dbname) ) {
		echo "<p>Unable to connect to the " . $dbname . " database!</p>";
		exit();
	}
}
else {
	echo "<p>Unable to connect to the " . $dbserver . " server!</p>";
	exit();
} 

/**
 * @param int $pattern
 *  the pattern passed to glob()
 * @param int $flags
 *  the flags passed to glob()
 * @param string $path
 *  the path to scan
 * @return mixed
 *  an array of files in the given path matching the pattern.
 */
function rglob($pattern, $flags = 0, $path = '') {
    if (!$path && ($dir = dirname($pattern)) != '.') {
        if ($dir == '\\' || $dir == '/') {
					$dir = '';
				}
        return rglob(basename($pattern), $flags, $dir . '\\');
    }
    $paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
    $files = glob($path . $pattern, $flags);
    foreach ($paths as $p) {
			$files = array_merge($files, rglob($pattern, $flags, $p . '\\'));
		}
    return $files;
}

$aFiles = rglob('{*.gif,*.jpg}', GLOB_BRACE, $path); // No space after comma!

echo "<table border='1' cellspacing='0' cellpadding='3'><thead><tr>";
echo "<th>Index</th>";
echo "<th>Path</th>";
echo "<th>File</th>";
echo "<th>Date</th>";
echo "<th>Width</th>";
echo "<th>Height</th>";
echo "<th>SQL</th>";
echo "<th>Row updated</th>";
echo "<th>Match</th>";
echo "<th>New name</th>";
echo "</tr></thead><tbody>";

foreach ($aFiles as $pathAndFilename) {
	$dateStr = date ("Ymd", filemtime($pathAndFilename));
	$size = @getImageSize($pathAndFilename);
	$width = $size[0];
	$height = $size[1];
	$index = strrpos  ($pathAndFilename, "\\");
	if($index === false) {
		$index = -1;
	}
	$path = substr($pathAndFilename, 0, $index+1);
	$filename = substr($pathAndFilename, $index+1);
	echo "<tr><td>" .$index . "</td>";
	echo "<td>" .$path . "</td>";
	echo "<td>" .$filename . "</td>";
	echo "<td>" . $dateStr . "</td>";
	echo "<td>" . $width. "</td>";
	echo "<td>" . $height . "</td>";
	
	$sql_find = "SELECT * FROM photos WHERE photoName = '" . $filename . "'";
	$rs_find = @mysql_query($sql_find);
	if(mysql_affected_rows() == 1) {

// Insert date, width and height into table
$sql_update = <<<SQL1

	UPDATE photos
	SET version = '$dateStr', width = $width, height=$height
	WHERE photoName = '$filename';

SQL1;

		//$sql_update =  "UPDATE photos SET version = '" . $dateStr . "', width = " . $width . ", height= " . $height . "WHERE photoName = '" . $filename . "';";
		echo "<td>" . $sql_update . "</td>";
		
		$rs_update = @mysql_query($sql_update);
		
		echo "<td>" . mysql_affected_rows() . "</td>";

		// Rename the file to carry the date (if it hasn't already)
		$match = preg_match('/.*_[0-9]{8}\.(jpg|gif)/', $filename);
		echo "<td>" . $match . "</td>";
		
		$newPathAndFilename = $pathAndFilename;
		if(!$match) {
			$newPathAndFilename = preg_replace ('/\.(jpg|gif)/', "_" . $dateStr . ".$1", $pathAndFilename);
			rename($pathAndFilename, $newPathAndFilename);
		}
		echo "<td>" . $newPathAndFilename . "</td>";
		
	}
	else {
		echo "<td></td><td></td><td></td><td></td>";
	}
}


?>
</body>
</html>





















