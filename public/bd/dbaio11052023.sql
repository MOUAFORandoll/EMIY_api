-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 11 mai 2023 à 21:44
-- Version du serveur : 8.0.27
-- Version de PHP : 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dbaio`
--

-- --------------------------------------------------------

--
-- Structure de la table `boutique`
--

DROP TABLE IF EXISTS `boutique`;
CREATE TABLE IF NOT EXISTS `boutique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `localisation_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `code_boutique` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A1223C54A76ED395` (`user_id`),
  KEY `IDX_A1223C54C68BE09C` (`localisation_id`),
  KEY `IDX_A1223C5412469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `boutique`
--

INSERT INTO `boutique` (`id`, `user_id`, `localisation_id`, `category_id`, `titre`, `description`, `status`, `code_boutique`, `date_created`) VALUES
(1, 11, 1, 1, 'Presta Shop', 'as', 1, 'boutiqueopQ0O', '2022-12-18'),
(2, 2, 55, 5, 'King Shop', 'description*****', 1, 'boutiqueYam45', '2022-12-18'),
(3, 3, 2, 3, 'Nutrition Prime', 'description*****', 1, 'boutique3nIel', '2022-12-18'),
(4, 3, 5, 1, 'Mokolo Online', 'description*****', 0, 'boutiqueTZiLl', '2022-12-18'),
(5, 4, 2, 4, 'uno', 'false', 1, 'boutiqueiHNCR', '2023-02-13'),
(6, 1, 4, 1, 'tet', 'false', 1, 'boutiquebTSKD', '2023-02-13'),
(7, 23, 57, 7, 'J\'AFRIKA DESIGN ', 'chez j\'afrika disign, nous mettons à votre disposition des chaussures et vêtements représentant la culture Cameroun et africains.', 1, 'boutique4Sigt', '2023-03-12'),
(8, 21, NULL, 1, 'titre', '690863838', 0, 'boutiquenCAg7', '2023-05-03');

-- --------------------------------------------------------

--
-- Structure de la table `boutique_object`
--

DROP TABLE IF EXISTS `boutique_object`;
CREATE TABLE IF NOT EXISTS `boutique_object` (
  `id` int NOT NULL AUTO_INCREMENT,
  `boutique_id` int DEFAULT NULL,
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BB6A4419AB677BE6` (`boutique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `boutique_object`
--

INSERT INTO `boutique_object` (`id`, `boutique_id`, `src`, `date_created`) VALUES
(1, 1, 'bt67wKJ.jpg', '2023-03-11 20:33:33'),
(2, 3, 'btD9JB4.jpg', '2023-03-11 20:34:09'),
(3, 2, 'btg0Izt.jpg', '2023-03-11 20:35:43'),
(4, 4, 'btLBRwC.jpg', '2023-03-11 20:37:33'),
(5, 5, 'btP2l9M.jpg', '2023-03-11 20:37:48'),
(6, 6, 'btwbuTW.jpg', '2023-03-11 20:38:08'),
(7, 7, 'produitGTT5p.jpg', '2023-03-12 03:37:52'),
(8, 8, 'btGNRp8.png', '2023-05-03 18:45:55');

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `flutter_icon` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_64C19C1A4D60759` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `libelle`, `description`, `date_created`, `status`, `flutter_icon`) VALUES
(1, 'Electronique', 'description*****', '2022-12-18', 1, 58705),
(3, 'Alimentaire', 'description*****', '2022-12-18', 1, 57491),
(4, 'Electro-Menager', 'description*****', '2022-12-18', 1, 50545),
(5, 'Vetements', 'description*****', '2022-12-18', 1, 57895),
(6, 'Ustenciles', 'description*****', '2022-12-18', 1, 57852),
(7, 'Super-marche', 'Super-marche', '2023-02-20', 1, 45450);

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mode_paiement_id` int DEFAULT NULL,
  `panier_id` int DEFAULT NULL,
  `localisation_id` int DEFAULT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` date NOT NULL,
  `code_commande` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_buy` tinyint(1) NOT NULL,
  `status_finish` int NOT NULL,
  `token` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6EEAA67D438F5B63` (`mode_paiement_id`),
  KEY `IDX_6EEAA67DF77D927C` (`panier_id`),
  KEY `IDX_6EEAA67DC68BE09C` (`localisation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id`, `mode_paiement_id`, `panier_id`, `localisation_id`, `titre`, `description`, `date_created`, `code_commande`, `code_client`, `status_buy`, `status_finish`, `token`) VALUES
(1, 1, 1, 46, 'Achat de produit', 'Achat de produit', '2023-03-12', 'DfCE', 'mJgZ', 1, 0, 'comNxZHZ'),
(2, 1, 2, 47, 'Achat de produit', 'Achat de produit', '2023-03-12', 'jPN1', 'CxWf', 1, 0, 'comU3owz'),
(3, 1, 3, 48, 'Achat de produit', 'Achat de produit', '2023-03-12', 'BOvY', 'dFUk', 1, 0, 'com1O9xC');

-- --------------------------------------------------------

--
-- Structure de la table `commission`
--

DROP TABLE IF EXISTS `commission`;
CREATE TABLE IF NOT EXISTS `commission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pourcentage_produit` double DEFAULT NULL,
  `frais_livraison_produit` double NOT NULL,
  `frais_buy_livreur` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commission`
--

INSERT INTO `commission` (`id`, `pourcentage_produit`, `frais_livraison_produit`, `frais_buy_livreur`) VALUES
(1, 2, 250, 500);

-- --------------------------------------------------------

--
-- Structure de la table `compte`
--

DROP TABLE IF EXISTS `compte`;
CREATE TABLE IF NOT EXISTS `compte` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `solde` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CFF65260A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `compte`
--

INSERT INTO `compte` (`id`, `user_id`, `solde`) VALUES
(1, 1, 40000),
(2, 12, 0),
(3, 13, 0),
(4, 14, 0),
(5, 16, 0),
(6, 17, 0),
(7, 18, 0),
(8, 19, 0),
(9, 21, 0),
(10, 22, 0),
(11, 23, 0),
(12, 2, 0),
(13, 3, 0),
(14, 4, 0),
(15, 5, 0),
(16, 11, 0),
(17, 24, 0),
(18, 25, 0),
(19, 26, 0),
(20, 27, 0),
(21, 28, 0),
(22, 29, 0),
(23, 30, 0),
(24, 31, 0),
(25, 32, 0),
(26, 33, 0),
(27, 34, 0),
(28, 36, 0),
(29, 39, 0),
(30, 43, 0),
(31, 44, 0),
(32, 45, 0),
(33, 46, 0),
(34, 47, 0),
(35, 50, 0),
(36, 54, 0),
(37, 60, 0),
(38, 62, 0),
(39, 70, 0),
(40, 72, 0),
(41, 74, 0),
(42, 76, 0),
(43, 80, 0),
(44, 83, 0),
(45, 84, 0),
(46, 85, 0),
(47, 86, 0),
(48, 87, 0),
(49, 88, 0),
(50, 89, 0),
(51, 90, 0),
(52, 91, 0),
(53, 92, 0),
(54, 93, 0),
(55, 94, 0),
(56, 96, 0),
(57, 98, 0),
(58, 100, 0),
(59, 101, 0),
(60, 102, 0);

-- --------------------------------------------------------

--
-- Structure de la table `connexion`
--

DROP TABLE IF EXISTS `connexion`;
CREATE TABLE IF NOT EXISTS `connexion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `date_in` datetime NOT NULL,
  `date_out` datetime DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_936BF99CA76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20230207201800', '2023-03-26 20:51:07', 381),
('DoctrineMigrations\\Version20230310091413', '2023-03-10 09:14:19', 4214),
('DoctrineMigrations\\Version20230408203602', '2023-04-08 20:37:43', 142),
('DoctrineMigrations\\Version20230408204428', '2023-04-08 20:45:30', 48),
('DoctrineMigrations\\Version20230503180047', '2023-05-03 18:01:02', 4228),
('DoctrineMigrations\\Version20230503180634', '2023-05-03 18:06:39', 132),
('DoctrineMigrations\\Version20230507192345', '2023-05-07 19:25:30', 1335),
('DoctrineMigrations\\Version20230507211620', '2023-05-07 21:16:26', 571),
('DoctrineMigrations\\Version20230507214855', '2023-05-07 21:48:59', 493),
('DoctrineMigrations\\Version20230507215639', '2023-05-07 21:56:42', 195),
('DoctrineMigrations\\Version20230507224208', '2023-05-07 22:42:12', 561),
('DoctrineMigrations\\Version20230510202528', '2023-05-10 20:26:43', 171),
('DoctrineMigrations\\Version20230510203114', '2023-05-10 20:38:21', 179),
('DoctrineMigrations\\Version20230511202315', '2023-05-11 20:23:25', 690),
('DoctrineMigrations\\Version20230511210937', '2023-05-11 21:09:42', 146);

-- --------------------------------------------------------

--
-- Structure de la table `historique_paiement`
--

DROP TABLE IF EXISTS `historique_paiement`;
CREATE TABLE IF NOT EXISTS `historique_paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `type_paiement_id` int DEFAULT NULL,
  `commande_id` int DEFAULT NULL,
  `montant` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_710402ECA76ED395` (`user_id`),
  KEY `IDX_710402EC615593E9` (`type_paiement_id`),
  KEY `IDX_710402EC82EA2E54` (`commande_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `list_commande_livreur`
--

DROP TABLE IF EXISTS `list_commande_livreur`;
CREATE TABLE IF NOT EXISTS `list_commande_livreur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int DEFAULT NULL,
  `livreur_id` int DEFAULT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A629619382EA2E54` (`commande_id`),
  KEY `IDX_A6296193F8646701` (`livreur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `list_produit_panier`
--

DROP TABLE IF EXISTS `list_produit_panier`;
CREATE TABLE IF NOT EXISTS `list_produit_panier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `panier_id` int DEFAULT NULL,
  `produit_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `quantite` int NOT NULL,
  `code_produit_panier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AAC86140F77D927C` (`panier_id`),
  KEY `IDX_AAC86140F347EFB` (`produit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `list_produit_panier`
--

INSERT INTO `list_produit_panier` (`id`, `panier_id`, `produit_id`, `status`, `quantite`, `code_produit_panier`, `date_created`) VALUES
(1, 1, 7, 0, 3, 'Pqd6', '2023-03-12 01:56:23'),
(2, 1, 6, 0, 2, 'bnem', '2023-03-12 01:56:23'),
(3, 2, 7, 0, 3, 'mTyJ', '2023-03-12 01:56:41'),
(4, 2, 6, 0, 2, 'Hki2', '2023-03-12 01:56:41'),
(5, 3, 7, 0, 3, '834P', '2023-03-12 01:56:48'),
(6, 3, 6, 0, 2, '8iw4', '2023-03-12 01:56:48'),
(7, 4, 1, 0, 3, '09ah', '2023-03-27 18:49:27'),
(8, 5, 1, 0, 3, 'FKug', '2023-03-27 18:55:09'),
(9, 6, 1, 0, 3, 'YzTr', '2023-03-27 18:56:29'),
(10, 7, 1, 0, 3, '8z0N', '2023-03-27 19:02:50'),
(11, 8, 1, 0, 3, 'rKcx', '2023-03-27 19:05:41'),
(12, 9, 1, 0, 3, 'Xf5f', '2023-03-27 19:15:36');

-- --------------------------------------------------------

--
-- Structure de la table `list_produit_promotion`
--

DROP TABLE IF EXISTS `list_produit_promotion`;
CREATE TABLE IF NOT EXISTS `list_produit_promotion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `promotion_id` int DEFAULT NULL,
  `date_created` date NOT NULL,
  `prix_promotion` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C9E2B793F347EFB` (`produit_id`),
  KEY `IDX_C9E2B793139DF194` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `localisation`
--

DROP TABLE IF EXISTS `localisation`;
CREATE TABLE IF NOT EXISTS `localisation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `ville` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_in` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BFD3CE8FA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `localisation`
--

INSERT INTO `localisation` (`id`, `user_id`, `ville`, `longitude`, `latitude`, `ip`, `date_in`) VALUES
(1, 1, 'Douala', 9.7823, 4.09505, '154.72.150.165', '2023-01-18 22:08:49'),
(2, 1, 'Douala', 9.7823, 4.09505, '154.72.150.165', '2023-01-18 22:08:49'),
(3, 1, 'Douala', 9.7023, 4.99505, '154.72.150.165', '2023-01-18 22:09:12'),
(4, 1, 'Douala', 9.7023, 4.0505, '154.72.150.165', '2023-01-18 22:10:34'),
(5, NULL, 'Douala', 9.7123, 4.1505, '', '2023-01-18 22:14:04'),
(6, 1, 'Douala', 9.7023, 4.0505, '154.72.150.236', '2023-01-20 19:01:32'),
(7, 1, 'Douala', 9.7023, 4.0505, '154.72.150.172', '2023-01-20 19:49:45'),
(8, 1, 'Douala', 9.7023, 4.0505, '154.72.150.172', '2023-01-20 20:13:23'),
(9, 3, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-01-20 20:32:48'),
(10, 3, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-01-20 20:34:59'),
(11, 3, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-01-20 20:36:59'),
(12, 3, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-01-20 20:39:04'),
(13, 3, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-01-20 20:41:51'),
(14, 3, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-01-20 20:42:59'),
(15, 3, 'Douala', 9.7023, 4.0505, '154.72.150.133', '2023-01-20 22:19:02'),
(16, 3, 'Douala', 9.7023, 4.0505, '154.72.150.155', '2023-01-22 18:32:00'),
(17, 3, 'Douala', 9.7023, 4.0505, '154.72.150.201', '2023-01-22 20:40:47'),
(18, 3, 'Douala', 9.7023, 4.0505, '154.72.150.201', '2023-01-22 20:53:46'),
(19, 3, 'Douala', 9.7023, 4.0505, '154.72.150.201', '2023-01-22 21:36:05'),
(20, 3, 'Douala', 9.7023, 4.0505, '154.72.150.201', '2023-01-22 21:47:37'),
(21, 3, 'Douala', 9.7023, 4.0505, '154.72.150.201', '2023-01-22 22:29:03'),
(22, 3, 'Douala', 9.7023, 4.0505, '154.72.150.201', '2023-01-22 22:29:34'),
(23, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 21:44:00'),
(24, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 21:46:06'),
(25, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 21:47:18'),
(26, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 21:47:40'),
(27, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 21:47:41'),
(28, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 22:39:29'),
(29, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 22:39:31'),
(30, 1, 'Douala', 9.7023, 4.0505, '154.72.150.221', '2023-01-23 22:39:57'),
(31, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:05:49'),
(32, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:26:14'),
(33, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:28:46'),
(34, 2, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:52:10'),
(35, 3, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:56:32'),
(36, 21, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 21:03:08'),
(37, 21, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 21:05:48'),
(38, 22, 'Douala', 9.7023, 4.0505, '129.0.76.33', '2023-03-11 21:19:30'),
(39, 22, 'Douala', 9.7023, 4.0505, '129.0.76.33', '2023-03-11 21:20:04'),
(40, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 21:25:53'),
(41, 22, 'Douala', 9.7023, 4.0505, '129.0.76.33', '2023-03-11 21:28:15'),
(42, 22, 'Douala', 9.7023, 4.0505, '129.0.76.33', '2023-03-11 21:31:24'),
(43, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 21:33:14'),
(44, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 01:50:54'),
(45, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 01:54:29'),
(46, NULL, 'Douala', 9.7023, 4.0505, '', '2023-03-12 01:56:23'),
(47, NULL, 'Douala', 9.7023, 4.0505, '', '2023-03-12 01:56:41'),
(48, NULL, 'Douala', 9.7023, 4.0505, '', '2023-03-12 01:56:48'),
(49, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 01:58:36'),
(50, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 02:02:21'),
(51, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 02:30:56'),
(52, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 03:21:53'),
(53, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 03:26:41'),
(54, 2, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 03:30:05'),
(55, NULL, 'Douala', 9.7023, 4.0505, '', '2023-03-12 03:30:46'),
(56, 23, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 03:32:11'),
(57, NULL, 'Douala', 9.7023, 4.0505, '', '2023-03-12 03:37:52'),
(58, 23, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 03:43:57'),
(59, 3, 'Douala', 9.7023, 4.0505, '41.202.207.11', '2023-03-12 04:30:48'),
(60, 3, 'Douala', 9.7023, 4.0505, '41.202.207.11', '2023-03-12 04:55:57'),
(61, 23, 'Douala', 9.7023, 4.0505, '41.202.219.246', '2023-03-12 05:22:24'),
(62, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.250', '2023-03-12 06:37:43'),
(63, 3, 'Douala', 9.7023, 4.0505, '41.202.207.11', '2023-03-12 06:41:48'),
(64, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.250', '2023-03-12 10:12:13'),
(65, 1, 'Yaoundé', 11.5154, 3.8661, '154.72.167.174', '2023-03-12 11:38:55'),
(66, 23, 'Douala', 9.7023, 4.0505, '154.72.169.104', '2023-03-13 14:38:08');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mode_paiement`
--

DROP TABLE IF EXISTS `mode_paiement`;
CREATE TABLE IF NOT EXISTS `mode_paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mode_paiement`
--

INSERT INTO `mode_paiement` (`id`, `libelle`, `site_id`) VALUES
(1, 'Orange Money', '1000'),
(2, 'Momo', '1000'),
(3, 'Carte', '000\r\n');

-- --------------------------------------------------------

--
-- Structure de la table `notation_boutique`
--

DROP TABLE IF EXISTS `notation_boutique`;
CREATE TABLE IF NOT EXISTS `notation_boutique` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `boutique_id` int DEFAULT NULL,
  `note` double NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7B281E2419EB6921` (`client_id`),
  KEY `IDX_7B281E24AB677BE6` (`boutique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notation_boutique`
--

INSERT INTO `notation_boutique` (`id`, `client_id`, `boutique_id`, `note`, `date_created`) VALUES
(1, 2, 1, 2, '2023-05-08'),
(2, 1, 1, 1, '2023-05-08'),
(3, 1, 3, 4, '2023-05-08'),
(4, 2, 3, 2, '2023-05-08');

-- --------------------------------------------------------

--
-- Structure de la table `notation_produit`
--

DROP TABLE IF EXISTS `notation_produit`;
CREATE TABLE IF NOT EXISTS `notation_produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `produit_id` int DEFAULT NULL,
  `note` double NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_433F4C4C19EB6921` (`client_id`),
  KEY `IDX_433F4C4CF347EFB` (`produit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notation_produit`
--

INSERT INTO `notation_produit` (`id`, `client_id`, `produit_id`, `note`, `date_created`) VALUES
(1, 1, 1, 2, '2023-05-07'),
(2, 2, 1, 2, '2023-05-07'),
(3, 2, 7, 2, '2023-05-08');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `code_panier` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_24CC0DF2A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id`, `user_id`, `date_created`, `code_panier`, `nom_client`, `prenom_client`, `phone_client`) VALUES
(1, NULL, '2023-03-12', 'comNxZHZ', 'gerums ', 'hermal', '696557830'),
(2, NULL, '2023-03-12', 'comU3owz', 'gerums ', 'hermal', '696557830'),
(3, NULL, '2023-03-12', 'com1O9xC', 'gerums ', 'hermal', '696557830'),
(4, NULL, '2023-03-27', 'comiytWD', 'mhh', 'luj', '690863838'),
(5, NULL, '2023-03-27', 'comhVdXq', 'mhh', 'luj', '690863838'),
(6, NULL, '2023-03-27', 'comoC9Iz', 'mhh', 'luj', '690863838'),
(7, NULL, '2023-03-27', 'comu6vfw', 'mhh', 'luj', '690863838'),
(8, NULL, '2023-03-27', 'comjFtRr', 'mhh', 'luj', '690863838'),
(9, NULL, '2023-03-27', 'comEuqsx', 'mhh', 'luj', '690863838');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `boutique_id` int DEFAULT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` date NOT NULL,
  `prix_unitaire` int NOT NULL,
  `quantite` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `code_produit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29A5EC2712469DE2` (`category_id`),
  KEY `IDX_29A5EC27AB677BE6` (`boutique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `category_id`, `boutique_id`, `titre`, `description`, `date_created`, `prix_unitaire`, `quantite`, `status`, `code_produit`, `taille`) VALUES
(1, NULL, 6, 'iPhone X', 'iPhone x64go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 153250, 10, 1, 'produitEs7BY', 0),
(2, NULL, 1, 'iPhone 8+', 'iPhone 8 6', '2023-03-11', 104545, 10, 1, 'produitk3YEJ', 0),
(3, NULL, 6, 'iPhone 7+', 'iPhone 7 128go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 102250, 10, 1, 'produituZhXa', 0),
(4, NULL, 4, 'Google pixel ', 'iPhone 8 64go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 92050, 100, 1, 'produitIWMnx', 0),
(5, NULL, 6, 'techno', 'Techno 8 64go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 51250, 100, 1, 'produitMsqx8', 0),
(6, NULL, 4, 'chaussures ', 'escarpins new', '2023-03-11', 5350, 10, 1, 'produitfNfem', 0),
(7, NULL, 2, 'montre Rolex ', 'montre Rolex de qualité supérieure disponible pour tous ', '2023-03-12', 5350, 10, 1, 'produitqxMMS', 0),
(8, NULL, 2, 'pendule ', 'pendule de décoration très élégante pour tout type de maison ', '2023-03-12', 13510, 1000, 1, 'produitF0kOZ', 0),
(9, NULL, 2, 'montre', 'montre pour homme et femme très élégante ', '2023-03-12', 5707, 10, 1, 'produitp1XME', 0),
(10, NULL, 2, 'chaussures africaine ', 'chaussures de chez jafrika disign pour femme qui valorise la culture africaine. ', '2023-03-12', 8410, 10, 1, 'produitNw2ug', 0),
(11, NULL, 2, 'découpe manuelle', 'découpe manuelle de cuisine qui aide a découper tout les éléments de cuisine comme tomate, conditions et autre .. a l\'aide de sa manivelle, elle nous donne la possibilité de faire plusieurs découpe en réglant l\'aipesseur personne.. je vous le recommande ', '2023-03-12', 8308, 20, 1, 'produitN63s5', 0),
(12, NULL, 2, 'laptop ', '*HP EliteBook 840 G5 ultra slim et tres portable *- _Designé La Classe et la Performance 🤪-_* *Intel core i7 most recent professional business laptop* 🔥🔥* \n\n✳️ *Caractéristiques* 🏋🏿‍♂️ HP Elitebook 840 G5 Intel®️ core i7 8ieme Gen 💫💫 upto ~4.50Ghz \n\n✳️ *Disque (I)* SSD NVME 512Go plus\n\n\n ✳️*Ram 16GO extensible a 32/\n* \n✳️  *Carte graphique Intel UHD 620 5GB total  capable dexecuter Les taches Lourdes requisant jusquà 2 g Dédiés*🔥🔥\n\n✳️ USB 3.0  Type-C lecteur carte sim empreinte digitale / WI-FI/ Bluetooth, Webcam  / Écran 14\" POUCES FHD 🥳🥳/\n\n✳️  *Autonomie* , 💪🏾 Batteries \n\n✳️ Win 10 Pro ou 11pro  + Office déjà installé\n\n\n*🧯BARÊME ✅ 335 000 FRS CFA⭐⭐*\n\n#Vendue avec une facture+ Garantie et SAV assurée ✅🖥', '2023-03-12', 255250, 10, 1, 'produitwZ8XI', 0),
(13, NULL, 7, 'chaussures ', 'chaussures typiquement en perle ', '2023-03-12', 8920, 20, 1, 'produitXjgIF', 0),
(14, NULL, 3, 'Google pixel 3 a', 'photo de moi en fond décran ', '2023-03-12', 76750, 10, 1, 'produittXcjp', 0),
(15, NULL, 3, 'Google pixel 3 a', 'photo de moi en fond décran ', '2023-03-12', 76750, 10, 1, 'produitYmVVS', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_object`
--

DROP TABLE IF EXISTS `produit_object`;
CREATE TABLE IF NOT EXISTS `produit_object` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int DEFAULT NULL,
  `src` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5FFF60D6F347EFB` (`produit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit_object`
--

INSERT INTO `produit_object` (`id`, `produit_id`, `src`, `date_created`) VALUES
(1, 1, 'produitBfKT3.jpg', '2023-03-11'),
(2, 1, 'produit0llgU.jpg', '2023-03-11'),
(3, 1, 'produit39U9E.jpg', '2023-03-11'),
(4, 1, 'produit4Ooot.jpg', '2023-03-11'),
(5, 2, 'produit4vcKU.jpg', '2023-03-11'),
(6, 2, 'produit8KdRb.jpg', '2023-03-11'),
(7, 2, 'produitB9q4r.jpg', '2023-03-11'),
(8, 2, 'produitBIrbD.png', '2023-03-11'),
(9, 3, 'produitTafXl.jpg', '2023-03-11'),
(10, 3, 'produitFHmAD.png', '2023-03-11'),
(11, 3, 'produitFjJOR.png', '2023-03-11'),
(12, 3, 'produitGroZM.jpg', '2023-03-11'),
(13, 4, 'produitHrQl4.png', '2023-03-11'),
(14, 4, 'produitI6B1q.png', '2023-03-11'),
(15, 4, 'produitIGlHZ.jpg', '2023-03-11'),
(16, 4, 'produitJDlMI.jpg', '2023-03-11'),
(17, 5, 'produitlZgEP.jpg', '2023-03-11'),
(18, 5, 'produitKSujd.png', '2023-03-11'),
(19, 6, 'produitL2Aqh.jpg', '2023-03-11'),
(20, 7, 'produitP1oeo.jpg', '2023-03-12'),
(21, 7, 'produitPMBtS.jpg', '2023-03-12'),
(22, 8, 'produitPMEkb.jpg', '2023-03-12'),
(23, 8, 'produitQWv7O.jpg', '2023-03-12'),
(24, 9, 'produitRf4VQ.jpg', '2023-03-12'),
(25, 10, 'produitTbEIB.jpg', '2023-03-12'),
(26, 10, 'produitY8HUz.jpg', '2023-03-12'),
(27, 10, 'produitYooeu.jpg', '2023-03-12'),
(28, 11, 'produitaqG48.jpg', '2023-03-12'),
(29, 11, 'produitbN086.jpg', '2023-03-12'),
(30, 11, 'produitcmt4I.jpg', '2023-03-12'),
(31, 11, 'produitd5vz5.jpg', '2023-03-12'),
(32, 12, 'produiteUfbg.jpg', '2023-03-12'),
(33, 12, 'produitgZGWC.jpg', '2023-03-12'),
(34, 13, 'produitgoKvZ.png', '2023-03-12'),
(35, 13, 'produithKA1T.png', '2023-03-12'),
(36, 14, 'produitk3Z8Y.jpg', '2023-03-12'),
(37, 15, 'produitkzn5v.png', '2023-03-12');

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `short`
--

DROP TABLE IF EXISTS `short`;
CREATE TABLE IF NOT EXISTS `short` (
  `id` int NOT NULL AUTO_INCREMENT,
  `boutique_id` int DEFAULT NULL,
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F2890A2AB677BE6` (`boutique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `short`
--

INSERT INTO `short` (`id`, `boutique_id`, `src`, `titre`, `status`, `description`, `date_created`) VALUES
(5, 6, 'produitWxCZH.mp4', 'test', 1, 'edx', '2023-03-26');

-- --------------------------------------------------------

--
-- Structure de la table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `panier_id` int DEFAULT NULL,
  `mode_paiement_id` int DEFAULT NULL,
  `type_transaction_id` int DEFAULT NULL,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` int NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_create` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `nom_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_723705D119EB6921` (`client_id`),
  KEY `IDX_723705D1F77D927C` (`panier_id`),
  KEY `IDX_723705D1438F5B63` (`mode_paiement_id`),
  KEY `IDX_723705D17903E29B` (`type_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `type_paiement`
--

DROP TABLE IF EXISTS `type_paiement`;
CREATE TABLE IF NOT EXISTS `type_paiement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_paiement`
--

INSERT INTO `type_paiement` (`id`, `libelle`) VALUES
(1, 'livreur'),
(2, 'Boutique');

-- --------------------------------------------------------

--
-- Structure de la table `type_transaction`
--

DROP TABLE IF EXISTS `type_transaction`;
CREATE TABLE IF NOT EXISTS `type_transaction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_transaction`
--

INSERT INTO `type_transaction` (`id`, `libelle`) VALUES
(1, 'Achat'),
(2, 'Retrait');

-- --------------------------------------------------------

--
-- Structure de la table `type_user`
--

DROP TABLE IF EXISTS `type_user`;
CREATE TABLE IF NOT EXISTS `type_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type_user`
--

INSERT INTO `type_user` (`id`, `libelle`, `status`) VALUES
(1, 'admin', 1),
(2, 'client', 1),
(3, 'livreur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `userplateform`
--

DROP TABLE IF EXISTS `userplateform`;
CREATE TABLE IF NOT EXISTS `userplateform` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type_user_id` int DEFAULT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `phone` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` date NOT NULL,
  `key_secret` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `code_parrain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_recup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F140D851444F97DD` (`phone`),
  KEY `IDX_F140D8518F4FBC60` (`type_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `userplateform`
--

INSERT INTO `userplateform` (`id`, `type_user_id`, `nom`, `prenom`, `email`, `roles`, `phone`, `status`, `password`, `date_created`, `key_secret`, `code_parrain`, `code_recup`) VALUES
(1, 1, 'admin', 'mouafo', 'h@4.com', '[\"ROLE_USER\"]', 690863838, 1, '$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G', '2022-12-16', '1234', NULL, NULL),
(2, 3, 'Toche', 'Hermann ', 'h@h.com', '[\"ROLE_USER\"]', 650863838, 1, '$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G', '2022-12-16', '12341', NULL, NULL),
(3, 3, 'Estou', 'shouki', 'h@h.com', '[\"ROLE_USER\"]', 693087868, 1, '$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G', '2022-12-16', '12349', NULL, NULL),
(12, 2, 'Mouafo ', 'Ran ', '', '[\"ROLE_USER\"]', 612345678, 1, '$2y$13$gYF1PoU/aagL6W8zUtDCWeDhLKAYSit9GZImAkhsJr8mBrDcRXyam', '2023-01-24', '$2y$10$a0JWYP.yzOThE.Ylez56MuTQOIzTccfHo7HpKNZL14HiNUiIeTm8a', NULL, NULL),
(13, 2, 'romi', 'ari', '', '[\"ROLE_USER\"]', 655555555, 1, '$2y$13$nr9cVGdP59Smy7VlaVHLiONoyy0x5fozz/csd0Qy2rNCwK7cjclfu', '2023-01-24', '$2y$10$9l17jYzqnWSWgopVTkdx9uN2eWYNOpOlKvVS38JBVUK9W3dHevcv.', NULL, NULL),
(14, 2, 'hgg', 'hh', '', '[\"ROLE_USER\"]', 5455, 1, '$2y$13$NM9mOsqcZ7mzJTuRX6JmfujHw1GoNLJNBmpVuBz/mP7HTheseCOsK', '2023-01-24', '$2y$10$nkf5cGMWduqLI2FvigyHZ.97No8i3qzDuFRzisAZoRIRHA6By09oK', NULL, NULL),
(16, 2, 'hgg', 'hh', '', '[\"ROLE_USER\"]', 54554, 1, '$2y$13$Zjci5ync10SGHECIZ6Bhruyg.KUefBfW78OdMlqo5RQfP0LFKWKIe', '2023-01-24', '$2y$10$TADG5sIp/Z1wqBJGUX1rw.3FDUO3JXz5FJasDNKOZsB/Gl7sbWCga', NULL, NULL),
(17, 2, 'rr', 'uy', '', '[\"ROLE_USER\"]', 424, 1, '$2y$13$y5CIhrLmXAYLXJ0I6BdfoesO8O1MhEjET9j/2IweInBlbvbyH6pIu', '2023-01-24', '$2y$10$csx9mI0pyvhF6Q.4sS7YFesexF/oYGQ8QZpUpbm4qQstLs4fhR0IW', NULL, NULL),
(19, 2, 'yrt', 'ggg', '', '[\"ROLE_USER\"]', 56576766, 1, '$2y$13$GccQCC.V78PXDcj6M8DyL.08545fPu25i9InQs.uqkI4NPSo/R2l.', '2023-03-08', '$2y$10$5/ZXFNb.19o7LIyaHQtgPe5Mc2WJdTh3E3hjmJgObgl6MofOPmhxW', NULL, NULL),
(34, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 6565655, 1, '$2y$13$DZU5sssGyNsm0hG6/3Z12.itpzo1XF6xjMjA6kN85UEj7DNAgofNa', '2023-05-10', '$2y$10$XG0uWdhizLKlTBPPva.lbuLg4HxRf3BamEx7KPsCZ3yI7rf3mdFHG', NULL, NULL),
(36, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 65645, 1, '$2y$13$48hvKTstHFLoBJjFwrM1DO0u0KrAo0j91bCNOHZ9MNcUE8FQqPCTi', '2023-05-10', '$2y$10$MwO/f3ArizO4FSVk0mpEaOI7c5L9xUWJ8tkYhDLfmnhbBk8sXGp4y', NULL, NULL),
(39, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 2147483647, 1, '$2y$13$YxkWsa1ZNTcYFnln7Kzs3.c0nDidsvNsiyXBO44EbVGq.W5Di3ivy', '2023-05-10', '$2y$10$M6Ps.mF6hBZaySM5OiO70.zb2Q74VYDSgQJ4ar8FEGJmI/mIMAab2', NULL, NULL),
(60, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 70006655, 1, '$2y$13$KZMx6lMCvRUEgW7rOvR7vuT59ZfdGsa.uP4z4XUhhj1UmS7j2vR4G', '2023-05-10', '$2y$10$.QOqYS89eWFC7itDIkCJCewLmZwQRaeCvw45YNbzwK8.mBKZVo8uG', '39@60', NULL),
(62, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 464564654, 1, '$2y$13$5OB3ApRT9RuCS/pL0vgszuqT3Iuely0VBNW9wvAc67gvRSUI2./Fy', '2023-05-10', '$2y$10$r0Bj3HgaxGIXfYpGXepV8.tG8LMv9XclBk46iB.glLjWpUgG4hxhy', '39@62', NULL),
(70, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 40545, 1, '$2y$13$U2WieckzEhwN69lFF9nPl.cduTaA/lfQmpOrCHEmBZA/NgjhFR4Na', '2023-05-11', '$2y$10$ybjxAyCd.bpJpXKhYHIrueYNF.VwmJXPDcq5lic0PaxfRdQFNsQZO', '39', NULL),
(72, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 445450545, 1, '$2y$13$bvk8ZDfDkvMVIVTPsEyJGOHix1prk2lvlDYGOcfBE.lKtXebSpwvS', '2023-05-11', '$2y$10$IOeUkzhAdK9BPDzBYYZdvuXI8B6J6.YU2XcFFL8UWSWoqUrYpSYwG', '39', NULL),
(74, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 6959555, 1, '$2y$13$amB1NyjnAltL4Y8umN7LuOoxcQ8uHC1SDa37m8xgg89TUfN3e48mi', '2023-05-11', '$2y$10$T4FX.FulYWu1ZelT7ZyOE.tNz9p1VU/CurNirFDlfOD3VMjnaXALu', '39', NULL),
(76, NULL, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 694559555, 1, '$2y$13$Ker0kIfhCVoIe7GzZBiXh.6/ph7kKxHr.FYTV413NQmgzXFpYApEm', '2023-05-11', '$2y$10$BlyleHTtUUf9hSAL0fE1uePgfmh44K/HumTJ8iDgRlzjNg9AV5Uii', '39', NULL),
(80, NULL, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 698586868, 1, '$2y$13$5HSvAA62/VIpxJV69BwDlOTaVuII1c1H.2B09/MqGRtRHlX8KXQdy', '2023-05-11', '$2y$10$Z7wy8bOxTl83xX/mTebNguOaVygBO1KH9dkKQvmU0rn6u4OOOXCM2', '39', NULL),
(83, NULL, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 689886868, 1, '$2y$13$Otzfi20UUTitUuQesE6rruR1F6VWJ/Vj0vA1sxqt2vrhOkLBVN/0y', '2023-05-11', '$2y$10$1ddprJjuHW8hPBBP8RLc3eEu11e2c3v6iZoc24cF4JdPcm9j.GBUW', '125', NULL),
(84, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 689886869, 1, '$2y$13$s6mfHtm/MJqggK.uJwu1leqOVEzPVj5y79LAazOT7QXefqnzgpiO.', '2023-05-11', '$2y$10$kdN4ztlYgq.4kAIH9j8zReGyRtT1yQwkaXH4jhjpZZxicnSbiRUEW', '125', NULL),
(85, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 689886069, 1, '$2y$13$yJKH9iZ28aRY.VwelRGXV.byrfGpo2fmZvZpAPDpBMw58kXQ43V6G', '2023-05-11', '$2y$10$PN1lQLl4ofNbRYpon08Z2e3SpHxhzMKR.L5W3/h4.2Wf19fIBK57e', '125', NULL),
(86, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 89886069, 1, '$2y$13$mK9lfiAwAizbXrWuTsjsLOtUEDPRDtgMEoseKOXPXMormaqsdbsK.', '2023-05-11', '$2y$10$.FWIMSxm3Pp5E2CfQ6ONVOZI/Ewt0tsTxofptCLlK.7xU.afFNlWe', '125', NULL),
(87, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 898806069, 1, '$2y$13$K.rLJ0ZJlH13kV/m3ZlK1OwI51gTROLUfRf0pCyEI44AyaAwSAUU.', '2023-05-11', '$2y$10$iW1HopjWotqI9ylOfr90U.5u0IaZ.vdli11tujbrRJIHxViGtm3VW', '125', NULL),
(88, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 848806069, 1, '$2y$13$7sB0jBidbLEdzpdof1ZiSOgOq8tK9aXoj/nICUAAGlJ9RAztviya2', '2023-05-11', '$2y$10$OL171wGV6Ecq3IBOzKMjfu9eRKd5pLgAd6yy8.FCULpI3.PACgidC', '125', NULL),
(89, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 848006069, 1, '$2y$13$jO.LsIQLBX.d3Csf9./Tc.0RxbiuLV0YiZhFkrGA074wDgS4xGJiS', '2023-05-11', '$2y$10$soNDLQJx.l005KzF/28TgenhbRVNbBM9GAMdltmUPhIbvrlIHrKUS', '125', NULL),
(90, 2, 'string', 'string', 'string', '[\"ROLE_USER\"]', 0, 1, '$2y$13$WJaB20MQ3cBFja7jJoJ63.iT3wcnqF8UGxeNGCOuLzM1yE4MEvPl2', '2023-05-11', '$2y$10$Krme7yFgCe0/rvXWLB2uZ.jRJ6uhvv2W8.YacxZASl4n7xWZ3XpE6', 'string', NULL),
(91, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 8406069, 1, '$2y$13$PHWNko2yGXfgDAR139M3Aub9UjKqyx/70Eb7IP9/iLy7pbR9Vsqea', '2023-05-11', '$2y$10$H8ah99wwi3Qs6POL9hNxS.gWSOaymhEnAbLh3r5.vYM8Wh4xl28QK', '125', NULL),
(92, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 84060969, 1, '$2y$13$s1VVhj51iydJC6n3qjf4j.HM2BBTCXkTZs8pSlHduWB362i3JDAOi', '2023-05-11', '$2y$10$qnMVgMvFZRAhAFfMp0VLjOWNl55OdprSd5ECKE7M03wpayBCY6QHK', '125', NULL),
(93, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 6989789, 1, '$2y$13$/d0uBZnNJJBr8iYDG7rvsegzTDJrzmqdY65.tVqC78zsV6FTo0b/i', '2023-05-11', '$2y$10$zbggye2TWvEsYz7xamMSruAWbwsOGMc3SZgDLunfO0/jknDuEhprO', '1', NULL),
(94, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 69849789, 1, '$2y$13$BuVdeA63g6yVu8w5P2POjuEcOg6waCg7EZ06Fie0VUz1ORmVKb0gW', '2023-05-11', '$2y$10$E1DTeYAF51ZIndhY5HAv1eSFhIyDl1.6YGkMCmxxSLTSzHlA1gkba', '1', NULL),
(96, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 698479789, 1, '$2y$13$Ipk7oL8gU39q7b4dFQo2tOxaq7cluIOnjWwONrh7TCa3elRYy9aLa', '2023-05-11', '$2y$10$jYYpJwZDFxF3lnsjJFOEqONYwLMkKNiOcwwROqxIiWUUMy5SX60ny', '1', NULL),
(98, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 6984789, 1, '$2y$13$9DI3DLlUQSHNzr7qEVOUO.zXmvc4lruZ3UStdu90TsRomtaon3ib2', '2023-05-11', '$2y$10$Rlri5LBRb3hq0uIDi9OSjuZmUwIu/Sh7bplYR0sZLjAG7dR5PshRG', '1', NULL),
(100, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 69847809, 1, '$2y$13$vbk8mXLlPsfLIQcYOciSq.JaGteDEcU2wqEjMzHI4LXt9akHAVJ1i', '2023-05-11', '$2y$10$f7AhXkN8csYmX41awrYc/uU9qNtGos.5Keje7Ek8u8Zr5YDVXn2ma', '1', NULL),
(101, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 690847809, 1, '$2y$13$G2ebVWO313sKFrdznqTfpOc2nJOwzqlrMCliQHtEb0fJpj7gaDAXi', '2023-05-11', '$2y$10$WfC.EPFYXLqkVHWGRNSJAO4nxAXJ3qAK1DDPRSvekX5.Jq5hn0Qci', '1', NULL),
(102, 2, 'name.text', 'surname.text', 'email.text', '[\"ROLE_USER\"]', 6847809, 1, '$2y$13$k/kdXvXKPU96DbVfKVXdUeTaX26jcK0b4mUWzn3BEmxKbp0iPHEPe', '2023-05-11', '$2y$10$M5mtzfM0E7LeOp07zjfKKOnhEwgrhjoVOMZIjOIyfajTqQVRT.MfG', '1', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `boutique`
--
ALTER TABLE `boutique`
  ADD CONSTRAINT `FK_A1223C5412469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_A1223C54A76ED395` FOREIGN KEY (`user_id`) REFERENCES `userplateform` (`id`),
  ADD CONSTRAINT `FK_A1223C54C68BE09C` FOREIGN KEY (`localisation_id`) REFERENCES `localisation` (`id`);

--
-- Contraintes pour la table `boutique_object`
--
ALTER TABLE `boutique_object`
  ADD CONSTRAINT `FK_BB6A4419AB677BE6` FOREIGN KEY (`boutique_id`) REFERENCES `boutique` (`id`);

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `FK_6EEAA67D438F5B63` FOREIGN KEY (`mode_paiement_id`) REFERENCES `mode_paiement` (`id`),
  ADD CONSTRAINT `FK_6EEAA67DC68BE09C` FOREIGN KEY (`localisation_id`) REFERENCES `localisation` (`id`),
  ADD CONSTRAINT `FK_6EEAA67DF77D927C` FOREIGN KEY (`panier_id`) REFERENCES `panier` (`id`);

--
-- Contraintes pour la table `compte`
--
ALTER TABLE `compte`
  ADD CONSTRAINT `FK_CFF65260A76ED395` FOREIGN KEY (`user_id`) REFERENCES `userplateform` (`id`);

--
-- Contraintes pour la table `connexion`
--
ALTER TABLE `connexion`
  ADD CONSTRAINT `FK_936BF99CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `userplateform` (`id`);

--
-- Contraintes pour la table `historique_paiement`
--
ALTER TABLE `historique_paiement`
  ADD CONSTRAINT `FK_710402EC615593E9` FOREIGN KEY (`type_paiement_id`) REFERENCES `type_paiement` (`id`),
  ADD CONSTRAINT `FK_710402EC82EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`),
  ADD CONSTRAINT `FK_710402ECA76ED395` FOREIGN KEY (`user_id`) REFERENCES `userplateform` (`id`);

--
-- Contraintes pour la table `list_commande_livreur`
--
ALTER TABLE `list_commande_livreur`
  ADD CONSTRAINT `FK_A629619382EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`),
  ADD CONSTRAINT `FK_A6296193F8646701` FOREIGN KEY (`livreur_id`) REFERENCES `userplateform` (`id`);

--
-- Contraintes pour la table `list_produit_panier`
--
ALTER TABLE `list_produit_panier`
  ADD CONSTRAINT `FK_AAC86140F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`),
  ADD CONSTRAINT `FK_AAC86140F77D927C` FOREIGN KEY (`panier_id`) REFERENCES `panier` (`id`);

--
-- Contraintes pour la table `list_produit_promotion`
--
ALTER TABLE `list_produit_promotion`
  ADD CONSTRAINT `FK_C9E2B793139DF194` FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`id`),
  ADD CONSTRAINT `FK_C9E2B793F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `localisation`
--
ALTER TABLE `localisation`
  ADD CONSTRAINT `FK_BFD3CE8FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `userplateform` (`id`);

--
-- Contraintes pour la table `notation_boutique`
--
ALTER TABLE `notation_boutique`
  ADD CONSTRAINT `FK_7B281E2419EB6921` FOREIGN KEY (`client_id`) REFERENCES `userplateform` (`id`),
  ADD CONSTRAINT `FK_7B281E24AB677BE6` FOREIGN KEY (`boutique_id`) REFERENCES `boutique` (`id`);

--
-- Contraintes pour la table `notation_produit`
--
ALTER TABLE `notation_produit`
  ADD CONSTRAINT `FK_433F4C4C19EB6921` FOREIGN KEY (`client_id`) REFERENCES `userplateform` (`id`),
  ADD CONSTRAINT `FK_433F4C4CF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `FK_24CC0DF2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `userplateform` (`id`);

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `FK_29A5EC2712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_29A5EC27AB677BE6` FOREIGN KEY (`boutique_id`) REFERENCES `boutique` (`id`);

--
-- Contraintes pour la table `produit_object`
--
ALTER TABLE `produit_object`
  ADD CONSTRAINT `FK_5FFF60D6F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`);

--
-- Contraintes pour la table `short`
--
ALTER TABLE `short`
  ADD CONSTRAINT `FK_8F2890A2AB677BE6` FOREIGN KEY (`boutique_id`) REFERENCES `boutique` (`id`);

--
-- Contraintes pour la table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `FK_723705D119EB6921` FOREIGN KEY (`client_id`) REFERENCES `userplateform` (`id`),
  ADD CONSTRAINT `FK_723705D1438F5B63` FOREIGN KEY (`mode_paiement_id`) REFERENCES `mode_paiement` (`id`),
  ADD CONSTRAINT `FK_723705D17903E29B` FOREIGN KEY (`type_transaction_id`) REFERENCES `type_transaction` (`id`),
  ADD CONSTRAINT `FK_723705D1F77D927C` FOREIGN KEY (`panier_id`) REFERENCES `panier` (`id`);

--
-- Contraintes pour la table `userplateform`
--
ALTER TABLE `userplateform`
  ADD CONSTRAINT `FK_F140D8518F4FBC60` FOREIGN KEY (`type_user_id`) REFERENCES `type_user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
