#!/usr/bin/php

<?php

$c1 = 0;
$c2 = 0;
$c3 = 0;

$n1 = 0;
$n2 = 0;
$n3 = 0;
	
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

function photo_title_formatter($title) {

	if ($title == '') {
		return 'Untitled';
	}
	
	return $title;
}

function canon_title_formatter($title) {
	// 2012CG-134-Killarney8.jpg
	// 2004CG_0001-CondowithBookshelf.jpg
	// 2005CG-0090b-AmelieResearching.jpg
	// 2011CG-0896bs-ShaynaMiriamEloise.jpg
	// 2012CG-0546.Hawaii.jpg
	// 2013CG-0255.jpg
	
	if ($title == '') {
		return 'Untitled';
	}
	
	global $c1, $c2, $c3;
	
	$pattern1 = "/^.{6}[_|-].{3,6}[\.|-](.*)\..{3,4}$/";
	
	if (preg_match($pattern1, $title)) {
		$c1++;
		return preg_replace($pattern1, "$1", $title);
	}
	
	$pattern2 = "/^(.{6}[_|-].{3,6})\..{3,4}$/";
	
	if (preg_match($pattern2, $title)) {
		$c2++;
		return preg_replace($pattern2, "Untitled", $title);
	}
	
	$c3++;
	echo "[Canon] Not modified: $title\r\n";
	
	return $title;
}

function nikon_title_formatter($title) {
	// N2002-03-04-0129-AlongHumber.jpg
	// N2003-10-17-Img0001-DesertLake.jpg
	// N2002-09-22-0491.jpg
	// N2004-04-29-058-Flowers.jpg
	
	if ($title == '') {
		return 'Untitled';
	}

	global $n1, $n2, $n3;
	
	$pattern1 = "/^.{5}-.{2}-.{2}-.{3,7}-(.*)\..{3,4}$/";
	
	if (preg_match($pattern1, $title)) {
		$n1++;
		return preg_replace($pattern1, "$1", $title);
	}

	$pattern2 = "/^.{5}-.{2}-.{2}-.{3,7}\..{3,4}$/";
	
	if (preg_match($pattern2, $title)) {
		$n2++;
		return preg_replace($pattern2, "Untitled", $title);
	}

	$n3++;
	echo "[Nikon] Not modified: $title\r\n";
	
	return $title;
}

$photos_filter = function($cols) {
	return array(
		$cols[0],						// ID
		$cols[1],						// PhotoNum
		$cols[2],						// OldPhotoNum
		photo_title_formatter($cols[3]),// Title
		$cols[4],						// Filename
		$cols[5],						// Year
		$cols[6],						// Date
		$cols[7],						// Author
		$cols[8],						// Place
		$cols[9],						// Caption
		bool_formatter($cols[12]), 		// NegScan
		bool_formatter($cols[16])		// Nix
	);
};

$nikon_filter = function($cols) {
	return array(
		$cols[0],						// ID
		$cols[1],						// PhotoNum
		null,							// OldPhotoNum
		nikon_title_formatter($cols[2]),// Title
		$cols[4],						// Filename
		$cols[5],						// Year
		$cols[6],						// Date
		$cols[7],						// Author
		$cols[8],						// Place
		$cols[9],						// Caption
		null, 							// NegScan
		bool_formatter($cols[16])		// Nix
	);
};

$canon_filter = function($cols) {
	return array(
		$cols[0],						// ID
		$cols[1],						// PhotoNum
		null,							// OldPhotoNum
		canon_title_formatter($cols[2]),// Title
		$cols[4],						// Filename
		$cols[5],						// Year
		$cols[6],						// Date
		$cols[7],						// Author
		$cols[8],						// Place
		$cols[9],						// Caption
		null, 							// NegScan
		bool_formatter($cols[16])		// Nix
	);
};

$new_canon_filter = function($cols) {
	return array(
		$cols[1],						// PhotoNum
		null,							// OldPhotoNum
		canon_title_formatter($cols[2]),// Title
		$cols[4],						// Filename
		$cols[5],						// Year
		$cols[6],						// Date
		$cols[7],						// Author
		$cols[8],						// Place
		$cols[9],						// Caption
		null, 							// NegScan
		bool_formatter($cols[16])		// Nix
	);
};

require '../../public/includes/settings.inc.php';

$statement = $pdo->prepare("INSERT INTO Photos (ID, Photonum, Oldphotonum, Title, Filename, Year, Date, Author, Place, Caption, Negscan, Nix) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

migrate("./Photos.txt", $photos_filter, $statement);
migrate("./Nikon.txt", $nikon_filter, $statement);
migrate("./Canon.txt", $canon_filter, $statement);

$statement = $pdo->prepare("INSERT INTO Photos (Photonum, Oldphotonum, Title, Filename, Year, Date, Author, Place, Caption, Negscan, Nix) VALUES (?,?,?,?,?,?,?,?,?,?,?)");

migrate("./NewCanon.txt", $new_canon_filter, $statement);

echo "[Canon] Modified: $c1\r\n";
echo "[Canon] Modified Untitled: $c2\r\n";
echo "[Canon] Not Modified: $c3\r\n";

echo "[Nikon] Modified: $n1\r\n";
echo "[Nikon] Modified Untitled: $n2\r\n";
echo "[Nikon] Not Modified: $n3\r\n";

/*
ID	PHOTONUM	OLDPHOTONM	TITLE	FILENAME	YEAR	DATE	AUTHORS	PLACE	CAPTION	CAMERA	TECHNICAL	NEGSCAN	MAINALBUM	ALBUMPLUS	PUBLISHIST	NIX	NACKT
ID	NikonID	Title	TitleTemp	Filename	Jahr	Date	Author	Place	Caption	Camera	Nix
ID	CanonID	Title	TitleTemp	Filename	Jahr	Date	Author	Place	Caption	Camera	Nix
*/

?>