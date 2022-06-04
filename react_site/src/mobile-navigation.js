/*jshint unused:false*/

var MobileNavigation = (function() {
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  variables 
	var _menuToggle,
		_menuLabel,
		_nav,
		_overlay,			
		_lastRadioBtnSelected = null,
		_lastLabelSelected = null;

	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  methods

	var _getListItemsInBranch = function(nodeInNav) {
		var listItems = [];
		while(nodeInNav.tagName !== "NAV") {
			if(nodeInNav.tagName === "LI") {
				listItems.push(nodeInNav);
			}
			nodeInNav = nodeInNav.parentNode;
		}
		return listItems;
	};

	var _resetNavElements = function(listItems) {
		var radioBtn;
		listItems.forEach((listItem) => {
			radioBtn = listItem.querySelector("input");
			radioBtn.checked = false;

			var labelSelected = listItem.querySelector("label");
			labelSelected.classList.remove("checked");
		});
	};

	var _toggleMobileMenuDisplay = function() {
		if(_menuToggle.checked) { // User has opened the menu
			_menuLabel.classList.add("touched");
			_nav.setAttribute("aria-hidden", "false");
		}
		else {
			_nav.setAttribute("aria-hidden", "true");
			
			if(_lastRadioBtnSelected){
				var listItemsForLastSelection = _getListItemsInBranch(_lastRadioBtnSelected.closest("li"));
				_resetNavElements(listItemsForLastSelection);
			}
		}
	};

	var _closeMobileMenuOnOverlayClick = function() {
		var menuToggleCtrl = document.querySelector("#menuToggle");
		menuToggleCtrl.checked = false;
		_toggleMobileMenuDisplay();
	};

	// If previously selected label/radio button precedes the current one in the DOM, the visible position of the current label 
	// will change when the previous list one collapses so scroll so that user still has finger over the label they touched.			
	var _bringSelectedMenuItemBackIntoPosition = function(evt, radioBtnSelected, labelSelected){
		var radioBtnsInNav = _nav.querySelectorAll("li > input");
		var radioBtn;
		
		for(var i=0; i < radioBtnsInNav.length; i++) {
			radioBtn = radioBtnsInNav[i];
			if(radioBtn === radioBtnSelected) {
				break; // No need to do anything - selected label above the one previously selected
			}
			else if(_lastRadioBtnSelected && radioBtn === _lastRadioBtnSelected) {
				var clickedPointAtY = evt.pageY;
				var labelBoundingInfo = labelSelected.getBoundingClientRect();
				var midPointOfLabelNowAtY = labelBoundingInfo.y + labelBoundingInfo.height/2 + window.scrollY;
				window.scrollBy(0, midPointOfLabelNowAtY - clickedPointAtY);
			}
		}
	};

	var _menuEventListener = function(evt) {
		var src = evt.target;
		if(src.tagName === "INPUT") {
			var radioBtnSelected = src;

			// Get associated label and all radio buttons
			var labelSelected = radioBtnSelected.closest("li").querySelector("label");

			if(labelSelected.classList.contains("checked")) { //radioBtnSelected.checked will always be true
				radioBtnSelected.checked = false; // This will collapse menu if same menu item selected second time
				labelSelected.classList.remove("checked");
			}
			else {
				radioBtnSelected.checked = true; // should be anyway
				labelSelected.classList.add("checked");

				// Now remove "checked" class on LIs in previous branch and 
				// ensure radio buttons in that branch, not in this branch, are unchecked.
				if(_lastRadioBtnSelected && radioBtnSelected !== _lastRadioBtnSelected) {
					var listItemsForLastSelection = [],
						listItemsForSelection = [],
						listItemLastSelected = _lastRadioBtnSelected.closest("li"),
						listItemSelected = radioBtnSelected.closest("li");

					listItemsForLastSelection = _getListItemsInBranch(listItemLastSelected);
					listItemsForSelection = _getListItemsInBranch(listItemSelected);

					// Elements in previous heirarchy that are not in the current one
					var difference = listItemsForLastSelection.filter(x => !listItemsForSelection.includes(x));
					_resetNavElements(difference);
				}
			}

			_bringSelectedMenuItemBackIntoPosition(evt, radioBtnSelected, labelSelected);

			_lastRadioBtnSelected = radioBtnSelected;
			_lastLabelSelected = labelSelected;
		}
	};

	var _setMobileMenu = function() {
		// Set event listener on the button to open/close the menu
		_menuToggle = document.querySelector("#menuToggle");
		_menuLabel = _menuToggle.parentNode.querySelector("label");
		_nav = _menuToggle.parentNode.querySelector("nav");

		_menuToggle.addEventListener("click", _toggleMobileMenuDisplay);

		// TIL that clicking on a label creates a click event on the label AND THEN a separate click event on the associated input.
		// Knowing that, look for the event on the radio button since if changes are made on the 1st event, they may be undone by 2nd.
		_nav.addEventListener("click", _menuEventListener);

		// Also allow the menu to be closed by clicking on the overlay.actionRow
		_overlay = document.querySelector("#menuOverlay");
		_overlay.addEventListener("click", _closeMobileMenuOnOverlayClick);
	};

	var _unsetMobileMenu = function() {
		_menuToggle.removeEventListener("click", _toggleMobileMenuDisplay);
		_nav.removeEventListener("click", _menuEventListener);
		_overlay.removeEventListener("click", _toggleMobileMenuDisplay);
	};

	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Public methods
	return {  
		setMobileMenu: _setMobileMenu,
		unsetMobileMenu: _unsetMobileMenu,
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.

export default MobileNavigation;