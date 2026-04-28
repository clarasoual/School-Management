<?php
require 'connexion.php';

// Récupérer les classes avec le nom du prof et la moyenne des élèves
$stmt = $pdo->query("
    SELECT 
        c.id,
        c.nom AS nom_classe,
        CONCAT(p.prenom, ' ', p.nom) AS professeur,
        COUNT(DISTINCT e.id) AS nb_eleves,
        ROUND(AVG(n.note), 2) AS moyenne
    FROM classes c
    LEFT JOIN professeurs p ON c.id_professeur = p.id
    LEFT JOIN eleves e ON e.id_classe = c.id
    LEFT JOIN notes n ON n.id_eleve = e.id
    GROUP BY c.id
    ORDER BY c.nom
");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>School Management</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400&family=Pacifico&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
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
<h1>School Management</h1>
<div class="container">
<?php foreach ($classes as $classe) : ?>
<div class="card" onclick="goToClass(<?= $classe['id'] ?>)" style="cursor:pointer;">
    <div class="card-title"><?= htmlspecialchars($classe['nom_classe']) ?></div>
    <p><strong>Élèves :</strong> <?= $classe['nb_eleves'] ?></p>
<p><strong>Moyenne :</strong> <?= $classe['moyenne'] !== null ? number_format($classe['moyenne'], 2, ',', '') . '/20' : 'Aucune note' ?></p>    <p><strong>Prof. Principal :</strong> <?= htmlspecialchars($classe['professeur']) ?></p>
    <div class="arrow">➜</div>
</div>
<?php endforeach; ?>
</div>
<footer>
<a href="#">Support</a>
<a href="#">FAQ</a>
<a href="#">Aide</a>
</footer>

<script>
function goToClass(id) {
  window.location.href = 'eleves.php?classe=' + id;
}
</script>
</body>
</html>