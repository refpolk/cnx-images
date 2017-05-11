<?php

require 'includes/settings.images.inc.php';

function get_criterion($pdo, $field) {
		
	return is_null($field) ? "IS NULL" : " = " . $pdo->quote($field);
}

function image_exists($pdo, $image) {
	
	if ($image->ID != '') {
		$statement = $pdo->query(
			"SELECT * FROM Images WHERE Title " . get_criterion($pdo, $image->Title) 
			. " AND Author " . get_criterion($pdo, $image->Author) 
			. " AND Filename " . get_criterion($pdo, $image->Filename) 
			. " AND URL " . get_criterion($pdo, $image->URL)
			. " AND ID = " . $pdo->quote($image->ID) . ";");			
	} else {
		$statement = $pdo->query(
			"SELECT * FROM Images WHERE Title " . get_criterion($pdo, $image->Title) 
			. " AND Author " . get_criterion($pdo, $image->Author) 
			. " AND Filename " . get_criterion($pdo, $image->Filename) 
			. " AND URL " . get_criterion($pdo, $image->URL) . ";");		
	}

	return ($statement && $statement->rowCount() > 0);
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
	
	if (is_null($image->Title) || $image->Title === '') {
		
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
					$error = "An exception occured: $e->getMessage()";
				}
			}
		} else {
			
			if (image_exists($pdo, $image)) {
			
				$warning = 'Your image cannot be saved: a photo with the same title, authors, filename and url already exists.';
			
			} else {
				
				try {			
					$statement = $pdo->prepare("INSERT INTO Images (Title, Filename, URL, Author, Year, Source, ELibrary, Caption, Note, Publishist, Copyright, Marked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
			
					$statement->execute(array($image->Title, $image->Filename, $image->URL, $image->Author, $image->Year, $image->Source, $image->ELibrary, $image->Caption, $image->Note, $image->Publishist, $image->Copyright, $image->Marked));
				
					header('Location: edit-image.php?id=' . $pdo->lastInsertId() . "&m=1");
					
				} catch (Exception $e) {
					$error = "An exception occured: $e->getMessage()";
				}
			}
		}
	}
} else {
	
	$update = isset($_GET['id']);
	
	if (isset($_GET['m'])) {
		$message = 'Your image has been saved successfully!';
	}
	
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
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php require 'includes/styles.inc.php'; ?>
		<title><?php echo $title; ?></title>
	</head>
	<body data-mode="<?php echo ($update ? 'Edit' : 'Add'); ?>">
		<?php require 'includes/menu-image.inc.php'; ?>
		
		<div class="container">	
			
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h1><?php echo $title; ?></h1>
					</div>
				</div>				
			</div>
			
			<form class="form-horizontal" name="form" method="POST" action="edit-image.php<?php if ($update) { echo "?id=$image->ID"; } ?>">
	
				<?php require 'includes/messages.inc.php'; ?>
			
				<?php if ($update) { ?>
				<div class="form-group">
					<label class="control-label col-xs-2">ID</label>
					<div class="col-xs-10">
						<p class="form-control-static"><?php echo $image->ID;?></p>
						<input type="hidden" id="id" class="form-control" name="id" value="<?php echo $image->ID;?>">
					</div>
				</div>
				<?php } ?>			
				<div class="form-group">
					<label for="title" class="control-label col-xs-2">Title <span class="mandatory edit-only">*</span></label>
					<div class="col-xs-10">
						<input id="title" class="form-control" type="text" maxlength="115" name="title" value="<?php echo $image->Title;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="author" class="control-label col-xs-2">Author</label>
					<div class="col-xs-10">
						<input id="author" class="form-control" type="text" maxlength="100" name="author" value="<?php echo $image->Author;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="year" class="control-label col-xs-2">Year</label>
					<div class="col-xs-10">
						<input id="year" class="form-control" type="text" name="year" maxlength="5" value="<?php echo $image->Year;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="source" class="control-label col-xs-2">Source</label>
					<div class="col-xs-10">
						<input id="source" class="form-control" type="text" maxlength="50"  name="source" value="<?php echo $image->Source;?>">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">ELibrary (T/F)</label>
					<div class="col-xs-10">
						  <label for="elibrary-true" class="radio-inline"><input type="radio" id="elibrary-true" name="elibrary" value="T" <?php if ($image->ELibrary == 'T') echo 'checked';?>>T</label>
						  <label for="elibrary-false" class="radio-inline"><input type="radio" id="elibrary-false" name="elibrary" value="F" <?php if ($image->ELibrary == 'F' || !$update) echo 'checked';?>>F</label>
					</div>
				</div>
				<div class="form-group">
					<label for="caption" class="control-label col-xs-2">Caption</label>
					<div class="col-xs-10">
						<textarea id="caption" class="form-control" name="caption" rows="2"><?php echo $image->Caption;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="note" class="control-label col-xs-2">Note</label>
					<div class="col-xs-10">
						<textarea id="note" class="form-control" name="note" rows="2"><?php echo $image->Note;?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="publishist" class="control-label col-xs-2">Publishist</label>
					<div class="col-xs-10">
						<input id="publishist" class="form-control" type="text" name="publishist" value="<?php echo $image->Publishist;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="copyright" class="control-label col-xs-2">Copyright</label>
					<div class="col-xs-10">
						<input id="copyright" class="form-control" type="text" size=100 name="copyright" value="<?php echo $image->Copyright;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="marked" class="control-label col-xs-2">Marked</label>
					<div class="col-xs-10">
						<input id="marked" class="form-control" type="text" maxlength="2" name="marked" value="<?php echo $image->Marked;?>">
					</div>
				</div>
				<div class="form-group">
					<label for="filename" class="control-label col-xs-2">Filename</label>
					<div class="col-xs-10">
						<input id="filename" class="form-control" type="text" name="filename" value="<?php echo $image->Filename;?>">
					</div>
				</div>
				<div class="form-group edit-only">
					<label class="control-label col-xs-2">Select File</label>
					<div class="col-xs-10">
						<input id="file" class="file" name="file" type="file">						
					</div>
				</div>
				<div id="thumbnail" class="form-group">
					<label class="control-label col-xs-2">Thumbnail View</label>
					<div class="col-xs-3">
						<?php if ($image->Filename != '') { ?>
						<a class="zoom thumbnail" title="Zoom" href="#">
							<img src="<?php if ($image->Filename != '') { echo $filelocation . $image->Filename; } ?>" alt="<?php echo $image->Title;?>"/>
						</a>
						<button type="button" class="btn btn-sm btn-default zoom">Zoom</button>
						<button type="button" class="btn btn-sm btn-default remove edit-only">Remove</button>
						<?php } ?>
					</div>
				</div>				
				<div class="form-group">
					<label for="url" class="control-label col-xs-2">URL</label>
					<div class="col-xs-10">
						<input id="url" class="form-control" type="text" maxlength="150" name="url" value="<?php echo $image->URL;?>">
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-2 col-xs-10">
						<button type="submit" name="edit" class="btn btn-lg btn-primary">Edit Image</button>
						<button type="submit" name="submit" class="btn btn-lg btn-primary edit-only">Save Image</button>
						<button type="submit" name="cancel" class="btn btn-lg btn-default">Cancel</button>
					</div>
				</div>
			</form>
		</div>
		
		<div id="zoom-dialog" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
				<?php if ($image->Title != '') { ?>
		        <h4 class="modal-title"><?php echo $image->Title;?></h4>
				<?php } ?>
		      </div>
		      <div class="modal-body">
				<?php if ($image->Filename != '') { ?>
		        <p><img src="<?php echo $filelocation . $image->Filename; ?>" alt="<?php echo $image->Title;?>" style="width:100%;"/></p>
				<?php } ?>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>
		
		<div id="remove-dialog" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Remove</h4>
		      </div>
		      <div class="modal-body">
		        <p>Do you really want to remove the image?</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary" data-dismiss="modal" id="remove">Remove</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		      </div>
		    </div>
		  </div>
		</div>
	
		<?php require 'includes/scripts.inc.php'; ?>
		<script src="scripts/edit.js"></script>
	</body>
</html>