<? /*
qry_menu_countChildren.php
Count the number of active children of the specified menu item.

Variables:
=>| parentID
*/
		
		$sql = "SELECT menuID FROM menus2 WHERE parentID = " . $parentID . " AND menus2.enabled = 1";
		$rs_numChildren = @mysql_query($sql);
		if($rs_numChildren) {
			$allSQL .= "rs_numChildren (" . mysql_num_rows($rs_numChildren) . " records returned)<br>" . $sql . "<br><br>";
		}
		else {
			echo "Cannot find children for menuID '" . $menuID . "'";
		}
?>
