<? /*
index.php for "Time for justice". */

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
	$fuseAction = "injustice";
}

	
switch ($fuseAction) {
	case "injustice":
		$heading1Text = "Palestine<br><span>The victims are now the oppressors</span>";
		$contentPage = '../artsandculture/dsp_palestine.php';
		include "../dsp_outline.php";
		break;

	case "callsForJustice":
		$heading1Text = "Calls for Justice";
		$contentPage = 'dsp_callsForJustice.php';
		include "../dsp_outline.php";
		break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Time for Justice' section!</p>";
		break;
}

$mysqli->close();
?>