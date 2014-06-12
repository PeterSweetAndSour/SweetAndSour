<? /* Change the file names. By default is US-style MMDDYY but needs to be YYMMDD or YYYYMMDD to sort by date. */


$directory = "C:\Documents and Settings\user\My Documents\My Pictures\_disk10\Palm\ToBeRenamed";

$photoFiles = scandir($directory);
foreach ($photoFiles as $filename) {
	if($filename != "." && $filename != "..") {
		$oldPathAndFile = $directory . "\\" . $filename;
		$dateModified =  date("Y-m-d", filemtime($oldPathAndFile));
		$indexDot = stripos($filename, ".");
		
		$newPathAndFile = $directory . "\\" . $dateModified . "_Palm_" . substr($filename, $indexDot - 3);

		echo $oldPathAndFile . "<br />";
		echo $newPathAndFile . "<br />";

		echo rename($oldPathAndFile, $newPathAndFile) ? "success" : "fail";
		echo "<br /><br />";
	}
}
?>