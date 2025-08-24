<?php /*
act_setThumbnailFunction.php

This function creates the HTML for a thumbnail picture. The caption is taken from the database
and if the linkedImg field in the result set is not an empty string, the photo is placed between 
anchor tags. It is assumed that the image to link to will be in the same /images subdirectory 
but an override URL can be specified if necessary.

Must be called from a subdirectory or the paths for the included files are wrong.

=>| photosToPlace which could be:
    * a string for single photo name
			"photo01Sm.jpg"
			which will get converted to
			["photo01Sm.jpg"]
			so that we can use array loop

		* an array for a single photo
			["photoName"=>"photo01Sm.jpg", "overrideURL"=>"https://youtube.com...", "cssClass"=>"figure--thumbnail-large", "logo"=>"instagram"]

		* an array for multiple photos that can contain strings and/or arrays
      ["photo01Sm.jpg", "photo02Sm.jpg", ["photoName"=>"photo03Sm.jpg", "overrideURL"=>"https://youtube.com..."]]

*/
include '../imagemgt/fn_getPhotoURL.php';      //Function that returns url
include '../imagemgt/fn_getPhotoInfo.php';     //Function that returns $photos (associative array) or false

function setThumbnail($photosToPlace) {
	global $useVersionedFiles; //from config file
	global $useAmazonS3;       //from config file
	global $rootRelativeUrl;   //from config file
	global $mysqli;


	// Make a new associative array with $photoName as the key and elements $overrideURL and $cssClass
	// but note that $photosToPlace may already be in this form.
	$allPhotoDetails = [];

	// Make another array of just the names of the photos
	$photoNames = [];

	// Set single photos in an array
	if(is_string($photosToPlace)) {
		$photosToPlace = array($photosToPlace);
	}
	else if(is_array($photosToPlace) and array_key_exists("photoName", $photosToPlace)) { // An associative array with photoName and other keys
		$photosToPlace = [ $photosToPlace ];
	}

	foreach($photosToPlace as $photoToPlace) {
		$photoDetails = [];

		if(is_string($photoToPlace)) { // A string with just a photo name
			array_push($photoNames, $photoToPlace);

			$photoDetails["figureCssClass"] = "figure--thumbnail";
			$photoDetails["overrideURL"] = "";

			$allPhotoDetails[$photoToPlace] = $photoDetails;
		}
		else if(is_array($photoToPlace)) { // An array with $photoName and either/both $overrideURL and $cssClass
			array_push($photoNames, $photoToPlace["photoName"]);

			if(array_key_exists("cssClass", $photoToPlace)) {
				$photoDetails["figureCssClass"] = $photoToPlace["cssClass"];
			}
			else {
				$photoDetails["figureCssClass"] = "figure--thumbnail";
			}

			if(array_key_exists("logo", $photoToPlace)) {
				$photoDetails["logoCssClass"] = $photoToPlace["logo"];
			}

			if(array_key_exists("overrideURL", $photoToPlace)) {
				$photoDetails["overrideURL"] = $photoToPlace["overrideURL"];
			}
			else {
				$photoDetails["overrideURL"] = "";
			}

			$allPhotoDetails[$photoToPlace["photoName"]] = $photoDetails;
		}
	}

	//Find the information related to this photo, or these photos.
	$photoInfoFromDB = getPhotoInfo($photoNames);

	if(isset($photoInfoFromDB)) {
		// Combine the two associative arrays, one element at a time since array_merge and "+" will lose lose data as it will see key clashes
		foreach($photoNames as $photoName) {
			$allPhotoDetails[$photoName] = array_merge($allPhotoDetails[$photoName], $photoInfoFromDB[$photoName]);
		}
		
		// Since the SQL result set will be in random order, need to extract correct result from the associative arrays
		foreach($photoNames as $index=>$photoName) {
			$thisPhoto = $allPhotoDetails[$photoName];

			if($index != 0) {
				?> --><?php
			}

			$imgSrc = getPhotoUrl($photoName, $thisPhoto["folderName"], $thisPhoto["grandparentFolderName"], $rootRelativeUrl, $useVersionedFiles, $thisPhoto["version"]);

			$overrideURL = $thisPhoto["overrideURL"];
			if($overrideURL == NULL) {
				if($thisPhoto["linkedImg"] == "") { // Unlikely - there should be a linked image for every thumbnail
					?><figure class="<?= $thisPhoto["figureCssClass"] ?>">
						<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $thisPhoto["width"] ?>" height="<?= $thisPhoto["height"] ?>" alt="Please refer to following caption." loading="lazy" />
						<figcaption class="figure__caption--thumbnail"><?= $thisPhoto["caption"] ?></figcaption>
					</figure><?php
				}
				else { // Normal situation with thumbnail linked to larger image
					$fullSizeImgSrc = getPhotoUrl($thisPhoto["linkedImg"], $thisPhoto["folderName"], $thisPhoto["grandparentFolderName"], $rootRelativeUrl, $useVersionedFiles, $thisPhoto["linkedImageVersion"]);
					$urlPageWithLinkedImage = $rootRelativeUrl . "imagemgt/index.php?fuseAction=showPhotoAndCaption&photoName=" . $thisPhoto["linkedImg"];
					?>
					<figure class="<?= $thisPhoto["figureCssClass"] ?>">
						<a class="figure__link--gallery" href="<?= $urlPageWithLinkedImage ?>" data-linked-image-src="<?= $fullSizeImgSrc ?>" data-size="<?= $thisPhoto["linkedImageWidth"] ?>x<?= $thisPhoto["linkedImageHeight"] ?>">
							<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $thisPhoto["width"] ?>" height="<?= $thisPhoto["height"] ?>" alt="Please refer to following caption." loading="lazy" />
						</a>
						<figcaption class="figure__caption--thumbnail"><?= $thisPhoto["caption"] ?></figcaption>
						<figcaption class="figure__caption--fullsize"><?= $thisPhoto["linkedImageCaption"] ?></figcaption>	
					</figure><?php
				}
			}
			else { // Override set so clicking on image takes you somewhere else
				$numPipes = substr_count($overrideURL, "|");
				if($numPipes == 0) {
					$linkURL = $allPhotoDetails[$photoName]["overrideURL"];
					$target = "_blank";
				}
				else {
					$linkParts = explode("|", $allPhotoDetails[$photoName]["overrideURL"]);
					$linkURL = $linkParts[0];
					$target  = $linkParts[1];
				}
				?><figure class="<?= $thisPhoto["figureCssClass"] ?>">
						<a href="<?= $linkURL ?>" target="<?= $target ?>">
							<img class="figure__image" src="<?= $imgSrc ?>" width="<?= $thisPhoto["width"] ?>" height="<?= $thisPhoto["height"] ?>" alt="Please refer to following caption." loading="lazy" />
							<?php if(array_key_exists("logoCssClass", $thisPhoto)) { ?>
								<div class="<?= $thisPhoto["logoCssClass"] . "-logo" ?>"></div>
							<?php } ?>
						</a>
						<figcaption class="figure__caption--thumbnail"><?= $thisPhoto["caption"] ?></figcaption>
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
