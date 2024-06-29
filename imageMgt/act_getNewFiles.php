<?php /*
act_getNewFiles.php
Get the files in this image directory that are not already in the database

Variables:
=>| path       physical path to the directory holding the images
|=> newFiles   an array holding the names of files not in the DB
*/
			//Get an array of photos in this directory
			$filesInDir = array();
			$dir = opendir($path);
			while($file = readdir($dir)) {
				if(($file != ".") && ($file != ".."))
					array_push($filesInDir, $file);
			}
			closedir($dir);
			
			//Get an array of photos already in the database for fhis folder
			$filesInDB = array();
			$sql = "SELECT photoName FROM photos WHERE folderID = '" . $folderID . "' ORDER BY photoName";
			$rs_photos = @mysql_query($sql);
			$allSQL .= "rs_photos (" . mysql_num_rows($rs_photos) . " records returned)<br>" . $sql . "<br><br>";
			while( $row = mysql_fetch_array( $rs_photos ) ) {
				$file = $row["photoName"];
				array_push($filesInDB, $file);
			}
			
			//Get a new array that has the files not in the database i.e. in filesInDir but not in filesInDB
			$newFiles = my_array_diff($filesInDir, $filesInDB);

			/* display_array("filesInDir", $filesInDir);
			display_array("filesInDB", $filesInDB);
			display_array("newFiles", $newFiles); */
					
			//If not empty, sort the array for presentation. Gives size=1 if empty array is sorted.
			if(count($newFiles) > 0)
				sort($newFiles);
?>
