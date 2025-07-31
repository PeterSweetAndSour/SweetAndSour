<?php /*
act_setThumbnailFunction.php

This function creates the HTML for a thumbnail picture. The caption is taken from the database
and if the linkedImg field in the result set is not an empty string, the photo is placed between 
anchor tags. It is assumed that the image to link to will be in the same /images subdirectory 
but an override URL can be specified if necessary.

Must be called from a subdirectory or the paths for the included files are wrong.

=>| photoNames    (a string for single photo, or an array if multiple)
=>| overrideURL   optional - can be used to override the URL carried in the DB.
=>| cssClass      optional
*/
include '../imagemgt/fn_getPhotoURL.php';      //Function that returns url
include '../imagemgt/fn_getPhotoInfo.php';     //Function that returns $photos (associative array) or false

function setThumbnail($photoNames, $overrideURL = "", $cssClass = "") {
	global $useVersionedFiles; //from config file
	global $useAmazonS3;       //from config file
	global $rootRelativeUrl;   //from config file
	global $mysqli;
	
	// If a string, change to an array
	if(is_string($photoNames)) {
		$photoNames = array($photoNames);
	}

	$classString = ' class="' . ($cssClass ? $cssClass  . '"' : 'figure--thumbnail"');

	//Find the information related to this photo, or these photos.
	$photos = getPhotoInfo($photoNames);
	
	if(isset($photos)) {
		
		// Since the SQL result set will be in random order, need to extract correct result from the associative array $photos
		foreach($photoNames as $index=>$photoName) {
			if($index != 0) {
				?> --><?php
			}

			$imgSrc = getPhotoUrl($photoName, $photos[$photoName]["folderName"], $photos[$photoName]["grandparentFolderName"], $rootRelativeUrl, $useVersionedFiles, $photos[$photoName]["version"]);

			if($overrideURL == "") {
				if($photos[$photoName]["linkedImg"] == "") { // Unlikely - there should be a linked image for every thumbnail
					?><figure<?= $classString ?>>
						<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $photos[$photoName]["width"] ?>" height="<?= $photos[$photoName]["height"] ?>" alt="Please refer to following caption." loading="lazy" />
						<figcaption class="figure__caption--thumbnail"><?= $photos[$photoName]["caption"] ?></figcaption>
					</figure><?php
				}
				else { // Normal situation with thumbnail linked to larger image
					$fullSizeImgSrc = getPhotoUrl($photos[$photoName]["linkedImg"], $photos[$photoName]["folderName"], $photos[$photoName]["grandparentFolderName"], $rootRelativeUrl, $useVersionedFiles, $photos[$photoName]["linkedImageVersion"]);
					$urlPageWithLinkedImage = $rootRelativeUrl . "imagemgt/index.php?fuseAction=showPhotoAndCaption&photoName=" . $photos[$photoName]["linkedImg"];
					?>
					<figure<?= $classString ?>>
						<a class="figure__link--gallery" href="<?= $urlPageWithLinkedImage ?>" data-linked-image-src="<?= $fullSizeImgSrc ?>" data-size="<?= $photos[$photoName]["linkedImageWidth"] ?>x<?= $photos[$photoName]["linkedImageHeight"] ?>">
							<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $photos[$photoName]["width"] ?>" height="<?= $photos[$photoName]["height"] ?>" alt="Please refer to following caption." loading="lazy" />
						</a>
						<figcaption class="figure__caption--thumbnail"><?= $photos[$photoName]["caption"] ?></figcaption>
						<figcaption class="figure__caption--fullsize"><?= $photos[$photoName]["linkedImageCaption"] ?></figcaption>	
					</figure><?php
				}
			}
			else { // Override set so clicking on image takes you somewhere else
				$numPipes = substr_count($overrideURL, "|");
				if($numPipes == 0) {
					$linkURL = $overrideURL;
					$target = "_blank";
				}
				else {
					$linkParts = explode("|", $overrideURL);
					$linkURL = $linkParts[0];
					$target  = $linkParts[1];
				}
				?><figure<?= $classString ?>>
						<a href="<?= $linkURL ?>" target="<?= $target ?>">
							<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $photos[$photoName]["width"] ?>" height="<?= $photos[$photoName]["height"] ?>" alt="Please refer to following caption." loading="lazy" />
							<?php if($cssClass == "figure--thumbnail-youtube") { ?>
								<div class="youtube-logo"></div>
							<?php } ?>
						</a>
						<figcaption class="figure__caption--thumbnail"><?= $photos[$photoName]["caption"] ?></figcaption>
					</figure><?php
			}

			if($index != count($photoNames)-1) {
				?><!-- no white space!<?php
			}
		}
	}
	else {
		?>
		<p><?= implode($photoNames, ", ") ?> could not be found.</p>
		<?php
	}

}

// Use this if you want a text link to the BIG image. Use the name of the SMALL image so the SQL works.
function setTextLinkToPicture($photoName, $displayText) {
	global $rootRelativeUrl;   //from config file

	//Find the information related to this photo
	$photos = getPhotoInfo(array($photoName));
	
	if(isset($photos)) {
				$linkURL = $rootRelativeUrl . "imagemgt/index.php?fuseAction=showPhotoAndCaption&amp;photoName=" . $photos[$photoName]["linkedImg"] . "&amp;altText=" . urlencode(strip_tags($photos[$photoName]["caption"]));
				
				$openingAnchorTag = '<a href="' . $linkURL . '" onclick="SweetAndSour.getPhoto(\'' . $photos[$photoName]["linkedImg"] . '\', \'' . str_replace("'", "&rsquo;", strip_tags($photos[$photoName]["caption"])) . '\'' . '); return false;' . '"  target="_blank">';

				echo $openingAnchorTag . $displayText . '</a>';
	}
	else {
		echo "$displayText";
	}
}


?>
