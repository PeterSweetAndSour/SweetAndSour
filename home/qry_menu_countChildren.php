<? /*
qry_menu_countChildren.php
Count the number of active children of the specified menu item.

Variables:
=>| parentID
*/
		
		$sql = "SELECT menuID FROM menus2 WHERE parentID = " . $parentID . " AND menus2.enabled = 1";
		$rs_numChildren = $mysqli->query($sql);
		if ($rs_numChildren) {
			$allSQL .= "rs_numChildren (" . $rs_numChildren->num_rows . " records returned)<br>" . $sql . "<br><br>";
			$result->close();
		}
		else {
			echo "Cannot find children for menuID '" . $menuID . "'";
		}
?>
