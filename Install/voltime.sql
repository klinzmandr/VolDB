-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: pacificwildlifecare.org
-- Generation Time: Feb 19, 2014 at 10:02 PM
-- Server version: 5.1.65
-- PHP Version: 5.3.10-1ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pwcmbrdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `voltime`
--

CREATE TABLE IF NOT EXISTS `voltime` (
  `VTID` int(6) NOT NULL AUTO_INCREMENT,
  `VTDT` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `MCID` varchar(6) DEFAULT NULL,
  `VolDate` varchar(12) DEFAULT NULL,
  `VolTime` decimal(5,2) DEFAULT NULL,
  `VolMileage` int(5) DEFAULT NULL,
  `VolCategory` varchar(20) DEFAULT NULL,
  `VolNotes` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`VTID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
