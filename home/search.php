<?php
//Pass search parameters on to google
header("Location:http://www.google.com/search?hl=en&q=" . urlencode($_POST["search"]) . "+site%3Asweetandsour.org");
?>
<p>This is the search page but you should not see this text as you are supposed to get redirected.</p>