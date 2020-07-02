<?
/* act_sendEmail
Send mail message.

=>| Form.senderName
=>| Form.senderEmail
=>| Form.msgSubject
=>| Form.msgText
|=> URL.msgSent
|=> Session.senderName
|=> Session.senderEmail
|=> Session.msgSubject
|=> Session.msgText
*/

$_SESSION["senderName"] = htmlspecialchars($_POST["senderName"]);
$_SESSION["senderEmail"] = htmlspecialchars($_POST["senderEmail"]);
$_SESSION["msgSubject"] = htmlspecialchars($_POST["msgSubject"]);
$_SESSION["msgText"] = htmlspecialchars($_POST["msgText"]);

$reCAPTCHA_url = "https://www.google.com/recaptcha/api/siteverify";
$reCAPTCHA_data["secret"] = $reCAPTCHA_secretKey;
$reCAPTCHA_data["response"] = $_POST["g-recaptcha-response"];
$reCAPTCHA_data["remoteip"] = $_SERVER["REMOTE_ADDR"];	

$json_response = CallAPI("POST", $reCAPTCHA_url, $reCAPTCHA_data);
$response = json_decode($json_response);

if (!is_null($response) and $response->success) {
	//Send the message
	mail($myEmailAddr, $_SESSION["msgSubject"], $_SESSION["senderName"] . " wrote from SweetAndSour.org: \r\r" . $_SESSION["msgText"], "From: " . $_SESSION["senderEmail"]);

	//Send user to "thank you" page
	header("Location: index.php?fuseAction=contactUs&status=success");
}
else {
	// The CAPTCHA failed or there was a server error.
	$errorCodes = "Unknown error from the %26ldquo;Verify You Are Human%26rdquo; test";
	if(!is_null($response) and !is_null($response->error-codes)) {
		$errorCodes = join(" | ", $response->error-codes);
	}
 	header("Location: index.php?fuseAction=contactUs&status=error&errorCodes=" . $errorCodes);
}
?>

