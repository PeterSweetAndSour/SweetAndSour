<? /*
act_getPath.php

Get the name of the folder and if applicable, the name of the grandparent folder, 
that the photo is in so path can be established
=>| folderName
=>| parentFolderName
=>| grandparentFolderName
|=> $folderPath
|=> $folderUrl
*/

if(isset($folderName)) {
	
	//Determine the url to the photo
	if(!is_null($grandparentFolderName)) {
		$folderPath = $rootRelativeUrl . $grandparentFolderName . "/images/" . $parentFolderName . "/" . $folderName . "/";
		$folderUrl = $homeUrl . $grandparentFolderName . "/images/" . $parentFolderName . "/" . $folderName . "/";
	}
	else if(!is_null($parentFolderName)) {
		$folderPath = $rootRelativeUrl . $parentFolderName . "/images/" . $folderName . "/";
		$folderUrl = $homeUrl . $parentFolderName . "/images/" . $folderName . "/";
	}
	else {
		$folderPath = $rootRelativeUrl . $folderName . "/images/";
		$folderUrl = $folderName . "/images/";
	}
}
?>


