<?php
// Récupération des variables d'environnement injectées par Railway
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT');
$user = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASSWORD');
$db   = getenv('MYSQL_DATABASE');

if (!$host || !$port || !$user || !$pass || !$db) {
    
    die("Erreur de configuration : Variables d'environnement DB manquantes. Vérifiez le liage des services.");
}

// Tentative de connexion à la base de données Railway

$connexion = new mysqli($host, $user, $pass, $db, $port);

// Gestion des erreurs de connexion 
if ($connexion->connect_error) {
    // Affiche l'erreur de connexion dans les logs Railway
    error_log("DB Connection Failed: " . $connexion->connect_error);
    die("Connexion échouée : " . $connexion->connect_error);
}

// Définir l'encodage des caractères 
$connexion->set_charset("utf8");

?>