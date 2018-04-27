<? /*
dsp_displayPhotoAndCaption.php

Will put caption to the right of the photo if there is 200px space available unless forceCaptionBelow is true.

=>| photoName
=>| altText
=>| panorama       optional - "true"/"false"
=>| panoWidth      available if panorama is true
=>| panoHeight     available if panorama is true
*/
include '../imageMgt/fn_getPhotoInfo.php';
include '../imageMgt/fn_getPhotoURL.php';

$photoName = $_GET["photoName"];

//Find the information related to this photo
$photos = getPhotoInfo(array($photoName));

if($photos) {
	//Get the $url where it can be found.
	$imgSrc = getPhotoUrl($photoName, $photos[$photoName]["folderName"], $photos[$photoName]["grandparentFolderName"], $urlPrefix, $useVersionedFiles, $photos[$photoName]["version"]);

	//If $panorama is true, get applet display size
	if(isset($_GET["panorama"])) {
		$panorama = $_GET["panorama"];
	}
	else {
		$panorama = "false";
	}
		
	if($panorama == "true") {
		if(isset($_GET["panoWidth"]))
			$panoWidth = $_GET["panoWidth"];
		else
			$panoWidth = 320;
			
		if(isset($_GET["panoWidth"]))
			$panoHeight = $_GET["panoHeight"];
		else
			$panoHeight = 200;
		
		//Also add expanatory text to caption
	  $caption = "<p>To control the panorama:</p><ul><li>drag mouse to move up/down, left/right</li><li>shift & mouse drag to zoom in</li><li>ctrl & mouse drag to zoom out</li><li>+ to zoom in by fixed increment</li><li>- to zoom out by fixed increment</li></ul>" . $caption;
	}
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<title>Lan and Peter's web site</title>
		<link rel="shortcut icon" type="images/x-icon" href="http://www.sweetandsour.org/favicon.ico" />
		<link rel="stylesheet" type="text/css" media="screen,print"  href="../css/styles_20111124.min.css" />
		<style type="text/css">
			html body {
				background-color:#fff;
				background-image:none;
			}
		</style>
	</head>
	<!-- This file in imageMgt directory -->
	<body class="photoPage">
		<div class="photoHolder">
			<div class="imgShadow">
				<?if($panorama == "false") {?>
					<img src="<?= $imgSrc ?>" width="<?= $photos[$photoName]["width"] ?>" height="<?= $photos[$photoName]["height"] ?>" />
				<?} else {?>
					<div class="left">
						<applet archive="../imageMgt/ptviewer.jar" code="ptviewer.class" width="<?= $panoWidth ?>" height="<?= $panoHeight ?>">
							<param name="file" value="<?= $url ?>">
							<param name="auto" value="0.125">
						</applet>
						</div>
				<?}?>
			</div>		
		</div>
		<div class="caption">
			<?= $photos[$photoName]["caption"] ?>
		</div>
	</body>
	</html>
	<?
}
else {
	?>
		<p>Oops. <?= $photoName ?> not found.</p>
	<?
}
?>
