#!/usr/bin/php

<?php

require '../../public/includes/settings.inc.php';

echo "'ID','Title','Filename'\r\n";

$statement = $pdo->query("SELECT ID, Title, Filename FROM Photos;");

while ($photo = $statement->fetch(PDO::FETCH_OBJ)) {
	
	if (!file_exists('../../public/photos/' . $photo->Filename)) {
		
		echo "'$photo->ID','$photo->Title','$photo->Filename'\r\n";
	}
}

?>