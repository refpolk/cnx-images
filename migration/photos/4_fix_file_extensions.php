#!/usr/bin/php

<?php

/*

rename 's/\.JPEG$/.jpg/' *.JPEG
rename 's/\.jpeg$/.jpg/' *.jpeg
rename 's/\.JPG$/.jpg/' *.JPG
rename 's/\.GIF$/.gif/' *.GIF
rename 's/\.PNG$/.png/' *.PNG

*/

require '../../public/includes/settings.inc.php';

echo "Filename, New filename\r\n";

$updateStatement = $pdo->prepare("UPDATE Photos SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Photos WHERE Filename <> '';");

while ($photo = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$newFilename = '';
	
	$pathParts = pathinfo('../../public/photos/' . $photo->Filename);

	if (strtolower($pathParts['extension']) == 'jpeg') {
		$newFilename = $pathParts['filename'] . ".jpg";
	} else if (strtolower($pathParts['extension']) != '') {
		$newFilename = $pathParts['filename'] . "." . strtolower($pathParts['extension']);
	}

	if ($newFilename != '' && file_exists('../../public/photos/' . $newFilename)) {
		$updateStatement->execute(array($newFilename, $photo->ID));
		echo "$image->Filename, $newFilename\r\n";	
	}
}

?>