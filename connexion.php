<?php
session_start();

// Connexion à la base de données 
require_once 'connexion_db.php';

$emailValue = '';
$displayNewUserFields = false;

$redirectAfterLogin = isset($_GET['redirect']) ? $_GET['redirect'] : null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $nom = isset($_POST['nom']) ? filter_var($_POST['nom'], FILTER_SANITIZE_STRING) : ''; 
    $prenom = isset($_POST['prenom']) ? filter_var($_POST['prenom'], FILTER_SANITIZE_STRING) : '';  

    $emailValue = htmlspecialchars($email);

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Veuillez entrer une adresse email valide.";
        $_SESSION['message_type'] = "error";
    } else {

        $querySelectLecteur = "SELECT id, nom, prenom, email FROM lecteurs WHERE email = ?";
        $stmtSelectLecteur = $connexion->prepare($querySelectLecteur);

        if ($stmtSelectLecteur) {  
            $stmtSelectLecteur->bind_param("s", $email); 
            $stmtSelectLecteur->execute();  
            $resultLecteur = $stmtSelectLecteur->get_result();
            $lecteur = $resultLecteur->fetch_assoc(); 
            $stmtSelectLecteur->close(); 

            if ($lecteur) {

                // Stockage session
                $_SESSION['idLecteur'] = $lecteur['id'];
                $_SESSION['emailLecteur'] = $lecteur['email'];
                $_SESSION['nomLecteur'] = $lecteur['nom'];
                $_SESSION['prenomLecteur'] = $lecteur['prenom'];

                $_SESSION['message'] = "Hi, " . htmlspecialchars($lecteur['prenom']) . " !";
                $_SESSION['message_type'] = "success";

                if (!empty($_SESSION['redirect'])) {
                    $go = $_SESSION['redirect'];
                    unset($_SESSION['redirect']);
                    header("Location: $go");
                    exit();
                }

                header("Location:userAccount.php");
                exit();

            } else {
                if (empty($nom) || empty($prenom)) {
                    $displayNewUserFields = true;
                    $_SESSION['message'] = "Cet email n'est pas encore enregistré. Veuillez compléter votre nom et prénom pour créer un compte.";
                    $_SESSION['message_type'] = "info";
                } else {
                    $queryInsertLecteur = "INSERT INTO lecteurs (nom, prenom, email) VALUES (?, ?, ?)";
                    $stmtInsertLecteur = $connexion->prepare($queryInsertLecteur);

                    if ($stmtInsertLecteur) { 
                        $stmtInsertLecteur->bind_param("sss", $nom, $prenom, $email);
                        $stmtInsertLecteur->execute(); 
                        $nouveauIdLecteur = $connexion->insert_id; 
                        $stmtInsertLecteur->close(); 

                        $_SESSION['idLecteur'] = $nouveauIdLecteur;
                        $_SESSION['emailLecteur'] = $email;
                        $_SESSION['nomLecteur'] = $nom;
                        $_SESSION['prenomLecteur'] = $prenom;

                        $_SESSION['message'] = "Bienvenue, " . htmlspecialchars($prenom) . " ! Votre compte a été créé.";
                        $_SESSION['message_type'] = "success";

                        if (!empty($_SESSION['redirect'])) {
                            $go = $_SESSION['redirect'];
                            unset($_SESSION['redirect']);
                            header("Location: $go");
                            exit();
                        }

                        header("Location:userAccount.php"); 
                        exit();
                    } else {
                        error_log("Erreur insertion lecteur: " . $connexion->error);
                        $_SESSION['message'] = "Une erreur est survenue lors de l'inscription.";
                        $_SESSION['message_type'] = "error";
                    }
                }
            }
        }
    }
}

$connexion->close();

include('includes/header.php');
?>
    <main>
        <div class="container">
            <div class="connexion-box">
                <h2>Connexion </h2>

                <?php
					if (isset($_SESSION['message'])) {
						echo "<div class='alert {$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
						unset($_SESSION['message'], $_SESSION['message_type']);
					}
				?>

                <p class="description">Saisissez votre email pour vous connecter ou créer un compte.</p>

                <form action="connexion.php" method="POST">
                    <div class="form-group">
                        <label for="email">Adresse Email :</label>
                        <input type="email" id="email" name="email" placeholder="nom.email@exemple.com" value="<?php echo $emailValue; ?>" required>
                    </div>

                    <div id="newUserFields" style="display: none;">
                        <div class="form-group">
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" placeholder="Votre nom">
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom">
                        </div>
                    </div>

                    <button type="submit" class="bouton primaireBouton">Continuer</button>
                </form>
            </div>
        </div>
    </main>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            
            const newUserFieldsDiv = document.getElementById('newUserFields');
            const nomInput = document.getElementById('nom');
            const prenomInput = document.getElementById('prenom');

            const shouldDisplayNewUserFields = <?php echo json_encode($displayNewUserFields); ?>;

            if (shouldDisplayNewUserFields) {
               
                newUserFieldsDiv.style.display = 'block'; 
                nomInput.setAttribute('required', 'required'); 
                prenomInput.setAttribute('required', 'required'); 
            } else {
                
                newUserFieldsDiv.style.display = 'none'; 
                nomInput.removeAttribute('required'); 
                prenomInput.removeAttribute('required'); 
            }
        });
    </script>

<?php
include('includes/footer.php'); 
?>
