<? /*
index.php for "Home". */

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
	$fuseAction = "welcome";
}

switch ($fuseAction) {
	//Welcome page
	case "welcome":
		$heading1Text = "Welcome";
		$contentPage = 'dsp_welcome.php';
		include "../dsp_outline.php";
		break;

	case "instagram":
		$heading1Text = "Instagram links";
		$contentPage = 'dsp_instagram.php';
		include "../dsp_outline.php";
		break;
		
	// Default case.
	default:
		echo "<p>No recognized fuse action in 'Home' section!</p>";
		break;
}

$mysqli->close();
?>