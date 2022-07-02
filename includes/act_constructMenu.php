<? /*
dsp_menuID.php

Must be called from a subdirectory or the paths for the included files are wrong.

Variables:
=>| $arr_menuData  	from qry_menu.php
=>| $selectedID 	from qry_getMenuID.php
=>| $parentID 		from qry_getMenuID.php
=>| $grandparentID 	from qry_getMenuID.php
=>| $fuseAction
|=> $str_menuHTML
|=> $str_menuJS
|=> $str_menu1Txt
|=> $str_menu2Txt
|=> $str_menu3Txt
*/
//$time_start = microtime(true);

$int_lastMenuLevel = 0;
$str_lastMenuText = "";
$int_indexLastLevel2 = 0;
$int_topLevelCounter = 0;

$isReact = substr($_SERVER['REQUEST_URI'], 0,2) === "r/";

$arr_menuHTML = array();
$str_menuJS = "";
$open_panel_index = 0; 

$int_len = count($arr_menuData);

$arr_menuHTML[] = "<nav id=\"imageMenu\" aria-hidden=\"true\" aria-labelledby=\"menuBtn\" role=\"navigation\">";

for($i=0; $i < $int_len; $i++) {
	//Get any class attributes for the current list item
	$li_class = array();
	
	//Set class to selected if matches menuID or ancestors
	if($arr_menuData[$i]["menu_id"] == $selectedID || 
		$arr_menuData[$i]["menu_id"] == $parentID || 
		$arr_menuData[$i]["menu_id"] == $grandparentID) {
		$li_class[] =  "selected";
		
		// If secondary menu, need to prevent display of pipe in preceding list item
		if($arr_menuData[$i]["menu_level"] == 2 && $int_indexLastLevel2 != 0) {
			array_splice($arr_menuHTML, $int_indexLastLevel2, 0, " class=\"beforeSelected\"");
		}
	}
	
	// For level 1 list items
	if($arr_menuData[$i]["menu_level"] == 1) {
		// Use the display text without spaces as class for image menu control 
		$li_class[] = str_replace(" ", "", strtolower($arr_menuData[$i]["display_text"]));
		
		// Keep track of the index of the currently selected item so we know which panel to expand
		if($li_class[0] == "selected") {
			$open_panel_index = $int_topLevelCounter;
		}
		$int_topLevelCounter++;
	}

	// Look ahead to see if the menu level is changing as we need to add a class on this list item
	if(($i < $int_len -1) and ($arr_menuData[$i+1]["menu_level"] > $arr_menuData[$i]["menu_level"])) {
			$li_class[] = "hasChildren";
	}

	if($arr_menuData[$i]["menu_level"] > $int_lastMenuLevel) { // starting a nested list
		if($arr_menuData[$i]["menu_level"] != 1) {
			$arr_menuHTML[] .= '<input type="radio" name="menuLevel' . $int_lastMenuLevel . '" id="radio' . $arr_menuData[$i]["menu_id"]  . '">';
			$arr_menuHTML[]  = '<label for="radio' . $arr_menuData[$i]["menu_id"] . '">' . $str_lastMenuText . '</label>';
		}
		
		$arr_menuHTML[] .= "<ul class=\"menu" . $arr_menuData[$i]["menu_level"] . "\">";
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
	// If secondary menu, note array position as we may need to add class="beforeSelected" later
	if($arr_menuData[$i]["menu_level"] == 2) {
		$int_indexLastLevel2 = count($arr_menuHTML);
	}
	$arr_menuHTML[] = ">";
	
	// If top-level menu, apply a class to the anchor
	$a_class = "";
	if($arr_menuData[$i]["menu_level"] == 1) {
		$a_class = " class=\"photoLink\"";
	}

	$arr_menuHTML[] = "<a" . $a_class . " href=\"" . $rootRelativeUrl . ($isReact ? "r/" : "") . $arr_menuData[$i]["folder_name"] . "/" . $arr_menuData[$i]["fuse_action"] . "\">";


	// If top-level menu, insert an empty span for image replacement
	if($arr_menuData[$i]["menu_level"] == 1) {
		$arr_menuHTML[] = "<span></span>";
		$arr_menuHTML[] = "<p class=\"verticalText\">" . $arr_menuData[$i]["display_text"] . "</p>";
	}
	else {
			$arr_menuHTML[] = $arr_menuData[$i]["display_text"];
	}
	
	$arr_menuHTML[] = "</a>";

	
	$int_lastMenuLevel = $arr_menuData[$i]["menu_level"];
	$str_lastMenuText = $arr_menuData[$i]["display_text"];
}

$arr_menuHTML[] = "</li></ul>\n";
$arr_menuHTML[] = "</nav>\n";

$str_menuHTML = join("", $arr_menuHTML);

//$time_end = microtime(true);
//$time_diff_sec = ($time_end - $time_start);
//echo "Time to print menu was " . round($time_end - $time_start, 4) . "<br />";
?>
