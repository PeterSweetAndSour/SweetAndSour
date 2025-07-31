<!doctype html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
		<link rel="shortcut icon" type="images/x-icon" href="http://sweetandsour.org.s3.amazonaws.com/favicon.ico" />
		<title>Image Administrator</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="imageAdmin.js"></script>
		<link rel="stylesheet" href="imageAdmin.css" type="text/css" media="all" />
	</head>
	<body>
		<div class="wrapper">
			<section class="folders">
				<header>
					<h1>Image Admin for Sweet &amp; Sour</h1>
				</header>
				<!-- List folders in the "folders" table. Drop down menu. -->
				<form>
					<select class="folderId">
						<option value="0">*Select folder*
						<?php 
						while( $row = mysql_fetch_array($rs_folders) ) {
							$thisFolderName = $row["folderName"]; 
							$thisFolderId = $row["folderId"];
								?>
								<option <?= isset($folderId) && ($folderId == $thisFolderId) ? 'selected="selected"' : '' ?> value="<?= $thisFolderId ?>">
									<?= $thisFolderId ?>. <?= $thisFolderName ?>
								</option>
								<?php 
						} ?>
					</select>
				</form>
			</section>
			

			<div id="photos"></div>

		</div>
	</body>
</html>