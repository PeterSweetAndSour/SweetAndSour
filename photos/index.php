<? /*
index.php  for "Technology". */

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';
include 'app_locals.php';

//Reset the variable holding all the SQL
$allSQL = "";

//Set fuseaction
if(isset($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "portsmouth2009";
}


switch ($fuseAction) {

	//The house on Reno Rd
	case "renoRd":
		$heading1Text = "The house on Reno Rd.";
		$contentPage = 'dsp_renoRd.php';
		include "../dsp_outline.php";
		break;

	//Utah trip May 2005
	case "utah2005":
		$heading1Text = "Utah trip May 2005";
		$contentPage = 'dsp_utah2005.php';
		include "../dsp_outline.php";
		break;


	//Australia trip Dec 2005
	case "aust2005":
		$heading1Text = "Australia trip 2005";
		$contentPage = 'dsp_aust2005.php';
		include "../dsp_outline.php";
		break;
	
	//Grand Canyon Jan 2006
	case "grandCanyon":
		$heading1Text = "Grand Canyon 2006";
		$contentPage = 'dsp_grandcanyon.php';
		include "../dsp_outline.php";
		break;

	//Cherry blossoms Jan 2006
	case "cherryBlossoms":
		$heading1Text = "Cherry Blossoms 2006";
		$contentPage = 'dsp_cherryblossoms.php';
		include "../dsp_outline.php";
		break;
		
	//Longwood Gardens May 2006
	case "longwoodGardens":
		$heading1Text = "Longwood Gardens 2006";
		$contentPage = 'dsp_longwoodGardens.php';
		include "../dsp_outline.php";
		break;

	//Montreal Oct 2006
	case "montreal2006":
		$heading1Text = "Canada 2006";
		$contentPage = 'dsp_montreal2006.php';
		include "../dsp_outline.php";
		break;

	//Trois Rivieres Oct 2006
	case "troisRivieres2006":
		$heading1Text = "Canada 2006";
		$contentPage = 'dsp_troisRivieres2006.php';
		include "../dsp_outline.php";
		break;

	//Quebec Oct 2006
	case "quebec2006":
		$heading1Text = "Canada 2006";
		$contentPage = 'dsp_quebec2006.php';
		include "../dsp_outline.php";
		break;

	//Ottawa Oct 2006
	case "ottawa2006":
		$heading1Text = "Canada 2006";
		$contentPage = 'dsp_ottawa2006.php';
		include "../dsp_outline.php";
		break;

	//Australia trip Dec 2007
	case "aust2007":
		$heading1Text = "Australia trip 2007";
		$contentPage = 'dsp_aust2007.php';
		include "../dsp_outline.php";
		break;
		
	//Portsmouth NH Aug 2009
	case "portsmouth2009":
		$heading1Text = "Trip to Maine 2009";
		$contentPage = 'dsp_portsmouth2009.php';
		include "../dsp_outline.php";
		break;
		
	//Acadia National Park Aug 2009
	case "acadia2009":
		$heading1Text = "Trip to Maine 2009";
		$contentPage = 'dsp_acadia2009.php';
		include "../dsp_outline.php";
		break;
		
	//Maine Central Coast Aug 2009
	case "centralCoastME":
		$heading1Text = "Trip to Maine 2009";
		$contentPage = 'dsp_centralCoastME2009.php';
		include "../dsp_outline.php";
		break;

	//Portsland, ME Aug 2009
	case "portland2009":
		$heading1Text = "Trip to Maine 2009";
		$contentPage = 'dsp_portland2009.php';
		include "../dsp_outline.php";
		break;

		
	//Panoramas
	case "panorama":
		$heading1Text = "Panoramas";
		$contentPage = 'dsp_panorama.php';
		include "../dsp_outline.php";
		break;
		

		

/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Photos' section!</p>";
		break;
}

$mysqli->close();
?>
