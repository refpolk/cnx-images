<?php

require 'includes/settings.photos.inc.php';

class Log {
	public $Row;
	public $Result;
	public $Message;
	public $ID;
}

function get_criterion($field) {
		
	return is_null($field) ? "IS NULL" : " = '$field'";
}

function photo_exists($pdo, $photo) {
	
	$statement = $pdo->query(
		"SELECT * FROM Photos WHERE Title " . get_criterion($photo->Title) 
		. " AND Author " . get_criterion($photo->Author) 
		. " AND Filename " . get_criterion($photo->Filename) 
		. " AND URL " . get_criterion($photo->URL) . ";");
	
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
	
	$log->ID = photo_exists($pdo, $photo);
	
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

            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {

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
<html>
	<head>
		<title><?php echo $title; ?></title>
		<?php require 'includes/styles.inc.php'; ?>
	</head>
	<body>
		<div class="container">

			<?php require 'includes/menu.inc.php'; ?>
		
			<h1><?php echo $title; ?></h1>
			
			<?php require 'includes/messages.inc.php'; ?>
			
			<p>Download the sample CSV file: <a href="/resources/import_photos_sample.csv">import_photos_sample.csv</a></p>
			
			<p>The first line contains headers and is not imported. The 'Title' field is mandatory, all the other fields are optional.</p>
		
			<form method="POST" enctype="multipart/form-data" action="import-photo.php" >
				<input type="file" name="csv" value="" /><br />
				<input type="submit" name="submit" value="Import" />
			</form>
			
			<?php if (!is_null($logs)) { ?>
			
			<table>
				<thead>
					<tr>
						<td>Row</td>
						<td>Result</td>
						<td>Message</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($logs as $log) { ?>
						 <tr class="<?php echo $log->Result == true ? "message" : "error" ; ?>">
							 <td><?php echo $log->Row; ?></td>
							 <td><?php echo $log->Result == true ? "OK" : "KO" ; ?></td>
							 <td>
								<?php echo $log->Message; ?> 
		 						<?php if ($log->ID > 0) { ?>
									<a href="<?php echo "/edit-photo.php?id=$log->ID"; ?>" target="_blank">See Photo</a>
		 						<?php } ?>
							 </td>
						 </tr> 
					<?php } ?>
				</tbody>				
			</table>
			
			<?php } ?>

		</div>
		
		<?php require 'includes/scripts.inc.php'; ?>
		
	</body>
</html>