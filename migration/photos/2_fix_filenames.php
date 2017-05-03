#!/usr/bin/php

<?php

require '../../public/includes/settings.inc.php';

echo "ID, Old Filename, New Filename\r\n";

$updateStatement = $pdo->prepare("UPDATE Photos SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Photos WHERE Filename <> '';");

while ($photo = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$path = '../../public/photos/' . $photo->Filename;
	$newFilename = '';
	
	if (!file_exists($path)) {
		
		if (file_exists($path . '.jpg')) {
			$newFilename = $photo->Filename . '.jpg';
		} else if (file_exists($path . '.jpeg')) {
			$newFilename = $photo->Filename . '.jpeg';
		} else if (file_exists($path . '.gif')) {
			$newFilename = $photo->Filename . '.gif';
		}
		
		if ($newFilename != '') {
			$updateStatement->execute(array($newFilename, $photo->ID));
			echo "$image->ID, $image->Filename, $newFilename\r\n";
		}
	}
}

$selectStatement = $pdo->query("SELECT ID, Filename FROM Photos WHERE Filename LIKE '%.tif';;");

while ($photo = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$newFilename = str_ireplace('.tif', '.jpg', $photo->Filename);
	
	if (file_exists($newFilename)) {
		$updateStatement->execute(array($newFilename, $photo->ID));
		echo "$photo->ID, $photo->Filename, $newFilename\r\n";
	}
}


?>