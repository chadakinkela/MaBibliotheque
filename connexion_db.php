<?php
$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT') ?: 3306;
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'MVNZcvWkeqJpxTOHMYPnZIWowXroCrNg';

$connexion = new mysqli($host, $user, $pass, $db, $port);

if ($connexion->connect_error) {
    die("Connexion échouée : " . $connexion->connect_error);
}

$connexion->set_charset("utf8");
?>
