-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2011 at 02:56 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `phpleague_wordpress`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_club`
--

DROP TABLE IF EXISTS `blog_phpleague_club`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_club` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `id_country` smallint(4) unsigned NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `coach` varchar(100) DEFAULT NULL,
  `creation` year(4) NOT NULL DEFAULT '0000',
  `website` varchar(255) DEFAULT NULL,
  `logo_big` varchar(255) DEFAULT NULL,
  `logo_mini` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_country`
--

DROP TABLE IF EXISTS `blog_phpleague_country`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_country` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_fixture`
--

DROP TABLE IF EXISTS `blog_phpleague_fixture`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_fixture` (
  `number` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `scheduled` date NOT NULL DEFAULT '0000-00-00',
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_league` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_league` (`id_league`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_league`
--

DROP TABLE IF EXISTS `blog_phpleague_league`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_league` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `year` year(4) NOT NULL,
  `pt_victory` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `pt_draw` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `pt_defeat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `promotion` tinyint(3) unsigned NOT NULL DEFAULT '4',
  `qualifying` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `relegation` tinyint(3) unsigned NOT NULL DEFAULT '3',
  `id_favorite` smallint(4) unsigned NOT NULL DEFAULT '0',
  `nb_leg` tinyint(1) NOT NULL DEFAULT '2',
  `team_link` enum('no','yes') NOT NULL DEFAULT 'no',
  `default_time` time NOT NULL DEFAULT '17:00:00',
  `nb_teams` tinyint(1) NOT NULL DEFAULT '0',
  `player_mod` enum('no','yes') NOT NULL DEFAULT 'no',
  `sport_type` varchar(50) NOT NULL DEFAULT 'football',
  `starting` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `substitute` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `prediction_mod` enum('no','yes') NOT NULL DEFAULT 'no',
  `point_right` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `point_wrong` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `point_part` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `deadline` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_match`
--

DROP TABLE IF EXISTS `blog_phpleague_match`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_match` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `id_team_home` smallint(5) unsigned DEFAULT NULL,
  `id_team_away` smallint(5) unsigned DEFAULT NULL,
  `played` datetime DEFAULT NULL,
  `id_fixture` smallint(5) unsigned DEFAULT NULL,
  `goal_home` tinyint(1) unsigned DEFAULT NULL,
  `goal_away` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_fixture` (`id_fixture`),
  KEY `id_team_away` (`id_team_away`),
  KEY `id_team_home` (`id_team_home`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_player`
--

DROP TABLE IF EXISTS `blog_phpleague_player`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_player` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL DEFAULT '',
  `lastname` varchar(100) NOT NULL DEFAULT '',
  `description` text,
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `weight` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `height` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `picture` varchar(255) NOT NULL DEFAULT '',
  `id_country` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_term` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_player_data`
--

DROP TABLE IF EXISTS `blog_phpleague_player_data`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_player_data` (
  `id_event` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_player_team` smallint(4) unsigned NOT NULL DEFAULT '0',
  `id_match` smallint(4) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `id_match` (`id_match`),
  KEY `id_event` (`id_event`),
  KEY `id_player_team` (`id_player_team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_player_team`
--

DROP TABLE IF EXISTS `blog_phpleague_player_team`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_player_team` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `id_player` smallint(4) unsigned NOT NULL DEFAULT '0',
  `id_team` smallint(4) unsigned NOT NULL DEFAULT '0',
  `number` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `position` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_player` (`id_player`),
  KEY `id_team` (`id_team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_table_cache`
--

DROP TABLE IF EXISTS `blog_phpleague_table_cache`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_table_cache` (
  `club_name` varchar(255) DEFAULT NULL,
  `points` smallint(4) unsigned DEFAULT NULL,
  `played` tinyint(3) unsigned DEFAULT NULL,
  `victory` tinyint(3) unsigned DEFAULT NULL,
  `draw` tinyint(3) unsigned DEFAULT NULL,
  `defeat` tinyint(3) unsigned DEFAULT NULL,
  `goal_for` smallint(4) unsigned DEFAULT NULL,
  `goal_against` smallint(4) unsigned DEFAULT NULL,
  `diff` smallint(4) DEFAULT NULL,
  `pen` tinyint(2) DEFAULT NULL,
  `home_points` smallint(4) unsigned DEFAULT NULL,
  `home_played` tinyint(3) unsigned DEFAULT NULL,
  `home_v` tinyint(3) unsigned DEFAULT NULL,
  `home_d` tinyint(3) unsigned DEFAULT NULL,
  `home_l` tinyint(3) unsigned DEFAULT NULL,
  `home_g_for` smallint(4) unsigned DEFAULT NULL,
  `home_g_against` smallint(4) unsigned DEFAULT NULL,
  `home_diff` smallint(4) DEFAULT NULL,
  `away_points` smallint(4) unsigned DEFAULT NULL,
  `away_played` tinyint(3) unsigned DEFAULT NULL,
  `away_v` tinyint(3) unsigned DEFAULT NULL,
  `away_d` tinyint(3) unsigned DEFAULT NULL,
  `away_l` tinyint(3) unsigned DEFAULT NULL,
  `away_g_for` smallint(4) unsigned DEFAULT NULL,
  `away_g_against` smallint(4) unsigned DEFAULT NULL,
  `away_diff` tinyint(4) DEFAULT NULL,
  `id_team` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_league` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `id_league` (`id_league`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_table_chart`
--

DROP TABLE IF EXISTS `blog_phpleague_table_chart`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_table_chart` (
  `id_team` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `fixture` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ranking` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `id_team` (`id_team`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_table_prediction`
--

DROP TABLE IF EXISTS `blog_phpleague_table_prediction`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_table_prediction` (
  `id_league` smallint(3) unsigned NOT NULL DEFAULT '0',
  `id_member` smallint(3) unsigned NOT NULL DEFAULT '0',
  `points` smallint(3) unsigned NOT NULL DEFAULT '0',
  `participation` smallint(3) unsigned NOT NULL DEFAULT '0',
  `type` enum('demo','demo2') NOT NULL DEFAULT 'demo',
  KEY `id_league` (`id_league`),
  KEY `id_member` (`id_member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `blog_phpleague_team`
--

DROP TABLE IF EXISTS `blog_phpleague_team`;
CREATE TABLE IF NOT EXISTS `blog_phpleague_team` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_league` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_club` smallint(5) unsigned NOT NULL DEFAULT '0',
  `penalty` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_league` (`id_league`),
  KEY `id_club` (`id_club`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;