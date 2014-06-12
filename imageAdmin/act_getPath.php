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
		$folderPath = $homePath . $grandparentFolderName . "/images/" . $parentFolderName . "/" . $folderName . "/";
		$folderUrl = $homeUrl . $grandparentFolderName . "/images/" . $parentFolderName . "/" . $folderName . "/";
	}
	else if(!is_null($parentFolderName)) {
		$folderPath = $homePath . $parentFolderName . "/images/" . $folderName . "/";
		$folderUrl = $homeUrl . $parentFolderName . "/images/" . $folderName . "/";
	}
	else {
		$folderPath = $homePath . $folderName . "/images/";
		$folderUrl = $folderName . "/images/";
	}
}
?>


