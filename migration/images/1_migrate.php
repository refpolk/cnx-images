#!/usr/bin/php

<?php

function migrate($filePath, $filter, $statement) {
	
	$file = fopen($filePath, 'r');
	$filesize = filesize($filePath);

	$row = fgets($file, $filesize);

	while ($row = fgets($file, $filesize)) {
		$cols = explode("\t", $row);
		$filteredCols = $filter($cols);
		try {
			$statement->execute($filteredCols);
		} catch (PDOException $e) {
			print "ERROR!: " . $e->getMessage();
			var_dump($cols);
			var_dump($filteredCols);
			die();
		}
	}

	fclose($file);
}

function bool_formatter($value){
	return ($value == 'False' ? 'F' : ($value == 'True' ? 'T' : null));
}

function title_formatter($title) {

	if ($title == '') {
		return 'Untitled';
	}
	
	return $title;
}

$images_filter = function($cols) {
	return array(
		$cols[0],						// ID
		title_formatter($cols[2]),		// Title
		$cols[3],						// Filename
		$cols[5],  						// Author
		$cols[6],						// Year
		$cols[7],						// Source
		$cols[8],						// Caption
		bool_formatter($cols[9]),		// ELibrary
		$cols[14],						// Marked
		$cols[15]						// Publishist
	);
};

require '../../Application/includes/settings.inc.php';

$statement = $pdo->prepare("INSERT INTO Images (ID, Title, Filename, Author, Year, Source, Caption, ELibrary, Marked, Publishist) VALUES (?,?,?,?,?,?,?,?,?,?)");

migrate("./Images.txt", $images_filter, $statement);	

?>