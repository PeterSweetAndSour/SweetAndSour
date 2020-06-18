<?
	/*
	showphotos.php
	This page allows the user to choose a folder within the application based on the structure
	indicated in the "folders" table.  If the image file is in the specified location, it will
	be displayed when selected.  Note that the file listing is from the database, not the files
	actually in the directory.
	
	Variables:
	+++ app_globals.php	 included file
	|=> $folderId  		 a number indicating the folder
	|=> $photoName		    name of the photo (primary key)
	*/
	include '../imagemgt/fn_getPhotoInfo.php';
	include '../imagemgt/fn_getPhotoURL.php';
	
	
	if( isset($_GET["folderId"]) ) {
		$folderId = $_GET["folderId"];
	}
	else {
		$folderId = -1;
	}
	
	if( isset($_GET["photoName"]) ) {
		$photoName = $_GET["photoName"];
		
		//Find the information related to this photo
		$photos = getPhotoInfo(array($photoName));

		if($photos) {
			//Get the $url where it can be found.
			 echo $_SERVER['PHP_SELF'];
			$localImgSrc = $rootRelativeUrl . $photos[$photoName]["grandparentFolderName"] . "/images/" . $photos[$photoName]["folderName"] . "/" . $photoName;
			$dateModified = date("Y-m-d", filemtime($localImgSrc));
		}
	}
	else {
		$photoName = "";
	}

	//If the user has selected a folder, find image files in database for that folder
	if($folderId != -1) {
		$sql = "SELECT photoName FROM photos WHERE folderId = " . $folderId . " ORDER BY photoName";
		$rs_photos = @mysql_query($sql);
		$allSQL .= "rs_photos (" . mysql_num_rows($rs_photos) . " records returned)<br />" . $sql . "<br /><br />";
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Show photos</title>
	<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
	<link rel="shortcut icon" href="<?=$rootRelativeUrl ?>imagemgt/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen"  href="<?=$rootRelativeUrl ?>css/styles_20111124.min.css" />
	<script type="text/javascript">
		// <![CDATA[
		//User has selected a folder. Redraw page so list of photos in the DATABASE can be obtained.
		function getPhotosInDB() {
			folderIndex = document.forms[0].folderId.selectedIndex;
			folderId = document.forms[0].folderId[folderIndex].value;
			newURL = "<?= $_SERVER['PHP_SELF'] ?>?fuseAction=showPhotos&folderId=" + folderId;
			//alert(newURL);
			window.location.href = newURL;
		}
		
		//A photo has been selected.  Get details of the photo from the database.
		function getPhotoDetails() {
			folderIndex = document.forms[0].folderId.selectedIndex;
			folderId = document.forms[0].folderId[folderIndex].value;
			photoIndex = document.forms[0].photoName.selectedIndex;
			photoName = document.forms[0].photoName[photoIndex].value;
			newURL = "<?= $_SERVER['PHP_SELF'] ?>?fuseAction=showPhotos&folderId=" + folderId + "&photoName=" + escape(photoName);
			//alert(newURL);
			window.location.href = newURL;
		}
		
		//Add a new photo to the database
		function addPhoto() {
			index = document.forms[0].folderId.selectedIndex;
			if(index != 0) {
				folderId = document.forms[0].folderId[index].value;
				newURL = "index.php?fuseAction=formPhoto&formType=new&folderId=" + folderId;
			}
			else
				newURL = "index.php?fuseAction=formPhoto&formType=new";
				
			window.location.href = newURL;
		}
		
		<? if(isset($photoName)) { ?>
		function editPhoto() {
			window.location.href = "index.php?fuseAction=formPhoto&formType=edit&folderId=<?= $folderId ?>&photoName=<?= $photoName ?>";
		}
		
		function deletePhoto() {
			if(confirm("Are you sure you want to remove this photo from the database? \n\n(The file itself will not be deleted.)")) {
				var $newUrl = "index.php?fuseAction=deletePhoto&folderId=<?= $folderId ?>&photoName=<?= $photoName ?>";
				//alert($newUrl)
				window.location.href = newUrl;
			}
		}
		<? } ?>
		// ]]>
	</script>
</head>

<body>
	<div class="page">
	<div id="content" class="clearfix">
		<form name="form_products" action="#" method="post">
		<h1>Show photos in database</h1>
		<table width="620" border="0" cellspacing="1" cellpadding="2">
			<tr> 
				<td width="210"><p><b>Photo:</b></p></td>
				<td width="410"><p><b>Photo details:</p></td>
			</tr>
			<tr>
				<td valign="top"> 

					<table width="194" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<!-- List folders in the "folders" table. Drop down menu. -->
								<select name="folderId" style="width:204px" onChange="getPhotosInDB()">
									<option value="0">*Select folder*
									<? 
									while( $row = mysql_fetch_array( $rs_folders ) ) {
										if($row["folderId"] == $folderId):
											$folderName  = $row["folderName"]; ?>
											<option selected value="<?= $row["folderId"] ?>"> <?= $row["folderId"] ?>. <?= $row["folderName"] ?></option>
										<? else: ?>
											<option          value="<?= $row["folderId"] ?>"> <?= $row["folderId"] ?>. <?= $row["folderName"] ?></option>
										<? endif;
									} ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<!-- List photos in the "photos" table. Select list. -->
								<? if( isset($folderID) ): ?>
									<select name="photoName" style="width:204px" size="24" onChange="getPhotoDetails()">
										<? while( $row = mysql_fetch_array( $rs_photos ) ) {
										if($row["photoName"] == $photoName): ?>
											<option selected value="<?= $row["photoName"] ?>"> <?= $row["photoName"] ?>
										<? else: ?>
											<option          value="<?= $row["photoName"] ?>"> <?= $row["photoName"] ?>
										<? endif; 
									} ?>
									</select>
								<? endif; ?>
							</td>
						</tr>
					</table>

				</td>
				<td width="410" valign="top">
						<table width="414" border="0" cellspacing="0" cellpadding="0">
							<tr style="height:156px">
								<td valign="top">
									<? if( isset($photos) ) { ?>
									<p><img src="clear1px.gif" alt="" width="94" height="1"><br />Photo:</p>
									<? }; ?></p>
								</td>
								<td valign="top">
									<img src="clear1px.gif" alt="" width="198" height="1"><br />
									<? if($photoName != "" && $photos[$photoName]["width"] && $photos[$photoName]["height"]) : 
										//Display the photo based on whether the width or height is greater
										if($photos[$photoName]["width"] > $photos[$photoName]["height"]): ?>
											<img src="<?= $localImgSrc ?>" width="150" border="1">							  
										<? else: ?>
											<img src="<?= $localImgSrc ?>" height="150" border="1">							  
										<? endif; ?>
									<? endif; ?>
								</td>
								<td rowspan="5" align="left" valign="top">
									<input type="button" name="add"       value="add photo"    onclick="addPhoto()"    style="height:24px; width:94px" /><br />
									<? if($photoName != ""): ?>
										<input type="button" name="edit"   value="edit photo"   onclick="editPhoto()"   style="height:24px; width:94px" /><br />
										<input type="button" name="delete" value="delete photo" onclick="deletePhoto()" style="height:24px; width:94px" /><br />
									<? endif; ?>
								</td>
							</tr>
							<?
							if( isset($dateModified) ) {
								?>
								<tr>
									<td><p>Date modified:</p></td>
									<td colspan="2">
										<p><?= $dateModified ?></p>
										<input type="hidden" name="dateModified" value="<?= str_replace("-", "", $dateModified) ?>" />
									</td>
								</tr>
								<?
							}
							if( isset($photos) ) { ?>
								<tr>
									<td><p>Width (px):</p></td>
									<td colspan="2"><p><?= $photos[$photoName]["width"] ?></p></td>
								</tr>
								<tr>
									<td><p>Height (px):</p></td>
									<td colspan="2"><p><?= $photos[$photoName]["height"] ?></p></td>
								</tr>
								<tr>
									<td><p>Linked image:</p></td>
									<td colspan="2"><p><?= $photos[$photoName]["linkedImg"] ?></p></td>
								</tr>
								<tr>
									<td valign="top"><p>Caption:</p></td>
									<td colspan="2"><textarea disabled rows="13" wrap="soft" cols="40"><?= $photos[$photoName]["caption"] ?></textarea></td>
								</tr>
							<? }; ?>
							
						</table>
					
				</td>
			</tr>
		</table>
	</form>
	</div>
	</div>
</body>
</html>

