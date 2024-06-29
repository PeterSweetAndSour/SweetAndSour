<?php
/* act_sendEmail
Send mail message.

=>| Form.senderName
=>| Form.senderEmail
=>| Form.msgSubject
=>| Form.msgText
=>| Form.challenge

|=> response (json)

I could not get this to work locally, even when sending the IP address of live in the data. From 'ping' the IP address is 167.114.64.93
but I just noticed that if I echo $_SERVER["REMOTE_ADDR"] on the site it is 79.169.27.30. Interesting. 
*/

$mailServerEmail = "Do.Not.Reply@sweetandsour.org"; //The "From" domain has to match the server for reliable delivery
$senderName = htmlspecialchars($_POST["senderName"]);
$senderEmail = htmlspecialchars($_POST["senderEmail"]);
$msgSubject = htmlspecialchars($_POST["msgSubject"]);
$msgTextPreamble = "Sent from the Contact Us page of sweetandsour.org by " . $senderName . " (" . $senderEmail . "):";
$msgText = htmlspecialchars($_POST["msgText"]);
$msgTextWithPreamble = $msgTextPreamble . PHP_EOL . PHP_EOL . $msgText;

$challenge =  strtolower(trim(htmlspecialchars($_POST["challenge"])));
$correctChallengeResponse = $challenge === "south australia";

$reCAPTCHA_url = "https://www.google.com/recaptcha/api/siteverify";
$reCAPTCHA_data["secret"] = $reCAPTCHA_secretKey;
$reCAPTCHA_data["response"] = $_POST["g-recaptcha-response"];
$reCAPTCHA_data["remoteip"] = $isLive ? $_SERVER["REMOTE_ADDR"] : "localhost"; // remoteip is ::1 but neither 127.0.0.1 or localhost works either despite registering them.

if($isLive) {
	$json_response = CallAPI("POST", $reCAPTCHA_url, $reCAPTCHA_data);
	$response = json_decode($json_response);

	$logText = "Timestamp: " . date("F j, Y, g:i a") . ", Name: " . $senderName . ", Email: " . $senderEmail . ", IP: " . $reCAPTCHA_data["remoteip"]. ", Subject (first 32 chars): " . substr($msgSubject, 0, 32) . ", Text (first 64 chars)" . substr($msgText, 0, 64) . "&hellip;" . PHP_EOL;
	file_put_contents('./reCAPTCHA.txt', $logText, FILE_APPEND);
}
else {
	$response = (object) ["success" => True, "challenge_ts" => "2020-07-04 16:00:00+01:00", "hostname" => "localhost"];
}

if (!is_null($response) and $response->success and $correctChallengeResponse) {
	//Send the message
	if($isLive) {
		$result = mail($myEmailAddr, $msgSubject, $msgTextWithPreamble, "From: " . $mailServerEmail);
	}
	else {
		$result = True; // Pretend that it works on local where no mail server is actually installed.
	}

	if($result) {
		$json = (object) ["status" => "success", "text" => "Thank you for your email! I will try to reply within 24 hours.<br><p>At the time of writing in April 2022, the Deputy Premier of South Australia was, of course, <a href=\"https://en.wikipedia.org/wiki/Susan_Close\" class=\"external\" target=\"_blank\">Susan Close</a>.</p>"]; 
	}
	else {
		$json = (object) ["status" => "failure", "text" => "The mail function failed for some reason."]; 
	};
}
else if (!$correctChallengeResponse) {
	$json = (object) ["status" => "failure", "text" => "It appears that you are bypassing the humanity check. Please stop."]; 
}
else {
	// The CAPTCHA failed.
	$errorMsg = "";
	if(is_null($response)) {
		$errorMsg = "No response from reCAPTCHA verification. Sorry.";
	}
	else if(!is_null($response->error-codes)) {
		$errorMsg = join(" | ", " Error codes: " . $response->error-codes);
	}
	else {
		$errorMsg = "Reason unknown. Details have been logged and hopefully this will be sorted out soon.";
	}

 	$json = (object) ["status" => "failure", "text" => "reCAPTCHA verification failed. " . $errorMsg]; 
}

echo json_encode($json);
?>

