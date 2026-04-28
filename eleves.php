<?php
require 'connexion.php';

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_notes'])) {
    $idMatiere = (int)($_POST['matiere'] ?? 0);
    $nomEvaluation = trim($_POST['nom_evaluation'] ?? '');
    $date = trim($_POST['date'] ?? '');

    if (empty($nomEvaluation)) {
        $erreurs[] = "Le nom de l'évaluation est obligatoire.";
    }
    if (empty($date)) {
        $erreurs[] = "La date est obligatoire.";
    }
    if ($idMatiere <= 0) {
        $erreurs[] = "La matière est invalide.";
    }

    $notesValides = false;
    if (isset($_POST['notes'])) {
        foreach ($_POST['notes'] as $idEleve => $note) {
            if ($note !== '') {
                $notesValides = true;
                if (!is_numeric($note) || $note < 0 || $note > 20) {
                    $erreurs[] = "Toutes les notes doivent être entre 0 et 20.";
                    break;
                }
            }
        }
    }
    if (!$notesValides && empty($erreurs)) {
        $erreurs[] = "Veuillez saisir au moins une note.";
    }

    if (empty($erreurs)) {
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
}

$stmtClasses = $pdo->query("
    SELECT c.id, c.nom AS nom_classe, p.prenom || ' ' || p.nom AS professeur
    FROM classes c
    LEFT JOIN professeurs p ON c.id_professeur = p.id
    ORDER BY c.nom
");
$classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);

$classeId = isset($_GET['classe']) ? (int)$_GET['classe'] : $classes[0]['id'];
if (!empty($_POST['classe_id'])) $classeId = (int)$_POST['classe_id'];

$classeCourante = null;
foreach ($classes as $c) {
    if ($c['id'] == $classeId) {
        $classeCourante = $c;
        break;
    }
}

$stmtEleves = $pdo->prepare("
    SELECT id, nom, prenom FROM eleves
    WHERE id_classe = ?
    ORDER BY nom
");
$stmtEleves->execute([$classeId]);
$eleves = $stmtEleves->fetchAll(PDO::FETCH_ASSOC);

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
            padding-top: 40px;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: #f6f2dc;
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 750px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .modal h2 {
            font-family: "Caveat", cursive;
            font-size: 32px;
            color: #5e503f;
            margin: 0 0 20px 0;
        }
        .modal-fields {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .modal-fields label {
            font-size: 13px;
            color: #7b6a58;
            display: block;
            margin-bottom: 4px;
        }
        .modal-fields input,
        .modal-fields select {
            padding: 8px 12px;
            border: 2px solid #5e503f;
            border-radius: 6px;
            background: #fff;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            color: #5e503f;
            outline: none;
        }
        .modal-fields input.erreur {
            border-color: #a05050;
        }
        .modal table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .modal th, .modal td {
            border: 2px solid #5e503f;
            padding: 10px;
            text-align: center;
        }
        .modal thead th {
            background: #eee7cf;
            font-weight: 500;
        }
        .modal input[type="number"],
        .modal input[type="text"] {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #c9b89d;
            border-radius: 4px;
            font-family: "Montserrat", sans-serif;
            font-size: 13px;
            background: #fff;
        }
        .modal input[type="number"].erreur {
            border-color: #a05050;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
            margin-bottom: 30px;
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
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 13px;
        }
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
<div class="modal-overlay" id="modal" <?= !empty($erreurs) ? 'class="modal-overlay active"' : '' ?>>
    <div class="modal">
        <h2>Ajouter des notes</h2>

        <?php if (!empty($erreurs)): ?>
            <div class="error-msg">
                <?php foreach ($erreurs as $e): ?>
                    <div>⚠ <?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="eleves.php" id="form-notes" onsubmit="return validerFormulaire()">
            <input type="hidden" name="ajouter_notes" value="1">
            <input type="hidden" name="classe_id" value="<?= $classeId ?>">

            <div class="modal-fields">
                <div>
                    <label>Évaluation</label>
                    <input type="text" name="nom_evaluation" id="champ-evaluation" placeholder="ex: DS-3" required>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="date" id="champ-date" required>
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
                        <td><input type="number" class="champ-note" name="notes[<?= $eleve['id'] ?>]" min="0" max="20" step="0.5" placeholder="—"></td>
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
function validerFormulaire() {
    let valide = true;

    const evaluation = document.getElementById('champ-evaluation');
    const date = document.getElementById('champ-date');
    const champsNotes = document.querySelectorAll('.champ-note');

    [evaluation, date].forEach(c => c.classList.remove('erreur'));
    champsNotes.forEach(c => c.classList.remove('erreur'));

    if (evaluation.value.trim() === '') {
        evaluation.classList.add('erreur');
        valide = false;
    }
    if (date.value === '') {
        date.classList.add('erreur');
        valide = false;
    }

    let auMoinsUneNote = false;
    champsNotes.forEach(function(champ) {
        if (champ.value !== '') {
            auMoinsUneNote = true;
            const val = parseFloat(champ.value);
            if (isNaN(val) || val < 0 || val > 20) {
                champ.classList.add('erreur');
                valide = false;
            }
        }
    });

    if (!auMoinsUneNote) {
        alert('Veuillez saisir au moins une note.');
        return false;
    }

    if (!valide) {
        alert('Veuillez corriger les champs en rouge avant d\'enregistrer.');
    }

    return valide;
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