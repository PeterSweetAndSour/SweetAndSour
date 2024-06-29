<?php 
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
	<p class="pagination" data-folderid="<?= $folderId ?>"><?php
		if($showFirstPage) {
			?><a href="#" title="First page" class="firstPage" data-page="1">&laquo;</a> <?php
		}
		else {
			?>&laquo; <?php
		}

		if($showPreviousPage) {
			?><a href="#" title="Previous page" class="previousPage" data-page="<?= $previousPage ?>">&lsaquo;</a> <?php
		}
		else {
			?>&lsaquo; <?php
		}

		?><?= $page ?> <?php

		if($showNextPage) {
			?><a href="#" title="Next page" class="nextPage" data-page="<?= $nextPage ?>">&rsaquo;</a> <?php
		}
		else {
			?>&rsaquo; <?php
		}

		if($showLastPage) {
			?><a href="#" title="Last" class="lastPage" data-page="<?= $lastPage ?>">&raquo;</a> <?php
		}
		else {
			?>&raquo; <?php
		}
		?>
		&nbsp;&nbsp;(Page <?= $page ?> of <?= $lastPage ?>)
	</p>
<?