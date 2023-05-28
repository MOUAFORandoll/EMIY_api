-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 24 mai 2023 à 21:18
-- Version du serveur : 8.0.27
-- Version de PHP : 8.0.13

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
-- SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : dbaio
--

-- --------------------------------------------------------

--
-- Structure de la table boutique
--
 
INSERT INTO type_paiement (id, libelle) VALUES
(1, 'livreur'),
(2, 'Boutique');

 

INSERT INTO type_transaction (id, libelle) VALUES
(1, 'Achat'),
(2, 'Retrait'),
(3, 'Depot');
 

INSERT INTO type_user (id, libelle, status) VALUES
(1, 'admin', true),
(2, 'client', true),
(3, 'livreur', true);
INSERT INTO category (id, libelle, description, date_created, status, flutter_icon) VALUES
(1, 'Electronique', 'description*****', '2022-12-18', true, 58705),
(3, 'Alimentaire', 'description*****', '2022-12-18', true, 57491),
(4, 'Electro-Menager', 'description*****', '2022-12-18', true, 50545),
(5, 'Vetements', 'description*****', '2022-12-18', true, 57895),
(6, 'Ustenciles', 'description*****', '2022-12-18', true, 57852),
(7, 'Super-marche', 'Super-marche', '2023-02-20', true, 45450);

--
-- Structure de la table list_produit_promotion
-- 
--
-- Déchargement des données de la table localisation
--


-- --------------------------------------------------------

--
-- Structure de la table messenger_messages
--
 
--
-- Déchargement des données de la table mode_paiement
--

INSERT INTO mode_paiement (id, libelle, site_id) VALUES
(1, 'Orange Money', '1000'),
(2, 'Momo', '1000'),
(3, 'Carte', '000\r\n'),
(4, 'free coin', '1000');
INSERT INTO user_plateform (id, type_user_id, nom, prenom, email, roles, phone, status, password, date_created, key_secret, code_parrain, code_recup) VALUES
(1, 1, 'admin', 'mouafo', 'h@4.com', '["ROLE_USER"]', 690863838, true,'$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G', '2022-12-16', '1234', NULL, NULL),
(2, 3, 'Toche', 'Hermann ', 'h@h.com', '["ROLE_USER"]', 650863838, true,'$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G', '2022-12-16', '12341', NULL, NULL),
(3, 3, 'Estou', 'shouki', 'h@h.com', '["ROLE_USER"]', 693087868, true,'$2y$13$EPYqK4p5UszFfrj3IHEvAu3AYV4JQZZgVkQZD9G4//rDiwIBYxG8G', '2022-12-16', '12349', NULL, NULL),
(12, 2, 'Mouafo ', 'Ran ', '', '["ROLE_USER"]', 612345678, true,'$2y$13$gYF1PoU/aagL6W8zUtDCWeDhLKAYSit9GZImAkhsJr8mBrDcRXyam', '2023-01-24', '$2y$10$a0JWYP.yzOThE.Ylez56MuTQOIzTccfHo7HpKNZL14HiNUiIeTm8a', NULL, NULL),
(13, 2, 'romi', 'ari', '', '["ROLE_USER"]', 655555555, true,'$2y$13$nr9cVGdP59Smy7VlaVHLiONoyy0x5fozz/csd0Qy2rNCwK7cjclfu', '2023-01-24', '$2y$10$9l17jYzqnWSWgopVTkdx9uN2eWYNOpOlKvVS38JBVUK9W3dHevcv.', NULL, NULL),
(14, 2, 'hgg', 'hh', '', '["ROLE_USER"]', 5455, true,'$2y$13$NM9mOsqcZ7mzJTuRX6JmfujHw1GoNLJNBmpVuBz/mP7HTheseCOsK', '2023-01-24', '$2y$10$nkf5cGMWduqLI2FvigyHZ.97No8i3qzDuFRzisAZoRIRHA6By09oK', NULL, NULL),
(16, 2, 'hgg', 'hh', '', '["ROLE_USER"]', 54554, true,'$2y$13$Zjci5ync10SGHECIZ6Bhruyg.KUefBfW78OdMlqo5RQfP0LFKWKIe', '2023-01-24', '$2y$10$TADG5sIp/Z1wqBJGUX1rw.3FDUO3JXz5FJasDNKOZsB/Gl7sbWCga', NULL, NULL),
(17, 2, 'rr', 'uy', '', '["ROLE_USER"]', 424, true,'$2y$13$y5CIhrLmXAYLXJ0I6BdfoesO8O1MhEjET9j/2IweInBlbvbyH6pIu', '2023-01-24', '$2y$10$csx9mI0pyvhF6Q.4sS7YFesexF/oYGQ8QZpUpbm4qQstLs4fhR0IW', NULL, NULL),
(19, 2, 'yrt', 'ggg', '', '["ROLE_USER"]', 56576766, true,'$2y$13$GccQCC.V78PXDcj6M8DyL.08545fPu25i9InQs.uqkI4NPSo/R2l.', '2023-03-08', '$2y$10$5/ZXFNb.19o7LIyaHQtgPe5Mc2WJdTh3E3hjmJgObgl6MofOPmhxW', NULL, NULL),
(34, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 6565655, true,'$2y$13$DZU5sssGyNsm0hG6/3Z12.itpzo1XF6xjMjA6kN85UEj7DNAgofNa', '2023-05-10', '$2y$10$XG0uWdhizLKlTBPPva.lbuLg4HxRf3BamEx7KPsCZ3yI7rf3mdFHG', NULL, NULL),
(36, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 65645, true,'$2y$13$48hvKTstHFLoBJjFwrM1DO0u0KrAo0j91bCNOHZ9MNcUE8FQqPCTi', '2023-05-10', '$2y$10$MwO/f3ArizO4FSVk0mpEaOI7c5L9xUWJ8tkYhDLfmnhbBk8sXGp4y', NULL, NULL),
(39, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 2147483647, true,'$2y$13$YxkWsa1ZNTcYFnln7Kzs3.c0nDidsvNsiyXBO44EbVGq.W5Di3ivy', '2023-05-10', '$2y$10$M6Ps.mF6hBZaySM5OiO70.zb2Q74VYDSgQJ4ar8FEGJmI/mIMAab2', NULL, NULL),
(60, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 70006655, true,'$2y$13$KZMx6lMCvRUEgW7rOvR7vuT59ZfdGsa.uP4z4XUhhj1UmS7j2vR4G', '2023-05-10', '$2y$10$.QOqYS89eWFC7itDIkCJCewLmZwQRaeCvw45YNbzwK8.mBKZVo8uG', '39@60', NULL),
(62, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 464564654, true,'$2y$13$5OB3ApRT9RuCS/pL0vgszuqT3Iuely0VBNW9wvAc67gvRSUI2./Fy', '2023-05-10', '$2y$10$r0Bj3HgaxGIXfYpGXepV8.tG8LMv9XclBk46iB.glLjWpUgG4hxhy', '39@62', NULL),
(70, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 40545, true,'$2y$13$U2WieckzEhwN69lFF9nPl.cduTaA/lfQmpOrCHEmBZA/NgjhFR4Na', '2023-05-11', '$2y$10$ybjxAyCd.bpJpXKhYHIrueYNF.VwmJXPDcq5lic0PaxfRdQFNsQZO', '39', NULL),
(72, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 445450545, true,'$2y$13$bvk8ZDfDkvMVIVTPsEyJGOHix1prk2lvlDYGOcfBE.lKtXebSpwvS', '2023-05-11', '$2y$10$IOeUkzhAdK9BPDzBYYZdvuXI8B6J6.YU2XcFFL8UWSWoqUrYpSYwG', '39', NULL),
(74, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 6959555, true,'$2y$13$amB1NyjnAltL4Y8umN7LuOoxcQ8uHC1SDa37m8xgg89TUfN3e48mi', '2023-05-11', '$2y$10$T4FX.FulYWu1ZelT7ZyOE.tNz9p1VU/CurNirFDlfOD3VMjnaXALu', '39', NULL),
(76, NULL, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 694559555, true,'$2y$13$Ker0kIfhCVoIe7GzZBiXh.6/ph7kKxHr.FYTV413NQmgzXFpYApEm', '2023-05-11', '$2y$10$BlyleHTtUUf9hSAL0fE1uePgfmh44K/HumTJ8iDgRlzjNg9AV5Uii', '39', NULL),
(80, NULL, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 698586868, true,'$2y$13$5HSvAA62/VIpxJV69BwDlOTaVuII1c1H.2B09/MqGRtRHlX8KXQdy', '2023-05-11', '$2y$10$Z7wy8bOxTl83xX/mTebNguOaVygBO1KH9dkKQvmU0rn6u4OOOXCM2', '39', NULL),
(83, NULL, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 689886868, true,'$2y$13$Otzfi20UUTitUuQesE6rruR1F6VWJ/Vj0vA1sxqt2vrhOkLBVN/0y', '2023-05-11', '$2y$10$1ddprJjuHW8hPBBP8RLc3eEu11e2c3v6iZoc24cF4JdPcm9j.GBUW', '125', NULL),
(84, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 689886869, true,'$2y$13$s6mfHtm/MJqggK.uJwu1leqOVEzPVj5y79LAazOT7QXefqnzgpiO.', '2023-05-11', '$2y$10$kdN4ztlYgq.4kAIH9j8zReGyRtT1yQwkaXH4jhjpZZxicnSbiRUEW', '125', NULL),
(85, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 689886069, true,'$2y$13$yJKH9iZ28aRY.VwelRGXV.byrfGpo2fmZvZpAPDpBMw58kXQ43V6G', '2023-05-11', '$2y$10$PN1lQLl4ofNbRYpon08Z2e3SpHxhzMKR.L5W3/h4.2Wf19fIBK57e', '125', NULL),
(86, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 89886069, true,'$2y$13$mK9lfiAwAizbXrWuTsjsLOtUEDPRDtgMEoseKOXPXMormaqsdbsK.', '2023-05-11', '$2y$10$.FWIMSxm3Pp5E2CfQ6ONVOZI/Ewt0tsTxofptCLlK.7xU.afFNlWe', '125', NULL),
(87, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 898806069, true,'$2y$13$K.rLJ0ZJlH13kV/m3ZlK1OwI51gTROLUfRf0pCyEI44AyaAwSAUU.', '2023-05-11', '$2y$10$iW1HopjWotqI9ylOfr90U.5u0IaZ.vdli11tujbrRJIHxViGtm3VW', '125', NULL),
(88, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 848806069, true,'$2y$13$7sB0jBidbLEdzpdof1ZiSOgOq8tK9aXoj/nICUAAGlJ9RAztviya2', '2023-05-11', '$2y$10$OL171wGV6Ecq3IBOzKMjfu9eRKd5pLgAd6yy8.FCULpI3.PACgidC', '125', NULL),
(89, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 848006069, true,'$2y$13$jO.LsIQLBX.d3Csf9./Tc.0RxbiuLV0YiZhFkrGA074wDgS4xGJiS', '2023-05-11', '$2y$10$soNDLQJx.l005KzF/28TgenhbRVNbBM9GAMdltmUPhIbvrlIHrKUS', '125', NULL),
(90, 2, 'string', 'string', 'string', '["ROLE_USER"]', 0, true,'$2y$13$WJaB20MQ3cBFja7jJoJ63.iT3wcnqF8UGxeNGCOuLzM1yE4MEvPl2', '2023-05-11', '$2y$10$Krme7yFgCe0/rvXWLB2uZ.jRJ6uhvv2W8.YacxZASl4n7xWZ3XpE6', 'string', NULL),
(91, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 8406069, true,'$2y$13$PHWNko2yGXfgDAR139M3Aub9UjKqyx/70Eb7IP9/iLy7pbR9Vsqea', '2023-05-11', '$2y$10$H8ah99wwi3Qs6POL9hNxS.gWSOaymhEnAbLh3r5.vYM8Wh4xl28QK', '125', NULL),
(92, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 84060969, true,'$2y$13$s1VVhj51iydJC6n3qjf4j.HM2BBTCXkTZs8pSlHduWB362i3JDAOi', '2023-05-11', '$2y$10$qnMVgMvFZRAhAFfMp0VLjOWNl55OdprSd5ECKE7M03wpayBCY6QHK', '125', NULL),
(93, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 6989789, true,'$2y$13$/d0uBZnNJJBr8iYDG7rvsegzTDJrzmqdY65.tVqC78zsV6FTo0b/i', '2023-05-11', '$2y$10$zbggye2TWvEsYz7xamMSruAWbwsOGMc3SZgDLunfO0/jknDuEhprO', '1', NULL),
(94, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 69849789, true,'$2y$13$BuVdeA63g6yVu8w5P2POjuEcOg6waCg7EZ06Fie0VUz1ORmVKb0gW', '2023-05-11', '$2y$10$E1DTeYAF51ZIndhY5HAv1eSFhIyDl1.6YGkMCmxxSLTSzHlA1gkba', '1', NULL),
(96, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 698479789, true,'$2y$13$Ipk7oL8gU39q7b4dFQo2tOxaq7cluIOnjWwONrh7TCa3elRYy9aLa', '2023-05-11', '$2y$10$jYYpJwZDFxF3lnsjJFOEqONYwLMkKNiOcwwROqxIiWUUMy5SX60ny', '1', NULL),
(98, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 6984789, true,'$2y$13$9DI3DLlUQSHNzr7qEVOUO.zXmvc4lruZ3UStdu90TsRomtaon3ib2', '2023-05-11', '$2y$10$Rlri5LBRb3hq0uIDi9OSjuZmUwIu/Sh7bplYR0sZLjAG7dR5PshRG', '1', NULL),
(100, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 69847809, true,'$2y$13$vbk8mXLlPsfLIQcYOciSq.JaGteDEcU2wqEjMzHI4LXt9akHAVJ1i', '2023-05-11', '$2y$10$f7AhXkN8csYmX41awrYc/uU9qNtGos.5Keje7Ek8u8Zr5YDVXn2ma', '1', NULL),
(101, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 690847809, true,'$2y$13$G2ebVWO313sKFrdznqTfpOc2nJOwzqlrMCliQHtEb0fJpj7gaDAXi', '2023-05-11', '$2y$10$WfC.EPFYXLqkVHWGRNSJAO4nxAXJ3qAK1DDPRSvekX5.Jq5hn0Qci', '1', NULL),
(102, 2, 'name.text', 'surname.text', 'email.text', '["ROLE_USER"]', 6847809, true,'$2y$13$k/kdXvXKPU96DbVfKVXdUeTaX26jcK0b4mUWzn3BEmxKbp0iPHEPe', '2023-05-11', '$2y$10$M5mtzfM0E7LeOp07zjfKKOnhEwgrhjoVOMZIjOIyfajTqQVRT.MfG', '1', NULL);

INSERT INTO localisation (id, user_id, ville, longitude, latitude, ip, date_in) VALUES
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
(57, 2, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:52:10'),
(35, 3, 'Yaoundé', 11.5154, 3.8661, '154.72.167.128', '2023-03-11 20:56:32'), 
(198, 1, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-05-21 08:59:48'),
(199, 1, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-05-21 09:50:37'),
(200, 1, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-05-21 09:51:12'),
(201, 1, 'Douala', 9.7023, 4.0505, '154.72.167.178', '2023-05-21 11:13:45'),
(202, 1, 'Douala', 9.7023, 4.0505, '154.72.167.194', '2023-05-21 19:12:00'),
(203, 1, 'Douala', 9.7023, 4.0505, '154.72.167.194', '2023-05-21 19:12:12'),
(55, NULL, 'Douala', 9.7023, 4.0505, '', '2023-05-21 20:53:38'),
(205, NULL, 'Douala', 9.7023, 4.0505, '', '2023-05-21 20:54:01'),
(206, NULL, 'Douala', 9.7023, 4.0505, '', '2023-05-21 21:01:09');

INSERT INTO boutique (id, user_id, localisation_id, category_id, titre, description, status, code_boutique, date_created) VALUES
(1, 34, 1, 1, 'Presta Shop', 'as', true, 'boutiqueopQ0O', '2022-12-18'),
(2, 2, 55, 5, 'King Shop', 'description*****', true, 'boutiqueYam45', '2022-12-18'),
(3, 12, 2, 3, 'Nutrition Prime', 'description*****', true, 'boutique3nIel', '2022-12-18'),
(4, 3, 5, 1, 'Mokolo Online', 'description*****', false, 'boutiqueTZiLl', '2022-12-18'),
(5, 16, 2, 4, 'uno', 'false', true, 'boutiqueiHNCR', '2023-02-13'),
(6, 13, 4, 1, 'on', 'Materiaux electroniques ', true, 'boutiquebTSKD', '2023-02-13'),
(7, 13, 57, 7, 'JAFRIKA DESIGN ', 'chez jafrika disign, nous mettons à votre disposition des chaussures et vêtements représentant la culture Cameroun et africains.', true, 'boutique4Sigt', '2023-03-12'),
(8, 17, NULL, 1, 'titre', '690863838', true, 'boutiquenCAg7', '2023-05-03'),
(9, 1, 206, 1, 'mouafo land', 'appareils électroniques au Cameroun Douala précisément',true, 'boutiqueLwQMu', '2023-05-21');

-- --------------------------------------------------------
 

INSERT INTO boutique_object (id, boutique_id, src, date_created) VALUES
(1, 1, 'bt67wKJ.jpg', '2023-03-11 20:33:33'),
(2, 3, 'btD9JB4.jpg', '2023-03-11 20:34:09'),
(3, 2, 'btg0Izt.jpg', '2023-03-11 20:35:43'),
(4, 4, 'btLBRwC.jpg', '2023-03-11 20:37:33'),
(5, 5, 'btP2l9M.jpg', '2023-03-11 20:37:48'),
(6, 6, 'btwbuTW.jpg', '2023-03-11 20:38:08'),
(7, 7, 'produitGTT5p.jpg', '2023-03-12 03:37:52'),
(8, 8, 'btGNRp8.png', '2023-05-03 18:45:55'),
(9, 6, 'btzdIZc.jpg', '2023-05-20 19:59:42'),
(10, 9, 'bt65pc4.jpg', '2023-05-21 21:01:09');

-- --------------------------------------------------------

 

-- --------------------------------------------------------
 
 
-- --------------------------------------------------------

--
-- Structure de la table commission
-- 
-- Déchargement des données de la table commission
--

INSERT INTO commission (id, pourcentage_produit, frais_livraison_produit, frais_buy_livreur) VALUES
(1, 2, 250, 500);

-- --------------------------------------------------------

--
-- Structure de la table compte
--
 
-- Déchargement des données de la table compte
--

INSERT INTO compte (id, user_id, solde) VALUES
(1, 1, 93500),
(2, 12, 0),
(3, 13, 0),
(4, 14, 0),
(5, 16, 0),
(6, 17, 0);
-- --------------------------------------------------------

--
-- Structure de la table connexion
--
 
-- Déchargement des données de la table doctrine_migration_versions
--

INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES
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
('DoctrineMigrations\\Version20230511210937', '2023-05-11 21:09:42', 146),
('DoctrineMigrations\\Version20230519070119', '2023-05-19 07:01:29', 448),
('DoctrineMigrations\\Version20230520172200', '2023-05-20 17:22:35', 5551);

-- --------------------------------------------------------

--
-- Structure de la table historique_paiement
--
 
--
-- Déchargement des données de la table jwt_refresh_token
--

INSERT INTO jwt_refresh_token (id, refresh_token, username, valid, date_expire_token) VALUES
(1, '00d776d450ecff01ff8bd40604fa7f7467f218dcfcb2404d59fabe7e268fd9d4285bb48ae16676019232214e2f3bfcb73e35f6a9fd58ec51ae2c700c4bc7e841', '690863838', '2023-06-18 07:02:50', '2023-05-19 08:02:02'),
(2, 'b50bf1bb2bb290dcd16d3712942bab830c28a3556fedad6cb522f8da44e66245d7658011847c7858fed14208cb7807c1f70b0353cb912172c896a8c3930f25cc', '690863838', '2023-06-18 07:06:43', '2023-05-19 08:06:43'),
(3, '2794434f68be41da3a8e61f27f48c1590a5846c7b1b50b0d6f9163e4974eee333a73822d0150da2a9809b1cdea6ea52a355d0d179536cdd7c55232ab6736d226', '690863838', '2023-06-18 18:08:44', '2023-05-19 19:08:44'),
(4, 'e1137a261474bd86a5803d68678699fa4a9ff7612cede109b894fcd439f22379aae6033dd7dc99985121ba2c19ff75f9e5f1a37dcce2c8b25bc164a9fd912520', '690863838', '2023-06-19 10:07:36', '2023-05-20 11:07:36');

-- --------------------------------------------------------

--
-- Structure de la table list_commande_livreur
--
 
-- Déchargement des données de la table list_produit_panier
-- 

-- --------------------------------------------------------

--
-- Structure de la table notation_boutique
--
 
--
-- Déchargement des données de la table notation_boutique
--

INSERT INTO notation_boutique (id, client_id, boutique_id, note, date_created) VALUES
(1, 2, 1, 2, '2023-05-08'),
(2, 1, 1, 1, '2023-05-08'),
(3, 1, 3, 4, '2023-05-08'),
(4, 2, 3, 2, '2023-05-08'),
(5, 2, 7, 2, '2023-05-20');

-- --------------------------------------------------------

--
-- Structure de la table notation_produit
--
 
--
-- Déchargement des données de la table notation_produit
--
 

-- --------------------------------------------------------

--
-- Structure de la table panier
--
 
--
-- Déchargement des données de la table panier
--
 
--
-- Structure de la table produit
-- 
INSERT INTO produit (id, category_id, boutique_id, titre, description, date_created, prix_unitaire, quantite, status, code_produit, taille) VALUES
(1, NULL, 6, 'iPhone X', 'iPhone x64go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 153250, 10, true, 'produitEs7BY', 0),
(2, NULL, 1, 'iPhone 8+', 'iPhone 8 6jj', '2023-03-11', 111707, 10, true, 'produitk3YEJ', 0),
(3, NULL, 6, 'iPhone 7+', 'iPhone 7 128go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 102250, 10, true, 'produituZhXa', 0),
(4, NULL, 4, 'Google pixel ', 'iPhone 8 64go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 92050, 100, true, 'produitIWMnx', 0),
(5, NULL, 6, 'techno', 'Techno 8 64go état de batterie 100/100 bypass prend pas la carte sim si non tout est clean connexion avec wifi intéressé me à un bon prix 😎', '2023-03-11', 51250, 100, true, 'produitMsqx8', 0),
(6, NULL, 4, 'chaussures ', 'escarpins new', '2023-03-11', 5350, 10, true, 'produitfNfem', 0),
(7, NULL, 2, 'montre Rolex ', 'montre Rolex de qualité supérieure disponible pour tous ', '2023-03-12', 5350, 10, true, 'produitqxMMS', 0),
(8, NULL, 2, 'pendule ', 'pendule de décoration très élégante pour tout type de maison ', '2023-03-12', 13510, 1000, true, 'produitF0kOZ', 0),
(9, NULL, 2, 'montre', 'montre pour homme et femme très élégante ', '2023-03-12', 5707, 10, true, 'produitp1XME', 0),
(10, NULL, 2, 'chaussures africaine ', 'chaussures de chez jafrika disign pour femme qui valorise la culture africaine. ', '2023-03-12', 8410, 10, true, 'produitNw2ug', 0),
(11, NULL, 2, 'découpe manuelle', 'découpe manuelle de cuisine qui aide a découper tout les éléments de cuisine comme tomate, conditions et autre .. a laide de sa manivelle, elle nous donne la possibilité de faire plusieurs découpe en réglant laipesseur personne.. je vous le recommande ', '2023-03-12', 8308, 20, true, 'produitN63s5', 0),
(12, NULL, 2, 'laptop ', '*HP EliteBook 840 G5 ultra slim et tres portable *- _Designé La Classe et la Performance 🤪-_* *Intel core i7 most recent professional business laptop* 🔥🔥* \n\n✳️ *Caractéristiques* 🏋🏿‍♂️ HP Elitebook 840 G5 Intel®️ core i7 8ieme Gen 💫💫 upto ~4.50Ghz \n\n✳️ *Disque (I)* SSD NVME 512Go plus\n\n\n ✳️*Ram 16GO extensible a 32/\n* \n✳️  *Carte graphique Intel UHD 620 5GB total  capable dexecuter Les taches Lourdes requisant jusquà 2 g Dédiés*🔥🔥\n\n✳️ USB 3.0  Type-C lecteur carte sim empreinte digitale / WI-FI/ Bluetooth, Webcam  / Écran 14\" POUCES FHD 🥳🥳/\n\n✳️  *Autonomie* , 💪🏾 Batteries \n\n✳️ Win 10 Pro ou 11pro  + Office déjà installé\n\n\n*🧯BARÊME ✅ 335 000 FRS CFA⭐⭐*\n\n#Vendue avec une facture+ Garantie et SAV assurée ✅🖥', '2023-03-12', 255250, 10, true, 'produitwZ8XI', 0),
(13, NULL, 7, 'chaussures ', 'chaussures typiquement en perle ', '2023-03-12', 8920, 20, true, 'produitXjgIF', 0),
(14, NULL, 3, 'Google pixel 3 a', 'photo de moi en fond décran ', '2023-03-12', 76750, 10, true, 'produittXcjp', 0),
(15, NULL, 3, 'Google pixel 3 a', 'photo de moi en fond décran ', '2023-03-12', 76750, 10, true, 'produitYmVVS', 0);

-- --------------------------------------------------------
 

INSERT INTO produit_object (id, produit_id, src, date_created) VALUES
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
-- Déchargement des données de la table short
--

INSERT INTO short (id, boutique_id, src, titre, status, description, date_created) VALUES
(5, 6, 'produitWxCZH.mp4', 'test', true, 'edx', '2023-03-26'),
(6, 6, '7a00378706b1e64865063f8549e66b10.mp4', 'test', true, 'edx', '2023-03-26'),
(7, 6, '0c969b1001a9c4c647c844dc2ceeb18f.mp4', 'test', true, 'edx', '2023-03-26'),
(8, 6, '0ccc5e58031d7ed1e56e5e0bc99e6aea.mp4', 'test', true, 'edx', '2023-03-26'),
(9, 6, '4f2c7547a40e47763d618fd37b7dbd2a.mp4', 'test', true, 'edx', '2023-03-26'),
(10, 6, '5d161e64fd4accf2e59fb6f2953cf5eb.mp4', 'test', true, 'edx', '2023-03-26'),
(11, 6, '6dd9d072b51e57060fdd9e64cd437c24.mp4', 'test', true, 'edx', '2023-03-26'),
(12, 6, 'produitZt0RX.mp4', 'test', true, 'edx', '2023-03-26'),
(13, 6, 'produitR6KAe.mp4', '001', true, '001', '2023-05-21');

-- --------------------------------------------------------

--
-- Structure de la table transaction
--
 
--
-- Déchargement des données de la table transaction
--
 
-- --------------------------------------------------------
 
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table boutique
-- 
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
