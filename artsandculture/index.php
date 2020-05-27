<? /*
index.php  for "Everything else". */

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
	$fuseAction = "oddsAndEnds";
}

switch ($fuseAction) {
	//Odds & ends
	case "oddsAndEnds";
		$heading1Text = "Odds and ends";
		$contentPage = 'dsp_oddsAndEnds.php';
		include '../dsp_outline.php';
		break;

	//Aussie rock and roll
	case "rockAndRoll_1":
		$heading1Text = "Aussie rock and roll &ndash; Introduction";
		$contentPage = 'dsp_rockAndRoll_1.php';
		include '../dsp_outline.php';
		break;
	case "rockAndRoll_2":
		$heading1Text = "Aussie rock and roll &ndash; A bit of history";
		$contentPage = 'dsp_rockAndRoll_2.php';
		include '../dsp_outline.php';
		break;
	case "rockAndRoll_3":
		$heading1Text = "Aussie rock and roll &ndash; Bands A to I";
		$contentPage = 'dsp_rockAndRoll_3.php';
		include '../dsp_outline.php';
		break;
	case "rockAndRoll_4":
		$heading1Text = "Aussie rock and roll &ndash; Bands K to T";
		$contentPage = 'dsp_rockAndRoll_4.php';
		include '../dsp_outline.php';
		break;

	
	//Humor
	case "humor1":
		$heading1Text = "British TV comedies";
		include '../includes/act_setVideo.php';
		$contentPage = 'dsp_humor1.php';
		include '../dsp_outline.php';
		break;
	case "humor2":
		$heading1Text = "Other TV";
		$contentPage = 'dsp_humor2.php';
		include '../dsp_outline.php';
		break;
	case "humor3":
		$heading1Text = "Men and Women";
		$contentPage = 'dsp_humor3.php';
		include '../dsp_outline.php';
		break;
	case "humor4":
		$heading1Text = "Random";
		$contentPage = 'dsp_humor4.php';
		include '../dsp_outline.php';
		break;
	case "finance";
		include 'dsp_finance.html';
		break;


	//Speeches
	case "farnsworth";
		$useInPageHeader = true;
		$contentPage = 'dsp_farnsworth.php';
		include '../dsp_outline.php';
		break;
	case "bumperStickers";
		$heading1Text = "&quot;Wisdom in ten words or less&quot; &ndash; Bumper stickers";
		$contentPage = 'dsp_bumperStickers.php';
		include '../dsp_outline.php';
		break;
	case "palestine";
		$heading1Text = "&quot;The victims are now the oppressors&quot; &ndash; Palestinine";
		$contentPage = 'dsp_palestine.php';
		include '../dsp_outline.php';
		break;
	case "genocide";
		$heading1Text = "&quot;Sadly, the Jews are not alone&quot; &ndash; Remembering two other genocides";
		$contentPage = 'dsp_genocide.php';
		include '../dsp_outline.php';
		break;

	
	//Activism
	case "callsForJustice";
		$heading1Text = "Calls for Justice";
		$contentPage = 'dsp_callsForJustice.php';
		include '../dsp_outline.php';
		break;


	//Lifetime reading plan
	case "readingPlan":
		$heading1Text = "Lifetime Reading Plan";
		$contentPage = 'dsp_readingPlan.php';
		include '../dsp_outline.php';
		break;
		

	//Books I've read
	case "books":
		$heading1Text = "Books I've read";
		$contentPage = 'dsp_books.php';
		include '../dsp_outline.php';
		break;
		
	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Arts and Culture' section!</p>";
		break;
}

$mysqli->close();
?>
