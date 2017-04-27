<?php

require 'includes/settings.images.inc.php';

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
		<div class="container">

		<?php require 'includes/menu.inc.php'; ?>
		
		<h1><?php echo $title; ?></h1>

		<form id="searchform" name="form" method="GET" action="search-image.php">

			<?php require 'includes/messages.inc.php'; ?>
			
			<table style="width=100%">
				<tr>
					<td></td>	
					<td>
						<input type="text" size="115" maxlength="115" name="q" value="<?php echo $query ?>"  placeholder="Title / Filename / Author / Caption / Note" />
						<input type="submit" value="Search Images" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
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
				<?php while ($image = $statement->fetch(PDO::FETCH_OBJ)) { ?>
				<tr>
					<td valign="top">
						ID: <?php echo $image->ID; ?><br />
						Title: <a href="/edit-image.php?id=<?php echo $image->ID; ?>"><?php echo $image->Title; ?></a><br />
						Filename: <?php echo $image->Filename; ?><br />
						Author: <?php echo $image->Author; ?>
					</td>
					<td>
						<?php echo "FL:" . $filelocation . $image->Filename; ?>
						<?php if ($image->Filename != '' && file_exists($filelocation . $image->Filename)) { ?>
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
			</table>
			
			<?php require 'includes/pager.inc.php'; ?>
			
		<?php } ?>

		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/search.js"></script>
		<script src="scripts/pager.js"></script>
		
	</body>
</html>