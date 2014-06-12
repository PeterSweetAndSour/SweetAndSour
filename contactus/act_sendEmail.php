<?
/* act_sendEmail
Send mail message.

=>| Form.senderName
=>| Form.senderEmail
=>| Form.msgSubject
=>| Form.msgText

*/
require_once("recaptchalib.php");
$privatekey = "6LcWaL4SAAAAAPzruU1s5TkFF99apP0wpvWKk3qp";

$_SESSION["senderName"] = htmlspecialchars($_POST["senderName"]);
$_SESSION["senderEmail"] = htmlspecialchars($_POST["senderEmail"]);
$_SESSION["msgSubject"] = htmlspecialchars($_POST["msgSubject"]);
$_SESSION["msgText"] = htmlspecialchars($_POST["msgText"]);

$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
 		header("Location: index.php?fuseAction=contactUs&captchaError=true");
  } 
	else {
		//Send the message
		mail($myEmailAddr, $_SESSION["msgSubject"], $_SESSION["senderName"] . " wrote from SweetAndSour.org: \r\r" . $_SESSION["msgText"], "From: " . $_SESSION["senderEmail"]);

		//Send user to "thank you" page
		header("Location: index.php?fuseAction=contactUs&msgSent=true");
  }

?>

