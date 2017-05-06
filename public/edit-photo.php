<?php

require 'includes/settings.photos.inc.php';

function photo_exists($pdo, $photo) {
		
	if ($photo->ID != '') {
		$statement = $pdo->prepare("SELECT * FROM Photos WHERE Title=? AND Author=? AND Filename=? AND URL=? AND ID<>?;");
		$statement->execute(array($photo->Title, $photo->Author, $photo->Filename, $photo->URL, $photo->ID));
	} else {		
		$statement = $pdo->prepare("SELECT * FROM Photos WHERE Title=? AND Author=? AND Filename=? AND URL=?;");
		$statement->execute(array($photo->Title, $photo->Author, $photo->Filename, $photo->URL));
	}	
	
	return ($statement->rowCount() > 0);
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
			
				$statement = $pdo->prepare("UPDATE Photos SET Photonum=?, OldPhotonum=?, Title=?, Filename=?, URL=?, Year=?, Date=?, Author=?, Place=?, Caption=?, Note=?, Negscan=?, Nix=?, Publishist=? WHERE ID=?;");
				
				$statement->execute(array($photo->Photonum, $photo->OldPhotonum, $photo->Title, $photo->Filename, $photo->URL, $photo->Year, $photo->Date, $photo->Author, $photo->Place, $photo->Caption, $photo->Note, $photo->Negscan, $photo->Nix, $photo->Publishist, $photo->ID));
				
				$message = 'Your changes have been saved successfully!';
			}
		} else {
		
			if (photo_exists($pdo, $photo)) {
				
				$warning = 'Your photo cannot be saved: a photo with the same title, authors, filename and url already exists.';
				
			} else {
				
				$statement = $pdo->prepare("INSERT INTO Photos (Photonum, OldPhotonum, Title, Filename, URL, Year, Date, Author, Place, Caption, Note, Negscan, Nix, Publishist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
				
				$statement->execute(array($photo->Photonum, $photo->OldPhotonum, $photo->Title, $photo->Filename, $photo->URL, $photo->Year, $photo->Date, $photo->Author, $photo->Place, $photo->Caption, $photo->Note, $photo->Negscan, $photo->Nix, $photo->Publishist));
				
				header('Location: edit-photo.php?id=' . $pdo->lastInsertId() . "&m=1");
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
<html>
	<head>
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

			<form class="form-horizontal" role="form" name="form" method="POST" action="edit-photo.php<?php if ($update) { echo "?id=$photo->ID"; } ?>">
		
				<?php require 'includes/messages.inc.php'; ?>

				
				<?php if ($update) { ?>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">ID</label>
					<div class="col-xs-10">
						<td colspan="3"><?php echo $photo->ID;?><input type="hidden" class="form-control" name="id" value="<?php echo $photo->ID;?>" /></td>
					</div>						
				</div>
				<?php } ?>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Title <span class="mandatory edit-only">*</span></label>
					<div class="col-xs-10">
						<td colspan="3"><input type="text"maxlength="200" class="form-control" name="title" value="<?php echo $photo->Title;?>" /></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Photonum</label>
					<div class="col-xs-4">					
						<td><input type="text" name="photonum" maxlength="10" class="form-control" value="<?php echo $photo->Photonum;?>" /></td>
					</div>						
					<label for="id" class="control-label col-xs-2">OldPhotonum</label>
					<div class="col-xs-4">											
						<td><input type="text" name="oldPhotonum" maxlength="12" class="form-control" value="<?php echo $photo->OldPhotonum;?>" /></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Year</label>
					<div class="col-xs-4">									
						<td><input type="text" name="year" maxlength="5" class="form-control" value="<?php echo $photo->Year;?>" /></td>
					</div>						
					<label for="id" class="control-label col-xs-2">Date</label>
					<div class="col-xs-4">							
						<td><input type="text" name="date" maxlength="10" class="form-control" value="<?php echo $photo->Date;?>" /></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Author</label>
					<div class="col-xs-10">							
						<td colspan="3"><input type="text" name="authors" maxlength="100" class="form-control" value="<?php echo $photo->Author;?>" /></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Place</label>
					<div class="col-xs-10">							
						<td colspan="3"><input type="text" name="place" maxlength="60" class="form-control" value="<?php echo $photo->Place;?>" /></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Caption</label>
					<div class="col-xs-10">							
						<td colspan="3"><textarea name="caption" maxlength="1000" class="form-control" rows="2"><?php echo $photo->Caption;?></textarea></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Note</label>
					<div class="col-xs-10">							
						<td colspan="3"><textarea name="note" maxlength="1000" class="form-control" rows="2"><?php echo $photo->Note;?></textarea></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Publishist</label>
					<div class="col-xs-10">							
						<td colspan="3"><input type="text" name="publishist" maxlength="200" class="form-control" value="<?php echo $photo->Publishist;?>"></td>
					</div>						
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Nix (T/F)</label>
					<div class="col-xs-10">							
						<label class="radio-inline"><input type="radio" name="nix" value="T" <?php if ($photo->Nix == 'T') echo 'checked';?>>T</label>						
						<label class="radio-inline"><input type="radio" name="nix" value="F" <?php if ($photo->Nix == 'F' || !$update) echo 'checked';?>>F</label>						
					</div>							
					<label for="id" class="control-label col-xs-2">Negscan (T/F)</label>
					<div class="col-xs-10">							
						<label class="radio-inline"><input type="radio" name="negscan" value="T" <?php if ($photo->Negscan == 'T') echo 'checked';?>>T</label>						
						<label class="radio-inline"><input type="radio" name="negscan" value="F" <?php if ($photo->Negscan == 'F' || !$update) echo 'checked';?>>F</label>												
					</div>
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">Filename</label>
					<div class="col-xs-10">							
						<td colspan="3"><input id="filename" type="text" name="filename" class="form-control" value="<?php echo $photo->Filename;?>"/></td>
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
						<a class="zoom thumbnail" title="Zoom" href="#">
							<img src="<?php if ($photo->Filename != '') { echo $filelocation . $photo->Filename; } ?>" alt="<?php echo $photo->Title;?>"/>
						</a>
						<button type="button" class="btn btn-sm btn-default zoom">Zoom</button>
						<button type="button" class="btn btn-sm btn-default remove edit-only">Remove</button>
					</div>
				</div>
				<div class="form-group">
					<label for="id" class="control-label col-xs-2">URL</label>
					<div class="col-xs-10">	
						<input type="text" name="url" maxlength="150" class="form-control" value="<?php echo $photo->URL;?>" />
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