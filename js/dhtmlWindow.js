/* 
This DHTML window is based on something written by Mike Hall of brainjar.com that
doesn't appear to exist on that site any more. It has been extended to include "maximize"
and rewritten to use (and avoid conflict with) MooTools.

This is a "self-executing" function that will:
1. run once
2. keep private the things outside the return{} block (these all begin with underscore "_")
3. namespace everything so that conflicts with other Javascript should be avoided.

Not using Class from MooTools, at least not yet.
*/

var DHTMLWin = (function() { 

	// PRIVATE STUFF
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Private member variables for the window
	var i;
	var _mapList;
	var _mapName;

	//  General properties
	var _maxzIndex                        =   0;
	var _resizeCornerSize                 =  16;
	var _minimizedTextWidth               = 100;
	var _inactiveFrameBackgroundColor     = "#c0c0c0";
	var _inactiveFrameBorderColor         = "#f0f0f0 #505050 #404040 #e0e0e0";
	var _inactiveTitleBarColor            = "#808080";
	var _inactiveTitleTextColor           = "#c0c0c0";
	var _inactiveClientAreaBorderColor    = "#404040 #e0e0e0 #f0f0f0 #505050";
	var _inactiveClientAreaScrollbarColor = "";
	var _inMoveDrag                       = false;
	var _inResizeDrag                     = false;
	var _initialTop                       = 5;
	var _initialLeft                      = 5;
	var _loadingContent = '<div class="loading"><img src="../imageMgt/loading.gif" alt="loading ..." /></div>';
	var _active = null;  //reference to the currently active window object. Set with winMakeActive();
	var _lastScrollTime = 0;
	var _lastSetTimeoutID = 0;
		
	// Window components
	var _frame;
	var _titleBar;
	var _titleBarText;
	var _titleBarButtonsMaximize;
	var _titleBarButtonsRestore;
	var _clientArea;
	var _mapMaximize;
	var _mapRestore;
	
	// Colors.
	var _activeFrameBackgroundColor;
	var _activeFrameBorderColor;
	var _activeTitleBarColor;
	var _activeTitleBarBGImage;
	var _activeTitleTextColor;
	var _activeClientAreaBorderColor;
	var _activeClientAreaScrollbarColor;

	// Images.
	var _activeButtonsImageMaximize;
	var _inactiveButtonsImageMaximize;
	var _activeButtonsImageRestore;
	var _inactiveButtonsImageRestore;
	
	// Flags.
	var _isOpen      = false;
	var _isMinimized = false;
	var _isMaximized = false;
	
	// Browser
	var _browser;
	
	// Window position and size
	var _apparentTop = _initialTop;
	var _apparentLeft = _initialLeft;
	var _minimumHeight;
	var _minimumWidth;
	var _xOffset;
	var _yOffset;
	var _oldLeft;
	var _oldTop;
	var _oldWidth;
	var _oldHeight;
	
	// Window widths
	var _initWd;               // initial width
	var _w;                    // 
	var _dw;                   // 
	var _widthDiff;            // 
	var _clipTextMinimumWidth; // 
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Window Methods 
	
	function _open() {
		if (_isOpen) {
			if (_isMinimized) {
				_restore();
			}
			return;
		}

		// Restore the window and make it visible.
		_makeActive();
		_isOpen = true;
		_frame.style.display = "block";

		// Set position
		var realWinScroll = winGetScroll();
		_frame.style.top  = (realWinScroll.y + _apparentTop) + "px";
		_frame.style.left = (realWinScroll.x + _apparentLeft) + "px";
	}
	
	
	function _close() {
		// Hide the window.
		//this.frame.style.visibility = "hidden";
		_frame.style.display = "none";
		_isOpen = false;
		_clientArea.innerHTML = _loadingContent; 
	}
	
	
	function _maximize() {
		if (!_isOpen || _isMaximized) {
			return;
		}
		
		_makeActive();

		// Save current frame and title bar text size and position.
		//this.restoreFrameTop = this.frame.offsetTop;
		//this.restoreFrameLeft = this.frame.offsetLeft;
		_restoreFrameWidth = _frame.offsetWidth;
		_restoreTextWidth = _titleBarText.offsetWidth;
		_restoreClientWidth = _clientArea.offsetWidth; //this shouldn't be necessary
		_restoreClientHeight = _clientArea.offsetHeight;
		_restoreApparentTop = _apparentTop;
		_restoreApparentLeft = _apparentLeft;

		//Maximize
		var realWinScroll = winGetScroll();
		_frame.style.top = (realWinScroll.y + 5) + "px";
		_frame.style.left = "5px";
		var size = winGetSize();
		_frame.style.width = (size.x - 20) + "px";
		_clientArea.style.height = (size.y - _titleBar.offsetHeight - 40) + "px"; //frame will wrap around it
		_clientArea.style.display = "block"; //for IE - not expanding to fill available width
		_clientArea.style.width = "auto";    //for IE - not expanding to fill available width
		_apparentTop = 5;
		_apparentLeft = 5;

		// Set buttons
		_setButtonsRestore();

		_isMaximized = true;
	}
	
	
	function _minimize() {
		if (!_isOpen || _isMinimized) {
			return;
		}
		_makeActive();

		// Save current frame and title bar text widths.
		_restoreFrameWidth = _frame.offsetWidth;
		_restoreTextWidth = _titleBarText.offsetWidth;
		_restoreClientWidth = _clientArea.offsetWidth;
		_restoreClientHeight = _clientArea.offsetHeight;
		_restoreApparentTop = _apparentTop;
		_restoreApparentLeft = _apparentLeft;

		// Disable client area display.
		_clientArea.style.display = "none";

		// Minimize frame and title bar text widths.
		if (_minimumWidth) {
			_frame.style.width = _minimumWidth + "px";
		}
		else {
			_frame.style.width = "";
		}
		_titleBarText.style.width = _minimizedTextWidth + "px";

		// Set buttons
		_setButtonsRestore();
	}
	
	
	// Return to normal size (not maximized or minimized)
	function _restore() {
		// Return if window is not open, or not currently minimized or maximized
		if(!_isOpen || (!_isMinimized && !_isMaximized)) {
			return;
		}
		_makeActive();

		// Enable client area display.
		_clientArea.style.display = "block";

		// Restore frame and title bar text widths.
		_frame.style.width = _restoreFrameWidth + "px";
		_titleBarText.style.width = _restoreTextWidth + "px";
		_clientArea.style.height = _restoreClientHeight + "px";
		_clientArea.style.width = "auto"; //for IE

		//Reset apparent position
		_apparentTop = _restoreApparentTop;
		_apparentLeft = _restoreApparentLeft;

		// Restore frame position
		var realWinScroll = winGetScroll();
		_frame.style.top = (realWinScroll.y + _apparentTop) + "px";
		_frame.style.left = (realWinScroll.x + _apparentLeft) + "px";

		// Set buttons
		_setButtonsMaximize();

		_isMinimized = false;
		_isMaximized = false;
	}
	
	
	function _makeActive() {
		if (_active == this) {
			return;
		}
		// Make currently active window inactive

		if (_active) {
			_frame.style.backgroundColor    = _inactiveFrameBackgroundColor;
			_frame.style.borderColor        = _inactiveFrameBorderColor;
			_titleBar.style.backgroundColor = _inactiveTitleBarColor;
			_titleBar.style.backgroundImage = "none";
			_titleBar.style.color           = _inactiveTitleTextColor;
			_clientArea.style.borderColor   = _inactiveClientAreaBorderColor;
			if (_browser.isIE) {
				_clientArea.style.scrollbarBaseColor = _inactiveClientAreaScrollbarColor;
			}
			if (_browser.isNS && _browser.version < 6.1) {
				_clientArea.style.overflow = "hidden";
			}
			if (_active.inactiveButtonsImageMaximize) {
				_titleBarButtonsMaximize.src = _active.inactiveButtonsImageMaximize;
			}
			if (_active.inactiveButtonsImageRestore) {
				_titleBarButtonsRestore.src = _active.inactiveButtonsImageRestore;
			}
		}

		// Activate this window.
		_frame.style.backgroundColor    = _activeFrameBackgroundColor;
		_frame.style.borderColor        = _activeFrameBorderColor;
		_titleBar.style.backgroundColor = _activeTitleBarColor;
		_titleBar.style.backgroundImage = _activeTitleBarBGImage;
		_titleBar.style.color           = _activeTitleTextColor;
		_clientArea.style.borderColor   = _activeClientAreaBorderColor;
		
		if (_browser.isIE) {
			_clientArea.style.scrollbarBaseColor = _activeClientAreaScrollbarColor;
		}
		if (_browser.isNS && _browser.version < 6.1) {
			_clientArea.style.overflow = "auto";
		}
		if (_inactiveButtonsImageMaximize) {
			_titleBarButtonsMaximize.src = _activeButtonsImageMaximize;
		}
		if (_inactiveButtonsImageRestore) {
			_titleBarButtonsRestore.src = _activeButtonsImageRestore;
		}
		_frame.style.zIndex = ++_maxzIndex;

		_active = this;
	}
	
	
	function _setButtonsMaximize() {
		_titleBarButtonsMaximize.style.display = "inline";
		_titleBarButtonsRestore.style.display = "none";
	}
	
	
	function _setButtonsRestore() {
		_titleBarButtonsMaximize.style.display = "none";
		_titleBarButtonsRestore.style.display = "inline";
	}
	
	
	function _adjustToScroll() {
		var fakeWinPos = objFindPos(_frame);
		var currentLeft = fakeWinPos.x;
		var currentTop = fakeWinPos.y;

		var realWinScroll = winGetScroll();
		var requiredLeft = realWinScroll.x + _apparentLeft;
		var requiredTop = realWinScroll.y + _apparentTop;
			
		var moveLeft = (requiredLeft - currentLeft);
		var moveTop = (requiredTop - currentTop);
		
		if(moveLeft != 0 || moveTop != 0) {
			_frame.style.left = (currentLeft + moveLeft) + "px";
			_frame.style.top = (currentTop + moveTop) + "px";
		}
	}
	
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Event handlers
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	
	function _winClientAreaClick(event) {
	  // Make this window the active one.
	  _frame.makeActive();
	}
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Drag - this needs a complete rewrite to use MooTools Drag instead
	function _winMoveDragStart(event) {
		var target;
		var x, y;

		if (_browser.isIE) {
			target = window.event.srcElement.tagName;
		}
		if (_browser.isNS) {
			target = event.target.tagName;
		}

		if (target == "AREA") {
			return;
		}

		_frame.makeActive();

		// Get cursor offset from window frame.
		if (browser.isIE) {
			x = window.event.x;
			y = window.event.y;
		}
		if (browser.isNS) {
			x = event.pageX;
			y = event.pageY;
		}
		_xOffset = _frame.offsetLeft - x;
		_yOffset = _frame.offsetTop  - y;

		// Set document to capture mousemove and mouseup events.
		/*
		if (browser.isIE) {
			document.onmousemove = winMoveDragGo;
			document.onmouseup   = winMoveDragStop;
		}
		if (browser.isNS) {
			document.addEventListener("mousemove", winMoveDragGo,   true);
			document.addEventListener("mouseup",   winMoveDragStop, true);
			event.preventDefault();
		}
		*/
		document.addEvent("mousemove", _winMoveDragGo);
		document.addEvent("mouseup", _winMoveDragStop);

		_frame.inMoveDrag = true;
	}
	
	
	function winMoveDragGo(event) {

		var x, y;

		if (!winCtrl.inMoveDrag) {
			return;
		}
		
		// Get cursor position.
		if (browser.isIE) {
			x = window.event.x;
			y = window.event.y;
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}
		if (browser.isNS) {
			x = event.pageX;
			y = event.pageY;
			event.preventDefault();
		}

		// Move window frame based on offset from cursor.
		_frame.style.left = (x + _xOffset) + "px";
		_frame.style.top  = (y + _yOffset) + "px";
	}

	
	function winMoveDragStop(event) {

		_inMoveDrag = false;

		// Remove mousemove and mouseup event captures on document.
		/*
		if (browser.isIE) {
			document.onmousemove = null;
			document.onmouseup   = null;
		}
		if (browser.isNS) {
			document.removeEventListener("mousemove", winMoveDragGo, true);
			document.removeEventListener("mouseup", winMoveDragStop, true);
		}
		*/
		document.removeEvent("mousemove", _winMoveDragGo);
		document.removeEvent("mouseup", _winMoveDragStop);

		//Note final position of fake window relative to the real window
		var fakeWinPos = objFindPos(_frame);
		var realWinScroll = winGetScroll();
		_apparentTop = fakeWinPos.y - realWinScroll.y;
		_apparentLeft = fakeWinPos.x - realWinScroll.x;
	}


	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Resize
	function _winResizeCursorSet(event) {
		var target;
		var xOff, yOff;

		if (_isMinimized || _inResizeDrag) {
			return;
		}
		
		// If not on window frame, restore cursor and exit.
		if (_browser.isIE) {
			target = window.event.srcElement;
		}
		if (_browser.isNS) {
			target = event.target;
		}
		if (target != _frame) {
			return;
		}
		// Find resize direction.
		if (_browser.isIE) {
			xOff = window.event.offsetX;
			yOff = window.event.offsetY;
		}
		if (_browser.isNS) {
			xOff = event.layerX;
			yOff = event.layerY;
		}
		
		var _resizeDirection = ""
		if (yOff <= _resizeCornerSize) {
			_resizeDirection += "n";
		}
		else if (yOff >= _frame.offsetHeight - _resizeCornerSize) {
			_resizeDirection += "s";
		}
		if (xOff <= _resizeCornerSize) {
			_resizeDirection += "w";
		}
		else if (xOff >= _frame.offsetWidth - _resizeCornerSize) {
			_resizeDirection += "e";
		}

		// If not on window edge, restore cursor and exit.
		if (_resizeDirection == "") {
			this.onmouseout(event);
			return;
		}

		// Change cursor.
		if (_browser.isIE) {
			document.body.style.cursor = _resizeDirection + "-resize";
		}
		if (_browser.isNS) {
			_frame.style.cursor = _resizeDirection + "-resize";
		}
	}
	
	function _winResizeCursorRestore(event) {
		if (_inResizeDrag) {
			return;
		}
		// Restore cursor.
		if (_browser.isIE) {
			document.body.style.cursor = "";
		}
		if (_browser.isNS) {
			_frame.style.cursor = "";
		}
	}
	
	function _winResizeDragStart() {
		var target;

		// Make sure the event is on the window frame.
		if (_browser.isIE) {
			target = window.event.srcElement;
		}
		if (_browser.isNS) {
			target = event.target;
		}
		if (target != _frame) {
			return;
		}
		_makeActive();

		if (_isMinimized) {
			return;
		}
		
		// Save cursor position.
		if (browser.isIE) {
			_xPosition = window.event.x;
			_yPosition = window.event.y;
		}
		if (browser.isNS) {
			_xPosition = event.pageX;
			_yPosition = event.pageY;
		}

		// Save window frame position and current window size.
		_oldLeft   = parseInt(_frame.style.left,  10);
		_oldTop    = parseInt(_frame.style.top,   10);
		_oldWidth  = parseInt(_frame.style.width, 10);
		_oldHeight = parseInt(_clientArea.style.height, 10);

		// Set document to capture mousemove and mouseup events.
		/*
		if (browser.isIE) {
			document.onmousemove = winResizeDragGo;
			document.onmouseup   = winResizeDragStop;
		}
		if (browser.isNS) {
			document.addEventListener("mousemove", winResizeDragGo,   true);
			document.addEventListener("mouseup"  , winResizeDragStop, true);
			event.preventDefault();
		}
		*/
		document.addEvent("mousemove", _winResizeDragGo);
		document.addEvent("mouseup", _winResizeDragStop);
		
		_inResizeDrag = true;
	}

	
	function _winResizeDragGo(event) {

		var north, south, east, west;
		var dx, dy;
		var w, h;

		if (!_inResizeDrag) {
			return;
		}

		// Set direction flags based on original resize direction.
		north = false;
		south = false;
		east  = false;
		west  = false;
		if (_resizeDirection.charAt(0) == "n") {
			north = true;
		}
		if (_resizeDirection.charAt(0) == "s") {
			south = true;
		}
		if (_resizeDirection.charAt(0) == "e" || _resizeDirection.charAt(1) == "e") {
			east = true;
		}
		if (_resizeDirection.charAt(0) == "w" || _resizeDirection.charAt(1) == "w") {
			west = true;
		}

		// Find change in cursor position.
		if (_browser.isIE) {
			dx = window.event.x - _xPosition;
			dy = window.event.y - _yPosition;
		}
		if (_browser.isNS) {
			dx = event.pageX - _xPosition;
			dy = event.pageY - _yPosition;
		}

		// If resizing north or west, reverse corresponding amount.
		if (west) {
			dx = -dx;
		}
		if (north) {
			dy = -dy;
		}
		
		// Check new size.
		w = _oldWidth  + dx;
		h = _oldHeight + dy;
		if (w <= _minimumWidth) {
			w = _minimumWidth;
			dx = w - _oldWidth;
		}
		if (h <= _minimumHeight) {
			h = _minimumHeight;
			dy = h - _oldHeight;
		}

		// Resize the window. For IE, keep client area and frame widths in synch.
		if (east || west) {
			_frame.style.width = w + "px";
			if (_browser.isIE) {
			  _clientArea.style.width = (w - _widthDiff) + "px";
			 }
		}
		if (north || south) {
			_clientArea.style.height = h + "px";
		}

		// Clip the title bar text, if necessary.
		if (east || west) {
			if (w < _clipTextMinimumWidth) {
				_titleBarText.style.width = (_minimizedTextWidth + w - _minimumWidth) + "px";
			}
			else {
				_titleBarText.style.width = "";
			}
		}

		// For a north or west resize, move the window.
		if (west) {
			_frame.style.left = (_oldLeft - dx) + "px";
		}
		if (north) {
			_frame.style.top  = (_oldTop  - dy) + "px";
		}

		if (_browser.isIE) {
			window.event.cancelBubble = true;
			window.event.returnValue = false;
		}
		if (browser.isNS) {
			event.preventDefault();
		}
	}


	function _winResizeDragStop(event) {

	  _inResizeDrag = false;

		// Remove mousemove and mouseup event captures on document.
		/*
		if (browser.isIE) {
			 document.onmousemove = null;
			 document.onmouseup   = null;
		}
		if (browser.isNS) {
			 document.removeEventListener("mousemove", winResizeDragGo,   true);
			 document.removeEventListener("mouseup"  , winResizeDragStop, true);
		}
		*/
	  	document.removeEvent("mousemove", _winResizeDragGo);
		document.removeEvent("mouseup", _winResizeDragStop);
	}
	
	
	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// The browser object
	function _browserObj() {
	  var ua, s, i;

	  this.isIE    = false;  // Internet Explorer
	  this.isNS    = false;  // Netscape
	  this.version = null;

	  ua = navigator.userAgent;

	  s = "MSIE";
	  if ((i = ua.indexOf(s)) >= 0) {
		 this.isIE = true;
		 this.version = parseFloat(ua.substr(i + s.length));
		 return;
	  }

	  s = "Netscape6/";
	  if ((i = ua.indexOf(s)) >= 0) {
		 this.isNS = true;
		 this.version = parseFloat(ua.substr(i + s.length));
		 return;
	  }

	  // Treat any other "Gecko" browser as NS 6.1. Includes Firefox.
	  s = "Gecko";
	  if ((i = ua.indexOf(s)) >= 0) {
		 this.isNS = true;
		 this.version = 6.1;
		 return;
	  }
	}
	
	

	// PUBLIC STUFF
	return {  
		// ------------------------------------------------------------------------------------------------------------------------------------------------
		// Constructor
		dhtmlWin : function(objWinDiv) {
			// Replace this with reference to Moo Browser
			_browser = new _browserObj();
			/*
			console.log("browser isIE: " + _browser.isIE);
			console.log("browser isNS: " + _browser.isNS);
			console.log("browser version: " + _browser.version);
			*/
			
			// Window components
			_frame = objWinDiv;
			_titleBar = objWinDiv.getElement("div.titleBar");
			_titleBarText = objWinDiv.getElement("span.titleBarText");
			_titleBarButtonsMaximize = objWinDiv.getElement("img.titleBarButtonsMaximize");
			_titleBarButtonsRestore = objWinDiv.getElement("img.titleBarButtonsRestore");
			_clientArea = objWinDiv.getElement("div.clientArea");
			_mapMaximize = objWinDiv.getElement("map.mapMaximize");
			_mapRestore = objWinDiv.getElement("map.mapRestore");
			
			// Colors.
			_activeFrameBackgroundColor = _frame.style.backgroundColor;;
			_activeFrameBorderColor = _frame.style.borderColor;
			_activeTitleBarColor = _titleBar.style.backgroundColor;
			_activeTitleBarBGImage = _titleBar.style.backgroundImage;
			_activeTitleTextColor = _titleBar.style.color;
			_activeClientAreaBorderColor = _clientArea.style.borderColor;
			if(_browser.isIE) {
				_activeClientAreaScrollbarColor = _clientArea.style.scrollbarBaseColor;
			}

			// Images.
			_activeButtonsImageMaximize = _titleBarButtonsMaximize.src;
			_inactiveButtonsImageMaximize = _titleBarButtonsMaximize.getAttribute("longdesc");
			_activeButtonsImageRestore = _titleBarButtonsRestore.src;
			_inactiveButtonsImageRestore = _titleBarButtonsRestore.getAttribute("longdesc");
			
			// Set up event handling.
			//_frame.parentWindow = this;
			_frame.addEvent("mousemove", _winResizeCursorSet);
			_frame.addEvent("mouseout", _winResizeCursorRestore);
			_frame.addEvent("mousedown", _winResizeDragStart);
			
			_titleBar.parentWindow = _frame;
			_titleBar.addEvent("mousedown", _winMoveDragStart);

			_clientArea.parentWindow = _frame;
			_clientArea.addEvent("click", _winClientAreaClick);
			
			for (i = 0; i < _mapMaximize.childNodes.length; i++) {
				if (_mapMaximize.childNodes[i].tagName == "AREA") {
				  _mapMaximize.childNodes[i].parentWindow = _frame;
				}
			}
			for (i = 0; i < _mapRestore.childNodes.length; i++) {
				if (_mapRestore.childNodes[i].tagName == "AREA") {
				  _mapRestore.childNodes[i].parentWindow = _frame;
				}
			}
			
			// Save the inital frame width and position, then reposition  the window. (why reposition?)
			_initWd = parseInt(_frame.style.width); // This returns NaN in both IE and FF so why is it used. There is a check below for it being NaN. Odd. Maybe NS4 worked.
			
			// For IE, start calculating the value to use when setting the client area width based on the frame width.
			if (_browser.isIE) {
				_titleBarText.style.display = "none"; // Turn off text. Not sure why. Perhaps if width was forced wider by excessive text.
				_w = _clientArea.offsetWidth;  // Width of div id=photoContent. 
				_widthDiff = _frame.offsetWidth - _w; // Width of "window" side borders
				_clientArea.style.width = _w + "px"; // Why do this? Makes it bigger by width of side borders.
				_dw = _clientArea.offsetWidth - _w;  // It appears that this is the difference in width between IE and FF rendering of the window side borders. Maybe.
				_w = _w - _dw;     
				_widthDiff = _widthDiff + _dw;
				_titleBarText.style.display = ""; // Turn text back on. 
			}

			// Find the difference between the frame's style and offset widths.
			// For IE, adjust the client area/frame width difference accordingly.
			// Same set of steps as the IF statement immediately above but using _frame instead of _clientArea.
			_w = _frame.offsetWidth;
			_frame.style.width = _w + "px";
			_dw = _frame.offsetWidth - _w;
			_w = _w - _dw;     
			_frame.style.width = _w + "px";
			if (_browser.isIE) {
				_widthDiff = _widthDiff - _dw; // Why is this getting reset? It was set in another IE-only block above.
			}
			
			// Find the minimum width for resize.
			_isOpen = true;  // Flag as open so minimize call will work.
			_minimize();
			
			// Get the minimum width.
			if (_browser.isNS && _browser.version >= 1.2) {
				// For later versions of Gecko.
				_minimumWidth = _frame.offsetWidth;
			}
			else {
				// For all others.
				_minimumWidth = _frame.offsetWidth - _dw;
			}

			// Find the frame width at which or below the title bar text will need to be clipped.
			_titleBarText.style.width = "";
			_clipTextMinimumWidth = _frame.offsetWidth - _dw;

			// Set the minimum height.
			_minimumHeight = 1;

			// Restore window. For IE, set client area width.
			_restore();
			_isOpen = false;  // Reset flag.
			_initWd = Math.max((isNaN(_initWd) ? 0 : _initWd), _minimumWidth); 
			_frame.style.width = _initWd + "px";

			if (_browser.isIE) {
				_clientArea.style.width = (_initWd - _widthDiff) + "px";
			}
			
			// Clip the title bar text if needed.
			if (_clipTextMinimumWidth >= _minimumWidth) {
				_titleBarText.style.width = (_minimizedTextWidth + _initWd - _minimumWidth) + "px";
			}			
			
			// Restore the window to its original position.
			//_frame.style.left = _initLt;

			//Switch hiding photo frame with display=none as this avoids scrolling jitters; need display=block & visibility=hidden for above.
			_frame.style.display = "none";
			_frame.style.visibility = "visible";
		},
		
		// ------------------------------------------------------------------------------------------------------------------------------------------------
		// Set methods.
		
		winOpen : function() {
			_open();
		},
		
		winClose : function() {
			_winClose();
		},
		
		winMinimize : function() {
			_minimize();
		},
		
		winMaximize : function() {
			_maximize();
		},
		
		winRestore : function() {
			_restore();
		},
		
		winMakeActive : function() {
			_makeActive();
		},
		
		winSetButtonsMaximize : function() {
			_setButtonsMaximize();
		},
		
		winSetButtonsRestore : function() {
			_setButtonsRestore();
		},
		
		winAdjustToScroll : function() {
			_adjustToScroll();
		}
	
		// ------------------------------------------------------------------------------------------------------------------------------------------------
	};  
})(); // the paranthesis will execute the function immediately.. Do not remove.





//=============================================================================
// Utility functions.
//=============================================================================

function winFindByClassName(el, className) {

	var i, tmp;

	if (el.className == className) {
		return el;
	}
	// Search for a descendant element assigned the given class.

	for (i = 0; i < el.childNodes.length; i++) {
		tmp = winFindByClassName(el.childNodes[i], className);
		if (tmp != null) {
			return tmp;
		}
	}

	return null;
}

//Get window size. Actually want inside (available space), not window dimensions. 
//Use document.body.clientWidth for width but not document.body.clientHeight for 
//height since page almost certainly extends beyond visible window.
function winGetSize() {
	var currPageWidth = 0;
	
	if(document.body.clientWidth) { //IE, FF and Opera all support this. Excludes scroll bars, browser toolbars etc.
		currPageWidth = document.body.clientWidth;
	}
	else if(window.innerWidth) { //Fallback position just in case. Real browsers: Firefox, Safari etc.
		currPageWidth = window.innerWidth;
	}
	else if(document.documentElement.clientWidth) { //IE 6
		currPageWidth = document.documentElement.clientWidth;
	}
	
	var currPageHeight = 0;
	if(window.innerHeight) { //Real browsers: Firefox, Opera etc.
		currPageHeight = window.innerHeight;
	}
	else if(document.documentElement.offsetHeight) { //IE 6
		currPageHeight = document.documentElement.offsetHeight;
	}
	
	return ({x: currPageWidth, y: currPageHeight});	
}

//Get window scroll position
function winGetScroll() {
	var scrollLeft = 0;
	var scrollTop = 0;
	if (self.pageYOffset) { // all except Explorer
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
	
	return ({x: scrollLeft, y: scrollTop});
}

//Get position of any object on the page
function objFindPos(obj) {
	var curLeft = 0;
	var curTop = 0;
	if (obj.offsetParent) {
		curLeft = obj.offsetLeft;
		curTop = obj.offsetTop;
		while (obj = obj.offsetParent) {
			curLeft += obj.offsetLeft;
			curTop += obj.offsetTop;
		}
	}
	return ({x:curLeft, y:curTop});
}





//=============================================================================
// Initialize
//=============================================================================

// Create an array of DHTMLWin objects
var winList = new Array();


// Initialize windows and build winList array.
window.addEvent('domready', function() {
	$$("div.window").each(function(item, index){
		winList[item.id] = DHTMLWin.dhtmlWin(item); //passing a DIV to the constructor function
	});
});


