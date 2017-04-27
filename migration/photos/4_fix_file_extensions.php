#!/usr/bin/php

<?php

require '../../public/includes/settings.photos.inc.php';

$updateStatement = $pdo->prepare("UPDATE Photos SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Photos WHERE Filename <> '';");

while ($photo = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$path_parts = pathinfo($filelocation . $photo->Filename);

	$filename = $path_parts['filename'] . "." . $path_parts['extension'];
	$newFilename = $path_parts['filename'] . "." . strtolower($path_parts['extension']);

	echo $filename . " => " . $newFilename . "\r\n";
	
	$updateStatement->execute(array($newFilename, $photo->ID));
}

?>