<?
	//qry_folderNames.php
	//Get all folder names
	$sql = "SELECT folderId, folderName FROM folders ORDER BY folderId DESC";
	$rs_folders = @mysql_query($sql);
	$allSQL .= "rs_folders (" . mysql_num_rows($rs_folders) . " records returned)<br />" . $sql . "<br /><br />";
?>