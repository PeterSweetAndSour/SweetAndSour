<?php
/*act_createImage.php
Dynamically creates an image (a square of a specified color).
Principle use is to be stretched as needed to fill space below menu.

Variables:
=>| hexColor  such as "#CC66FF".
*/
$img = imageCreate(10,10);

$red = hexdec(substr($hexColor,0,2));
$green = hexdec(substr($hexColor,2,2));
$blue = hexdec(substr($hexColor,4,2));

$fillColor = imageColorAllocate($img,$red,$green,$blue);

imagefill($img,0,0,$fillColor);

Header("Content-Type: image/png");
imagePNG($img);
?>
