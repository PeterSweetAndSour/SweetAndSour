<? /*
dsp_menuID.php

Must be called from a subdirectory or the paths for the included files are wrong.

Variables:
=>| $arr_menuData  	from qry_menu.php
=>| $selectedID 		from qry_getMenuID.php
=>| $parentID 			from qry_getMenuID.php
=>| $grandparentID 	from qry_getMenuID.php
=>| $fuseAction
|=> $str_menuHTML
|=> $str_menuJS
|=> $str_menu1Txt
|=> $str_menu2Txt
|=> $str_menu3Txt
*/
$time_start = microtime(true);

$int_lastMenuLevel = 0;
$int_indexLastLevel2 = 0;
$int_topLevelCounter = 0;

$arr_menuHTML = array();
$str_menuJS = "";

$int_len = count($arr_menuData);

$arr_menuHTML[] = "<div id=\"imageMenu\">";

for($i=0; $i < $int_len; $i++) {
	//Get any class attributes for the current list item
	$li_class = array();
	
	//Set class to selected if matches menuID or ancestors
	if($arr_menuData[$i]["menu_id"] == $selectedID || 
		$arr_menuData[$i]["menu_id"] == $parentID || 
		$arr_menuData[$i]["menu_id"] == $grandparentID) {
		$li_class[] =  "selected";
		
		// Hold menu text for use as heading(s) if not set by index.php or display page
		if($arr_menuData[$i]["menu_level"] == 1) {
			$str_menu1Txt = $arr_menuData[$i]["display_text"];
		}
		else if($arr_menuData[$i]["menu_level"] == 2) {
			$str_menu2Txt = $arr_menuData[$i]["display_text"];
		}
		else {
			$str_menu3Txt = $arr_menuData[$i]["display_text"];
		}
		
		// If secondary menu, need to prevent display of pipe in preceeding list item
		if($arr_menuData[$i]["menu_level"] == 2 && $int_indexLastLevel2 != 0) {
			array_splice($arr_menuHTML, $int_indexLastLevel2, 0, " class=\"beforeSelected\"");
		}
	}
	
	// For level 1 list items
	if($arr_menuData[$i]["menu_level"] == 1) {
		// Use the fuseAction as class for image menu control 
		$li_class[] = str_replace(" ", "", strtolower($arr_menuData[$i]["display_text"]));
		
		// Keep track of the index of the currently selected item so we know which panel to expand
		if($li_class[0] == "selected") {
			$open_panel_index = $int_topLevelCounter;
		}
		$int_topLevelCounter++;
	}
	
	
	if($arr_menuData[$i]["menu_level"] > $int_lastMenuLevel) { // starting a nested list
		if($arr_menuData[$i]["menu_level"] == 2) {
			$arr_menuHTML[] = "<ul class=\"menu2\">";
		}
		else {
			$arr_menuHTML[] = "<ul>";
		}
	}
	elseif($int_lastMenuLevel - $arr_menuData[$i]["menu_level"] == 1 ) { // ending a nested list
		$arr_menuHTML[] = "</li></ul></li>";
	}
	elseif($int_lastMenuLevel - $arr_menuData[$i]["menu_level"] == 2 ) { // ending a nested nested list
		$arr_menuHTML[] = "</li></ul></li></ul></li>";
	}
	else { // complete the previous list item at this level (there may have been intermediate list items at the next level for a submenu
		$arr_menuHTML[] = "</li>";
	}

	$arr_menuHTML[] .= "<li";
	// Apply class(es)
	if(count($li_class)) {
		$arr_menuHTML[] = " class=\"" . implode(" ", $li_class) . "\"";
	}
	// If secondary menu, note array position as we may need to add class=beforeSelected later
	if($arr_menuData[$i]["menu_level"] == 2) {
		$int_indexLastLevel2 = count($arr_menuHTML);
	}
	$arr_menuHTML[] = ">";
	
	// If top-level menu, apply a class to the anchor
	$a_class = "";
	if($arr_menuData[$i]["menu_level"] == 1) {
		$a_class = " class=\"photoLink\"";
	}

	$arr_menuHTML[] = "<a" . $a_class . " href=\"../" . $arr_menuData[$i]["folder_name"] . "/index.php?fuseAction=" . $arr_menuData[$i]["fuse_action"] . "\" title=\"" . $arr_menuData[$i]["display_text"] . "\">";

	// If top-level menu, insert an empty span for image replacement
	if($arr_menuData[$i]["menu_level"] == 1) {
		$arr_menuHTML[] = "<span></span>";
	}
	
	$arr_menuHTML[] = $arr_menuData[$i]["display_text"];
	$arr_menuHTML[] = "</a>";

	
	$int_lastMenuLevel = $arr_menuData[$i]["menu_level"];
}

$arr_menuHTML[] = "</li></ul>\n";
$arr_menuHTML[] = "</div>\n";

$str_menuHTML = join("", $arr_menuHTML);

$time_end = microtime(true);
$time_diff_sec = ($time_end - $time_start);
//echo "Time to print menu was " . round($time_end - $time_start, 4) . "<br />";


$str_menuJS = <<<jsStr

<script type="text/javascript">
	
	window.addEvent('domready', function(){
		// Get the top level (photo) navigation list items - NOT ANCHORS
		var topLevelListItems = $("imageMenu").getFirst("ul").getChildren(); 
		
		// Create the menu and set the selected top-level menu item
		var myMenu = new ImageMenu(
			topLevelListItems,
			{                    // options object
				onOpen:function(linkUrl, panelIndex){myMenu.reset(panelIndex)},
				openWidth:500,    // must match stylesheet
				closedWidth:55,   // must match stylesheet
				border:2,
				open: $open_panel_index, // I thought this may not be necessary since stylesheet now specifies widths for selected and closed but need to set widths array
				stopEvent:false  // don't stop event in ImageMenu as a submenu will need it on the containing list item
			}
		);
		
		// Set the submenu
		IMSubNav.prepare()
		
	});
	
</script>
jsStr;

?>
