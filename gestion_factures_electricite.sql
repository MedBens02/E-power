-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 01, 2025 at 05:33 PM
-- Server version: 8.3.0
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_factures_electricite`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `created_at`) VALUES
(1, 'bens', 'med', 'medbens@gmail.com', '$2y$10$aA2ZSbPQxBZzVUliiiC4j.X35FUVuDNjFmhXrJ21BmmDjhs9vd5b.', '2025-03-28 00:07:02'),
(2, 'John', 'Smith', 'admin1@electricbill.com', '$2y$10$zvBR11zYmLLpIxePF/gYIusEZaAs8Sbd3T0Ictydt3UsCRY/knWE6', '2025-04-01 10:24:38'),
(3, 'Alice', 'Brown', 'admin2@electricbill.com', '$2y$10$rnEpjXhL.Pwpe.OB7mbUxuTui3GzVuHgtM..6KaQ6ZKcZ3LSFEIJW', '2025-04-01 10:24:38');

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
CREATE TABLE IF NOT EXISTS `agents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `nom`, `prenom`, `matricule`) VALUES
(1, 'Youssef', 'El Majdoub', 'AG-100'),
(2, 'Fatima', 'Karim', 'AG-101');

-- --------------------------------------------------------

--
-- Table structure for table `anomalies`
--

DROP TABLE IF EXISTS `anomalies`;
CREATE TABLE IF NOT EXISTS `anomalies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `consommation_mensuelle_id` int NOT NULL,
  `consommation_annuelle_id` int NOT NULL,
  `ecart` int NOT NULL,
  `statut` enum('non corrigé','corrigé') DEFAULT 'non corrigé',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `consommation_mensuelle_id` (`consommation_mensuelle_id`),
  KEY `consommation_annuelle_id` (`consommation_annuelle_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anomalies`
--

INSERT INTO `anomalies` (`id`, `client_id`, `consommation_mensuelle_id`, `consommation_annuelle_id`, `ecart`, `statut`) VALUES
(1, 1, 1, 1, 10, 'non corrigé'),
(2, 2, 3, 2, 50, 'non corrigé');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `adresse`, `created_at`) VALUES
(1, 'client', 'med', 'medcl@client.com', '$2y$10$NsT1eOVPJvIK6RoBxAOcmOaattAKiNBgfrAq6CYjcg0.iEBgMsRZS', 'sadnkjbcjk b', '2025-03-28 00:23:49'),
(2, 'Benani', 'Naima', 'client1@electricbill.com', '$2y$10$8Zz/ahE43Gw8vMthCzLZUO31A3G41cBpgMgOvRS8N8bR/Hd18xTZq', 'Rue des Fleurs, Tanger', '2025-04-01 10:24:38'),
(3, 'El', 'Hassan', 'client2@electricbill.com', '$2y$10$jdHySr7VxmRkK2nFQLW/gO4OmxFrXrHwDK.lU7713.EZlC7cb0Xz6', 'Rue du Soleil, Casablanca', '2025-04-01 10:24:38'),
(4, 'testdd', 'ajax', 'ajxtst@gmail.com', '$2y$10$KzCK5.1rE6Z8sItShEgQBO0drt7La6r05p/Q6kNTl6A.orE9ZCSIe\n', 'cdaklnv amafmmkd', '2025-04-01 10:41:17');

-- --------------------------------------------------------

--
-- Table structure for table `compteurs`
--

DROP TABLE IF EXISTS `compteurs`;
CREATE TABLE IF NOT EXISTS `compteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `consommation_totale` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `compteurs`
--

INSERT INTO `compteurs` (`id`, `client_id`, `description`, `consommation_totale`, `created_at`) VALUES
(1, 1, 'Mon compteur 1', 775, '2025-04-01 10:24:38'),
(2, 2, 'Mon compteur 1', 500, '2025-04-01 10:24:38'),
(3, 1, 'Compteur Secondaire (Maison secondaire)', 250, '2025-04-01 13:30:03');

-- --------------------------------------------------------

--
-- Table structure for table `consommations_annuelles`
--

DROP TABLE IF EXISTS `consommations_annuelles`;
CREATE TABLE IF NOT EXISTS `consommations_annuelles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `agent_id` int NOT NULL,
  `annee` int NOT NULL,
  `consommation` int NOT NULL,
  `date_saisie` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `agent_id` (`agent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consommations_annuelles`
--

INSERT INTO `consommations_annuelles` (`id`, `client_id`, `agent_id`, `annee`, `consommation`, `date_saisie`) VALUES
(1, 1, 1, 2024, 1500, '2025-03-01'),
(2, 2, 2, 2024, 2100, '2025-03-01');

-- --------------------------------------------------------

--
-- Table structure for table `consommations_mensuelles`
--

DROP TABLE IF EXISTS `consommations_mensuelles`;
CREATE TABLE IF NOT EXISTS `consommations_mensuelles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `compteur_id` int NOT NULL,
  `mois` int NOT NULL,
  `annee` int NOT NULL,
  `valeur_compteur` int NOT NULL,
  `photo_compteur` varchar(255) DEFAULT NULL,
  `anomalie` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `compteur_id` (`compteur_id`),
  KEY `idx_client_mois_annee` (`client_id`,`mois`,`annee`)
) ;

--
-- Dumping data for table `consommations_mensuelles`
--

INSERT INTO `consommations_mensuelles` (`id`, `client_id`, `compteur_id`, `mois`, `annee`, `valeur_compteur`, `photo_compteur`, `anomalie`) VALUES
(1, 1, 1, 1, 2025, 120, 'compteur1_jan.jpg', 0),
(2, 1, 1, 2, 2025, 130, 'compteur1_feb.jpg', 0),
(3, 2, 2, 1, 2025, 200, 'compteur2_jan.jpg', 0),
(4, 1, 1, 4, 2025, 160, 'placeholder-meter.jpg', 0),
(5, 1, 1, 4, 2025, 70, 'placeholder-meter.jpg', 0),
(6, 1, 3, 3, 2025, 270, 'compteur3_mar.jpg', 0),
(7, 1, 3, 4, 2025, 300, 'compteur3_apr.jpg', 0),
(8, 1, 1, 4, 2025, 100, '1743514239_enonce.jpg', 0),
(9, 1, 1, 4, 2025, 70, '1743514477_test.jpg', 0),
(10, 1, 1, 4, 2025, 75, '1743526512_enonce.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `consommation_id` int NOT NULL,
  `mois` int NOT NULL,
  `annee` int NOT NULL,
  `prix_ht` decimal(10,2) NOT NULL,
  `tva` decimal(10,2) NOT NULL DEFAULT '18.00',
  `prix_ttc` decimal(10,2) GENERATED ALWAYS AS ((`prix_ht` * (1 + (`tva` / 100)))) STORED,
  `statut_paiement` enum('payée','non payée') DEFAULT 'non payée',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `consommation_id` (`consommation_id`),
  KEY `idx_facture_statut` (`statut_paiement`)
) ;

--
-- Dumping data for table `factures`
--

INSERT INTO `factures` (`id`, `client_id`, `consommation_id`, `mois`, `annee`, `prix_ht`, `tva`, `statut_paiement`) VALUES
(1, 1, 1, 1, 2025, 110.40, 18.00, 'payée'),
(2, 1, 2, 2, 2025, 119.60, 18.00, 'non payée'),
(3, 2, 3, 1, 2025, 220.00, 18.00, 'payée');

--
-- Triggers `factures`
--
DROP TRIGGER IF EXISTS `calcul_prix_ht`;
DELIMITER $$
CREATE TRIGGER `calcul_prix_ht` BEFORE INSERT ON `factures` FOR EACH ROW BEGIN
    DECLARE consommation INT;
    DECLARE prix DECIMAL(10,2);
    
    SELECT valeur_compteur INTO consommation 
    FROM consommations_mensuelles 
    WHERE id = NEW.consommation_id;
    
    SELECT prix_unitaire INTO prix 
    FROM tarifs 
    WHERE consommation BETWEEN tranche_min AND tranche_max 
    LIMIT 1;
    
    SET NEW.prix_ht = consommation * prix;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `message` text NOT NULL,
  `date_envoi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `client_id`, `message`, `date_envoi`, `lu`) VALUES
(1, 1, 'Votre facture de Janvier est payée.', '2025-04-01 10:24:38', 0),
(2, 2, 'Votre facture de Janvier est en attente de paiement.', '2025-04-01 10:24:38', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reclamations`
--

DROP TABLE IF EXISTS `reclamations`;
CREATE TABLE IF NOT EXISTS `reclamations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `type` enum('Fuite externe','Fuite interne','Facture','Autre') NOT NULL,
  `description` text,
  `statut` enum('en attente','résolu') DEFAULT 'en attente',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pieces_jointes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `idx_reclamation_statut` (`statut`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reclamations`
--

INSERT INTO `reclamations` (`id`, `client_id`, `type`, `description`, `statut`, `date_creation`, `pieces_jointes`) VALUES
(1, 1, 'Facture', 'Montant trop élevé pour janvier.', 'en attente', '2025-04-01 10:24:38', NULL),
(2, 2, 'Autre', 'Service client injoignable ce week-end.', 'en attente', '2025-04-01 10:24:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tarifs`
--

DROP TABLE IF EXISTS `tarifs`;
CREATE TABLE IF NOT EXISTS `tarifs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tranche_min` int NOT NULL,
  `tranche_max` int NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tarifs`
--

INSERT INTO `tarifs` (`id`, `tranche_min`, `tranche_max`, `prix_unitaire`) VALUES
(1, 0, 100, 0.82),
(2, 101, 150, 0.92),
(3, 151, 9999, 1.10);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
