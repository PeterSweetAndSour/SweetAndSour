<?php
// https://www.zenrows.com/blog/web-scraping-php#get-data

include_once("../includes/simple_html_dom.php");

// Get the search term from the URL
$searchTerm =  $_GET["position"];

// Query PeopleSoft for jobs with this search term
$url = "https://sweetandsour.org/files/search-results-accountant.html";
//$url = "https://careers.dc.gov/psc/erecruit/EMPLOYEE/HRMS/c/HRS_HRAM_FL.HRS_CG_SEARCH_FL.GBL?Page=HRS_APP_SCHJOB_FL&Action=U&HRS_SCH_WRK_HRS_SCH_TEXT100=" . $searchTerm;

$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."\cacert.pem");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Android 4.4; Tablet; rv:41.0) Gecko/41.0 Firefox/41.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$headers = [
	"Accept: */*",
	"Accept-Encoding: keep-alive",
	"Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7,vi;q=0.6",
	"Content-Type: application/x-www-form-urlencoded",
	"Origin: https://careers.dc.gov"
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$htmlContent = curl_exec( $ch );

// check for errors
if ($htmlContent === false) {
	// handle the error
	$error = curl_error($ch);
	echo "cURL error: " . $error;
	exit;
}

// close cURL session
curl_close($ch);

$html = str_get_html($htmlContent);
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
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

	<link 
		rel="stylesheet" 
		href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
		integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
		crossorigin="" />
	<script 
		src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
		integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
		crossorigin="">
	</script>

	<link href="search-results.css" rel="stylesheet">
	<link href="search-results-styles-from-peoplesoft.css" rel="stylesheet">

	<script>
		let globals = {};
		globals.searchResultsJson = <?= $searchResultsJson ?>;
	</script>
</head>
<body>
	<form name="win0" method="post" action="https://careers.dc.gov/psc/erecruit/EMPLOYEE/HRMS/c/HRS_HRAM_FL.HRS_CG_SEARCH_FL.GBL" autocomplete="off" class="PSForm">
		<a class="ps-anchor" id="ICFirstAnchor_win0"></a>
		<div id="PT_WRAPPER" class="ps_wrapper">
			<div class="ps_header" id="PT_HEADER">
				<div class="ps_header_panel" id="PT_HEADER_PANEL">
					<div class="ps_pspagecontainer_hdr">
						<div class="ps_box-group psc_layout ps_header_bar-container">
							<div class=" psc_force-hidden">
								<div class="ps_box-label"><span class="ps-label">&nbsp;</span></div>
								<span class="ps_box-value" id="ICSCRIPTSID">ptnbsid=%2fxD4mBuTijhTakliXmbW3%2fhrUcA%3d</span>
							</div>
							<div role="banner" class=" ps_header_bar ps_career_fluid1">
								<div class=" ps_back_cont">
									<div class=" ps_system_cont">
										<div class="ps_box-group psc_layout"></div>
									</div>
									<div class=" ps_custom_cont"></div>
									<div class="ps_box-group ps_career_fluid1">
										<div class="ps_box-htmlarea">
											<div class="ps-htmlarea">
												<!-- Begin HTML Area Name Undisclosed -->
												<!-- End HTML Area -->
											</div>
										</div>
										<a name="DC_NEW_LOGO" class="ps-anchor"></a>
										<div class="ps_box-staticimg"><img src="//careers.dc.gov/cs/erecruit/cache/DC_NEW_PS_LOGO_1.png" class="ps-staticimg" alt="Static Image" title="Static Image"></div>
									</div>
								</div>
								<div class=" ps_pagetitle_cont">
									<div class=" ps_system_cont">
										<h1 id="PT_PAGETITLE" class="ps_pagetitle"><span class="ps-text" id="PT_PAGETITLElbl">Search Jobs</span></h1>
									</div>
									<div class=" ps_custom_cont"></div>
								</div>
							</div>
							<div class="ps_box-group psc_layout ps_header_confirmation">
								<div role="alert" aria-live="assertive" class="ps_box-group psc_layout psc_confirmation-animate ">
									<div class="ps_box-group psc_layout psc_confirmation-area">
										<div class="ps_box-longedit psc_disabled psc_wrappable psc_has_value psc_confirmation-msg">
											<div class="ps_box-label"><span class="ps-label">&nbsp;</span></div>
											<span class="ps_box-value" id="PT_CONFIRM_MSG">&nbsp;</span>
										</div>
										<div class="ps_box-button psc_image_only psc_modal-close psc_confirmation-close"><span class="ps-button-wrapper" title="Close"><a id="PT_CONFIRM_CLOSE" class="ps-button" role="button" onclick="javascript:cancelBubble(event);" href="javascript:submitAction_win0(document.win0,'PT_CONFIRM_CLOSE');"><img src="//careers.dc.gov/cs/erecruit/cache/PT_MODAL_CLOSE_NUI_1.svg" id="PT_CONFIRM_CLOSE$IMG" class="ps-img" alt="Close"></a></span></div>
									</div>
								</div>
							</div>
							<div class="ps_box-group psc_layout ps_ag-processheader"></div>
							<div class=" ps_header_bar_custom"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="ps_search psc_close side" id="PT_SEARCH"></div>
			<div class="ps_box-pagetabs psc_hidden"></div>
			<div class="ps_mid_section" id="PT_MID_SECTION">
				<div class="pst_panel-side1" id="PT_SIDE">
					<div class="pst_panel-side1-top" id="PT_SIDE_TOP"></div>
					<div class="pst_panel-side1-bottom" id="PT_SIDE_BOTTOM"></div>
				</div>
				<div class="ps_content" id="PT_CONTENT" role="main">
					<div class="ps_main" id="PT_MAIN">
						<div class="ps_pagecontainer">
							<div class="ps_pspagecontainer">
								<div class="ps_box-group psc_layout psc_panel-container psc_panel-fixed psc_initial">
									<div class="ps_box-group psc_layout psc_panel-action ">
										<div class="ps_box-frame">
											<div class="ps_box-link">
												<span class="ps-link-wrapper">
													<a class="ps-link careers-home" href="https://careers.dc.gov">
														<span class="ps-text">Careers</span>
														<img src="//careers.dc.gov/cs/erecruit/cache/PS_CAREERS_HOME_S_FL_1.svg" class="ps-img" alt="">
													</a>
												</span>
											</div>
										</div>
										<section id="filterContainer" class="ps_box-group psc_layout psc_panel-actioninterior">
											<div class="ps_box-group psc_layout ps_panel_content">
												<div class="ps_box-group psc_layout">
													<div class="ps_box-group psc_layout psc_margin-top05em pts_facets  pts_kwsrch">
														<div class="ps_box-group psc_layout psc_padding-bottom3em">
															<div class="ps_box-group pts_width100">
																<div class="ps_box-scrollarea">

																	<!-- Location -->
																	<div class="ps_box-scrollarea-row">
																		<div class="ps_box-group psc_collapsible facet_group_box">
																			<div>
																				<a href="#" id="openMapLink" class="openMapLink">Select from map</a>
																				<h2 class="ps_header-group">Location</h2>
																			</div>
																			<div class="ps_detail-group">
																				<fieldset id="locationFieldset" class="ps_box-group psc_layout ps_grid-norowborder pts_nui_norowlines psc_margin-left0_4em psc_fieldset-hidereadable pts_nui_facetarea_ms">
																					<legend class="ps_header-group">Department</legend>

																					<div class="ps_box-group psc_layout">
																						<div class="ps_box-htmlarea ptpg_treefacet_size pts_nui_facetarea_ms">
																							<div class="ps-htmlarea">
																								<!-- ** What does bindingapplied="true" do? Taking it out makes the DIV disappear. ** -->
																								<div bindingapplied="true" class="ptpg_jet_treeitem pg_treefacet_container oj-tree oj-tree-0 oj-tree-default oj-component-initnode">
																										<ul id="locationFilter" role="tree" class="oj-tree-list oj-tree-no-dots oj-tree-no-icons">
																										</ul>
																								</div>
																							</div>
																						</div>
																					</div>
																				</fieldset>
																			</div>
																		</div>
																	</div>

																	<!-- Department -->
																	<div class="ps_box-scrollarea-row">
																		<div class="ps_box-group psc_collapsible facet_group_box">
																			<h2 class="ps_header-group">Department</h2>
																			<div class="ps_detail-group">
																				<fieldset id="departmentFieldset" class="ps_box-group psc_layout ps_grid-norowborder pts_nui_norowlines psc_margin-left0_4em psc_fieldset-hidereadable pts_nui_facetarea_ms">
																					<legend class="ps_header-group">Department</legend>

																					<div class="ps_box-group psc_layout">
																						<div class="ps_box-htmlarea ptpg_treefacet_size pts_nui_facetarea_ms">
																							<div class="ps-htmlarea">
																								<!-- ** What does bindingapplied="true" do? Taking it out makes the DIV disappear. ** -->
																								<div bindingapplied="true" class="ptpg_jet_treeitem pg_treefacet_container oj-tree oj-tree-0 oj-tree-default oj-component-initnode">
																										<ul id="departmentFilter" role="tree" class="oj-tree-list oj-tree-no-dots oj-tree-no-icons">
																										</ul>
																								</div>
																							</div>
																						</div>
																					</div>
																				</fieldset>
																			</div>
																		</div>
																	</div>

																	<!-- ClosingDate -->
																	<div class="ps_box-scrollarea-row">
																		<div class="ps_box-group psc_collapsible facet_group_box">
																			<h2 class="ps_header-group">Close Date</h2>
																			<div class="ps_detail-group">
																				<fieldset id="closeDateFieldset" class="ps_box-group psc_layout ps_grid-norowborder pts_nui_norowlines psc_margin-left0_4em psc_fieldset-hidereadable pts_nui_facetarea_ms">
																					<legend class="ps_header-group">Close Date</legend>

																					<div class="ps_box-group psc_layout">
																						<div class="ps_box-htmlarea ptpg_treefacet_size pts_nui_facetarea_ms">
																							<div class="ps-htmlarea">
																								<!-- ** What does bindingapplied="true" do? Taking it out makes the DIV disappear. ** -->
																								<div bindingapplied="true" class="ptpg_jet_treeitem pg_treefacet_container oj-tree oj-tree-0 oj-tree-default oj-component-initnode">
																									<ul id="closeDateFilter" role="tree" class="oj-tree-list oj-tree-no-dots oj-tree-no-icons">
																										<li class="ps_grid-row">
																											<div class="ps_box-group psc_layout">
																												<div class="ps_box-checkbox psc_standard psc_margin-top0_2em pts_fontweight-normal">
																													<div class="ps_box-control">
																														<input type="checkbox" class="ps-checkbox" value="Within 7 days" checked="true" id="closeDateWithin7Days">
																													</div>
																													<div class="ps_box-label ps_right">
																														<label class="ps-label" for="closeDateWithin7Days" id="labelCloseDateWithin7Days">Within 7 days</label>
																													</div>
																												</div>
																											</div>
																										</li>
																										<li class="ps_grid-row">
																											<div class="ps_box-group psc_layout">
																												<div class="ps_box-checkbox psc_standard psc_margin-top0_2em pts_fontweight-normal">
																													<div class="ps_box-control">
																														<input type="checkbox" class="ps-checkbox" value="More than 7 days" checked="true" id="closeDateMoreThan7Days">
																													</div>
																													<div class="ps_box-label ps_right">
																														<label class="ps-label" for="closeDateMoreThan7Days" id="labelCloseDateMoreThan7Days">More than 7 days</label>
																													</div>
																												</div>
																											</div>
																										</li>
																									</ul>
																								</div>
																							</div>
																						</div>
																					</div>
																				</fieldset>
																			</div>
																		</div>
																	</div>

																</div>
															</div>
														</div>
														<div class="ps_box-group psc_layout">
															<div class="ps_box-button psc_image_only psc_force-hidden"></div>
														</div>
														<div class="ps_box-group psc_layout psc_force-hidden"></div>
													</div>
												</div>
											</div>
										</section>
									</div>
									<div class="ps_box-group psc_layout psc_panel-content">
										<section class="ps_box-group psc_layout psc_panel-contentinterior">
											<div class="ps_box-group psc_layout psc_page-container">
												<div class="ps_box-group psc_layout psc_flex-none">
													<div class="ps_box-group psc_layout">
														<div class="ps_box-group psc_layout">
															<div class="ps_box-group psc_layout ps_apps_pageheader psc_pageheader-fixed">
																<div class="ps_box-group psc_layout psc_maxwidth-40em psc_margin-left0_5em">
																	<div class="ps_box-group psc_layout hrs_cg_search_fld_gbox psc_margin-bottomnone psc_padding-bottomnone psc_display-inlineblock">
																		<div class="ps_box-edit psc_label-top psc_padding-top0_5em psc_padding-bottomnone psc_lineheight-full">
																			<div class="ps_box-label"><label for="HRS_SCH_WRK_HRS_SCH_TEXT100" class="ps-label">Search Jobs</label></div>
																			<div class="ps_box-control">
																				<input type="text" id="searchTerm" class="ps-edit" placeholder="Search by job title, location, or keyword" maxlength="100" value="<?= $searchTerm ?>">
																			</div>
																		</div>
																	</div>
																	<div class="ps_box-button psc_image_only psc_display-inlineblock psc_padding-left0_4em psc_padding-top0_1em psc_lineheight-full"><span class="ps-button-wrapper" title="Search"><a class="ps-button" role="button" href="#"><img src="//careers.dc.gov/cs/erecruit/cache/PT_NUI_EXECUTE_1.svg" class="ps-img" alt="Search"></a></span></div>
																	<div class="ps_box-group psc_layout psc_margin-top0_4em ">
																		<div class="ps_box-link psc_display-inlineblock psc_float-right">
																			<span class="ps-link-wrapper">
																				<a id="HRS_SCH_WRK_FLU_HRS_SAVE_SRCH" class="ps-link" ptlinktgt="pt_peoplecode" onclick="javascript:cancelBubble(event);" href="#">Save Search</a>
																			</span>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="ps_box-group psc_layout ps_apps_content">
													<div class="ps_box-group psc_layout">
														<div class="ps_box-group psc_layout psc_margin-topnone">
															<div class="ps_box-group hrs_cg_page_width_1024">
																<div class="ps_box-grid-list psc_wrap psc_gridlist-standard psc_grid-norowcount psc_grid-notitle psc_gridlist-bordertop psc_show-actionable">
																	<div class="ps_box-grid-header">
																		<div class="ps_box-grid-header_bar psc_has_systemop">
																			<div class="ps_header-grid-custom">
																				<div class="ps_box-group psc_layout psc_padding-top0_7em psc_padding-left1em">
																					<div class="ps_box-htmlarea hrs_searched_keyword ">
																						<div class="ps-htmlarea">

																							<!-- Number of jobs found for search term -->
																							<b id="searchResultCount"></b> jobs found for: <b>"<?= $searchTerm ?>"</b>

																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="ps_header-grid-end"></div>
																		</div>
																	</div>
																	<div class="ps_box-gridc">
																		<div class="ps_box-gridc-right">
																			<div class="ps_box-grid ps_scrollable sbar sbar_v ps_scrollable_v">

																				<!-- List of jobs generated by Javascript from searchResultsJson. -->
																				<div class="ps_grid-list" id="jobsListContainer" title="Search Results List">
																					<h1>View source to see JSON containing search results</h1>
																				</div>
																				<div class="hidden" id="noJobs">
																					<p>Sorry, there are no jobs that match your search. Try something else.</p>
																				</div>

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</section>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</form>

	<div id="overlay" class="overlay hidden">
		<div class="content">
			<button id="closeMapBtn" class="overlayBtn closeMapBtn">&times;</button>
			<h3>DC government work locations within commuting distance</h3>
			<div id="warningNotice" class="warningNotice hidden"></div>
			<div>
				<form>
					<div>
						<label for="startAddress">Enter your address instead of the President's (street number and name in DC/MD/VA within 40 miles of the US Capitol Building):</label><br>
						<input type="text" name="startAddress" id="startAddress" maxlength="32" size="32" value="1600 Pennsylvania Ave NW" />
					</div>
					<div>
						How you will get to work?
						<input type="radio" name="transitMode" id="modeTransit" value="transit" checked /><label for="modeTransit">Transit</label>
						<input type="radio" name="transitMode" id="modeWalk" value="walk" /><label for="modeWalk">Walk</label>
						<input type="radio" name="transitMode" id="modeBicycle" value="bicycle" /><label for="modeBicycle">Bicycle</label>
						<input type="radio" name="transitMode" id="modeDrive" value="drive" /><label for="modeDrive">Drive</label>
					</div>
					<div>
						Maximum commute time (up to 60 minutes)? <input type="number" name="maxTime" id="maxTime" size="4" value="45" min="3" max="60" />
					</div>
					<p>Notes:</p>
					<ol>
						<li>The areas shown to be reachable by transit appear to be quite conservative - you will likely be able to go further but check bus/train schedules.</li>
						<li>Parking is <strong>not</strong> provided at most DC government locations.</li>
						<li>Click on the work locations markers to include, or exclude, specific work locations regardless of whether they are inside the result area.</li>
					</ol>
					
					<button id="updateMapBtn" class="overlayBtn updateMapBtn">Update!</button>
					<div id="map" class="map"></div>
				</form>
			</div>
		</div>
	</div>
	<script src="search-results.js"></script>
</body>
</html>