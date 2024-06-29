<?php /* 
act_deletePhoto.php

Deletes a photo from the database, not the photo itself.

Variables:
=>| photoName
=>| folderId
*/
$photoName = $_GET["photoName"];
$folderID = $_GET["folderID"];

echo "folderId: " + $folderId;

$sql = "DELETE FROM photos WHERE photoName = '" . $photoName . "'";
//$deleteSuccess = @mysql_query($sql);  //returns true/false

//Return to the ShowPhotos page.
//header("Location: http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/" . "index.php?fuseAction=showPhotos&folderID=" . $folderID);
?>