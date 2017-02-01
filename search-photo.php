<?php

require 'includes/settings.photos.inc.php';

if (isset($_GET['q'])) {
	
	$query = $_GET['q'];
	$option = $_GET['o'];
	
	if ($query == '') {
		
		$warning = 'Your query cannot be empty!';
		
	} else {

		if ( $option == 'all' )
		{
			$searchField = explode(' ',$query);

			$query_where = implode( '%\' AND Title LIKE \'%', $searchField );
			$query_Title = 'Title LIKE \'%' . $query_where . '%\'';

			$query_where = implode( '%\' AND Authors LIKE \'%', $searchField );
			$query_Author = ' OR Authors LIKE \'%' . $query_where . '%\'';

			$query_where = implode( '%\' AND Caption LIKE \'%', $searchField );
			$query_Caption = ' OR Caption LIKE \'%' . $query_where . '%\'';

			$query_where = implode( '%\' AND Filename LIKE \'%', $searchField );
			$query_Filename = ' OR Filename LIKE \'%' . $query_where . '%\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		elseif ( $option == 'exact' )
		{
			$query_Title = 'Title = \'' . $query. '\'';
			$query_Author= ' OR Authors = \'' . $query. '\'';
			$query_Caption= ' OR Caption = \'' . $query. '\'';
			$query_Filename= ' OR Filename = \'' . $query. '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		elseif ( $option == 'any' )
		{
			$searchField = explode(' ',$query);
		
			$query_where = implode( '\|', $searchField );

			$query_Title = 'Title REGEXP \'' . $query_where . '\'';
			$query_Author = ' OR Authors REGEXP \'' . $query_where . '\'';
			$query_Caption = ' OR Caption REGEXP \'' . $query_where . '\'';
			$query_Filename = ' OR Filename REGEXP \'' . $query_where . '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		
		$numberOfItems = $pdo->query("SELECT COUNT(*) FROM Photos WHERE $query_All")->fetchColumn();
		
		$pageSize = isset($_GET['s']) ? (int) $_GET['s'] : 100;
		$pageNumber = isset($_GET['p']) ? (int) $_GET['p'] : 1;
		$startPage = ($pageNumber - 1) * $pageSize;
		$numberOfPages = (int) (($numberOfItems + $pageSize - 1) / $pageSize);
	
		$statement = $pdo->query("SELECT * FROM Photos WHERE $query_All ORDER BY ID ASC LIMIT $startPage, $pageSize;");
	}
}

$title = 'Search Photos';

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

		<form id="searchform" name="form" method="GET" action="search-photo.php">
			
			<?php require 'includes/messages.inc.php'; ?>
						
			<table style="width=100%">
				<tr>	
					<td>
						<input type="text" size="115" maxlength="115" name="q" value="<?php echo $query ?>" placeholder="Title / Authors / Caption / Filename" />
						<input type="submit" value="Search Photos" />
					</td>
				</tr>
				<tr>
					<td>
						<fieldset>
							<legend>Advanced Options:</legend>
							<input type="radio" name="o" value="all" <?php if ($option == 'all' || !isset($option)) { echo 'checked'; } ?>>
							Include all terms in result (default)    
							<input type="radio" name="o" value="exact" <?php if ($option == 'exact') { echo 'checked'; } ?>>
							Exact
							<input type="radio" name="o" value="any" <?php if ($option == 'any') { echo 'checked'; } ?>>
							Find results with any one of the terms
						</fieldset>
					</td>
				</tr>
			</table>
		</form>
		<?php if (isset($statement)) { ?>
		
			<?php require 'includes/pager.inc.php'; ?>
		
			<table>
				<?php while ($photo = $statement->fetch(PDO::FETCH_OBJ)) { ?>
				<tr>
					<td valign="top">
						ID: <?php echo $photo->ID; ?><br />
						Title: <a href="/edit-photo.php?id=<?php echo $photo->ID; ?>"><?php echo $photo->Title; ?></a><br />
						Filename: <?php echo $photo->Filename; ?><br />
						Authors: <?php echo $photo->Authors; ?>
					</td>
					<td>
						<img height="100" src="<?php echo $filelocation . $photo->Filename; ?>" alt="<?php echo $photo->Title; ?>" />
					</td>
				</tr>
				<?php } ?>
			</table>
		
			<?php require 'includes/pager.inc.php'; ?>
		
		<?php } ?>

		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/search.js"></script>
		<script src="scripts/pager.js"></script>
		
	</body>
</html>