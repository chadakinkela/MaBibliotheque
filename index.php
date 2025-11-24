<?php
// On démarre la session, puis on inclut le header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/header.php');
?>

<main>
    <section class="sectionAcceuil">
        <div class="container">
            <h1>Le Savoir à Portée de Clic</h1>
            <p>Votre bibliothèque numérique, explorez-le !</p>

            <!-- Barre de recherche -->
            <form class="rechercheForm" onsubmit="return false;">
				<div id="resultPopup"></div>
                <div class="rechercheInputGroup">
                    <input type="search" id="searchInput" placeholder="Rechercher par titre, auteur, catégorie..." aria-label="Rechercher des livres">
					<span class="rechercheIcon"><img src="icons/loupe.png" alt=""></span>
                </div>
                <a href="catalogue.php" class="parcoursCollections">Tout le catalogue</a>
                
            </form>
		
        </div>
    </section>

    <section class="commentCaMarcheSection">
        <div class="container">
            <h2>Comment ça Marche !?</h2>
            <div class="etapesGrille">
                <div class="etapeCarte">
                        <div class="etapeIcone">
                            <img src="icons/connect.jpeg" alt="Icône de disponibilité">
                        </div>
                        <a href="connexion.php" style="text-decoration:none;"><h3>1. Connectez-Vous</h3></a>
                        <p>Votre bibliothèque vous suit partout, accessible 24h/24, 7j/7.</p>
                    </div>
                    <div class="etapeCarte">
                        <div class="etapeIcone">
                            <img src="icons/search.jpeg" alt="Icône de recherche">
                        </div>
                        <a href="catalogue.php" style="text-decoration:none;"><h3>2. Explorez le Catalogue</h3></a>
                        <p>Découvrez notre vaste collection de livres numériques par titre, auteur...</p>
                    </div>
                    <div class="etapeCarte">
                        <div class="etapeIcone">
                            <img src="icons/emprunt.jpeg" alt="Icône d'emprunt" >
                        </div>
                        <h3>3. Empruntez & Lisez</h3>
                        <p>Sélectionnez vos ouvrages préférés et lisez-les directement sur votre appareil.</p>
                    </div> 
            </div>
        </div>
    </section>
</main>

<?php include('includes/footer.php'); ?>

<script src="js/script.js"></script>
