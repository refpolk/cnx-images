#!/usr/bin/php

<?php

require '../../public/includes/settings.inc.php';

echo "'ID','Title','Filename'\r\n";

$statement = $pdo->query("SELECT ID, Title, Filename FROM Images;");

while ($image = $statement->fetch(PDO::FETCH_OBJ)) {
	
	if (!file_exists($filelocation . $image->Filename)) {
		
		echo "'$image->ID','$image->Title','$image->Filename'\r\n";
	}
}

?>