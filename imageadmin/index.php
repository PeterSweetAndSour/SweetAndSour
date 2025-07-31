<?php
include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

//Reset the variable holding all the SQL
$allSQL = "";

//Set fuseaction
if(isSet($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "showPhotoPage";
}

switch ($fuseAction) {
	case "showPhotoPage":
		include 'act_setLocalVarFolderId.php';
		include 'qry_folderNames.php';
		include 'dsp_imageAdmin.php';
		break;
		
	case "getPhotos": // called via ajax
		include 'act_setLocalVarFolderId.php';
		include '../includes/qry_thisFolder.php';
		include 'act_getPath.php';
		include 'act_getPhysicalFilesInFolder.php';
		include 'act_markupForAdminThumbnail.php';
		include 'act_pagination.php';
		include 'qry_getFilesInDBForFolder.php';
		include 'dsp_photos.php';
		break;
		
	case "renameFile": // ajax
		break;
		 
	case "makeWebPhotos": // ajax
		break;

	case "savePhoto": // ajax
		break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action!</p>";
		break;
}
?>