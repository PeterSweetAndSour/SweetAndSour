<?php /* Get list of image file names to use in setThumbnail function */

$directory = "D:\Inetpub\wwwroot\sweetAndSour\photos\images\Maine2009";

$photoFiles = scandir($directory);
foreach ($photoFiles as $filename) {
	if($filename != "." && $filename != "..") {
		$indexSm = stripos($filename, "Sm");
		if($indexSm > -1) {
			echo "\"" . $filename . "\",<br/>";
		}
	}
}
?>