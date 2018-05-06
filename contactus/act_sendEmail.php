<?
/* act_sendEmail
Send mail message.

=>| Form.senderName
=>| Form.senderEmail
=>| Form.msgSubject
=>| Form.msgText

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

if ($response->success) {
	//Send the message
	mail($myEmailAddr, $_SESSION["msgSubject"], $_SESSION["senderName"] . " wrote from SweetAndSour.org: \r\r" . $_SESSION["msgText"], "From: " . $_SESSION["senderEmail"]);

	//Send user to "thank you" page
	header("Location: index.php?fuseAction=contactUs&msgSent=true");
}
else {
	// What happens when the CAPTCHA was entered incorrectly
	header("Location: index.php?fuseAction=contactUs&formError=true&errors=" . join(" | ", $response->error-codes));
}

?>

