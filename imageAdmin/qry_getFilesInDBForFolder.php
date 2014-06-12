<? /*
act_getFilesInDBForFolder.php
Get all the files in an image directory

Variables:
=>| folderId  	a number indicating the folder
|=> filesInDB		an array of arrays with filename as key
*/

if($folderId != -1) {
	//Get an array of photos in the database for this folder
	$filesInDB = array();
	$sql = "SELECT photoName, caption, linkedImg, width, height ";
	$sql.= "FROM photos ";
	$sql.= "WHERE folderId = '" . $folderId . "' ORDER BY photoName";
	$rs_photos = @mysql_query($sql);
	$allSQL .= "rs_photos (" . mysql_num_rows($rs_photos) . " records returned)<br>" . $sql . "<br><br>";
	while( $row = mysql_fetch_array( $rs_photos ) ) {
		$file = $row["photoName"];
		$width = $row["width"];
		$height = $row["height"];
		$linkedImg = $row["linkedImg"];
		$caption = $row["caption"];
		$filesInDB[$file] = array("width" => $width, "height" => $height, "linkedImg" => $linkedImg, "caption" => $caption);
	}
}
?>
