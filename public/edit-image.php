<?php

require 'includes/settings.images.inc.php';

function image_exists($pdo, $image) {
	
	if ($image->ID != '') {
		$statement = $pdo->prepare("SELECT * FROM Images WHERE Title=? AND Author=? AND Filename=? AND URL=? AND ID<>?;");
		$statement->execute(array($image->Title, $image->Author, $image->Filename, $image->URL, $image->ID));
	} else {
		$statement = $pdo->prepare("SELECT * FROM Images WHERE Title=? AND Author=? AND Filename=? AND URL=?;");
		$statement->execute(array($image->Title, $image->Author, $image->Filename, $image->URL));
	}
	
	return ($statement->rowCount() > 0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$image = (object) array(	
		'ID' => $_POST['id'],
		'Title' => $_POST['title'],
		'Author' => $_POST['author'],
		'Year' => $_POST['year'],
		'Source' => $_POST['source'],
		'Caption' => $_POST['caption'],
		'Note' => $_POST['note'],
		'Publishist' => $_POST['publishist'],
		'Copyright' => $_POST['copyright'],
		'Marked' => $_POST['marked'],
		'Filename' => $_POST['filename'],
		'URL' => $_POST['url'],
		'ELibrary' => $_POST['elibrary']
	);
	
	$update = isset($_POST['id']);
	
	if ($image->Title == '') {
		
		$warning = 'Your image cannot be saved: the title must be filled out.';
		
	} else {
	
		if ($update) {
			
			if (image_exists($pdo, $image)) {
			
				$warning = 'Your changes cannot be saved: an image with the same title, author, filename and url already exists.';
			
			} else {

				try {
					$statement = $pdo->prepare("UPDATE Images SET Title=?, Filename=?, URL=?, Author=?, Year=?, Source=?, ELibrary=?, Caption=?, Note=?, Publishist=?, Copyright=?, Marked=? WHERE ID=?;");
				
					$statement->execute(array($image->Title, $image->Filename, $image->URL, $image->Author, $image->Year, $image->Source, $image->ELibrary, $image->Caption, $image->Note, $image->Publishist, $image->Copyright, $image->Marked, $image->ID));	
					
					$message = 'Your changes have been saved successfully!';
					
				} catch (Exception $e) {
					$error = "ERROR: $e";
				}
			}
		} else {
			
			if (image_exists($pdo, $image)) {
			
				$warning = 'Your image cannot be saved: a photo with the same title, authors, filename and url already exists.';
			
			} else {
				
				try {			
					$statement = $pdo->prepare("INSERT INTO Images (Title, Filename, URL, Author, Year, Source, ELibrary, Caption, Note, Publishist, Copyright, Marked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
			
					$statement->execute(array($image->Title, $image->Filename, $image->URL, $image->Author, $image->Year, $image->Source, $image->ELibrary, $image->Caption, $image->Note, $image->Publishist, $image->Copyright, $image->Marked));
					
					$message = 'Your image has been saved successfully!';
				
					header('Location: edit-image.php?id=' . $pdo->lastInsertId());
					
				} catch (Exception $e) {
					$error = "ERROR: $e";
				}
			}
		}
	}
} else {
	
	$update = isset($_GET['id']);
	
	if ($update) {

		$id = $_GET['id'];
		$statement = $pdo->query("SELECT * FROM Images WHERE ID = $id;");

		if ($statement && $statement->rowCount() > 0) {
			
			$image = $statement->fetch(PDO::FETCH_OBJ);
			
		} else {
			
			$error = "The image $id doesn't exist.";
		}
	}
}

$title = ($update ? 'Edit' : 'Add') . ' Image';

?>
<html>
	<head>
		<?php require 'includes/styles.inc.php'; ?>
		<title><?php echo $title; ?></title>
	</head>
	<body data-mode="<?php echo ($update ? 'Edit' : 'Add'); ?>">
		<div class="container">

		<?php require 'includes/menu.inc.php'; ?>
	
		<h1><?php echo $title; ?></h1>

		<form name="form" method="POST" action="edit-image.php<?php if ($update) { echo "?id=$image->ID"; } ?>">
		
			<?php require 'includes/messages.inc.php'; ?>

			<table style="width=100%">
				<?php if ($update) { ?>
				<tr>
					<td>ID</td>	
					<td colspan="3"><?php echo $image->ID;?><input type="hidden" name="id" value="<?php echo $image->ID;?>" /></td>
				</tr>
				<?php } ?>			
				<tr>
					<td>Title <span class="mandatory edit-only">*</span></td>	
					<td><input type="text" size="100" maxlength="115" name="title" value="<?php echo $image->Title;?>"></td>
				</tr>
				<tr>
					<td>Author</td>
					<td><input type="text" size="100" maxlength="100" name="author" value="<?php echo $image->Author;?>"></td>
				</tr>
				<tr>
					<td>Year</td>
					<td><input type="text" name="year" size="100" maxlength="5" value="<?php echo $image->Year;?>"></td>
				</tr>
				<tr>
					<td>Source</td>
					<td><input type="text" size="100" maxlength="50"  name="source" value="<?php echo $image->Source;?>"></td>
				</tr>
				<tr>
					<td>ELibrary (T/F)</td>
					<td>
						<input type="radio" name="elibrary" value="T" <?php if ($image->ELibrary == 'T') echo 'checked';?> /> T<br />
						<input type="radio" name="elibrary" value="F" <?php if ($image->ELibrary == 'F' || !$update) echo 'checked';?> /> F
					</td>
				</tr>
				<tr>
					<td>Caption</td>
					<td><textarea name="caption" rows="5" cols="86"><?php echo $image->Caption;?></textarea></td>
				</tr>
				<tr>
					<td>Note</td>
					<td><textarea name="note" rows="5" cols="86"><?php echo $image->Note;?></textarea></td>
				</tr>
				<tr>
					<td>Publishist</td>
					<td><input type="text" size="100" name="publishist" value="<?php echo $image->Publishist;?>"></td>
				</tr>
				<tr>
					<td>Copyright</td>
					<td><input type="text" size=100 name="copyright" value="<?php echo $image->Copyright;?>"></td>
				</tr>
				<tr>
					<td>Marked</td>
					<td><input type="text" size="100" maxlength="2" name="marked" value="<?php echo $image->Marked;?>"></td>
				</tr>
				<tr>
					<td>Filename</td>
					<td><input type="text" id="filename" size="100" name="filename" value="<?php echo $image->Filename;?>"/></td>
				</tr>
				<tr class="edit-only">
					<td>Select File</td>
					<td><input type="file" id="file" name="file" /></td>
				</tr>
				<tr id="thumbnail">
					<td>Thumbnail View</td>
					<td>
						<a class="zoom" title="Zoom" href="#">
							<img src="<?php if ($image->Filename != '') { echo $filelocation . $image->Filename; } ?>" height="150" alt="<?php echo $image->Title;?>"/>
						</a>
						<a class="zoom" title="Zoom" href="#">Zoom</a>
						<a  class="edit-only" id="delete" title="Delete" href="#">Delete</a>
						<div id="zoom-dialog" class="dialog" title="<?php echo $image->Title;?>">
						  <p><img src="<?php if ($image->Filename != '') { echo $filelocation . $image->Filename; } ?>" height="500" alt="<?php echo $image->Title;?>"/></p>
						</div>
						<div id="delete-dialog" class="dialog" title="Delete the image?">
						  <p>Do you really want to delete the image?</p>
						</div>
					</td>
				</tr>
				<tr>
					<td>URL</td>
					<td><input type="text" size="100" maxlength="150" name="url" value="<?php echo $image->URL;?>"></td>
				</tr>
				<tr>
					<td colspan="4">
						<input type="submit" name="edit" value="Edit Image">
						<input class="edit-only" type="submit" name="submit" value="Save Image">
						<input type="submit" name="cancel" value="Cancel">
					</td>
				</tr>
			</table>
		</form>
		</div>
	
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/edit.js"></script>
	</body>
</html>