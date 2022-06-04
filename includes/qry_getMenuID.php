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
|=> responseCode
|=> responseDesc
*/

$selectedID    = NULL;
$parentID      = NULL;
$grandparentID = NULL;

$sql_menuId = <<<EOT

		SELECT M1.menuID, M1.parentID, M2.parentID AS grandparentID
		FROM menus2 AS M1 
		LEFT OUTER JOIN menus2 AS M2 ON M1.parentID = M2.menuID
		WHERE M1.fuseAction = ?

EOT;

$sql = "SELECT * FROM content WHERE path=?";
$stmt = $mysqli->prepare($sql_menuId);
$stmt -> bind_param('s', $fuseAction);
$stmt -> execute();
$rs_menuId = $stmt->get_result();
$numRows = $rs_menuId -> num_rows;

if(!isset($allSQL)) { $allSQL = ""; }
$allSQL .= "rs_menuId (" .$numRows  . " records returned)<br />" . $sql_menuId . "<br /><br />";

if ($mysqli->connect_errno) {
	$responseCode = 500;
	$responseDesc = "No connection to DB. Error " . $mysqli->connect_errno . ": " . $mysqli->connect_error;
	exit();
}
else if ($mysqli->errno) {
	$responseCode = 500;
	$responseDesc = "Database failure. Error: " . $mysqli->errno . ": " . $mysqli->error;
}
else if($numRows === 0) {
	$responseCode = 404;
	$responseDesc = "menuID not found for fuseAction " . $fuseAction;
}
else if($numRows === 1) { //If one row returned, set selected, parent & grandparent;
	$row = $rs_menuId->fetch_array(MYSQLI_ASSOC);
	$selectedID    = $row["menuID"];
	$parentID      = $row["parentID"];
	$grandparentID = $row["grandparentID"];
	$responseCode = 200;
	$responseDesc = "Success";
}
else if($numRows === 2) { // If 2 rows returned, have to figure out which is child
	$row = $rs_menuId->fetch_array(MYSQLI_ASSOC);
	$selectedID_1    = $row["menuID"];
	$parentID_1      = $row["parentID"];
	$grandparentID_1 = $row["grandparentID"];
	
	$row = $rs_menuId->fetch_array(MYSQLI_ASSOC);
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
	$responseCode = 200;
	$responseDesc = "Success";
}
else if($numRows === 3) {
	for($i=0; $i<3; $i++) {
		$row = $rs_menuId->fetch_array(MYSQLI_ASSOC); // If 3 rows returned, child row has grandparent != 0 and != -1
		if($row["grandparentID"] > 0) {
			$selectedID    = $row["menuID"];
			$parentID      = $row["parentID"];
			$grandparentID = $row["grandparentID"];
			
			break;
		}
	}
	$responseCode = 200;
	$responseDesc = "Success";
}
else {
	$responseCode = 500;
	$responseDesc = "Something weird happened and more than 4 rows were returned.";
}

?>
