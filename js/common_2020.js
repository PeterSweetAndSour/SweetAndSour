/*
common.js
*/

var SweetAndSour = (function() { 

	// PRIVATE STUFF
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  variables 
	var _level1Selected,
		_level2Selected,
		_lastLevel1Over,
		_lastLevel2Over,
		_level2PreviousSibling;
		
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  methods
	
	// Level 1 
	var _activate1 = function (listItem) {
		if(listItem !== _level1Selected) {
			_level1Selected.classList.add("off");
			listItem.classList.add("on");
			_lastLevel1Over = listItem;
		}
	};
	
	var _restore1 = function () {
		_level1Selected.classList.remove("off");
		if(_lastLevel1Over) {
			_lastLevel1Over.classList.remove("on");
		}
	};
	
	// --------------------------------------------------------------------------
	// Level 2
	var _activate2 = function(listItem) {
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
	
	var _restore2 = function() {
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

	// Styles for mouseover on menu. 
	// This would not be necessary if CSS sibling selector (~) could select preceding as well as following.
	function _handleHoverOverMenu() {
		// Get currently selected level 1 list item
		var imageMenu = document.querySelector("#imageMenu");
		_level1Selected = imageMenu.querySelector("ul.menu1 > li.selected");
	
		// Get the primary (photo) navigation list items.
		var level1ListItems = imageMenu.querySelectorAll("ul.menu1 > li");

		level1ListItems.forEach((level1ListItem) => {
			level1ListItem.addEventListener("click", () => { _activate1(level1ListItem); }, false);
			level1ListItem.addEventListener("mouseover", () => { _activate1(level1ListItem); }, false);
			level1ListItem.addEventListener("mouseout", () => { _restore1(); }, false);
		});

		// Do the same for all second level list items
		_level2Selected = imageMenu.querySelector("ul.menu2 > li.selected");
		var level2ListItems = imageMenu.querySelectorAll("ul.menu2 > li");

		level2ListItems.forEach((level2ListItem) => {
			level2ListItem.addEventListener("click", () => { _activate2(level2ListItem); }, false);
			level2ListItem.addEventListener("mouseover", () => { _activate2(level2ListItem); }, false);
			level2ListItem.addEventListener("mouseout", () => { _restore2(); }, false);
		});
	}
		

		
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	function _displayLoadingMsg() {
		var imageMenu = document.querySelector("#imageMenu");
		var menuLinks = imageMenu.querySelectorAll("a");
		menuLinks.forEach((menuLink) => {
			menuLink.addEventListener("click", () => { 
				document.querySelector("#overlay").style.display = "block";
				document.querySelector("#loading").style.display = "block";
			}, false);
		});	
	}

	// Create the overlay that will appear when a menu link is clicked
	function _createLoadingOverlay() {
		var nav = document.querySelector("nav");
		var overlay = document.createElement("div");
		overlay.id = "overlay";
		nav.appendChild(overlay);
		
		var loading = document.createElement("div");
		loading.classList.add = "loading";
		
		var img = document.createElement("img");
		img.src = "../images/loading_20080830.gif";
		img.setAttribute("alt", "Just a moment ...");
		
		var para = document.createElement("p");
		para.innerHTML = "Just a moment &hellip;";
		
		loading.appendChild(img);
		loading.appendChild(para);
		nav.appendChild(loading);
	}
	

	function _setMenu() {
		_handleHoverOverMenu();
		_displayLoadingMsg();
	}


	
				
	// PUBLIC STUFF
	return {  
		// ------------------------------------------------------------------------------------------------------------------------------------------------
		
		// Initialize page
		initialize : function() {
					
			// Create a "Loading ..." overlay across menu to be activated when menu item is clicked
			if(document.querySelector(".imageMenu")) {
				_createLoadingOverlay();
				//_setMenu();
				_setMobileMenu();
			}
		},
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.


// ------------------------------------------------------------------------------------------------------------------------------------------------
// Initialize the page
// ------------------------------------------------------------------------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function(){
  SweetAndSour.initialize();
});




