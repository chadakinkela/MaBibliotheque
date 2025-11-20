<?php

if (!class_exists('mysqli')) {
    die("ERREUR FATALE: L'extension PHP 'mysqli' est manquante ou non activée sur le serveur Railway.");
}

// Nous testons seulement la variable HOST, si elle fonctionne, les autres aussi.
$host = getenv('MYSQL_HOST'); 

if (empty($host)) {
    die("ERREUR FATALE: Les variables d'environnement ne sont pas lues sur cette page. (Host est vide)");
}

// Si les deux tests passent, le script devrait continuer.
die("Tests de base réussis. HOST est: " . $host . ". L'extension mysqli est présente.");

// $port = getenv('MYSQL_PORT'); 
// $user = getenv('MYSQL_USER');
// $pass = getenv('MYSQL_PASSWORD');
// $db   = getenv('MYSQL_DATABASE');
// $connexion = new mysqli($host, $user, $pass, $db, $port);
?>