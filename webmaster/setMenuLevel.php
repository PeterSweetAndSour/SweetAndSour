<?
// You are probably looking for updatemenuTble.php. 
// Ran this on each of the top level menu items to set the new menuLevel field in the db. 
// (It timed out attempting to do everything.)

$connectionOK = false;
include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

if($dbconnection) {
	//Confirm connection to particular database is possible
	if(! @mysql_select_db($dbname) ) {
		echo "<p>Unable to connect to the " . $dbname . " database!";
		exit();
	}
}
else {
	echo "<p>Unable to connect to the " . $dbserver . " server!";
	exit();
} 

// retrieve the left and right value of the $root node
$sql_root_node = <<<SQL1

	SELECT *
	FROM menus2 
	WHERE menuID = 3;

SQL1;

$rs_root_node = mysql_query($sql_root_node);
$a_root_node = mysql_fetch_array($rs_root_node);

// dump the output in an array
$menu_data = array();


// now, retrieve all descendants of the $root node 
$sql_menu = <<<SQL2

	SELECT rgt, menuID, displayText, fuseAction, enabled
	FROM menus2, folders
	WHERE (lft BETWEEN $a_root_node[lft] AND $a_root_node[rgt]) AND menus2.folderID = folders.folderID AND enabled = 1
	ORDER BY lft ASC;

SQL2;

$time_1 = microtime(true);
$rs_descendants = mysql_query($sql_menu);
$time_2 = microtime(true);
$time_diff_sec = round($time_2 - $time_1, 4);
$allSQL = "";

if($rs_descendants) {
	$allSQL .= "rs_descendants: " . mysql_num_rows($rs_descendants) . " records returned in " . $time_diff_sec . " sec. <br />" . $sql_menu . "<br /><br />";
	echo $allSQL . "<br />";
}

// start with an empty $right stack
$right = array();

$output = "";

while ($row = mysql_fetch_array($rs_descendants)) {
	
	// only check stack if there is one
	if (count($right) > 0) {
		// check if we should remove a node from the stack
		while ($right[count($right)-1] < $row['rgt']) {
			array_pop($right);
		}
	}

	$command2 = "UPDATE menus2 SET menuLevel=" . (count($right)+1) . " WHERE menuID=" . $row['menuID'];
	echo $command2 . "<br />";
   mysql_query($command2);

	$output .= count($right) . ". " . $row["displayText"] . "<br />";
	
	// add this node to the stack
	$right[] = $row['rgt'];
}

echo $output;
?>