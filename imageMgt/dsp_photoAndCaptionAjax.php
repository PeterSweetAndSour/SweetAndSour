<?
header("Cache-Control: no-cache");
header("text/html");
header("Pragma: nocache");
/*
dsp_displayPhotoAndCaptionAjax.php

Will put caption to the right of the photo if there is 200px space available unless forceCaptionBelow is true.

=>| photoName
=>| panorama       optional - "true"/"false"
=>| panoWidth      available if panorama is true
=>| panoHeight     available if panorama is true
*/
include '../imagemgt/fn_getPhotoInfo.php';
include '../imagemgt/fn_getPhotoURL.php';

$photoName = $_GET["photoName"];

//Find the information related to this photo
$photos = getPhotoInfo(array($photoName));

if($photos) {
	//Get the $url where it can be found.
	$imgSrc = getPhotoUrl($photoName, $photos[$photoName]["folderName"], $photos[$photoName]["grandparentFolderName"], $rootRelativeUrl, $useVersionedFiles, $photos[$photoName]["version"]);

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
		$caption = "<p>To control the panorama:</p><ul><li>drag mouse to move up/down, left/right</li><li>shift & mouse drag to zoom in</li><li>ctrl & mouse drag to zoom out</li><li>+ to zoom in by fixed increment</li><li>- to zoom out by fixed increment</li></ul>" . $photos[$photoName]["caption"];
	  }
	  else {
		$caption = $photos[$photoName]["caption"];
	  }
		
		$newWindowUrl = "/imagemgt/index.php?fuseAction=showPhotoAndCaption&amp;photoName=" . $photoName;
	?>
	<p class="print"><a target="_blank" href="<?= $newWindowUrl ?>"><img src="<?=$rootRelativeUrl ?>images/printer.png" title="printer" /></a><a target="_blank" href="<?= $newWindowUrl ?>">Print</a> (opens in a new window then select File > Print)</p>
	<div class="photoHolder">
		<div class="imgShadow">
			<?if($panorama == "false") {?>
				<img src="<?= $imgSrc ?>" width="<?= $photos[$photoName]["width"] ?>" height="<?= $photos[$photoName]["height"] ?>" />
			<?} else {?>
				<div class="left">
					<applet archive="<?=$rootRelativeUrl ?>imagemgt/ptviewer.jar" code="ptviewer.class" width="<?= $panoWidth ?>" height="<?= $panoHeight ?>">
						<param name="file" value="<?= $url ?>">
						<param name="auto" value="0.125">
					</applet>
					</div>
			<?}?>
		</div>		
	</div>
	<div class="caption">
		<?= $caption ?>
	</div>
	<div class="footer">
		<p id="photoLink" class="end">URL to this photo: http://www.sweetandsour.org/imagemgt/index.php?fuseAction=showPhotoAndCaption&amp;photoName=<?= $photoName ?></p>
	</div>
	<?
}
else {
	?>
	<p>Oops. <?= $photoName ?> not found.</p>
	<?
}
?>
