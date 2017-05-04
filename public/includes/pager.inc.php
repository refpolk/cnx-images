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

<div class="col-xs-2">
	<label for="page-size">Page size</label>
	<select name="page-size" class="page-size form-control" title="Number of results on each page" >
		<option value="100" <?php if ($pageSize == 100) echo 'selected="selected"'; ?>>100</option>
		<option value="500" <?php if ($pageSize == 500) echo 'selected="selected"'; ?>>500</option>
		<option value="1000" <?php if ($pageSize == 1000) echo 'selected="selected"'; ?>>1000</option>
	</select>
</div>
		
<div class="col-xs-10 text-right">
	<ul class="pagination pagination-sm">
		<?php if ($pageNumber > 1) { ?>
		<li class="previous"><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=1&s=" . $pageSize; ?>" title="To first page"><<</a></li>
		<li class="previous"><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber - 1) . "&s=" . $pageSize; ?>" title="To previous page"><</a></li>
		<?php } else { ?>
		<li class="disabled previous"><a href=""><<</a></li>
		<li class="disabled previous"><a href="#"><</a></li>
		<?php } ?>
	
		<?php
		for ($i = $start; $i < $end; $i++) { 
			if ($i > 0 && $i <= $numberOfPages) {
				if ($i == $pageNumber) {
					echo "<li class=\"active\"><a href=\"{$_SERVER['PHP_SELF']}{$newQuery}p=$i&s=$pageSize\">$i</a></li>";
				} else {
					echo "<li><a href=\"{$_SERVER['PHP_SELF']}{$newQuery}p=$i&s=$pageSize\">$i</a></li>";
				}

			}
		}	
		?>
	
		<?php if ($pageNumber < $numberOfPages) { ?>
		<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber + 1) . "&s=" . $pageSize; ?>" title="To next page">></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . $numberOfPages . "&s=" . $pageSize; ?>" title="To last page">>></a></li>
		<?php } else { ?>
		<li class="disabled"><a href="#">></a></li>
		<li class="disabled"><a href="#">>></a></li>
		<?php } ?>
	</ul>
</div>