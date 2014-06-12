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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
	<link rel="stylesheet" type="text/css" href="../css/styles_20111124.min.css" />
	<link rel="stylesheet" type="text/css" href="<?= $homeUrl ?>css/handheld_20090322.min.css" media="handheld" /><!-- See handheld_20090322.css for uncompressed version -->
	<!--[if lte IE 6]>
		<style type="text/css">
 			div#search {width:325px;}
			input.gsc-search-button {padding:1px 0 0 0;}
			body div.wiindow {position: absolute;;}
		</style>
	<![endif]-->
	
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
	
			<!-- Search box. Gets replaced by Javascript when user clicks submit button. -->
			<div id="search">
				<div id="searchControl">
					<form class="gsc-search-box" accept-charset="utf-8" method="post" action="../home/search.php">
						<table cellspacing="0" cellpadding="0" class="gsc-search-box">
							<tbody>
								<tr>
									<td class="gsc-input"><input type="text" autocomplete="off" size="10" class=" gsc-input" name="search" title="search"/></td>
									<td class="gsc-search-button"><input type="submit" value="Search" class="gsc-search-button" title="search"/></td>
									<td class="gsc-clear-button"></td>
								</tr>
							</tbody>
						</table>
						<table cellspacing="0" cellpadding="0" class="gsc-branding">
							<tbody>
								<tr>
									<td class="gsc-branding-user-defined"></td>
									<td class="gsc-branding-text"><div class="gsc-branding-text">powered by</div></td>
									<td class="gsc-branding-img"><img src="http://www.google.com/uds/css/small-logo.png" class="gsc-branding-img"/></td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>			
				<div id="searchResults"></div>
				<!--
				<div id="searchControl">Loading &hellip;</div>
				-->
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
		<div class="dragBarTop"></div>
		<div class="dragBarRight"></div>
		<div class="dragBarBottom"></div>
		<div class="dragBarLeft"></div>
	</div>
	
	<?
	// Extra js files for specific pages may have been added on index.php files
	$jsFiles[] = "mootools-1.2-core.js";
	$jsFiles[] = "mootools-1.2-more.js";
	$jsFiles[] = "imageMenu_20101126.min.js";
	$jsFiles[] = "common_20101126.js";
	foreach ($jsFiles as $file) {
		?>
		<script type="text/javascript" src="../js/<?= $file ?>"></script>
		<?
	}
	?>
	<?= $str_menuJS ?>
</body>
</html>
