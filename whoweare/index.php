<? /*
index.php for "Who we are". */

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';
include 'app_locals.php';

//Reset the variable holding all the SQL
$allSQL = "";

//Set fuseaction
if(isSet($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "whoWeAre";
}

switch ($fuseAction) {
	case "whoWeAre":
		$heading1Text = "About Us";
		$contentPage = 'dsp_whoWeAre.php';
		include '../dsp_outline.php';
		break;
	case "oldPhotosLan":
		$heading1Text = "Old Photos &ndash; Lan";
		$contentPage = 'dsp_oldPhotosLan.php';
		include '../dsp_outline.php';
		break;
	case "oldPhotosPeter":
		$heading1Text = "Old Photos &ndash; Peter";
		$contentPage = 'dsp_oldPhotosPeter.php';
		$showToTopLink = false;  //Page too short to include "To top" link.
		include '../dsp_outline.php';
		break;
		

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Who we are' section!</p>";
		break;
}

?>
