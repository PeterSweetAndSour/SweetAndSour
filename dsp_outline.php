<? 
/*
dsp_outline.php
Sets framework into which all display pages (except individual photos) will be set.
Provides photo at top left, heading, subheading, menu and sets a space for the content
page.

=>| $useAmazonS3       (from config file)    
=>| $heading1Text
=>| $contentPage       (from relevant case in index.php)
=>| $displayMenu       (set on sweetandsour_conf.php, override on index.php if necessary)
=>| $javascriptFile    (optional Javascript file)
=>| $pageContentOnly   (used with contentToJsonInDb.php)
*/

//Include function to set a thumbnail photo & caption. Called on content pages.
include '../imagemgt/act_setThumbnailFunction.php';

// ------------------------------------------------------------------------
// For getting content into database with React site
if(isset($_GET["pageContentOnly"]) && $_GET["pageContentOnly"] == "true") {
	if(isset($heading1Text)) {
		echo "<h1>" . $heading1Text . "</h1>";
	}
	include $contentPage;
	return;
}
// ------------------------------------------------------------------------


$topIconSrc = $rootRelativeUrl . "images/toTopLink.png";
$backIconSrc = $rootRelativeUrl . "images/backLink.png";

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
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />

	<title>
		<?= str_replace("<span>", "", (str_replace("</span>", "", isset($heading1Text) ? $heading1Text : ""))) ?> 
		[Sweet and Sour]
	</title>
	
	<? 
	if($fuseAction == "welcome" || $fuseAction == "thisWebSite") {
		include $_SERVER['DOCUMENT_ROOT'] . $rootRelativeUrl. "includes/asciiPV.txt";
	}
	?>
	<link rel="shortcut icon" type="images/x-icon" href="<?=$rootRelativeUrl ?>favicon.ico" />

	<? 
	if($compress) {
		?>
		<link rel="stylesheet" type="text/css" href="<?=$rootRelativeUrl ?>css/combined.min.css" />
		<?
	}
	else {
		?>
		<link rel="stylesheet" type="text/css" href="<?=$rootRelativeUrl ?>css/styles_2020.css" />
		<link rel="stylesheet" type="text/css" href="<?=$rootRelativeUrl ?>css/photoswipe.css" />
		<link rel="stylesheet" type="text/css" href="<?=$rootRelativeUrl ?>css/default-skin/default-skin.css" />
		<?
	}
	?>

</head>
<?php flush(); ?>
<body class="<?= $bodyClass ?><?= $mediaSource ?>">
	<a id="top"></a>
	<div class="page">
		<header>
			<p class="logo"><a href="<?= $homeUrl ?>home" title="Go to home page">Sweet and Sour</a></p>
			<p class="tagline">One is sweet and the other is &hellip; a web developer</p>
			<p class="sr-only"><a href="#content">Jump to content</a></p><!-- Hidden except for screen readers -->
			<? 
			if($displayMenu) {
				?>
				<form class="menu">
					<input class="menu" type="checkbox" id="menuToggle" />
					<label id="menuBtn" class="menu" for="menuToggle" role="button" aria-label="Toggle menu" aria-controls="imageMenu">Open</label>
					<?= $str_menuHTML ?>
					<div id="menuOverlay" class="menuOverlay"><div class="loading"><p><img src="<?=$rootRelativeUrl ?>images/loading_20080830.gif" alt="Just a moment ..." />Just a moment &hellip;</p></div></div>
				</form>
				<?
			}
			?>
		</header>	
	
		<main id="content">
			<?
			if($useInPageHeader == true) {
				; // Do nothing
			}
			else if(isset($heading1Text)) {
				echo "<h1>" . $heading1Text . "</h1>";
			}
			?>
			<? include $contentPage; ?>
		</main>

		<footer>
			<? if($showToTopLink == true) { ?>
				<!-- Link to top of page. -->
				<p><a class="toTop" href='#top'>To top</a></p>
			<? } ?>
			<!-- Copyright notice. -->
			<p class="copyright"><?= $copyright ?></p>
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

	if($compress) {
		?>
		<script src="<?=$rootRelativeUrl ?>js/combined.min.js"></script>
		<?
	}
	else {
		?>
		<script src="<?=$rootRelativeUrl ?>js/mobile-navigation.js"></script>
		<script src="<?=$rootRelativeUrl ?>js/desktop-navigation.js"></script>
		<script src="<?=$rootRelativeUrl ?>js/common_2020.js"></script>

		<script src="<?=$rootRelativeUrl ?>js/photoswipe.js"></script>
		<script src="<?=$rootRelativeUrl ?>js/photoswipe-ui-default.js"></script>
		<script src="<?=$rootRelativeUrl ?>js/photoswipe_setup.js"></script>
		<?
	}

	/* Extra js files for specific pages may have been added on index.php files */
	foreach ($jsFiles as $file) {
		if (strncmp(trim($file), "<script", 7) === 0) {
			?><?= $file ?><? 
		} else { 
			?><script type="text/javascript" src="<?=$rootRelativeUrl ?>js/<?= $file ?>"></script><?
		}
	}
	?>
</body>
</html>
