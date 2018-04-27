<?
/*
Page to enter/edit photo details.  If photo name is already in the
database, will get sent back here.

Variables:
=>| $formType       new/edit
=>| $folderID       may be defined if formType is "new"; will be defined if formType is "edit"
=>| $photoName      not defined if formType is "new" and photo not yet identified

		=>| $folder         if returning from savephoto.php
		=>| $linkedImg      if returning from savephoto.php
		=>| $caption        if returning from savephoto.php
		=>| $duplicateName  if returning from savephoto.php
*/
include 'fn_getPhotoInfo.php';     //Returns photos (associative array) or false
include 'fn_getPhotoURL.php';
	
//Set local variables
if(isSet($_GET["formType"])) {
	$formType = $_GET["formType"];
}
if(isSet($_GET["folderID"])) {
	$folderID = $_GET["folderID"];
}
if(isSet($_GET["photoName"])) {
	$photoName = $_GET["photoName"];
	
}

	
if(isset($folderID)) {
	//Get the name of the folder and if applicable, the name of the grandparent folder, that the photo is in so path can be established
	include '../includes/qry_thisFolder.php';
	
	//Determine the url to the photo
	if(is_null($grandparentFolderName))
		$path = "../" . $folderName . "/images/";
	else
		$path = "../" . $grandparentFolderName . "/images/" . $folderName . "/";
		
	if(isSet($photoName)) {
		$localImgSrc = $path . $photoName;
		$imageSize = @getimagesize($localImgSrc);
		$width = $imageSize[0];
		$height = $imageSize[1];
		$dateModified = date("Y-m-d", filemtime($localImgSrc));
	}
}

if($formType == "new") {
	if((isset($folderID)) && ($folderID != "0")) { //A folder has been specified
		//Get an array holding the files in directory that are NOT in DB
		include 'act_GetNewFiles.php';
	}
	else { //No folder is specified.
		$folderID  = 0;
		$photoName = "";
	}
	$linkedImg = "";
	$caption   = "";
}
else {
	//Find the information related to this photo
	$photos = getPhotoInfo(array($photoName));

	if($photos) {
		//Get the $url where it can be found.
		$localImgSrc = "../" . $photos[$photoName]["grandparentFolderName"] . "/images/" . $photos[$photoName]["folderName"] . "/" . $photoName;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Add/edit photos</title>
	<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
	<link rel="shortcut icon" href="../imageMgt/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen,print"  href="../css/styles_20111124.min.css" />

	<script type="text/javascript">
		// <![CDATA[
		//User has selected a folder. Redraw page so list of photos in the DIRECTORY can be obtained.
		function getPhotosInDir() {
			formType = document.forms[0].formType.value;
			selCtrlFolderId = document.getElementById("selCtrlFolderId");
			folderID = selCtrlFolderId[selCtrlFolderId.selectedIndex].value;
			newURL = "./index.php?fuseAction=formPhoto&folderID=" + folderID + "&formType=" + formType;
			//alert(newURL);
			window.location.href = newURL;
		}

		<? if($formType == "new") { ?>
		//If new photo and user selects a photo, redraw the page so it can be displayed
		function showPhoto() {
			formType = document.forms[0].formType.value;
			selCtrlFolderId = document.getElementById("selCtrlFolderId");
			folderID = selCtrlFolderId[selCtrlFolderId.selectedIndex].value;
			selCtrlPhotoName = document.getElementById("selCtrlPhotoName");;
			photoName = selCtrlPhotoName[selCtrlPhotoName.selectedIndex].value;
			newURL = "./index.php?fuseAction=formPhoto&folderID=" + folderID + "&photoName=" + escape(photoName) + "&formType=" + formType;
			//alert(newURL);
			window.location.href = newURL;
		}
		<? } ?>
		
		//If new photo and linked image is empty, initialize with modified $photoName when 
		//clicking in this field. Most are ...1.jpg or ...1.gif so change 1 to 2.
		function initializeLink() {
			<? if($formType == "new") { ?>
				selCtrlPhotoName = document.getElementById("selCtrlPhotoName");;
				photoName = selCtrlPhotoName[selCtrlPhotoName.selectedIndex].value;
				
				//Get position of "1."
				indexOne = photoName.indexOf("1.");

				if(indexOne != -1)
					linkedImage = photoName.substring(0, indexOne) + "2." + photoName.substring(indexOne + 2, photoName.length);
				else
					linkedImage = photoName.replace(/Sm/, "Lg");

				document.forms[0].linkedImg.value = linkedImage;
			<? } ?>
			return;
		}
		
		//When user submits form, make sure a photo has been specified. Link URL and caption are optional.
		function validateForm() {
			selCtrlFolderId = document.getElementById("selCtrlFolderId");
			folderID = selCtrlFolderId[selCtrlFolderId.selectedIndex].value;
			selCtrlPhotoName = document.getElementById("selCtrlPhotoName");;
			photoName = selCtrlPhotoName[selCtrlPhotoName.selectedIndex].value;
		
			if(folderID == 0) {
				alert("You must specify a folder.");
				selCtrlFolderId.focus();
				return false;
			}
			if(photoName == 0) {
				alert("You must specify a photograph.");
				selCtrlPhotoName.focus();
				return false;
			}
			return true;
		}
		// ]]>
	</script>

</head>

<body>
	<div class="page">
	<div id="content" class="clearfix">
	<form name="form_photo" action="/sweetAndSour.org/imageMgt/index.php?fuseAction=savePhoto" method="post" onSubmit="return validateForm()">
		<input type="hidden" name="formType" value="<?= $formType ?>" />

		<? if($formType == "new"): ?>
 			<h1 align="center">Add photo</h1>
		<? else: ?>
		   <h1 align="center">Edit photo details</h1>
		<? endif; ?>

		<table width="800" border="0" cellspacing="1" cellpadding="2">
			<tr> 
				<td><p>
					<img src="clear1px.gif" alt="" width="74" height="1" alt="" /><br />
					Folder:</p>
				</td>

				<!-- Select control for folders. -->
				<td>
					<? /*If a new photo and user selects new folder; redraw so files in that directory 
					can be obtained; if editing an existing photo, don't redraw as user is indicating
					photo in in a different folder. */
					if($formType == "new")
						$onChange = "getPhotosInDir()";
					else
						$onChange="";
					?>
					<select id="selCtrlFolderId" name="folderID" style="width:224px" onChange="<?= $onChange ?>">
						<option value="0">*Select folder*</option>
						<? while( $row = mysql_fetch_array( $rs_folders ) ) {
							if($row["folderID"] == $folderID): 
								$folderName  = $row["folderName"]; ?>
								<option selected value="<?= $row["folderID"] ?>"> <?= $row["folderID"] ?>. <?= $row["folderName"] ?></option>
							<? else: ?>
								<option          value="<?= $row["folderID"] ?>"> <?= $row["folderID"] ?>. <?= $row["folderName"] ?></option>
							<? endif; 
						} ?>
					</select>
				</td>

				<!-- Display the selected image, if applicable. -->
				<td rowspan="5" align="center">
					<img src="clear1px.gif" alt="" width="504" height="1" /><br />
					<? 
					if(isset($imageSize)) {
						//Display the photo based on whether the width or height is greater
						if( $width > $height ) {
							if($width/$height > 204/116) { /* Sep 09: I wonder why I chose those number */ ?>
								<img src="<?= $localImgSrc ?>" width="204" border="1" alt="" /> <?
 							}
							else { ?>
								<img src="<?= $localImgSrc ?>" height="116" border="1" alt="" />	<?
							}
						}
						else {  /*height is greater than width so just make 116 pixels tall.*/ ?>
								<img src="<?= $localImgSrc ?>" height="116" border="1" alt="" />	<?
						}
					}
					else if(isset($photoName) && $photoName != "") { //If the photoName has been specified and photo not found, issue a warning; if photo not specified, do nothing. ?>
						<p>Image not found at<br /> <?= $localImgSrc ?>.</p> <?
					} ?>
				</td>
			</tr>
			<tr>
				<td><p>Photo:</p></td>
				<td colspan="2">
					<? if($formType == "new") { ?>
						<!-- List photos in this directory that are not already in the database. -->
						<select id="selCtrlPhotoName" name="photoName" style="width:204px" onChange="showPhoto()">
							<option value="0">*Select a photo*</option>
							<? //Loop through the newFiles array
							for($i=0; $i<count($newFiles); $i++) {
								if($newFiles[$i] == $photoName) { ?>
									<option selected value="<?= $newFiles[$i] ?>"><?= $newFiles[$i] ?></option><?
								}
								else { ?>
									<option          value="<?= $newFiles[$i] ?>"><?= $newFiles[$i] ?></option><?
								}
							} ?>
						</select> <?
					}
					else { ?>
					   <input type="text" name="photoName" value="<?= $photoName ?>" style="width:204px" /> <?
					} ?>
				</td>
			</tr>
			<?
			if(isset($imageSize)) { ?>
				<tr>
					<td><p>Width:</p></td>
					<td colspan="2">
						<p><?= $width ?></p>
						<input type="hidden" name="width" value="<?= $width ?>" />
					</td>
				</tr>
				<tr>
					<td><p>Height:</p></td>
					<td colspan="2">
						<p><?= $height ?></p>
						<input type="hidden" name="height" value="<?= $height ?>" />
					</td>
				</tr>
			<? }
			if(isset($dateModified)) {
				?>
				<tr>
					<td><p>Date modified:</p></td>
					<td colspan="2">
						<p><?= $dateModified ?></p>
						<input type="hidden" name="dateModified" value="<?= str_replace("-", "", $dateModified) ?>" />
					</td>
				</tr>
				
				<tr>
					<td><p>Link image:</p></td>
					<td colspan="2">
						<input type="text" name="linkedImg" value="<? if(isset($photoName) && isset($photos[$photoName]["linkedImg"])) {?><?= $photos[$photoName]["linkedImg"] ?><?}?>" style="width:204px" onFocus="initializeLink()" />
					</td>
				</tr>
				
				<tr> 
					<td valign="top" style="vertical-align:top">
						<p>Caption:<br /><span class="small">(Do not add &lt;p&gt; tags for thumbnails.)</span></p>
					</td>
					<td colspan="2">
						<textarea name="caption" cols="78" rows="10" wrap="soft"><? if(isset($photoName) && isset($photos[$photoName]["caption"])) { ?><?= $photos[$photoName]["caption"] ?><? } ?></textarea>
					</td>
				</tr>
				<? 
			} ?>
			<tr>
				<td colspan="3" align="center">
					<input type="submit" name="save" value="Save" />
				</td>
			</tr>
			<tr>
				<td colspan="3"><p>Return to <a href="index.php?fuseAction=showPhotos&amp;folderID=<?= $photos[$photoName]["folderID"] ?>">Show photos</a></p></td>
			</tr>
		</table>
	</form>
	</div>
	</div>
</body> 
</html>
