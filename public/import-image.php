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

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		elseif ( $option == 'exact' )
		{
			$query_Title = 'Title = \'' . $query. '\'';
			$query_Author= ' OR Author = \'' . $query. '\'';
			$query_Caption= ' OR Caption = \'' . $query. '\'';
			$query_Filename= ' OR Filename = \'' . $query. '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		elseif ( $option == 'any' )
		{
			$searchField = explode(' ',$query);
		
			$query_where = implode( '\|', $searchField );

			$query_Title = 'Title REGEXP \'' . $query_where . '\'';
			$query_Author = ' OR Author REGEXP \'' . $query_where . '\'';
			$query_Caption = ' OR Caption REGEXP \'' . $query_where . '\'';
			$query_Filename = ' OR Filename REGEXP \'' . $query_where . '\'';

			$query_All = $query_Title . $query_Author . $query_Caption . $query_Filename;
		}
		
		$numberOfItems = $pdo->query("SELECT COUNT(*) FROM Images WHERE $query_All")->fetchColumn();
		
		$pageSize = isset($_GET['s']) ? (int) $_GET['s'] : 100;
		$pageNumber = isset($_GET['p']) ? (int) $_GET['p'] : 1;
		$startPage = ($pageNumber - 1) * $pageSize;
		$numberOfPages = (int) (($numberOfItems + $pageSize - 1) / $pageSize);
	
		$statement = $pdo->query("SELECT * FROM Images WHERE $query_All ORDER BY ID ASC LIMIT $startPage, $pageSize;");
	}
}

$title = 'Import Images';

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
		
			<form method="POST" enctype="multipart/form-data" action="import-image.php" >
			
				<?php require 'includes/messages.inc.php'; ?>
			
				<input type="file" name="csv" value="" />
				<input type="submit" name="submit" value="Save" />
			</form>

		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		
	</body>
</html>