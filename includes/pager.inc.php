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
	
	?>

	Total: <?php echo $numberOfItems; ?> | Page size: 
	
	<select class="page-size" title="Number of results on each page" >
		<option value="100" <?php if ($pageSize == 100) echo 'selected="selected"'; ?>>100</option>
		<option value="500" <?php if ($pageSize == 500) echo 'selected="selected"'; ?>>500</option>
		<option value="1000" <?php if ($pageSize == 1000) echo 'selected="selected"'; ?>>1000</option>
	</select>
	
	| Page:

	<?php if ($pageNumber > 1) { ?>
	<a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=1&s=" . $pageSize; ?>" title="To first page"><<</a>
	<a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber - 1) . "&s=" . $pageSize; ?>" title="To previous page"><</a>
	<?php } else { echo "<< <"; } ?>
	
	<input class="page-number" type="text" size="4" value="<?php echo $pageNumber; ?>" title="Current page" /> / <span class="number-of-pages"><?php echo $numberOfPages; ?></span>
	
	<?php if ($pageNumber < $numberOfPages) { ?>
	<a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber + 1) . "&s=" . $pageSize; ?>" title="To next page">></a>
	<a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . $numberOfPages . "&s=" . $pageSize; ?>" title="To last page">>></a>
	<?php } else { echo "> >>"; } ?>