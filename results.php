<?php
// Connexion à la base de données
require_once 'connexion_db.php';

// On récupère la donnée saisi
$termeRecherche = isset($_GET['search']) ? trim($_GET['search']) : "";

// Si le champ n'est pas vide, on lance la recherche
if ($termeRecherche !== "") {

    // Préparation de la requête SQL sécurisée
    $requete = $connexion->prepare("
        SELECT id, titre, auteur, categorie, image
        FROM livres
        WHERE titre LIKE ? 
           OR auteur LIKE ? 
           OR categorie LIKE ?
    ");

    $motCle = "%" . $termeRecherche . "%";

    $requete->bind_param("sss", $motCle, $motCle, $motCle);
    $requete->execute();
    $resultat = $requete->get_result();

    // Si on trouve des résultats
    if ($resultat->num_rows > 0) {

        echo "<div class='resultatsContainer'>";

        while ($livre = $resultat->fetch_assoc()) {

            //on sécurise les données
            $image = !empty($livre["image"]) ? htmlspecialchars($livre["image"]) : "placeholder.jpg";
            $titre = htmlspecialchars($livre["titre"]);
            $auteur = htmlspecialchars($livre["auteur"]);
            $categorie = htmlspecialchars($livre["categorie"]);

            //pour chaque livre, on affiche ceci :
            echo "
            <div class='livreItem'>
                <div class='imageZone'>
                    <img src='images/$image' alt='$titre'>
                </div>

                <div class='texteZone'>
                    <h3>$titre</h3>
                    <p class='auteur'>$auteur</p>
                    <p class='categorie'>Catégorie : $categorie</p>

                    <div class='actions'>
						<a class='btnDescription' href='description.php?id={$livre['id']}'>Lire la description</a>
						<a href='wishlist.php?id={$livre['id']}' class='btnWishlist'>Emprunter</a>
					</div>
                </div>
            </div>";
        }

        echo "</div>";
    } else {
        echo "<p class='noResult'>Aucun résultat trouvé.</p>";
    }

    $requete->close();
}

$connexion->close();
?>
