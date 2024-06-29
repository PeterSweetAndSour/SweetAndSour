<?php /*
dsp_videoPage.php
This file is called via an Ajax request and will load inside the fake popup window.

=>| $windowTitle
=>| $videoList
=>| $videoToPlay (which one to make the play link out of)
*/

$windowTitle = $_GET["windowTitle"];
$videoArray = split(",", $_GET["videoList"]);
$count = count($videoArray);
$videoToPlay = $_GET["videoToPlay"];

if($videoToPlay != 0) {
	?>
	<p>
	<?php
}

for($i=0; $i < $count; $i++) {

	if($i == $videoToPlay) {
		if($videoToPlay != 0) {
			?>
			</p>
			<?php
		}
		?>
		<object width="425" height="344">
			<param name="movie" value="<?= $videoArray[$i] ?>"></param>
			<param name="allowFullScreen" value="true"></param>
			<embed src="<?= $videoArray[$i] ?>" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed>
		</object>
		<?php
		if($videoToPlay != $count - 1) {
			?>
			<p>
			<?php
		}
	}
	else {
		$ajaxUrl = "<?=$rootRelativeUrl ?>includes/dsp_videoPage.php";
		$ajaxUrl .= "?windowTitle=" . rawurlencode($windowTitle);
		$ajaxUrl .= "&linkImg=" . $linkImg;
		$ajaxUrl .= "&videoList=" . rawurlencode($_GET["videoList"]);
		$ajaxUrl .= "&videoToPlay=" . $i;
		?>
		<a href="javascript:void(0)" onclick="SweetAndSour.ajaxPopup('<?= $ajaxUrl ?>', '<?= rawurlencode($windowTitle) ?>')">Part <?= $i + 1 ?></a>
		<?php
		if($i < $count - 1) {
			?>
			<br />
			<?php
		}
	}
	
	if($videoToPlay != $count - 1) {
	?>
	</p>
	<?php
}

}
?>