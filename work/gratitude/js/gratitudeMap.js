import { places } from "./gratitudeMapData.js";
import { legendHTML } from "./legendHTML.js?version=5";


var gratitudeDC = (function() {
	
	const mapCenter = [38.890541, -77.008753];
	const initialZoom = 12;
	let map, mapContainer, placeInfo, photoContainer, displayNameContainer, quoteContainer, photo, closeButton, introText;
	let leafletMarkerObjects = [], selectedMarker, markersLayer, learnMoreContainer, learnMoreLink, caption, titleToIdLookup = {};
	let indexOfPlaceInJSONData;

  const _fixDOM = function(){
		// Deal with things that Drupal gives us even if we don't want them.
    const regionContent = document.querySelector("#region-content");
    if(regionContent) {
      regionContent.classList.remove("grid-12");
      regionContent.classList.remove("region-content");
      regionContent.setAttribute("style", "");
      regionContent.id = "";
    }

    const pageTitle = document.querySelector("#page-title");
    if(pageTitle) {
      pageTitle.classList.add("hidden");
    }

    const body = document.querySelector("body");
    body.classList.remove("not-front");

		// While we are here, set overflow:hidden on body EXCEPT if in the editor or it becomes unusable.
		const toolbar = document.querySelector("#toolbar");
		if(!toolbar) {
			const body = document.querySelector("body");
			body.style.overflow = "hidden";
		}
  };

  const _setGlobalVars = function() {
		mapContainer = document.querySelector("#mapContainer");
    placeInfo = document.querySelector("#placeInfo");
    photoContainer = document.querySelector("#placeInfo > .photoContainer");
    displayNameContainer = document.querySelector("#placeInfo .displayName");
    quoteContainer = document.querySelector("#placeInfo > .quoteContainer");
		learnMoreContainer = document.querySelector("#placeInfo > .learnMoreContainer");
		learnMoreLink = document.querySelector("#learnMoreLink");
    closeButton = document.querySelector("#closeButton");
    introText = document.querySelector("#introText");
    photo = photoContainer.querySelector("img"); 
		caption = photoContainer.querySelector("p")
  };
	
	const _isMobile = function() {
			return window.orientation > -1;
	}
  
	const _setHeights = function() {
		let usableHeight = window.innerHeight;
		mapContainer.style.height = (usableHeight - 80) + "px";
		placeInfo.style.height = (usableHeight - 128) + "px";
	};
	
	const _setOnResize = function() {
		window.addEventListener("resize", () => {
			_setHeights();
		});
	};

	const _setMap = function() {
		map = L.map("mapContainer").setView(mapCenter, initialZoom);
		
		L.tileLayer(
			"https://tile.openstreetmap.org/{z}/{x}/{y}.png", 
			{
				maxZoom: 19,
				attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a>"
			}
		).addTo(map);

		map.on("click", _hidePlaceInfo);
	};

	const _setSearch = function() {
		markersLayer = new L.LayerGroup();	//layer contain searched elements
	
		map.addLayer(markersLayer);

		let isMobile = window.innerWidth < 960;
	
		var searchControl = new L.Control.Search({
			container: isMobile ? "searchContainer" : "",
			position: isMobile ? "" : "topright",
			layer: markersLayer,
			initial: false,
			collapsed: true,
			zoom: 15,
			hideMarkerOnCollapse: true,
			textErr: "Location not found<br>(Search by name, not address, so<br>&ldquo;White House&rdquo;, not 1600 Penn&hellip;"
		});
	
		map.addControl( searchControl );

		// Make search and cancel buttons accessible
		_makeSearchAndCancelButtonsAccessible();

		searchControl.on("search:locationfound", function(evt) {
			indexOfPlaceInJSONData = titleToIdLookup[evt.text];

			_ensureMarkerCategoryIsEnabledOnLocationFound();

			// Display the information about this place
			_displayPlaceInfo(indexOfPlaceInJSONData, true);

			//Open the tooltip for the marker
			_openDesignatedPopup(indexOfPlaceInJSONData);
		}).on("search:cancel", function(evt) {
			_hidePlaceInfo();
			_closeDesignatedPopup(indexOfPlaceInJSONData);
			_removeHighlightCircle();
			_removeQueryString();
		});
	};

	const _makeSearchAndCancelButtonsAccessible = function() {
		const clearSearchButton = document.querySelector(".search-cancel");
		clearSearchButton.setAttribute("role", "button");
		clearSearchButton.setAttribute("aria-label", "Clear search input");

		const searchButton = document.querySelector(".search-button");
		searchButton.setAttribute("role", "button");
		searchButton.setAttribute("aria-label", "Open search control/initiate search button");
	};

	const _ensureMarkerCategoryIsEnabledOnLocationFound = function() {
		//Ensure the category to which this marker belongs is enabled or marker will be hidden.
		//Ensure that the checkbox is checked. If not, check it and post a notification.
		const categoryId = places[indexOfPlaceInJSONData].categoryId;
		const categoryName = places[indexOfPlaceInJSONData].categoryName;
		const chkBoxThisCategory = document.querySelectorAll("#legend > form > fieldset > div > input")[categoryId];
		if(!chkBoxThisCategory.checked) {
			chkBoxThisCategory.checked = true;

			let markerPins = document.querySelectorAll(".marker-pin");
			var placeCategory;
			markerPins.forEach((markerPin) => {
				placeCategory = markerPin.children[0].dataset.categoryid;
				if(Number(placeCategory) === categoryId) {
					markerPin.classList.remove("hidden");
				}
			});
			alert("All locations in the \"" + categoryName + "\" category will now be displayed");
		}
	};
	
	const _removeHighlightCircle = function() {
		//haven't figured this out yet
	};

	const _removeQueryString = function() {
		history.pushState({}, "", window.location.pathname);
	}

	const _setMarkers = function() {
		let place, markerPin;
		let icons = [];
			icons[1]  = {"class": "fa-cross", "desc": "Churches"};
			icons[2]  = {"class": "fa-heart", "desc": "Community-based organizations"};
			icons[3]  = {"class": "fa-landmark", "desc": "Cultural institution"};
			icons[4]  = {"class": "fa-landmark-flag", "desc": "DC government"};
			icons[5]  = {"class": "fa-leaf", "desc": "Farmers' market and community gardens"};
			icons[6]  = {"class": "fa-volleyball", "desc": "Gyms and recreation centers"};
			icons[7]  = {"class": "fa-book", "desc": "Libraries"};
			icons[8]  = {"class": "fa-shop", "desc": "Local businesses"};
			icons[9]  = {"class": "fa-house-medical", "desc": "Medical care facilities"};
			icons[10] = {"class": "fa-person-walking", "desc": "Parks and trails"};
			icons[11] = {"class": "fa-utensils", "desc": "Restaurants and grocery stores"};
			icons[12] = {"class": "fa-person-chalkboard", "desc": "Schools"};

		for(let i=0; i < places.length; i++) {
			place = places[i];
			let numberRange = null;
			if(place.numberOfCitations == 1) {
				numberRange = 1;
			}
			else if (place.numberOfCitations == 2) {
				numberRange = 2;
			}
			else if (place.numberOfCitations <= 4) {
				numberRange = 3;
			}
			else {
				numberRange = 4;
			}

			markerPin = L.divIcon({
				className: "marker-pin marker-pin-range-" + numberRange,
				iconSize: [31, 52],
				iconAnchor: [10, 44],
				popupAnchor: [3, -40],
				/* bgPos: [5, 6], */
				html: "<i class=\"fa-solid " + icons[place.categoryId].class + "\" data-categoryid=\"" + place.categoryId + "\" aria-label=\"" + icons[place.categoryId].desc + "\"></i>"
			});
			
			let leafletMarker = new L.marker(
				place.latitudeLongitude, 
				{
					"arrayIndex": i,
					"title": place.displayName,
					"aria-label": place.displayName,
					"icon": markerPin,
					"categoryId": place.categoryId
				}
			).on("click", _displayPlaceInfoOnClick).on("keypress", _displayPlaceInfoOnKeyPress).addTo(map).bindPopup(place.displayName);

			leafletMarkerObjects.push(leafletMarker);
			titleToIdLookup[place.displayName] = leafletMarkerObjects.length - 1;
			markersLayer.addLayer(leafletMarker);
		};
	};
  
	const _createLegend = function() {
		// The obvious place to create the Legend is in .leafletControlContainer but need to get it at top of DOM
		const mapContainer = document.querySelector("#mapContainer");
	
		let legend = document.createElement("div");
		legend.id = "legend";
		legend.classList.add("legend", "hidden");
		legend.innerHTML = legendHTML; // exported from legendHTML.js

		const firstChildOfMapContainer = mapContainer.firstChild;
		mapContainer.insertBefore(legend, firstChildOfMapContainer);

		legend.addEventListener("click", (event) => {
			// Prevent event listener set in _setEventOnMap from closing legend if it is open
			event.stopPropagation(); 
		});
	
		_createOpenLegendButton();
		_setEventsOnLegendCheckboxes();
		_setEventOnMap();
	};

	const _createOpenLegendButton = function() {
		// The obvious place to create the button to open the legend is in .leaflet-bottom.leaflet-left but want
		// it to be the very first thing that a keyboard-only user tabs to so they can open the legend and filter.
		// I tried tabindex. https://stackoverflow.com/questions/78173004/tabindex-not-behaving-as-expected-what-am-i-doing-wrong
		const legend = document.querySelector("#legend");
		const mapContainer = document.querySelector("#mapContainer");

		let openLegendBtn = document.createElement("button");
		openLegendBtn.classList.add("open-legend-btn");
		openLegendBtn.id = "open-legend-btn";
		openLegendBtn.innerHTML = "Legend";

		mapContainer.insertBefore(openLegendBtn, legend);

		openLegendBtn.addEventListener("click", (event) => {
			const legend = document.querySelector("#legend");
			legend.classList.toggle("hidden");
			_avoidOverlapOnLegendDisplay();
			
			// Prevent event listener set in _setEventOnMap from closing legend if it is open
			event.stopPropagation();
		});
	};

	const _avoidOverlapOnLegendDisplay = function() {
			// Hide the alternateH1 and zoom controls when legend is displayed
			const alternateH1 = document.querySelector(".alternateH1");
			const zoomCtrlsContainer = document.querySelector(".leaflet-left");
		
			if(legend.classList.contains("hidden")) {
				alternateH1.classList.remove("hidden");
				zoomCtrlsContainer.classList.remove("hidden");
			}
			else {
				alternateH1.classList.add("hidden");
				zoomCtrlsContainer.classList.add("hidden");
			}
	}


	const _setEventsOnLegendCheckboxes = function() {
		const checkboxes = document.querySelectorAll(".legend > form > fieldset > div > input");
		checkboxes.forEach((checkbox) => {
			checkbox.addEventListener('click', (event) => {
				_handleCheckboxClick(event);
			});
		});
	};

	const _setEventOnMap = function() {
		const mapContainer = document.querySelector("#mapContainer");
		mapContainer.addEventListener("click", (event) => {
			/* If target is the close button on a marker popup
			   - reset tabindex back to zero on all the marker pins
				 - hide place info
				 - remove the query string
				 - clear and close the search control if open
				  */
			let target = event.target;
			if(target.classList.contains("leaflet-popup-close-button") || 
			  target.parentNode.classList.contains("leaflet-popup-close-button")) {
				const markerPins = document.querySelectorAll(".marker-pin");
				markerPins.forEach((markerPin) => {
						markerPin.setAttribute("tabindex", 0);
				});

				_hidePlaceInfo();
				_removeQueryString();
				_clearAndCloseSearchCtrl();
			}

			// Close the legend if it is open
			const legend = document.querySelector("#legend");
			legend.classList.add("hidden");
			_avoidOverlapOnLegendDisplay();
		});
	};

	const _clearAndCloseSearchCtrl = function() {
		const cancelButton = document.querySelector("a.search-cancel");
		cancelButton.click();
		const searchButton = document.querySelector("a.search-button");
		searchButton.click();
	};

	const _handleCheckboxClick = function(event) {
		const selectedCheckbox = event.target;
		const category = selectedCheckbox.value;
		const allCheckboxes =  document.querySelectorAll('input[name="category"]');

		if(category === "0") { // Select/Deselect all
			allCheckboxes.forEach((checkbox) => {
				checkbox.checked = selectedCheckbox.checked;
			});
			let allCheckboxLabel = document.querySelector("#all-checkbox-label");
			allCheckboxLabel.innerHTML = selectedCheckbox.checked ? "Deselect All" : "Select All";
		}

		// Show or hide the markers as appropriate
		let selectedCheckboxes = document.querySelectorAll('input[name="category"]:checked');
		let values = [];
		selectedCheckboxes.forEach((checkbox) => {
				values.push(checkbox.value);
		});
		
		let placeCategory;

		let markerPins = document.querySelectorAll(".marker-pin");
		markerPins.forEach((markerPin) => {
			placeCategory = markerPin.children[0].dataset.categoryid;
			if(values.includes(placeCategory)) {
				markerPin.classList.remove("hidden");
			}
			else {
				markerPin.classList.add("hidden");
			}
		});
	};

	const _setClosePlaceButton = function() { /* Mobile only so no keyboard */
    closeButton.addEventListener("click", (event) => {
      event.preventDefault();
      introText.classList.add("hidden");
      placeInfo.classList.add("hidden");
			photo.setAttribute("src", "https://innovate.dc.gov/sites/default/files/dc/sites/MOPI/page_content/images/pixel.png");
			_removeQueryString();
			
    })
		closeButton.focus();
  };

	const _displayPlaceInfoOnClick = function(event) {
		selectedMarker = event.target;
		indexOfPlaceInJSONData = selectedMarker.options.arrayIndex;
		_displayPlaceInfo(indexOfPlaceInJSONData, true);
	};

	const _displayPlaceInfoOnKeyPress = function(event) {
		const key = event.code || event.originalEvent.code; // I don't recall ever needing originalEvent before.
		if (key !== "Enter" && key !== "Space") {
			return;
		}

		selectedMarker = event.target;
		
		const indexOfPlaceInJSONData = selectedMarker.options.arrayIndex;
		_displayPlaceInfo(indexOfPlaceInJSONData, true);

		// Reset tabindex on all other markers so user can get to Learn More link in #PlaceInfo panel etc.
		const markerPins = Array.from(document.querySelectorAll(".marker-pin"));
		const indexSelectedMarker = markerPins.indexOf(selectedMarker._icon);
		markerPins.splice(indexSelectedMarker, 1);
		markerPins.forEach((markerPin) => {
				markerPin.setAttribute("tabindex", -1);
		});
	};

	const _displayPlaceInfoOnLoad = function() {
		const queryString = window.location.search;
		if(!queryString) {
			return;
		}

		const params = new URLSearchParams(queryString);
		const index = params.get("id");
		if(!index) {
			return;
		}

		_displayPlaceInfo(index, true, true);
		_openDesignatedPopup(index);
	};

	const _displayPlaceInfoOnForwardOrBack = function(index) {
		_displayPlaceInfo(index, false);
		_openDesignatedPopup(index);
	};

	const _openDesignatedPopup = function(index) {
		let leafletMarker = leafletMarkerObjects[index];
		if(leafletMarker) {
			leafletMarker.openPopup();
		}
	};

	const _closeDesignatedPopup = function(index) {
		let leafletMarker = leafletMarkerObjects[index];
		if(leafletMarker) {
			leafletMarker.closePopup();
		}
	};

	const _displayPlaceInfo = function(index, updateHistory, retainIntroText = false) {
		let place = places[index];
		if(!place) {
			return;
		}
    
		placeInfo.classList.remove("hidden");
		introText.classList.add("hidden");
		if(!retainIntroText) {
			introText.classList.add("hidden");
		}

		photo.setAttribute("src", place.photoUrl);
		photo.setAttribute("alt", place.altText);
		photoContainer.classList.remove("hidden");

		if(place.caption) {
			caption.innerHTML = place.caption;
			caption.classList.remove("hidden");
		}
		else {
			caption.classList.add("hidden");
		}

		displayNameContainer.classList.remove("hidden");
		displayNameContainer.innerHTML = place.displayName;

		quoteContainer.classList.remove("hidden");
		quoteContainer.innerHTML = place.quote;

		if(place.website) {
			learnMoreContainer.classList.remove("hidden");
			learnMoreLink.setAttribute("href", place.website);
			learnMoreLink.innerHTML = place.website;
		}
		else {
			learnMoreContainer.classList.add("hidden");
		}

		closeButton.focus();

		//Update visible URL
		if(updateHistory) {
			history.pushState(index, "", "?id=" + index + "&name=" + place.displayName.replaceAll(" ", "-"));
		}
	};


	const _hidePlaceInfo = function() {
		photoContainer.classList.add("hidden");
		photo.setAttribute("alt", "Placeholder image that will be replaced when location is selected.");
		displayNameContainer.classList.add("hidden");
		quoteContainer.classList.add("hidden");
		learnMoreContainer.classList.add("hidden");
	};


	const _initialize = function() {
		_fixDOM();
		_setGlobalVars(); 
		if(_isMobile()) {
			_setHeights();
			_setOnResize();
		}
		_setMap();
		_setSearch();
		_setMarkers();
		_setClosePlaceButton();
		_createLegend();
		_displayPlaceInfoOnLoad();

		// Handle forward/back buttons
		window.addEventListener("popstate", (event) => {
			// If a state has been provided, we update the current page.
			if (event.state) {
				_displayPlaceInfoOnForwardOrBack(event.state);
			}
		});
};

	// --------------------------------------------------------------------------
	// Public methods
	return {  
		initialize : _initialize
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.


document.addEventListener("DOMContentLoaded", () => {
	gratitudeDC.initialize();
});