<?php 

$query = $_SERVER['QUERY_STRING'];
$newQuery = '?';
		
if ($query != '') {
	
	$newQueryArray = array();
	parse_str($query, $queryArray);
			
	foreach ($queryArray as $key => $value) {
	
		if ($key != 'p' && $key != 's') {
			$newQueryArray[$key] = $value;
		}
	}
	
	if (count($newQueryArray) > 0) {
		$newQuery .= http_build_query($newQueryArray) . '&';
	}
}

$start = 0; 
$end = 0;

if ($pageNumber < 3) {
	$start = 0; 
	$end = 6;
} else if ($pageNumber > $numberOfPages - 3) {
	$start = $numberOfPages - 4; 
	$end = $numberOfPages + 1;
} else {
	$start = $pageNumber - 2; 
	$end = $pageNumber + 3;
}

?>

<?php if ($numberOfItems > 100) { ?>
<div class="col-xs-2">
	<ul class="pagination pagination-sm">
	    <li class="<?php if ($pageSize == 100) echo "active"; ?>"><a title="100 results per page" class="page-size page-size-1" href="#">100</a></li>
	    <li class="<?php if ($pageSize == 500) echo "active"; ?>"><a title="500 results per page" class="page-size page-size-2" href="#">500</a></li>
	    <li class="<?php if ($pageSize == 1000) echo "active"; ?>"><a title="1000 results per page" class="page-size page-size-3" href="#">1000</a></li>
	</ul>
</div>
<?php } ?>
<?php if ($numberOfPages > 1) { ?>
<div class="col-xs-10 text-right">
	<ul class="pagination pagination-sm">
		<?php if ($pageNumber > 1) { ?>
		<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=1&s=" . $pageSize; ?>" title="First page">&laquo;</a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber - 1) . "&s=" . $pageSize; ?>" title="Previous page">&lsaquo;</a></li>
		<?php } ?>
	
		<?php
		for ($i = $start; $i < $end; $i++) { 
			if ($i > 0 && $i <= $numberOfPages) {
				if ($i == $pageNumber) {
					echo "<li class=\"active\"><a title=\"Page $i\" href=\"{$_SERVER['PHP_SELF']}{$newQuery}p=$i&s=$pageSize\">$i</a></li>";
				} else {
					echo "<li><a title=\"Page $i\" href=\"{$_SERVER['PHP_SELF']}{$newQuery}p=$i&s=$pageSize\">$i</a></li>";
				}

			}
		}	
		?>
	
		<?php if ($pageNumber < $numberOfPages) { ?>
		<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber + 1) . "&s=" . $pageSize; ?>" title="Next page">&rsaquo;</a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . $numberOfPages . "&s=" . $pageSize; ?>" title="Last page">&raquo;</a></li>
		<?php } ?>
	</ul>
</div>
<?php } ?>