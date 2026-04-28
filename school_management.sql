CREATE TABLE professeurs (
  id SERIAL PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL
);

INSERT INTO professeurs (id, nom, prenom) VALUES
(1, 'Delfosse', 'M.'),
(2, 'Félicie', 'C.'),
(3, 'Dupont', 'J.'),
(4, 'Fradet', 'P.'),
(5, 'Ponthier', 'D.');

SELECT setval('professeurs_id_seq', 6);

CREATE TABLE classes (
  id SERIAL PRIMARY KEY,
  nom VARCHAR(20) NOT NULL,
  id_professeur INTEGER DEFAULT NULL REFERENCES professeurs(id)
);

INSERT INTO classes (id, nom, id_professeur) VALUES
(1, '6° A', 1),
(2, '6° C', 2),
(3, '4° C', 3),
(4, '3° C', 4),
(5, '5° B', 5);

SELECT setval('classes_id_seq', 6);

CREATE TABLE eleves (
  id SERIAL PRIMARY KEY,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  id_classe INTEGER DEFAULT NULL REFERENCES classes(id)
);

INSERT INTO eleves (id, nom, prenom, id_classe) VALUES
(1, 'CICUTTINI', 'Nino', 1),
(2, 'MARTIN', 'Emma', 1),
(3, 'BERNARD', 'Lucas', 1),
(4, 'DUBOIS', 'Léa', 1),
(5, 'SOUAL', 'Clara', 2),
(6, 'PETIT', 'Hugo', 2),
(7, 'MOREAU', 'Inès', 2),
(8, 'SIMON', 'Tom', 2),
(9, 'ROBERT', 'Camille', 3),
(10, 'RICHARD', 'Nathan', 3),
(11, 'LAURENT', 'Zoé', 3),
(12, 'LEFEVRE', 'Antoine', 4),
(13, 'GARCIA', 'Manon', 4),
(14, 'THOMAS', 'Raphaël', 4),
(15, 'ROUX', 'Jade', 5),
(16, 'DAVID', 'Théo', 5),
(17, 'MARTINEZ', 'Chloé', 5);

SELECT setval('eleves_id_seq', 18);

CREATE TABLE matieres (
  id SERIAL PRIMARY KEY,
  nom VARCHAR(50) NOT NULL
);

INSERT INTO matieres (id, nom) VALUES
(1, 'Histoire'),
(2, 'Mathématiques'),
(3, 'Français'),
(4, 'Sciences'),
(5, 'Anglais');

SELECT setval('matieres_id_seq', 6);

CREATE TABLE notes (
  id SERIAL PRIMARY KEY,
  id_eleve INTEGER DEFAULT NULL REFERENCES eleves(id),
  id_matiere INTEGER DEFAULT NULL REFERENCES matieres(id),
  nom_evaluation VARCHAR(100) NOT NULL,
  note DECIMAL(4,2) NOT NULL,
  appreciation VARCHAR(255) DEFAULT NULL,
  date DATE DEFAULT NULL
);

INSERT INTO notes (id, id_eleve, id_matiere, nom_evaluation, note, appreciation, date) VALUES
(1, 1, 1, 'DS-2', 14.00, 'Peut mieux faire', '2024-11-15'),
(2, 1, 2, 'Contrôle-1', 17.00, 'Très bon travail', '2024-11-20'),
(3, 1, 3, 'Rédaction', 13.00, 'Bon effort', '2024-11-25'),
(4, 2, 3, 'Rédaction', 18.00, 'Excellent travail', '2024-11-15'),
(5, 2, 1, 'DS-1', 12.00, 'Des efforts à faire', '2024-11-20'),
(6, 2, 5, 'Expression', 16.00, 'Très bien', '2024-11-25'),
(7, 3, 2, 'Contrôle-2', 11.00, 'Doit revoir les bases', '2024-11-15'),
(8, 3, 4, 'TP-2', 14.00, 'Correct', '2024-11-20'),
(9, 3, 1, 'DS-2', 13.00, 'Peut mieux faire', '2024-11-25'),
(10, 4, 5, 'Compréhension', 20.00, 'Parfait', '2024-11-15'),
(11, 4, 3, 'Rédaction', 17.00, 'Très bien', '2024-11-20'),
(12, 4, 2, 'Contrôle-1', 15.00, 'Bien', '2024-11-25'),
(13, 5, 2, 'Contrôle-1', 19.00, 'Excellent', '2024-11-15'),
(14, 5, 3, 'Dictée', 16.00, 'Très bien', '2024-11-20'),
(15, 5, 4, 'TP-1', 15.00, 'Bonne participation', '2024-11-25'),
(16, 6, 4, 'TP-1', 12.00, 'Passable', '2024-11-15'),
(17, 6, 2, 'Contrôle-2', 10.00, 'Insuffisant', '2024-11-20'),
(18, 6, 1, 'DS-1', 14.00, 'Assez bien', '2024-11-25'),
(19, 7, 3, 'Dictée', 15.00, 'Bien', '2024-11-15'),
(20, 7, 5, 'Expression', 18.00, 'Excellent', '2024-11-20'),
(21, 7, 4, 'TP-2', 13.00, 'Correct', '2024-11-25'),
(22, 8, 2, 'Contrôle-1', 16.00, 'Bien', '2024-11-15'),
(23, 8, 1, 'DS-2', 11.00, 'Des lacunes', '2024-11-20'),
(24, 8, 3, 'Rédaction', 14.00, 'Assez bien', '2024-11-25'),
(26, 1, 5, 'ds2', 20.00, 'ENCORE PARFAIT', '2008-01-22');

SELECT setval('notes_id_seq', 27);