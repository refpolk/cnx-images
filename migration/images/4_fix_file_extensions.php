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

$updateStatement = $pdo->prepare("UPDATE Images SET Filename = ? WHERE ID = ?;");

$selectStatement = $pdo->query("SELECT ID, Filename FROM Images WHERE Filename <> '';");

while ($image = $selectStatement->fetch(PDO::FETCH_OBJ)) {
	
	$newFilename = '';
	
	$pathParts = pathinfo('../../public/images/' . $image->Filename);

	if (array_key_exists($pathParts['extension'])) {
		if (strtolower($pathParts['extension']) == 'jpeg') {
			$newFilename = $pathParts['filename'] . ".jpg";
		} else  {
			$newFilename = $pathParts['filename'] . "." . strtolower($pathParts['extension']);
		}
	}

	if ($newFilename != '' && file_exists('../../public/images/' . $newFilename)) {
		$updateStatement->execute(array($newFilename, $image->ID));
		echo "$image->Filename, $newFilename\r\n";	
	}
}

?>