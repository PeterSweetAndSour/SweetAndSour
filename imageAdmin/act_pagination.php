<? /* act_pagination.php
=>| $filesInFolder
=>| $_GET["page"]
|=> $imagesPerPage
|=> $showFirstPage
|=> $showLastPage
|=> $page
|=> $lastPage
|=> $showPreviousPage
|=> $showNextPage
|=> $previousPage
|=> $nextPage
*/

$imagesPerPage = 10;
$page = 1;
$lastPage = ceil( count($filesInFolder)/$imagesPerPage);
if(isset($_GET["page"])) {
	$page = $_GET["page"];
}

$showFirstPage = false;
$showPreviousPage = false;
$showNextPage = false;
$showLastPage = false;

$previousPage = 1;
if($page > 1) {
	$showPreviousPage = true;
	$previousPage = $page - 1;
}
if($previousPage > 1) {
	$showFirstPage = true;
}

$nextPage = 1;
if($page < $lastPage) {
	$showNextPage = true;
	$nextPage = $page + 1;
}
if($nextPage < $lastPage) {
	$showLastPage = true;
}
?>