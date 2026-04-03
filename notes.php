<?php
require 'connexion.php';

// Récupérer l'id de l'élève depuis l'URL
$eleveId = isset($_GET['eleve']) ? (int)$_GET['eleve'] : 0;

// Récupérer les infos de l'élève et sa classe
$stmtEleve = $pdo->prepare("
    SELECT e.id, e.nom, e.prenom, c.nom AS nom_classe, c.id AS id_classe
    FROM eleves e
    LEFT JOIN classes c ON e.id_classe = c.id
    WHERE e.id = ?
");
$stmtEleve->execute([$eleveId]);
$eleve = $stmtEleve->fetch(PDO::FETCH_ASSOC);

// Récupérer toutes les notes de l'élève
$stmtNotes = $pdo->prepare("
    SELECT n.nom_evaluation, n.note, n.appreciation, m.nom AS matiere, m.id AS id_matiere
    FROM notes n
    LEFT JOIN matieres m ON n.id_matiere = m.id
    WHERE n.id_eleve = ?
    ORDER BY m.nom
");
$stmtNotes->execute([$eleveId]);
$notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les matières distinctes de cet élève pour le menu déroulant
$matieres = [];
foreach ($notes as $note) {
    if (!in_array($note['matiere'], array_column($matieres, 'nom'))) {
        $matieres[] = ['id' => $note['id_matiere'], 'nom' => $note['matiere']];
    }
}

// Calculer la moyenne générale
$moyenneGenerale = count($notes) > 0
    ? round(array_sum(array_column($notes, 'note')) / count($notes), 2)
    : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Élève — <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></title>
    <link rel="stylesheet" href="nino.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Pacifico&display=swap" rel="stylesheet">
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

<main class="main">
    <a class="back-arrow" href="eleves.php?classe=<?= $eleve['id_classe'] ?>">←</a>

    <div class="student-header">
        <div class="label">Élève</div>
        <div class="student-name">
            <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?>
            — <?= htmlspecialchars($eleve['nom_classe']) ?>
        </div>
    </div>

    <div class="search-area">
        <label>Matière</label>
        <select id="select-matiere" onchange="filtrerNotes(this.value)">
            <option value="toutes">Toutes les matières</option>
            <?php foreach ($matieres as $matiere): ?>
                <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <span id="moyenne-display">
            <?php if ($moyenneGenerale !== null): ?>
                Moyenne générale : <?= number_format($moyenneGenerale, 2, ',', '') ?>/20
            <?php endif; ?>
        </span>
    </div>

    <div class="table-container">
        <table class="grades-table" aria-label="Table des évaluations">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Évaluation</th>
                    <th>Note</th>
                    <th>Appréciation</th>
                </tr>
            </thead>
            <tbody id="tbody-notes">
                <?php foreach ($notes as $note): ?>
                <tr data-matiere="<?= $note['id_matiere'] ?>">
                    <td><?= htmlspecialchars($note['matiere']) ?></td>
                    <td><?= htmlspecialchars($note['nom_evaluation']) ?></td>
                    <td><?= number_format($note['note'], 2, ',', '') ?>/20</td>
                    <td><?= htmlspecialchars($note['appreciation']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php for ($i = count($notes); $i < 6; $i++): ?>
                <tr class="vide"><td></td><td></td><td></td><td></td></tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</main>

<footer>
    <a href="#">Support</a>
    <a href="#">FAQ</a>
    <a href="#">Aide</a>
</footer>

<script>
// Toutes les notes en JS pour le filtre et le calcul de moyenne
const toutesLesNotes = <?= json_encode($notes) ?>;

function filtrerNotes(filtre) {
    const rows = document.querySelectorAll('#tbody-notes tr:not(.vide)');
    const videsRows = document.querySelectorAll('#tbody-notes tr.vide');

    let notesFiltrees = toutesLesNotes;

    rows.forEach(function(row) {
        if (filtre === 'toutes' || row.dataset.matiere === filtre) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    if (filtre !== 'toutes') {
        notesFiltrees = toutesLesNotes.filter(n => n.id_matiere == filtre);
    }

    // Cacher les lignes vides pendant le filtre
    videsRows.forEach(row => row.style.display = filtre === 'toutes' ? '' : 'none');

    // Calculer et afficher la moyenne
    const moyenne = notesFiltrees.length > 0
        ? (notesFiltrees.reduce((acc, n) => acc + parseFloat(n.note), 0) / notesFiltrees.length).toFixed(2).replace('.', ',')
        : null;

    const select = document.getElementById('select-matiere');
    const label = filtre === 'toutes' ? 'Moyenne générale' : 'Moyenne ' + select.options[select.selectedIndex].text;
    document.getElementById('moyenne-display').textContent = moyenne ? label + ' : ' + moyenne + '/20' : '';
}
</script>

</body>
</html>