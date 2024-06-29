<div id="showDebugInfo">
	<?php
	//Include this to see php variables
	function dump_array($array) {
		if(is_array($array)) {
			$size = count($array);
			$string = "";
			if($size) {
				$count = 0;
				//add each element's key and value to the string
				foreach($array as $var => $value) {
					$string .= "&nbsp;&nbsp;&nbsp;$var: $value <br />";
				}
				return $string;
			}
		}
		else {
			//if not an array, just return it
			return false;
		}
	}

	echo "<hr /><p><b>Begin SQL executed</b><br/>";
	echo $allSQL . "<br />&nbsp;</p>";

	echo "<p><b>Begin GET variables</b><br/>";
	echo dump_array($_GET) . "<br />&nbsp;</p>";

	echo "<p><b>Begin POST variables </b><br/>";
	echo dump_array($_POST) . "<br />&nbsp;</p>";

	echo "<p><b>Begin SESSION variables</b><br/>";
	echo dump_array($_SESSION) . "<br />&nbsp;</p>";
	?>
</div>