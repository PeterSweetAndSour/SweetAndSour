<? /*
dsp_database.php

This page displays the contents of the all database tables. It can handle any number of
tables, each with any number of fields. The only condition for correct operation is that
the primary key must be identified in each table
*/

//Fill an array with names of the tables in the database
$result = mysql_list_tables($dbname);
$i = 0;
while($i < mysql_num_rows($result)) {
	$tb_names[$i] = mysql_tablename($result, $i);
	$i++;
}

//Function to get primary key of specified table. Assumes key is not composite.
function getPrimaryKey($tableName) {
	//Set preliminary SQL to get field names and determine which is primary key then pass sql.
	$sql = "SELECT * FROM $tableName";
	$rs_thisTable = @mysql_query($sql);

	$keyField = "Error";
	//Check that there is a result set
	if($rs_thisTable) {
		$fields = mysql_num_fields($rs_thisTable);
		$i = 0;
		while($i < $fields) {
			$flags = mysql_field_flags($rs_thisTable, $i);
			if(strstr($flags, "primary_key")) {
				$keyField = mysql_field_name($rs_thisTable, $i);
				break;
			}
			$i++;
		}
		return $keyField;
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Lan and Peter's web site</title>
	<style>
		body {
			padding-left:10px;
			padding-top:10px;
		}
		p {
			font-size: 11px;
			font-family: Verdana, Helvetica, sans-serif;
			margin-top: 0pt;
			margin-bottom: 1pt;
		}

		.boldWhite {
			font-weight: bold;
			color: white;
		}

		h4 {
			color: #990033;
			font-size: 11pt;
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-weight: bold;
			margin-top: 0pt;
			margin-bottom: 6pt;
		}
	</style>
</head>

<body>
	<p>See the bottom for the generic code used to create this page<br><br></p>

	<?
	//For each table in the database ...
	forEach($tb_names as $tableName) {
		//Print a heading
		echo "<h3>Table '" . $tableName . "'</h3>";

		//Get primary key
		$keyFieldName = getPrimaryKey($tableName);

		if($keyFieldName == "Error") {
			echo "Could not determine primary key for table " . $tableName;
		}
		else {
			$sql = "SELECT * FROM " .  $tableName . " ORDER BY " . $keyFieldName;
			$rs_thisTable = @mysql_query($sql);

			//Check that there is a result set
			if($rs_thisTable) {
				$allSQL .= "rs_thisTable (" . @mysql_num_rows($rs_thisTable) . " records returned)<br>" . $sql . "<br><br>";

				//Print the top row of the table with headings
				$fields = mysql_num_fields($rs_thisTable);
				$i = 0;
				echo "<table border='0' cellpadding='2' cellspacing='1'>";
				echo "<tr>";
				while($i < $fields) {
					$fieldNames[$i]  = mysql_field_name($rs_thisTable, $i);
					if($fieldNames[$i] == $keyFieldName)
						echo "<td bgcolor='#000066'><p class='boldWhite'>" . $fieldNames[$i] . "*</td>"; //Mark primary key field
					else
						echo "<td bgcolor='#000066'><p class='boldWhite'>" . $fieldNames[$i] . "</td>";
					$i++;
				}
				echo "</tr>";

				//Now display the contents of the table
				$rowColor = "99CCFF"; //Pale blue
				while( $row = mysql_fetch_array( $rs_thisTable ) ) {
					$i = 0;
					echo "<tr>";
					while($i < $fields) {
						$cellContents = $row[$fieldNames[$i]];
						//Escape <,>,"
						$cellContents = str_replace("<", "&lt;", $cellContents);
						$cellContents = str_replace(">", "&gt;", $cellContents);
						$cellContents = str_replace("\"", "&quot;", $cellContents);
						echo "<td bgcolor='#" . $rowColor . "' valign='top'><p>" . $cellContents . "</p></td>";
						$i++;
					}
					echo "</tr>";
					//Alternate row colors
					if($rowColor == "99CCFF")
						$rowColor = "D3EAFF"; //Paler blue
					else
						$rowColor = "99CCFF";
				}

				//Close the table
				echo "</table>";
				echo "<p>* marks the primary key<br><br><br></p>";
			}
			else {
				echo "<p>No rs_thisTable result set for table " . $tableName . "</p><br>";
			}
		}
	}
	?>

<h3>Code used to create this page</h3>
<p>This code will work with any mySQL database since it can handle any number of tables
and any number of fields within those tables. The only requirement is that the primary
key is identified in each table so the rows can be sorted on the key. The main features
are shown below; some of the generic html is ignored.</p>
<pre>
	//Fill an array with names of the tables in the database
	$result = mysql_list_tables($dbname);
	$i = 0;
	while($i &lt; mysql_num_rows($result)) {
		$tb_names[$i] = mysql_tablename($result, $i);
		$i++;
	}

	//Function to get primary key of specified table. Assumes key is not composite.
	function getPrimaryKey($tableName) {
		//Set preliminary SQL to get field names and determine which is primary key then pass sql.
		$sql = &quot;SELECT * FROM $tableName&quot;;
		$rs_thisTable = @mysql_query($sql);

		$keyField = &quot;Error&quot;;
		//Check that there is a result set
		if($rs_thisTable) {
			$fields = mysql_num_fields($rs_thisTable);
			$i = 0;
			while($i &lt; $fields) {
				$flags = mysql_field_flags($rs_thisTable, $i);
				if(strstr($flags, &quot;primary_key&quot;)) {
					$keyField = mysql_field_name($rs_thisTable, $i);
					break;
				}
				$i++;
			}
			return $keyField;
		}
	}
</pre>
<p><br>The following is inside the &lt;body&gt; section to generate the tables:<br></p>
<pre>
	//For each table in the database ...
	forEach($tb_names as $tableName) {
		//Print a heading
		echo &quot;&lt;h4&gt;Table '&quot; . $tableName . &quot;'&lt;/h4&gt;&quot;;

		//Get primary key
		$keyFieldName = getPrimaryKey($tableName);

		if($keyFieldName == &quot;Error&quot;) {
			echo &quot;Could not determine primary key for table &quot; . $tableName;
		}
		else {
			$sql = &quot;SELECT * FROM &quot; .  $tableName . &quot; ORDER BY &quot; . $keyFieldName;
			$rs_thisTable = @mysql_query($sql);

			//Check that there is a result set
			if($rs_thisTable) {
				$allSQL .= &quot;rs_thisTable (&quot; . @mysql_num_rows($rs_thisTable) . &quot; records returned)&lt;br&gt;&quot; . $sql . &quot;&lt;br&gt;&lt;br&gt;&quot;;

				//Print the top row of the table with headings
				$fields = mysql_num_fields($rs_thisTable);
				$i = 0;
				echo &quot;&lt;table border='0' cellpadding='2' cellspacing='1'&gt;&quot;;
				echo &quot;&lt;tr&gt;&quot;;
				while($i &lt; $fields) {
					$fieldNames[$i]  = mysql_field_name($rs_thisTable, $i);
					if($fieldNames[$i] == $keyFieldName)
						echo &quot;&lt;td bgcolor='#000066'&gt;&lt;p class='boldWhite'&gt;&quot; . $fieldNames[$i] . &quot;*&lt;/td&gt;&quot;; //Mark primary key field
					else
						echo &quot;&lt;td bgcolor='#000066'&gt;&lt;p class='boldWhite'&gt;&quot; . $fieldNames[$i] . &quot;&lt;/td&gt;&quot;;
					$i++;
				}
				echo &quot;&lt;/tr&gt;&quot;;

				//Now display the contents of the table
				$rowColor = &quot;99CCFF&quot;; //Pale blue
				while( $row = mysql_fetch_array( $rs_thisTable ) ) {
					$i = 0;
					echo &quot;&lt;tr&gt;&quot;;
					while($i &lt; $fields) {
						$cellContents = $row[$fieldNames[$i]];
						//Escape &lt;,&gt;,&quot;
						$cellContents = str_replace(&quot;&lt;&quot;, &quot;&lt;&quot;, $cellContents);
						$cellContents = str_replace(&quot;&gt;&quot;, &quot;&gt;&quot;, $cellContents);
						$cellContents = str_replace(&quot;\&quot;&quot;, &quot;&quot;&quot;, $cellContents);
						echo &quot;&lt;td bgcolor='#&quot; . $rowColor . &quot;' valign='top'&gt;&lt;p&gt;&quot; . $cellContents . &quot;&lt;/p&gt;&lt;/td&gt;&quot;;
						$i++;
					}
					echo &quot;&lt;/tr&gt;&quot;;
					//Alternate row colors
					if($rowColor == &quot;99CCFF&quot;)
						$rowColor = &quot;D3EAFF&quot;; //Paler blue
					else
						$rowColor = &quot;99CCFF&quot;;
				}

				//Close the table
				echo &quot;&lt;/table&gt;&quot;;
				echo &quot;&lt;p&gt;* marks the primary key&lt;br&gt;&lt;br&gt;&lt;br&gt;&lt;/p&gt;&quot;;
			}
			else {
				echo &quot;&lt;p&gt;No rs_thisTable result set for table &quot; . $tableName . &quot;&lt;/p&gt;&lt;br&gt;&quot;;
			}
		}
	}
</pre>

</body>
</html>
