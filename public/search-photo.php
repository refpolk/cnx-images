<?php

require 'includes/settings.photos.inc.php';

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
			$query_Note = ' OR Note LIKE \'%' . $query_where . '%\'';
			
			$query_where = implode( '%\' AND Place LIKE \'%', $searchField );
			$query_Place = ' OR Place LIKE \'%' . $query_where . '%\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename . $query_Note . $query_Place;
		}
		elseif ( $option == 'exact' )
		{
			$query_Title = 'Title = \'' . $query. '\'';
			$query_Author = ' OR Author = \'' . $query. '\'';
			$query_Caption = ' OR Caption = \'' . $query. '\'';
			$query_Filename = ' OR Filename = \'' . $query. '\'';
			$query_Note = ' OR Note = \'' . $query. '\'';
			$query_Place = ' OR Place = \'' . $query. '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename . $query_Note . $query_Place;
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
			$query_Place = ' OR Place REGEXP \'' . $query_where . '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename . $query_Note . $query_Place;
		}
		
		$numberOfItems = $pdo->query("SELECT COUNT(*) FROM Photos WHERE $query_All")->fetchColumn();
		
		$pageSize = isset($_GET['s']) ? (int) $_GET['s'] : 100;
		$pageNumber = isset($_GET['p']) ? (int) $_GET['p'] : 1;
		$startPage = ($pageNumber - 1) * $pageSize;
		$numberOfPages = (int) (($numberOfItems + $pageSize - 1) / $pageSize);
	
		$statement = $pdo->query("SELECT * FROM Photos WHERE $query_All ORDER BY ID ASC LIMIT $startPage, $pageSize;");
		
		if ($numberOfItems == 0){
			$warning = 'No results found for your query!';
		}
	}
}

$title = 'Search Photos';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">		
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body>		
		<?php require 'includes/menu-photo.inc.php'; ?>
		
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
					<form  id="searchform" name="form" method="GET" action="search-photo.php">			
						<?php require 'includes/messages.inc.php'; ?>
					    <div class="form-group">
							<div class="input-group">
								<input type="text" class="input-lg form-control" name="q" value="<?php echo $query ?>" placeholder="Title / Author / Caption / Filename / Note / Place" />
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
								<?php while ($photo = $statement->fetch(PDO::FETCH_OBJ)) { ?>
								<tr>
									<td><?php echo $photo->ID; ?></td>
									<td><a href="/edit-photo.php?id=<?php echo $photo->ID; ?>"><?php echo $photo->Title; ?></a></td>
									<td><?php echo $photo->Filename; ?></td>
									<td><?php echo $photo->Author; ?></td>																
									<td class="col-xs-2">
										<?php if ($photo->Filename != '') { ?>
										<a class="zoom thumbnail" data-dialog-id="#dialog-<?php echo $photo->ID; ?>" title="Zoom" href="#">
											<img src="<?php echo $filelocation . $photo->Filename; ?>" height="100" alt="<?php echo $photo->Title;?>"/>
										</a>
										<div id="dialog-<?php echo $photo->ID; ?>" class="modal fade" role="dialog">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal">&times;</button>
										        <h4 class="modal-title"><?php echo $photo->Title;?></h4>
										      </div>
										      <div class="modal-body">
										        <p><img src="<?php if ($photo->Filename != '') { echo $filelocation . $photo->Filename; } ?>" alt="<?php echo $photo->Title;?>" style="width:100%;"/></p>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										      </div>
										    </div>
										  </div>
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