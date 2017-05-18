<?php

require 'includes/settings.images.inc.php';

class Log {
	public $Row;
	public $Result;
	public $Message;
	public $ID;
}

function get_criterion($pdo, $field) {
		
	return is_null($field) ? "IS NULL" : " = " . $pdo->quote($field);
}

function image_exists($pdo, $image) {
	
	$statement = $pdo->query(
		"SELECT * FROM Images WHERE Title " . get_criterion($pdo, $image->Title) 
		. " AND Author " . get_criterion($pdo, $image->Author) 
		. " AND Filename " . get_criterion($pdo, $image->Filename) 
		. " AND URL " . get_criterion($pdo, $image->URL) . ";");
	
	if ($statement && $statement->rowCount() > 0) {
			
		$image = $statement->fetch(PDO::FETCH_OBJ);
		
		return $image->ID;
	}
		
	return -1;
}

function build_image($data) {
	return (object) array(	
		'Title' => $data[0],
		'Filename' => $data[1],
		'Author' => $data[2],
		'Year' => $data[3],
		'Source' => $data[4],
		'Caption' => $data[5],
		'Note' => $data[6],
		'Publishist' => $data[7],
		'Copyright' => $data[8],
		'Marked' => $data[9],
		'URL' => $data[10],
		'ELibrary' => $data[11]
	);
}

function insert_image($pdo, $data, $row) {
	
	$image = build_image($data);
	
	$log = new Log();
	$log->Row = $row + 1;

	try {
		
		$log->ID = image_exists($pdo, $image);
		
	} catch (Exception $e) {
		
		$log->Message = "An exception occured: " . $e->getMessage();
		$log->Result = false;
		return $log;
	}
	
	if ($log->ID < 0) {
		
		if (is_null($image->Title) || $image->Title === "") {
			
			$log->Message = "Your image cannot be saved: the title must be filled out.";
			$log->Result = false;
			
		} else {
		
			try {
				
				$statement = $pdo->prepare("INSERT INTO Images (Title, Filename, URL, Author, Year, Source, ELibrary, Caption, Note, Publishist, Copyright, Marked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

				$statement->execute(array($image->Title, $image->Filename, $image->URL, $image->Author, $image->Year, $image->Source, $image->ELibrary, $image->Caption, $image->Note, $image->Publishist, $image->Copyright, $image->Marked));
	
				$log->ID = $pdo->lastInsertId();
				$log->Message = "Your image has been saved successfully!";
				$log->Result = true;
								
			} catch (Exception $e) {
				
				$log->Message = "An exception occured: " . $e->getMessage();
				$log->Result = false;
			}
		}
		
	} else {
		
		$log->Message = "Your image cannot be saved: an image with the same title, authors, filename and url already exists.";
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
					array_push($logs, insert_image($pdo, $data, $row));
				}
				
                $row++;
            }
			
            fclose($handle);
			
			$message = "Your images have been imported successfully!";
			
			foreach($logs as $log) {
			    if ($log->Result == false) {
					$warning = "Some images have not been imported!";
					$message = "";					
			        break;
			    }
			}
        }
    } else {
    	$error = "No file or invalid file uploaded. A CSV file is required.";
    }
}

$title = 'Import Images';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body>
		<?php require 'includes/menu-image.inc.php'; ?>
		
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
						<p>A sample <abbr title="Comma-Separated Values">CSV</abbr> file can be downloaded here: <a href="/resources/import_images_sample.csv" title="Download the sample file" class="alert-link">import_images_sample.csv</a>.</p>
						<p>The first line is not imported and must contain the column names. The column names are not controlled, however the order of the columns matters and must be strictly followed. The only mandatory column is the first one, which contains the title, all the other columns are optional. The values of the columns must be separated by commas and delimited by double quotes. The only forbidden character in a value is the double quote.</p>
					</div>
				</div>
			</div>

			<form method="POST" enctype="multipart/form-data" action="import-image.php" >
									
				<div class="form-group">
					<label>Select File</label>
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
											<a href="<?php echo "/edit-image.php?id=$log->ID"; ?>" title="See Image" target="_blank">See Image</a>
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