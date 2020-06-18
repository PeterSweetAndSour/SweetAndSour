<? /*
index.php  for "Technology". */

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
	$fuseAction = "thisWebSite";
}

switch ($fuseAction) {

	//This web site
	case "thisWebSite":
		$heading1Text = "This web site";
		$contentPage = 'dsp_thisWebSite.php';
		include "../dsp_outline.php";
		break;
		
	//Cars
	case "cars1":
		$heading1Text = "Cars &ndash; <span>General principles</span>";
		$contentPage = 'dsp_cars1.php';
		include "../dsp_outline.php";
		break;
	case "cars2":
		$heading1Text = "Cars &ndash; <span>Innovative Designs</span>";
		$contentPage = 'dsp_cars2.php';
		include "../dsp_outline.php";
		break;

/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Technology' section!</p>";
		break;
}
?>
