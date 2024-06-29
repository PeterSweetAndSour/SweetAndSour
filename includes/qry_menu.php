<?php /*
qry_menu.php
This page creates a 2-D array of all menu items in display order.
Algorithm from http://www.sitepoint.com/article/hierarchical-data-database/2

Inner arrays have format.
	[0] displayText
	[1] menuID
	[2] folderID
	[3] fuseAction
	[4] menuLevel

	
Variables:
=>| fuseAction
|=> $arr_menuData
*/

// retrieve the left and right value of the $root node
$sql_root_node = <<<SQL1

	SELECT *
	FROM menus2 
	WHERE menuID = 0;

SQL1;

$rs_root_node = $mysqli->query($sql_root_node);
$arr_root_node = $rs_root_node->fetch_array(MYSQLI_ASSOC);

// now, retrieve all descendants of the $root node 
$sql_menu = <<<SQL2

	SELECT rgt, menuID, menuLevel, displayText, fuseAction, enabled, folderName
	FROM menus2, folders
	WHERE 
		(lft BETWEEN $arr_root_node[lft] AND $arr_root_node[rgt]) 
		AND menus2.folderID = folders.folderID 
		AND enabled = 1
		AND (fuseaction = '$fuseAction' OR displayOnPgOnly = 0)
	ORDER BY lft ASC;

SQL2;

//$time_start = microtime(true);
$rs_descendants =$mysqli->query($sql_menu);
//$time_end = microtime(true);
//$time_diff_sec = round($time_end - $time_start, 4);

if($rs_descendants) {
	$allSQL .= "rs_descendants: " . $rs_descendants->num_rows . " records ";
	//$allSQL .= "returned in " . $time_diff_sec . " sec. ";
	$allSQL .= "<br />" . $sql_menu . "<br /><br />";
}

// dump the output in an array
$arr_menuData = array();

//$time_start = microtime(true);
while ($row = $rs_descendants->fetch_array(MYSQLI_ASSOC)) {
	$arr_menuData[] = array("display_text" => $row['displayText'], "menu_id" => $row["menuID"], "folder_name" => $row['folderName'], "fuse_action" => $row['fuseAction'], "menu_level" => $row["menuLevel"]);
}
//$time_end = microtime(true);
//$time_diff_sec = ($time_end - $time_start);
//echo "Time to load db results into array was " . round($time_end - $time_start, 4) . "<br />";
?>

