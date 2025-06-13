<?php
/*
Jobs are on https://mota.dc.gov/jobs in groups
"EXECUTIVE CABINET", "INDEPENDENT AGENCY APPOINTED LEADERSHIP", "POLICY, OPERATIONS, AND SUPPORT POSITIONS"
which are in H3s in article > .content > field > field-items > field-item

Following the H3s are p.rteindent1 > a which contain only the job title. it will be necessary to follow
each link to get further information so a new cUrl connection for each one. 

Note that if "We currently do not have any open postings." then it
is in the same p.rteindent1 but its child element is an em.

The pages are unreliably structured so we will need to parse the string contents of #job-description:
* OFFICE... or AGENCY
* OPEN DATE... or OPEN
* CLOSE DATE (may not be present)
* GRADE... ie EXECUTIVE LEVEL...)
* SALARY... or SALARY RANGE
The name of the detail may or may not be in <b> or <strong> tags.

Then it is free form text with no consistent structure. There is no job ID or location.
The headings for one job are:
* background (yes, lower-case relying on CSS to capitalze)
* Major duties
* COMPETENCIES, KNOWLEDGE, SKILLS, AND ABILITIES
* MINIMUM QUALIFICATIONS
* Work environment
* RESIDENCY

then another has:
* INTRODUCTION:
* DUTIES AND RESPONSIBILITIES:
* OTHER SIGNIFICANT FACTS:
* RESIDENCY REQUIREMENT

... so I will just get "other text" for the search to work with.
*/
include_once("../includes/simple_html_dom.php");
include_once("function-get-html.php");

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
		echo("Ignore - inside href<br>");
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
function get_next_name_value_pair($first_1000_chars, $index_start, &$name_value_pairs) {
	$index_colon = strpos($first_1000_chars, ":", $index_start);
	//echo("index_colon: " . $index_colon . ", ||" . substr($first_1000_chars, $index_colon - 20, 40) . "|| ");
	$index_start = $index_colon + 1;

	// Ignore this colon if inside an inline style or href attribute (most likely scenario)
	if(colon_is_inside_tag($first_1000_chars, $index_colon)) {
		return $index_start;
	}

	//Get the text immediately before the colon up to the preceding ">"
	$index_end_previous_tag = index_previous_search_char($first_1000_chars, $index_colon, ">");
	$name = trim(substr($first_1000_chars, $index_end_previous_tag + 1, $index_colon - $index_end_previous_tag - 1));
	$value = "";

	// Need to walk forward to find non-whitespace text that is not a tag so to get the start of the value
	// then the value is from there to the next tag
	$index_next_non_whitespace_char = index_next_non_whitespace($first_1000_chars, $index_colon + 1);
	$value_and_index_start_next_tag = get_value_after_colon($index_next_non_whitespace_char, $first_1000_chars);
	$value = $value_and_index_start_next_tag["value"];
	$index_start_next_tag = $value_and_index_start_next_tag["index_start_next_tag"];

	if(strpos(strtolower($name), "office") or strpos(strtolower($name), "agency")) {
		$name = "department";
	}
	else if(strpos(strtolower($name), "open")) {
		$name = "postedDate";
	}
	else if(strpos(strtolower($name), "close") or strpos(strtolower($name), "closing")) {
		$name = "closeDate";
	}
	else if(strpos(strtolower($name), "grade")) {
		$name = "grade";
	}
	else if(strpos(strtolower($name), "salary") or strpos(strtolower($name), "executive")) {
		$name = "salary";
	}

	array_push($name_value_pairs, [$name, $value]);

	$index_start = $index_start_next_tag + 1;

	return $index_start;
}

// Get value from text after a colon
function get_value_after_colon($index_next_non_whitespace_char, $first_1000_chars) {
	$value = "";
	while($index_next_non_whitespace_char != -1) {
		$next_non_whitespace_char = substr($first_1000_chars, $index_next_non_whitespace_char, 1);
		//echo($next_non_whitespace_char . "<br>");

		if($next_non_whitespace_char == "<") { // Found a tag - could be opening or closing
			$index_end_of_tag = index_next_search_char($first_1000_chars, $index_next_non_whitespace_char + 1, ">"); // Find end of tag
			$index_next_non_whitespace_char = index_next_non_whitespace($first_1000_chars, $index_end_of_tag + 1);   // Reset index to end of tag
		}
		else { // Found the text after the colon
			$index_start_next_tag = index_next_search_char($first_1000_chars, $index_next_non_whitespace_char, "<");
			$value = substr($first_1000_chars, $index_next_non_whitespace_char, $index_start_next_tag - $index_next_non_whitespace_char);
			break;
		}
	}
	return array("value"=>$value, "index_start_next_tag"=>$index_start_next_tag);
}


// Since there is no consistency about how MOTA's vacancy announcements are structured, return an array
// of name/value pairs of whatever we can find before/after a ":" in the first 1000 characters
function get_name_value_pairs($job_description) {
	$first_1000_chars = substr($job_description, 0, 1000);

	$index_start = 0;
	$name_value_pairs = [];
	echo("This is get_name_value_pairs");
	while(strpos($first_1000_chars, ":", $index_start)) {
		if(is_int($index_start)){
			$index_start = get_next_name_value_pair($first_1000_chars, $index_start, $name_value_pairs);
		}
		else {
			break;
		}
	}

	return $name_value_pairs;
}

// Get the text that follows the name-value pairs as it may be useful for search.
function get_job_text ($job_description, $count_name_value_pairs) {
	$paragraphs = $job_description->find("p");
	for($i=0; $i < $count_name_value_pairs; $i++) {
		$paragraph_with_name_value_pair = $paragraphs[$i];
		$paragraph_with_name_value_pair	->remove();
	}

	$job_text = strip_tags($job_description);
	return $job_text;
}

function get_job_details($job_description) {
	// Scan start of document to get salary, closing date and so on.
	$name_value_pairs = get_name_value_pairs($job_description);

	// Now get the remaining text so it can be searched. 
	$count_name_value_pairs = count($name_value_pairs);

	$job_text = get_job_text($job_description, $count_name_value_pairs);
	return array("name_value_pairs"=>$name_value_pairs, "job_text"=>$job_text);
}
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MOTA Jobs</title>
	<link rel="shortcut icon" href="https://dc.gov/sites/default/files/favicon_0.ico" type="image/vnd.microsoft.icon" />
	<script></script>
	<style>
		.data {background-color: #fdf;}
	</style>
</head>
<body>
	<h1>Positions from the Mayor's Office of Talent Acquisition</h1>
	<?php

	$url = "https://mota.dc.gov/jobs";
	$origin_header = "https://mota.dc.gov";
	$MOTA_jobs_page = get_HTML($url, $origin_header);
	if($MOTA_jobs_page) {
		// Why does adding .ext to the "a" selector make it fail?
		$links = $MOTA_jobs_page->find("article > .content > .field > .field-items > .field-item > p.rteindent1 > a");
		
		$link = $links[0];
		foreach ($links as $link) {
				if(str_contains($link->innertext, "Talent Bank")) {
					break;
				}

				$job_page_href = $link->href;
				$job_title = $link->innertext;

			// Now get details for this job by following the link
			$job_page = get_HTML($job_page_href, "https://mota.applytojob.com");
			if($job_page) {
				$job_description_wrapper = $job_page->find("#job-description", 0);
				if($job_description_wrapper) {
					?>
					<h2><a href="<?= $job_page_href ?>"><?= $job_title ?></a></h2>
					<?php
					$job_description = $job_description_wrapper->innertext;

					// Display the text in name/value pairs from the vacancy announcement.
					$job_details = get_job_details($job_description);

					foreach ($job_details["name_value_pairs"] as $detail) {
						?>
						<?= $detail[0] ?>: <?= $detail[1] ?><br>
						<?php
					}

					$job_text = $job_details["job_text"];
					echo("<br>$job_text");
				}
				else {
					continue;
				}

				echo("<hr>");
			}
		}
	}
	?>
	
</body>
</html>