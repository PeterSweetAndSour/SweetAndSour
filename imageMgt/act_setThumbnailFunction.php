<? /*
act_setThumbnailFunction.php

This function creates the HTML for a thumbnail picture and caption that are placed
in a container DIV which is floated in to place. The caption is taken from the database
and if the linkedImg field in the result set is not an empty string, the photo is 
placed between anchor tags. It is assumed that the image to link to will be in the 
same /images subdirectory but an override URL can be specified if necessary.

Can optionally specify that this is to link to a panorama file so that the image will
be displayed in an viewer applet. If $panorama is set to true, must also spacify the
display width and height which will be much smaller than the image size.

Must be called from a subdirectory or the paths for the included files are wrong.

If setting $overrideUrl or $panorama, $photoNames must be a string with a single name.

=>| photoNames    (a string for single photo or an array if multiple)
=>| overrideURL   optional - can be used to override the URL carried in the DB.
=>| panorama      true/false
=>| panoWidth     pixel width to display in applet
=>| panoHeight    pixel height to display in applet
*/
include '../imageMgt/fn_getPhotoURL.php';      //Function that returns url
include '../imageMgt/fn_getPhotoInfo.php';     //Function that returns $photos (associative array) or false

function setThumbnail($photoNames, $overrideURL = "", $panorama = "false") {
	global $useVersionedFiles; //from config file
	global $useAmazonS3;       //from config file
	global $urlPrefix;         //from config file
	
	// If a string, change to an array
	if(is_string($photoNames)) {
		$photoNames = array($photoNames);
	}

	//Find the information related to this photo
	$photos = getPhotoInfo($photoNames);
	
	if(isset($photos)) {
		
		// Since the SQL result set will be in random order, need to extract correct result from the associative array $photos
		foreach($photoNames as $photoName) {
			$str  = '<div class="photoBox">';  // ' . $classToApply . '
			$str .= '<div class="imgShadow">';
			
			
			//If necessary, override the url for the link; By default it will open in a new tab/window but can 
			//explicitly set target with "|_self" etc after the URL and further set an onclick handler after another pipe.
			if($overrideURL != "") {
				$numPipes = substr_count($overrideURL, "|");
				if($numPipes == 0) {
					$linkURL = $overrideURL;
					$target = "_blank";
				}
				else {
					$linkParts = explode("|", $overrideURL);
					$linkURL = $linkParts[0];
					$target  = $linkParts[1];
					if($numPipes == 2) {
						$onclick = $linkParts[2];
					}
				}
			}
			else {
				if($photos[$photoName]["linkedImg"] == "") {
					$linkURL = "";
				}
				else {
					$linkURL = "../imageMgt/index.php?fuseAction=showPhotoAndCaption&amp;photoName=" . $photos[$photoName]["linkedImg"] . "&amp;altText=" . urlencode(strip_tags($photos[$photoName]["caption"]));
				}
			}
		
			if($panorama == "true") {
				$linkURL .= "&amp;panorama=true";
			}	
			
			
			
			if($linkURL != "") {
				$str .= '<a href="' . $linkURL . ($overrideURL == "" ? '" onclick="SweetAndSour.getPhoto(\'' . $photos[$photoName]["linkedImg"] . '\', \'' . str_replace("'", "&rsquo;", strip_tags($photos[$photoName]["caption"])) . '\'' . ($panorama == "true" ? ', true': '') . '); return false;' : '') . '"' . ($overrideURL != "" ? ' target="' . $target . '"' : '') . (isset($onclick) ? ' onclick="' . $onclick . '"' : '') . '>';
			}
			
			$imgSrc = getPhotoUrl($photoName, $photos[$photoName]["folderName"], $photos[$photoName]["grandparentFolderName"], $urlPrefix, $useVersionedFiles, $photos[$photoName]["version"]);
			
			$str .= '<img src="' . $imgSrc . '" width="' . $photos[$photoName]["width"] . '" height="' . $photos[$photoName]["height"] . '" alt="" />';
			if($linkURL != "")
				$str .= '</a>';
			
			$str .= '</div>';
			$str .=  '<div class="caption captionThumb">';
			
			//Caption
			$str .= $photos[$photoName]["caption"];
			//$str .= ($overrideURL == '' ? '' : ' <a href="' . $overrideURL . '" target="_blank" class="external"></a>');
		
			$str .= '</div>';
			$str .= '</div>
			';
			echo $str;
		}
	}
	else {
		$caption = implode($photoNames, ", ") . " could not be found.";
	}
	
	/*
	if ($numargs > 4) {
		$panoWidth = func_get_arg(4);
		$linkURL .= "&amp;panoWidth=" . $panoWidth;
	}
	if ($numargs > 5) {
		$panoHeight = func_get_arg(5);
		$linkURL .= "&amp;panoHeight=" . $panoHeight;
	}
	*/
}

// Use this if you want a text link to the BIG image. Use the name of the SMALL image so the SQL works.
function setTextLinkToPicture($photoName, $displayText) {
	//Find the information related to this photo
	$photos = getPhotoInfo(array($photoName));
	
	if(isset($photos)) {
				$linkURL = "../imageMgt/index.php?fuseAction=showPhotoAndCaption&amp;photoName=" . $photos[$photoName]["linkedImg"] . "&amp;altText=" . urlencode(strip_tags($photos[$photoName]["caption"]));
				
				$openingAnchorTag = '<a href="' . $linkURL . '" onclick="SweetAndSour.getPhoto(\'' . $photos[$photoName]["linkedImg"] . '\', \'' . str_replace("'", "&rsquo;", strip_tags($photos[$photoName]["caption"])) . '\'' . '); return false;' . '"  target="_blank">';

				echo $openingAnchorTag . $displayText . '</a>';
	}
	else {
		echo "$displayText";
	}
}


?>
