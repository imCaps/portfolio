CREATE DATABASE `sport_events`;
USE `sport_events`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `athletes`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `points`;

CREATE TABLE IF NOT EXISTS `athletes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chip_id` int(11) NOT NULL,
  `start_number` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chip_id` int(11) NOT NULL,
  `point_id` int(11) NOT NULL,
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_id` varchar(50) NOT NULL COMMENT 'final-corridor, finish-line...',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `athletes` ADD UNIQUE KEY `start_number` (`start_number`), ADD UNIQUE KEY `chip_id` (`chip_id`);


ALTER TABLE `points` ADD UNIQUE KEY `point_id` (`point_id`);

INSERT INTO `athletes` (`id`, `chip_id`, `start_number`, `full_name`) VALUES
  (0, 123, 52498, 'Michael Phelps'),
  (1, 432, 1245, 'Larisa Latynina'),
  (2, 124, 4543, 'Takashi Ono'),
  (3, 234, 24331, 'Paavo Nurmi');

INSERT INTO `points` (`id`, `point_id`) VALUES
  (0, 'final-corridor'),
  (1, 'finish-line');

INSERT INTO `events` (`id`, `chip_id`, `point_id`) VALUES
  (0, 123, 0);