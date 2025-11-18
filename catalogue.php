<?php
// Démarre la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "bibliotheque_db");
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Pagination
$limitParPage = 12;
$pageActuelle = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($pageActuelle - 1) * $limitParPage;

// Filtres et tri
$triPar = $_GET['sort-by'] ?? 'recent';
$disponibiliteFiltre = $_GET['availability'] ?? 'all';

$whereConditions = [];
if ($disponibiliteFiltre === 'available') $whereConditions[] = "nombre_exemplaire > 0";
$whereClause = !empty($whereConditions) ? " WHERE " . implode(" AND ", $whereConditions) : "";

switch ($triPar) {
    case 'title-asc': $orderBy = "ORDER BY titre ASC"; break;
    case 'title-desc': $orderBy = "ORDER BY titre DESC"; break;
    case 'recent':
    default: $orderBy = "ORDER BY id DESC"; break;
}

// Total livres pour pagination
$countSql = "SELECT COUNT(id) AS totalLivres FROM livres" . $whereClause;
$countResult = $conn->query($countSql);
$totalLivres = $countResult ? $countResult->fetch_assoc()['totalLivres'] : 0;
$totalPages = ceil($totalLivres / $limitParPage);
$pageActuelle = max(1, min($pageActuelle, $totalPages));
$offset = ($pageActuelle - 1) * $limitParPage;

// Récupération livres page actuelle
$livresSql = "SELECT id, titre, auteur, nombre_exemplaire, image FROM livres $whereClause $orderBy LIMIT $limitParPage OFFSET $offset";
$livresResult = $conn->query($livresSql);
$livresAffiches = $livresResult ? $livresResult->fetch_all(MYSQLI_ASSOC) : [];

// Récupérer la wishlist de l'utilisateur connecté
$wishlistIds = [];
if (isset($_SESSION['idLecteur'])) {
    $idLecteur = $_SESSION['idLecteur'];
    $stmt = $conn->prepare("SELECT id_livre FROM liste_lecture WHERE id_lecteur = ?");
    if ($stmt) {
        $stmt->bind_param("i", $idLecteur);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $wishlistIds[] = $row['id_livre'];
        }
        $stmt->close();
    }
}

include('includes/header.php');
?>

<main class="mainContent">
    <section class="catalogueIntroduction">
        <div class="container">
            <h1>Notre Catalogue : <?php echo $totalLivres; ?> livres trouvés</h1>
        </div>
    </section>

    <section class="catalogueContenu">
        <div class="container">
            <form method="GET" action="catalogue.php" class="formulaireFiltres">
                <aside class="filtresAside">
                    <div class="filtreGroup">
                        <h3>Filtres Rapides</h3>
                        <ul>
                            <li><a href="#">Genres</a></li>
                            <li><a href="#">Auteurs</a></li>
                        </ul>
                    </div>

                    <div class="filtreGroup disponibiliteFiltre">
                        <h3>Disponibilité</h3>
                        <label>
                            <input type="radio" name="availability" value="available" <?php if ($disponibiliteFiltre === 'available') echo 'checked'; ?>> Disponible (Stock > 0)
                        </label>
                        <label>
                            <input type="radio" name="availability" value="all" <?php if ($disponibiliteFiltre === 'all') echo 'checked'; ?>> Tout
                        </label>
                    </div>

                    <div class="filtreGroup">
                        <h3>Options</h3>
                        <label>
                            <input type="checkbox" name="option" value="add-to-cart"> Ajouter à ma liste
                        </label>
                    </div>
                    <button type="submit" class="appliquerFiltresBouton bouton primaireBouton">Appliquer les filtres</button>
                </aside>
            </form>

            <div class="affichageCataloguePrincipal">
                <div class="optionsTri">
                    <span>Trier par :</span>
                    <select name="sort-by" onchange="window.location.href = 'catalogue.php?page=<?php echo $pageActuelle; ?>&sort-by=' + this.value + '&availability=<?php echo $disponibiliteFiltre; ?>'"> 
                        <option value="recent" <?php if ($triPar === 'recent') echo 'selected'; ?>>Plus récents</option>
                        <option value="title-asc" <?php if ($triPar === 'title-asc') echo 'selected'; ?>>Titre (A-Z)</option>
                        <option value="title-desc" <?php if ($triPar === 'title-desc') echo 'selected'; ?>>Titre (Z-A)</option>
                    </select>
                </div>

                <div class="livresGrille">
                    <?php if (!empty($livresAffiches)): ?>
                        <?php foreach ($livresAffiches as $livre): 
                            $disponible = $livre['nombre_exemplaire'] > 0;
                            $dejaAjoute = in_array($livre['id'], $wishlistIds);
                        ?>
                            <div class="livreCarte">
                                <img src="images/<?php echo htmlspecialchars($livre['image']); ?>" alt="Couverture de <?php echo htmlspecialchars($livre['titre']); ?>" class="couvertureLivre">
                                <h3><?php echo htmlspecialchars($livre['titre']); ?></h3>
                                <p class="auteur"><?php echo htmlspecialchars($livre['auteur']); ?></p>
                                <div class="actionsLivre">
                                    <a href="description.php?id=<?php echo $livre['id']; ?>" class="bouton petitBouton <?php if (!$disponible) echo 'desactive'; ?>">Lire</a>

                                    <a href="#" 
                                       class="bouton secondaireBouton <?php if ($dejaAjoute) echo 'desactive'; ?>" 
                                       data-livre-id="<?php echo $livre['id']; ?>">
                                       <?php echo $dejaAjoute ? 'Déjà ajouté' : 'Ajouter à ma liste'; ?>
                                    </a>
                                </div>
                                <?php if (!$disponible): ?>
                                    <span class="indisponibleEtiquette">Indisponible</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="aucunLivreTrouve">Aucun livre ne correspond à vos critères de recherche.</p>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li>
                                <?php $urlPrecedent = "catalogue.php?page=" . ($pageActuelle - 1) . "&sort-by=" . $triPar . "&availability=" . $disponibiliteFiltre; ?>
                                <a href="<?php if ($pageActuelle > 1) echo $urlPrecedent; else echo '#'; ?>" class="<?php if ($pageActuelle <= 1) echo 'desactive'; ?>">&laquo; Précédent</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): 
                                $urlPage = "catalogue.php?page=" . $i . "&sort-by=" . $triPar . "&availability=" . $disponibiliteFiltre;
                            ?>
                                <li>
                                    <a href="<?php echo $urlPage; ?>" class="<?php if ($i === $pageActuelle) echo 'pageActive'; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li>
                                <?php $urlSuivant = "catalogue.php?page=" . ($pageActuelle + 1) . "&sort-by=" . $triPar . "&availability=" . $disponibiliteFiltre; ?>
                                <a href="<?php if ($pageActuelle < $totalPages) echo $urlSuivant; else echo '#'; ?>" class="<?php if ($pageActuelle >= $totalPages) echo 'desactive'; ?>">Suivant &raquo;</a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addButtons = document.querySelectorAll('.actionsLivre .secondaireBouton');

    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const livreId = this.dataset.livreId;

            <?php if (!isset($_SESSION['idLecteur'])) : ?>
                // Redirection vers connexion avec retour automatique
                window.location.href = 'connexion.php?redirect=wishlist.php?id=' + livreId;
            <?php else : ?>
                if (this.classList.contains('desactive')) return;
                fetch('wishlist.php?id=' + livreId)
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(err => console.error(err));
            <?php endif; ?>
        });
    });
});
</script>

<?php
$conn->close();
include('includes/footer.php');
?>
