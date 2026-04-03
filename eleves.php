<?php
require 'connexion.php';

// Traitement du formulaire d'ajout de notes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_notes'])) {
    $idMatiere = (int)$_POST['matiere'];
    $nomEvaluation = trim($_POST['nom_evaluation']);
    $date = $_POST['date'];

    $stmtInsert = $pdo->prepare("
        INSERT INTO notes (id_eleve, id_matiere, nom_evaluation, note, appreciation, date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($_POST['notes'] as $idEleve => $note) {
        if ($note !== '') {
            $appreciation = $_POST['appreciations'][$idEleve] ?? '';
            $stmtInsert->execute([$idEleve, $idMatiere, $nomEvaluation, $note, $appreciation, $date]);
        }
    }

    header('Location: eleves.php?classe=' . $_POST['classe_id'] . '&success=1');
    exit;
}

// Récupérer toutes les classes pour le menu déroulant
$stmtClasses = $pdo->query("
    SELECT c.id, c.nom AS nom_classe, CONCAT(p.prenom, ' ', p.nom) AS professeur
    FROM classes c
    LEFT JOIN professeurs p ON c.id_professeur = p.id
    ORDER BY c.nom
");
$classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la classe sélectionnée
$classeId = isset($_GET['classe']) ? (int)$_GET['classe'] : $classes[0]['id'];

$classeCourante = null;
foreach ($classes as $c) {
    if ($c['id'] == $classeId) {
        $classeCourante = $c;
        break;
    }
}

// Récupérer les élèves de la classe
$stmtEleves = $pdo->prepare("
    SELECT id, nom, prenom FROM eleves
    WHERE id_classe = ?
    ORDER BY nom
");
$stmtEleves->execute([$classeId]);
$eleves = $stmtEleves->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les matières pour le formulaire
$matieres = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des élèves</title>
    <link rel="stylesheet" href="eleves.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Caveat:wght@500&family=Dancing+Script:wght@600&display=swap" rel="stylesheet">

</head>

<body>

<header>
    <div class="left"><span>MS</span></div>
    <nav>
        <a href="index.php">Classes</a>
        <a href="eleves.php">Élèves</a>
    </nav>
    <div class="right">Enseignant : Léa Dupuis</div>
</header>

<div class="main">

    <a class="back-arrow" href="index.php">←</a>

    <h1>
        <?php if ($classeCourante): ?>
            Classe <?= htmlspecialchars($classeCourante['nom_classe']) ?> — <?= htmlspecialchars($classeCourante['professeur']) ?>
        <?php else: ?>
            Gestion des élèves
        <?php endif; ?>
    </h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-msg">✓ Les notes ont bien été enregistrées !</div>
    <?php endif; ?>

    <div class="search-area">
        <label>Classe</label>
        <select id="select-classe" onchange="window.location.href='eleves.php?classe='+this.value">
            <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $classeId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['nom_classe']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button class="btn-ajouter" onclick="document.getElementById('modal').classList.add('active')">
        + Ajouter des notes
    </button>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eleves as $eleve): ?>
                <tr>
                    <td><?= htmlspecialchars($eleve['nom']) ?></td>
                    <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                    <td><a class="notes-link" href="notes.php?eleve=<?= $eleve['id'] ?>">Voir notes</a></td>
                </tr>
                <?php endforeach; ?>
                <?php for ($i = count($eleves); $i < 7; $i++): ?>
                <tr><td></td><td></td><td></td></tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- MODAL -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <h2>Ajouter des notes</h2>
        <form method="POST" action="eleves.php">
            <input type="hidden" name="ajouter_notes" value="1">
            <input type="hidden" name="classe_id" value="<?= $classeId ?>">

            <div class="modal-fields">
                <div>
                    <label>Évaluation</label>
                    <input type="text" name="nom_evaluation" placeholder="ex: DS-3" required>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="date" required>
                </div>
                <div>
                    <label>Matière</label>
                    <select name="matiere" required>
                        <?php foreach ($matieres as $matiere): ?>
                            <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Note /20</th>
                        <th>Appréciation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eleves as $eleve): ?>
                    <tr>
                        <td><?= htmlspecialchars($eleve['nom']) ?></td>
                        <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                        <td><input type="number" name="notes[<?= $eleve['id'] ?>]" min="0" max="20" step="0.5" placeholder="—"></td>
                        <td><input type="text" name="appreciations[<?= $eleve['id'] ?>]" placeholder="Appréciation"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="modal-buttons">
                <button type="button" class="btn-annuler" onclick="document.getElementById('modal').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn-valider">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
</script>

<footer>
    <a href="#">Support</a>
    <a href="#">FAQ</a>
    <a href="#">Aide</a>
</footer>

</body>
</html>