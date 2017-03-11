<?php

$sqldb = "Images";
$sqlpassword = "Sherwood109";
$sqluser = "ulli";
$sqlhost = "localhost";

$application = "Images and Photos Databases";

$message = '';
$warning = '';
$error = '';

try {
    $pdo = new PDO("mysql:host={$sqlhost};dbname={$sqldb}", $sqluser, $sqlpassword);
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch (PDOException $e) {
    $error = 'Database connexion error: ' . $e->getMessage();
    die();
}

?>