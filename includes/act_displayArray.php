<?php
//Function to display the contents of an array where
//$name is the display name like "Broken records"
//$array1 is the actual name like "bknRcds"
function display_array($name, $array1) {
	echo "Array " . $name . " size: " . count($array1) . "<br />";
	for($i=0; $i<count($array1); $i++) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $name . "[" . $i . "]: " . $array1[$i] . "<br />";
	}
	echo "<br />";
}
?>
