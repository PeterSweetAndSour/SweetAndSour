<?php
include '../sweetandsour_conf.php';
include 'includes/act_getDBConnection.php';

function display_tree($menuID) {

   // retrieve the left and right value of the $root node
   $result = mysql_query("SELECT * FROM menus2 WHERE menuID=" . $menuID . ";");
   $row = mysql_fetch_array($result);

   // start with an empty $right stack
   $right = array();

   // now, retrieve all descendants of the $root node
   $result = mysql_query("SELECT * FROM menus2 WHERE lft BETWEEN " . $row["lft"] . " AND " . $row["rgt"] . " ORDER BY lft ASC;");

   // display each row
   while ($row = mysql_fetch_array($result)) {
       // only check stack if there is one
       if (count($right) > 0) {
           // check if we should remove a node from the stack
           while ($right[count($right)-1] < $row['rgt']) {
               array_pop($right);
           }
       }

       // display indented node title
       echo str_repeat('&nbsp;', count($right)*2) . $row['displayText'] . ' | ' . count($right) . '<br />';

       // add this node to the stack
       $right[] = $row['rgt'];
   }
}

display_tree(0);
