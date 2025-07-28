<?php

include_once("../includes/simple_html_dom.php");
include_once("../includes/fn_loadPDFParser.php");
include_once("../includes/function-get-html.php");
include_once("../includes/fn_prettyPrintJSON.php");

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
				$vacancy_announcement_url =	$link_to_document[0]->href;
	
				// Conirm it is a PDF because on 21-Feb-25 one is a Word document
				if(!preg_match('/.*\.pdf$/', $vacancy_announcement_url)) {
					continue;
				}

				// Step 4: Download and parse the pdf
				$pdf_content = file_get_contents($vacancy_announcement_url);
				$pdf = $parser->parseContent($pdf_content);
				$text = $pdf->getText();

				$position_id = get_job_id($text);
				$open_date = get_open_date($text);
				$close_date = get_close_date($text);
				$salary_range = get_salary_range($text);
				$office = get_office($text);
				$type = get_type_of_appointment($text);
				$duration = get_duration_of_appointment($text);
				$location = get_location($text);
				$other_text = get_other_text($text);

				$this_job = <<<THIS_JOB
				{
					"jobTitle": "$job_title", 
					"positionId": "$position_id", 
					"url": "$vacancy_announcement_url", 
					"location": "$location", 
					"department": "$office", 
					"postedDate": "$open_date", 
					"closeDate": "$close_date", 
					"salaryRange": "$salary_range", 
					"otherText": "$other_text"
				}
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
	
	$json = substr($json, 0, strlen($json)-1); // Strip the last comma
	$json .= ']'; // Close the array
	$json = pretty_print_json($json, true); // Make it look nice for humans. Remove if actual JSON is desired.

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DC Council jobs</title>
	<link rel="shortcut icon" href="https://dc.gov/sites/default/files/favicon_0.ico" type="image/vnd.microsoft.icon" />
  <style>
		body {font-family: Helvetica,Arial;}
	</style>
</head>
<body>
  <h1>DC Council jobs (in JSON format)</h1>
	<?= $json ?>
</body>
</html>
