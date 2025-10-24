-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 24 oct. 2025 à 12:11
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `videos_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Actualité'),
(6, 'Autre'),
(3, 'Environnement'),
(2, 'Institutionnel'),
(4, 'Sécurité'),
(5, 'Sport');

-- --------------------------------------------------------

--
-- Structure de la table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `motdepasse` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifiant` (`identifiant`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `connexion`
--

INSERT INTO `connexion` (`id`, `identifiant`, `motdepasse`, `date_creation`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', '2025-10-24 11:29:16');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `liste_videos`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `liste_videos`;
CREATE TABLE IF NOT EXISTS `liste_videos` (
`categorie` varchar(100)
,`date_publication` datetime
,`description` text
,`fichier` varchar(255)
,`id` int
,`id_categorie` int
,`miniature` varchar(255)
,`titre` varchar(150)
);

-- --------------------------------------------------------

--
-- Structure de la table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `fichier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `miniature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_categorie` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_titre` (`titre`),
  KEY `idx_categorie` (`id_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `videos`
--

INSERT INTO `videos` (`id`, `titre`, `description`, `fichier`, `miniature`, `date_publication`, `id_categorie`) VALUES
(4, 'Le Département des collèges : La sécurité', '', 'Le_De__partement_des_colle__ges__La_s__curit__.mp4', 'Le_De__partement_des_colle__ges__La_s__curit__.jpg', '2025-10-24 10:00:21', 1),
(9, 'Le Département des collèges : Le numérique', 'test', 'Le_De__partement_des_colle__ges__Le_nume__rique.mp4', 'Le_De__partement_des_colle__ges__Le_nume__rique.jpg', '2025-10-24 12:39:29', 2);

-- --------------------------------------------------------

--
-- Structure de la vue `liste_videos`
--
DROP TABLE IF EXISTS `liste_videos`;

DROP VIEW IF EXISTS `liste_videos`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `liste_videos`  AS SELECT `v`.`id` AS `id`, `v`.`titre` AS `titre`, `v`.`description` AS `description`, `v`.`fichier` AS `fichier`, `v`.`miniature` AS `miniature`, `v`.`date_publication` AS `date_publication`, `c`.`nom` AS `categorie`, `c`.`id` AS `id_categorie` FROM (`videos` `v` join `categories` `c` on((`v`.`id_categorie` = `c`.`id`))) ORDER BY `v`.`date_publication` DESC ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `fk_videos_categories` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
