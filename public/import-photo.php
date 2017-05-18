<?php

require 'includes/settings.photos.inc.php';

class Log {
	public $Row;
	public $Result;
	public $Message;
	public $ID;
}

function get_criterion($pdo, $field) {
		
	return is_null($field) ? "IS NULL" : " = " . $pdo->quote($field);
}

function photo_exists($pdo, $photo) {
	
	$statement = $pdo->query(
		"SELECT * FROM Photos WHERE Title " . get_criterion($pdo, $photo->Title) 
		. " AND Author " . get_criterion($pdo, $photo->Author) 
		. " AND Filename " . get_criterion($pdo, $photo->Filename) 
		. " AND URL " . get_criterion($pdo, $photo->URL) . ";");
	
	if ($statement && $statement->rowCount() > 0) {
			
		$photo = $statement->fetch(PDO::FETCH_OBJ);
		
		return $photo->ID;
	}
		
	return -1;
}

function build_photo($data) {
	return (object) array(	
		'Title' => $data[0],
		'Filename' => $data[1],
		'Author' => $data[2],
		'Year' => $data[3],
		'Date' => $data[4],
		'Photonum' => $data[5],
		'OldPhotonum' => $data[6],
		'URL' => $data[7],
		'Place' => $data[8],
		'Caption' => $data[9],
		'Note' => $data[10],
		'Negscan' => $data[11],
		'Nix' => $data[12],
		'Publishist' => $data[13]
	);
}

function insert_photo($pdo, $data, $row) {
	
	$photo = build_photo($data);
	
	$log = new Log();
	$log->Row = $row + 1;
	
	try {
		
		$log->ID = photo_exists($pdo, $photo);
		
	} catch (Exception $e) {
		
		$log->Message = "An exception occured 1: " . $e->getMessage();
		$log->Result = false;
		return $log;
	}
	
	if ($log->ID < 0) {
		
		if (is_null($photo->Title) || $photo->Title === "") {
			
			$log->Message = "Your photo cannot be saved: the title must be filled out.";
			$log->Result = false;
			
		} else {
		
			try {
				
				$statement = $pdo->prepare("INSERT INTO Photos (Photonum, OldPhotonum, Title, Filename, URL, Year, Date, Author, Place, Caption, Note, Negscan, Nix, Publishist) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
				
				$statement->execute(array($photo->Photonum, $photo->OldPhotonum, $photo->Title, $photo->Filename, $photo->URL, $photo->Year, $photo->Date, $photo->Author, $photo->Place, $photo->Caption, $photo->Note, $photo->Negscan, $photo->Nix, $photo->Publishist));
	
				$log->ID = $pdo->lastInsertId();
				$log->Message = "Your photo has been saved successfully!";
				$log->Result = true;
				
			} catch (Exception $e) {
				
				$log->Message = "An exception occured: " . $e->getMessage();
				$log->Result = false;
			}
		}
		
	} else {
		
		$log->Message = "Your photo cannot be saved: a photo with the same title, authors, filename and url already exists.";
		$log->Result = false;
	}
	
	return $log;
}

function valid_file_uploaded() {

   	if (empty($_FILES)) {
        return false;
	}

    $tempName = $_FILES['csv']['tmp_name'];
	
	if (!empty($tempName) && is_uploaded_file($tempName)) {
		
		$ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
		
		if ($ext === 'csv') {
			return true;
		}
    }
	
   	return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
	if (valid_file_uploaded()) {
		
		$logs = array();
	
        if (($handle = fopen($_FILES['csv']['tmp_name'], 'r')) !== FALSE) {

            set_time_limit(0);

            $row = 0;

            while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {

				if ($row > O) {
					array_push($logs, insert_photo($pdo, $data, $row));
				}
				
                $row++;
            }
			
            fclose($handle);
			
			$message = "Your photos have been imported successfully!";
			
			foreach($logs as $log) {
			    if ($log->Result == false) {
					$warning = "Some photos have not been imported!";
					$message = "";					
			        break;
			    }
			}
        }
    } else {
    	$error = "No file or invalid file uploaded. A CSV file is required.";
    }
}

$title = 'Import Photos';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body>
		<?php require 'includes/menu-photo.inc.php'; ?>
		
		<div class="container">	
		
			<div class="row">
				<div class="col-xs-12">
					<div class="page-header">
						<h1>
							<?php echo $title; ?>
						</h1>
					</div>
				</div>				
			</div>
				
			<?php require 'includes/messages.inc.php'; ?>
						
			<?php if (is_null($logs)) { ?>
				
			<div class="row">				
				<div class="col-xs-12">
					<div class="alert alert-info">
						<h4>File format</h4>
						<p>A sample <abbr title="Comma-Separated Values">CSV</abbr> file can be downloaded here: <a href="/resources/import_photos_sample.csv" title="Download the sample file" class="alert-link">import_photos_sample.csv</a>.</p>
						<p>The first line is not imported and must contain the column names. The column names are not controlled, however the order of the columns matters and must be strictly followed. The only mandatory column is the first one, which contains the title, all the other columns are optional. The values of the columns must be separated by commas and delimited by double quotes. The only forbidden character in a value is the double quote.</p>
					</div>
				</div>
			</div>

			<form method="POST" enctype="multipart/form-data" action="import-photo.php" >
									
				<div class="form-group">
					<label >Select File</label>
					<input id="file" class="file" name="csv" type="file">
				</div>
				
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-lg btn-primary">Import File</button>
				</div>
			</form>
			
			<?php } else { ?>
				
			<div class="row">				
				<div class="col-xs-12">
					<div class="form-group">
						<a class="btn btn-default" href="import-photo.php">Import a new file</a>
					</div>
				</div>
			</div>			
					
			<div class="row">
				<div class="col-xs-12">							
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>Row</th>
								<th>Result</th>
								<th>Message</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($logs as $log) { ?>
								 <tr class="<?php echo $log->Result == true ? "message" : "error" ; ?>">
									 <td><?php echo $log->Row; ?></td>
									 <td>
										 <span class="label label-<?php echo $log->Result == true ? "success" : "danger" ; ?>">
										 	<?php echo $log->Result == true ? "OK" : "KO" ; ?>
									 	 </span>
									 </td>
									 <td>
										<?php echo $log->Message; ?> 
				 						<?php if ($log->ID > 0) { ?>
											<a href="<?php echo "/edit-photo.php?id=$log->ID"; ?>" title="See Photo" target="_blank">See Photo</a>
				 						<?php } ?>
									 </td>
								 </tr> 
							<?php } ?>
						</tbody>				
					</table>
				</div>			
			</div>
			
			<?php } ?>

		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		
	</body>
</html>