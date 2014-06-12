<?
	//qry_folderNames.php
	//Get all folder names
	$sql = "SELECT folderID, folderName FROM folders ORDER BY folderID";
	$rs_folders = @mysql_query($sql);
	$allSQL .= "rs_folders (" . mysql_num_rows($rs_folders) . " records returned)<br />" . $sql . "<br /><br />";
?>