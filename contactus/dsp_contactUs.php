<?
/* dsp_contactUs.php

This page presents a form to the user to send an email back to site owner, or thanks them for sending it.
The use of reCAPTCHA effectively requires that the user has Javascript enabled and is an accessibility issue 
https://www.w3.org/TR/turingtest/

Variables:
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
?>

<div id="noticeWarning" class="notice warning hidden"></div>
<div id="noticeSuccess" class="notice success hidden"></div>

<div id="contactUsFormWrapper" class="story">
	<p>Send us an email if you are an old friend. If we don&apos;t know you, send us an email and you will become a friend. Make sure you get your email address correct so we can reply.</p>

	<div id="GDPRNotice" class="notice info">
		<h2>GDPR Notice</h2>
		<p>This is the only page on the site where I collect and temporarily store information about you so that you can send me a message. For this to work, cookies will be set but they will disappear when you close the tab/browser. 
		If you are OK, with that&hellip;</p>
		<p><button id="continueBtn" class="continue">Continue</button></p>
	</div>

	<form id="contactUsForm" method="post" action="index.php?fuseAction=sendEmail" class="hidden">
		<div class="floating-label">
			<input type="text" class="narrow" placeholder="Your name" name="senderName" id="senderName" maxlength="50" 
				value="" required>
			<label for="senderName">Your name</label>
		</div>
		<div class="floating-label">
			<input type="email" class="narrow" placeholder="Email address" name="senderEmail" id="senderEmail" maxlength="50" 
				value="" required> 
			<label for="senderEmail">Email address</label>
		</div>
		<div class="floating-label">
			<input type="text" class="wide" placeholder="Subject" name="msgSubject" id="msgSubject" maxlength="50" 
				value="" autocomplete="off" required>
			<label for="msgSubject">Subject</label>
		</div>

		<div class="textAreaRow">
			<label for="msgText">Message</label>
			<textarea name="msgText" id="msgText" wrap="virtual" rows="10" maxlength="1000" required></textarea>
			<p id="remainingChars" class="feedback">Maximum length: 1,000 characters.</p>
		</div>

		<div class="reCaptchaRow">
			<label>Prove  you are human</label>
			<div id="reCAPTCHAWrapper" data-site-key="<?= $reCAPTCHA_siteKey ?>"></div>
			<input type="hidden" id="reCAPTCHAIsValid">
		</div>

		<div class="actionRow">
			<div class="buttonWrapper">
				<input type="submit" id="submitBtn" class="submit" name="Send" value="Send" disabled>
				<div id="submitSpinnerWrapper" class="submitSpinnerWrapper hidden">
					<? include '../includes/dsp_spinner.html'; ?>
				</div>
			</div>
		</div>
	</form>
</div>




