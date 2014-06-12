<? /*
fn_getPhotoURL.php
Assumes images are always in an images subdirectory.

Variables:
=>| folderName
=>| grandparentFolderName
=>| urlPrefix (from sweetandsour_conf.php)
=>| useVersionedFiles
=>| version
|=> url
*/


function getPhotoUrl($photoName, $folderName, $grandparentFolderName, $urlPrefix, $useVersionedFiles, $version) {
	
	//Determine whether to use the plain or versioned file name
	if($useVersionedFiles) {
		$photoFileName = preg_replace ('/\.(jpg|gif)/', "_" . $version . ".$1", $photoName);
	}
	else {
		$photoFileName = $photoName;
	}

	//Determine the url to the photo; $urlPrefix is set on dsp_outline.php
	if(is_null($grandparentFolderName)) {
		$url = $urlPrefix . $folderName . "/images/" . $photoFileName;
	}
	else {
		$url = $urlPrefix . $grandparentFolderName . "/images/" . $folderName . "/" . $photoFileName;
	}
	
	return $url;
}
?>
