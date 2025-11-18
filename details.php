<?php
session_start();

// Si pas connecté, direction page connexion.php
if (!isset($_SESSION['id_lecteur'])) {
    header("Location: connexion.php?redirect=details.php?id=$idLivre");
    exit;
}

$idLecteur = $_SESSION['id_lecteur'];

// Récupérer id du livre
$idLivre = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idLivre <= 0) {
    echo "Livre invalide.";
    exit;
}

// Connexion DB
$connexion = new mysqli("localhost", "root", "", "bibliotheque_db");
if ($connexion->connect_error) {
    die("Erreur de connexion : " . $connexion->connect_error);
}

// Vérifier si déjà emprunté
$check = $connexion->prepare("
    SELECT COUNT(*) FROM liste_lecture WHERE id_livre = ? AND id_lecteur = ?
");
$check->bind_param("ii", $idLivre, $idLecteur);
$check->execute();
$check->bind_result($count);
$check->fetch();
$check->close();

if ($count > 0) {
    echo "<p>Vous avez déjà emprunté ce livre.</p>";
    exit;
}

// Insérer l’emprunt
$insert = $connexion->prepare("
    INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt)
    VALUES (?, ?, NOW())
");
$insert->bind_param("ii", $idLivre, $idLecteur);

if ($insert->execute()) {
    echo "<p>Livre ajouté à votre liste de lecture avec succès !</p>";
    echo "<a href='catalogue.php'>Retour au catalogue</a>";
} else {
    echo "Erreur lors de l'emprunt.";
}

$insert->close();
$connexion->close();
?>
