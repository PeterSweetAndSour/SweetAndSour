<? /*
index.php  for the "Coming to America" (letters subdirectory). */

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
	$fuseAction = "lettersOverview";
}


switch ($fuseAction) {
	//Overview
	case "lettersOverview":
		$heading1Text = "Overview";
		$contentPage = 'dsp_lettersOverview.php';
		include '../dsp_outline.php';
		break;

	//Letter 1
	case "letter1":
		$heading1Text = "Letter 1";
		$contentPage = 'dsp_letter1.php';
		include '../dsp_outline.php';
		break;

	//Letter 2
	case "letter2a":
		$heading1Text = "Letter 2 <span>(page 1)</span>";
		$contentPage = 'dsp_letter2a.php';
		include '../dsp_outline.php';
		break;
	case "letter2b":
		$heading1Text = "Letter 2 <span>(page 2)</span>";
		$contentPage = 'dsp_letter2b.php';
		include '../dsp_outline.php';
		break;
	
	//Letter 3
	case "letter3a":
		$heading1Text = "Letter 3 <span>(page 1)</span>";
		$contentPage = 'dsp_letter3a.php';
		include '../dsp_outline.php';
		break;
	case "letter3b":
		$heading1Text = "Letter 3 <span>(page 2)</span>";
		$contentPage = 'dsp_letter3b.php';
		include '../dsp_outline.php';
		break;
	case "letter3c":
		$heading1Text = "Letter 3 <span>(page 3)</span>";
		$contentPage = 'dsp_letter3c.php';
		include '../dsp_outline.php';
		break;
	case "letter3d":
		$heading1Text = "Letter 3 <span>(page 4)</span>";
		$contentPage = 'dsp_letter3d.php';
		include '../dsp_outline.php';
		break;
	case "letter3e":
		$heading1Text = "Letter 3 <span>(page 5)</span>";
		$contentPage = 'dsp_letter3e.php';
		include '../dsp_outline.php';
		break;
	case "letter3f":
		$heading1Text = "Letter 3 <span>(page 6)</span>";
		$contentPage = 'dsp_letter3f.php';
		include '../dsp_outline.php';
		break;

	//Letter 4
	case "letter4a":
		$heading1Text = "Letter 4 <span>(page 1)</span>";
		$contentPage = 'dsp_letter4a.php';
		include '../dsp_outline.php';
		break;
	case "letter4b":
		$heading1Text = "Letter 4 <span>(page 2)</span>";
		$contentPage = 'dsp_letter4b.php';
		include '../dsp_outline.php';
		break;
	case "letter4c":
		$heading1Text = "Letter 4 <span>(page 3)</span>";
		$contentPage = 'dsp_letter4c.php';
		include '../dsp_outline.php';
		break;
	case "letter4d":
		$heading1Text = "Letter 4 <span>(page 4)</span>";
		$contentPage = 'dsp_letter4d.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 5
	case "letter5a":
		$heading1Text = "Letter 5 <span>(page 1)</span>";
		$contentPage = 'dsp_letter5a.php';
		include '../dsp_outline.php';
		break;
	case "letter5b":
		$heading1Text = "Letter 5 <span>(page 2)</span>";
		$contentPage = 'dsp_letter5b.php';
		include '../dsp_outline.php';
		break;
	case "letter5c":
		$heading1Text = "Letter 5 <span>(page 3)</span>";
		$contentPage = 'dsp_letter5c.php';
		include '../dsp_outline.php';
		break;
	case "letter5d":
		$heading1Text = "Letter 5 <span>(page 4)</span>";
		$contentPage = 'dsp_letter5d.php';
		include '../dsp_outline.php';
		break;
	case "letter5e":
		$heading1Text = "Letter 5 <span>(page 5)</span>";
		$contentPage = 'dsp_letter5e.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 6
	case "letter6a":
		$heading1Text = "Letter 6 <span>(page 1)</span>";
		$contentPage = 'dsp_letter6a.php';
		include '../dsp_outline.php';
		break;
	case "letter6b":
		$heading1Text = "Letter 6 <span>(page 2)</span>";
		$contentPage = 'dsp_letter6b.php';
		include '../dsp_outline.php';
		break;
	case "letter6c":
		$heading1Text = "Letter 6 <span>(page 3)</span>";
		$contentPage = 'dsp_letter6c.php';
		include '../dsp_outline.php';
		break;
	case "letter6d":
		$heading1Text = "Letter 6 <span>(page 4)</span>";
		$contentPage = 'dsp_letter6d.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 7
	case "letter7a":
		$heading1Text = "Letter 7 <span>(page 1)</span>";
		$contentPage = 'dsp_letter7a.php';
		include '../dsp_outline.php';
		break;
	case "letter7b":
		$heading1Text = "Letter 7 <span>(page 2)</span>";
		$contentPage = 'dsp_letter7b.php';
		include '../dsp_outline.php';
		break;
	case "letter7c":
		$heading1Text = "Letter 7 <span>(page 3)</span>";
		$contentPage = 'dsp_letter7c.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 8
	case "letter8a":
		$heading1Text = "Letter 8 <span>(page 1)</span>";
		$contentPage = 'dsp_letter8a.php';
		include '../dsp_outline.php';
		break;
	case "letter8b":
		$heading1Text = "Letter 8 <span>(page 2)</span>";
		$contentPage = 'dsp_letter8b.php';
		include '../dsp_outline.php';
		break;
	case "letter8c":
		$heading1Text = "Letter 8 <span>(page 3)</span>";
		$contentPage = 'dsp_letter8c.php';
		include '../dsp_outline.php';
		break;
		
	//Blizzard report
	case "blizzard97":
		$heading1Text = "Blizzard report";
		$contentPage = 'dsp_blizzard97.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 9
	case "letter9a":
		$heading1Text = "Letter 9 <span>(page 1)</span>";
		$contentPage = 'dsp_letter9a.php';
		include '../dsp_outline.php';
		break;
	case "letter9b":
		$heading1Text = "Letter 9 <span>(page 2)</span>";
		$contentPage = 'dsp_letter9b.php';
		include '../dsp_outline.php';
		break;
	case "letter9c":
		$heading1Text = "Letter 9 <span>(page 3)</span>";
		$contentPage = 'dsp_letter9c.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 10
	case "letter10a":
		$heading1Text = "Letter 10 <span>(page 1)</span>";
		$contentPage = 'dsp_letter10a.php';
		include '../dsp_outline.php';
		break;
	case "letter10b":
		$heading1Text = "Letter 10 <span>(page 2)</span>";
		$contentPage = 'dsp_letter10b.php';
		include '../dsp_outline.php';
		break;
	case "letter10c":
		$heading1Text = "Letter 10 <span>(page 3)</span>";
		$contentPage = 'dsp_letter10c.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 11
	case "letter11":
		$heading1Text = "Letter 11";
		$contentPage = 'dsp_letter11.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 12
	case "letter12a":
		$heading1Text = "Letter 12 <span>(page 1)</span>";
		$contentPage = 'dsp_letter12a.php';
		include '../dsp_outline.php';
		break;
	case "letter12b":
		$heading1Text = "Letter 12 <span>(page 2)</span>";
		$contentPage = 'dsp_letter12b.php';
		include '../dsp_outline.php';
		break;
	case "letter12c":
		$heading1Text = "Letter 12 <span>(page 3)</span>";
		$contentPage = 'dsp_letter12c.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 13
	case "letter13a":
		$heading1Text = "Letter 13 <span>(page 1)</span>";
		$contentPage = 'dsp_letter13a.php';
		include '../dsp_outline.php';
		break;
	case "letter13b":
		$heading1Text = "Letter 13 <span>(page 2)</span>";
		$contentPage = 'dsp_letter13b.php';
		include '../dsp_outline.php';
		break;
	case "letter13c":
		$heading1Text = "Letter 13 <span>(page 3)</span>";
		$contentPage = 'dsp_letter13c.php';
		include '../dsp_outline.php';
		break;
	case "letter13d":
		$heading1Text = "Letter 13 <span>(page 4)</span>";
		$contentPage = 'dsp_letter13d.php';
		include '../dsp_outline.php';
		break;
	case "letter13e":
		$heading1Text = "Letter 13 <span>(page 5)</span>";
		$contentPage = 'dsp_letter13e.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 14
	case "letter14a":
		$heading1Text = "Letter 14 <span>(page 1)</span>";
		$contentPage = 'dsp_letter14a.php';
		include '../dsp_outline.php';
		break;
	case "letter14b":
		$heading1Text = "Letter 14 <span>(page 2)</span>";
		$contentPage = 'dsp_letter14b.php';
		include '../dsp_outline.php';
		break;
	case "letter14c":
		$heading1Text = "Letter 14 <span>(page 3)</span>";
		$contentPage = 'dsp_letter14c.php';
		include '../dsp_outline.php';
		break;
	case "letter14d":
		$heading1Text = "Letter 14 <span>(page 4)</span>";
		$contentPage = 'dsp_letter14d.php';
		include '../dsp_outline.php';
		break;
		
	//Letter 15
	case "letter15a":
		$heading1Text = "Letter 15 <span>(page 1)</span>";
		$contentPage = 'dsp_letter15a.php';
		include '../dsp_outline.php';
		break;
	case "letter15b":
		$heading1Text = "Letter 15 <span>(page 2)</span>";
		$contentPage = 'dsp_letter15b.php';
		include '../dsp_outline.php';
		break;
	case "letter15c":
		$heading1Text = "Letter 15 <span>(page 3)</span>";
		$contentPage = 'dsp_letter15c.php';
		include '../dsp_outline.php';
		break;
	case "letter15d":
		$heading1Text = "Letter 15 <span>(page 4)</span>";
		$contentPage = 'dsp_letter15d.php';
		include '../dsp_outline.php';
		break;

	//Letter 16
	case "letter16a":
		$heading1Text = "Letter 16 <span>(page 1)</span>";
		$contentPage = 'dsp_letter16a.php';
		include '../dsp_outline.php';
		break;
	case "letter16b":
		$heading1Text = "Letter 16 <span>(page 2)</span>";
		$contentPage = 'dsp_letter16b.php';
		include '../dsp_outline.php';
		break;
	case "letter16c":
		$heading1Text = "Letter 16 <span>(page 3)</span>";
		$contentPage = 'dsp_letter16c.php';
		include '../dsp_outline.php';
		break;
	case "letter16d":
		$heading1Text = "Letter 16 <span>(page 4)</span>";
		$contentPage = 'dsp_letter16d.php';
		include '../dsp_outline.php';
		break;
	case "letter16e":
		$heading1Text = "Letter 16 <span>(page 5)</span>";
		$contentPage = 'dsp_letter16e.php';
		include '../dsp_outline.php';
		break;

	case "letter16f":
		$heading1Text = "Letter 16 <span>(page 6)</span>";
		$contentPage = 'dsp_letter16f.php';
		include '../dsp_outline.php';
		break;

	case "letter16g":
		$heading1Text = "Letter 16 <span>(page 7)</span>";
		$contentPage = 'dsp_letter16g.php';
		include '../dsp_outline.php';
		break;
		
	case "mapFrance";
		include 'dsp_mapFrance.htm';
		break;	


	//Letter 17
	case "letter17a":
		$heading1Text = "Letter 17 <span>(page 1)</span>";
		$contentPage = 'dsp_letter17a.php';
		include '../dsp_outline.php';
		break;
	case "letter17b":
		$heading1Text = "Letter 17 <span>(page 2)</span>";
		$contentPage = 'dsp_letter17b.php';
		include '../dsp_outline.php';
		break;
	case "letter17c":
		$heading1Text = "Letter 17 <span>(page 3)</span>";
		$contentPage = 'dsp_letter17c.php';
		include '../dsp_outline.php';
		break;
	case "letter17d":
		$heading1Text = "Letter 17 <span>(page 4)</span>";
		$contentPage = 'dsp_letter17d.php';
		include '../dsp_outline.php';
		break;
	case "letter17e":
		$heading1Text = "Letter 17 <span>(page 5)</span>";
		$contentPage = 'dsp_letter17e.php';
		include '../dsp_outline.php';
		break;

	//Letter 18
	case "letter18a":
		$heading1Text = "Letter 18 <span>(page 1)</span>";
		$contentPage = 'dsp_letter18a.php';
		include '../dsp_outline.php';
		break;
	case "letter18b":
		$heading1Text = "Letter 18 <span>(page 2)</span>";
		$contentPage = 'dsp_letter18b.php';
		include '../dsp_outline.php';
		break;
	case "letter18c":
		$heading1Text = "Letter 18 <span>(page 3)</span>";
		$contentPage = 'dsp_letter18c.php';
		include '../dsp_outline.php';
		break;

	//Letter 19
	case "letter19a":
		$heading1Text = "Letter 19 <span>(page 1)</span>";
		$contentPage = 'dsp_letter19a.php';
		include '../dsp_outline.php';
		break;
	case "letter19b":
		$heading1Text = "Letter 19 <span>(page 2)</span>";
		$contentPage = 'dsp_letter19b.php';
		include '../dsp_outline.php';
		break;
	case "letter19c":
		$heading1Text = "Letter 19 <span>(page 3)</span>";
		$contentPage = 'dsp_letter19c.php';
		include '../dsp_outline.php';
		break;

	case "letter20":
		$heading1Text = "Update from Portugal #1";
		$contentPage = 'dsp_letter20.php';
		include '../dsp_outline.php';
		break;

	case "letter21":
		$heading1Text = "Update from Portugal #2";
		$contentPage = 'dsp_letter21.php';
		include '../dsp_outline.php';
		break;
	
		/**** Default case. ****/
	default:
		echo "<p>" . $fuseAction . " is not a recognized fuse action in 'Coming to America' section!</p>";
		break;
}

$mysqli->close();
?>
