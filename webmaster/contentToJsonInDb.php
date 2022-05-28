<?php
/*
To convert the site to React, I need the content available in JSON format ready to slot into the page which
means I can't embed setThumbnail(...) functions in PHP. 

Run http://localhost:8080/sweetandsour/webmaster/contentToJsonInDb.php after making changes to any content
page to update the database but only run it for the required directories since it is slow.
*/
$fuseAction = null;

include '../../sweetandsour_conf.php';
include '../../sweetandsour_db_admin.php'; // allow insert
include '../includes/act_getDBConnection.php';
include '../includes/qry_menu.php'; // gives $arr_menuData

// Get array of paths/URLs that form the menu (borrowing from act_constructMenu.php)
// If editing just one or two files, put the paths in root relative format inside the square brackets,
// otherwise it will do everything so, for example: /sweetandsour/wherewelive/washington
$pathsToProcess = [];

if(count($pathsToProcess) == 0) {
	for($i=0; $i < count($arr_menuData); $i++) {
		$path = $rootRelativeUrl . $arr_menuData[$i]["folder_name"] . "/" . $arr_menuData[$i]["fuse_action"];
		array_push($pathsToProcess, $path);
	}
}

for($j=0; $j < count($pathsToProcess); $j++){

	// Get the H1 from index.php and content from .php file for each page
	$path = $pathsToProcess[$j];
	echo $path . "<br />";
	$localURL = "http://localhost:8080" . $path . "?pageContentOnly=true";

	$html = file_get_contents($localURL);

	// Insert it in the database if new, or update if existing
	$sql = "INSERT INTO content(path, html) 
	        VALUES (?, ?)
					ON DUPLICATE KEY UPDATE 
					   html = VALUES(html)";

	if($stmt = $mysqli -> prepare($sql)) {
		$stmt -> bind_param("ss", $path, $html);
		$stmt -> execute();
		$stmt -> close();
	}
	flush();
}
echo "Done";
?>