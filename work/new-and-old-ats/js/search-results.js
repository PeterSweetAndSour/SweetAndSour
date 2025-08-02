const searchResults = (function() {
	globals = {};
	globals.searchResultsJson = [];
	globals.apiKey = "3be05a9e24e6444e92828765a54ee719";
	globals.centerDC = {lat: 38.89046131142881, long: -77.00904254243133}; // US Capitol Building
	globals.searchRadius = 65000; // in meters from centerDC
	globals.zoom = 13;
	globals.DCGovtWorkplaces = { // Need a complete list
		"One Judiciary Square": {lat: 38.89575842409828, long: -77.0157525685818, checkBoxId: "1_Judiciary_Square"},
		"1 Judiciary Square": {lat: 38.89575842409828, long: -77.0157525685818, checkBoxId: "1_Judiciary_Square"}, /* Handling "1" as well as "One" */
		"64 New York Ave NE": {lat: 38.909404103508585, long: -77.00682069859793, checkBoxId: "64_New_York_Ave_NE"},
		"100 M St SE": {lat: 38.87693687601847, long: -77.00543590240223, checkBoxId: "100_M_St_SE"},
		"200 I St SE": {lat: 38.88009836346278, long: -77.00301703123823, checkBoxId: "200_I_St_SE"},
		"250 E St SW": {lat: 38.88308925938507, long: -77.01442327356588, checkBoxId: "250_E_St_SW"},
		"250 M St SE": {lat: 38.87710278028664, long: -77.00228744473007, checkBoxId: "250_M_St_SE"},
		"300 Indiana Ave NW": {lat: 38.89434842828109, long: -77.01654850240159, checkBoxId: "300_Indiana_Ave_NW"},
		"400 6th St NW": {lat: 38.89531187250936, long: -77.02019283138686, checkBoxId: "400_6th_St_NW"},
		"401 E St SW": {lat: 38.88384810576917, long: -77.01837154472987, checkBoxId: "401_E_St_SW"},
		"441 4th St NW": {lat: 38.895274696574845, long: -77.01549536345173, checkBoxId: "441_4th_St_NW"}, /* a.k.a. One Judiciary Square! */
		"450 H St NW": {lat: 38.8996997262375, long: -77.01834512531399, checkBoxId: "450_H_St_NW"},
		"500 K St NE": {lat: 38.902917768930244, long: -76.99929019090672, checkBoxId: "500_K_St_NE"},
		"K St NE": {lat: 38.902917768930244, long: -76.99929019090672, checkBoxId: "500_K_St_NE"}, /* DACL is posting jobs displaying the location as just "K STREET NE" :-( */
		"515 5th St NW": {lat: 38.896895522050734, long: -77.01835873123757, checkBoxId: "515_5th_St_NW"},
		"655 15th St NW": {lat: 38.89808214620608, long: -77.03307029705881, checkBoxId: "655_15th_St_NW"},
		"821 Howard Rd SE": {lat: 38.86407220427522, long: -76.99797825570516, checkBoxId: "821_Howard_Rd_SE"},
		"899 N Capitol St NE": {lat: 38.90122286178571, long: -77.00851884472931, checkBoxId: "899_N_Capitol_St_NE"},
		"901 G St NW": {lat: 38.898857617145985, long: -77.02490318890965, checkBoxId: "901_G_St_NW"}, /* (MLK Library) */
		"955 L'Enfant Plaza SW": {lat: 38.88474343293372, long: -77.02495740233987, checkBoxId: "955 L'Enfant Plaza SW"},
		"1000 Mt Olivet Rd NE": {lat: 38.912293364817415, long: -76.98856784061661, checkBoxId: "1000_Mt_Olivet_Rd_NE"},
		"1015 Half St SE": {lat: 38.878250824689125, long: -77.00787066007439, checkBoxId: "1015_Half_St_SE"},
		"1030 15th St NW": {lat: 38.90351341483112, long: -77.03502647350298, checkBoxId: "1030_15th_St_NW"},
		"1050 First St NE": {lat: 38.90351025490712, long: -77.00626776007343, checkBoxId: "1050_1st_St_NE"},
		"1050 1st St NE": {lat: 38.90351025490712, long: -77.00626776007343, checkBoxId: "1050_1st_St_NE"}, /* Handling "1st" as well as "First" */
		"1100 4th St SW": {lat: 38.8779236210903, long: -77.01734764473007, checkBoxId: "1100_4th_St_SW"},
		"1100 Alabama Ave SE": {lat: 38.84824020773402, long:-76.98793478699581, checkBoxId: "1100_Alabama_Ave_SE"}, /* (St. Elizabeths Hospital) */
		"1133 15th St NW": {lat: 38.90504625093286, long: -77.0340116735652, checkBoxId: "1133_15th_St_NW"},
		"1200 First St NE": {lat: 38.9062224010956, long: -77.00633570240119, checkBoxId: "1200_1st_St_NE"},
		"1200 1St NE": {lat: 38.9062224010956, long: -77.00633570240119, checkBoxId: "1200_1st_St_NE"}, /* Handling "1st" as well as "First" */
		"1225 I St NW": {lat: 38.90176666835236, long: -77.02900950255103, checkBoxId: "1225_I_St_NW"},
		"1250 U St NW": {lat: 38.91691674221576, long: -77.02927802126682, checkBoxId: "1250_U_St_NW"},
		"1325 G St NW": {lat: 38.898638267512005, long: -77.03104443123755, checkBoxId: "1325_G_St_NW"},
		"1350 Pennsylvania Ave NW": {lat: 38.89524892650393, long: -77.0311579735655, checkBoxId: "1350_Pennsylvania_Ave_NW"},
		"John A Wilson Building": {lat: 38.89524892650393, long: -77.0311579735655, checkBoxId: "1350_Pennsylvania_Ave_NW"}, /* Handling building name as well as address */
		"1900 E St SE": {lat: 38.88289549219518, long: -76.97726929403633, checkBoxId: "1900_E_St_SE"}, 
		"1901 D St SE": {lat: 38.883812015891536, long: -76.97599977044904, checkBoxId: "1901_D_St_SE"}, /* (Detention Facility) */
		"Detention Facility": {lat: 38.883812015891536, long: -76.97599977044904, checkBoxId: "1901_D_St_SE"}, /* Handling title as well as address */
		"2000 14th St NW": {lat: 38.91751656148031, long: -77.03237447356476, checkBoxId: "2000_14th_St_NW"},
		"2001 E Capitol St": {lat: 38.88910535380775, long: -76.97550222355162, checkBoxId: "2001_E_Capitol_St"}, /* (DC Armory) */
		"2235 Shannon Pl SE": {lat: 38.86457082553895, long:  -76.99139750255229, checkBoxId: "2235_Shannon_Pl_SE"},
		"2720 Martin Luther King Jr SE": {lat: 38.853848229101054, long: -76.9951069305929, checkBoxId: "2720_Martin_Luther_King_Jr_SE"},
		"3924 Minnesota Ave NE": {lat: 38.89411232957772, long: -76.95144373123782, checkBoxId: "3924_Minnesota_Ave_NE"},
		"4058 Minnesota Ave NE": {lat: 38.8972271711626, long: -76.94782147356544, checkBoxId: "4058_Minnesota_Ave_NE"},
		"4665 Blue Plains Dr SW": {lat: 38.82174895414032, long: -77.01337855584724, checkBoxId: "4665_Blue_Plains_Dr_SW"}, /* (Police Academy) */
		"5171 S Dakota Ave NE": {lat: 38.953866767991535, long: -76.99689878892342, checkBoxId: "5171_S_Dakota_Ave_NE"}
	};
	globals.uniqueLocationsInResults = {};
	globals.markerLocations =[];
	globals.mapInitialized = false;
	globals.mapOpen = false;

	const _getSearchResults = function() {
		let searchResultsPeoplesoft = [];
		let searchResultsNewATS = [];
		globals.searchResultsJson = [];
		const pilotAgencies = "Office of the Chief Technology Officer, Office of Contracting & Procurement and the Department of Parks & Recreation";
		const searchParams = new URLSearchParams(window.location.search);
		const searchTerm = searchParams.get("position");
		const server_Peoplesoft_URL = "./api/search-results-peoplesoft.js?search-term=" + searchTerm; // To PeopleSoft
		const server_New_ATS_URL = "./api/search-results-new-ats.js?search-term=" + searchTerm; // To new ATS for pilot agencies
	
		const request1 = fetch(server_Peoplesoft_URL)
			.then(response => {
				if (response.ok) {
					return response.json();
				}
				else {
					throw new Error("Search failed for DC government agencies outside " + pilotAgencies + ". Response status = " + response.status);
				}
			})
			.then((json) => { 
				_updateJsonwithUrlsToVacancyAnnouncement("Peoplesoft", json);
				searchResultsPeoplesoft = json; // Uses the spread operator which is new to me! 
			})
			.catch(error => {
				_setErrorMessage(error);
			});

		
		const request2 = fetch(server_New_ATS_URL)
			.then(response => {
				if (response.ok) {
					return response.json();
				}
				else {
					throw new Error("Search failed for positions at the " + pilotAgencies + ". Response status = " + response.status);
				}
			})
			.then((json) => { 
				_updateJsonwithUrlsToVacancyAnnouncement("New ATS", json);
				searchResultsNewATS = json; 
			})
			.catch(error => {
				_setErrorMessage(error);
			});

		Promise.allSettled([request1, request2])
			.then(() => {
				globals.searchResultsJson = [...searchResultsPeoplesoft, ...searchResultsNewATS]; // Uses the spread operator which is new to me! 
				_processSearchResults();
			})
			.catch(error => {
				_setErrorMessage("Something went wrong. " + error);
			});
	}

	const _updateJsonwithUrlsToVacancyAnnouncement = function(source, json) {
		for(searchResult of json) {
			if(source === "Peoplesoft") {
				_setUrlToPeoplesoftVacancyAnnouncement(searchResult);
			}
			else {
				_setUrlToNewATSVacancyAnnouncement(searchResult);
			}
		}
	};

	const _setUrlToPeoplesoftVacancyAnnouncement = function(searchResult) {
		const postingSequence = 1;
		const jobId = searchResult.jobId;
		const urlToVacancyAnnouncement = "https://careers.dc.gov/psp/erecruit/EMPLOYEE/HRMS/c/HRS_HRAM_FL.HRS_CG_SEARCH_FL.GBL?Page=HRS_APP_JBPST_FL&Action=U&FOCUS=Applicant&SiteId=1&JobOpeningId=" + jobId +"&PostingSeq=" + postingSequence;
		searchResult.url = urlToVacancyAnnouncement;
		searchResult.source = "Peoplesoft";  // Just for demonstration.Remove from final.
		searchResult.class = "peoplesoft";
	}
	const _setUrlToNewATSVacancyAnnouncement = function(searchResult) {
		const jobTitle = searchResult.jobTitle;
		const jobId = searchResult.jobId;
		const urlToVacancyAnnouncement = "./job-posting.php?jobTitle=" + encodeURI(jobTitle) + "&jobId=" + jobId;
		searchResult.url = urlToVacancyAnnouncement;
		searchResult.source = "New ATS"; // Just for demonstration.Remove from final.
		searchResult.class = "newAts";
	}

	const _processSearchResults = function() {
		_displayNumberOfSearchResults();
		_displaySearchTerm();
		_normalizeLocationAddresses();
		_constructJobsList(globals.searchResultsJson);

		_constructLocationFilter();
		_constructDepartmentFilter();
		_constructCloseDateFilter();
		_setFilterEventListener();
		_setMapEventListeners();
		_setFilterPanelToggleButtonEventListener();
		_setFilterPanelToggleMenuItemEventListener();
		_setToggleMapInstructionsVisibilityEventListener();
	};
	
	const _setErrorMessage = function(message) {
		const errorMsgContainer = document.querySelector("#errorMsgContainer");
		errorMsgContainer.innerHTML += "<p>" + message + "</p>";
	};

	const _displayNumberOfSearchResults = function() {
		const searchResultCountContainer = document.querySelector("#searchResultCountContainer");
		searchResultCountContainer.innerText = globals.searchResultsJson.length;
	};

	const _displaySearchTerm = function() {
		const searchFormField = document.querySelector("#searchTerm");
		const searchTermContainer = document.querySelector("#searchTermContainer");

		const urlParams = new URLSearchParams(window.location.search);
		const searchTerm = urlParams.get("position");

		searchFormField.value = searchTerm;
		searchTermContainer.innerText = searchTerm;
	};

	const _normalizeLocationAddresses = function() {
		for (const searchResult of globals.searchResultsJson) {
			let location = searchResult.location; // Such as "1234 Main St"
			searchResult.location = _getBaseAddress(location);
		}
	}; 

	const _constructJobsList = function(filteredSearchResultsJson) {
		const noJobs = document.querySelector("#noJobs");
		const jobsListContainer = document.querySelector("#jobsListContainer");
		jobsListContainer.innerHTML = "";

		if(filteredSearchResultsJson.length === 0) {
			noJobs.classList.remove("hidden");
		}
		else {
			noJobs.classList.add("hidden");

			let unorderedList = document.createElement("ul");
			unorderedList.classList.add("ps_grid-body");
			jobsListContainer.appendChild(unorderedList);

			for (const searchResult of filteredSearchResultsJson) {
				_constructJobResult(searchResult, unorderedList);
			}
		}
	};

	const _constructJobResult = function(searchResult, unorderedList) {
		let listItem = document.createElement("li");
		listItem.classList.add("ps_grid-row", "psc_rowact");
		unorderedList.appendChild(listItem);

		let linkToVacancyAnnouncement = document.createElement("a");
		linkToVacancyAnnouncement.href = searchResult.url;
		listItem.appendChild(linkToVacancyAnnouncement);

		_constructSource(searchResult, linkToVacancyAnnouncement); // Just for demonstration.Remove from final.
		_constructJobTitle(searchResult, linkToVacancyAnnouncement);
		_constructJobId(searchResult, linkToVacancyAnnouncement);
		_constructJobLocation(searchResult, linkToVacancyAnnouncement);
		_constructJobDepartment(searchResult, linkToVacancyAnnouncement);
		_constructJobPostedDate(searchResult, linkToVacancyAnnouncement);
		_constructJobCloseDate(searchResult, linkToVacancyAnnouncement);
	};

	// Start: Just for demonstration.Remove from final.
	const _constructSource = function(searchResult, linkToVacancyAnnouncement) {
		const sourceIndicator = document.createElement("span");
		sourceIndicator.classList.add("sourceIndicator", searchResult.class);
		sourceIndicator.innerText = "(" + searchResult.source + ")";
		linkToVacancyAnnouncement.appendChild(sourceIndicator);
	};
	// End: Just for demonstration.Remove from final.
	
	

	const _constructJobTitle = function(searchResult, linkToVacancyAnnouncement) {
		let jobTitle = searchResult.jobTitle;
		let jobTitleRow = _constructJobResultRow("Job Title", jobTitle, ["psc_strong", "psc_margin-left2em", "psc_margin-top0_7em", "psc_margin-bottom0_5em", "psc_nolabel"]);
		linkToVacancyAnnouncement.appendChild(jobTitleRow);
	};

	const _constructJobId = function(searchResult, linkToVacancyAnnouncement) {
		let jobId = searchResult.jobId;
		let jobIdRow = _constructJobResultRow("Job Title", jobId);
		linkToVacancyAnnouncement.appendChild(jobIdRow);
	};

	const _constructJobLocation = function(searchResult, linkToVacancyAnnouncement) {
		let location = searchResult.location;
		let locationRow = _constructJobResultRow("Location", location);
		linkToVacancyAnnouncement.appendChild(locationRow);
	};

	const _constructJobDepartment = function(searchResult, linkToVacancyAnnouncement) {
		let department = searchResult.department;
		let departmentRow = _constructJobResultRow("Department", department);
		linkToVacancyAnnouncement.appendChild(departmentRow);
	};

	const _constructJobPostedDate = function(searchResult, linkToVacancyAnnouncement) {
		let postedDate = searchResult.postedDate;
		let postedDateRow = _constructJobResultRow("Posted Date", postedDate);
		linkToVacancyAnnouncement.appendChild(postedDateRow);
	};

	const _constructJobCloseDate = function(searchResult, linkToVacancyAnnouncement) {
		let closeDate = searchResult.closeDate;
		let closeDateRow = _constructJobResultRow("Close Date", closeDate);
		linkToVacancyAnnouncement.appendChild(closeDateRow);
	};

	const _constructJobResultRow = function(label, value, additionalCSSClasses = []) {
		let divBoxEdit = document.createElement("div");
		divBoxEdit.classList.add("ps_box-edit", "psc_disabled", "psc_has_value");
		for(CSSClass of additionalCSSClasses) {
			divBoxEdit.classList.add(CSSClass);
		}

		let divBoxLabel = document.createElement("div");
		divBoxLabel.classList.add("ps_box-label");
		divBoxEdit.appendChild(divBoxLabel);

		let spanBoxLabel = document.createElement("span");
		spanBoxLabel.classList.add("ps-label");
		spanBoxLabel.innerText = label;
		divBoxLabel.appendChild(spanBoxLabel);

		let spanBoxValue = document.createElement("span");
		spanBoxValue.classList.add("ps_box-value");
		spanBoxValue.innerText = value;
		divBoxEdit.appendChild(spanBoxValue);

		return divBoxEdit;
	}

	const _constructFilterHtml = function(propertyName, instances) {
		const containerForProperty = document.querySelector("#"+ propertyName + "Filter"); // ul
		containerForProperty.innerHTML = "";

		let i = 0;
		for(let uniqueInstance in instances) {
			let instanceListItem = document.createElement("li");
			instanceListItem.classList.add("ps_grid-row");
			containerForProperty.appendChild(instanceListItem);

			let instanceBoxGroup = document.createElement("div");
			instanceBoxGroup.classList.add("ps_box-group", "psc_layout");
			instanceListItem.appendChild(instanceBoxGroup);
		
			let instanceCheckBoxContainer = document.createElement("div");
			instanceCheckBoxContainer.classList.add("ps_box-checkbox", "psc_standard", "psc_margin-top0_2em", "pts_fontweight-normal");
			instanceBoxGroup.appendChild(instanceCheckBoxContainer);

			let instanceBoxControl = document.createElement("div");
			instanceBoxControl.classList.add("ps_box-control");
			instanceCheckBoxContainer.appendChild(instanceBoxControl);

			let instanceCheckBox = document.createElement("input");
			instanceCheckBox.setAttribute("type", "checkbox");
			instanceCheckBox.classList.add("ps-checkbox");
			instanceCheckBox.checked = true;
			instanceCheckBox.value = uniqueInstance;
			instanceCheckBox.name = propertyName;
			instanceCheckBox.setAttribute("checked", true);
			if(propertyName === "location") {
				const baseAddress = _getBaseAddress(uniqueInstance);
				instanceCheckBox.id = _getCheckboxIdFromBaseAddress(baseAddress);
			}
			else {
				instanceCheckBox.id = propertyName + i;
			}
			instanceBoxControl.appendChild(instanceCheckBox);

			let instanceLabelContainer = document.createElement("div");
			instanceLabelContainer.classList.add("ps_box-label", "ps_right");
			instanceCheckBoxContainer.appendChild(instanceLabelContainer);
			
			let instanceLabel = document.createElement("label");
			instanceLabel.classList.add("ps-label");
			instanceLabel.setAttribute("for", propertyName + i);
			instanceLabel.innerText = uniqueInstance + " (" + instances[uniqueInstance] + ")";
			instanceLabelContainer.appendChild(instanceLabel);

			i++;
		}
	};

	const capitalizeFirstLetter = function(string) {
		// Check for apostrophe names like O'Reilly and L'Enfant
		if(string[1] === "'") {
			return string[0].toUpperCase() + "'" + string[2].toUpperCase() + string.slice(3).toLowerCase();
		}

    return string[0].toUpperCase() + string.slice(1).toLowerCase();
	}
	const capitalizeAddress = function(address) {
		var quadrants = ["NE", "SE", "SW", "NW"];
		var addressParts = address.split(" ");
		var capitalizedAddress = "";
		for(part of addressParts) {
			if(!quadrants.includes(part)) {
				capitalizedAddress += capitalizeFirstLetter(part) + " ";
			}
			else {
				capitalizedAddress += part;
			}
		}
		capitalizedAddress = capitalizedAddress.trim();
		return capitalizedAddress;
	};

	const _getBaseAddress = function(rawAddress) {
		let baseAddress = rawAddress.replaceAll("&amp;","&").replaceAll("&#039;", "'").replaceAll(/\.|,|&nbsp/g, ""); // Remove punctuation and html entities
		baseAddress = baseAddress.replace("N.W.", "NW").replace("N.E.", "NE").replace("S.E.", "SE").replace("S.W.", "SW"); // Remove dots from abbreviations
		baseAddress = baseAddress.replace(/(NE|NW|SE|SW) .*$/, "$1"); // Remove anything after the quadrant such as " Suite 123", " 10th Floor" or " (DC Armory)"
		baseAddress = capitalizeAddress(baseAddress);
		baseAddress = baseAddress.replace("Street", "St").replace("Avenue", "Ave").replace("Road", "Rd").replace("Place", "Pl"); // Standardize on types with 2 characters
		return baseAddress
	}

	const _getCheckboxIdFromBaseAddress = function(baseAddress) {
		try {
			return globals.DCGovtWorkplaces[baseAddress].checkBoxId;
		}
		catch(error) {
			console.warn("'"+ baseAddress + "' is not recognized as a DC government workplace. Tell the administrator to add it.")
			return baseAddress.replaceAll(" ", "_");
		}
	};

	const	_constructLocationFilter = function() {
		globals.uniqueLocationsInResults = {};
		for (const searchResult of globals.searchResultsJson) {
			let location = searchResult.location; // Such as "1234 Main St"
			if(globals.uniqueLocationsInResults.hasOwnProperty(location)) {
				globals.uniqueLocationsInResults[location]++;
			}
			else {
				globals.uniqueLocationsInResults[location] = 1;
			}
		}

		_constructFilterHtml("location", globals.uniqueLocationsInResults);
	};

	const	_constructDepartmentFilter = function() {
		const departments = {};

		for (const searchResult of globals.searchResultsJson) {
			let department = searchResult.department; // Such as "Board of Ethics and Government Accou" - yes, truncated
			if(departments.hasOwnProperty(department)) {
				departments[department]++;
			}
			else {
				departments[department] = 1;
			}
		}

		_constructFilterHtml("department", departments);
	};

	const	_constructCloseDateFilter = function() {
		const closeDates = {};
		closeDates.within7Days = 0;
		closeDates.moreThan7Days = 0;

		const now = Date.now(); // milliseconds since 1/1/1970
		globals.sevenDaysFromNow = now + 7*24*60*60*1000;

		for (const searchResult of globals.searchResultsJson) {
			let closeDate = searchResult.closeDate; // Such as "06/30/2024"
			let closeDateMilliseconds = Date.parse(closeDate);
			if(closeDateMilliseconds < globals.sevenDaysFromNow) {
				closeDates.within7Days++;
			}
			else {
				closeDates.moreThan7Days++;
			}
		}

		const labelCloseWithin7Days = document.querySelector("#labelCloseDateWithin7Days");
		labelCloseWithin7Days.innerHTML = "Within 7 days (" + closeDates.within7Days + ")";

		const labelCloseMoreThan7Days = document.querySelector("#labelCloseDateMoreThan7Days");
		labelCloseMoreThan7Days.innerHTML = "More than 7 days (" + closeDates.moreThan7Days + ")";
	};

	const _setFilterEventListener = function() {
		const filterContainer = document.querySelector("#filterContainer");
		filterContainer.addEventListener("change", function(event) {
			const target = event.target;
			if(target.tagName.toLowerCase() === "input") {
				_handleFilterChange();
			}
		});
	};

	const _handleFilterChange = function() {
		let filteredSearchResultsJson = [];
		let keepGoing;
		let location;
		let department;
		let closeDate;
		let filterContainer = document.querySelector("#filterContainer");
		let selectedLocations = filterContainer.querySelectorAll("#locationFilter input:checked");
		let selectedDepartments = filterContainer.querySelectorAll("#departmentFilter input:checked");
		let closeDateWithin7DaysSelected = filterContainer.querySelector("#closeDateWithin7Days:checked") === null ? false : true;
		let closeDateMoreThan7DaysSelected = filterContainer.querySelector("#closeDateMoreThan7Days:checked") === null ? false : true;

		for (let searchResult of globals.searchResultsJson) {
			keepGoing = false;

			// First check for a match among the selected locations
			location = searchResult.location;
			for(let selectedLocation of selectedLocations) {
				if(location === selectedLocation.value) {
					keepGoing = true;
					break;
				}
				keepGoing = false;
			}

			// Now check for a match among selected departments
			if(keepGoing) {
				department = searchResult.department;
				for(let selectedDepartment of selectedDepartments) {
					if(department === selectedDepartment.value) {
						keepGoing = true;
						break;
					}
					keepGoing = false;
				}
			}

			// Now compare closeDate against global variable for 7 days from now.
			if(keepGoing) {
				closeDate = searchResult.closeDate;
				let closeDateMilliseconds = Date.parse(closeDate);
				if((closeDateWithin7DaysSelected && closeDateMilliseconds < globals.sevenDaysFromNow) ||
					 (closeDateMoreThan7DaysSelected && closeDateMilliseconds > globals.sevenDaysFromNow)) {
						// keep going
				}
				else {
					keepGoing = false;
				}
			}

			if(keepGoing) {
				filteredSearchResultsJson.push(searchResult);
			}
		}

		_constructJobsList(filteredSearchResultsJson);
	};

	const _setFilterPanelToggleButtonEventListener = function() {
		const filterPanelToggleBtn = document.querySelector("#PT_PANEL2_BTN");
		filterPanelToggleBtn.addEventListener("click", (event) => {
			event.preventDefault();
			_toggleFilterPanelVisibility();
		});
	}

	const _setFilterPanelToggleMenuItemEventListener = function() {
		const filterPanelToggleMenuItem = document.querySelector("#filterPanelToggle");
		filterPanelToggleMenuItem.addEventListener("click", (event) => {
			event.stopPropagation();
			_toggleFilterPanelVisibility();
		} );
	};

	const _toggleFilterPanelVisibility = function(event) {
		const filterPanelContainer = document.querySelector("#filterPanelContainer");
		if(filterPanelContainer.classList.contains("psc_initial")) {
			filterPanelContainer.classList.remove("psc_initial");
			filterPanelContainer.classList.add("psc_open");
			return;
		}

		filterPanelContainer.classList.toggle("psc_open");
		filterPanelContainer.classList.toggle("psc_closed");
	}

	const _setMapEventListeners = function() {
		const openMapLink = document.querySelector("#openMapLink");
		const closeMapBtn = document.querySelector("#closeMapBtn");
		const updateMapBtn = document.querySelector("#updateMapBtn");
		const overlay = document.querySelector("#overlay");
		const addressInput = document.querySelector("#startAddress");
		const timeInput = document.querySelector("#maxTime");
		const filterMenuListItem = document.querySelector("#filterPanelToggle");

		openMapLink.addEventListener("click", (event) => {
			event.preventDefault();
			overlay.classList.remove("hidden");
			globals.mapOpen = true;

			if(!globals.mapInitialized) {
				_drawMap();
				_callGetCommuterMap();
				_setHeightInStylesheet();
			}

			filterMenuListItem.classList.add("mapOpen");
		});

		closeMapBtn.addEventListener("click", (event) => {
			overlay.classList.add("hidden");
			globals.mapOpen = false;

			filterMenuListItem.classList.remove("mapOpen");
		});

		updateMapBtn.addEventListener("click", (event) => {
			event.preventDefault();
			_handleSearchBtnClick();
		});

		window.addEventListener("resize", function() {
			// The expand/collapse animation relies on the height of #mapInstructions being set in stylesheet so reset on window resize.
			if(globals.mapOpen) {
				_setHeightInStylesheet();
			}
		});

		addressInput.addEventListener("click", () => { addressInput.select(); });
		timeInput.addEventListener("click", () => { timeInput.select(); });
	};

	const _setToggleMapInstructionsVisibilityEventListener = function() {
		const toggleInstructionsBtn = document.querySelector("#toggleInstructions");
		toggleInstructionsBtn.addEventListener("click", toggleMapInstructionsVisibility);
	};

	const toggleMapInstructionsVisibility = function() {
		const mapInstructions = document.querySelector("#mapInstructions");
		mapInstructions.classList.toggle("collapsed");
	};

	const _drawMap = function() {
		map = L.map('map').setView([globals.centerDC.lat, globals.centerDC.long], globals.zoom);

		// Retina displays require different mat tiles quality
		var isRetina = L.Browser.retina;

		var baseUrl = `https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${globals.apiKey}`;
		var retinaUrl = `https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}@2x.png?apiKey=${globals.apiKey}`;

		// Tiles
		L.tileLayer(isRetina ? retinaUrl : baseUrl, {
			attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
			maxZoom: 20,
			id: 'osm-bright',
		}).addTo(map);

		/* I intended to call addWorkLocationsToMap() from the initialize method but there is a scope problem because Leaflet
		is creating a variable 't' and even though 'map' is a global variable, 't' is not so there is an 'undefined' error.
		I also tried passing 'L' that is apparently global but that did not help so recreating that method here to build markerLocations. */
		let workplaceProperties;
		let baseAddress; // Without explanatory text like "(DC Armory)"
		let index = 0;
		let marker;
		let latitude, longitude;

		for(const location in globals.uniqueLocationsInResults) {
			if(globals.uniqueLocationsInResults.hasOwnProperty(location)) {
				baseAddress = _getBaseAddress(location);

				try {
					workplaceProperties = globals.DCGovtWorkplaces[baseAddress];
					latitude = workplaceProperties.lat;
					longitude = workplaceProperties.long;
				} 
				catch (error) {
					console.log("'" + baseAddress + "' not found in list of DC workplaces. No marker placed on map.");
					continue;
				}

				globals.markerLocations[index] = L.marker([latitude, longitude], {title: baseAddress}).addTo(map);
				globals.markerLocations[index]._icon.classList.add("green");
				globals.markerLocations[index]._icon.setAttribute("linkedchkbox", workplaceProperties.checkBoxId);

				globals.markerLocations[index].addEventListener("click", function(evt){
					marker = evt.target;
					marker._icon.classList.toggle("green");
					marker._icon.classList.toggle("red");
					_setCheckboxBasedOnMarkerState(marker);

					// Setting the checkbox dynamically does NOT fire the change event so the jobs list does not update. 
					// This didn't seem to work: const event = new Event("change"); linkedChkBox.dispatchEvent(event);
					_handleFilterChange();
				});

				index++;
			}
		};

		globals.mapInitialized = true;
	};

	const _setCheckboxBasedOnMarkerState = function(marker) {
		let linkedChkBoxID = marker._icon.getAttribute("linkedchkbox");
		let linkedChkBox = document.getElementById(linkedChkBoxID);

		if(marker._icon.classList.contains("green")) {
			linkedChkBox.checked = true;
			linkedChkBox.setAttribute("checked", "checked");
		}
		else {
			linkedChkBox.checked = false;
			linkedChkBox.removeAttribute("checked");
		}
	};

	const _getTransitMode = function() {
		var transitModes = document.getElementsByName("transitMode");
		var selected = Array.from(transitModes).find(radio => radio.checked);
		return selected.value;
	};

	const _getMaxCommuteTime = function() {
		return document.querySelector("#maxTime").value;
	};

	const _getCommuterMap = function(startAddress) {
		warningNotice.classList.add("hidden");
		const encodedAddressText = encodeURI(startAddress);
		var getAddressDetailsUrl = `https://api.geoapify.com/v1/geocode/search?text=${encodedAddressText}&format=json&apiKey=${globals.apiKey}&filter=circle:${globals.centerDC.long},${globals.centerDC.lat},${globals.searchRadius}`;

		fetch(getAddressDetailsUrl, {
			method: 'GET'
		})
		.then(response => {
			if(response.ok) {
				return response.json();
			}
			else {
				_displayErrorMsg("Invalid server response. Status code: " + response.status + " (" + response.statusText +")");
			}
		})
		.then(data => {
				if(data.results && ["building", "amenity"].includes(data.results[0].result_type) && data.results[0].rank.confidence > 0.95) {
					var addressInfo =  {lat: data.results[0].lat, lon: data.results[0].lon, addressLine1: data.results[0].address_line1};
					var transitMode = _getTransitMode();
					var maxCommuteTime = _getMaxCommuteTime();
					_addStartMarkerToMap(addressInfo);
					_addIsochroneToMap(addressInfo, transitMode, maxCommuteTime * 60);
					_handleFilterChange();
				}
				else {
					_displayErrorMsg("Sorry, the address '" + data.query.text + "' was not recognized.");
				}
		})
		.catch((error) => {
			_displayErrorMsg(error);
		});
	};

	const _setMarkerAsInsideOrOutsideSearchArea = function(index, isInside){
		const marker = globals.markerLocations[index];
		marker._icon.classList.add(isInside ? "green" : "red");
		marker._icon.classList.remove(isInside ? "red" : "green");

		_setCheckboxBasedOnMarkerState(marker);
	};

	const _addStartMarkerToMap = function(addressInfo) {
		lastMarker = L.marker([addressInfo.lat, addressInfo.lon], {title: addressInfo.addressLine1}).addTo(map);
	};

	const _addIsochroneToMap = function(addressInfo, transitMode, range) {
		fetch(`https://api.geoapify.com/v1/isoline?lat=${addressInfo.lat}&lon=${addressInfo.lon}&type=time&mode=${transitMode}&range=${range}&apiKey=${globals.apiKey}`)
		.then(data => data.json())
		.then(geoJSONFeatures => {
			// Uncomment for testing
			// geoJSONFeatures = geoJSONFeaturesTestData;

			lastIsochrone = L.geoJSON(geoJSONFeatures, {
				style: (feature) => {
					return {
						stroke: true,
						color: '#9933ff',
						weight: 2,
						opacity: 0.7,
						fill: true,
						fillColor: '#333399',
						fillOpacity: 0.15,
						smoothFactor: 0.5,
						interactive: false
					};
				}
			}).addTo(map);

			_updateWorkLocationMarkers(geoJSONFeatures);
		});
	};

	const _displayErrorMsg = function(errorMsg) {
		const warningNotice = document.querySelector("#warningNotice");
		warningNotice.innerHTML = "<p>" + errorMsg + "</p>";
		warningNotice.classList.remove("hidden");
		window.scrollTo(0, 0);
		return false;
	};

	const _handleSearchBtnClick = function() {
		if(lastMarker) {lastMarker.removeFrom(map);}
		if(lastIsochrone){lastIsochrone.removeFrom(map);}
			
		_callGetCommuterMap(); 

		const mapInstructions = document.querySelector("#mapInstructions");
		mapInstructions.classList.add("collapsed");
	};

	const _callGetCommuterMap = function() {
		var startAddress = document.querySelector("#startAddress").value;
		if(startAddress) {
			_getCommuterMap(startAddress);
		}
		else {
			_displayErrorMsg("Please enter an address");
		}
	};

	// CSS transitions can't go from a fixed value to "auto" so an actual value must be set in the stylesheet 
	// for the height of the mapInstructions box so that the collapse/expand is animated.
	const _setHeightInStylesheet = function() {
		const mapInstructions = document.querySelector("#mapInstructions");
		const stylesheet = document.querySelector("#mapInstructionsHeightStylesheet").sheet;
		const computedStyles = getComputedStyle(mapInstructions);
		const paddingTop = computedStyles.getPropertyValue("padding-top");
		const paddingBottom  = computedStyles.getPropertyValue("padding-bottom");

		if(stylesheet.cssRules.length == 1) {
			stylesheet.deleteRule(0);
		}
		const heightWithoutBorderAndPadding = mapInstructions.offsetHeight - parseInt(paddingTop) - parseInt(paddingBottom);

		const newStyle = ".mapInstructions {height: " + heightWithoutBorderAndPadding + "px;}"
		stylesheet.insertRule(newStyle); 
	};

	const _locationIsInsidePolygon = function(location, polygon) { // Polygon may be an "enclave" within an outer polygon!
		//  From http://alienryderflex.com/polygon/ (written in C, not Javascript)
		//  The function will return true if the point x,y is inside the polygon, or false if it is not.
		let i;
		let j = polygon.length - 1;
		let numberOfNodesIsOdd = false;

		for (i = 0; i < polygon.length; i++) {
		//for (i=0; i<polyCorners; i++) {

			if((polygon[i][1] < location.lat && polygon[j][1] >= location.lat) || 
				 (polygon[j][1] < location.lat && polygon[i][1] >= location.lat)) {

				if(polygon[i][0] + (location.lat - polygon[i][1]) / (polygon[j][1] - polygon[i][1]) * (polygon[j][0] - polygon[i][0]) < location.lon) {
					numberOfNodesIsOdd =! numberOfNodesIsOdd;
				}
			}
			j=i; 
		}

		return numberOfNodesIsOdd;				
	};

	
	const _updateWorkLocationMarkers = function(responseObj) {
		/*
		The response from Geoapify contains features -> geometry -> coordinates which is an array of arrays of arrays.
		The top-level contans one element for each isochrone which is represented by a "blob" on the map.
		However, there can be "enclaves" within blobs that a user can't reach within the travel time specified.
		If there are enclaves, the first of the second-level arrays is the "outer" polygon, then subsequent elements 
		are the "enclaves". If there are no enclaves, there is a single array element at the second level.
		Third-level elements are the longitude/latitude points for each corner of the polygon.
		*/
		const topLevelArrayElements = responseObj.features[0].geometry.coordinates;
		let secondLevelArrayElements;
		let isInsideTravelZone;
		let isInsidePolygon;
		let isEnclave;
		let DCGovtWorkplace;
		let latitude, longitude
		let i=0, j, k;

		for(const location in globals.uniqueLocationsInResults) {
			baseAddress = _getBaseAddress(location);
			try {
				DCGovtWorkplace = globals.DCGovtWorkplaces[baseAddress];
				latitude = DCGovtWorkplace.lat;
				longitude = DCGovtWorkplace.long;
			}
			catch (error) {
				//console.log("As previously noted, '" + location + "' not found.");
				continue;
			}
			markerLocation = {lat: latitude, lon: longitude};

			isInsideTravelZone = false;
			isEnclave = false;

			for(j=0; j < topLevelArrayElements.length; j++) {
				secondLevelArrayElements = topLevelArrayElements[j];
				isEnclave = false;
				//hasEnclaves = secondLevelArrayElements.length == 1 ? false : true;

				for(k=0; k < secondLevelArrayElements.length; k++) {
					if(k > 0) {
						isEnclave = true;
					}
					isInsidePolygon = _locationIsInsidePolygon(markerLocation, secondLevelArrayElements[k]);
					if(isInsidePolygon) {
						if(isEnclave){
							isInsideTravelZone = false;
							break;
						}
						else {
							isInsideTravelZone = true; // May be reset to false if there are enclaves.
						}
					}
				}

				if(isInsideTravelZone) {
					break; // No need to test other isoclines/polygons
				}
			}

			_setMarkerAsInsideOrOutsideSearchArea(i, isInsideTravelZone ? true : false);
			i++;
		}
	};

	const _setSearchButton = function() {
		const searchButton = document.querySelector("#searchButton");
		searchButton.addEventListener("click", event => {
			event.preventDefault();

			const searchFormField = document.querySelector("#searchTerm");
			const searchTerm = searchFormField.value;

			const searchParams = new URLSearchParams(window.location.search);
			searchParams.set("position", searchTerm)
			history.pushState(null, null, "?" + searchParams.toString());

			_getSearchResults();
		});

	};

	const _setMenuButtonEventListener = function() {
		const hamburgerMenuButton = document.querySelector("#menuBtn");

		hamburgerMenuButton.addEventListener("click", (event) => {
			event.stopPropagation();
			hamburgerMenuButton.classList.toggle("active");
		});
	};

	// Close menu if clicking away from button
	const _setCloseMenuEventListener = function() {
		const body = document.querySelector("body");
		const hamburgerMenuButton = document.querySelector("#menuBtn");

		body.addEventListener("click", () => {
			hamburgerMenuButton.classList.remove("active");
		});
	};

	const _initialize = function() {
		_getSearchResults();
		_setSearchButton();
		_setMenuButtonEventListener();
		_setCloseMenuEventListener();
	};

	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Public methods
	return {  
		initialize: _initialize
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.

document.addEventListener("DOMContentLoaded", function(){
	searchResults.initialize();
});