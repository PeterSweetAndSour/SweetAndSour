 <?php
 // 2022-12-10: It looks like I had captured the HTML from Slack in linesToChange.txt and was going to 
 // intending to present whereIsPeter as an HTML file but for whatever reason, I ended up with a PNG image.

$pattern1 = '/role="listitem" style="top: [0-9]{4}\.[0-9]{2}px;"/';
$fileHandle = @fopen("linesToChange.txt", "r"); // Open file form read.

if ($fileHandle) {
	while (!feof($fileHandle)) // Loop till end of file.
	{
		$subject = fgets($fileHandle, 4096); // Read a line.
		preg_match($pattern1, $subject, $matches1);
		if ($matches1[0]) // For example: role="listitem" style="top: 4020.09px;"
		{
			//echo ($matches1[0] . "<br>");
			$pattern2 = '/[0-9]{4}\./';
			preg_match($pattern2, $matches1[0], $matches2);
			if($matches2[0]) {
				$topPx = intval($matches2[0]);
				$newTopPx = 6948 + $topPx;
				//echo ($matches2[0] . ", topPx: " . $topPx . ",newTopPx: " . $newTopPx . "<br>");
				$replacement = 'role="listitem" style="top: ' . $newTopPx . 'px;"'; // Discard fraction of pixel
				echo preg_replace($pattern1, $replacement, $subject);
			}
		}
	}
	
	fclose($fileHandle); // Close the file.
}
?>


