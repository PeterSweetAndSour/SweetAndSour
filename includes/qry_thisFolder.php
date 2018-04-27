<? /*
qry_thisFolder.php
Get name of the folder given the folderID

Variables:
=>| folderId
|=> parentFolderId
|=> grandparentFolderID
|=> parentId
|=> folderName
|=> parentFolderName
|=> grandparentFolderName

*/

if($folderId != -1) {
	//Get the name of the folder that the photo is in so path can be established
	$sql = "SELECT tbl_1.folderId, tbl_1.parentFolderId, tbl_1.grandparentFolderId, tbl_1.folderName, tbl_2.folderName AS 'parentFolderName', tbl_3.folderName AS 'grandparentFolderName' ";
	$sql.= "FROM folders AS tbl_1 ";
	$sql.= "LEFT JOIN folders AS tbl_2 ON tbl_1.parentFolderId = tbl_2.folderId ";
	$sql.= "LEFT JOIN folders AS tbl_3 ON tbl_1.grandparentFolderId = tbl_3.folderId ";
	$sql.= "WHERE tbl_1.folderId = " . $folderId;
	
	
	$rs_thisFolder = $mysqli->query($sql);
	if($rs_thisFolder) {
		$allSQL .= "rs_thisFolder (" . $rs_thisFolder-num_rows . " records returned)<br>" . $sql . "<br><br>";

		$row = $rs_thisFolder$rowd->fetch_array(MYSQLI_ASSOC);
		$folderName = $row["folderName"];
		$parentFolderName = $row["parentFolderName"];
		$grandparentFolderName = $row["grandparentFolderName"];
		$parentFolderID = $row["parentFolderId"];
		$grandparentFolderID = $row["grandparentFolderId"];
	}
}
?>
