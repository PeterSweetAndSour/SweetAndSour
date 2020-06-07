let DesktopNavigation = (function() { 

	// PRIVATE STUFF
	
	// ----------------------------------------------------------------------------------------------------------------
	// Private  variables 
	let _level1SelectedListItem,
		_level2SelectedListItem,
		_level1And2ListItems,

		_lastLevel1Over,
		_lastLevel2Over,
		_level2PreviousSibling,
		_containingList,
		_containingListItem;
		
	// ----------------------------------------------------------------------------------------------------------------
	// Private  methods

	var _initializeMenuVariables = function(nav) {
		_level1SelectedListItem = nav.querySelector("ul.menu1 > li.selected");
		_level2SelectedListItem = _level1SelectedListItem.querySelector("ul.menu2 > li.selected");
		_level1And2ListItems = nav.querySelectorAll("ul.menu1 > li, ul.menu1 > li.selected > ul > li");
	}
	
	// Level 1 
	var _activate_1 = function (listItem) {
		if(listItem !== _level1SelectedListItem) {
			_level1SelectedListItem.classList.add("off");
			listItem.classList.add("on");
			_lastLevel1Over = listItem;
		}
	};
	
	var _restore_1 = function () {
		_level1SelectedListItem.classList.remove("off");
		if(_lastLevel1Over) {
			_lastLevel1Over.classList.remove("on");
		}
	};
	
	// --------------------------------------------------------------------------
	// Level 2
	var _activate_2 = function(listItem) {
		if(listItem !== _level2SelectedListItem) {
			if(_level2SelectedListItem) {
				_level2SelectedListItem.classList.add("off");
			}		
			
			listItem.classList.add("on");
			_lastLevel2Over = listItem;
			_level2PreviousSibling = listItem.previousElementSibling;
			if(_level2PreviousSibling) {
				_level2PreviousSibling.classList.add("beforeSelected");
			}
		}
	};
	
	var _restore_2 = function() {
		if(_level2SelectedListItem) {
			_level2SelectedListItem.classList.remove("off");
		}
		if(_lastLevel2Over) {
			_lastLevel2Over.classList.remove("on");
			if(_level2PreviousSibling) {
				_level2PreviousSibling.classList.remove("beforeSelected");
			}
		}
	};

	var _clickOnMenuEventListener = function(evt) {
		var listItem = evt.target;
		if(listItem === _level1SelectedListItem || listItem === _level1SelectedListItem) {
			evt.preventDefault();
			return;
		}
		
		_showLoadingGraphic(listItem);
		_expandClickedOnOrMousedOverMenuItem(listItem);

	};

	var _mouseOverMenuEventListener = function(evt) {
		var src = evt.target;
		_expandClickedOnOrMousedOverMenuItem(src);
	};
		
	var _mouseOutMenuEventListener = function(evt) {
		var src = evt.target;
		_shrinkExpandedMenuItem(src);
	};

	var _showLoadingGraphic = function() {
		document.querySelector(".overlay").style.display = "block";
		document.querySelector(".loading").style.display = "block";
	};

	var _expandClickedOnOrMousedOverMenuItem = function(listItem) {
		// For top level menu, expand the menu item that was clicked or hovered over and shrink the one that was previously selected.
		// This would not be necessary if CSS sibling selector (~) could select preceding as well as following;
		// the currently selected item may be before the one hovered over but there is no way to address it in CSS.
		containingList = listItem.parentNode;
		if(containingList.classList.contains("menu1")) {
			_activate_1(listItem);
		}
		if(containingList.classList.contains("menu2")) {
			_activate_2(listItem);
		}
	};

	var _shrinkExpandedMenuItem = function(listItem) {
		containingList = listItem.parentNode;
		if(containingList.classList.contains("menu1")) {
			_restore_1(listItem);
		}
		if(containingList.classList.contains("menu2")) {
			_restore_2(listItem);
		}
	};


	var _setDesktopMenu = function() {
		if(document.querySelector("#imageMenu")) {
			var nav = document.querySelector("nav");
			_initializeMenuVariables(nav);

			_level1And2ListItems.forEach((listItem) => {
				listItem.addEventListener("click", _clickOnMenuEventListener);
				listItem.addEventListener("mouseenter", _mouseOverMenuEventListener);
				listItem.addEventListener("mouseleave", _mouseOutMenuEventListener);
			});
		}
	};

	var _unsetDesktopMenu = function() { // Remove event handlers
		_level1And2ListItems.forEach((listItem) => {
			listItem.removeEventListener("click", _clickOnMenuEventListener);
			listItem.removeEventListener("mouseenter", _mouseOverMenuEventListener);
			listItem.removeEventListener("mouseleave", _mouseOutMenuEventListener);
		});
	};

	
				
	// ----------------------------------------------------------------------------------------------------------------
	// PUBLIC STUFF
	return {  
		setDesktopMenu: _setDesktopMenu,
		unsetDesktopMenu: _unsetDesktopMenu
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.





