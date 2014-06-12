/*
Created by Sean Kane (http://celtickane.com/programming/code/ajax.php)
Feather Ajax v1.0.0
Usage:
	Javascript call:
		var ajaxObj = new AjaxObject();
		ajaxObj.sndReq('get','myajax.php?action=gettext');
	
	Called page specifies ID of element before => to load into. Can do multiple if |-separated
	echo "myElement=>The Ajax worked at ".date("h:i:s A")."!|";
*/
function AjaxObject() {
	this.createRequestObject = function() {	//This is called at the very bottom -- it sets this.http to the Ajax request object (ro)
		var xmlHttp;
		if (window.XMLHttpRequest){
			// If IE7, Firefox, Safari, etc: Use native object
			xmlHttp = new XMLHttpRequest();
		}
		else if (window.ActiveXObject){
			// ...otherwise, use the ActiveX control for IE5.x and IE6
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		return xmlHttp;
	}
	this.sndReq = function(action, url) { //This function will start the Ajax process, and is called manually by the user
		//action is either 'get' or 'post'
		//url can contain GET variables -- like myajax.php?action=test
		this.http.open(action, url, true); //The URL includes any GET or POST variables you want
		this.http.onreadystatechange = this.handleResponse; //This is the callback function when the state of the http object changes
		this.http.send(null);	//Actually start the request process -- this will contact the URL via the action specified, and wait to call this.handleResponse
	}
	this.handleResponse = function() { //This function is called when this.http's state changes
		//console.log("me.http.readyState: " + me.http.readyState);
		if ( me.http.readyState == 4) { //State of 4 means the connection is done (data was transferred)
			if (me.http.status == 200) {
				var rawdata = me.http.responseText.split("|"); //If there aren't any |'s in the string, it will just grab the entire string and put it into items[0]
				for ( var i = 0; i < rawdata.length; i++ ) { //Loop through the rawdata[] array
					var item = (rawdata[i]).split("=>");	//Split each item into id=>value where id is item[0] and the value is item[1]
					if (item[0] != "") {	//If it is a valid HTML id
						//console.log("here I am");
						document.getElementById(item[0]).innerHTML = item[1]; //Set the innerHTML property of the given HTML item to the value of item[1]
						//item[0] is the id of the HTML element, item[1] is the value of that HTML element
					}
				}
			}
			else {
				//Error
			}
		}
		//if (me.http.readyState == 1) {} //Here's something you can do if you want...put code in this if statement if you want something to happen
		//while you're downloading the data -- like a loading spinner icon, or even a "Please Wait..." label
	}
	var me = this;	//Unfortunately, this is necessary because this.http won't work in the callback function 'handleResponse' (we have to use me.http instead)
	this.http = this.createRequestObject(); //http holds the request object (ro), which is returned from the createRequestObject() function -- this is automatically run when you make a new AjaxObject()
}