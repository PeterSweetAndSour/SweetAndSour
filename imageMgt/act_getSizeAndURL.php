<? /*
act_getSizeAndURL.php
Assumes images are always are in an images subdirectory.

Variables:
=>| folderName
=>| grandparentFolderName
=>| photoName

|=> url
|=> width
|=> height
*/
		//Determine the url to the photo
		if(is_null($grandparentFolderName))
			$url = $rootRelativeUrl . $folderName . "/images/" . $photoName;
		else
			$url = $rootRelativeUrl . $grandparentFolderName . "/images/" . $folderName . "/" . $photoName;
		
		//Get photo size using the getImageSize funciton
		$size = @getImageSize($url);
		
		if(is_array($size)) {
			$width = $size[0];
			$height = $size[1];
			$photoInfoFound = true;
		}
		else {
			$width = 100;
			$height = 100;
			$photoInfoFound = false;
			echo "<p>getImageSize() failed for " . $url ."</p>";
		}
		
?>
