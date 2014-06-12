<html>
<head>
	<script type="text/javascript" src="featherajax.js"></script>
	<script type="text/javascript">
		function doThis() {
			var ajaxObj = new AjaxObject(); 
			ajaxObj.sndReq('get','http://localhost/sweetAndSour/imageMgt/index.php?target=photo&fuseAction=showPhotoAndCaptionAjax&photoName=montreal001SideStreetLg.jpg');
		}
	</script>
	<link rel="stylesheet" type="text/css" media="screen,print"  href="../stylesStatic.css" />
</head>
<body>
	<div id="photo" class="caption ajaxBox"></div>
	<input type="button" value="Click Me" onClick="doThis()" />
</body>
</html>
