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
		<div class="container">

		<?php require 'includes/menu.inc.php'; ?>
	
		<h1><?php echo $title; ?></h1>

		<form name="form" method="POST" action="edit-photo.php<?php if ($update) { echo "?id=$photo->ID"; } ?>">
		
			<?php require 'includes/messages.inc.php'; ?>

			<table style="width=100%">
				<?php if ($update) { ?>
				<tr>
					<td>ID</td>	
					<td colspan="3"><?php echo $photo->ID;?><input type="hidden" name="id" value="<?php echo $photo->ID;?>" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td>Title <span class="mandatory edit-only">*</span></td>	
					<td colspan="3"><input type="text" size="100" maxlength="200" name="title" value="<?php echo $photo->Title;?>" /></td>
				</tr>
				<tr>
					<td>Photonum</td>
					<td><input type="text" name="photonum" size="30" maxlength="10" value="<?php echo $photo->Photonum;?>" /></td>
					<td>OldPhotonum</td>
					<td><input type="text" name="oldPhotonum" size="30" maxlength="12" value="<?php echo $photo->OldPhotonum;?>" /></td>
				</tr>
				<tr>
					<td>Year</td>
					<td><input type="text" name="year" size="30" maxlength="5" value="<?php echo $photo->Year;?>" /></td>
					<td>Date</td>
					<td><input type="text" name="date" size="30" maxlength="10" value="<?php echo $photo->Date;?>" /></td>
				</tr>
				<tr>
					<td>Author</td>
					<td colspan="3"><input type="text" name="authors" size="100" maxlength="100" value="<?php echo $photo->Author;?>" /></td>
				</tr>
				<tr>
					<td>Place</td>
					<td colspan="3"><input type="text" name="place" size="100" maxlength="60" value="<?php echo $photo->Place;?>" /></td>
				</tr>
				<tr>
					<td>Caption</td>
					<td colspan="3"><textarea name="caption" size="100" maxlength="1000" rows="5" cols="86"><?php echo $photo->Caption;?></textarea></td>
				</tr>
				<tr>
					<td>Note</td>
					<td colspan="3"><textarea name="note" size="100" maxlength="1000" rows="5" cols="86"><?php echo $photo->Note;?></textarea></td>
				</tr>
				<tr>
					<td>Publishist</td>
					<td colspan="3"><input type="text" name="publishist" size="100" maxlength="200" value="<?php echo $photo->Publishist;?>"></td>
				</tr>
				<tr>
					<td>Nix (T/F)</td>
					<td>
						<input type="radio" name="nix" value="T" <?php if ($photo->Nix == 'T') echo 'checked';?> /> T<br />
					    <input type="radio" name="nix" value="F" <?php if ($photo->Nix == 'F' || !$update) echo 'checked';?> /> F
					</td>
					<td>Negscan (T/F)</td>
					<td>
						<input type="radio" name="negscan" value="T" <?php if ($photo->Negscan == 'T') echo 'checked';?> /> T<br />
						<input type="radio" name="negscan" value="F" <?php if ($photo->Negscan == 'F' || !$update) echo 'checked';?> /> F
					</td>
				</tr>
				<tr>
					<td>Filename</td>
					<td colspan="3"><input id="filename"  type="text" size="100" name="filename" value="<?php echo $photo->Filename;?>"/></td>
				</tr>
				<tr class="edit-only">
					<td>Select File</td>
					<td colspan="3"><input id="file" type="file" name="file" /></td>
				</tr>
				<tr id="thumbnail">
					<td>Thumbnail View</td>
					<td colspan="3">
						<a class="zoom" title="Zoom" href="#">
							<img src="<?php if ($photo->Filename != '') { echo $filelocation . $photo->Filename; } ?>" height="150" alt="<?php echo $photo->Title;?>"/>
						</a>
						<a class="zoom" title="Zoom" href="#">Zoom</a>
						<a class="edit-only" id="delete" title="Delete" href="#">Delete</a>
						<div id="zoom-dialog" class="dialog" title="<?php echo $photo->Title;?>">
						  <p><img src="<?php if ($photo->Filename != '') { echo $filelocation . $photo->Filename; } ?>" height="500" alt="<?php echo $photo->Title;?>"/></p>
						</div>
						<div id="delete-dialog" class="dialog" title="Delete the photo?">
						  <p>Do you really want to delete the photo?</p>
						</div>
					</td>
				</tr>
				<tr>
					<td>URL</td>
					<td colspan="3">
						<input type="text" name="url" size="100" maxlength="150" value="<?php echo $photo->URL;?>" />
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<input type="submit" name="edit" value="Edit Photo">						
						<input class="edit-only" type="submit" name="submit" value="Save Photo">
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