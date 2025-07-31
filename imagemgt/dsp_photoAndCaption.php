<?php /*
dsp_displayPhotoAndCaption.php

Will put caption to the right of the photo if there is 200px space available unless forceCaptionBelow is true.

=>| photoName
*/
//include '../imagemgt/fn_getPhotoInfo.php';
//include '../imagemgt/fn_getPhotoURL.php';

$photoName = $_GET["photoName"];

//Find the information related to this photo
$photos = getPhotoInfo(array($photoName));

if($photos) {
	//Get the $url where it can be found.
	$imgSrc = getPhotoUrl($photoName, $photos[$photoName]["folderName"], $photos[$photoName]["grandparentFolderName"], $rootRelativeUrl, $useVersionedFiles, $photos[$photoName]["version"]);	
	?>
	<figure>
		<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $photos[$photoName]["width"] ?>" height="<?= $photos[$photoName]["height"] ?>" alt="See caption" />
		<figcaption class="figure__caption"><?= $photos[$photoName]["caption"] ?></figcaption>
	</figure>
	<?php
}
else {
	?>
	<p class="error">Photo not found.</p>
	<?php
}
?>
