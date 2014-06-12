<?
// Check for a post
function send_email(){

	// Grab the data
	$subject = $_POST['subject'];
	
	if($_POST['to_email']) {
		$email_addresses = explode(";", $_POST['to_email']);
		$_SESSION['email_addresses_str'] = $_POST['to_email'];
	}
	
	if($_POST['emailBody']) {
		$body = stripslashes($_POST['emailBody']);
		$_SESSION['email_body'] = stripslashes($_POST['emailBody']);
	}

	// Error checking
	if (! trim($subject) ){
		$message = "I'm sorry, but you must enter a subject.";
		$_SESSION['message'] = $message;
		 return false;
	}
	if (!$email_addresses){
		$message = "I'm sorry, but you must enter an email address.";
		$_SESSION['message'] = $message;
		 return false;
	}
	if (! trim($body) ){
		$message = "I'm sorry, but you must enter a message to send.";
		$_SESSION['message'] = $message;
		 return false;
	}

	// headers
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: " . $_SESSION['from_email'] . "\r\n";
	$headers .= "Reply-To: doNotReply@sweetandsour.org\r\n";
	
	// Send it!
	foreach($email_addresses as $email_address){
		mail($email_address, $subject, $body, $headers);
	}

	$message = "Sent successfully from " . $_SESSION['from_email'] . " to " . $_SESSION['email_addresses_str'] . " at " . date("H:i:s");;
	$_SESSION['message'] = $message;

	return true;
}

echo "<!-- session[from_email]: " . $_SESSION['from_email'] . "-->";

// Override default 'from' email address
if($_GET['from_email'] != "") {
	$_SESSION['from_email'] = $_GET['from_email'];
}
else {
	if($_SESSION['from_email'] == "") {
		$_SESSION['from_email'] = "testOnly@sweetandsour.org";
	}
}

if($_SESSION['email_body'] == "") {
	$_SESSION['email_body'] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
	<html>
	<head>
	  <title>HTML Email</title>
	</head>
	<body>
	  <style type=\"text/css\">

	  </style>

	</body>
	</html>";
}

//If page has been submitted back to itself, run the send_email function
if ($_POST['postFlag']) {
	$result = send_email();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Send an Email</title>
<style type="text/css">
	body{margin:0;	padding:20px; font-family:"Lucida Grande", Arial, Verdana, sans-serif; font-size:11px;}
	#wrapper{width:500px; padding:20px; margin:0 auto; border:1px solid #EEE;}
	h1{margin:0; font-size:20px;}
	h2{font-size:14px;}
	h3{font-size:12px;}
	.code{font-family:"Courier New", Courier, mono;; font-size:10px;}
	em.highlight{font-style:normal; background:#FFFFCC; }
	table{ width:100%; border-spacing:1px; background:#EEE; }
	table td{ background:#FFF; padding:5px; }
	table th{ background:#FFF; text-align:left; padding:5px; }
	.note{color:#999;}
	.message{	background:#cfc;	border:1px solid #C7E3F0;	padding:15px;	margin:15px 0;}
	.error{	background:#fcc;	border:1px solid #FBC84B;	padding:15px;	margin:15px 0;}
	p.resources {margin-bottom:0}
	ul.resources {margin-top:0}
</style>
</head>

<body>
<div id="wrapper">
	<h1>Send an HTML Email</h1>
	<? if ($result) : ?>
		<div class="message"><?= $_SESSION['message'] ?></div>
	<? elseif ($_POST['postFlag']) : ?>
			<div class="error"><?= $_SESSION['message'] ?></div>
	<? endif; ?>
	<form action="index.php" method="post">
		<p>
			<strong>Subject:</strong><br />
			<input type="text" size="40" name="subject" value="<?= $_POST['subject'] ?>" />
		</p>
		<p>
			<strong>From:</strong><br />
			<?= $_SESSION['from_email'] ?>
		</p>
		<p>
			<strong>To:&nbsp;<span class="note">(Separate multiple email addresses with a ";".)</span></strong><br />
			<input type="text" size="40" name="to_email" value="<?= $_SESSION['email_addresses_str'] ?>" />
		</p>
		<p>
			<strong>Message:&nbsp;<span class="note">(Copy/paste HTML but note that backslashes will generally be stripped.)</span></strong><br />
			<textarea cols="80" rows="20" name="emailBody"><?= $_SESSION['email_body'] ?></textarea>
		</p>
		<p><input type="submit" value="Send Email" /></p>
		<input type="hidden" name="postFlag" value="true" />
		
		<p class="resources">Comments:</p>
		<ul class="resources">
			<li>Send to your ...@fool.com address to get it basically right, then motleyfool@cp.strongdelivery.com for final testing in multiple clients.</li>
			<li>Messages will appear to be from testOnly@sweetandsour.org.</li>
			<li>You should include a DOCTYPE declaration but as of 2006 old-style HTML 4.01 is best supported</li>
			<li>Since the &lt;head&gt; is often stripped by web mail clients, the style block is best located in the &lt;body&gt;.</li>
		</ul>
		
		<p class="resources">Resources:</p>
		<ul class="resources">
			<li><a href="http://wiki.foolhq.com/moin.cgi/CreativeServices#line208">the wiki</a> (needs updating; also references some of the links below)</li>
			<li><a href="http://www.thinkvitamin.com/features/design/html-emails">HTML Emails - Taming the Beast</a></li>
			<li><a href="http://www.campaignmonitor.com/blog/archives/2005/11/html_email_desi.html">Email Design Guidelines for 2006</a></li>
			<li><a href="http://www.campaignmonitor.com/blog/2006/03/a_guide_to_css_support_in_emai.html">A Guide to CSS Support in Email</a> (includes tables of what works in different email clients)</li>
			<li><a href="http://www.campaignmonitor.com/blog/archives/2005/08/optimizing_css_1.html">Optimizing CSS presentation in HTML emails</a></li>
			<li><a href="http://www.campaignmonitor.com/blog/archives/2006/07/a_css_solution.html">A CSS Solution to Image Blocking</a></li>
			<li><a href="http://www.sitepoint.com/blogs/2007/01/10/microsoft-breaks-html-email-rendering-in-outlook/">Microsoft Breaks HTML Email Rendering in Outlook 2007</a></li>
			<li><a href="http://alistapart.com/articles/cssemail">CSS and Email, Kissing in a Tree</a></li>
		</ul>
		<p style="color:#999">Override default 'from' email address with ?from_email=whatever@somewhere.com</p>
	</form>
</div>
</body>
</html>