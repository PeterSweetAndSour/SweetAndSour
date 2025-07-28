<?php
/* Build JSON file by scraping results from a copy of Peoplesoft  serach results page
   1. Search for "accountant" on <careers class="dc gov"></careers>
   2. View source and copy it to search-results-accountant.html 
	    *I could not get it to work with the dc.gov url.)
	 3. Upload to sweetandsour.org
	 4. Run this page
	 5. Copy JSON from page source
	 6. Copy half to search-results-new-ats.js and half to search-results-peoplesoft.js (remove trailing comma from last row of forst half!)
	 7. Upload the search-results-<source>.js files to sweetandsour.org/api

	 Scraping information: https://www.zenrows.com/blog/web-scraping-php#get-data
*/

include_once("../includes/simple_html_dom.php");
include_once("get-html-function.php");

// Get the search term from the URL
//$searchTerm =  $_GET["position"];

// Query PeopleSoft for jobs with this search term
$url = "https://sweetandsour.org/files/search-results-accountant.html";
$origin_header = "https://sweetandsour.org";

$html = get_HTML($url, $origin_header);
$searchResultsList = $html->find("section#win0divPT_PANEL2_CNTINNER .ps_grid-body", 0); // An unordered list
$jobs = [];
$searchResultsJson = "[]";

// Build an array that will be turned into JSON later. This will be used to populate the sidebar filters as well as show the positions.
if($searchResultsList) {
	$searchResultListItems = $searchResultsList->children;
	$last_key = array_search(end($searchResultListItems), $searchResultListItems);

	$searchResultsJson = "[\n";

	foreach($searchResultListItems as $key => $searchResultListItem) {
		$divChildrenOfListItem = $searchResultListItem->children;

		$jobTitle = $divChildrenOfListItem[0]->find("span.ps_box-value", 0)->innertext;
		$jobId = $divChildrenOfListItem[1]->find("span.ps_box-value", 0)->innertext;
		$location = $divChildrenOfListItem[2]->find("span.ps_box-value", 0)->innertext;
		$location = str_replace("&amp;", "&", $location);
		$location = str_replace("&nbsp;", "", $location);
		$checkBoxId = str_replace(" ", "_", $location);
;		$department = $divChildrenOfListItem[3]->find("span.ps_box-value", 0)->innertext;
		$department = str_replace("&amp;", "&", $department);
		$department = str_replace("&nbsp;", "", $department);
		$postedDate = $divChildrenOfListItem[4]->find("span.ps_box-value", 0)->innertext;
		$closeDate = $divChildrenOfListItem[5]->find("span.ps_box-value", 0)->innertext;

		$searchResultsJson .= "{\"jobTitle\": \"" . $jobTitle . "\", \"jobId\": " . $jobId . ", \"location\": \"" . $location . "\", \"department\": \"" . $department . "\", \"postedDate\": \"" . $postedDate . "\", \"closeDate\": \"" . $closeDate . "\"}";

		if ($key != $last_key) {
			$searchResultsJson .= ", ";
		}

		$searchResultsJson .= "\n";
	}
	$searchResultsJson .= "]";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search Jobs</title>
	<link rel="shortcut icon" href="https://dc.gov/sites/default/files/favicon_0.ico" type="image/vnd.microsoft.icon" />
	<script>
		const json = <?= $searchResultsJson ?>;
	</script>
</head>
<body>
<h1>View Souce to copy JSON with one job per line.</h1>
</body>
</html>
