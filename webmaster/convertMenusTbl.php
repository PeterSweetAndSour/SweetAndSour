<?
//Use this file to update the lft and rgt values in the menus table after adding/deleting elements.
//Assumes all the menuLevel and parentID values are correctly set.

include '../../sweetandsour_conf.php';
include '../includes/act_getDBConnection.php';

function rebuild_tree($parent, $left) {
   // the right value of this node is the left value + 1
   $right = $left+1;

   // get all children of this node
	$command1 = "SELECT menuID FROM menus2 WHERE parentID=" . $parent . " ORDER BY orderInGroup";
   $result = mysql_query($command1);
	echo "Rows returned: " . mysql_num_rows($result) .  " for " . $command1 . "<br />";
	
   while ($row = mysql_fetch_array($result)) {
       // recursive execution of this function for each
       // child of this node
       // $right is the current right value, which is
       // incremented by the rebuild_tree function
		 echo "rebuilt_tree(" . $row["menuID"] . ", " . $right . ")" . "<br />";;
       $right = rebuild_tree($row["menuID"], $right);
   }

   // we have got the left value, & now that we have processed
   // the children of this node we also know the right value
	$command2 = "UPDATE menus2 SET lft=" . $left . ", rgt=". $right . " WHERE menuID=" . $parent;
	echo $command2 . "<br />";
   mysql_query($command2);

   // return the right value of this node + 1
   return $right+1;
}

rebuild_tree(0,1);
echo "All done";
?>