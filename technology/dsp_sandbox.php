<? 
//dsp_sandbox.php
?>

<div class="story">
	<p class="endText">This is a place for me to try things.</p>
</div>
<h3>Great Circle Mapper</h3>
<div class="story">
	<p>Get the distance and path &ldquo;as the crow flies&rdquo; between two points.</p>
	<form action="index.aspx?fuseAction=sandbox" method="post">
		<div class="inputRow" id="fromField">
			<input type="text" class="text" id="from" name="from" placeholder="Enter airport code or city" length="40" maxlength="40" /> 
			<label for="from">From (enter city or 3-letter airport code)</label>
			<div /><!-- For suggestions -->
		</div>
		<div class="inputRow" id="toField">
			<input type="text" class="text" id="from" name="from" placeholder="Enter airport code or city" length="40" maxlength="40" /> 
			<label for="from">To (enter city or 3-letter airport code) </label>
			<div /><!-- For suggestions -->
		</div>
		<div class="actionRow">
			<input type="button" value="Map it!" />
		</div>
		<div id="results" />
	</form>
</div>


