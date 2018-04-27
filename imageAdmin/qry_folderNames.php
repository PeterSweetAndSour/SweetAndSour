<?
	//qry_folderNames.php
	//Get all folder names
	$sql = "SELECT folderId, folderName FROM folders ORDER BY folderId DESC";
	$rs_folders = $mysqli->query($sql);
	$allSQL .= "rs_folders (" . $rs_folders->num_rows . " records returned)<br />" . $sql . "<br /><br />";
?>