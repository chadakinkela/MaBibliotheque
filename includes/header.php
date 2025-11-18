<?php

// Démarrer la session si pas encore démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
$logged_in = isset($_SESSION['prenomLecteur']);

// Récupérer son prénom si connecté
$user_prenom = $logged_in ? htmlspecialchars($_SESSION['prenomLecteur']) : null;

// Déterminer la page actuelle
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Chada Kinkela"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : "MaBibliothèque"; ?></title>

    <link rel="stylesheet" href="styles/design.css">
</head>
<body>

<header class="header">
    <div class="container">

        <div class="logo">
            <span class="siteName">MaBibliothèque</span>
            <span class="tagline">en ligne</span>
        </div>

        <nav class="nav">
            <ul>
                <li>
                    <a href="index.php"
                       class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                        Accueil
                    </a>
                </li>

                <li>
                    <a href="catalogue.php"
                       class="<?php echo ($currentPage == 'catalogue.php') ? 'active' : ''; ?>">
                        Catalogue
                    </a>
                </li>

                <?php if ($logged_in): ?>
                    <li>
                        <a href="userAccount.php"
                           class="<?php echo ($currentPage == 'userAccount.php') ? 'active' : ''; ?>">
                           Hi, <?= $user_prenom ?> !
                        </a>
                    </li>

                    <li>
                        <a href="logout.php" class="bouton secondaireBouton">
                            Déconnexion
                        </a>
                    </li>

                <?php else: ?>
                    <li>
                        <a href="connexion.php"
                           class="<?php echo ($currentPage == 'connexion.php') ? 'active' : ''; ?>">
                            Mon Compte
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>

    </div>
</header>
