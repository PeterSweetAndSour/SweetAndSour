<? /* dsp_photos.php

=>| $imagesPerPage
=>| $folderPath
=>| $folderUrl
=>| $filesInDB
=>| $filesInFolder
=>| $page

*/

?>
<div class="clearfix">
	<? include 'dsp_pagination.php' ?>
</div>
<?
$firstOnPage = $imagesPerPage * ($page - 1);
$remainingFiles = count($filesInFolder) - (($page-1) * $imagesPerPage);
$lastOnPage = $firstOnPage + min($remainingFiles, $imagesPerPage);

for($i = $firstOnPage; $i < $lastOnPage; $i++) {
	$filename = $filesInFolder[$i];
	$fullUrl = $folderUrl . $filename;
	$relativeUrl = str_replace ("http://localhost/sweetandsour.org", "", $fullUrl);

	// Determine if this file is already in the database
	if(array_key_exists($filename, $filesInDB)) {
		$fileDetails = $filesInDB[$filename];
		$width = $fileDetails["width"];
		$height = $fileDetails["height"];
		$linkedImg = $fileDetails["linkedImg"];
		$caption = $fileDetails["caption"];
		$inDB = true;
	}
	else {
		//Get photo size using the getImageSize funciton
		$size = @getImageSize($folderPath . $filename);
		$width = $size[0];
		$height = $size[1];
		$inDB = false;
	}
	
	?>
	<section class="photo clearfix<?= $inDB ? "" : " negativeHilite" ?>">
		<?= markupForAdminThumbnail($folderUrl, $filename, $width, $height) ?>
		<p>In database: <?= $inDB ? "<span class=\"pos\">yes</span>" : "<span class=\"neg\">no</span>" ?></p>
		<!--p>Path and File: <?= $folderPath ?><?= $filename ?></p-->
		<p><?= $width ?> x <?= $height ?> pixels</p>
		<p>File date: <?= date("F d Y H:i:s.", filemtime($folderPath . $filename)); ?></p>
		<p>Url: <?= $relativeUrl ?></p>
		<? if($inDB) { ?>
			
		<? } ?>
	</section>
	<?
}
?><? include 'dsp_pagination.php' ?><?

include '../includes/act_showDebugInfo.php';
?>
