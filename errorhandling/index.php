<? /*
index.php for "Home". */

include '../../sweetandsour_conf.php';
include 'app_locals.php';


//Set fuseaction
if(isSet($_GET["fuseAction"])) {
	$fuseAction = $_GET["fuseAction"];
}
else {
	$fuseAction = "";
}

switch ($fuseAction) {
	case "error401":
		//$heading1Text = "Access denied <span>(Error 401)</span>";
		$contentPage = 'dsp_401.php';
		include '../dsp_outline.php';
		break;
	case "error404":
		//$heading1Text = "Page not found <span>(Error 404)</span>";
		$contentPage = 'dsp_404.php';
		include '../dsp_outline.php';
		break;
	case "error500":
		$heading1Text = "Server error <span>(Error 500)</span>";
		$contentPage = 'dsp_500.php';
		include '../dsp_outline.php';
		break;
			
	// Default case.
	default:
		echo "<p>No recognized fuse action in 'Error Handling' section!</p>";
		break;
}


?>