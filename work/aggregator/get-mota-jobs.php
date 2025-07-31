<?php
/*
Jobs are on https://mota.dc.gov/jobs in groups
"EXECUTIVE CABINET", "INDEPENDENT AGENCY APPOINTED LEADERSHIP", "POLICY, OPERATIONS, AND SUPPORT POSITIONS"
which are in H3s in article > .content > field > field-items > field-item

Following the H3s are p.rteindent1 > a which contain only the job title. it will be necessary to follow
each link to get further information so a new cUrl connection for each one. 

Note that if "We currently do not have any open postings." then it
is in the same p.rteindent1 but its child element is an em.

The vacancy announcements appear to be reliably structured than when I first looked at this with all four current
listings having:
* a single paragraph at the top with name/value pairs for the office, salary etc separated by break tags that also includes
	- a statement about Excepted Service
	- a statement about DC residency

	The names of the details are not consistent:
	- OFFICE... or AGENCY
	- OPEN DATE... or OPEN
	- CLOSE DATE (may not be present)
	- GRADE... ie EXECUTIVE LEVEL...)
	- SALARY... or SALARY RANGE
	The name of the detail may or may not be in <b> or <strong> tags.

* sections each with heading in H1 with several SPANs inside that may include
	- background (yes, lower-case relying on CSS to capitalze)
	- Major duties
	- COMPETENCIES, KNOWLEDGE, SKILLS, AND ABILITIES
	- MINIMUM QUALIFICATIONS
	- Work environment
	- SPECIAL NOTE - SECURITY SENSITIVE
	- RESIDENCY REQUIREMENT

There is no job ID or location.

Another had:
* INTRODUCTION:
* DUTIES AND RESPONSIBILITIES:
* OTHER SIGNIFICANT FACTS:
* RESIDENCY REQUIREMENT

*/
include_once("../includes/simple_html_dom.php");
include_once("../includes/function-get-html.php");
include_once("../includes/fn_prettyPrintJSON.php");

function index_next_non_whitespace($str, $start_index = 0) {
	// Loop through the string
	for ($i = $start_index; $i < strlen($str); $i++) {
			// Check if the character is not a whitespace
			if (!ctype_space($str[$i])) {
					return $i; // Return the index of the first non-whitespace character
			}
	}
	
	// Return -1 if only whitespace found
	return -1;
}

function index_previous_search_char($str, $start_index, $search_char) {
	for ($i = $start_index; $i > 0; $i--) {
		// Check if the character is end of tag
		if ($str[$i] == $search_char) {
				return $i; // Return the index of the first ">"
		}
	}

	// Return -1 if character not found
	return -1;
}

function index_next_search_char($str, $start_index, $search_char) {
	for ($i = $start_index; $i < strlen($str); $i++) {
		// Check if the character is end of tag
		if ($str[$i] == $search_char) {
				return $i; // Return the index of the first ">"
		}
	}

	// Return -1 if character not found
	return -1;
}

// If a colon is inside a tag, it will be inside an inline style or href attribute
function colon_is_inside_tag($str, $index_colon) {
	if((substr($str, $index_colon - 5, 6) == "https:") or (substr($str, $index_colon - 4, 5) == "http:")) {
		//echo("Ignore - inside href<br>");
		return true;
	}

	$index_previous_equals = index_previous_search_char($str, $index_colon, "=");
	$index_previous_right_angle = index_previous_search_char($str, $index_colon, ">");
	if($index_previous_equals > $index_previous_right_angle and substr($str, $index_previous_equals - 5, 6) == "style=") {
		//echo("Ignore - inside style<br>");
		return true;
	}

	return false;
}

// Function that minimizes memory overflow to insert next name-value pair into array.
// https://stackoverflow.com/questions/31863777/how-do-i-resolve-memory-issues-inside-a-loop
function get_next_name_value_pair($string, $index_start, &$name_value_pairs) {
	$index_colon = strpos($string, ":", $index_start);
	$index_start = $index_colon + 1;

	// Ignore this colon if inside an inline style or href attribute (most likely scenario)
	if(colon_is_inside_tag($string, $index_colon)) {
		return $index_start;
	}

	//Get the text immediately before the colon up to the preceding ">"
	$index_end_previous_tag = index_previous_search_char($string, $index_colon, ">");
	if($index_end_previous_tag == -1) { $index_end_previous_tag = 0; } // There was no previous tag as the string started with text.
	$name = trim(substr($string, $index_end_previous_tag + 1, $index_colon - $index_end_previous_tag - 1));
	$value = "";

	$value_and_index_start_next_tag = get_value_after_colon($index_colon, $string);

	$value = $value_and_index_start_next_tag["value"];

	if(str_contains(strtolower($name), "position") or str_contains(strtolower($name), "title")) {
		$name = "job_title";
	}
	else if(str_contains(strtolower($name), "office") or str_contains(strtolower($name), "agency") or str_contains(strtolower($name), "department")) {
		$name = "office";
	}
	else if(str_contains(strtolower($name), "open") or str_contains(strtolower($name), "posted")) {
		$name = "open_date";
	}
	else if(str_contains(strtolower($name), "close") or str_contains(strtolower($name), "closing")) {
		$name = "close_date";
	}
	else if(str_contains(strtolower($name), "grade")) {
		$name = "grade";
	}
	else if(str_contains(strtolower($name), "salary") or str_contains(strtolower($name), "executive")) {
		$name = "salary";
	}

	array_push($name_value_pairs, [$name, $value]);

	$index_start_next_tag = $value_and_index_start_next_tag["index_start_next_tag"];
	if($index_start_next_tag == null) {
		return null;
	}
	
	$index_start = $index_start_next_tag + 1;
	return $index_start;
}

// Get value from text after a colon which may be AFTER closing bold/em/span tags. 
// For example: 
// <p><span style="font-size:16px;"><span style="font-family:'Times New Roman', Times, serif;"><b>POSITION: &nbsp;</b>Associate Director / Faith Community Liaison<br><b>OFFICE: &nbsp; &nbsp; &nbsp; </b>Mayor's Office of Community Relations and Services (MOCRS)&nbsp;<br>
// or even
// <p><span style="font-size:16px;"><span style="font-family:'Times New Roman', Times, serif;"><b>POSITION: &nbsp;</b></span></span><span style="font-size:12pt;"><span style="line-height:107%;"><span style="font-family:'Times New Roman', serif;">Director of Operations</span></span></span><br>
// Value will be before a break tag, or potentially a closing tag for a paragraph, list item or div.
function get_value_after_colon($index_colon, $string) {
	$check_for = "/(<br>)|(<\/p>)|(<\/li>)|(<\/div>)/";
	preg_match($check_for, $string, $matches, PREG_OFFSET_CAPTURE, $index_colon);
	$index_stop_search_at = $matches[0][1];

	$value = substr($string, $index_colon + 1, $index_stop_search_at - $index_colon - 1);
	$value = strip_tags($value);
	$value = str_replace(array("&#160;", "&#8239;", "&nbsp;"), " ", $value);
	$value = trim($value);

	preg_match ("/<[a-z]/", $string, $matches, PREG_OFFSET_CAPTURE, $index_stop_search_at + 1);
	$index_start_next_opening_tag = $matches[0][1];

	return array("value"=>$value, "index_start_next_tag"=>$index_start_next_opening_tag);
}


// Since there is inconsistency about how MOTA's vacancy announcements are structured, return an array
// of name/value pairs of whatever we can find before/after a ":" in the first 1000 characters.
// Start on the assumption that they are all in the first paragraph but if we only get one, widen to the
// first thousand characters. 
function get_name_value_pairs($job_description_node) {
	$first_paragraph = $job_description_node->find("p", 0);
	$name_value_pairs = [];

	if($first_paragraph !== null) {
		$first_paragraph_str =  $first_paragraph->outertext;
		$name_value_pairs = scan_string_for_name_value_pairs($first_paragraph_str);
	}

	// Count name value pairs and if only one, run it again but with the first 1000 characters of job description
	if(count($name_value_pairs) <= 1) {
		$first_1000_chars = substr($job_description_node->outertext, 0, 1000);
		$name_value_pairs = scan_string_for_name_value_pairs($first_1000_chars);
	}

	return $name_value_pairs;
}

function get_property($name_value_pairs, $property_name){
	$num_name_value_pairs = count($name_value_pairs);
	for($i=0; $i < $num_name_value_pairs; $i++) {
		$detail = $name_value_pairs[$i];
		if($property_name == $detail[0]) {
			return $detail[1];
		}
	}
	return "";
}

function scan_string_for_name_value_pairs($string) {
	$index_start = 0;
	$name_value_pairs = [];

	while(strpos($string, ":", $index_start)) {
		if(is_int($index_start)){
			$index_start = get_next_name_value_pair($string, $index_start, $name_value_pairs);
		}
		else {
			break;
		}
	}

	return $name_value_pairs;
}

function fix_headings (&$job_description_node) {
	for($i = 1; $i <= 3; $i++) {
		$headings = $job_description_node->find("h" . $i);
		foreach($headings as $heading) {
			$heading->innertext = mb_convert_case($heading->plaintext, MB_CASE_TITLE);
		}
	}
}

// Get the text that follows the last name/value pair.
function get_job_text ($job_description_node, $name_value_pairs) {
	fix_headings($job_description_node);
	$job_description_str = $job_description_node->innertext;

	$count_name_value_pairs = count($name_value_pairs);
	$index_start_desc = 0;

	if($count_name_value_pairs) {
		$last_value = $name_value_pairs[$count_name_value_pairs - 1][1];
		$index_last_value = strpos($job_description_str, $last_value);

		preg_match ("/<[a-z]/", $job_description_str, $matches, PREG_OFFSET_CAPTURE, $index_last_value);
		$index_start_next_opening_tag = $matches[0][1];
	}

	$job_text_str = substr($job_description_str, $index_start_next_opening_tag);

	// Cleanup
	$job_text_str = str_replace(array("&#160;", "&#8239;", "&nbsp;"), " ", $job_text_str);
	$job_text_str = str_replace("\"", "&quot;", $job_text_str);  // Insert break tags after paragraphs in preparation for removing paragraphs.
	$job_text_str = str_replace("</p>", "</p><br><br>", $job_text_str);  // Insert break tags after paragraphs in preparation for removing paragraphs.
	$job_text_str = strip_tags($job_text_str, ["h1", "h2", "h3", "br"]); // Remove tags except for headings and break tags.
	$job_text_str = preg_replace("/<h[123][^>]*>/", "<br><br><strong>", $job_text_str); // Replace opening header tags with breaks and strong tags.
	$job_text_str = preg_replace("/<\/h[123][^>]*>/", "</strong><br>", $job_text_str); // Replace closing header tags with closing strong tag and break.
	$job_text_str = strip_tags($job_text_str, ["br", "strong"]); // Remove all tags except break and strong tags.
	$job_text_str = preg_replace("/(<br> *){3,}/", "<br><br>", $job_text_str); // If there are 3 or more consequtive break tags, cut to 2.
	$job_text_str = preg_replace("/^(<br>)*/", "", $job_text_str); // Remove any break tags at start (will happen if desc start with heading).
	$job_text_str = preg_replace("/(<br>)* *$/", "", $job_text_str); // Remove any break tags at the end

	return $job_text_str;
}

function get_job_details($job_description_node) {
	// Scan start of document to get salary, closing date and so on.
	$name_value_pairs = get_name_value_pairs($job_description_node);

	$job_text = get_job_text($job_description_node, $name_value_pairs);
	return array("name_value_pairs"=>$name_value_pairs, "job_text"=>$job_text);
}

$url = "https://mota.dc.gov/jobs";
$origin_header = "https://mota.dc.gov";
$MOTA_jobs_page = get_HTML($url, $origin_header);
if($MOTA_jobs_page) {
	$json = '[';
	// Why does adding .ext to the "a" selector make it fail?
	$links = $MOTA_jobs_page->find("article > .content > .field > .field-items > .field-item > p.rteindent1 > a");
	
	$link = $links[0];
	foreach ($links as $link) {
		if(str_contains($link->innertext, "Talent Bank")) {
			break;
		}

		$vacancy_announcement_url = $link->href;

		// Now get details for this job by following the link
		$job_page = get_HTML($vacancy_announcement_url, "https://mota.applytojob.com");
		if($job_page) {
			$job_description_node = $job_page->find("#job-description", 0);
			if($job_description_node) {	
				$job_details = get_job_details($job_description_node);
				$name_value_pairs = $job_details["name_value_pairs"];

				$position_id = "";
				$job_title = get_property($name_value_pairs, "job_title");
				$open_date = get_property($name_value_pairs, "open_date");
				$close_date = get_property($name_value_pairs, "close_date");
				$salary_range = get_property($name_value_pairs, "salary");
				$office = get_property($name_value_pairs, "office");
				$type = get_property($name_value_pairs, "grade");
				$duration = "";
				$location = "";
				$other_text = $job_details["job_text"];

				$this_job = <<<THIS_JOB

				{
					"positionId": "$position_id",
					"jobTitle": "$job_title", 
					"department": "$office", 
					"type": "$type",
					"salaryRange": "$salary_range", 
					"url": "$vacancy_announcement_url", 
					"location": "$location", 
					"postedDate": "$open_date", 
					"closeDate": "$close_date", 
					"otherText": "$other_text"
				},
				THIS_JOB;
				$json .= $this_job;			}
			else {
				continue;
			}
		}
	}
	$json = substr($json, 0, strlen($json)-1); // Strip the last comma
	$json .= ']'; // Close the array
	$json = pretty_print_json($json, true); // Make it look nice for humans. Remove if actual JSON is desired (without <br> tags).
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MOTA jobs</title>
	<link rel="shortcut icon" href="https://dc.gov/sites/default/files/favicon_0.ico" type="image/vnd.microsoft.icon" />
	<script></script>
	<style>
		body {font-family: Helvetica,Arial; font-size: 13px;}
	</style>
</head>
<body>
	<h1>Positions from the Mayor's Office of Talent Acquisition (in JSON format)</h1>
	<p>This page is slow in part because it has to follow the links from MOTA's jobs page and scrape the contents of each.</p>
	<hr>
	<p><?= $json ?></p>
</body>
</html>
