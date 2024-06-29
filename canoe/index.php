<?php
//index.php for "Canoe".

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

//Reset the variable holding all the SQL
$allSQL = "";

switch ($fuseAction) {
	case "canoe":
		$heading1Text = "Canoe!";
		$displayMenu = false;
		$showToTopLink = false;  //Page too short to include "To top" link.
		$contentPage = 'dsp_canoe.php';
		include "../dsp_outline.php";
		break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Contact us' section!</p>";
		break;
}
?>