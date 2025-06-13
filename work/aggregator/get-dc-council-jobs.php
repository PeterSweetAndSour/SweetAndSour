<?php

include_once("../includes/simple_html_dom.php");
include_once("../includes/alt_autoload.php");
include_once("function-get-html.php");

ob_end_flush();
ob_implicit_flush();

function get_data($pattern, $text) {
	$data_found = preg_match($pattern, $text, $matches); 
	//echo("<br>". $pattern . "<br>");
	//print_r($matches);
	//echo("<br>");
	if($data_found) {
		return $matches[1];
	}
	else {
		return false;
	}
}

// Regex pattern must handle hyphen/dash, minus sign (u2212), ndash (u2013) and mdash (u2014) that may or may not have 
// spaces on one or both sides. Note the 'u' after the end of the pattern to force UTF-8.
function get_job_id ($text) {
	$pattern = "/ANNOUNCEMENT NO: (CDC ?(?:-|\x{2013}|\x{2014}|\x{2212}) ?[0-9]{2,4} ?(?:-|\x{2013}|\x{2014}|\x{2212}) ?[0-9]{2,4})/u";
	return get_data($pattern, $text);
}

// Have to cope with dates where the month name is written out such as February 24, 2025" and MM/DD/YY[YY]
function get_open_date($text) {
	$pattern = "/OPENING DATE: ((?:[A-Za-z]{3,9} [0-9]{1,2}, (?:20)?[0-9]{2})|(?:[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4})) /";
	$open_date_found = preg_match($pattern, $text, $matches);
	return get_data($pattern, $text);
}

function get_close_date($text) {
	$pattern = "/CLOSING DATE: ((?:Open )?(?:until filled)|(?:[A-Za-z]{3,9} [0-9]{1,2}, (?:20)?[0-9]{2})|(?:[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4})) /i";
	$open_date_found = preg_match($pattern, $text, $matches);
	return get_data($pattern, $text);
}

function get_salary_range($text) {
	//$pattern = "/SALARY RANGE: (\$[0-9,]{5,8} ?(?:-|\x{2013}|\x{2014}|\x{2212}) ?\$[0-9,]{5,8})/u"; // Don't know why this is not working. OK in regex101.com
	$pattern = "/SALARY RANGE:\s*(.*\s*.*)\s*TOUR OF DUTY/";
	return trim(get_data($pattern, $text));
}

function get_office($text) {
	$pattern = "/OFFICE:\s*(.*\s*.*)\s*TYPE OF APPOINTMENT/u";
	return trim(get_data($pattern, $text));
}

function get_type_of_appointment($text) {
	$pattern = "/TYPE OF APPOINTMENT:\s*(.*)\s*DURATION OF APPOINTMENT/";
	return get_data($pattern, $text);
}

function get_duration_of_appointment($text) {
	$pattern = "/DURATION OF APPOINTMENT:\s*(.*)\s*AREA OF CONSIDERATION/";
	return get_data($pattern, $text);
}

function get_location($text) {
	$pattern = "/LOCATION:\s*.*\s*(\d*.*)\s*\r?\n/"; // Trying to capture the address, not "Wilson Building" so ignore text that does not start with a number
	return trim(get_data($pattern, $text));
}

function get_other_text($text) {
	$index = strpos($text, "This position is NOT in a collective bargaining unit.");
	if(!$index) {
		$index = strpos($text, "POSITION OVERVIEW");
	}

	if(!$index) {
		$index = strpos($text, "Washington, DC") + 6;
	}

	$other_text = substr($text, $index);
	return $other_text;
}

/**
 * Formats a JSON string for pretty printing
 *
 * @param string $json The JSON to make pretty
 * @param bool $html Insert nonbreaking spaces and <br />s for tabs and linebreaks
 * @return string The prettified output
 * @author Jay Roberts
 */
function format_json($json, $html = false) {
	$tabcount = 0; 
	$result = ''; 
	$inquote = false; 
	$ignorenext = false; 
	if ($html) { 
			$tab = "&nbsp;&nbsp;&nbsp;"; 
			$newline = "<br/>";
	} else { 
			$tab = "\t"; 
			$newline = "\n"; 
	} 
	for($i = 0; $i < strlen($json); $i++) { 
			$char = $json[$i]; 
			$previous_char = $json[$i - 1]; 
			if ($ignorenext) { 
					$result .= $char; 
					$ignorenext = false; 
			} else { 
					switch($char) { 
							case '{': 
									$tabcount++; 
									$result .= $char . $newline . str_repeat($tab, $tabcount); 
									break; 
							case '}': 
									$tabcount--; 
									$result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char; 
									break; 
							case ',': 
									if($previous_char == "\"") {
										$result .= $char . $newline . str_repeat($tab, $tabcount); 
									}
									else {
										$result .= $char; 
									}
									break; 
							case '"': 
									$inquote = !$inquote; 
									$result .= $char; 
									break; 
							case '\\': 
									if ($inquote) $ignorenext = true; 
									$result .= $char; 
									break; 
							default: 
									$result .= $char; 
					} 
			} 
	} 
	return $result; 
}

$parser = new Smalot\PdfParser\Parser();

$url = "https://dccouncil.gov/jobs-solicitations21/";
$origin_header = "https://dccouncil.gov";
$jobs = [];

$DC_Council_jobs_page = get_HTML($url, $origin_header);
if($DC_Council_jobs_page) {
	$job_links = $DC_Council_jobs_page->find("article.listing-post > h3 > a");

	$json = '[';
	foreach ($job_links as $link) {
		$job_title = $link->innertext;
		$page_with_pdf_link = get_HTML($link->href, $origin_header);

		if($page_with_pdf_link) {
			$link_to_document = $page_with_pdf_link->find("a.icon--pdf.icon-link");

			if($link_to_document) {
				$document_url =	$link_to_document[0]->href;
	
				// Conirm it is a PDF because on 21-Feb-25 one is a Word document
				if(!preg_match('/.*\.pdf$/', $document_url)) {
					continue;
				}

				// Step 4: Download and parse the pdf
				$pdf_content = file_get_contents($document_url);
				$pdf = $parser->parseContent($pdf_content);
				$text = $pdf->getText();

				$announcement_id = get_job_id($text);
				$open_date = get_open_date($text);
				$close_date = get_close_date($text);
				$salary_range = get_salary_range($text);
				$office = get_office($text);
				$type = get_type_of_appointment($text);
				$duration = get_duration_of_appointment($text);
				$location = get_location($text);
				$other_text = get_other_text($text);

				//$json .= '{"jobTitle": "' . $job_title . '", "jobId": "' . $announcement_id . '", "url": "' . $document_url . '", "location": "' . $location . '", "department": "' . $office . '", "postedDate": "' . $open_date . '", "closeDate": "' . $close_date . '", "salaryRange": "' . $salary_range . '"},';	
				$this_job = <<<THIS_JOB
				{"jobTitle": "$job_title", "jobId": "$announcement_id", "url": "$document_url", "location": "$location", "department": "$office", "postedDate": "$open_date", "closeDate": "$close_date", "salaryRange": "$salary_range", "otherText": "$other_text"}
				THIS_JOB;
				$json .= $this_job . ",";
			}
			else {
				continue;
			}
		}
		else {
			continue;
		}
	}
	// Strip the last comma
	$json = substr($json, 0, strlen($json)-1);
	$json .= ']';
	//$json = json_encode($json, JSON_PRETTY_PRINT);
	$json = format_json($json, true);

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HTML5 Boilerplate</title>
  <style>
		body {font-family: Helvetica,Arial;}
	</style>
</head>

<body>
  <h1>DC Council jobs (in JSON format)</h1>

		<?= $json ?>

</body>

</html>
