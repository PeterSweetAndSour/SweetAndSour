var DesktopNavigation = (function() { 

	// PRIVATE STUFF
	
	// ----------------------------------------------------------------------------------------------------------------
	// Private  variables 
	var _level1Selected,
		_level2Selected,
		_lastLevel1Over,
		_lastLevel2Over,
		_level2PreviousSibling,
		_containingList,
		_containingListItem;
		
	// ----------------------------------------------------------------------------------------------------------------
	// Private  methods

	var _initializeMenuVariables = function(nav) {
		_level1Selected = nav.querySelector("ul.menu1 > li.selected");
		_level2Selected = _level1Selected.querySelector("ul.menu2 > li.selected")
	}
	
	// Level 1 
	var _activate_1 = function (listItem) {
		if(listItem !== _level1Selected) {
			_level1Selected.classList.add("off");
			listItem.classList.add("on");
			_lastLevel1Over = listItem;
		}
	};
	
	var _restore_1 = function () {
		_level1Selected.classList.remove("off");
		if(_lastLevel1Over) {
			_lastLevel1Over.classList.remove("on");
		}
	};
	
	// --------------------------------------------------------------------------
	// Level 2
	var _activate_2 = function(listItem) {
		if(listItem !== _level2Selected) {
			if(_level2Selected) {
				_level2Selected.classList.add("off");
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
		if(_level2Selected) {
			_level2Selected.classList.remove("off");
		}
		if(_lastLevel2Over) {
			_lastLevel2Over.classList.remove("on");
			if(_level2PreviousSibling) {
				_level2PreviousSibling.classList.remove("beforeSelected");
			}
		}
	};

	var _clickOnMenuEventListener = function(evt) {
		var src = evt.target;
		if(src.tagName === "A") {
			_showLoadingGraphic();
			_expandClickedOnOrMousedOverMenuItem(src);
		}
	};

	var _mouseOverMenuEventListener = function(evt) {
		var src = evt.target;
		if(src.tagName === "A") {
			_expandClickedOnOrMousedOverMenuItem(src);
		}
	};
		
	var _mouseOutMenuEventListener = function(evt) {
		var src = evt.target;
		if(src.tagName === "A") {
			_shrinkClickedOnOrMousedOverMenuItem(src);
		}
	};

	var _showLoadingGraphic = function() {
		document.querySelector(".overlay").style.display = "block";
		document.querySelector(".loading").style.display = "block";
	};

	var _expandClickedOnOrMousedOverMenuItem = function(anchor) {
		// Expand the menu item that was clicked or hovered over and shrink the one that was previously selected.
		// This would not be necessary if CSS sibling selector (~) could select preceding as well as following;
		// the currently selected item may be before the one hovered over but there is no way to address it in CSS.
		_containingListItem = anchor.parentNode;
		_containingList = _containingListItem.parentNode;
		if(_containingList.classList.contains("menu1")) {
			_activate_1(_containingListItem);
		}
		if(_containingList.classList.contains("menu2")) {
			_activate_2(_containingListItem);
		}
	};

	var _shrinkClickedOnOrMousedOverMenuItem = function(anchor) {
		_containingListItem = anchor.parentNode;
		_containingList = _containingListItem.parentNode;
		if(_containingList.classList.contains("menu1")) {
			_restore_1(_containingListItem);
		}
		if(_containingList.classList.contains("menu2")) {
			_restore_2(_containingListItem);
		}
	};


	var _setDesktopMenu = function() {
		if(document.querySelector("#imageMenu")) {
			var nav = document.querySelector("nav");
			_initializeMenuVariables(nav);
			nav.addEventListener("click", _clickOnMenuEventListener);
			nav.addEventListener("mouseover", _mouseOverMenuEventListener);
			nav.addEventListener("mouseout", _mouseOutMenuEventListener);
		}
	};

	var _unsetDesktopMenu = function() { // Remove event handlers
		if(document.querySelector("#imageMenu")) {
			var nav = document.querySelector("nav");
			nav.removeEventListener("click", _clickOnMenuEventListener);
			nav.removeEventListener("mouseover", _mouseOverMenuEventListener);
			nav.removeEventListener("mouseout", _mouseOutMenuEventListener);
		}
	};

	
				
	// ----------------------------------------------------------------------------------------------------------------
	// PUBLIC STUFF
	return {  
		setDesktopMenu: _setDesktopMenu,
		unsetDesktopMenu: _unsetDesktopMenu
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.





