-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 03 avr. 2026 à 16:29
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `school_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `id_professeur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `id_professeur`) VALUES
(1, '6° A', 1),
(2, '6° C', 2),
(3, '4° C', 3),
(4, '3° C', 4),
(5, '5° B', 5);

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `id_classe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `nom`, `prenom`, `id_classe`) VALUES
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

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `nom`) VALUES
(1, 'Histoire'),
(2, 'Mathématiques'),
(3, 'Français'),
(4, 'Sciences'),
(5, 'Anglais');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `id_eleve` int(11) DEFAULT NULL,
  `id_matiere` int(11) DEFAULT NULL,
  `nom_evaluation` varchar(100) NOT NULL,
  `note` decimal(4,2) NOT NULL,
  `appreciation` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `id_eleve`, `id_matiere`, `nom_evaluation`, `note`, `appreciation`, `date`) VALUES
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

-- --------------------------------------------------------

--
-- Structure de la table `professeurs`
--

CREATE TABLE `professeurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `professeurs`
--

INSERT INTO `professeurs` (`id`, `nom`, `prenom`) VALUES
(1, 'Delfosse', 'M.'),
(2, 'Félicie', 'C.'),
(3, 'Dupont', 'J.'),
(4, 'Fradet', 'P.'),
(5, 'Ponthier', 'D.');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_professeur` (`id_professeur`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_classe` (`id_classe`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_eleve` (`id_eleve`),
  ADD KEY `id_matiere` (`id_matiere`);

--
-- Index pour la table `professeurs`
--
ALTER TABLE `professeurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `professeurs`
--
ALTER TABLE `professeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`id_professeur`) REFERENCES `professeurs` (`id`);

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_ibfk_1` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_eleve`) REFERENCES `eleves` (`id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
