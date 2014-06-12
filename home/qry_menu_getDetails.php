<? /*
qry_menu_getChildren.php
Get the active children of the specified menu item

Variables:
=>| $menuID
*/
		$sql = "SELECT displayText, fuseAction, folderName FROM menus2 INNER JOIN folders ON menus2.folderID = folders.folderID WHERE menuID = " . $menuID;
		$rs_menuDetails = @mysql_query($sql);
		if($rs_menuDetails) {
			$allSQL .= "rs_children (" . mysql_num_rows($rs_menuDetails) . " records returned)<br>" . $sql . "<br><br>";
		}
		else {
			echo "Cannot find details for menu item with ID '" . $menuID . "'";
		}
?>
