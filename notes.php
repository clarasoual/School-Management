<?php
require 'connexion.php';

// Suppression d'une note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_note'])) {
    $stmtDelete = $pdo->prepare("DELETE FROM notes WHERE id = ? AND id_eleve = ?");
    $stmtDelete->execute([(int)$_POST['note_id'], (int)$_POST['eleve_id']]);
    header('Location: notes.php?eleve=' . $_POST['eleve_id'] . '&deleted=1');
    exit;
}

// Traitement du formulaire d'ajout de note individuelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_note'])) {
    $stmtInsert = $pdo->prepare("
        INSERT INTO notes (id_eleve, id_matiere, nom_evaluation, note, appreciation, date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtInsert->execute([
        (int)$_POST['eleve_id'],
        (int)$_POST['matiere'],
        trim($_POST['nom_evaluation']),
        $_POST['note'],
        trim($_POST['appreciation']),
        $_POST['date']
    ]);
    header('Location: notes.php?eleve=' . $_POST['eleve_id'] . '&success=1');
    exit;
}

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

// Récupérer toutes les notes de l'élève (avec l'id de la note)
$stmtNotes = $pdo->prepare("
    SELECT n.id, n.nom_evaluation, n.note, n.appreciation, m.nom AS matiere, m.id AS id_matiere
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

// Toutes les matières pour le formulaire d'ajout
$toutesLesMatières = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Caveat:wght@500&family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
    <style>
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 1000;
            justify-content: center;
            align-items: flex-start;
            padding-top: 80px;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: #f6f2dc;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .modal h2 {
            font-family: "Caveat", cursive;
            font-size: 32px;
            color: #5e503f;
            margin: 0 0 20px 0;
        }
        .modal-field {
            margin-bottom: 14px;
        }
        .modal-field label {
            font-size: 13px;
            color: #7b6a58;
            display: block;
            margin-bottom: 4px;
        }
        .modal-field input,
        .modal-field select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #5e503f;
            border-radius: 6px;
            background: #fff;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            color: #5e503f;
            outline: none;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .btn-valider {
            background: #7b6a58;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-valider:hover { background: #5e503f; }
        .btn-annuler {
            background: transparent;
            color: #5e503f;
            border: 2px solid #5e503f;
            padding: 10px 24px;
            border-radius: 6px;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-ajouter {
            background: #7b6a58;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .btn-ajouter:hover { background: #5e503f; }
        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .delete-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .btn-supprimer {
            background: none;
            border: none;
            color: #a05050;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
        }
        .btn-supprimer:hover { color: #7b0000; }
    </style>
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

    <?php if (isset($_GET['success'])): ?>
        <div class="success-msg">✓ La note a bien été enregistrée !</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="delete-msg">✓ La note a bien été supprimée.</div>
    <?php endif; ?>

    <button class="btn-ajouter" onclick="document.getElementById('modal').classList.add('active')">
        + Ajouter une note
    </button>

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
                    <th></th>
                </tr>
            </thead>
            <tbody id="tbody-notes">
                <?php foreach ($notes as $note): ?>
                <tr data-matiere="<?= $note['id_matiere'] ?>">
                    <td><?= htmlspecialchars($note['matiere']) ?></td>
                    <td><?= htmlspecialchars($note['nom_evaluation']) ?></td>
                    <td><?= number_format($note['note'], 2, ',', '') ?>/20</td>
                    <td><?= htmlspecialchars($note['appreciation']) ?></td>
                    <td>
                        <form method="POST" action="notes.php" onsubmit="return confirm('Supprimer cette note ?')">
                            <input type="hidden" name="supprimer_note" value="1">
                            <input type="hidden" name="note_id" value="<?= $note['id'] ?>">
                            <input type="hidden" name="eleve_id" value="<?= $eleveId ?>">
                            <button type="submit" class="btn-supprimer" title="Supprimer">✕</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php for ($i = count($notes); $i < 6; $i++): ?>
                <tr class="vide"><td></td><td></td><td></td><td></td><td></td></tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- MODAL -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <h2>Ajouter une note</h2>
        <form method="POST" action="notes.php">
            <input type="hidden" name="ajouter_note" value="1">
            <input type="hidden" name="eleve_id" value="<?= $eleveId ?>">

            <div class="modal-field">
                <label>Évaluation</label>
                <input type="text" name="nom_evaluation" placeholder="ex: DS-3" required>
            </div>
            <div class="modal-field">
                <label>Date</label>
                <input type="date" name="date" required>
            </div>
            <div class="modal-field">
                <label>Matière</label>
                <select name="matiere" required>
                    <?php foreach ($toutesLesMatières as $matiere): ?>
                        <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-field">
                <label>Note /20</label>
                <input type="number" name="note" min="0" max="20" step="0.5" required>
            </div>
            <div class="modal-field">
                <label>Appréciation</label>
                <input type="text" name="appreciation" placeholder="ex: Très bien">
            </div>

            <div class="modal-buttons">
                <button type="button" class="btn-annuler" onclick="document.getElementById('modal').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn-valider">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
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

    videsRows.forEach(row => row.style.display = filtre === 'toutes' ? '' : 'none');

    const moyenne = notesFiltrees.length > 0
        ? (notesFiltrees.reduce((acc, n) => acc + parseFloat(n.note), 0) / notesFiltrees.length).toFixed(2).replace('.', ',')
        : null;

    const select = document.getElementById('select-matiere');
    const label = filtre === 'toutes' ? 'Moyenne générale' : 'Moyenne ' + select.options[select.selectedIndex].text;
    document.getElementById('moyenne-display').textContent = moyenne ? label + ' : ' + moyenne + '/20' : '';
}

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