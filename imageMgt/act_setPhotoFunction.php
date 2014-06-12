<? 
/******************
* NO LONGER USED! *
******************/

/*
act_setPhotoFunction.php

Puts a photo and caption on the page using absolute positioning.  By default, the photo 
is placed at 20 pixels right and 20 pixels down. Also by default, if the photo is narrower
than a treshold value set below, the caption will be placed to the right of the
photo; if it is wider, the caption will be placed below the photo.

However, if the variable "forceCapBelow" is "true", the caption is placed below the photo 
regardless of width.  This is appropriate if a smaller photo is to be followed be a lot 
of text. Without this option, all the text would be forced into a relatively narrow (180px) 
wide column to the right of the photo.

Main use is to be called by dsp_photoAndCaption.php which will place a single photo on a 
blank page.  This is convenient since the database holds the link from thumbnails to the 
full-size version and the act_setThumbnailFunction.php generates the link automatically 
to dsp_photoAndCaption.php.

The function can also be called directly from other pages if more than one photo needs to 
be placed on a page.

Variables:
=>| $photoName
=>| imageX
=>| imageY
=>| forceCapBelow (optional - "true"/"false", defaults to "false")
*/

function setPhoto($photoName, $imageX, $imageY, $forceCapBelow="false") {

	//First set threshold where caption is placed below or to the right of the photo.
	$threshold = 600;

	//Find the folder and caption for this photo (won't be using linkedImg which will also be returned but should be empty).
	$photoInfoFound = false;
	include '../imageMgt/qry_photoInfo.php';

	if($photoInfoFound == true) {
		//Get the size (height and width) of the photo and the url where it can be found.
		include '../imageMgt/act_getURL.php';

	//Calculate sizes and positions
	$divWidth = $width + 20;
	$divHeight = $height + 20;
	$imageX  = $x + 5;
	$imageY  = $y + 5;
	$captionX = $x;
	$captionY = $y + $divHeight + 16;

		//Determine position and width of box containing the caption
		if($forceCapBelow == "true") {
			$captionPosition = "below";  //Caption below photo
		}
		else {
			if($width > $threshold)
				$captionPosition = "below";  //Caption below photo
			else
				$captionPosition = "right";  //Caption to the right of the photo
		}

		if($captionPosition == "below") {  //Caption below photo
			$captionX     = $borderX;
			$captionY     = $borderY + $rectangleHeight + 16;
			if($forceCapBelow == "true")
				$captionWidth = 600;
			else
				$captionWidth = $width;
		}
		else {  //Caption to the right of the photo
			$captionX     = $borderX + $rectangleWidth + 16;
			$captionY     = $borderY;
			$captionWidth = 180;
		}

		//Image
		if($photoInfoFound == true) {
			echo "<div style='position:absolute; left:" . $imageX . "px; top:" . $imageY . "px; width:" . $divWidth . "px; height:" . $divHeight . "px'>";
			echo "<div class='img-shadow'>";
			echo "   <img src='" . $url . "' width=" . $width . " height=" . $height . " border=0>";
			echo "</div>";
			echo "</div>";
		}
		else {
			$caption = $photoName . " could not be found.";
		}
		
		//Caption
		echo "<div style='position:absolute; left:" . $captionX . "px; top:" . $captionY . "px; width:" . $captionWidth . "'>";
		echo "   <table border=0 cellpadding=0 cellspacing=0 width=" . $captionWidth . ">";
		echo "      <tr><td>" . $caption . "</td></tr>";
		echo "   </table>";
		echo "</div>";
	}
	else {
		echo "Sorry, " . $photoName . " could not be found.";
	}
}
?>
