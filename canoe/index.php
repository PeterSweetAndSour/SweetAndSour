<? /*
index.php for "Canoe". */

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

//Reset the variable holding all the SQL
$allSQL = "";

//Set fuseaction
if(isset($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "canoe";
}


switch ($fuseAction) {

	case "canoe":
		$heading1Text = "Canoe!";
		$displayMenu = false;
		//$returnLink = '<a href="<?=$rootRelativeUrl ?>home/" title="To main site">To our site</a>';
		$showToTopLink = false;  //Page too short to include "To top" link.
		$contentPage = 'dsp_canoe.php';
		include "../dsp_outline.php";
		break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Contact us' section!</p>";
		break;
}
?>
