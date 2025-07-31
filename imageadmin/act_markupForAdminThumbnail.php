<?php
function markupForAdminThumbnail($folderUrl, $filename, $width, $height) {
	$maxDimension = 200;
	if($height > $maxDimension || $width > $maxDimension) {
		if($width > $height) {
			$displayWidth = $maxDimension;
			$displayHeight = $maxDimension * $height/$width;
		}
		else {
			$displayWidth = $maxDimension * $width/$height;
			$displayHeight = $maxDimension;
		}
	}
	else {
		$displayWidth = $width;
		$displayHeight = $height;
	}
	
	return "<img src=\"$folderUrl$filename\" width=\"$displayWidth\" height=\"$displayHeight\" />";
}
?>