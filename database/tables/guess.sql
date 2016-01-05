-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.16 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table mohicanenpieguesser.mpg_guess
CREATE TABLE IF NOT EXISTS `mpg_guess` (
  `guess_id` int(11) NOT NULL AUTO_INCREMENT,
  `guess_session_id` varchar(255) NOT NULL,
  `guess_first_name` varchar(255) NOT NULL,
  `guess_last_name` varchar(255) NOT NULL,
  `guess_mail` text NOT NULL,
  `guess_weight` float NOT NULL,
  `guess_datetime` datetime NOT NULL,
  `guess_ip` tinytext NOT NULL,
  PRIMARY KEY (`guess_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
