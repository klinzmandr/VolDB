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

--
-- Dumping data for table `voltime`
--

INSERT INTO `voltime` (`VTID`, `VTDT`, `MCID`, `VolDate`, `VolTime`, `VolMileage`, `VolCategory`, `VolNotes`) VALUES
(1, '2014-02-19 19:32:24', 'ALV34', '2014-02-16', 11.50, NULL, 'CtrVol', NULL),
(2, '2014-02-19 19:32:24', 'DIL82', '2014-02-16', 8.00, NULL, 'CtrVol', NULL),
(3, '2014-02-19 19:32:24', 'OWE55', '2014-02-16', 4.00, NULL, 'CtrVol', NULL),
(4, '2014-02-19 19:32:24', 'CRO13', '2014-02-16', 4.00, NULL, 'Committee', NULL),
(5, '2014-02-19 19:32:24', 'THA32', '2014-02-16', 4.00, NULL, 'CtrVol', NULL),
(6, '2014-02-19 19:32:24', 'WOL37', '2014-02-16', 4.00, NULL, 'CtrVol', NULL),
(7, '2014-02-19 19:53:46', 'MEY56', '2014-02-16', 4.00, NULL, 'CtrVol', NULL),
(8, '2014-02-19 19:53:46', 'GRA10', '2014-02-16', 4.00, NULL, 'CtrVol', NULL),
(9, '2014-02-19 19:59:09', 'ALV34', '2014-02-17', 11.00, NULL, 'CtrVol', NULL),
(10, '2014-02-19 19:59:09', 'DIL82', '2014-02-17', 8.00, NULL, 'CtrVol', NULL),
(11, '2014-02-19 19:59:09', 'CHR16A', '2014-02-17', 4.50, NULL, 'CtrVol', NULL),
(12, '2014-02-19 19:59:09', 'COR77', '2014-02-17', 4.50, NULL, 'OfficeAdmin', NULL),
(13, '2014-02-19 19:59:09', 'DUM40', '2014-02-17', 5.50, NULL, 'CtrVol', NULL),
(14, '2014-02-19 19:59:09', 'GON27', '2014-02-17', 4.00, NULL, 'CtrVol', NULL),
(15, '2014-02-19 20:11:54', 'WRI15', '2014-02-17', 6.00, NULL, 'CtrVol', NULL),
(16, '2014-02-19 20:11:54', 'NEL23', '2014-02-17', 4.00, NULL, 'CtrVol', NULL),
(17, '2014-02-19 20:11:54', 'GOO15', '2014-02-17', 4.00, NULL, 'CtrVol', NULL),
(19, '2014-02-19 22:20:14', 'DUN13', '2014-02-18', 5.00, NULL, 'CtrVol', NULL),
(20, '2014-02-19 22:20:14', 'DUM40', '2014-02-18', 9.00, NULL, 'CtrVol', NULL),
(21, '2014-02-19 22:20:14', 'AIE30', '2014-02-18', 8.00, NULL, 'CtrVol', NULL),
(22, '2014-02-19 22:20:14', 'LAR33', '2014-02-18', 8.00, NULL, 'CtrVol', NULL),
(23, '2014-02-19 22:20:14', 'HEN26', '2014-02-18', 4.00, NULL, 'CtrVol', NULL),
(24, '2014-02-19 22:20:14', 'KUN58', '2014-02-18', 2.00, NULL, 'CtrVol', NULL),
(25, '2014-02-19 22:20:14', 'HAR21A', '2014-02-18', 3.00, NULL, 'CtrVol', NULL),
(26, '2014-02-19 22:20:14', 'ALV34', '2014-02-18', 2.00, NULL, 'CtrVol', NULL),
(27, '2014-02-19 22:20:14', 'PEA97', '2014-02-18', 1.00, 60, 'Transporter', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
