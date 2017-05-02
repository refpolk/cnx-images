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
		<?php require 'includes/menu.inc.php'; ?>
		
		<div class="container">

			<div class="page-header">
				<h1>
					<?php echo $title; ?>
					<span class="badge"><?php echo $numberOfItems; ?></span>
				</h1>
			</div>
			
			<div class="row">
	
			<?php require 'includes/pager.inc.php'; ?>

			<table class="table table-bordered">
				<thead>
					<tr>
		     		   	<th>ID</th>
		     		  	<th>Title</th>
						<th>Filename</th>
						<th>Author</th>
		  			</tr>
		 	   	</thead>
		 	  	<tfoot>
					<?php while ($image = $statement->fetch(PDO::FETCH_OBJ)) { ?>
		  			<tr>
		     		   	<td><?php echo $image->ID; ?></td>
		     		   	<td><a href="/edit-image.php?id=<?php echo $image->ID; ?>"><?php echo $image->Title; ?></a></td>
						<td><?php echo $image->Filename; ?></td>
						<td><?php echo $image->Author; ?></td>
		  			</tr>
					<?php } ?>
		 		</tbody>
			</table>
	
			<?php require 'includes/pager.inc.php'; ?>
			
			</div>
	
		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/pager.js"></script>
	
	</body>
</html>