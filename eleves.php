<?php
require 'connexion.php';

// Récupérer toutes les classes pour le menu déroulant
$stmtClasses = $pdo->query("
    SELECT c.id, c.nom AS nom_classe, CONCAT(p.prenom, ' ', p.nom) AS professeur
    FROM classes c
    LEFT JOIN professeurs p ON c.id_professeur = p.id
    ORDER BY c.nom
");
$classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la classe sélectionnée (depuis l'URL ou la première par défaut)
$classeId = isset($_GET['classe']) ? (int)$_GET['classe'] : $classes[0]['id'];

// Trouver la classe courante
$classeCourante = null;
foreach ($classes as $c) {
    if ($c['id'] == $classeId) {
        $classeCourante = $c;
        break;
    }
}

// Récupérer les élèves de la classe sélectionnée
$stmtEleves = $pdo->prepare("
    SELECT id, nom, prenom FROM eleves
    WHERE id_classe = ?
    ORDER BY nom
");
$stmtEleves->execute([$classeId]);
$eleves = $stmtEleves->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des élèves</title>
    <link rel="stylesheet" href="eleves.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
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

<footer>
    <a href="#">Support</a>
    <a href="#">FAQ</a>
    <a href="#">Aide</a>
</footer>

</body>
</html>