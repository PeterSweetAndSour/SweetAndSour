<? /*
index.php  for "Where we live". */

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';
include 'app_locals.php';

//Reset the variable holding all the SQL
$allSQL = "";

if(! isSet($fuseAction))
	$fuseAction = "pictures";

	
switch ($fuseAction) {
	//General
	case "pictures";
		$heading1Text = "Madison St Property";
		$displayMenu = false;
		$returnLink = '<a href="../home/" title="To main site">To main site</a>';
		$contentPage = 'dsp_pictures.php';
		include '../dsp_outline.php';
		break;


	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Madison Street' section!</p>";
		break;
}
?>
