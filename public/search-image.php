<?php

require 'includes/settings.images.inc.php';

if (isset($_GET['q'])) {
	
	$query = $_GET['q'];
	$option = $_GET['o'];
	
	$numberOfItems = 0;
	
	if ($query == '') {
		
		$warning = 'Your query cannot be empty!';
		
	} else {

		if ( $option == 'all' )
		{
			$searchField = explode(' ',$query);

			$query_where = implode( '%\' AND Title LIKE \'%', $searchField );
			$query_Title = 'Title LIKE \'%' . $query_where . '%\'';

			$query_where = implode( '%\' AND Author LIKE \'%', $searchField );
			$query_Author = ' OR Author LIKE \'%' . $query_where . '%\'';

			$query_where = implode( '%\' AND Caption LIKE \'%', $searchField );
			$query_Caption = ' OR Caption LIKE \'%' . $query_where . '%\'';

			$query_where = implode( '%\' AND Filename LIKE \'%', $searchField );
			$query_Filename = ' OR Filename LIKE \'%' . $query_where . '%\'';
			
			$query_where = implode( '%\' AND Note LIKE \'%', $searchField );
			$query_Filename = ' OR Note LIKE \'%' . $query_where . '%\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		elseif ( $option == 'exact' )
		{
			$query_Title = 'Title = \'' . $query. '\'';
			$query_Author = ' OR Author = \'' . $query. '\'';
			$query_Caption = ' OR Caption = \'' . $query. '\'';
			$query_Filename = ' OR Filename = \'' . $query. '\'';
			$query_Note = ' OR Note = \'' . $query. '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename . $query_Note;
		}
		elseif ( $option == 'any' )
		{
			$searchField = explode(' ',$query);
		
			$query_where = implode( '\|', $searchField );

			$query_Title = 'Title REGEXP \'' . $query_where . '\'';
			$query_Author = ' OR Author REGEXP \'' . $query_where . '\'';
			$query_Caption = ' OR Caption REGEXP \'' . $query_where . '\'';
			$query_Filename = ' OR Filename REGEXP \'' . $query_where . '\'';
			$query_Note = ' OR Note REGEXP \'' . $query_where . '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename . $query_Note;
		}
		
		$numberOfItems = $pdo->query("SELECT COUNT(*) FROM Images WHERE $query_All")->fetchColumn();
		
		$pageSize = isset($_GET['s']) ? (int) $_GET['s'] : 100;
		$pageNumber = isset($_GET['p']) ? (int) $_GET['p'] : 1;
		$startPage = ($pageNumber - 1) * $pageSize;
		$numberOfPages = (int) (($numberOfItems + $pageSize - 1) / $pageSize);
	
		$statement = $pdo->query("SELECT * FROM Images WHERE $query_All ORDER BY ID ASC LIMIT $startPage, $pageSize;");
		
		if ($numberOfItems == 0){
			$warning = 'No results found for your query!';
		}		
	}
}

$title = 'Search Images';

?>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body>
		<?php require 'includes/menu-image.inc.php'; ?>
		
		<div class="container">
		
			<div class="row">
				<div class="col-xs-12">	
					<div class="page-header">
						<h1>
							<?php echo $title; ?>
							<?php if (isset($statement)) { ?>
								<span class="badge"><?php echo $numberOfItems; ?></span>
							<?php } ?>
						</h1>
					</div>
				</div>					
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<form role="form" id="searchform" name="form" method="GET" action="search-image.php">

						<?php require 'includes/messages.inc.php'; ?>
					    <div class="form-group">
							<div class="input-group">
								<input type="text" class="form-control input-lg" name="q" value="<?php echo $query ?>" placeholder="Title / Author / Caption / Filename / Note" />
								<span class="input-group-btn">
									<button class="btn btn-lg btn-primary" type="submit">Search</button>
								</span>
							</div>
					    </div>
					    <div class="form-group">
							<div>
								<label class="radio-inline">
									<input type="radio" name="o" value="all" <?php if ($option == 'all' || !isset($option)) { echo 'checked'; } ?>>
									Include all terms in result
								</label>
								<label class="radio-inline">
									<input type="radio" name="o" value="exact" <?php if ($option == 'exact') { echo 'checked'; } ?>>
									Exact						
								</label>
								<label class="radio-inline">
									<input type="radio" name="o" value="any" <?php if ($option == 'any') { echo 'checked'; } ?>>
									Find results with any one of the terms					
								</label>
							</div>
						</div>
					</form>
				</div>
			</div>
						
			<?php if (isset($statement) && $statement->rowCount() > 0) { ?>
					
				<div class="row">
					<?php require 'includes/pager.inc.php'; ?>
				</div>

				<div class="row">
					<div class="col-xs-12">				
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
					     		   	<th>ID</th>
					     		   	<th>Title</th>
					     		   	<th>Filename</th>
					     		   	<th>Author</th>																
					     		  	<th>Thumbnail</th>
					  			</tr>
					 	   	</thead>
							<tbody>						
								<?php while ($image = $statement->fetch(PDO::FETCH_OBJ)) { ?>
								<tr>
									<td><?php echo $image->ID; ?></td>
									<td><a href="/edit-image.php?id=<?php echo $image->ID; ?>"><?php echo $image->Title; ?></a></td>
									<td><?php echo $image->Filename; ?></td>
									<td><?php echo $image->Author; ?></td>																
									<td>
										<?php if ($image->Filename != '') { ?>
										<a class="zoom" data-dialog-id="#dialog-<?php echo $image->ID; ?>" title="Zoom" href="#">
											<img src="<?php echo $filelocation . $image->Filename; ?>" height="100" alt="<?php echo $image->Title;?>"/>
										</a>
										<div class="dialog" id="dialog-<?php echo $image->ID; ?>" title="<?php echo $image->Title;?>">
											<p><img src="<?php echo $filelocation . $image->Filename; ?>" height="500" alt="<?php echo $image->Title;?>"/></p>
										</div>
										<?php } ?>						
									</td>
								</tr>
								<?php } ?>
							</tbody>						
						</table>
					</div>
				</div>
						
				<div class="row">
					<?php require 'includes/pager.inc.php'; ?>
				</div>

			<?php } ?>			

		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/search.js"></script>
		<script src="scripts/pager.js"></script>
		
	</body>
</html>