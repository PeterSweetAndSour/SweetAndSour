/*
common.js

This is a "self-executing" function that will:
1. run once
2. keep private the things outside the return{} block (these all begin with underscore "_")
3. namespace everything so that conflicts with other Javascript should be avoided.

YUI Compressor command:
C:\Internet\WWWRoot\YUICompressor\build>
	java -jar yuicompressor-2.4.2.jar C:\Internet\WWWRoot\sweetAndSour.org\js\common_yyyymmdd.js -o C:\Internet\WWWRoot\sweetAndSour.org\js\common_yyyymmdd.min.js 
*/

var SweetAndSour = (function() { 

	// PRIVATE STUFF
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  variables 
	var _dhtmlWin;
	var _clientArea;
	var _apparentTop = 0;
	var _apparentLeft = 0;
	var _lastSetTimeoutID = 0;
		

	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private  methods
	
	// Attempts to cut a string between words as close as possible to given character position.
	// Optional string tail can be appended if the text is trimmed.
	// Optional value searchwidth can be declared to control how fuzzy the cut will be.
	function _trimTextBack(str, chars, tail, searchWidth) {
		if (str.length > chars){
			if (!tail){	
				tail = " ...";
			}
		
			if(!searchWidth) {
				searchWidth = 12;
			}
			
			//Truncate at max character length less length of required tail
			var trimStr = str.substring(0, chars - tail.length);
			
			//Step backward one character at a time up to searchWidth looking for whitespace	
			var testChar;
			var index;
			for (var i=0; i < searchWidth; i++){
				if(i < trimStr.length) {
					index = trimStr.length - i;
					testChar = trimStr.charAt(index);
					if (/\s/i.test(testChar)){
						return trimStr.substring(0, index) + tail;
					}
				}
			}
			
			//Didn't find any whitespace so return truncated 
			return trimStr + tail;
		} 
		else {
			//Return the original string
			return str;
		}
	}
	
	//Get window scroll position
	function _winGetScroll() {
		var scrollLeft = 0;
		var scrollTop = 0;
		if (self.pageYOffset) { // all except Explorer i.e. Firefox etc.
			scrollLeft = self.pageXOffset;
			scrollTop = self.pageYOffset;
		}
		else if (document.documentElement && document.documentElement.scrollTop) {
			// Explorer 6 Strict
			scrollLeft = document.documentElement.scrollLeft;
			scrollTop = document.documentElement.scrollTop;
		}
		else if (document.body) { // all other Explorers
			scrollLeft = document.body.scrollLeft;
			scrollTop = document.body.scrollTop;
		}
		//console.log("_winGetScroll x: " + scrollLeft + ", y: " + scrollTop);
		return ({x: scrollLeft, y: scrollTop});
	}

	
	//Get position of any object on the page
	function _objFindPos(obj) {
		var curLeft = 0;
		var curTop = 0;
		if (obj && obj.offsetParent) {
			curLeft = obj.offsetLeft;
			curTop = obj.offsetTop;
			while (obj.offsetParent !== null) {
				obj = obj.offsetParent;
				curLeft += obj.offsetLeft;
				curTop += obj.offsetTop;
			}
		}
		return ({x:curLeft, y:curTop});
	}
	
	function _getCookieValue(key) {
		var keyIndex = document.cookie.indexOf(key);
		if(keyIndex == -1) {
			return null;
		}
		else {
			var startIndex = keyIndex + key.length + 1;
			var endIndex = document.cookie.indexOf(";", startIndex + 1);
			if(endIndex == -1) {
				endIndex = document.cookie.length;
			}
			var cookieValue = document.cookie.substring(startIndex, endIndex);
			//console.log("get cookieValue for " + key + ": " + cookieValue);
			return cookieValue;
		}
	}

	// ------------------------------------------------------------------------------------------------------------------------------------------------

	// When user scrolls the real window, move the fake window accordingly - 2018-04-24: could now use position:fixed
	function _onScroll() {
		//Cancel any previous setTimeout
		window.clearTimeout(_lastSetTimeoutID);
		_lastSetTimeoutID = window.setTimeout(_adjustToScroll, 200);
	}
	
	function _adjustToScroll() {
		var fakeWinPos = _objFindPos(_dhtmlWin);
		var currentLeft = fakeWinPos.x;
		var currentTop = fakeWinPos.y;

		var realWinScroll = _winGetScroll();
		var requiredLeft = Number(realWinScroll.x) + Number(_apparentLeft);
		var requiredTop = Number(realWinScroll.y) + Number(_apparentTop);
		//console.log("currentLeft: " + currentLeft + ", currentTop: " + currentTop + ", realWinScroll.x: " + realWinScroll.x + ", realWinScroll.y: " + realWinScroll.y + ", requiredLeft: " + requiredLeft + ", requiredTop: " + requiredTop);
			
		var moveLeft = (requiredLeft - currentLeft);
		var moveTop = (requiredTop - currentTop);
		//console.log("=> moveLeft: " + moveLeft + ", moveTop: " + moveTop);
		
		if(moveLeft !== 0 || moveTop !== 0) {
			_dhtmlWin.style.left = (currentLeft + moveLeft) + "px";
			_dhtmlWin.style.top = (currentTop + moveTop) + "px";
		}
	}
	
	
	// When page loads, this function is run to initialize apparentTop/Left based on cookie data if available
	function _setWindowPosition() {
		// Determine if a cookie has been set for the dhtml window position
		var windowPositionCookie = _getCookieValue("windowPosition");
		var realWinScroll = _winGetScroll();
		if(windowPositionCookie !== null) {
			_apparentLeft = windowPositionCookie.split(":")[0];
			_apparentTop = windowPositionCookie.split(":")[1];
		}
		else {
			var fakeWinPos = _objFindPos(_dhtmlWin);
			_apparentLeft = fakeWinPos.x - realWinScroll.x;
			_apparentTop = fakeWinPos.y - realWinScroll.y;
		}
		//console.log("apparent position: " + _apparentLeft + "," + _apparentTop);
		_dhtmlWin.style.left = Number(realWinScroll.x) + Number(_apparentLeft) + "px";
		_dhtmlWin.style.top  = Number(realWinScroll.y) + Number(_apparentTop) + "px";
	}

	// When window is dragged, stores the new apparent position (in the visible screen, not from top of page)
	function _saveWindowPosition() {
		var fakeWinPos = _objFindPos(_dhtmlWin);
		var realWinScroll = _winGetScroll();
		_apparentLeft = fakeWinPos.x - realWinScroll.x;
		_apparentTop = fakeWinPos.y - realWinScroll.y;
		
		// Scrolling before the page finishes loading can result in window being off the page so ...
		if(_apparentTop < 10) {
			//console.log("resetting apparentTop");
			_apparentTop = 10;
		}
		
		// Put this in a cookie to be available on next page load
		var expires = new Date();
		expires.setFullYear(expires.getFullYear() + 1);
		var cookieValue = "windowPosition=" + _apparentLeft + ":" + _apparentTop + "; expires= " + expires.toGMTString() + "; path=/";
		//console.log("_saveWindowPosition: " + cookieValue);
		document.cookie = cookieValue;
	}
	
	function _saveWindowSize() {
		var wFrame = _dhtmlWin.offsetWidth;
		var clientArea = _dhtmlWin.getElement("div.clientArea");
		var hClientArea = clientArea.offsetHeight;
		
		// Opera not resizing the nested DIVs properly so let's help
		_dhtmlWin.style.height = "auto";
		clientArea.style.width = "auto";
		
		var expires = new Date();
		expires.setFullYear(expires.getFullYear() + 1);
		
		var cookieValue = "windowSize=" + wFrame + ":" + hClientArea + "; expires= " + expires.toGMTString() + "; path=/";
		//console.log("_saveWindowSize: " + cookieValue);
		document.cookie = cookieValue;
	}
	
	
	// When page loads, this function is run to initialize window size based on cookie data if available
	function _setWindowSize() {
		var clientArea = _dhtmlWin.getElement("div.clientArea");
		
		// Determine if a cookie has been set for the dhtml window size
		// Set width of outer container and allow inner container to fill it;
		// set height of inner container and allow outer container to stetch around it.
		_dhtmlWin.style.height = "auto";
		clientArea.style.width = "auto";
		var windowSizeCookie = _getCookieValue("windowSize");
		if(windowSizeCookie !== null) {
			_dhtmlWin.style.width = windowSizeCookie.split(":")[0] + "px";
			clientArea.style.height = windowSizeCookie.split(":")[1] + "px";
		}
		else if(screen.availWidth < 1000) { // If monitor is small, reset photo frame size
			_dhtmlWin.style.width = "760px";
			clientArea.style.height = "455px";
		}	
	}
	
	// Reset size of (transparent) drag bars to match size of _dhtmlWin
	function _resetDragBars() {
		var dragBarLeft = _dhtmlWin.getElement("div.dragBarLeft");
		var dragBarBottom = _dhtmlWin.getElement("div.dragBarBottom");
		var dragBarRight = _dhtmlWin.getElement("div.dragBarRight");

		var size = _dhtmlWin.getSize();
		dragBarLeft.style.height = (size.y - 4) + "px";
		dragBarBottom.style.width = (size.x - 4) + "px";
		dragBarRight.style.height = (size.y - 4) + "px";
	}
	
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
	
	
	
	// Insert a break after every fifth photo in a photo album
	function _alignPhotos(photoAlbums) {
		var photos, numPhotos;
		photoAlbums.each(
			function(album){
				photos = album.getElements("div.photoBox");
				numPhotos = photos.length;
				photos.each(
					function(photo, index) {
						if(((index + 1)%5 === 0) && (index != numPhotos - 1)) {
							var brElement  = new Element('br', {clear: 'all'});
							brElement.inject(photo, 'after');
						}
					}
				);
			}
		);
	}
	
	function _toggleCodeDisplay(event) {
		var oLink = event.target;
		if(oLink.innerHTML.indexOf("Show") === 0) {
			oLink.innerHTML = oLink.innerHTML.replace(/Show/, "Hide");
			oLink.parentNode.parentNode.getElement("div.listing").style.display = "block";
		}
		else {
			oLink.innerHTML = oLink.innerHTML.replace(/Hide/, "Show");
			oLink.parentNode.parentNode.getElement("div.listing").style.display = "none";
		}
	}
		
	// -----------------------------------------------------------------------------
	// Load an external script and call the callback function when loaded
	// http://www.nczonline.net/blog/2009/06/23/loading-javascript-without-blocking/
	// -----------------------------------------------------------------------------
	function _loadScript(url, callback) {
		var script = document.createElement("script");
		script.type = "text/javascript";

		if (script.readyState){  //IE
			script.onreadystatechange = function(){
				if (script.readyState == "loaded" || script.readyState == "complete"){
					script.onreadystatechange = null;
					callback();
				}
			};
		} 
		else {  //Others
			script.onload = function(){
				callback();
			};
		}

		script.src = url;
		document.body.appendChild(script);
	}
	
	// Make window draggable and associate function to save the window position when done
	function _makePhotoWindowDraggable() {
		var dragByTitleBar = new Drag(
			"photoFrame", 
			{
				handle: $("photoFrame").getElement("div.titleBar"),
				onComplete: _saveWindowPosition
			}
		);
	}
	
	function _makePhotoWindowResizable() {
		_makePhotoWindowResizableFromTop();
		_makePhotoWindowResizableFromRight();
		_makePhotoWindowResizableFromBottom();
		_makePhotoWindowResizableFromLeft();
		_makePhotoWindowResizableFromSECornerHandle();
	}

	function _makePhotoWindowResizableFromTop() {
		// Drag bar TOP - only need to resize inner box since outer will shrink/expand vertically around it
		var resizeTop = _clientArea.makeResizable(
			{
				handle:$("photoFrame").getElement("div.dragBarTop"),
				modifiers: {x: null, y:'height'},
				invert: true,
				onComplete: function() {_saveWindowSize(); _resetDragBars();}
			}
		);
		
		// As the TOP drag bar is moved, need to simultaneously move the window (bottom edge will appear fixed and title bar pushed down)
		var dragTop = new Drag(
			"photoFrame", 
			{
				handle: $("photoFrame").getElement("div.dragBarTop"),
				modifiers: {x: null, y: 'top'},
				onComplete: _saveWindowPosition
			}
		);
	}

	function _makePhotoWindowResizableFromRight() {
		// Drag bar RIGHT - only need to resize outer box since inner will shrink/expand horizontally to fill it
		var resizeRight = _dhtmlWin.makeResizable(
			{
				handle:$("photoFrame").getElement("div.dragBarRight"),
				modifiers: {x: 'width', y: null},
				onComplete: function() {_saveWindowSize(); _resetDragBars();}
			}
		);
	}

	function _makePhotoWindowResizableFromBottom() {
		// Drag bar BOTTOM - only need to resize inner box since outer will shrink/expand vertically around it
		var resizeBottom = _clientArea.makeResizable(
			{
				handle:$("photoFrame").getElement("div.dragBarBottom"),
				modifiers: {x: null, y:'height'},
				onComplete: function() {_saveWindowSize(); _resetDragBars();}
			}
		);
	}

	function _makePhotoWindowResizableFromLeft() {
		// Drag bar LEFT - only need to resize outer box since inner will shrink/expand horizontally to fill it
		var resizeLeft = _dhtmlWin.makeResizable(
			{
				handle:$("photoFrame").getElement("div.dragBarLeft"),
				modifiers: {x: 'width', y: null},
				invert: true,
				onComplete: function() {_saveWindowSize(); _resetDragBars();}
			}
		);
		// As the LEFT drag bar is moved, need to simultaneously move the window (right edge will appear fixed)
		var dragLeft = new Drag(
			"photoFrame", 
			{
				handle: $("photoFrame").getElement("div.dragBarLeft"),
				modifiers: {x: 'left', y: null},
				onComplete: _saveWindowPosition
			}
		);
	}

	function _makePhotoWindowResizableFromSECornerHandle() {
		// Make window resizable by dragging the handle at the lower-right (change both the photo frame AND content area)
		var resizeSECornerOuter = _dhtmlWin.makeResizable(
			{
				handle:$("photoFrame").getElement("img.dragHandle"),
				onComplete: function() {_saveWindowSize(); _resetDragBars();}
			}
		);
		var resizeSECornerInner = _clientArea.makeResizable(
			{
				handle:$("photoFrame").getElement("img.dragHandle")
			}
		);
	}
	
	function _initializePhotoWindowCloseButtion() {
			// Make close button on popup "window" clickable. 
			var btnClose = _dhtmlWin.getElement("div.titleBar a");
			btnClose.addEvent("mousedown", function() {
					btnClose.className = "btnDown"; 
					_dhtmlWin.style.display = "none"; 
					
					//Clear the window when it is closed.
					$('photoContent').set('text', ''); 
					
					window.setTimeout(function(){ btnClose.className = ""; }, 1000); 
					
					// Make search box visible again
					//$("search").style.visibility = "visible";
				}
			);
			btnClose.addEvent(
				"mouseup", 
				function() { btnClose.className = ""; }
			);
	}
	
				
	// PUBLIC STUFF
	return {  
		// ------------------------------------------------------------------------------------------------------------------------------------------------
		
		// Initialize page
		initialize : function() {
			_dhtmlWin = $("photoFrame");
			_clientArea = _dhtmlWin.getElement("div.clientArea");
			
			// If a cookie for fake window position was set, reset the position
			_setWindowPosition();
			
			// If a cookie for fake window size was set, reset the position
			_setWindowSize();
			
			_makePhotoWindowDraggable();
			_makePhotoWindowResizable();
			
			_initializePhotoWindowCloseButtion();

					
			// Create a "Loading ..." overlay across menu to be activated when menu item is clicked
			if($("imageMenu")) {
				_createLoadingOverlay();
				_setMenu();
			}
			
			// If this page has a lot of photos set in div class="photoAlbum", run script to align photos
			var photoAlbums = $$("div.photoAlbum");
			if(photoAlbums.length > 0) {
				_alignPhotos(photoAlbums);
			}
			
		},
		
		
		// ---------------------------------------------------------------------
		// Display photos
		// ---------------------------------------------------------------------
		// Function to open a fake window and load something into it.
		ajaxPopup : function(ajaxUrl, windowTitle) {
			// Ensure that the window is clear in case it wasn't closed earlier.
			$('photoContent').set('text', ''); 
						
			// Get the photo and main caption and insert in hidden photo frame
			var ajaxRequest = new Request.HTML(
				{
					url: ajaxUrl,
					onSuccess: function(responseText) {
						// Inject the new DOM elements into the results div.
						$('photoContent').adopt(responseText);
					},
					onFailure: function() {
						$('photoContent').set('text', 'The request failed.');
					}
				}
			);
			ajaxRequest.send();
			
			//Set dhtml window title using the thumbnail caption
			$("photoFrame").getElement("span.titleBarText").innerHTML = unescape(windowTitle);
			
			// Make window visible
			$("photoFrame").style.display = "block";
			
			// Resize the (transparent) drag bars
			_resetDragBars();
			
			// Temporarily hide the search box or the form elements will be visible through the dhtml window
			//$("search").style.visibility = "hidden";
		},
		
		// Function to open up a window to view photos from thumbnails.
		getPhoto : function(photoName, thumbnailCaption, panorama) {
			var screenW = screen.availWidth;
			
			//If user has a really small monitor or window, just open a new window.
			if(screenW < 800) {
				var screenH = screen.availHeight;
				var newURL = "../imageMgt/index.php?fuseAction=showPhotoAndCaption&photoName=" + photoName + (panorama ? "&panorama=true" : "");
				var newWindow = window.open(newURL, 'newWindow', 'width=' + screenW + ',height=' + screenH + ',left=0,top=0,dependant=yes,directories=no,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,titlebar=yes,toolbar=no');
				newWindow.focus();
			}
			else {
				var ajaxUrl = "../imageMgt/index.php?fuseAction=showPhotoAndCaptionAjax&photoName=" + photoName + (panorama ? "&panorama=true" : "");
				var windowTitle = _trimTextBack(thumbnailCaption, 60, " ...", 10);
				this.ajaxPopup(ajaxUrl, windowTitle);
			}
		},
		
		popup : function(anchorOrURL, w, h, features) {
			if(!features) {
				features = "resizable=yes,scrollbars=yes";
			}
			var l = (screen.width - w) / 2;
			var t = (screen.height - h) / 2;
			features += ((features) ? "," : "") + 'width=' + w + ',height=' + h + ',left=' + l + ',top=' + t;

			var url;
			if(typeof(anchorOrURL) == "string") {
				url = anchorOrURL;
			}
			else if(typeof(anchorOrURL) == "object" && anchorOrURL.tagName.toLowerCase() == "a") {
				url = anchorOrURL.href;
			}

			if(url) {
				if(win) { // Reuse window if possible
					win.location.href = url;
				}
				else {
					win = window.open(url, "win", features);
				}
				win.focus();
				return false;
			}
		},
		
		// Look for DIVs with class="codeBlock"; make the H4 inside a link to toggle the code display; hide the code
		prepareCodeDisplay : function() {
			var aCodeBlocks = $$("div.codeBlock");
			aCodeBlocks.each(function(oCodeBlock, index) {
				var oHeader = oCodeBlock.getElement("h4");
				var oLink = new Element('a', {
					 'href': '#',
					 'html': 'Show ' + oHeader.innerHTML,
					 'events': {
						  'click': function(event){
								_toggleCodeDisplay(event);
								event.stop();
						  }
					 }
				});
				oHeader.innerHTML = "";
				oHeader.appendChild(oLink);
				 
				 var oListing = oCodeBlock.getElement("div.listing");
				 oListing.style.display = "none";
			});
		}
		
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.


// ------------------------------------------------------------------------------------------------------------------------------------------------
// Initialize the page
// ------------------------------------------------------------------------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function(){
  SweetAndSour.initialize();
});




