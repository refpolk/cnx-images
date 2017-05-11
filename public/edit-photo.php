<?php

require 'includes/settings.photos.inc.php';

function get_criterion($pdo, $field) {
		
	return is_null($field) ? "IS NULL" : " = " . $pdo->quote($field);
}

function photo_exists($pdo, $image) {
	
	if ($photo->ID != '') {
		$statement = $pdo->query(
			"SELECT * FROM Photos WHERE Title " . get_criterion($pdo, $photo->Title) 
			. " AND Author " . get_criterion($pdo, $photo->Author) 
			. " AND Filename " . get_criterion($pdo, $photo->Filename) 
			. " AND URL " . get_criterion($pdo, $photo->URL)
			. " AND ID = " . $pdo->quote($photo->ID) . ";");			
	} else {
		$statement = $pdo->query(
			"SELECT * FROM Images WHERE Title " . get_criterion($pdo, $photo->Title) 
			. " AND Author " . get_criterion($pdo, $photo->Author) 
			. " AND Filename " . get_criterion($pdo, $photo->Filename) 
			. " AND URL " . get_criterion($pdo, $photo->URL) . ";");		
	}

	return ($statement && $statement->rowCount() > 0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$photo = (object) array(
		'ID' => $_POST['id'],
		'Photonum' => $_POST['photonum'],
		'OldPhotonum' => $_POST['oldPhotonum'],
		'Title' => $_POST['title'],
		'Filename' => $_POST['filename'],
		'URL' => $_POST['url'],
		'Year' => $_POST['year'],
		'Date' => $_POST['date'],
		'Author' => $_POST['authors'],
		'Place' => $_POST['place'],
		'Caption' => $_POST['caption'],
		'Note' => $_POST['note'],
		'Negscan' => $_POST['negscan'],
		'Nix' => $_POST['nix'],
		'Publishist' => $_POST['publishist']
	);
	
	$update = isset($photo->ID);
	
	if ($photo->Title == '') {
		
		$warning = 'Your photo cannot be saved: the title must be filled out.';
		
	} else {
	
		if ($update) {
			
			if (photo_exists($pdo, $photo)) {
				
				$warning = 'Your changes cannot be saved: a photo with the same title, authors, filename and url already exists.';
				
			} else {
				
				try {
					$statement = $pdo->prepare("UPDATE Photos SET Photonum=?, OldPhotonum=?, Title=?, Filename=?, URL=?, Year=?, Date=?, Author=?, Place=?, Caption=?, Note=?, Negscan=?, Nix=?, Publishist=? WHERE ID=?;");
				
					$statement->execute(array($photo->Photonum, $photo->OldPhotonum, $photo->Title, $photo->Filename, $photo->URL, $photo->Year, $photo->Date, $photo->Author, $photo->Place, $photo->Caption, $photo->Note, $photo->Negscan, $photo->Nix, $photo->Publishist, $photo->ID));
				
					$message = 'Your changes have been saved successfully!';
					
				} catch (Exception $e) {
					$error = "An exception occured: $e->getMessage()";
				}				
			}
		} else {
		
			if (photo_exists($pdo, $photo)) {
				
				$warning = 'Your photo cannot be saved: a photo with the same title, authors, filename and url already exists.';
				
			} else {
				
				try {				
					$statement = $pdo->prepare("INSERT INTO Photos (Photonum, OldPhotonum, Title, Filename, URL, Year, Date, Author, Place, Caption, Note, Negscan, Nix, Publishist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
				
					$statement->execute(array($photo->Photonum, $photo->OldPhotonum, $photo->Title, $photo->Filename, $photo->URL, $photo->Year, $photo->Date, $photo->Author, $photo->Place, $photo->Caption, $photo->Note, $photo->Negscan, $photo->Nix, $photo->Publishist));
				
					header('Location: edit-photo.php?id=' . $pdo->lastInsertId() . "&m=1");
					
				} catch (Exception $e) {
					$error = "An exception occured: $e->getMessage()";
				}		
			}
		}
	}
} else {
	
	$update = isset($_GET['id']);
	
	if (isset($_GET['m'])) {
		$message = 'Your photo has been saved successfully!';
	}
	
	if ($update) {
		
		$id = $_GET['id'];
		
		$statement = $pdo->query("SELECT * FROM Photos WHERE ID = $id;");

		if ($statement && $statement->rowCount() > 0) {
			
			$photo = $statement->fetch(PDO::FETCH_OBJ);
			
		} else {
			
			$error = "The photo $id doesn't exist.";
		}
	}
}

$title = ($update ? 'Edit' : 'Add') . ' Photo';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">		
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body data-mode="<?php echo ($update ? 'Edit' : 'Add'); ?>">
		<?php require 'includes/menu-photo.inc.php'; ?>
		
		<div class="container">	
			
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h1><?php echo $title; ?></h1>
					</div>
				</div>				
			</div>

			<form class="form-horizontal" name="form" method="POST" action="edit-photo.php<?php if ($update) { echo "?id=$photo->ID"; } ?>">
		
				<?php require 'includes/messages.inc.php'; ?>

				
				<?php if ($update) { ?>
				<div class="form-group">
					<label class="control-label col-xs-2">ID</label>
					<div class="col-xs-10">
						<?php echo $photo->ID;?><input type="hidden" class="form-control" name="id" value="<?php echo $photo->ID;?>" />
					</div>						
				</div>
				<?php } ?>
				<div class="form-group">
					<label for="title" class="control-label col-xs-2">Title <span class="mandatory edit-only">*</span></label>
					<div class="col-xs-10">
						<input type="text" maxlength="200" class="form-control" id="title" name="title" value="<?php echo $photo->Title;?>" />
					</div>						
				</div>
				<div class="form-group">
					<label for="photonum" class="control-label col-xs-2">Photonum</label>
					<div class="col-xs-4">					
						<input type="text" id="photonum" name="photonum" maxlength="10" class="form-control" value="<?php echo $photo->Photonum;?>" />
					</div>						
					<label for="oldPhotonum" class="control-label col-xs-2">OldPhotonum</label>
					<div class="col-xs-4">											
						<input type="text" id="oldPhotonum" name="oldPhotonum" maxlength="12" class="form-control" value="<?php echo $photo->OldPhotonum;?>" />
					</div>						
				</div>
				<div class="form-group">
					<label for="year" class="control-label col-xs-2">Year</label>
					<div class="col-xs-4">									
						<input type="text" id="year" name="year" maxlength="5" class="form-control" value="<?php echo $photo->Year;?>" />
					</div>						
					<label for="date" class="control-label col-xs-2">Date</label>
					<div class="col-xs-4">							
						<input type="text" id="date" name="date" maxlength="10" class="form-control" value="<?php echo $photo->Date;?>" />
					</div>						
				</div>
				<div class="form-group">
					<label for="authors" class="control-label col-xs-2">Author</label>
					<div class="col-xs-10">							
						<input type="text" id="authors" name="authors" maxlength="100" class="form-control" value="<?php echo $photo->Author;?>" />
					</div>						
				</div>
				<div class="form-group">
					<label for="place" class="control-label col-xs-2">Place</label>
					<div class="col-xs-10">							
						<input type="text" id="place" name="place" maxlength="60" class="form-control" value="<?php echo $photo->Place;?>" />
					</div>						
				</div>
				<div class="form-group">
					<label for="caption" class="control-label col-xs-2">Caption</label>
					<div class="col-xs-10">							
						<textarea id="caption" name="caption" maxlength="1000" class="form-control" rows="2"><?php echo $photo->Caption;?></textarea>
					</div>						
				</div>
				<div class="form-group">
					<label for="note" class="control-label col-xs-2">Note</label>
					<div class="col-xs-10">							
						<textarea id="note" name="note" maxlength="1000" class="form-control" rows="2"><?php echo $photo->Note;?></textarea>
					</div>						
				</div>
				<div class="form-group">
					<label for="publishist" class="control-label col-xs-2">Publishist</label>
					<div class="col-xs-10">							
						<input id="publishist" type="text" name="publishist" maxlength="200" class="form-control" value="<?php echo $photo->Publishist;?>">
					</div>						
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">Nix (T/F)</label>
					<div class="col-xs-10">							
						<label for="nix-true" class="radio-inline"><input type="radio" id="nix-true" name="nix" value="T" <?php if ($photo->Nix == 'T') echo 'checked';?>>T</label>						
						<label for="nix-false" class="radio-inline"><input type="radio" id="nix-false" name="nix" value="F" <?php if ($photo->Nix == 'F' || !$update) echo 'checked';?>>F</label>						
					</div>							
					<label class="control-label col-xs-2">Negscan (T/F)</label>
					<div class="col-xs-10">							
						<label for="negscan-true" class="radio-inline"><input type="radio" id="negscan-true" name="negscan" value="T" <?php if ($photo->Negscan == 'T') echo 'checked';?>>T</label>						
						<label for="negscan-false" class="radio-inline"><input type="radio" id="negscan-false" name="negscan" value="F" <?php if ($photo->Negscan == 'F' || !$update) echo 'checked';?>>F</label>												
					</div>
				</div>
				<div class="form-group">
					<label for="filename" class="control-label col-xs-2">Filename</label>
					<div class="col-xs-10">							
						<input id="filename" type="text" name="filename" class="form-control" value="<?php echo $photo->Filename;?>"/>
					</div>						
				</div>
				<div class="form-group edit-only">
					<label for="file" class="control-label col-xs-2">Select File</label>
					<div class="col-xs-10">
						<input id="file" class="file" name="file" type="file">						
					</div>
				</div>
				<div id="thumbnail" class="form-group">
					<label class="control-label col-xs-2">Thumbnail View</label>
					<div class="col-xs-3">
						<?php if ($photo->Filename != '') { ?>
						<a class="zoom thumbnail" title="Zoom" href="#">
							<img src="<?php echo $filelocation . $photo->Filename; ?>" alt="<?php echo $photo->Title;?>"/>
						</a>
						<button type="button" class="btn btn-sm btn-default zoom">Zoom</button>
						<button type="button" class="btn btn-sm btn-default remove edit-only">Remove</button>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label for="url" class="control-label col-xs-2">URL</label>
					<div class="col-xs-10">	
						<input type="text" id="url" name="url" maxlength="150" class="form-control" value="<?php echo $photo->URL;?>" />
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-offset-2 col-xs-10">
						<button type="submit" name="edit" class="btn btn-lg btn-primary">Edit Photo</button>
						<button type="submit" name="submit" class="btn btn-lg btn-primary edit-only">Save Photo</button>
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
				<?php if ($photo->Title != '') { ?>
		        <h4 class="modal-title"><?php echo $photo->Title;?></h4>
				<?php } ?>				
		      </div>
		      <div class="modal-body">
				<?php if ($photo->Filename != '') { ?>
		        <p><img src="<?php echo $filelocation . $photo->Filename; ?>" alt="<?php echo $photo->Title;?>" style="width:100%;"/></p>
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
		        <p>Do you really want to remove the photo?</p>
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