<?php
// Récupération des variables d'environnement injectées par Railway
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');
$db   = getenv('MYSQL_DATABASE');


// Tentative de connexion à la base de données Railway

$connexion = new mysqli($host, $user, $pass, $db, $port);

// Gestion des erreurs de connexion 
if ($connexion->connect_error) {
    // 🛑 AFFICHER LES PARAMÈTRES POUR LE DÉBOGAGE 🛑
    die("CONNEXION ÉCHOUÉE. Identifiants: HOST='{$host}', PORT='{$port}', USER='{$user}'. Erreur: " . $connexion->connect_error); 
}

// Définir l'encodage des caractères 
$connexion->set_charset("utf8");

?>