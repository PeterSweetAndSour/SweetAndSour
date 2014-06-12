<? /*
act_getPhysicalFilesInFolder.php
Get all the files in an image directory

Variables:
=>| path					physical path to the directory holding the images
|=> filesInFolder		an array holding the names of files not in the DB
*/

$filesInFolder = array();
if(isset($folderPath)) {
	//Get an array of photos in this directory
	$dir = opendir($folderPath);
	if($dir) {
		while(false !== ($filename = readdir($dir))) {
			
			if(($filename != ".") && ($filename != "..")) {
				$pattern = '/(png|gif|jpg)$/i';
				if(preg_match($pattern, $filename) == 1) {
					array_push($filesInFolder, $filename);
				}
			}
		}
		closedir($dir);
	}
	unset($filename);
}
?>
