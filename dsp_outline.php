<? /*
dsp_outline.php
Sets framework into which all display pages (except individual photos) will be set.
Provides photo at top left, heading, subheading, menu and sets a space for the content
page.

=>| $useAmazonS3       (from config file)    
=>| $heading2Text
=>| $contentPage       (from relevant case in index.php)
=>| $displayMenu       (set on sweetandsour_conf.php, override on index.php if necessary)
=>| $javascriptFile    (optional Javascript file)
*/


$topIconSrc = $urlPrefix . "images/toTopLink.png";
$continueIconSrc = $urlPrefix . "images/continueLink.png";
$backIconSrc = $urlPrefix . "images/backLink.png";

	// Construct the menu
if($displayMenu) {
	include '../includes/qry_getMenuID.php'; 
	include '../includes/qry_menu.php'; 
	include '../includes/act_constructMenu.php'; 
}
else {
	$str_menuJS = "";
	$str_menuHTML = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
	<title>
		<?= str_replace("<span>", "", (str_replace("</span>", "", $heading1Text))) ?> 
		[Sweet and Sour]
	</title>
	
	<? 
	if($fuseAction == "welcome" || $fuseAction == "thisWebSite") {
		include "../includes/asciiPV.txt";
	}
	?>
	<link rel="shortcut icon" type="images/x-icon" href="<?= $urlPrefix ?>favicon.ico" />
	<link rel="stylesheet" type="text/css" href="../css/styles_20180428.css" />
	
	<?
	/* Extra js files for specific pages may have been added on index.php files */
	foreach ($jsFiles as $file) {
		if (strncmp(trim($file), "<script", 7) === 0) {
			?><?= $file ?><? 
		} else { 
			?><script type="text/javascript" src="../js/<?= $file ?>"></script><?
		}
	}
	?>
</head>
<?php flush(); ?>
<body class="<?= $bodyClass ?><?= $mediaSource ?>">
	<? 
	//Include function to set a thumbnail photo & caption. Called on content pages.
	include '../imageMgt/act_setThumbnailFunction.php';
	?>
	<a name="top"></a>
	<div class="page">
		<div id="header">
			<img id="logoMobile" src="<?= $urlPrefix ?>images/SweetAndSourMobile_20081220.gif" alt="Sweet and Sour" />
			<div id="logo"><a href="<?= $homeUrl ?>home/index.php" title="Go to home page"><span>&nbsp;</span>Sweet and Sour home</a></div>
			<p id="tagline">One is sweet and the other is &hellip; a web developer</p>
			<p id="jumpToContent"><a href="#content" title="Jump to content">Jump to content</a></p><!-- Hidden except for mobile devices -->
			<!-- Search was here. See includes/dsp_search.php -->
			<? 
			if($displayMenu) {
				?>
				<div id="menuWrapper">
					<!-- Menu. Javascript will append an overlay and "loading" graphic -->
					<?= $str_menuHTML ?>
				</div>
				<?
			}
			else {
				?>
				<p id="returnLink">&laquo; <?= $returnLink ?></p>
				<?
			} ?>
		</div>

		<div id="content" class="clearfix">
			<?
			if($useInPageHeader == true) {
				; // Do nothing
			}
			else if(isset($heading1Text)) {
				echo "<h1>" . $heading1Text . "</h1>";
			}
			
			include $contentPage; ?>

			<!-- Footer -->
			<div class="footer">
	   		<? if($showToTopLink == true) { ?>
	   			<!-- Link to top of page. -->
	   			<p class="toTop"><a href='#top'><img src='<?= $topIconSrc ?>' alt='To top' height='13' border='0' style='vertical-align:bottom' /></a> <a href='#top'>Top</a></p>
	   		<? } ?>
				<!-- Copyright notice. -->
				<p class="end" id="copyright"><?= $copyright ?></p>
		</div>

	   <?
	   if($showDebugInfo == true) {
	      include '../includes/act_showDebugInfo.php';
	      //phpinfo();
	   }
	   ?>
	</div>
	
	<!-- Photo frame (absolutely-positioned and display=none). -->
	<div id="photoFrame" class="window">
		<div class="titleBar">
			<span class="titleBarText">Photo</span>
			<a href="javascript:void(0)" title="Close window"></a>
		</div>
		
		<div id="photoContent" class="clientArea">
		</div>
		
		<img class="dragHandle" src="<?= $urlPrefix ?>images/dragHandle_20081128.gif" height="12" width="12" alt="Drag to resize window" />
		<div class="dragBarTop"></div>
		<div class="dragBarRight"></div>
		<div class="dragBarBottom"></div>
		<div class="dragBarLeft"></div>
	</div>
	
	<!--
	<script type="text/javascript" src="../js/mootools-1.2-core.js"></script>
	<script type="text/javascript" src="../js/mootools-1.2-more.js"></script>
	-->
	<script type="text/javascript" src="../js/mootools-core-1.6.0.js"></script>
	<script type="text/javascript" src="../js/mootools-more-1.6.0.js"></script>
	
	<script type="text/javascript" src="../js/imageMenu_20101126.js"></script>
	<script type="text/javascript" src="../js/common_20180424.js"></script>

</body>
</html>
