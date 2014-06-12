<? /* Change the file names to include "Sm" or "Lg" (or whatever) before the file extension. */


$directory = "D:\Inetpub\wwwroot\sweetAndSour\photos\images\Maine2009\folder700";

$photoFiles = scandir($directory);
foreach ($photoFiles as $filename) {
	if($filename != "." && $filename != "..") {
		$indexDot = stripos($filename, ".");
		$beforeDot = substr($filename, 0, $indexDot);
		$fileExtension = substr($filename, $indexDot);
		echo $filename . "<br />";
		echo $beforeDot  . "<br />";
		echo $fileExtension . "<br />";
		
		$oldPathAndFile = $directory . "\\" . $filename;
		
		$newPathAndFile = $directory . "\\" . $beforeDot . "Lg" . $fileExtension;

		echo $oldPathAndFile . "<br />";
		echo $newPathAndFile . "<br />";

		echo rename($oldPathAndFile, $newPathAndFile) ? "success" : "fail";
		echo "<br /><br />";
	}
}
?>