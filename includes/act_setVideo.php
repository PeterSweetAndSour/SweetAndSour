<?php /*
act_setVideo.php

This function creates a clickable thumbnail to play videos inside the popup window. 
Multiple video URLs can be specified to cope with multi-part videos on youtube.

=>| windowTitle
=>| linkUrl (link direct to youtube page)
=>| linkImg
=>| videoList (comma-separated list of embedUrls)
*/

function setVideo($windowTitle, $linkUrl, $linkImg, $videoList) {

	$videoList = str_replace(" ", "", $videoList);
	$videoArray = split(",", $videoList);
	
	$ajaxUrl = "<?=$rootRelativeUrl ?>includes/dsp_videoPage.php";
	$ajaxUrl .= "?windowTitle=" . rawurlencode($windowTitle);
	$ajaxUrl .= "&linkImg=" . $linkImg;
	$ajaxUrl .= "&videoList=" . rawurlencode(implode(",", $videoArray));
	$ajaxUrl .= "&videoToPlay=0";
	
	?>
	<div class="imgShadow">
		<a href="<?= $linkUrl  ?>" onclick="SweetAndSour.ajaxPopup('<?= $ajaxUrl ?>', '<?= rawurlencode($windowTitle) ?>'); return false;">
			<img src="<?= $linkImg ?>" alt="<?= $windowTitle ?>" />
		</a>
	</div>
	<?php
} 
?>
