<?php

	require 'includes/settings.images.inc.php';
	
	$numberOfItems = $pdo->query('SELECT COUNT(*) FROM Images;')->fetchColumn();
	$pageSize = isset($_GET['s']) ? (int) $_GET['s'] : 100;
	$pageNumber = isset($_GET['p']) ? (int) $_GET['p'] : 1;
	$startPage = ($pageNumber - 1) * $pageSize;
	$numberOfPages = (int) (($numberOfItems + $pageSize - 1) / $pageSize);
	
	$statement = $pdo->query("SELECT * FROM Images ORDER BY ID ASC LIMIT {$startPage}, {$pageSize};");
	
	$title = 'Browse Images';
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
							<span class="badge"><?php echo $numberOfItems; ?></span>
						</h1>
					</div>
				</div>				
			</div>
			
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
								<td class="col-xs-2">
									<?php if ($image->Filename != '') { ?>
									<a class="zoom thumbnail" data-dialog-id="#dialog-<?php echo $image->ID; ?>" title="Zoom" href="#">
										<img src="<?php echo $filelocation . $image->Filename; ?>" height="100" alt="<?php echo $image->Title;?>"/>
									</a>
										<div id="dialog-<?php echo $image->ID; ?>" class="modal fade" role="dialog">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal">&times;</button>
										        <h4 class="modal-title"><?php echo $image->Title;?></h4>
										      </div>
										      <div class="modal-body">
										        <p><img src="<?php if ($image->Filename != '') { echo $filelocation . $image->Filename; } ?>" alt="<?php echo $image->Title;?>" style="width:100%;"/></p>
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
		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/search.js"></script>		
		<script src="scripts/pager.js"></script>
	
	</body>
</html>