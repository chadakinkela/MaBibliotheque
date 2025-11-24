<?php
session_start();
require_once 'connexion_db.php';

// Sécuriser l'id reçu
$idLivre = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idLivre <= 0) {
    die("ID du livre invalide.");
}

// Vérifier si utilisateur connecté
if (!isset($_SESSION['idLecteur'])) {

    $_SESSION['redirect'] = "wishlist.php?id=$idLivre";

    header("Location: connexion.php");
    exit;
}

$idLecteur = $_SESSION['idLecteur'];

$check = $connexion->prepare("
    SELECT COUNT(*) 
    FROM liste_lecture 
    WHERE id_livre = ? AND id_lecteur = ?
");
$check->bind_param("ii", $idLivre, $idLecteur);
$check->execute();
$check->bind_result($count);
$check->fetch();
$check->close();

if ($count > 0) {
    // Message si le livre est déjà dans la liste de lecture.
    echo "Ce livre est déjà dans votre liste de lecture.";
    echo "<p><a href='catalogue.php'>Retour au catalogue</a></p>";
    $connexion->close();
    exit;
}

$insert = $connexion->prepare("
    INSERT IGNORE INTO liste_lecture (id_livre, id_lecteur, date_emprunt, date_retour)
    VALUES (?, ?, NULL, NULL)
");
$insert->bind_param("ii", $idLivre, $idLecteur);
$insert->execute();
$insert->close();

echo "Livre ajouté avec succès à votre liste de lecture.";
echo "<p><a href='catalogue.php'>Retour au catalogue</a></p>";
$connexion->close();
?>
