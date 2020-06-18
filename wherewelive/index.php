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
	$fuseAction = "lisbon";
}


switch ($fuseAction) {
	case "lisbon":
		$heading1Text = "Lisbon (2019 &ndash; ?)";
		$contentPage = "dsp_lisbon.php";
		include "../dsp_outline.php";
		break;
		
	//Sydney
	case "sydney":
		$heading1Text = "Sydney (to 1995)";
		$contentPage = 'dsp_sydney.php';
		include "../dsp_outline.php";
		break;

	//Kitchen renovation
	case "kitchen";
		$heading1Text = "Kitchen Renovation";
		$contentPage = 'dsp_kitchen.php';
		$displayMenu = false;
		include "../dsp_outline.php";

	//Denver
	case "denver":
		$heading1Text = "Denver (1995 &ndash; 2006)";
		$contentPage = 'dsp_denver.php';
		include "../dsp_outline.php";
		break;

	//Washington
	case "washington":
		$heading1Text = "Washington (2006 &ndash; 2019)";
		$contentPage = 'dsp_washington.php';
		include "../dsp_outline.php";
		break;
		
	case "houserenovation":
		$heading1Text = "House renovation";
		$contentPage = "dsp_houserenovation.php";
		include "../dsp_outline.php";
		break;
		
	case "instructionstobuilder":
		$heading1Text = "Instructions to Builder";
		$contentPage = "dsp_instructionstobuilder.php";
		include "../dsp_outline.php";
		break;
		

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Where we live' section!</p>";
		break;
}

$mysqli->close();
?>