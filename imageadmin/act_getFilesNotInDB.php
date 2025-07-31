<?php /*
act_getFilesNotInDatabase.php
Get all the files in an image directory but not in the database

Variables:
=>| filesInDB			an array holding the names of files not in the DB
=>| filesInDir  	an array holding the names of files in the folder
|=> filesNotInDb	an array
*/
if(isset($filesInDir) && isset($filesInDB)) {
	//Get a new array that has the files not in the database i.e. in filesInDir but not in filesInDB
	$filesNotInDb = my_array_diff($filesInDir, $filesInDB);
			
	//If not empty, sort the array for presentation. Gives size=1 if empty array is sorted.
	if(count(filesNotInDb) > 0) {
		sort(filesNotInDb);
	}
}
?>