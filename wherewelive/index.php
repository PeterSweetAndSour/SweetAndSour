<? /*
index.php  for "Where we live". */

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
	$fuseAction = "washington";
}


switch ($fuseAction) {
	//Sydney
	case "sydney":
		$heading1Text = "Sydney (to 1995)";
		$contentPage = 'dsp_sydney.php';
		include '../dsp_outline.php';
		break;

	//Harbour panoramas
	case "operaBridge";
		include '../imageMgt/act_setPhotoFunction.php';
		include 'dsp_operaBridge.php';
		break;

	//Kitchen renovation
	case "kitchen";
		$heading1Text = "Kitchen Renovation";
		$contentPage = 'dsp_kitchen.php';
		$displayMenu = false;
		$returnLink = '<a href="index.php?fuseAction=sydney" title="Return to Sydney">Return to Sydney</a>';
		include '../dsp_outline.php';

	//Denver
	case "denver":
		$heading1Text = "Denver (1995 &ndash; 2006)";
		$contentPage = 'dsp_denver.php';
		include '../dsp_outline.php';
		break;

	//Washington
	case "washington":
		//$sectionPhoto = "washington/cherry_28Detail.jpg";
		//$sectionPhotoLink = "../imageMgt/index.php?fuseAction=showPhotoAndCaption&photoName=cherry_028Lg.jpg";
		$heading1Text = "Washington (2006 ...)";
		$contentPage = 'dsp_washington.php';
		include '../dsp_outline.php';
		break;
		
	case "houserenovation":
		$heading1Text = "House renovation";
		$contentPage = "dsp_houserenovation.php";
		include "../dsp_outline.php";
		break;
		
	case "instructionstobuilder":
		$heading1Text = "Instructions to Builder";
		$contentPage = "dsp_instructionstobuilder.php";
		include '../dsp_outline.php';
		break;
		
	case "productreviews":
		$heading1Text = "Product Reviews";
		$contentPage = "dsp_productreviews.php";
		include "../dsp_outline.php";
		break;
		


	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Where we live' section!</p>";
		break;
}
?>
