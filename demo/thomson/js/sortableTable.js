/*
SORTABLE TABLE (Type 1)

Purpose: 
Allow sorting of tables, regardless of data formatting, by clicking a link in the 
table head. The current sort direction is shown by a graphic indicator.

Problem:
While it's easy to find sortable tables on the internet, the examples I found sorted
whatever was visible in the table. This means that formatted numbers (e.g. with commas)
or formatted strings (e.g. inside SPAN tags to give color) are incorrectly sorted. It
also wasn't possible to have right-justified columns were the arrow appears to the right
of the right-justified link.

For correct sorting, need to separate the data from what is displayed. While it is possible 
to hold the data in a sortable array and sort the data, rebuilding the table is problematic 
and a lot of work. In particular, Microsoft won't let you use innerHTML in tables so to 
rebuild a table this way requires you to have a container DIV and apply the innerHTML of the
whole table to that DIV. 

Using the createElement/createTextNode/appendChild is perhaps technically the correct 
way but it is long. It is also awkward if you have any HTML entities like &amp; that should appear 
in a string like "S&amp;P 500" if the page is XHTML. You have to determine where the HTML 
entity is, strip it out and make a separate createEntityReference call as well as 
createTextNode before and after. 

In both approaches, all the data has to be reformatted for display which means additional
Javascript processing is required.

Solution:
If the table section of the DOM can be thought of as a tree, instead of bothering to
rebuild the sticks and leaves, just move the branches around in the tree. Instead of
sorting an array or other data storage object and then figuring out how to rearrange
the tree to match, move the branches around while the sort is done on the data.

This particular version applies colors to alternate rows and to the current sorted column.

Instructions for use

1. Create a stylesheet that includes something like this:

	table.shareholders {width:100%; border-collapse:collapse; color:#666}
	table.shareholders col.holdingsColumn1 {width:23%}  (etc.)
 
 	table.shareholders th {padding: 2px 10px 5px 10px; text-align:right; vertical-align:bottom}
	table.shareholders td {padding: 2px 10px 2px 10px; text-align:right}
	table.shareholders th img {margin:0 0 0 3px; width:5px; height:3px}
	table.shareholders th div.shiftRight  {position:relative; top:0; left:8px; float:right}
	table.shareholders .flLeft {float:left; text-align:left}
	table.shareholders .flRight {float:right}
	
	table.shareholders tr.oddRow td      {background-color:#f2f2f2}
	table.shareholders tr.evenRow td     {}
	table.shareholders tr td.oddRowSort, table.shareholders tr th.oddRowSort  {background-color:#e6e6e6}
	table.shareholders tr td.evenRowSort {background-color:#f2f1f0}

	table.shareholders tr.hiliteRow td {background-color:#ffc}       [Optional highlighted row]
	table.shareholders tr td.hiliteRowSort {background-color:yellow} [Optional highlighted row]
		
	The key points to note in this implementation (change as you see fit):
	* Table cells are aligned right unless explicitly set to left
	* If you want the content of a TD or TH to be left aligned, put the content in a DIV and apply class 'flLeft' to the DIV.
	  This allows class attributes for the TD or TH to be altered dynamically to apply background colors to highlight alternate
	  rows and to indicate the sort column.
	* Apply 'flLeft' to sort indicator image in left-justified columns; 'flRight' if right-justified.
	* In right-justified columns, apply 'shiftRight' to the DIV holding the content of TH so that the text aligns with data cell 
	  text but puts the sort arrow off to right. Columns that are not the current sort column use /gif/x.gif as a placeholder so 
	  that the header text does not jump left/right when the column is or is not the current sort column.
	* You'll notice that there is left/right padding on the TH/TD. I suggest you use COLGROUP/COL above THEAD and assign widths
	  in the stylesheet to the columns since IE will likely wreck your layout if you attempt to apply widths to TH/TDs.
	* use oddRow/evenRow to shade alternate rows
	* use oddRowSort/evenRowSort to shade the currently sorted column.
	* use hiliteRow/hiliteRowSort to shade a particular row
	  
2. Create the table making sure that
	* IDs are given to the links (actions are assigned to the links in Javascript)
	* blank images (/gif/x.gif) are placed where arrows should appear
	
3. AFTER the table is constructed (since events are attached to the links) build 
	the 'sortableTable' object which describes the structure of the table, carries 
	the data and sets the path for the images. See example 
	(http://dev2.wallst.com/playground/peter/testSortableTable.htm) for details.	

*/ 
		//------------------------------------------------------------------------
		//From eventFunction.js
		//------------------------------------------------------------------------
		function getSrcElement(event) {
			return (event.srcElement) ? event.srcElement : event.currentTarget;
		}

		function addEvent(obj, eventType, afunction, isCapture) {
			// W3C DOM
			if (obj.addEventListener) {
				obj.addEventListener(eventType, afunction, isCapture);
				return true;
			}
			// Internet Explorer
			else if (obj.attachEvent) {
				return obj.attachEvent("on"+eventType, afunction);
			}
			else return false;	
		}


		//------------------------------------------------------------------------
		//Global variable (associative array with link ID as key) to associate sortableTable object and current sort direction with each link
		var linksArray = new Array();

		//Create Javascript object to store details of table to be sorted
		function sortableTable(dataObjName, dataArray, fieldObjArray, tbodyID, imageUp, imageDown, imageBlank, styleOddRows, styleEvenRows, styleSortColumnOddRow, styleSortColumnEvenRow, initialSortColumn) {
			this.dataArray = dataArray;
			this.fieldArray = fieldObjArray;
			this.tbodyID = tbodyID;
			this.imageUp = imageUp;
			this.imageDown = imageDown;
			this.imageBlank = imageBlank;
			this.styleOddRows = styleOddRows;
			this.styleEvenRows = styleEvenRows;
			this.styleSortColumnOddRow = styleSortColumnOddRow;
			this.styleSortColumnEvenRow = styleSortColumnEvenRow;
			this.lastSortOn = initialSortColumn;
			
			//Optional arguments for highlighted row
			var styleHiliteRow = "";
			var styleSortColumnHilite = "";
			if(arguments.length > 12) {
				styleHiliteRow = arguments[12];
				styleSortColumnHilite = arguments[13];
			}
			this.styleHiliteRow = styleHiliteRow;
			this.styleSortColumnHilite = styleSortColumnHilite;

			var field, fieldObj, linkId;
			for(field in fieldObjArray) {
				fieldObj = fieldObjArray[field];
				linkId = fieldObj.linkId;
				sortDir = fieldObj.dir;
				
				//alert("field: " + field + "\nlinkId: " + linkId + "\nsortDir: " + sortDir)
			
				//Attach onclick events to links in the table head to replace:  onclick="sortTable('indices', 'index', 'desc')"   			linkId = fieldArray[n].link;
				linkObj = document.getElementById(linkId);
				addEvent(linkObj, "click", sortTable);
				
				//Associate sortableTable object, field name and current sort direction with each link
				linksArray[linkId] = {dataObjName:dataObjName, sortOn:field, sortDir:sortDir};
			}
			
			if(initialSortColumn)
				lastSortOn = initialSortColumn;
		}
		

		/*Rearrange Javascript object and table in synchronized manner
		Note that while primitive data types are passed by value, arrays and objects, are passed by reference which means table rows must be cloned before being used to set another row. 
		Using "insertion sort" which is relatively simple but twice as fast as bubble sort. Here is what I started with (C++):
		int i, j, index;
		for (i=1; i < array_size; i++) {
			index = numbers[i];
			j = i;
			while ((j > 0) && (numbers[j-1] > index)) {
				numbers[j] = numbers[j-1];
				j = j - 1;
			}
			numbers[j] = index;
		}
		*/
		function sortTable() {
		
			var e = arguments.length ? arguments[0] : window.event;
			var sourceLink = getSrcElement(e);
			var sourceLinkId = sourceLink.id;
			
			//Look up links array to get name of sortableTable object, field name to sort on and sort direction
			var dataObjName, sortOn, sortDir;
			dataObjName = linksArray[sourceLinkId].dataObjName;
			sortOn      = linksArray[sourceLinkId].sortOn;
			sortDir     = linksArray[sourceLinkId].sortDir;
		
			//alert("dataObjName: " + dataObjName + "\nsortOn: " + sortOn + "\nsortDir: " + sortDir)
			
			//Make copy of dataObj for easy extraction of member variables
			var command = "var tableSortObj = " + dataObjName;
			eval(command);

			var dataArray              = tableSortObj.dataArray; //2-dimensional array
			var fieldObjArray          = tableSortObj.fieldArray;
			var tbodyID                = tableSortObj.tbodyID;
			var imageUp                = tableSortObj.imageUp;
			var imageDown              = tableSortObj.imageDown;
			var imageBlank             = tableSortObj.imageBlank;
			var styleOddRows           = tableSortObj.styleOddRows;
			var styleEvenRows          = tableSortObj.styleEvenRows;
			var styleSortColumnOddRow  = tableSortObj.styleSortColumnOddRow;
			var styleSortColumnEvenRow = tableSortObj.styleSortColumnEvenRow;
			var lastSortOn             = tableSortObj.lastSortOn;
			var styleHiliteRow         = tableSortObj.styleHiliteRow;
			var styleSortColumnHilite  = tableSortObj.styleSortColumnHilite;

			//Get child elements of table body
			var tableBody = document.getElementById(tbodyID);
			var rowCollection = tableBody.getElementsByTagName('tr');
		
			//"Insertion sort"
			var i, j, k, x, value, objRow, caseSensitive, ignoreCase;
			for (i=1; i < dataArray.length; i++) {
			
				//Create temporary holders for a comparison value, a row of data in the object and a table row from the DOM.
				compareValue = dataArray[i][sortOn];
				objRow = dataArray[i];
				cloneTR1 = rowCollection.item(i).cloneNode(true);
				
				//Determine if this needs to be a case-sensitive sort (only relevant for strings)
				ignoreCase = false;
				if(typeof(compareValue == "string")) {
					caseSensitive = fieldObjArray[sortOn].caseSensitive;
					
					if(typeof(caseSensitive) != "undefined" && caseSensitive == false)
						ignoreCase = true;
				}
	
				j = i;
				while (j > 0 && eval("dataArray[" + (j-1) + "][sortOn]" + (ignoreCase ? ".toLowerCase()" : "") + (sortDir == "asc" ? " > " : " < ") + "compareValue" + (ignoreCase ? ".toLowerCase()" : ""))) {  //
					dataArray[j] = dataArray[j-1];
						
					cloneTR2 = rowCollection.item(j-1).cloneNode(true)
					tableBody.replaceChild(cloneTR2, rowCollection.item(j));
					j = j - 1;
				}
				dataArray[j] = objRow;
				tableBody.replaceChild(cloneTR1, rowCollection.item(j));
				
				//Resorted Javascript 2-dimensional table
				var arrayStr = ""
				for(k=0; k<dataArray.length; k++) {
					var row = dataArray[k];
					rowStr = "";
					for(x in row)
						rowStr += row[x] + "  |  ";
					arrayStr += rowStr + "\n";
				}
			}
			//alert("DataObj after sorting:\n\n" + arrayStr)
			
			
			//Reset the effect of the link by updating linksArray
			linksArray[sourceLinkId].sortDir = (sortDir == "asc" ? "desc" : "asc")
			
			//Reset the sort indicator image by changing image source
			if(lastSortOn)
				document.getElementById(fieldObjArray[lastSortOn].imgHolder).src = imageBlank;
			document.getElementById(fieldObjArray[sortOn].imgHolder).src = (sortDir == "asc" ? imageUp : imageDown);
			
			//Get the (zero-based) column number for last sorted column and current sorted column.
			if(lastSortOn)
				lastSortColumn = fieldObjArray[lastSortOn].column;
			sortColumn = fieldObjArray[sortOn].column;
			
			//Reset row colors and highlight the cells in the sorted column
			var k, n, cellCollection, sortColumn, currentRowClass;
			for(k=0; k<rowCollection.length; k++) {
				currentRowClass = rowCollection.item(k).className;
				if(currentRowClass != styleHiliteRow) {
					if(k%2)
						rowCollection.item(k).className = styleOddRows;
					else
						rowCollection.item(k).className = styleEvenRows;
				}
					
				cellCollection = rowCollection.item(k).getElementsByTagName("td");
				for(n=0; n<cellCollection.length; n++) {
					if(n == sortColumn-1) {
						if(currentRowClass == styleHiliteRow) {
							cellCollection.item(n).className = styleSortColumnHilite;
						}
						else {
							if(k%2)
								cellCollection.item(n).className = styleSortColumnOddRow;
							else
								cellCollection.item(n).className = styleSortColumnEvenRow;
						}
					}
					else {
						cellCollection.item(n).className = "";
					}
				}
			}
			
			//Reset sort column in table head
			var table = tableBody.parentNode;
			cellCollection = table.getElementsByTagName("th");
			if(lastSortOn)
				cellCollection.item(lastSortColumn-1).className = "";
			cellCollection.item(sortColumn-1).className = styleSortColumnOddRow;


			//Reset the data object with updated structure
			tableSortObj.dataArray = dataArray;
			tableSortObj.lastSortOn = sortOn
			var command = dataObjName + "= tableSortObj";
			eval(command);
			
		}
