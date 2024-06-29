<?php /*
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
	global $mysqli;

	// Create an array into which the results will be dumped
	$photos = array();
			
	//Get details of these photos from the database
	$sql = "SELECT ";
	$sql.= "	tbl_1.folderID, ";
	$sql.= "	tbl_1.grandparentFolderID, ";
	$sql.= "	tbl_1.folderName, ";
	$sql.= "	tbl_2.folderName AS 'grandparentFolderName',  ";
	$sql.= "	photos.photoName,  ";
	$sql.= "	photos.linkedImg,  ";
	$sql.= "	photos.caption,  ";
	$sql.= "	photos.linkToFullSize,  ";
	$sql.= "	photos.version,  ";
	$sql.= "	photos.width,  ";
	$sql.= "	photos.height,  ";
	$sql.= "	linkedImage.caption AS linkedImageCaption, ";
	$sql.= "	linkedImage.width AS linkedImageWidth, ";
	$sql.= "	linkedImage.height AS linkedImageHeight, ";
	$sql.= "	linkedImage.version AS linkedImageVersion  ";
	$sql.= "FROM photos ";
	$sql.= "INNER JOIN folders AS tbl_1 ON photos.folderID = tbl_1.folderID ";
	$sql.= "LEFT JOIN folders AS tbl_2 ON tbl_1.grandparentFolderID = tbl_2.folderID ";
	$sql.= "LEFT JOIN photos AS linkedImage ON photos.linkedImg = linkedImage.photoName ";
	$sql.= "WHERE photos.photoName IN ('" . implode($photoNames, "','") . "')";
		
	$rs_photoInfo = $mysqli->query($sql);
	if($rs_photoInfo) { //Check that there is a result set
		$numRows = $rs_photoInfo->num_rows;
		$allSQL .= "rs_photoInfo (" . $numRows . " records returned)<br />" . $sql . "<br /><br />";

		if($numRows > 0) { //Check that the result set contains more than zero rows.
			$photoInfoFound = true;
			
			while ($row = $rs_photoInfo->fetch_array(MYSQLI_ASSOC)) {
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
		echo "No rs_photoInfo result set.<br />" . $sql . "<br />";
		return false;
	}
}
?>
