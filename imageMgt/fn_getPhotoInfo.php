<? /*
fn_getPhotoInfo.php
This file takes the photo name to get the relevant record from the database.
It then determines the url to photo and finds its size.

Note that the variable "linkedImg" will either be empty or contain the name of another
image, generally a larger version of the same image. The setThumbnail function on 
act_setThumbnail.php creates a URL to be used in the anchor tag but the URL can be
overridden if necessary when the function is called. 

Variables:
=>| photoNames (an array)

|=> photoInfoFound  true/false
|=> photos  associative array with key photoName containing an array with:
            - $folderId
            - $folderName
            - $grandparentFolderName
            - $linkedImg
            - $caption
            - $linkToFullSize
            - $version
            - $width
            - $height
*/

function getPhotoInfo($photoNames) {

	global $allSQL;
	global $photos;
	global $row;

	// Create an array into which the results will be dumped
	$photos = array();
			
	//Get details of these photos from the database
	$sql = "SELECT tbl_1.folderId, tbl_1.parentFolderId, tbl_1.grandparentFolderId, tbl_1.folderName, tbl_2.folderName AS 'parentFolderName', tbl_3.folderName AS 'grandparentFolderName', photos.photoName, photos.linkedImg, photos.caption, photos.linkToFullSize, photos.version, photos.width, photos.height ";
	$sql.= "FROM photos ";
	$sql.= "INNER JOIN folders AS tbl_1 ON photos.folderId = tbl_1.folderID ";
	$sql.= "LEFT JOIN folders AS tbl_2 ON tbl_1.parentFolderId = tbl_2.folderID ";
	$sql.= "LEFT JOIN folders AS tbl_3 ON tbl_1.grandparentFolderId = tbl_3.folderID ";
	$sql.= "WHERE photoName IN ('" . implode($photoNames, "','") . "')";
		
	$rs_photoInfo = @mysql_query($sql);
	if($rs_photoInfo) { //Check that there is a result set
		$allSQL .= "rs_photoInfo (" . @mysql_num_rows($rs_photoInfo) . " records returned)<br />" . $sql . "<br /><br />";

		if(mysql_num_rows($rs_photoInfo) > 0) { //Check that the result set contains more than zero rows.
			$photoInfoFound = true;
			
			while ($row = mysql_fetch_array($rs_photoInfo, MYSQL_ASSOC)) {
				$photoName = $row["photoName"];
				$photos[$photoName] = $row;
				/*
				$folderID              = $row["folderID"];
				$folderName            = $row["folderName"];
				$parentFolderName      = $row["parentFolderName"];
				$grandparentFolderName = $row["grandparentFolderName"];
				$linkedImg             = $row["linkedImg"];
				$caption               = $row["caption"];
				$linkToFullSize        = $row["linkToFullSize"];
				$version               = $row["version"];
				$width                 = $row["width"];
				$height                = $row["height"];
				
				echo "photoName: " . $photoName . "<br />";
				echo "folderId: " . $folderId . "<br />";
				echo "width" . $width . "<br />";
				*/
			}
			return $photos;
		}
		else {
			return false;
			echo "rs_photoInfo result set contains zero rows.<br />";
		}
	}
	else {
		return false;
		echo "No rs_photoInfo result set.<br />" . $sql . "<br />";
	}
}
?>
