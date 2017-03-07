<?php

	require 'includes/settings.photos.inc.php';
	
	$numberOfItems = $pdo->query('SELECT COUNT(*) FROM Photos;')->fetchColumn();
	$pageSize = isset($_GET['s']) ? (int) $_GET['s'] : 100;
	$pageNumber = isset($_GET['p']) ? (int) $_GET['p'] : 1;
	$startPage = ($pageNumber - 1) * $pageSize;
	$numberOfPages = (int) (($numberOfItems + $pageSize - 1) / $pageSize);
	
	$statement = $pdo->query("SELECT * FROM Photos ORDER BY ID ASC LIMIT {$startPage}, {$pageSize};");
	
	$title = 'Browse Photos';
?>
<html>
	<head>
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body>
		<div class="container">
		
			<?php require 'includes/menu.inc.php'; ?>

			<h1><?php echo $title; ?></h1>
	
			<?php require 'includes/pager.inc.php'; ?>

			<table style="width=100%">
				<thead>
					<tr>
		     		   	<th>ID</th>
		     		  	<th>Title</th>
						<th>Filename</th>
						<th>Author</th>
		  			</tr>
		 	   	</thead>
		 	  	<tfoot>
					<?php while ($photo = $statement->fetch(PDO::FETCH_OBJ)) { ?>
		  			<tr>
		     		   	<td><?php echo $photo->ID; ?></td>
		     		   	<td><a href="/edit-photo.php?id=<?php echo $photo->ID; ?>"><?php echo $photo->Title; ?></a></td>
						<td><?php echo $photo->Filename; ?></td>
						<td><?php echo $photo->Author; ?></td>
		  			</tr>
					<?php } ?>
		 		</tbody>
			</table>
	
			<?php require 'includes/pager.inc.php'; ?>
	
		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/pager.js"></script>
		
	</body>
</html>