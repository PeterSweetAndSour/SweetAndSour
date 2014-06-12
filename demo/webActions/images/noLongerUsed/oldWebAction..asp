<!-- #include virtual="/includes/asplib/init.asp" -->
<%
/*
This page displays web reporting results.
Variables:
+++ updateVariables.asp
+++ setNumRowsToShow.asp
+++ setOrderByCriteria.asp
+++ setWhereCriteria.asp
+++ webReportJS.asp

=>| get.changedID   
=>| post.siteNumber             This and other post variables available if page has submitted to itself.
=>| post.firstLine
=>| post.numRowsToShow
=>| post.colWidthStr
+>| User.Session("siteNumber")  This and other session variables available if page has previously been redrawn.
+>| User.Session("firstLine")
+>| User.Session("lastLine")
+>| User.Session("sortList")
+>| User.Session("colWidthStr")
+>| User.Session("numRowsToShow")
+>| User.Session("openBracket")
+>| User.Session("where_1")
+>| User.Session("condition_1")
+>| User.Session("compareText_1")
+>| User.Session("andOr_1")
+>| User.Session("where_2")
+>| User.Session("condition_2")
+>| User.Session("compareText_2")
+>| User.Session("closeBracket")
+>| User.Session("andOr_2")
+>| User.Session("where_3")
+>| User.Session("condition_3")
+>| User.Session("compareText_3")
+>| User.Session("filterList")

|=> post.siteNumber
|=> post.firstLine
|=> post.numRowsToShow
|=> post.colWidthStr

|=> post.sortBy_1
|=> post.sortDir_1
|=> post.sortBy_2
|=> post.sortDir_2
|=> post.sortBy_3
|=> post.sortDir_3

|=> post.openBracket
|=> post.where_1
|=> post.condition_1
|=> post.compareText_1
|=> post.andOr_1
|=> post.where_2
|=> post.condition_2
|=> post.compareText_2
|=> post.closeBracket
|=> post.andOr_2
|=> post.where_3
|=> post.condition_3
|=> post.compareText_3
|=> post.filterStr
*/
var siteID, firstLine, lastLine, editRow;
var rowToEdit = -1;

//Capture user identity if available
if(Request.ServerVariables("LOGON_USER") && Request.ServerVariables("LOGON_USER") != "")
	User.Session("loginStr") = ("" + Request.ServerVariables("LOGON_USER")).toUpperCase(); //It will fail if '"" +' is taken away. Apparently not recognized as a string.


/* Set database fields and related ASP variables. Used by updateVariables and setWhereCriteria.asp and setOrderByCriteria.asp. */ 
%><!-- #include file="setFields.asp" --><%
	
//Set session variables and column widths array
if ((! User.Session("siteNumber")) && (! rs("pageAction"))) { //Arriving from entry page
	Response.Redirect("webReportEntry.asp");
}
else if (rs("pageAction") == "start") { //User specified the site to review on webReportEntry.asp
	User.Session("siteNumber")    = rs("siteNumber");
	User.Session("firstLine")     = 1;
	User.Session("numRowsToShow") = 25;
	User.Session("sortList")      = "Page ASC,ExcludeReporting ASC,Note ASC";
	User.Session("filterList")    = "";
	
	columnWidthsASP = new Array();
	columnWidthsASP[0]  =   0; //Placeholder only
	columnWidthsASP[1]  = 100;
	columnWidthsASP[2]  = 100;
	columnWidthsASP[3]  = 100;
	columnWidthsASP[4]  = 100;
	columnWidthsASP[5]  = 227;
	columnWidthsASP[6]  =  36;
	columnWidthsASP[7]  =  36;
	columnWidthsASP[8]  =  60;
	columnWidthsASP[9]  =  60;
	columnWidthsASP[10] =  44;
	columnWidthsASP[11] = 120;
	columnWidthsASP[12] = 100;
	
	User.Session("colWidthStr") = columnWidthsASP.join(",");
	
	User.Session("openBracket") = "";
	User.Session("where_1") = "";
	User.Session("condition_1") = "";
	User.Session("compareText_1") = "";
	User.Session("andOr_1") = "";
	User.Session("where_2") = "";
	User.Session("condition_2") = "";
	User.Session("compareText_2") = "";
	User.Session("andOr_2") = "";
	User.Session("where_3") = "";
	User.Session("condition_3") = "";
	User.Session("compareText_3") = "";
}
else if(rs("pageAction") == "goToPage") {
	User.Session("firstLine") = rs("firstLine");
	User.Session("colWidthStr")   = rs("colWidthStr");
	columnWidthsASP = User.Session("colWidthStr").split(",");
}
else if(rs("pageAction") == "changeNumRows") {
	User.Session("numRowsToShow") = rs("numRowsToShow");
	User.Session("colWidthStr")   = rs("colWidthStr");
	columnWidthsASP = User.Session("colWidthStr").split(",");
}
else if(rs("pageAction") == "updatePage") {
	%>
	<!-- #include file="updateVariables.asp" -->
	<%
	User.Session("colWidthStr")   = rs("colWidthStr");
	columnWidthsASP = User.Session("colWidthStr").split(",");
}
else if(rs("pageAction") == "redrawAfterEdit") {
	changedID = rs("changedID");
	columnWidthsASP = User.Session("colWidthStr").split(",");
}
else if(rs("pageAction") == "editRow") {
	rowToEdit = rs("rowToEdit");
	User.Session("colWidthStr") = rs("colWidthStr");
	columnWidthsASP = User.Session("colWidthStr").split(",");
}
else if (! User.Session("siteNumber")) { //User has come to this page directly or session has timed out
	Response.Redirect("webReportEntry.asp");
}
else { //Session still current. Assume page has just been refreshed so continue if possible.
	columnWidthsASP = User.Session("colWidthStr").split(",");
}

//Function for easy access to record sets
function getV(objName, field, i) {
	var predicate = field;
	if (i || i==0)
		predicate = "row["+i+"]."+predicate
		value = eval(objName+'.value("'+predicate+'")')
		if (value == -32768 || value == "N/A")
			value = "";
		return  value;
}

//Get records to populate table
var lastLine = Number(User.Session("firstLine")) + Number(User.Session("numRowsToShow")) - 1;
var webActions = User.CreateObject("WSOD.OpenSQL.1");
webActions.SetVariableName("Get web actions for site " + User.Session("siteNumber"));
webActions.SetInput("Query.ID", 2148);
webActions.SetInput("SiteNumber", User.Session("siteNumber"));
webActions.SetInput("FirstLine", User.Session("firstLine"));
webActions.SetInput("LastLine", lastLine);
webActions.SetInput("SortList", User.Session("sortList"));
webActions.SetInput("FilterList", User.Session("filterList"));
webActions.Retrieve();
var objName = "webActions";

//Get category names
var catNames = User.CreateObject("WSOD.OpenSQL.1");
catNames.SetVariableName("Category names");
catNames.SetInput("Query.ID", 2147);
catNames.Retrieve();
var objName = "catNames";

//Get site name and MS dates for start of "last month" and "last week".
var siteNameSQL = User.CreateObject("WSOD.OpenSQL.1");
siteNameSQL.SetVariableName("Get site name for site " + User.Session("siteNumber"));
siteNameSQL.SetInput("Query.ID", 2146);
siteNameSQL.SetInput("SiteNumber", User.Session("siteNumber"));
siteNameSQL.Retrieve();
siteName   = siteNameSQL.value("SiteName");
monthStart = siteNameSQL.value("MonthBeginning");
weekStart  = siteNameSQL.value("WeekBeginning");
%>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<script language="Javascript" type="text/javascript">
		<!-- #include file="dragColumnsJS.asp" -->
		<!-- #include file="webReportJS.asp" -->
	</script>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Prototype</title>
	<style type="text/css">
		<!-- #include file="webReport.css" -->
	</style>

</head>

<body>
<form method="post"> <!-- Form action set by Javascript -->
	<h1>Web Actions</h1>
	<hr />
	<!-- Set site to view. -->
	<div class="inputRow">
		<div class="inputLabel">Site ID:</div>
		<div class="input">
			<input type="text" name="siteNumber"class="txt" id="siteNumber" value="<%= User.Session("siteNumber") %>"> &nbsp; <%= siteName %> <br />
		</div>
	</div>
	<!-- Display drop-down list to set number of rows to show. -->
	<!-- #include file="setNumRowsToShow.asp" -->
	
	<!-- Display fields for sorting. -->
	<!-- #include file="setOrderByCriteria.asp" -->
	
	<!-- Display fields for filtering. -->
	<!-- #include file="setWhereCriteria.asp" -->
	
	<div class="inputRow">
		<div class="inputLabel">&nbsp;</div>
		<div class="input">
			<input type="button" name="update" id="updateButton" value="Update" onclick="updatePage(<%= User.Session("firstLine") %>)">&nbsp;&nbsp;
		</div>
	</div>
	<hr /> 
	
	<!-- Paging controls above records listing. --> <% 
	var numPages = Math.floor(webActions.value("TotalRows")/User.Session("numRowsToShow")) + 1;
	var thisPage = Math.floor(User.Session("firstLine")/User.Session("numRowsToShow")) + 1;
	var firstLinePrevPage = Number(User.Session("firstLine")) - Number(User.Session("numRowsToShow"));
	var firstLineNextPage = Number(User.Session("firstLine")) + Number(User.Session("numRowsToShow"));
	%><div class="paging"><%
	if(User.Session("firstLine") != 1) { %>
	<a href="javascript:void(0)" onclick="goToPage(<%= firstLinePrevPage %>); return false">&lt;&lt; Previous</a>&nbsp;&nbsp;
	<%} 
	if(thisPage < numPages) { %>
	<a href="javascript:void(0)" onclick="goToPage(<%= firstLineNextPage %>); return false">Next &gt;&gt;</a>&nbsp;&nbsp;
	<%} 
	if(numPages > 3) { %>
	Page <input type="text" name="goToBox1" class="txt goTo" id="goToBox1" value="" onkeypress="return numHandle(event,false,false)">&nbsp;<a href="javascript:void(0)" onclick="goToPageFromBox(1, <%= numPages %>);return false">Go</a> &nbsp;&nbsp;<span id="goToError1" class="warning"></span>
	<%}%>
	</div>
	
	<table class="slider" id="slider">
		<col id="column1" />
		<col id="column2" />
		<col id="column3" />
		<col id="column4" />
		<col id="column5" />
		<col id="column6" />
		<col id="column7" />
		<col id="column8" />
		<col id="column9" />
		<col id="column10" />
		<col id="column11" />
		<col id="column12" />
		
		<tr>
			<th><div class="space">Page</div></th>
			<th><img class="drag" id="drag1" onmousedown="dragStart(event, 'drag1', '')"                 onmouseover="changeCursor('in', 'drag1')" onmouseout="changeCursor('out', 'drag1')" src="webReport.gif" alt="" /><div class="noHSpace">Note</div></th>
			<th><img class="drag" id="drag2" onmousedown="dragStart(event, 'drag2', '')"                 onmouseover="changeCursor('in', 'drag2')" onmouseout="changeCursor('out', 'drag2')" src="webReport.gif" alt="" /><div class="noHSpace">Description</div></th>
			<th><img class="drag" id="drag3" onmousedown="dragStart(event, 'drag3', 'inputDescription')" onmouseover="changeCursor('in', 'drag3')" onmouseout="changeCursor('out', 'drag3')" src="webReport.gif" alt="" /><div class="noHSpace">Project Note</div></th>
			<th><img class="drag" id="drag4" onmousedown="dragStart(event, 'drag4', 'inputProjectNote')" onmouseover="changeCursor('in', 'drag4')" onmouseout="changeCursor('out', 'drag4')" src="webReport.gif" alt="" /><div class="noHSpace">Category</div></th>
			<th><img class="drag" id="drag5" onmousedown="dragStart(event, 'drag5', 'selectCategory')"   onmouseover="changeCursor('in', 'drag5')" onmouseout="changeCursor('out', 'drag5')" src="webReport.gif" alt="" /><div class="noHSpace">New</div></th>
			<th><div class="space">Hide</div></th>
			<th><div class="space">Last Mth*</div></th>
			<th><div class="space">Last Wk**</div></th>
			<th><div class="space">Action</div></th>
			<th><div class="space">Modified</div></th>
			<th><div class="space">Modified By</div></th>
		</tr>
	
		<%
		var mouseOverBehavior, action, page, note, description, projectNote, actionCategory, excludeReporting, newEntry, hideEntry;
		for (var i=0; i<webActions.instanceCount("row"); i++) {
			id           = getV("webActions", "ID", i);	
			page         = getV("webActions", "Page", i);
			note         = getV("webActions", "Note", i);
			description  = getV("webActions", "Description", i);
			projectNote  = getV("webActions", "ProjectNote", i);
			actionCat    = getV("webActions", "ActionCategory", i);
			exclReport   = getV("webActions", "ExcludeReporting", i); //0, 1 or 2
			hitsLastWk   = getV("webActions", "HitsLastWeek", i);
			hitsLastMth  = getV("webActions", "HitsLastMonth", i);
			action       = getV("webActions", "Action", i);
			modifiedDate = formatDate(getV("webActions", "ModifiedDate", i), "yyyy-mm-dd hh:mm:ss");
			modifiedBy   = getV("webActions", "ModifiedBy", i);
			
			if(i == rowToEdit) {
				//For hidden form variables below
				currentID = id;
				currentExclReport = exclReport;
			
				//Store some session variables to allow "undo" after save
				User.Session("oldWebActionID") = id;
				User.Session("oldActionCategory") = actionCat;
				User.Session("oldDescription") = description;
				User.Session("oldExcludeReport") = exclReport;
				User.Session("oldProjectNote") = projectNote;
	
				//Determine what to show in "new" and "hide" columns
				if(exclReport == 2) { //New
					newEntry = "checked";
					hideEntry = "disabled";
				}
				else if (exclReport == 1) { //Hide entry
					newEntry = "disabled";
					hideEntry = "checked";
				}
				else { //0: Show entry
					newEntry = "disabled";
					hideEntry = "";
				}
				
				%>
				<tr class="edit">
					<td><div class="space"><%= page %></div></td>
					<td><div class="space"><%= note %></div></td>
					<td><div class="noVSpace"><input type="text" name="in_Description" value="<%= description %>" id="inputDescription"></div></td>
					<td><div class="noVSpace"><input type="text" name="in_ProjectNote" value="<%= projectNote %>" id="inputProjectNote"></div></td>
					<td>
						<div class="noVSpace">
							<select name="in_ActionCategory" id="selectCategory">
							<% 
							for (var j=0; j<catNames.instanceCount("row"); j++){
								category =  getV("catNames", "Name", j);
								%>
								<option value="<%= category %>" <%= (category == actionCat) ? "selected" : "" %>><%= category %></option>
								<%
							}
							%>
							</select>
						</div>
					</td>
					<td><div class="noVSpace"><div class="checkboxWrapper"><input type="checkbox" name="newEntry"  id="newEntry" value="true" <%= newEntry %>><img class="checkboxCover" src="checkboxCover.gif" onclick="changeNew()" /></div></div></td>
					<td><div class="noVSpace"><div class="checkboxWrapper"><input type="checkbox" name="hideEntry" id="hideEntry" value="true" <%= hideEntry %>><img class="checkboxCover" src="checkboxCover.gif"  onclick="changeHide()" /></div></div></td>
					<td><div class="space"><%= hitsLastMth %></div></td>
					<td><div class="space"><%= hitsLastWk %></div></td>
					<td><div class="space"><%= action %></div></td>
					<td><div class="space"><%= modifiedDate %></div></td>
					<td><div class="space"><%= modifiedBy %></div></td>
				</tr>
				<%
			}
			else { //Not a row being edited
				//If this row was just edited, highlight in style 'last' otherwise stipe in alternate colors
				style = i%2 ? "lite" : "dark";
				if(rs("pageAction") == "redrawAfterEdit") {
					if(id == changedID)
						style = "changed";
				}
				
				//Determine what to show in "new" and "hide" columns
				if(exclReport == 2) { //New
					newEntry = "<img src=\"checkmark.gif\" alt=\"New entry\" width=\"18\" height=\"14\" />";
					hideEntry = "";
				}
				else if (exclReport == 1) { //Hide
					newEntry = "";
					hideEntry = "<img src=\"checkmark.gif\" alt=\"New entry\" width=\"18\" height=\"14\" />";
				}
				else { //Show
					newEntry = "";
					hideEntry = "";
				}
				
				%>
				<tr class="<%= style %>" id="row<%=i%>" onmouseover="changeRowStyle('row<%=i%>', 'over')" onmouseout="changeRowStyle('row<%=i%>', '<%= style %>')"  onclick="<%= (rowToEdit != -1) ? "validateForm()" : "editRow(" + i + ")"%>">
					<td><div class="space"><%= page %></div></td>
					<td><div class="space"><%= note %></div></td>
					<td><div class="space"><%= description %></div></td>
					<td><div class="space"><%= projectNote %></div></td>
					<td><div class="space"><%= actionCat %></div></td>
					<td><div class="space"><%= newEntry %></div></td>
					<td><div class="space"><%= hideEntry %></div></td>
					<td><div class="space"><%= hitsLastMth %></div></td>
					<td><div class="space"><%= hitsLastWk %></div></td>
					<td><div class="space"><%= action %></div></td>
					<td><div class="space"><%= modifiedDate %></div></td>
					<td><div class="space"><%= modifiedBy %></div></td>
				</tr>
				<%
			}
		}
		%>
	</table>
	<!-- Current page indicator and paging controls below record listing. -->
	<div class="paging">Page <%= thisPage %> of <%= numPages %>.<br />
	<%if(User.Session("firstLine") != 1) { %>
	<a href="javascript:void(0)" onclick="goToPage(<%= firstLinePrevPage %>); return false">&lt;&lt; Previous</a>&nbsp;&nbsp;
	<%} 
	if(thisPage < numPages) { %>
	<a href="javascript:void(0)" onclick="goToPage(<%= firstLineNextPage %>); return false">Next &gt;&gt;</a>&nbsp;&nbsp;
	<%} 
	if(numPages > 3) { %>
	Page <input type="text" name="goToBox2" class="txt goTo" id="goToBox2" value="" onkeypress="return numHandle(event,false,false)">&nbsp;<a href="javascript:void(0)" onclick="goToPageFromBox(2, <%= numPages %>);return false">Go</a> &nbsp;&nbsp;<span id="goToError2" class="warning"></span>
	<%}%>
	</div>
	
	<% if(rowToEdit != -1) { %>
		<div class="buttonRow">
			<input type="button" name="cancelButton" value="Cancel" onclick="window.location.href='webReport.asp'" class="button">
			<input type="button" name="saveButton" value="Save" onclick="validateForm()" class="button">
			<input type="hidden" name="in_ExcludeReporting" value="<%= currentExclReport %>"><!-- Updated with Javascript if user alters settings of checkboxes. -->
			<input type="hidden" name="in_WebActionID" value="<%= currentID %>">
		</div>
	<%}%>
	
	<input type="hidden" name="pageAction"  id="pageAction"  value="">
	<input type="hidden" name="rowToEdit"   id="rowToEdit"   value=""> <!-- Set by Javascript at time of form submission if editing. -->
	<input type="hidden" name="firstLine"   id="firstLine"   value="<%= User.Session("firstLine") %>"> <!-- Will be overwritten by Javascript if user follows Previous/Next links. -->
	<input type="hidden" name="colWidthStr" id="colWidthStr" value=""> <!-- Set by Javascript at time of form submission. -->
</form>

<% if(rs("pageAction") == "redrawAfterEdit") { %>
	<!-- Undo last update option. (Second form on page uses duplicate field names but holds old data.) -->
	<div class="buttonRow">
	<form action="webReportSubmit.asp" method="post">
		<input type="hidden" name="in_WebActionID"      value="<%= User.Session("oldWebActionID") %>">
		<input type="hidden" name="in_ActionCategory"   value="<%= User.Session("oldActionCategory") %>">
		<input type="hidden" name="in_Description"      value="<%= User.Session("oldDescription") %>">
		<input type="hidden" name="in_ExcludeReporting" value="<%= User.Session("oldExcludeReport") %>">
		<input type="hidden" name="in_ProjectNote"      value="<%= User.Session("oldProjectNote") %>">
		<input type="submit" name="undo" value="Undo last change" class="button">
	</form>
	</div>
<%}%>

<div id="instructions">
	Instructions/notes:
	<ol id="instructions">
		<li>Columns showing horizontal arrows can be adjusted. Click on <img src="webReport.gif" alt="" /> and drag to the desired width.</li>
		<li>* Month beginning <%= formatDate(monthStart, "d mmm yy") %><br />** Week beginning <%= formatDate(weekStart, "d mmm yy") %></li>
		<li>To save changes, you can click on the Save button or, to save scrolling down to the bottom, simply click inside any other row.</li>
		<li>After making changes, click on any other row to submit the changes</li>
		<li>When updating the page, note that the columns New and Hide are actually based on a single entry in the database that accepts the values 0 (normal), 1 (hide) or 2 (new).</li>
		<li>Use the &quot;Where&quot; line of the update section to specify restrictions on the records returned e.g. "Action equals 400"</li>
		<li>You can specify up to three restrictions or &quot;filter sets&quots; on the &quot;Where&quot; line of the update section.</li>
		<li>Text entries for &quot;Where&quot; line of the update section are NOT case-sensitive.</li>
	</ol>
</div>
<% if(User.Session("Secure.Flags.ShowDebugInfo") && (User.Session("Secure.Flags.ShowDebugInfo") == "on")) { %>
<!-- #include file="showDebugInfo.asp" -->
<%}%>
</body>
</html>

