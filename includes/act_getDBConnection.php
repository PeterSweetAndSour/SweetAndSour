<? /*
act_getDBConnection.php
Establish database connection

Variables:
=>| $dbserver	database host name "localhost" etc.
=>| $dbuser	database user account
=>| $dbpassword	database password
=>| $dbname     database to use
*/

$connectionOK = false;
$dbconnection = @mysql_connect( $dbserver, $dbuser, $dbpassword );

if($dbconnection) {
	//Confirm connection to particular database is possible
	if(! @mysql_select_db($dbname) ) {
		echo "<p>Unable to connect to the " . $dbname . " database!";
		exit();
	}
}
else {
	echo "<p>Unable to connect to the " . $dbserver . " server!";
	exit();
} 
?>