/*
common.js

YUI Compressor command:
C:\Internet\WWWRoot\YUICompressor\build>
	java -jar yuicompressor-2.4.2.jar C:\Internet\WWWRoot\sweetAndSour.org\js\common_yyyymmdd.js -o C:\Internet\WWWRoot\sweetAndSour.org\js\common_yyyymmdd.min.js 
*/

var SweetAndSour = (function() { 

	// PRIVATE STUFF
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  variables 
	var lastLevel1Over = null;
	var level1Selected = null;

	var lastLevel2Over = null;
	var level2Selected = null;

	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  methods
	
	
	// Create the overlay that will appear when a menu link is clicked
	function _createLoadingOverlay() {
		var overlay = document.createElement("div");
		overlay.id = "overlay";
		$("menuWrapper").appendChild(overlay);
		
		var loading = document.createElement("div");
		loading.id = "loading";
		
		var img = document.createElement("img");
		img.src = "../images/loading_20080830.gif";
		img.setAttribute("alt", "Just a moment ...");
		
		var para = document.createElement("p");
		para.innerHTML = "Just a moment &hellip;";
		
		loading.appendChild(img);
		loading.appendChild(para);
		$("menuWrapper").appendChild(loading);
	}
	
	function _setMenu() {
		_displayLoadingMsg();
		_handleHoverOverMenu();
	}
	
	function _displayLoadingMsg() {
		var aMenuLinks = $("imageMenu").getElements("a");
		aMenuLinks.each(
			function(item, index) {
				item.addEventListener("click", 
					function(){
						$("overlay").style.display = "block";
						$("loading").style.display = "block";
					}
				);
			}
		);
	}
	
	// Split this up
	function _handleHoverOverMenu() {
		// Get currently selected level 1 list item
		level1Selected = $("imageMenu").getFirst("ul").getElement("li.selected"); 
		
		// Get the primary (photo) navigation list items. Note that we had to tell ImageMenu not to cancel event or this won't fire
		var level1ListItems = $("imageMenu").getFirst("ul").getChildren(); 
		level1ListItems.each(
			function (listItem1, index) {
				listItem1.addEvent("mouseover", activate1.bind(this, listItem1));
				listItem1.addEvent("mouseout", restore1.bind(this, listItem1));
				listItem1.addEvent("click", activate1.bind(this, listItem1));
			},
			this
		);
		
		// Do the same for all second level list items
		// Get currently selected level 2 list item - if there is a level 2 menu
		level2Selected = level1Selected.getElement("li.selected");

		var level2Lists = $$("ul.menu2");
		var level2ListItems = [];
		level2Lists.each(
			function(list) {
				level2ListItems.append(list.getChildren("li"));
			}
		);
		
		level2ListItems.each(
			function (listItem2, index) {
				listItem2.addEvent("mouseover", activate2.bind(this, listItem2));
				listItem2.addEvent("mouseout", restore2.bind(this, listItem2));
				listItem2.addEvent("click", activate2.bind(this, listItem2));
			},
			this
		);
	}
			
	// --------------------------------------------------------------------------
	// Level 1 
	var activate1 = function (listItem) {
		if(listItem != level1Selected) {
			level1Selected.addClass("off");
			listItem.addClass("on");
			lastLevel1Over = listItem;
		}
	};
	
	var restore1 = function () {
		level1Selected.removeClass("off");
		if(lastLevel1Over) {
			lastLevel1Over.removeClass("on");
		}
	};
	
	// --------------------------------------------------------------------------
	// Level 2
	var activate2 = function(listItem) {
		if(listItem != level2Selected) {
			if(level2Selected) {
				level2Selected.addClass("off");
			}		
			
			listItem.addClass("on");
			lastLevel2Over = listItem;
			level2Previous = listItem.getPrevious("li");
			if(level2Previous) {
				level2Previous.addClass("beforeSelected");
			}
		}
	};
	
	var restore2 = function() {
		if(level2Selected) {
			level2Selected.removeClass("off");
		}
		if(lastLevel2Over) {
			lastLevel2Over.removeClass("on");
			if(level2Previous) {
				level2Previous.removeClass("beforeSelected");
				level2Previous = null;
			}
		}
	};

		

	
				
	// PUBLIC STUFF
	return {  
		// ------------------------------------------------------------------------------------------------------------------------------------------------
		
		// Initialize page
		initialize : function() {
					
			// Create a "Loading ..." overlay across menu to be activated when menu item is clicked
			if($("imageMenu")) {
				_createLoadingOverlay();
				_setMenu();
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




