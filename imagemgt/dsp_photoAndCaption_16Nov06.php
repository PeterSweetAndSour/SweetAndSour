<?php /*
dsp_displayPhotoAndCaption.php

Will put caption to the right of the photo if there is 200px space available unless forceCaptionBelow is true.

=>| photoName
=>| forceCapBelow  optional - "true"/"false"
=>| panorama       optional - "true"/"false"
=>| panoWidth      available if panorama is true
=>| panoHeight     available if panorama is true
*/

if(! isset($_GET["forceCapBelow"]))
	$forceCapBelow = "false";
else
	$forceCapBelow = $_GET["forceCapBelow"];

//Find the folder and caption for this photo (won't be using linkedImg which will also be returned but should be empty).
$photoInfoFound = false;
$photoName = $_GET["photoName"];
include '../imagemgt/qry_photoInfo.php';

if($photoInfoFound == true) {
	//Get the size ($height and $width) of the photo and the $url where it can be found.
	include '../imagemgt/act_getURL.php';
}

//If $panorama is true, get applet display size
if(isset($_GET["panorama"]))
	$panorama = $_GET["panorama"];
else
	$panorama = "false";
	
if($panorama == "true") {
	if(isset($_GET["panoWidth"]))
		$panoWidth = $_GET["panoWidth"];
	else
		$panoWidth = 320;
		
	if(isset($_GET["panoWidth"]))
		$panoHeight = $_GET["panoHeight"];
	else
		$panoHeight = 200;
	
	//Also add expanatory text to caption
   $caption = "<p>To control the panorama:</p><ul><li>drag mouse to move up/down, left/right</li><li>shift & mouse drag to zoom in</li><li>ctrl & mouse drag to zoom out</li><li>+ to zoom in by fixed increment</li><li>- to zoom out by fixed increment</li></ul>" . $caption;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
	<title>Lan and Peter's web site</title>
	<link rel="shortcut icon" href="<?=$rootRelativeUrl ?>imagemgt/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen,print"  href="<?=$rootRelativeUrl ?>stylesStatic.css" />
   	<script type="text/javascript">
      	//Make JS variable out of URL variable
			var forceBelow = "<?= $forceCapBelow ?>";
      	
      	//Get browser type
      	var isIE = false;
      	var version;
      	var userAgent = navigator.userAgent.toLowerCase();
			var indexIE = userAgent.indexOf("msie");
			if(indexIE > -1) {
				isIE = true;
   			userAgent = userAgent.substr(indexIE, userAgent.length);
   			indexSemiColon = userAgent.indexOf(";");
   			version = parseFloat(userAgent.substr(0, indexSemiColon).replace("msie", ""));
			}
			else {
				version = parseFloat(navigator.appVersion);
			}
      	//alert("appName: " + navigator.appName + "\nuserAgent: " + navigator.userAgent + "\nversion: " + navigator.appVersion + "\n\nisIE: " + isIE + "\nversion: " + version)
 
    	
      	//Get page width
      	var currPageWidth;
      	if(isIE) {
      		if(version < 6)
      			currPageWidth = document.body.clientWidth;
      		else //IE6+
      			currPageWidth = document.documentElement.clientWidth;
      	}
      	else { //Real browsers: Firefox, Safari etc.
      		currPageWidth = window.innerWidth;
      	}
      	
      	//See how much space is beside picture
      	var availableSpace = currPageWidth - <?= $width ?>;
      	var captionWidth;
      	var captionPosition;
      	var divString;
			console.log("currPageWidth: " + currPageWidth);
      	console.log("availableSpace: " + availableSpace);
      	if(forceBelow == "true" || availableSpace < 200) {
      		captionPosition = "Below";
      		captionWidth = <?= $width ?> + 15;
      		if(captionWidth < 600)
      			captionWidth = 600;
      	}
      	else {
      		captionPosition = "Right";
      		captionWidth = availableSpace - 100;
      		if(captionWidth > 600)
      			captionWidth = 600;
   		}
   		
   		
   		//Set string that will used with document.write to set caption.
      	var divString = "<div class='caption captionFull caption" + captionPosition + "' style='width:" + captionWidth + "px'>";
      	//alert(divString)
   	</script>
</head>

<body>
<?php 
if($photoInfoFound) {
		?>
		<div class="caption captionFull">
  		<div class="photoHolder">
     		<div class="imgShadow">
     			<?if($panorama == "false") {?>
     				<img src="<?= $url ?>" width="<?= $width ?>" height="<?= $height ?>" />
     			<?} else {?>
      			<div class="left">
               	<applet archive="ptviewer.jar" code="ptviewer.class" width="<?= $panoWidth ?>" height="<?= $panoHeight ?>">
               		<param name="file" value="<?= $url ?>">
               		<param name="auto" value="0.125">
               	</applet>
               </div>
     			<?}?>
     		</div>		
  		</div>
  		
  		<script type="text/javascript">document.write(divString);document.close()</script>
  			<?= $caption ?>
		</div>
		<?php
} else {
	?>
	<p>Oops. Photo not found.
	<?php
}
?>
</body>
</html>
