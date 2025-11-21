<?php
// Récupération des variables d'environnement de Railway
$host = 'mysql.railway.internal';                       
$user = 'root';      
$pass = 'qsOmRwSjuCFYEKKdLPelVIggSoveVIbE';  
$db   = 'railway';  
$port = 3306;    

// Tentative de connexion à la base de données      
$connexion = @new mysqli($host, $user, $pass, $db, $port); 

// Gestion des erreurs de connexion
if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}

// Définir le charset pour supporter les accents et caractères spéciaux
$connexion->set_charset("utf8");

?>