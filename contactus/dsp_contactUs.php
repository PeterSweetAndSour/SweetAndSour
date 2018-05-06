<?

/* dsp_contactUs.php

This page presents a form to the user to send an email back to site owner, or thanks them for sending it.

Variables:
=>| URL.msgSent        (exists after sending email, value will be "true")
=>| URL.formError
=>| URL.errorCodes   
=>| Session.senderName
=>| Session.senderEmail
=>| Session.msgSubject
=>| Session.msgText
|=> Form.senderName
|=> Form.senderEmail
|=> Form.msgSubject
|=> Form.msgText

*/
	
if(isset($_GET["msgSent"])) { ?>
	<div class="story">
		<p>Thank you for your email! I will try to reply within 24 hours.</p>
	</div>
	<? 
}
elseif(isset($_GET["formError"])) { ?>
	<div class="story">
		<div align="center">
			<p>Something went wrong: <?= $_GET["errorCodes"] ?></p>
		</div>
	</div>
	<? 
}
else { ?>
	<div class="story">
		<p>Send us an email if you are an old friend. If we don't know you, send us an email and you will become a friend. Make sure you get your email address correct so we can reply.</p>

		<form method="post" action="index.php?fuseAction=sendEmail">
			<div class="inputRow">
				<label for="senderName">Your name:</label>
				<input type="text" name="senderName" id="senderName" maxlength="50" value="<?= isset($_SESSION["senderName"]) ? $_SESSION["senderName"] : "" ?>" required>
			</div>
			<div class="inputRow">
				<label>Your email address:</label>
				<input type="email" name="senderEmail" id="senderEmail" maxlength="50" value="<?= isset($_SESSION["senderEmail"]) ? $_SESSION["senderEmail"] : "" ?>" required>
			</div>
			<div class="inputRow">
				<label>Subject:</label>
				<input type="text" name="msgSubject" id="msgText" maxlength="50" value="<?= isset($_SESSION["msgSubject"]) ? $_SESSION["msgSubject"] : "" ?>" required>
			</div>
			<div class="inputRow">
				<label for="msgText">Message:</label>
				<textarea name="msgText" id="msgText" wrap="virtual" rows="15" required><?= isset($_SESSION["msgText"]) ? $_SESSION["msgText"] : "" ?></textarea>
			</div>
			<div class="inputRow">
				<label>Prove  you are human:</label>
				<div id="reCAPTCHA-wrapper"></div>
				<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
			</div>
			<div class="actionRow">
				<input type="submit" name="Send" value="Send">
			</div>
		</form>
	</div>
	<? 
} ?>

