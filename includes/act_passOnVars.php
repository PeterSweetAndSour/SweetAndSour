<? /*
	PassOnVars.php
	This page passes on whatever form variables are received by making a form with
	hidden form variables and then submitting the form. Useful on multi-part forms
	and where form action can't be completed and user has to be sent back to form
	with form variables so the form can be populated with the values previously
	entered. 
	
	Variables:
	=>| formAction
	=>| displayMsg		if not "", will display in Javascript alert.
	=>| any number of form variables
	|=> one form variable for each received
	*/
	
	//Cycle through $HTTP_POST_VARS and make hidden form variable 
?>
	<html><head></head>
	<body>
		<form action="<?= $formAction ?>" method="post">
			<? while($element = each($HTTP_POST_VARS)) { ?>
			   <input type="hidden" name="<?= $element["key"] ?>" value="<?= $element["value"] ?>">
			<? } ?>
		</form>
		<script type="text/javascript">
			<? if($displayMsg != "") { ?>
				alert("<?= $displayMsg ?>");
			<? } ?>
			//document.forms[0].submit();
		</script>
	</body>
	</html>
