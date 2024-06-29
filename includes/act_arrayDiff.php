<?php 
/* my_array_diff.php
   The regular array_diff function doesn't appear to work so I'll make my own
   for practice. 
   
   Function my_array_diff(array1, array2) finds everything in array1 that is 
   not in array2.
 */

function my_array_diff($array1, $array2) {
	//Test to make sure that both inputs are arrays
	if(! is_array($array1))
		exit("The first input to my_array_diff is not an array!");
	if(! is_array($array2))
		exit("The second input to my_array_diff is not an array!");
	
	//Declare a new array to hold elements from array1 not in array2
	$surplusElements = array();
	
	for($i=0; $i<count($array1); $i++) {
		if(! in_array( $array1[$i], $array2)) {
			array_push($surplusElements, $array1[$i] );
		}
	}
	return $surplusElements;
}

?>
