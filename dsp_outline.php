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
<html lang="en"  class="nav-no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

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
	
	<link rel="stylesheet" type="text/css" href="../css/styles_20180516.css" />
-	
	<link rel="stylesheet" type="text/css" href="../css/styles_2020.css" />

	<link rel="stylesheet" type="text/css" href="../css/photoswipe.css" />
	<link rel="stylesheet" type="text/css" href="../css/default-skin/default-skin.css" />

	
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
	<a id="top"></a>
	<div class="page">
		<header>
			<p class="logo"><a href="<?= $homeUrl ?>home/index.php" title="Go to home page"><span>&nbsp;</span>Sweet and Sour</a></p>
			<p class="tagline">One is sweet and the other is &hellip; a web developer</p>
			<p class="sr-only"><a href="#content" title="Jump to content">Jump to content</a></p><!-- Hidden except for screen readers -->

			<? 
			if($displayMenu) {
				?>
				<form>
					<label id="menuBtn" class="menu openMenu" for="menuToggle" role="button" aria-label="Toggle menu" aria-controls="mainMenu">Open</label>
					<input class="menu" type="checkbox" id="menuToggle" />
					  
					<nav id="mainMenu" aria-hidden="true" aria-labelledby="menuBtn" role="navigation">
						<?= $str_menuHTML ?>
					</nav>
				</form>
				<?
			}
			else {
				?>
				<p id="returnLink">&laquo; <?= $returnLink ?></p>
				<?
			} ?>
		</header>

		<script>

		</script>
	
		<section>
			<?
			if($useInPageHeader == true) {
				; // Do nothing
			}
			else if(isset($heading1Text)) {
				echo "<h1>" . $heading1Text . "</h1>";
			}
			?>

			<article>
				<? include $contentPage; ?>
			</article>
		</section>

		<footer>
			<? if($showToTopLink == true) { ?>
				<!-- Link to top of page. -->
				<p class="toTop"><a href='#top'><img src='<?= $topIconSrc ?>' alt='To top' height='13' border='0' style='vertical-align:bottom' /></a> <a href='#top'>Top</a></p>
			<? } ?>
			<!-- Copyright notice. -->
			<p class="end" id="copyright"><?= $copyright ?></p>
		</footer>

	   <?
	   if($showDebugInfo == true) {
	      include '../includes/act_showDebugInfo.php';
	      //phpinfo();
	   }
	   ?>
	</div>
	
	<? 
	include '../includes/dsp_photoswipe.php';
	?>

	<div id="overlay" class="overlay"><div class="loading"><p><img src="../images/loading_20080830.gif" alt="Just a moment ..." />Just a moment &hellip;</p></div></div>;
	
	<script src="../js/mobile-navigation.js"></script>
	<script src="../js/desktop-navigation.js"></script>
	<script src="../js/common_2020.js"></script>

	
	<script>
		// Initialize
		MobileNavigation.setMobileMenu();
	</script>
	<script src="../js/photoswipe.js"></script>
	<script src="../js/photoswipe-ui-default.js"></script>
	<script src="../js/photoswipe_setup.js"></script>
</body>
</html>
