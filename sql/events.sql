-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 22 Avril 2016 à 15:09
-- Version du serveur :  5.7.9
-- Version de PHP :  5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `fluxbb`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `event_desc` varchar(300) NOT NULL,
  `start` int(10) NOT NULL,
  `end` int(10) NOT NULL,
  `max_users` int(10) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `is_multi_game` tinyint(1) DEFAULT NULL,
  `title_formatted` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

--
-- Contenu de la table `events`
--

INSERT INTO `events` (`id`, `owner_id`, `title`, `event_desc`, `start`, `end`, `max_users`, `topic_id`, `is_multi_game`, `title_formatted`) VALUES
(47, 1, 'Test', 'Desc', 1461276000, 1461276000, 0, 47, 1, 'Partie du Vendredi 22 Avril'),
(48, 1, 'Test', 'Desc', 1461276000, 1461276000, 0, 47, 0, 'Partie du Vendredi 22 Avril'),
(49, 1, 'Test', 'Desc', 1461276000, 1461276000, 0, 48, 0, 'Partie du Vendredi 22 Avril');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
