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
		include '../dsp_outline.php';
		break;

	//Site map
	case "siteMap":
		$heading1Text = "Site Map";
		$contentPage = "dsp_siteMap.php";
		//$jsFiles[] = "xtree.js";
		include '../dsp_outline.php';
		break;
		
	// Facebook comments test	
	case "fb-comments":
		$testFacebookComments = true; //override config file
		$heading1Text = "Testing Facebook comments";
		$contentPage = "dsp_fbCommentsTest.php";
		include '../dsp_outline.php';
		break;
		
	// Default case.
	default:
		echo "<p>No recognized fuse action in 'Home' section!</p>";
		break;
}

$mysqli->close();
?>