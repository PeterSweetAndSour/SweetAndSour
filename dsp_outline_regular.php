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
	$menuJS = "";
	$menuHTML = "";
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
	<link rel="stylesheet" type="text/css" href="../css/styles_20100831.css" /><!-- See styles_20090920.css for uncompressed version -->
	<link rel="stylesheet" type="text/css" href="<?= $homeUrl ?>css/handheld_20090322.min.css" media="handheld" /><!-- See handheld_20090322.css for uncompressed version -->

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
	
			<!-- Search box -->
			<div id="search">
				<div id="searchControl">Loading ...</div>
				<div id="searchResults"></div>
			</div>
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
				<p class="end" id="standards">This site best viewed with a standards-compliant browser. <a class="external" href="http://www.webstandards.org/learn/faq/" title="Learn more than you probably wanted to know">Learn more</a>.</p>
	   		<div class="end">
	   			<a href="http://getfirefox.com/" title="Get Firefox - The Browser, Reloaded."><img src="<?= $urlPrefix ?>images/getFirefox_80x15.png" width="80" height="15" border="0" alt="Get Firefox" /></a> 
					<a href="http://www.opera.com/download/" title="Get Opera" ><img src="<?= $urlPrefix ?>images/getOpera94x15b.gif" width="94" height="15" alt="Get Opera" /></a> 
				</div>
			</div>
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
		<div class="dragBarLeft"></div>
		<div class="dragBarBottom"></div>
		<div class="dragBarRight"></div>
	</div>
	
	<script src="http://www.google.com/jsapi?key=ABQIAAAAKZa0m7K346FCikKHHE-NgRR8wM3bJBMlRhAtPYyu9yNs3cni-BRTweLyvA1cMNrN0zjIV7JZ2-aRzg" type="text/javascript"></script>
	<?
	// Extra js files for specific pages may have been added on index.php files
	$jsFiles[] = "mootools-1.2-core.js";
	$jsFiles[] = "mootools-1.2-more.js";
	$jsFiles[] = "imageMenu_20081017.min.js";
	$jsFiles[] = "common_20090723.js";
	foreach ($jsFiles as $file) {
		?>
		<script type="text/javascript" src="../js/<?= $file ?>"></script>
		<?
	}
	?>
	<?= $str_menuJS ?>
</body>
</html>
