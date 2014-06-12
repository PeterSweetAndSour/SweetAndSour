<? /*dsp_siteMap.php

Use recursion to print out top level menu items and then all their children and their children
and so on until there are no more.
*/
?>
<link type="text/css" rel="stylesheet" href="../css/xtree.css">

<!-- Start site map. -->
<div class="story">
	<script type="text/javascript" src="../js/xtree.js">/* This file cannot go at the bottom */</script>
	<script type="text/javascript">
      <?
      //Call recursive function to populate tree.
      siteMap(0,0);
      ?>
   </script>
</div>

<?
//Function to display children
function siteMap($parentID, $counter) {

	global $allSQL;

	//Get details of this menu item
	$menuID = $parentID;
	include 'qry_menu_getDetails.php';
		
  $row = mysql_fetch_array( $rs_menuDetails );
	$displayText = $row["displayText"];
	$displayText = str_replace("'", "&rsquo;", $displayText);

	$fuseAction  = $row["fuseAction"];
	$folderName  = $row["folderName"];
	$url = "../" . $folderName . "/index.php?fuseAction=" . $fuseAction;
	
	//Determine if this menu item has children
	include 'qry_menu_countChildren.php';
	$numChildren = mysql_num_rows($rs_numChildren);
	
	//If numChildren is zero, this is end node so want to display regular page icon with link
	if($numChildren == 0) {
		print "folder" . $counter . ".add(new WebFXTreeItem('" . $displayText . "', '" . $url . "'));" . chr(13) . chr(10);
	}
	else { //This items has children and so should be displayed as a folder
		$next = $counter + 1;

		//Display as a folder which means creating a tree object.	
		if($parentID == 0){
			print "var folder1 = new WebFXTree('Root');" . chr(13) . chr(10);
		}
		else {
			print chr(13) . chr(10);
			print "var folder" . $next . " = new WebFXTreeItem('" . $displayText . "', '" . $url . "');" . chr(13) . chr(10);
  			print "folder" . $counter . ".add(folder" . $next . ");" . chr(13) . chr(10);
		}
		
   	//For each child, make a recursive call to this function
		for($i=1; $i <= mysql_num_rows($rs_numChildren); $i++) {
   		$row = mysql_fetch_array( $rs_numChildren );
   		$childID = $row["menuID"];
   		siteMap($childID, $next);
   	}
   	
   
		if($parentID == 0) {
			print chr(13) . chr(10);
			print "document.write(folder1);" . chr(13) . chr(10);
		}
	}
}
?>
