<? 
/*
=>| $showFirstPage
=>| $showLastPage
=>| $page
=>| $lastPage
=>| $showPreviousPage
=>| $showNextPage
=>| $previousPage
=>| $nextPage
=>| folderId
*/
?>
	<p class="pagination" data-folderid="<?= $folderId ?>"><?
		if($showFirstPage) {
			?><a href="#" title="First page" class="firstPage" data-page="1">&laquo;</a> <?
		}
		else {
			?>&laquo; <?
		}

		if($showPreviousPage) {
			?><a href="#" title="Previous page" class="previousPage" data-page="<?= $previousPage ?>">&lsaquo;</a> <?
		}
		else {
			?>&lsaquo; <?
		}

		?><?= $page ?> <?

		if($showNextPage) {
			?><a href="#" title="Next page" class="nextPage" data-page="<?= $nextPage ?>">&rsaquo;</a> <?
		}
		else {
			?>&rsaquo; <?
		}

		if($showLastPage) {
			?><a href="#" title="Last" class="lastPage" data-page="<?= $lastPage ?>">&raquo;</a> <?
		}
		else {
			?>&raquo; <?
		}
		?>
		&nbsp;&nbsp;(Page <?= $page ?> of <?= $lastPage ?>)
	</p>
<?