<?
	/*showphotos.php
	This page allows the user to choose a folder within the application based on the structure
	indicated in the "folders" table.  If the image file is in the specified location, it will
	be displayed when selected.  Note that the file listing is from the database, not the files
	actually in the directory.
	
	Variables:
	+++ app_globals.php	 included file
	|=> $folderID  		 a number indicating the folder
	|=> $photoName		 name of the photo (primary key)
	*/
	
	if( isset($_GET["folderID"]) ) {
			$folderID = $_GET["folderID"];
	}
	if( isset($_GET["photoName"]) ) {
			$photoName = $_GET["photoName"];
	}

	//If the user has selected a folder, find image files in database for that folder
	if( isset($folderID) ) {
		$sql = "SELECT photoName FROM photos WHERE folderID = " . $folderID . " ORDER BY photoName";
		$rs_photos = @mysql_query($sql);
		$allSQL .= "rs_photos (" . mysql_num_rows($rs_photos) . " records returned)<br />" . $sql . "<br /><br />";
	}
	
	//If the user has selected a photo, find the information related to that photo
	$photoInfoFound = false;
	if( isset($photoName) ) {
		include 'qry_photoInfo.php';     //Returns photoInfoFound, folderID, linkedImg, caption
		if($photoInfoFound)
			include 'act_getURL.php'; //Returns url, width, height
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Show photos</title>
	<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
	<link rel="shortcut icon" href="../imageMgt/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen,print"  href="../css/stylesNew.css" />
	<script type="text/javascript">
		// <![CDATA[
		//User has selected a folder. Redraw page so list of photos in the DATABASE can be obtained.
		function getPhotosInDB() {
			folderIndex = document.forms[0].folderID.selectedIndex;
			folderID = document.forms[0].folderID[folderIndex].value;
			newURL = "<?= $PHP_SELF ?>?fuseAction=showPhotos&folderID=" + folderID;
			//alert(newURL);
			window.location.href = newURL;
		}
		
		//A photo has been selected.  Get details of the photo from the database.
		function getPhotoDetails() {
			folderIndex = document.forms[0].folderID.selectedIndex;
			folderID = document.forms[0].folderID[folderIndex].value;
			photoIndex = document.forms[0].photoName.selectedIndex;
			photoName = document.forms[0].photoName[photoIndex].value;
			newURL = "<?= $PHP_SELF ?>?fuseAction=showPhotos&folderID=" + folderID + "&photoName=" + escape(photoName);
			//alert(newURL);
			window.location.href = newURL;
		}
		
		//Add a new photo to the database
		function addPhoto() {
			index = document.forms[0].folderID.selectedIndex;
			if(index != 0) {
				folderID = document.forms[0].folderID[index].value;
				newURL = "index.php?fuseAction=formPhoto&formType=new&folderID=" + folderID;
			}
			else
				newURL = "index.php?fuseAction=formPhoto&formType=new";
				
			window.location.href = newURL;
		}
		
		<? if(isset($photoName)) { ?>
		function editPhoto() {
			window.location.href = "index.php?fuseAction=formPhoto&formType=edit&folderID=<?= $folderID ?>&photoName=<?= $photoName ?>";
		}
		
		function deletePhoto() {
			if(confirm("Are you sure you want to remove this photo from the database? \n\n(The file itself will not be deleted.)"))
				window.location.href = "index.php?fuseAction=deletePhoto&folderID=<?= $folderID ?>&photoName=<?= $photoName ?>";
		}
		<? } ?>
		// ]]>
	</script>
</head>

<body>
	<div class="page">
	<div id="content" class="clearfix">
		<form name="form_products" action="showproducts.cfm" method="post">
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
								<select name="folderID" style="width:204px" onChange="getPhotosInDB()">
									<option value="0">*Select folder*
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
								<p><img src="clear1px.gif" alt="" width="94" height="1"><br />
								<? if($photoInfoFound == true) { ?>Photo:<? } ?>
								</p>
							</td>
							<td valign="top">
								<p><img src="clear1px.gif" alt="" width="198" height="1"><br />
								<? if($photoInfoFound == true) { 
									//Display the photo based on whether the width or height is greater
									if( $width > $height ): ?>
										<img src="<?= $url ?>" width="150" border="1">							  
									<? else: ?>
										<img src="<?= $url ?>" height="150" border="1">							  
									<? endif;
								}; ?></p>
							</td>
							<td rowspan="5" align="left" valign="top">
								<input type="button" name="add"        value="add photo"    onclick="addPhoto()"    style="height:24px; width:94px" /><br />
								<? if( isset($photoName) ): ?>
									<input type="button" name="edit"   value="edit photo"   onclick="editPhoto()"   style="height:24px; width:94px" /><br />
									<input type="button" name="delete" value="delete photo" onclick="deletePhoto()" style="height:24px; width:94px" /><br />
								<? endif; ?>
							</td>
						</tr>
						<? if($photoInfoFound == true): ?>
						<tr>
							<td><p>Width (px):</p></td>
							<td colspan="2"><p><?= $width ?></p></td>
						</tr>
						<tr>
							<td><p>Height (px):</p></td>
							<td colspan="2"><p><?= $height ?></p></td>
						</tr>
						<tr>
							<td><p>Linked image:</p></td>
							<td colspan="2"><p><?= $linkedImg ?></p></td>
						</tr>
						<tr>
							<td valign="top"><p>Caption:</p></td>
							<td colspan="2"><textarea disabled rows="13" wrap="soft" cols="40"><?= $caption ?></textarea></td><!-- If you specify a "style" attribute, the wrapping stops working in Navigator/Mozilla so must specify approximate width with columns. -->
						</tr>
						<? endif; ?>
					</table>
					
				</td>
			</tr>
		</table>
	</form>
	</div>
	</div>
</body>
</html>

