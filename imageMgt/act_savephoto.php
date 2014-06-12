<? /* Save photo after add or edit

Variables:
|=> $folderID  	a number indicating the folder
|=> $photoName		name of the photo (primary key)
|=> $linkedImg
|=> $caption
*/

$folderID = $_POST["folderID"];
$formType = $_POST["formType"];
$photoName = $_POST["photoName"];
$linkedImg = $_POST["linkedImg"];
$caption = str_replace ("'", "''", trim($_POST["caption"]));
$width = $_POST["width"];
$height = $_POST["height"];
$dateModified = $_POST["dateModified"];

	
if($formType == "new") {
	//Confirm that the name of the photo (the primary key) is not already used
	$sql = "SELECT photoName FROM photos WHERE photoName = '" . $photoName . "'";
	$rs_checkName = @mysql_query($sql);
	$allSQL .= "rs_checkName (" . mysql_num_rows($rs_checkName) . " records returned)<br />" . $sql . "<br /><br />";

	if(mysql_num_rows($rs_checkName) != 0) { //The name is already used so send user back to form with all form variables
		$formAction="index.php?fuseAction=FormPhotos";
		$displayMsg="Sorry, that name has already been used.\nChoose another.";
		include '../includes/act_passOnVars.php';
	}
	else { //OK to save new photo
		$sql = "INSERT INTO photos (photoName, folderID, linkedImg, caption, width, height, version) VALUES ('". $photoName . "', " . $folderID . ", '" . $linkedImg . "', '" . $caption . "', " . $width . ", " . $height . ", '" . $dateModified . "')";
		$rs_insertPhoto = @mysql_query($sql);
		$allSQL .= "rs_insertPhoto (" . mysql_affected_rows() . " record inserted)<br />" . $sql . "<br /><br />";
	}
}
else { //Update existing record
		$sql = "UPDATE photos SET photoName='" . $photoName . "', folderID=" . $folderID . ", linkedImg='" . $linkedImg . "', caption='" . $caption . "' WHERE photoName='" . $photoName . "'" ;
		$rs_updatePhoto = @mysql_query($sql);
		$allSQL .= "rs_updatePhoto (" . mysql_affected_rows() . " record(s) affected)<br />" . $sql . "<br /><br />";
}
//Return to the ShowPhotos page.
header("Location: http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/" . "index.php?fuseAction=showPhotos&folderID=" . $folderID . "&photoName=" . $photoName);

//include '../includes/act_showDebugInfo.php';
?>
<!-- For debugging only. Comment out line above so "header" does not get executed.
<script type="text/javascript">
	if(confirm("Return?")) {
		window.location.href="index.php?fuseAction=showPhotos&folderID=<?= $folderID ?>&photoName=<?= $photoName ?>";
	}
</script>
-->

