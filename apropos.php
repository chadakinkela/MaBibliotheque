<?php
include('includes/header.php');
?>

<main class="aproposPage">
    <div class="container">
        <h1>À propos</h1>

        <section class="intro">
			<p>Bonjour,</p>
            <p>Je suis <strong>Chada</strong>, informaticienne, débrouillarde et consciencieuse.</p>
            <p>Je vis parce que j’apprends et c'est peu importe le domaine ou les circonstances de la vie, telle est ma philosophie, d’où mon côté débrouillardise. Et lorsqu’on apprend, on se doit un minimum de l’appliquer avec soin et rigueur, d’où mon côté consciencieuse et c'est dans cet état d'esprit qu'est née <strong>MaBibliothèque en ligne</strong>.</p>
            <p>Ce site web est mon projet final pour la validation de ma formation en développement Web niveau intermédiaire, offert par l’Organisation Internationale de la Francophonie à travers son programme <a href="https://dclic.francophonie.org/" target="_blank">DeClic.</a></p>
        </section>

        <!-- Fonctionnement du site -->
        <section class="fonctionnement">
            <h2>Fonctionnement du site</h2>
            <p>Sans trop m’attarder, je vais passer directement à la partie sur le fonctionnement du site. MaBibliothèque en ligne est composé de 9 fichiers principaux :</p>
            <ol>
                <li><strong>index.php</strong> : Contient le contenu de la page d’accueil du site, présentant le site et ses fonctionnalités principales.</li>
                <li><strong>results.php</strong> : Affiche les résultats de recherche de livres effectuée par l’utilisateur via la barre de recherche.</li>
                <li><strong>connexion.php</strong> : Permet aux utilisateurs de se connecter ou de créer un compte pour accéder aux fonctionnalités personnelles.</li>
                <li><strong>userAccount.php</strong> : Permet à l’utilisateur de gérer son compte, consulter sa liste de livres, ajouter, modifier ou rendre un livre, et mettre à jour ses informations personnelles.</li>
                <li><strong>details.php</strong> : vérifie si un utilisateur est connecté avant d'emprunter un livre, et vérifiele le statut du livre si ca n'a pas déjà été emprunté, sinon, il l'ajoute à la liste de lecture de l'utilisateur dans la base de données.</li>
                <li><strong>description.php</strong> : Fournit la description complète d’un livre sélectionné dans le catalogue ou les résultats de recherche.</li>
                <li><strong>wishlist.php</strong> : Permet d’ajouter un livre à sa liste de lecture et de consulter les livres enregistrés pour un futur emprunt.</li>
                <li><strong>catalogue.php</strong> : Affiche le catalogue complet des livres disponibles avec filtres, tri et pagination.</li>
                <li><strong>logout.php</strong> : Permet de se déconnecter du site</li>
            </ol>

            <p>Chaque page utilise <strong>header.php</strong> et <strong>footer.php</strong> pour l’en-tête et le pied de page communs, garantissant une navigation cohérente.</p>

            <p>Grâce à ce site, les utilisateurs peuvent rechercher, ajouter, modifier et supprimer des livres de leur liste, suivre leurs emprunts et rendre compte de leurs lectures de manière simple et intuitive.</p>
        </section>

        <!-- Lien vers le portfolio -->
        <section class="portfolio">
            <p>Pour en savoir plus sur moi, mes projets, vous pouvez consulter mon <a href="" target="_blank" >portfolio</a>.</p>
        </section>
    </div>
</main>

<?php
include('includes/footer.php');
?>
