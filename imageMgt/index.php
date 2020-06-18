<? /* index.php page assembles pages based on the specified fuse action

Variable conventions:
+++ included file
=>| temporary (local) variable passed in
|=> temporary (local) variable passed out
+>| persistant variable passed avaiable (session variable)
|+> persistant variable passed set (session variable)
*/

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

//Reset the variable holding all the SQL
$allSQL = "";


//Set fuseaction
if(isSet($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "showPhotos";
}
	
switch ($fuseAction) {

	/**** Image management. ****/
	case "showPhotos":
		include 'qry_folderNames.php';
		include '../includes/act_displayArray.php';
		include 'dsp_showPhotos.php';
		include '../includes/act_showDebugInfo.php';
		break;
	case "formPhoto":
		include 'qry_folderNames.php';
		include '../includes/act_arrayDiff.php';
		include '../includes/act_displayArray.php';
		include 'dsp_formPhoto.php';
		break;
	case "savePhoto":
		include 'act_savePhoto.php';
		break;
	case "deletePhoto"; //from database, not the photo itself
		include "act_deletePhoto.php";
		break;


	/*** Display a photo and caption on its own page. ***/
	case "showPhotoAndCaption";
		$bodyClass = "photoAndCaption";
		$displayMenu = false;
		$showToTopLink = false;
		$contentPage = 'dsp_photoAndCaption.php';
		include '../dsp_outline.php';
		break;
		
	/*** Return a photo and caption for ajax call. ***/
	case "showPhotoAndCaptionAjax";
		include 'dsp_photoAndCaptionAjax.php';
		break;
		
	
	/*** Warning for IE users that graphic has been shrunk to fit. ***/
	case "imageExpanderIE";
		 include 'dsp_imageExpanderIE.htm';
		 break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action!</p>";
		break;
}

if($showDebugInfo == true) {
	include '../includes/act_showDebugInfo.php';
}
$mysqli->close();
?>
