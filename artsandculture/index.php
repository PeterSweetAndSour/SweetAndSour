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
	$fuseAction = "kpop";
}

switch ($fuseAction) {
	//K-pop!
	case "kpop";
		$heading1Text = "K-pop!";
		$contentPage = 'dsp_k-pop.php';
		include '../dsp_outline.php';
		break;

	//Aussie rock and roll
	case "rockAndRoll":
		$heading1Text = "Aussie rock and roll";
		$contentPage = 'dsp_rockAndRollAU.php';
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
		$heading1Text = "Philo T. Farnsworth:<br /><span>The (forgotten) inventor of television</span>";
		$contentPage = 'dsp_farnsworth.php';
		include '../dsp_outline.php';
		break;
	case "bumperStickers";
		$heading1Text = "Bumper stickers<br /><span>Wisdom in ten words or less</span>";
		$contentPage = 'dsp_bumperStickers.php';
		include '../dsp_outline.php';
		break;
	case "palestine";
		$heading1Text = "Palestinine<br /><span>The victims are now the oppressors</span>";
		$contentPage = 'dsp_palestine.php';
		include '../dsp_outline.php';
		break;
	case "genocide";
		$heading1Text = "Remembering two other genocides<br/><span>Sadly, the Jews are not alone</span>";
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
