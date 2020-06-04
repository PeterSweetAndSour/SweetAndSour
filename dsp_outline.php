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
	<!--
	<link rel="stylesheet" type="text/css" href="../css/styles_20180516.css" />
-	-->
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
					<div class="overlay"></div>;
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
	
	<!--
	<script src="../js/common_2020.js"></script>
	-->
	<script>
		var lastRadioBtnSelected = null;
		var lastLabelSelected = null;

		function resetMenu() {
			var nav = menuToggle.parentNode.querySelector("nav");
			var labels = nav.querySelectorAll("label");
			var radioBtns = nav.querySelectorAll("input");
			var label, radioBtn;
			for(var i=0; i < labels.length; i++) {
				label = labels[i];
				radioBtn = radioBtns[i];

				label.classList.remove("checked");
				radioBtn.checked = false;

				lastradioBtnSelected = null;
				lastLabelSelected = null;
			}
		}

		function _setMobileMenu() {
			// Set event listener on the button to open/close the menu
			var menuToggle = document.querySelector("#menuToggle");
			var menuLabel = menuToggle.parentNode.querySelector("label");
			var nav = menuToggle.parentNode.querySelector("nav");
			menuToggle.addEventListener("click", function() {
				if(menuToggle.checked) { // User has opened the menu
					menuLabel.innerHTML = "Close";
					menuLabel.classList.remove("openMenu");
					menuLabel.classList.add("closeMenu");
					menuLabel.classList.add("touched");
					nav.setAttribute("aria-hidden", "false");
				}
				else {
					menuLabel.innerHTML = "Open";
					menuLabel.classList.remove("closeMenu");
					menuLabel.classList.add("openMenu");
					nav.setAttribute("aria-hidden", "true");
					resetMenu();
				}
			})

			// TIL that clicking on a label creates a click event on the label AND THEN a separate click event on the associated input.
			// Knowing that, look for the event on the radio button since if changes are made on the 1st event, they may be undone by 2nd.
			nav.addEventListener("click", function(evt) {
				var src = evt.target;
				if(src.tagName === "INPUT") {
					var radioBtnSelected = src;

					// Get associated label and all radio buttons
					var labelSelected = radioBtnSelected.closest("li").querySelector("label");

					if(labelSelected.classList.contains("checked")) { //radioBtnSelected.checked will always be true
						radioBtnSelected.checked = false; // This will collapse menu if same menu item selected second time
						labelSelected.classList.remove("checked");
					}
					else {
						radioBtnSelected.checked = true; // should be anyway
						labelSelected.classList.add("checked");

						// Now remove "checked" class on LIs in previous branch and 
						// ensure radio buttons in that branch, not in this branch, are unchecked.
						if(lastRadioBtnSelected && radioBtnSelected != lastRadioBtnSelected) {
							var listItemsForLastSelection = [],
								listItemsForSelection = [],
								listItemLastSelected = lastRadioBtnSelected.closest("li"),
								listItemSelected = radioBtnSelected.closest("li"),
								nodeInNav,
								radioBtn,
								label;

							nodeInNav = listItemLastSelected;
							while(nodeInNav.tagName !== "NAV") {
								if(nodeInNav.tagName === "LI") {
									listItemsForLastSelection.push(nodeInNav);
								}
								nodeInNav = nodeInNav.parentNode;
							}

							nodeInNav = listItemSelected;
							while(nodeInNav.tagName !== "NAV") {
								if(nodeInNav.tagName === "LI") {
									listItemsForSelection.push(nodeInNav);
								}
								nodeInNav = nodeInNav.parentNode;
							}
							
							// Elements in previous heirarchy that are not in the current one
							var difference = listItemsForLastSelection.filter(x => !listItemsForSelection.includes(x));
							difference.forEach((listItem) => {
								radioBtn = listItem.querySelector("input");
								radioBtn.checked = false;

								labelSelected = listItem.querySelector("label");
								labelSelected.classList.remove("checked");
							})
						}
					}

					// If previously selected label/radio button precedes the current one in the DOM, the visible position of the current label 
					// will change when the previous list one collapses so scroll so that user still has finger over the label they touched.			
					var radioBtnsInNav = nav.querySelectorAll("li > input");

					for(var i=0; i < radioBtnsInNav.length; i++) {
						radioBtn = radioBtnsInNav[i];
						if(radioBtn === radioBtnSelected) {
							break; // No need to do anything - selected label above the one previously selected
						}
						else if(lastRadioBtnSelected && radioBtn === lastRadioBtnSelected) {
							var clickedPointAtY = evt.pageY;
							var labelBoundingInfo = labelSelected.getBoundingClientRect();
							var midPointOfLabelNowAtY = labelBoundingInfo.y + labelBoundingInfo.height/2 + window.scrollY;
							window.scrollBy(0, midPointOfLabelNowAtY - clickedPointAtY);
						}
					}

					lastRadioBtnSelected = radioBtnSelected;
					lastLabelSelected = labelSelected;
					
				}
			});
		}

		_setMobileMenu() ;
	</script>
	<script src="../js/photoswipe.js"></script>
	<script src="../js/photoswipe-ui-default.js"></script>
	<script src="../js/photoswipe_setup.js"></script>
</body>
</html>
