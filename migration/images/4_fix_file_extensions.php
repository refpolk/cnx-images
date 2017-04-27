#!/usr/bin/php

<?php

require '../../public/includes/settings.images.inc.php';

echo "ID,Old Filename, New Filename\r\n";

$updateStatement = $pdo->prepare("UPDATE Images SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Images WHERE Filename <> '';");

while ($image = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$path_parts = pathinfo($filelocation . $image->Filename);

	$filename = $path_parts['filename'] . $path_parts['extension'];
	$newFilename = $path_parts['filename'] . strtolower($path_parts['extension']);
	
	echo $filename . " => " . $newFilename;
	//$updateStatement->execute(array($newFilename, $image->ID));

}

?>