<?php
// Démarre la session, puis inclut le header de la page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/header.php');

// Connexion à la base de données + Vérification de connexion, si elle échoue,  le script s'arrête affiche l'erreur.

require_once 'connexion_db.php';

// Avant d'accéder à cette page, l'utilisateur doit etre connecter,
// sinon, il est redirigé vers la page connexion 

if (!isset($_SESSION['idLecteur'])) {
    $_SESSION['message'] = "Veuillez vous connecter pour accéder à cette page.";
    $_SESSION['message_type'] = "error";
    header("Location: connexion.php");
    exit(); 
}

// Une fois connecter dans son compte, on récupère id et demarre sa session.
$idLecteur = $_SESSION['idLecteur'];

// Vérifie si une action a été soumise via un formulaire POST
if (isset($_POST['action'])) {
    $action = $_POST['action']; // Récupère le type d'action

    //Pour gérer les différentes actions de l'utilisateur dans son compte
    switch ($action) {
        case 'ajouterAListeLecture':
            // Filtre et valide l'id du livre, la date d'emprunt et la date de retour
            $idLivre = filter_var($_POST['idLivre'], FILTER_SANITIZE_NUMBER_INT);
            // Définit la date d'emprunt à aujourd'hui si elle n'est pas fournie
            $dateEmprunt = !empty($_POST['dateEmprunt']) ? $_POST['dateEmprunt'] : date('Y-m-d');
            // Définit la date de retour à NULL si elle n'est pas fournie (ce qui est le cas par défaut)
            $dateRetour = !empty($_POST['dateRetour']) ? $_POST['dateRetour'] : null;

            // Valide si un livre a été sélectionné
            if (empty($idLivre)) {
                $_SESSION['message'] = "Veuillez sélectionner un livre à ajouter à votre liste.";
                $_SESSION['message_type'] = "error";
            } else {
                // Vérifie si le livre est déjà dans la liste de lecture de l'utilisateur.
                $checkDuplicateQuery = "select count(*) from liste_lecture where id_livre = ? and id_lecteur = ?";
                $stmtCheck = $connexion->prepare($checkDuplicateQuery); 
                if($stmtCheck) {
                    $stmtCheck->bind_param("ii", $idLivre, $idLecteur); 
                    $stmtCheck->execute(); 
                    $stmtCheck->bind_result($count); 
                    $stmtCheck->fetch(); 
                    $stmtCheck->close(); 

                    // Si le livre est déjà présent, affiche un message d'information.
                    if ($count > 0) {
                        $_SESSION['message'] = "Ce livre est déjà dans votre liste de lecture.";
                        $_SESSION['message_type'] = "info";
                    } else {
                        // l'utilisateur insère le livre dans sa liste_lecture avec les valeurs ???? 
						//afin que le system gére les valeurs automatiquement et sécuritairement
                        $queryInsertListe = "INSERT INTO liste_lecture (id_livre, id_lecteur, date_emprunt, date_retour) VALUES (?, ?, ?, ?)";
                        $stmtInsertListe = $connexion->prepare($queryInsertListe);

                        if ($stmtInsertListe) {
                            $stmtInsertListe->bind_param("iiss", $idLivre, $idLecteur, $dateEmprunt, $dateRetour);
                            if ($stmtInsertListe->execute()) { 
                                $_SESSION['message'] = "Livre ajouté à votre liste de lecture avec succès.";
                                $_SESSION['message_type'] = "success";
                            } else {
                                $_SESSION['message'] = "Erreur lors de l'ajout du livre : " . $connexion->error;
                                $_SESSION['message_type'] = "error";
                            }
                            $stmtInsertListe->close(); 
                        } else {
                            error_log("Erreur de préparation (ajouterAListeLecture): " . $connexion->error);
                            $_SESSION['message'] = "Une erreur technique est survenue lors de l'ajout du livre.";
                            $_SESSION['message_type'] = "error";
                        }
                    }
                } else {
                    error_log("Erreur de préparation (vérification doublon): " . $connexion->error);
                    $_SESSION['message'] = "Une erreur technique est survenue.";
                    $_SESSION['message_type'] = "error";
                }
            }
            // Redirige toujours vers la section Mes Livres après l'ajout.
            header("Location: userAccount.php?section=mesLivres");
            exit();

        case 'modifierListeLecture':
            // Filtre et valide les données pour la modification.
            $idLivreModif = filter_var($_POST['idLivre'], FILTER_SANITIZE_NUMBER_INT);
            $dateEmprunt = filter_var($_POST['dateEmprunt'], FILTER_SANITIZE_STRING);
            
            $dateRetour = !empty($_POST['dateRetour']) ? filter_var($_POST['dateRetour'], FILTER_SANITIZE_STRING) : null;

            if (empty($idLivreModif) || empty($dateEmprunt)) {
                $_SESSION['message'] = "L'ID du livre et la date d'emprunt sont obligatoires pour la modification.";
                $_SESSION['message_type'] = "error";
            } else {
                // Met à jour l'entrée existante dans liste_lecture.
                $queryUpdateListe = "UPDATE liste_lecture SET date_emprunt = ?, date_retour = ? WHERE id_livre = ? AND id_lecteur = ?";
                $stmtUpdateListe = $connexion->prepare($queryUpdateListe);

                if ($stmtUpdateListe) {
                    $stmtUpdateListe->bind_param("ssii", $dateEmprunt, $dateRetour, $idLivreModif, $idLecteur);
                    if ($stmtUpdateListe->execute()) {
                        if ($stmtUpdateListe->affected_rows > 0) { // Vérifie si des lignes ont été affectées.
                            $_SESSION['message'] = "Entrée de la liste de lecture modifiée avec succès.";
                            $_SESSION['message_type'] = "success";
                        } else {
                            $_SESSION['message'] = "Aucune modification effectuée ou livre introuvable dans votre liste.";
                            $_SESSION['message_type'] = "info";
                        }
                    } else {
                        $_SESSION['message'] = "Erreur lors de la modification : " . $connexion->error;
                        $_SESSION['message_type'] = "error";
                    }
                    $stmtUpdateListe->close();
                } else {
                    error_log("Erreur de préparation (modifierListeLecture): " . $connexion->error);
                    $_SESSION['message'] = "Une erreur technique est survenue lors de la modification.";
                    $_SESSION['message_type'] = "error";
                }
            }
            header("Location: userAccount.php?section=mesLivres");
            exit();

        case 'rendreLivre': // Action pour rendre le livre
            $idLivreARendre = filter_var($_POST['idLivreARendre'], FILTER_SANITIZE_NUMBER_INT);

            if (empty($idLivreARendre)) {
                $_SESSION['message'] = "ID du livre manquant pour le retour.";
                $_SESSION['message_type'] = "error";
            } else {
                
                $queryUpdateRetour = "UPDATE liste_lecture SET date_retour = CURDATE() WHERE id_livre = ? AND id_lecteur = ? AND (date_retour IS NULL OR date_retour > CURDATE())";
                $stmtUpdateRetour = $connexion->prepare($queryUpdateRetour);

                if ($stmtUpdateRetour) {
                    $stmtUpdateRetour->bind_param("ii", $idLivreARendre, $idLecteur);
                    if ($stmtUpdateRetour->execute()) {
                        if ($stmtUpdateRetour->affected_rows > 0) {
                            $_SESSION['message'] = "Livre marqué comme rendu avec succès.";
                            $_SESSION['message_type'] = "success";
                        } else {
                            $_SESSION['message'] = "Le livre n'a pas pu être marqué comme rendu (peut-être déjà rendu ou non trouvé).";
                            $_SESSION['message_type'] = "info";
                        }
                    } else {
                        $_SESSION['message'] = "Erreur lors du marquage comme rendu : " . $connexion->error;
                        $_SESSION['message_type'] = "error";
                    }
                    $stmtUpdateRetour->close();
                } else {
                    error_log("Erreur de préparation (rendreLivre): " . $connexion->error);
                    $_SESSION['message'] = "Une erreur technique est survenue lors du marquage comme rendu.";
                    $_SESSION['message_type'] = "error";
                }
            }
            header("Location: userAccount.php?section=mesLivres");
            exit();
            
        case 'updateProfil':
            // Filtre et valide les nouvelles informations du profil.
            $newNom = filter_var($_POST['nomLecteur'], FILTER_SANITIZE_STRING);
            $newPrenom = filter_var($_POST['prenomLecteur'], FILTER_SANITIZE_STRING);
            $newEmail = filter_var($_POST['emailLecteur'], FILTER_SANITIZE_EMAIL);

            // Valide le format de l'email.
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['message'] = "Format d'email invalide.";
                $_SESSION['message_type'] = "error";
            } else {
                // Met à jour les informations du lecteur dans la base de données.
                $queryUpdateUser = "UPDATE lecteurs SET nom = ?, prenom = ?, email = ? WHERE id = ?";
                $stmtUpdateUser = $connexion->prepare($queryUpdateUser);
                if ($stmtUpdateUser) {
                    $stmtUpdateUser->bind_param("sssi", $newNom, $newPrenom, $newEmail, $idLecteur);
                    if ($stmtUpdateUser->execute()) {
                        // Met à jour les informations dans la session pour un affichage immédiat.
                        $_SESSION['nomLecteur'] = $newNom;
                        $_SESSION['prenomLecteur'] = $newPrenom;
                        $_SESSION['emailLecteur'] = $newEmail;
                        $_SESSION['message'] = "Votre profil a été mis à jour avec succès.";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Erreur lors de la mise à jour du profil : " . $connexion->error;
                        $_SESSION['message_type'] = "error";
                    }
                    $stmtUpdateUser->close();
                } else {
                    error_log("Erreur de préparation (updateProfil): " . $connexion->error);
                    $_SESSION['message'] = "Une erreur technique est survenue lors de la mise à jour du profil.";
                    $_SESSION['message_type'] = "error";
                }
            }
            header("Location: userAccount.php?section=profilParametres");
            exit();

        case 'deleteAccount':
            // Suppression des infos utilisteur ds nos tables avec effet définitive
            $connexion->begin_transaction();
            try {
                // Supprime toutes les entrées de la liste de lecture de l'utilisateur.
                $queryDeleteListe = "DELETE FROM liste_lecture WHERE id_lecteur = ?";
                $stmtDeleteListe = $connexion->prepare($queryDeleteListe);
                if ($stmtDeleteListe) {
                    $stmtDeleteListe->bind_param("i", $idLecteur);
                    $stmtDeleteListe->execute();
                    $stmtDeleteListe->close();
                }

                // Supprime ensuite le lecteur lui-même.
                $queryDeleteLecteur = "DELETE FROM lecteurs WHERE id = ?";
                $stmtDeleteLecteur = $connexion->prepare($queryDeleteLecteur);
                if ($stmtDeleteLecteur) {
                    $stmtDeleteLecteur->bind_param("i", $idLecteur);
                    $stmtDeleteLecteur->execute();
                    $stmtDeleteLecteur->close();
                }

                $connexion->commit();

                session_destroy(); // Détruit toutes les données de session après la suppression du compte
                // Redirige vers la page d'accueil avec un message de succès
                header("Location: index.php?message=Votre compte a été supprimé avec succès.&type=success");
                exit();
            } catch (Exception $e) {
                $connexion->rollback(); // Annule la transaction en cas d'erreur.
                $_SESSION['message'] = "Erreur lors de la suppression du compte : " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
            header("Location: userAccount.php?section=profilParametres");
            exit();
    }
}

//  On récupère tous les livres empruntés par l'utilisateur en joinant la table livres à la table liste_lecture 
$maBibliotheque = [];
$querySelectMaBibliotheque = "
    SELECT liste_lecture.id_livre, liste_lecture.date_emprunt, liste_lecture.date_retour, 
           livres.titre, livres.auteur, livres.description, livres.maison_edition, livres.image
    FROM liste_lecture 
    JOIN livres  ON liste_lecture.id_livre = livres.id
    WHERE liste_lecture.id_lecteur = ?
    ORDER BY livres.titre ASC";
	
$stmtSelectMaBibliotheque = $connexion->prepare($querySelectMaBibliotheque);

if ($stmtSelectMaBibliotheque) {
    $stmtSelectMaBibliotheque->bind_param("i", $idLecteur);
    $stmtSelectMaBibliotheque->execute();
    $resultMaBibliotheque = $stmtSelectMaBibliotheque->get_result();

    while ($livreDansListe = $resultMaBibliotheque->fetch_assoc()) {
        $maBibliotheque[] = $livreDansListe;
    }
    $stmtSelectMaBibliotheque->close();
} else {
    error_log("Erreur de préparation (sélection bibliothèque): " . $connexion->error);
    $_SESSION['message'] = "Impossible de charger votre bibliothèque personnelle.";
    $_SESSION['message_type'] = "error";
}

// Récupère tous les livres du catalogue.
$livresDisponibles = [];
$queryAllLivres = "SELECT id, titre, auteur FROM livres ORDER BY titre ASC";
$resultAllLivres = $connexion->query($queryAllLivres);

if ($resultAllLivres) {
    while ($livre = $resultAllLivres->fetch_assoc()) {
        $livresDisponibles[] = $livre;
    }
    $resultAllLivres->free(); // Libère la mémoire associée au résultat.
} else {
    error_log("Erreur de récupération des livres disponibles: " . $connexion->error);
}

// Calcul des statistiques des activités de l'utilisateur dans son compte 
$empruntsEnCours = 0;
$livresRetournes = 0;
$livresDansCollection = count($maBibliotheque); // Nombre total de livres dans la liste de lecture.
$livresLus = 0; // Initialise le compteur de livres lus.

foreach ($maBibliotheque as $livre) {
    // Calcule la date de retour prévue (30 jours après l'emprunt).
    $dateRetourPrevue = date('Y-m-d', strtotime($livre['date_emprunt'] . ' + 30 days'));

    // Compte les emprunts en cours : si date_retour est NULL ou dans le futur.
    if (!$livre['date_retour'] || (strtotime($livre['date_retour']) > time())) {
        $empruntsEnCours++;
    }

    // Compte les livres retournés, donc lus. On considere un livre est lu si sa date_retour est renseignée et passée.
    if ($livre['date_retour'] && (strtotime($livre['date_retour']) <= time())) {
        $livresRetournes++;
        $livresLus++; 
    }
}

// Recharge les informations du lecteur pour assurer qu'elles sont à jour.
$lecteurInfo = [];
$queryLecteur = "SELECT nom, prenom, email FROM lecteurs WHERE id = ?";
$stmtLecteur = $connexion->prepare($queryLecteur);
if($stmtLecteur) {
    $stmtLecteur->bind_param("i", $idLecteur);
    $stmtLecteur->execute();
    $resultLecteur = $stmtLecteur->get_result();
    $lecteurInfo = $resultLecteur->fetch_assoc();
    $stmtLecteur->close();
}

// Assigne les informations du lecteur, avec des valeurs par défaut si non trouvées.
$nomLecteur = $lecteurInfo['nom'] ?? $_SESSION['nomLecteur'] ?? 'Nom';
$prenomLecteur = $lecteurInfo['prenom'] ?? $_SESSION['prenomLecteur'] ?? 'Prénom';
$emailLecteur = $lecteurInfo['email'] ?? $_SESSION['emailLecteur'] ?? 'email@exemple.com';

// Ferme la connexion à la base de données.
$connexion->close();

// Détermine la section active par défaut ou via le paramètre 'section' dans l'URL.
$activeSection = $_GET['section'] ?? 'dashboard';

?>

<main class="dashboardLayout">
    <aside class="sidenav">
        <div class="userProfile">
            <div class="userAvatar"><?php echo htmlspecialchars(substr($prenomLecteur, 0, 1) . substr($nomLecteur, 0, 1)); ?></div>
            <p class="userName"><?php echo htmlspecialchars($prenomLecteur . ' ' . $nomLecteur); ?></p>
            <p class="userEmail"><?php echo htmlspecialchars($emailLecteur); ?></p>
        </div>
        <nav class="sidenavNav">
            <a href="userAccount.php?section=dashboard" class="sidenavItem <?php echo ($activeSection === 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>  Mon Compte
            </a>
            <a href="userAccount.php?section=mesLivres" class="sidenavItem <?php echo ($activeSection === 'mesLivres') ? 'active' : ''; ?>">
              Mes Livres
            </a>
            <a href="userAccount.php?section=profilParametres" class="sidenavItem <?php echo ($activeSection === 'profilParametres') ? 'active' : ''; ?>">
               Profil & Paramètres
            </a>
            <a href="logout.php" class="sidenavItem logout">
                Déconnexion
            </a>
        </nav>
    </aside>

    <div class="mainContent">
        <h1>
            <?php
                // Affiche le titre de la section active.
                if ($activeSection === 'dashboard') echo 'Mon Compte';
                else if ($activeSection === 'mesLivres') echo 'Mes Livres';
                else if ($activeSection === 'profilParametres') echo 'Profil & Paramètres';
            ?>
        </h1>

        <?php
        // Affiche les messages de session (succès, erreur ou info) s'ils existent.
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $messageType = $_SESSION['message_type'];
            echo "<div class='alert {$messageType}'>{$message}</div>";
            unset($_SESSION['message']); // Supprime le message de la session après affichage.
            unset($_SESSION['message_type']); // Supprime le type de message.
        }
        ?>

        <?php if ($activeSection === 'dashboard') : // Début de la section Mon compte ?>

            <section class="dashboardStats">
                <div class="statCard">
                    <p class="statValue"><?php echo $empruntsEnCours; ?></p>
                    <p class="statLabel">Emprunts en cours</p>
                </div>
                <div class="statCard">
                    <p class="statValue"><?php echo $livresRetournes; ?></p>
                    <p class="statLabel">Livres rendus</p>
                </div>
                <div class="statCard">
                    <p class="statValue"><?php echo $livresDansCollection; ?></p>
                    <p class="statLabel">Mes Livres </p>
                </div>
                <div class="statCard">
                    <p class="statValue"><?php echo $livresLus; ?></p>
                    <p class="statLabel">Livres lus</p>
                </div>
            </section>

        <?php elseif ($activeSection === 'mesLivres') : // Début de la section Mes Livres ?>
            <section class="addToListeForm">
                <h3>Ajouter un livre</h3>
                <form action="userAccount.php" method="POST" class="bookForm">
                    <input type="hidden" name="action" value="ajouterAListeLecture">
                    
                    <div class="form-group">
                        <label for="idLivre">Sélectionner un livre :</label>
                        <select id="idLivre" name="idLivre" required>
                            <option value="">-- Choisir un livre --</option>
                            <?php foreach ($livresDisponibles as $livre) : // Liste déroulante des livres disponibles. ?>
                                <option value="<?php echo htmlspecialchars($livre['id']); ?>">
                                    <?php echo htmlspecialchars($livre['titre'] . ' par ' . $livre['auteur']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dateEmprunt">Date d'emprunt :</label>
                        <input type="date" id="dateEmprunt" name="dateEmprunt" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dateRetour">Date de retour (optionnel, si déjà retourné) :</label>
                        <input type="date" id="dateRetour" name="dateRetour">
                    </div>
                    <button type="submit" class="bouton primaireBouton">Ajouter à ma liste</button>
                </form>
            </section>

            <section class="myBooksList">
                <h3>Lecture actuelle</h3>
                <?php if (empty($maBibliotheque)) : // Message si la liste est vide. ?>
                    <p class="infoMessage">Votre liste de lecture est vide. Ajoutez-y des livres ci-dessus !</p>
                <?php else : ?>
                    <div class="booksGrid">
                        <?php foreach ($maBibliotheque as $livreDansListe) : 
                            // Calcule la date de retour prévue (30 jours après l'emprunt).
                            $dateRetourPrevue = date('Y-m-d', strtotime($livreDansListe['date_emprunt'] . ' + 30 days'));
                            $statusEmprunt = "Non rendu";
                            $classStatus = "statusEnCours";

                            // Détermine le statut du livre (rendu, en retard, à rendre).
                            if ($livreDansListe['date_retour']) {
                                if (strtotime($livreDansListe['date_retour']) <= time()) {
                                    $statusEmprunt = "Rendu le " . date('d/m/Y', strtotime($livreDansListe['date_retour']));
                                    $classStatus = "statusRendu";
                                } else {
                                    // Si date_retour est dans le futur, ce n'est pas rendu mais prévu.
                                    $statusEmprunt = "Retour prévu le " . date('d/m/Y', strtotime($livreDansListe['date_retour']));
                                    $classStatus = "statusPrevu";
                                }
                            } else if (strtotime($dateRetourPrevue) < time()) {
                                $statusEmprunt = "En retard (prévu le " . date('d/m/Y', strtotime($dateRetourPrevue)) . ")";
                                $classStatus = "statusRetard";
                            } else {
                                $statusEmprunt = "À rendre avant le " . date('d/m/Y', strtotime($dateRetourPrevue));
                            }

                            // Détermine si le livre est lu, c'est à dire, s'il a été rendu et la date de retour est passée
                            $estLu = ($livreDansListe['date_retour'] && (strtotime($livreDansListe['date_retour']) <= time()));
                        ?>
                            <div class="bookCard">
                                <img src="images/<?php echo htmlspecialchars($livreDansListe['image'] ?? 'default_book_cover.png'); ?>"
									alt="Couverture du livre <?php echo htmlspecialchars($livreDansListe['titre']); ?>"
									class="bookCover">

                                <div class="bookInfo">
                                    <h4><?php echo htmlspecialchars($livreDansListe['titre']); ?></h4>
                                    <p class="bookAuthor">Auteur : <?php echo htmlspecialchars($livreDansListe['auteur']); ?></p>
                                    <p class="bookDetails">Édition : <?php echo htmlspecialchars($livreDansListe['maison_edition']); ?></p>
                                    <p class="bookDetails">Emprunté le : <?php echo htmlspecialchars(date('d/m/Y', strtotime($livreDansListe['date_emprunt']))); ?></p>
                                    <p class="bookDetails <?php echo $classStatus; ?>">Statut : <?php echo $statusEmprunt; ?></p>
                                    <p class="bookDetails <?php echo $estLu ? 'statusRendu' : ''; ?>">
                                        Lu : <?php echo $estLu ? 'Oui' : 'Non'; ?>
                                    </p>
                                </div>
                                <div class="bookActions">
                                    <button class="bouton primaireBouton readBookButton"
                                            data-id-livre="<?php echo htmlspecialchars($livreDansListe['id_livre']); ?>"
                                            data-titre="<?php echo htmlspecialchars($livreDansListe['titre']); ?>"
                                            data-description="<?php echo htmlspecialchars($livreDansListe['description']); ?>">
                                        <i class="fas fa-book-reader"></i> Lire
                                    </button>

                                    <?php if (!$livreDansListe['date_retour'] || (strtotime($livreDansListe['date_retour']) > time())) : // Afficher "Rendre" si pas encore rendu ou si la date de retour est dans le futur ?>
                                        <form action="userAccount.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="rendreLivre">
                                            <input type="hidden" name="idLivreARendre" value="<?php echo htmlspecialchars($livreDansListe['id_livre']); ?>">
                                            <button type="submit" class="bouton dangerBouton" onclick="return confirm('Confirmez-vous le retour de ce livre ?');">
                                                <i class="fas fa-undo-alt"></i> Rendre
                                            </button>
                                        </form>
                                    <?php else : ?>
                                        <button class="bouton secondaireBouton" disabled>
                                            <i class="fas fa-check"></i> Déjà rendu
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="bouton secondaireBouton editEntryButton"
                                            data-id-livre="<?php echo htmlspecialchars($livreDansListe['id_livre']); ?>"
                                            data-titre="<?php echo htmlspecialchars($livreDansListe['titre']); ?>"
                                            data-date-emprunt="<?php echo htmlspecialchars($livreDansListe['date_emprunt']); ?>"
                                            data-date-retour="<?php echo htmlspecialchars($livreDansListe['date_retour'] ?? ''); ?>">
                                            <i class="fas fa-edit"></i> Modifier
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        <?php elseif ($activeSection === 'profilParametres') : // Début de la section Profil & Paramètres ?>
            <section class="container">
                <h3>Mes informations personnelles</h3>
                <form action="userAccount.php" method="POST" class="bookForm">
                    <input type="hidden" name="action" value="updateProfil">
                    <div class="form-group">
                        <label for="profileNom">Nom :</label>
                        <input type="text" id="profileNom" name="nomLecteur" value="<?php echo htmlspecialchars($nomLecteur); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="profilePrenom">Prénom :</label>
                        <input type="text" id="profilePrenom" name="prenomLecteur" value="<?php echo htmlspecialchars($prenomLecteur); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="profileEmail">Email :</label>
                        <input type="email" id="profileEmail" name="emailLecteur" value="<?php echo htmlspecialchars($emailLecteur); ?>" required>
                    </div>
                    <button type="submit" class="bouton primaireBouton">Enregistrer les modifications</button>
                </form>

                <h3 class="mt-4">Paramètres du compte</h3>
                <div class="dashboardSection quickActions">
                    <div class="actionsGrid">
                        <a href="deconnexion.php" class="bouton secondaireBouton"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
                        <form action="userAccount.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="deleteAccount">
							<button type="submit" class="bouton dangerBouton" onclick="return confirm('ATTENTION : Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et effacera toutes vos données.');">
                                <i class="fas fa-trash-alt"></i> Supprimer mon compte
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        <?php endif;  ?>
    </div>
</main>

<div id="readBookModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2 id="readBookTitle"></h2>
        <p id="readBookDescription"></p>
        <div class="modal-actions">
            <button class="bouton secondaireBouton close-button">Fermer</button>
        </div>
    </div>
</div>

<div id="editEntryModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Modifier l'entrée du livre : <span id="editBookTitle"></span></h2>
        <form action="userAccount.php" method="POST" class="bookForm">
            <input type="hidden" name="action" value="modifierListeLecture">
            <input type="hidden" id="editIdLivre" name="idLivre">
            
            <div class="form-group">
                <label for="editDateEmprunt">Date d'emprunt :</label>
                <input type="date" id="editDateEmprunt" name="dateEmprunt" required>
            </div>
            <div class="form-group">
                <label for="editDateRetour">Date de retour :</label>
                <input type="date" id="editDateRetour" name="dateRetour">
            </div>
            <button type="submit" class="bouton primaireBouton">Enregistrer les modifications</button>
            <button type="button" class="bouton secondaireBouton close-button">Annuler</button>
        </form>
    </div>
</div>

<?php
include ('includes/footer.php');
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const readBookModal = document.getElementById('readBookModal');
        const editEntryModal = document.getElementById('editEntryModal');
        const closeButtons = document.querySelectorAll('.close-button');

        if (readBookModal) readBookModal.style.display = 'none';
        if (editEntryModal) editEntryModal.style.display = 'none';
        document.body.classList.remove('modal-open'); 

        // Fonction pour ouvrir une modale
        function openModal(modal) {
            if (modal) {
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }

        // Fonction pour fermer une modale
        function closeModal(modal) {
            if (modal) {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }

        // Fermer la fenetres avec les boutons de fermeture
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const parentModal = button.closest('.modal');
                if (parentModal) {
                    closeModal(parentModal);
                }
            });
        });

        // Fermer la fenetres en cliquant en dehors
        window.addEventListener('click', function(event) {
            if (event.target == readBookModal) {
                closeModal(readBookModal);
            }
            if (event.target == editEntryModal) {
                closeModal(editEntryModal);
            }
        });

        // Gestion du bouton Lire le livre
        document.querySelectorAll('.readBookButton').forEach(button => {
            button.addEventListener('click', function() {
                const title = this.dataset.titre;
                const description = this.dataset.description;
                
                document.getElementById('readBookTitle').textContent = title;
                document.getElementById('readBookDescription').textContent = description;
                
                openModal(readBookModal);
            });
        });

        // Gestion du bouton Modifier
        document.querySelectorAll('.editEntryButton').forEach(button => {
            button.addEventListener('click', function() {
                const idLivre = this.dataset.idLivre;
                const titre = this.dataset.titre;
                const dateEmprunt = this.dataset.dateEmprunt;
                const dateRetour = this.dataset.dateRetour;

                document.getElementById('editBookTitle').textContent = titre;
                document.getElementById('editIdLivre').value = idLivre;
                document.getElementById('editDateEmprunt').value = dateEmprunt;
                
                document.getElementById('editDateRetour').value = dateRetour === 'null' ? '' : dateRetour; 

                openModal(editEntryModal);
            });
        });

        
        const sidenavItems = document.querySelectorAll('.sidenavItem');
        sidenavItems.forEach(item => {
            item.addEventListener('click', function() {
                // Supprime la classe active de tous les éléments
                sidenavItems.forEach(navItem => navItem.classList.remove('active'));
                // Ajoute la classe active à l'élément cliqué
                this.classList.add('active');
            });
        });
    });
</script>
