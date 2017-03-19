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

	<!--<div class="col-xs-6">-->
		
	Total <span class="badge"><?php echo $numberOfItems; ?></span> | Page size: 
	
	<select class="page-size form-control col-xs-1" title="Number of results on each page" >
		<option value="100" <?php if ($pageSize == 100) echo 'selected="selected"'; ?>>100</option>
		<option value="500" <?php if ($pageSize == 500) echo 'selected="selected"'; ?>>500</option>
		<option value="1000" <?php if ($pageSize == 1000) echo 'selected="selected"'; ?>>1000</option>
	</select>
	
	<!--| Page:
	
	</div>-->
	<div class="col-xs-6">

	<ul class="pager pagination-sm nav navbar-nav navbar-right">
	<?php if ($pageNumber > 1) { ?>
	<li class="previous"><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=1&s=" . $pageSize; ?>" title="To first page"><<</a></li>
	<li class="previous"><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber - 1) . "&s=" . $pageSize; ?>" title="To previous page"><</a></li>
	<?php } else { ?>
	<li class="disabled previous"><a href=""><<</a></li>
	<li class="disabled previous"><a href="#"><</a></li>
	<?php } ?>
	
	<li class="disabled">
		<input class="page-number" type="text" size="1" value="<?php echo $pageNumber; ?>" title="Current page" />
		<?php
		/*
		<input class="page-number" type="text" size="4" value="<?php echo $pageNumber; ?>" title="Current page" /> / 
		<span class="number-of-pages"><?php echo $numberOfPages; ?></span>
		*/
		?>
	</li>
	<li class="disabled">
		<a href="#">/ <?php echo $numberOfPages; ?></a>
	</li>
	
	<?php if ($pageNumber < $numberOfPages) { ?>
	<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . ($pageNumber + 1) . "&s=" . $pageSize; ?>" title="To next page">></a></li>
	<li><a href="<?php echo $_SERVER['PHP_SELF'] . $newQuery . "p=" . $numberOfPages . "&s=" . $pageSize; ?>" title="To last page">>></a></li>
	<?php } else { ?>
	<li class="disabled"><a href="#">></a></li>
	<li class="disabled"><a href="#">>></a></li>
	<?php } ?>
	</ul>
	
	</div>