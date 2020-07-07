<?
/* act_sendEmail
Send mail message.

=>| Form.senderName
=>| Form.senderEmail
=>| Form.msgSubject
=>| Form.msgText

|=> response (json)

*/

$senderName = htmlspecialchars($_POST["senderName"]);
$senderEmail = htmlspecialchars($_POST["senderEmail"]);
$msgSubject = htmlspecialchars($_POST["msgSubject"]);
$msgText = htmlspecialchars($_POST["msgText"]);

$reCAPTCHA_url = "https://www.google.com/recaptcha/api/siteverify";
$reCAPTCHA_data["secret"] = $reCAPTCHA_secretKey;
$reCAPTCHA_data["response"] = $_POST["g-recaptcha-response"];
$reCAPTCHA_data["remoteip"] = $isLive ? $_SERVER["REMOTE_ADDR"] : "localhost"; // remoteip is ::1 but neither 127.0.0.1 or localhost works either despite registering them.

if($isLive) {
	$json_response = CallAPI("POST", $reCAPTCHA_url, $reCAPTCHA_data);
	$response = json_decode($json_response);
}
else {
	$response = (object) ["success" => True, "challenge_ts" => "2020-07-04 16:00:00+01:00", "hostname" => "localhost"];
}

if (!is_null($response) and $response->success) {
	//Send the message
	if($isLive) {
		//$logText = PHP_EOL . "senderName: " . $senderName . PHP_EOL . "senderEmail: " . $senderEmail .PHP_EOL . "msgSubject: " . $msgSubject .PHP_EOL . "Timestamp: " .date("F j, Y, g:i a").PHP_EOL;
		//file_put_contents('./reCAPTCHA.txt', $logText, FILE_APPEND);

		$result = mail($myEmailAddr, $msgSubject, $senderName . " wrote from SweetAndSour.org: \r\r" . $msgText, "From: " . $senderEmail);
	}
	else {
		$result = True;
	}

	if($result) {
		$json = (object) ["status" => "success", "text" => "Thank you for your email! I will try to reply within 24 hours."]; 
	}
	else {
		$json = (object) ["status" => "failure", "text" => "The mail function failed for some reason."]; 
	}; 
}
else {
	// The CAPTCHA failed.
	$errorCodes = "";
	if(is_null($response)) {
		$errorCodes = "No response from reCAPTCHA verification";
	}
	else if(!is_null($response->error-codes)) {
		$errorCodes = join(" | ", $response->error-codes);
	}

 	$json = (object) ["status" => "failure", "text" => "reCAPTCHA verification failed. Error codes: " . $errorCodes]; 
}

echo json_encode($json);
?>

