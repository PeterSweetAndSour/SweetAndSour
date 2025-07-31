<?php /*
fn_getPhotoURL.php
Assumes images are always in an images subdirectory.

Variables:
=>| folderName
=>| grandparentFolderName
=>| homePath (from sweetandsour_conf.php)
=>| useVersionedFiles
=>| version
|=> url
*/


function getPhotoUrl($photoName, $folderName, $grandparentFolderName, $rootRelativeUrl, $useVersionedFiles, $version) {
	
	//Determine whether to use the plain or versioned file name
	if($useVersionedFiles) {
		$photoFileName = preg_replace ('/\.(jpg|gif|webp)/', "_" . $version . ".$1", $photoName);
	}
	else {
		$photoFileName = $photoName;
	}

	//Determine the url to the photo
	if(is_null($grandparentFolderName)) {
		$url = $rootRelativeUrl . $folderName . "/images/" . $photoFileName;
	}
	else {
		$url = $rootRelativeUrl . $grandparentFolderName . "/images/" . $folderName . "/" . $photoFileName;
	}
	
	return $url;
}
?>
