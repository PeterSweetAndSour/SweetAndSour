<?php
$jobTitle = $_GET["jobTitle"];
$jobId = $_GET["jobId"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="shortcut icon" href="https://dmv.dc.gov/sites/default/files/favicon_0.ico" type="image/vnd.microsoft.icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HTML5 Boilerplate</title>
  <style>
		body {padding: 0 24px;}
		header {
			padding: 10px 0;
			font-size: 36px;
			border-bottom: 2px solid black;
		}
		header img {
			height: 60px;
			margin: 20px 40px 0 0;
		}
		header span {
			position: relative;
			top: -10px;
		}
	</style>
</head>

<body>
	<header>
		<img src="https://dc.gov/sites/default/files/dc/dcgovhr.png">
		<span>New ATS (simulated)</span>
	</header>
  <h1><?= $jobTitle ?></h1>
  <h2>Job ID: <?= $jobId ?></h2>
	<p>This page is a placeholder for the vacancy announcement page for the new ATS</p>
	<div id="lipsum">
<p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet condimentum risus. Curabitur vestibulum diam in eros rutrum faucibus. Nunc finibus semper urna ac molestie. Cras semper dapibus enim id congue. Nullam feugiat ligula in neque fringilla pellentesque. Phasellus dignissim justo vitae risus sagittis pharetra. Phasellus blandit diam lectus, eu posuere erat vulputate quis. Nulla facilisi. Integer accumsan, lectus eget posuere condimentum, leo eros commodo ligula, eu laoreet nisi lorem ut quam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus ut felis nec libero ornare blandit ut non ante. Vestibulum at lacus ultricies, ultricies odio at, aliquet erat.
</p>
<p>
Etiam ut diam quis quam tristique sodales eget at lectus. Duis quis tempor turpis. Aenean porttitor scelerisque aliquet. Donec imperdiet justo id metus gravida aliquam. Fusce malesuada nunc enim, at consequat arcu accumsan et. Pellentesque interdum porttitor sapien, dapibus pretium lectus cursus eu. Ut tempus, leo eget tincidunt iaculis, neque nulla finibus nisi, tempus ultrices dui est a lacus. Vestibulum non elementum velit, eu placerat nunc. Integer dignissim commodo elementum. Aliquam tellus est, tincidunt ac interdum id, varius a risus. Quisque sem mauris, aliquet non rutrum ut, pulvinar a dui.
</p>
<p>
In hac habitasse platea dictumst. Praesent ac metus fermentum, vestibulum augue id, dignissim libero. Proin ex odio, ullamcorper sed nisl rutrum, sagittis egestas nisl. Vivamus facilisis dignissim congue. Aliquam erat volutpat. Praesent tincidunt accumsan sollicitudin. Duis ultrices aliquet fringilla.
</p>
<p>
Curabitur imperdiet consequat semper. Donec ut libero varius sem consectetur eleifend. Curabitur pulvinar purus vitae nunc consectetur bibendum. Proin viverra nisi sit amet dignissim varius. Quisque pellentesque posuere posuere. Mauris laoreet ipsum in ex consectetur, quis eleifend lacus posuere. In hac habitasse platea dictumst. Etiam id maximus enim. Suspendisse in rhoncus leo. Donec finibus maximus quam at congue. Vivamus eget sodales enim. Integer blandit velit sed mi ultrices tincidunt. Curabitur rutrum eu diam convallis interdum. Fusce blandit ex at nisi volutpat, vel fringilla elit dignissim. Praesent convallis ultrices varius.
</p>
<p>
Praesent at lobortis eros. Quisque convallis mauris nec luctus tincidunt. Phasellus mattis suscipit felis, eu elementum sapien molestie id. Donec ut purus eget velit luctus fermentum ac ut tellus. Nunc nec faucibus est. Pellentesque vel ultrices metus. Quisque sit amet aliquet augue, eu condimentum massa.
</p>
<p>
Ut ultrices feugiat enim, nec posuere risus pretium luctus. Nunc pellentesque vestibulum elementum. Quisque vitae blandit dolor, consequat gravida lectus. Donec sodales massa metus. Duis eget quam sem. Mauris mi enim, posuere vitae aliquam quis, congue a metus. Nulla vehicula sed ex ac efficitur. Morbi et velit a dui ultrices vestibulum efficitur eu felis. Sed id enim sed ligula ultrices tempor id ac felis.
</p></div>
</body>

</html>
