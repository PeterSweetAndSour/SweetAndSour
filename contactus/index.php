<? /*
index.php for "Contact us". */

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
	$fuseAction = "contactUs";
}

switch ($fuseAction) {

	//Email form
	case "contactUs":
		$heading1Text = "Contact Us";
		$showToTopLink = false;  //Page too short to include "To top" link.
		$jsFiles[] = "<script src=\"" . $urlPrefix . "js/contactUsForm.js\"></script>";
		$jsFiles[] = "<script src=\"https://www.google.com/recaptcha/api.js\"></script>";
		$jsFiles[] = <<<reCAPTCHA

<script type="text/javascript">
	var onloadCallback = function() {
		grecaptcha.render('reCAPTCHA-wrapper', {
			'sitekey' : '$reCAPTCHA_siteKey'
		});
	};
</script>

reCAPTCHA;
		$contentPage = 'dsp_contactUs.php';
		include '../dsp_outline.php';
		break;

	//Send email
	case "sendEmail":
		include '../includes/act_callAPI.php';
		include 'act_sendEmail.php';
		break;

	/**** Default case. ****/
	default:
		echo "<p>No recognized fuse action in 'Contact us' section!</p>";
		break;
}

$mysqli->close();
?>
