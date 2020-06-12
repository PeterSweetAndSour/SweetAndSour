<?
// Use this file to update the "lft" and "rgt" values in the menus table after adding/deleting elements;
// these are used in includes/qry_menu.php to build the menu result set.
// Assumes all the menuLevel and parentID values are correctly set.
// Just load http://localhost:8080/sweetandsour/webmaster/updateMenuTbl.php in a browser.

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

function rebuild_tree($mysqli, $parent, $left) {
	// the right value of this node is the left value + 1
	$right = $left+1;

	// get all children of this node
	$command1 = "SELECT menuID, displayText FROM menus2 WHERE parentID=" . $parent . " ORDER BY orderInGroup";
	$result = mysqli_query($mysqli, $command1);
	echo "Rows returned: " . mysqli_num_rows($result) .  " for " . $command1 . "<br />";

	while ($row = mysqli_fetch_array($result)) {
		// recursive execution of this function for each
		// child of this node
		// $right is the current right value, which is
		// incremented by the rebuild_tree function
		if($parent == 7) {
			echo "** " . $row["displayText"] . " **";
		}
		echo "rebuilt_tree(" . $row["menuID"] . ", " . $right . ")" . "<br />";;
		$right = rebuild_tree($mysqli, $row["menuID"], $right);
	}

	// we have got the left value, & now that we have processed
	// the children of this node we also know the right value
	$command2 = "UPDATE menus2 SET lft=" . $left . ", rgt=". $right . " WHERE menuID=" . $parent;
	echo $command2 . "<br />";
	
	if (!mysqli_query($mysqli, $command2)) {
		echo("Error description: " . mysqli_error($mysqli));
	}

	// return the right value of this node + 1
	return $right+1;
}

rebuild_tree($mysqli, 0,1);
	echo "All done";
?>