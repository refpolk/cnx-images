#!/usr/bin/php

<?php

require '../../public/includes/settings.photos.inc.php';

echo "ID,Old Filename, New Filename\r\n";

$updateStatement = $pdo->prepare("UPDATE Photos SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Photos WHERE Filename <> '';");

while ($photo = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$path = $filelocation . $photo->Filename;
	$newFilename = '';
	
	if (!file_exists($path)) {
		
		if (file_exists($path . '.jpg')) {
			$newFilename = $path . '.jpg';
		} else if (file_exists($path . '.jpeg')) {
			$newFilename = $path . '.jpeg';
		} else if (file_exists($path . '.gif')) {
			$newFilename = $path . '.gif';
		}
		
		if ($newFilename != '') {
			//$updateStatement->execute(array($newFilename, $photo->ID));
			//  WRONG  :  it will include the pqth on the filename
			
			echo "$photo->ID,$photo->Filename, $newFilename\r\n";
		}
	}
}

$selectStatement = $pdo->query("SELECT ID, Filename FROM Photos WHERE Filename LIKE '%.tif';;");

while ($photo = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$newFilename = str_ireplace('.tif', '.jpg', $photo->Filename);
	
	if (file_exists($newFilename)) {
		$updateStatement->execute(array($newFilename, $photo->ID));
		echo "$photo->ID,$photo->Filename, $newFilename\r\n";
	}
}


?>