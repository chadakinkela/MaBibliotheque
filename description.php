<?php
// Connexion Db
require_once 'connexion_db.php';

// Vérifie si un id de livre est envoyé

$idLivre = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idLivre <= 0) {
    echo "<p>Livre invalide.</p>";
    exit;
}

// Récupérer les infos du livre

$requete = $connexion->prepare("
    SELECT id, titre, auteur, categorie, description, image, nombre_exemplaire
    FROM livres
    WHERE id = ?
");

$requete->bind_param("i", $idLivre);
$requete->execute();
$resultat = $requete->get_result();

if ($resultat->num_rows === 0) {
    echo "<p>Livre introuvable.</p>";
    exit;
}

$livre = $resultat->fetch_assoc();

if (isset($_GET['message'])) {
    if ($_GET['message'] === "ajoute") {
        echo "<p class='success'>Ajouté à votre liste de lecture</p>";
    } 
    if ($_GET['message'] === "existedeja") {
        echo "<p class='warning'>Ce livre est déjà dans votre liste</p>";
    }
}


?>

<?php include('includes/header.php'); ?>

<div class="descriptionContainer">

    <div class="imageBloc">
        <img src="images/<?php echo htmlspecialchars($livre['image']); ?>" 
             alt="<?php echo htmlspecialchars($livre['titre']); ?>">
    </div>

    <div class="infoBloc">
        <h2><?php echo htmlspecialchars($livre['titre']); ?></h2>

        <p><strong>Auteur :</strong> <?php echo htmlspecialchars($livre['auteur']); ?></p>
        <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($livre['categorie']); ?></p>
        <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($livre['description'])); ?></p>

        <p class="exemplaires">
            <strong>Exemplaires disponibles :</strong> 
            <?php echo intval($livre['nombre_exemplaire']); ?>
        </p>

        <a href="wishlist.php?id=<?php echo $livre['id']; ?>" class="btnEmprunter">
            Emprunter
        </a>
    </div>

</div>

<?php include ('includes/footer.php');?>
