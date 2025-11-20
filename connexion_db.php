<?php
// --- Déclaration statique pour le débogage ---
$host = 'mysql.railway.internal';      
$port = 3306;                  
$user = 'root';      
$pass = 'qsOmRwSjuCFYEKKdLPelVIggSoveVIbE';  
$db   = 'railway';            

if (!class_exists('mysqli')) {
    die("ERREUR FATALE: L'extension PHP 'mysqli' est manquante ou non activée.");
}

if (empty($host) || empty($user) || empty($pass) || empty($db)) {
    die("ERREUR FATALE: Le code contient des variables vides.");
}

$connexion = @new mysqli($host, $user, $pass, $db, $port);

if ($connexion->connect_error) {
    die("CONNEXION ÉCHOUÉE: " . $connexion->connect_error);
}

die("CONNEXION RÉUSSIE ! Le site est prêt à afficher les données.");

$connexion->set_charset("utf8");

?>