<?

/* dsp_contactUs.php

This page presents a form to the user to send an email back to site owner, or thanks them for sending it.

Variables:
=>| URL.msgSent        (exists after sending email, value will be "true")
=>| URL.captchaError   
=>| Session.senderName
=>| Session.senderEmail
=>| Session.msgSubject
=>| Session.msgText
|=> Form.senderName
|=> Form.senderEmail
|=> Form.msgSubject
|=> Form.msgText

*/

$phJavascript = "<script type'\"text/javascript\" src=\"validateEmailForm.js\"></script>";
$publickey = "6LcWaL4SAAAAAAUD55YamDp3wTMka-BH5IRvMiJ2";
require_once("recaptchalib.php");
	
if(isset($_GET["msgSent"])) { ?>
<div class="story">
	<div align="center">
	 	<p><br><br>Thank you for your email. One of us will reply within 24 hours.<br><br><br></p>
	</div>
</div>

<? }
else { ?>
<div class="story">
	<p>Send us an email if you are an old friend. If we don't know you, send us an email and you will become a friend. Make sure you get your email address correct so we can reply.</p>

	<form method="post" action="index.php?fuseAction=sendEmail" onsubmit="return validateForm()">
		<table border="0" cellpadding="2" cellspacing="0" width="465">
			<tbody>
				<tr>
					<th width="150">Your name:</th>
					<td><input type="text" name="senderName" size="36" maxlength="36" value="<?= isset($_SESSION["senderName"]) ? $_SESSION["senderName"] : "" ?>" style="width:306px" /></td>
				</tr>
				<tr>
					<th>Your email addr.:</th>
					<td><input type="text" name="senderEmail" size="36" maxlength="36" value="<?= isset($_SESSION["senderEmail"]) ? $_SESSION["senderEmail"] : "" ?>" style="width:306px" /></td>
				</tr>
				<tr>
					<th>Subject:</th>
					<td><input type="text" name="msgSubject" size="36" maxlength="50" value="<?= isset($_SESSION["msgSubject"]) ? $_SESSION["msgSubject"] : "" ?>" style="width:306px" /></td>
				</tr>
				<tr>
					<th valign="top">Message:</th>
					<td>
						<textarea name="msgText" wrap="virtual" rows="15" cols="36"><?= isset($_SESSION["msgText"]) ? $_SESSION["msgText"] : "" ?></textarea>
					</td>
				</tr>
				<tr>
					<th valign="top">Prove  you are human:</th>
					<td>
						<? if(isset($_GET["captchaError"])) { ?>
							<p style="color:red"><strong>Oops. You got the words wrong. Try again.</strong></p>
						<? } ?>
						<? echo recaptcha_get_html($publickey); ?>
					</td>
				<tr>
					<td colspan="2" align="right"><input type="submit" name="Send" value="Send" /></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<? } ?>

