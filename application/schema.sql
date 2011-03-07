-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 07, 2011 at 01:37 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `astracan`
--

-- --------------------------------------------------------

--
-- Table structure for table `point`
--

CREATE TABLE IF NOT EXISTS `point` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `school` varchar(128) NOT NULL,
  `kind` enum('unknown','community','four_year','high_school') NOT NULL DEFAULT 'unknown',
  `course` enum('unknown','existing','newcourse') NOT NULL DEFAULT 'unknown',
  `email` varchar(128) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `state` varchar(2) NOT NULL,
  `address` varchar(256) NOT NULL,
  `coordinate` point NOT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `coordinate` (`coordinate`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
