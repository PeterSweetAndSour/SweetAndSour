// ---------------------------------------------------------------------------
// Cross-browser event functions. Created by Scott Andrew, tweaked by WSOD.
// ---------------------------------------------------------------------------

var EVENT = {
	ONLOAD: "load",
	ONUNLOAD: "unload",
	ONMOUSEOVER: "mouseover",
	ONMOUSEOUT: "mouseout",
	ONMOUSEMOVE: "mousemove",
	ONCLICK: "click",
	ONCHANGE: "change",
	ONSUBMIT: "submit",
	ONBLUR: "blur",
	ONFOCUS: "focus",
	ONCHANGE: "change"
};

function getSrcElement() {
	//allows elements to be passed directly into event handlers
	if(arguments[0].tagName) {
		return arguments[0];
	} else {
		var event = arguments[0];
		return event.srcElement || event.currentTarget || window;
	};
};

/*
function getSrcElement () {
	if (false && arguments[0].getAttribute) {
	// extension to allow for passing elements directly into event handlers 
		return arguments[0];
	} else {
		var event = arguments[0];
		return (event.srcElement) ? event.srcElement : event.currentTarget;
	}
}*/

function addEvent(obj, eventType, afunction, isCapture) {
	// W3C DOM
	if (obj.addEventListener) {
	   obj.addEventListener(eventType, afunction, isCapture);
	   return true;
	}
	// Internet Explorer
	else if (obj.attachEvent) {
	   return obj.attachEvent("on" + eventType, afunction);
	}
	else return false;
}

function removeEvent(obj, eventType, afunction, isCapture) {
	if (obj.removeEventListener) {
	   obj.removeEventListener(eventType, afunction, isCapture);
	   return true;
	}
	else if (obj.detachEvent) {
	   return obj.detachEvent("on" + eventType, afunction);
	}
	else return false;
}

function removeTextSelect() {
	if (!document.onselectstart) {
		document.onselectstart = function() {
			cancelEvent();
			return false;
		}
	}
}
function enableTextSelect() {
	if (document.onselectstart) {
		document.onselectstart = null;
	}
}

function cancelEvent(event){
	if (event) {
		if (event.preventDefault){event.preventDefault()};
		if (event.stopPropagation){event.stopPropagation()};
		event.cancelBubble = true;
		event.returnValue = false;
	}
	
	return false;
}
