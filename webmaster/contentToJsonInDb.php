<?php
/*
To convert the site to React, I need the content available in JSON format ready to slot into the page which
means I can't embed setThumbnail(...) functions in PHP. 

Run http://localhost:8080/sweetandsour/webmaster/contentToJsonInDb.php after making changes to any content
page to update the database but only run it for the required directories since it is slow.
*/

$directoriesToSearch = [];
array_push($directoriesToSearch, "artsandculture");
// array_push($directoriesToSearch, "canoe");
// array_push($directoriesToSearch, "contactus");
// array_push($directoriesToSearch, "home");
// array_push($directoriesToSearch, "letters");
// array_push($directoriesToSearch, "photos");
// array_push($directoriesToSearch, "tehnology");
// array_push($directoriesToSearch, "timeforjustice");
// array_push($directoriesToSearch, "wherewelive");
// array_push($directoriesToSearch, "whoweare");

$pathToStartFolder = "../";
$directory_iterator = new RecursiveDirectoryIterator($pathToStartFolder); // start from directory above "webmaster".
$iterator = new RecursiveIteratorIterator($directory_iterator);

class CustomFilterIterator extends FilterIterator {
	function accept() {
		$current = $this -> getInnerIterator() -> current();
		return (
			$current -> isFile() &&
			(substr(($current -> getFilename()), 0, 4) == "dsp_")
		);
	}
}

$filter_iterator = new CustomFilterIterator($iterator);
$filesToProcess = [];

foreach ($filter_iterator as $file) {
	$path = $file -> getPathname();
	$topLevelFolder = explode("\\", $path)[1]; // Must escape the backslash
	if(in_array($topLevelFolder, $directoriesToSearch)) {
		array_push($filesToProcess, $path);
	}
}

foreach ($filesToProcess as &$file) {
    echo $file . "<br /  >";

	// For each, run setThumbnail script to get output text

	// store escaped json text in db

}

?>