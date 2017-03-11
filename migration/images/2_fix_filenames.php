#!/usr/bin/php

<?php

require '../../Application/includes/settings.inc.php';

echo "ID,Old Filename, New Filename\r\n";

$updateStatement = $pdo->prepare("UPDATE Images SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Images WHERE Filename <> '';");

while ($image = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$path = $filelocation . $image->Filename;
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
			//$updateStatement->execute(array($newFilename, $image->ID));
			//  WRONG  :  it will include the pqth on the filename
			echo "$image->ID,$image->Filename, $newFilename\r\n";
		}
	}
}

$selectStatement = $pdo->query("SELECT ID, Filename FROM Images WHERE Filename LIKE '%.tif';;");

while ($image = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$newFilename = str_ireplace('.tif', '.jpg', $image->Filename);
	
	if (file_exists($newFilename)) {
		$updateStatement->execute(array($newFilename, $image->ID));
		echo "$image->ID,$image->Filename, $newFilename\r\n";
	}
}


?>