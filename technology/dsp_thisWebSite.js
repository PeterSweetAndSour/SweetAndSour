	function hide(headingID, textID, fileName) {
		heading = document.getElementById(headingID);
		heading.innerHTML = "<b><a href=\"javascript:show('" + headingID + "','" + textID + "','" + fileName + "')\">Show</a> " + fileName + "<b>";

		programList = document.getElementById(textID);
		programList.style.display = "none";
	}

	function show(headingID, textID, fileName) {
		heading = document.getElementById(headingID);
		heading.innerHTML = "<b><a href=\"javascript:hide('" + headingID + "', '" + textID + "','" + fileName + "')\">Hide</a> " + fileName + "<b>";

		programList = document.getElementById(textID);
		programList.style.display = "block";
	}
	
