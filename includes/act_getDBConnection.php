<? /*
act_getDBConnection.php
Establish database connection

Variables:
=>| $dbserver	database host name "localhost" etc.
=>| $dbuser	database user account
=>| $dbpassword	database password
=>| $dbname     database to use
*/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

/* check connection */
if (mysqli_connect_errno()) {
    echo "<p>Unable to connect to the database!";
    exit();
}
?>