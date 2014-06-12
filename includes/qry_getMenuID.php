<? /*
qry_getMenuID.php
Get the menuID of the menu item with this fuseAction.

Will often get 2 or 3 results returned where there is a parent-child relationship
Want to get the child. For instance, both the "Home" and "Welcome" menu items 
have the same fuse action ("welcome") and since "Welcome" is under "Home", the 
"Home" menu needs to be expanded and "Welcome" highlighted. I did consider 
extending the idea so menus could be infinitely deep but this becomes difficult 
to navigate and the extra code was going to be unnecessarily large.

Variables:
=>| fuseAction
|=> selectedID
|=> parentID
|=> grandparentID
*/
$sql_menuId = <<<EOT

		SELECT M1.menuID, M1.parentID, M2.parentID AS grandparentID
		FROM menus2 AS M1 
		LEFT OUTER JOIN menus2 AS M2 ON M1.parentID = M2.menuID
		WHERE M1.fuseAction = '$fuseAction'

EOT;

$rs_menuId = @mysql_query($sql_menuId);
if($rs_menuId) {
	$allSQL .= "rs_menuId (" . mysql_num_rows($rs_menuId) . " records returned)<br />" . $sql_menuId . "<br /><br />";
	
	if(mysql_num_rows($rs_menuId) == 1) { //If one row returned, set selected, parent & grandparent;
		$row = mysql_fetch_array( $rs_menuId );
		$selectedID    = $row["menuID"];
		$parentID      = $row["parentID"];
		$grandparentID = $row["grandparentID"];
	}
	else if(mysql_num_rows($rs_menuId) == 2){ // If 2 rows returned, have to figure out which is child
		$row = mysql_fetch_array( $rs_menuId );
		$selectedID_1    = $row["menuID"];
		$parentID_1      = $row["parentID"];
		$grandparentID_1 = $row["grandparentID"];
		
		$row = mysql_fetch_array( $rs_menuId );
		$selectedID_2    = $row["menuID"];
		$parentID_2      = $row["parentID"];
		$grandparentID_2 = $row["grandparentID"];
		
		if($parentID_1 == $selectedID_2) {
			$selectedID    = $selectedID_1;
			$parentID      = $parentID_1;
			$grandparentID = $grandparentID_1;
		}
		else {
			$selectedID    = $selectedID_2;
			$parentID      = $parentID_2;
			$grandparentID = $grandparentID_2;
		}
	}
	else { // 3 rows returned
		for($i=0; $i<3; $i++) {
			$row = mysql_fetch_array( $rs_menuId ); // If 3 rows returned, child row has grandparent != 0 and != -1
			if($row["grandparentID"] > 0) {
				$selectedID    = $row["menuID"];
				$parentID      = $row["parentID"];
				$grandparentID = $row["grandparentID"];
				
				break;
			}
		}
	}
	//echo "<span style='color:yellow'>selectedID: " . $selectedID . "<br />parentID: " . $parentID . "<br />grandparentID: " . $grandparentID . "</span><br />";
}
else {
	echo "Cannot find menu item for fuse action '" . $fuseAction . "'<br />";
}
?>
