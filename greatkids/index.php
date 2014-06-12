<? /*
index.php for "Great kids". */

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
	$fuseAction = "relatives";
}

	
switch ($fuseAction) {
	case "relatives":
		$heading1Text = "Children of relatives";
		$contentPage = 'dsp_relativesChildren.php';
		include '../dsp_outline.php';
		break;

	case "friends":
		$heading1Text = "Children of friends";
		$contentPage = 'dsp_friendsChildren.php';
		include '../dsp_outline.php';
		break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Great kids' section!</p>";
		break;
}
?>
