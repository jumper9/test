-- Adminer 4.2.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `sc_captcha`;
CREATE TABLE `sc_captcha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8 NOT NULL,
  `created_date` datetime NOT NULL,
  `remote_ip` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sc_prizes`;
CREATE TABLE `sc_prizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `initial_stock` int(11) NOT NULL,
  `actual_stock` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `sc_prizes` (`id`, `name`, `code`, `start_date`, `end_date`, `initial_stock`, `actual_stock`, `status`) VALUES
(1,	'premio 1 -  activo',	'1111',	'2015-01-01 00:00:00',	'2016-01-01 00:00:00',	100,	93,	1),
(2,	'Premio 2 - vencido',	'1112',	'2015-01-01 00:00:00',	'2015-05-01 00:00:00',	100,	100,	1),
(3,	'Premio 3 - todavia no activo',	'1113',	'2015-08-01 00:00:00',	'2015-09-01 00:00:00',	100,	100,	1),
(4,	'Premio 4 - sin stock',	'1114',	'2015-01-01 00:00:00',	'2016-01-01 00:00:00',	100,	0,	1);

DROP TABLE IF EXISTS `sc_users`;
CREATE TABLE `sc_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dni` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `data` varchar(4000) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sc_winners`;
CREATE TABLE `sc_winners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `prize_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `prize_id` (`prize_id`),
  CONSTRAINT `sc_winners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sc_users` (`id`),
  CONSTRAINT `sc_winners_ibfk_2` FOREIGN KEY (`prize_id`) REFERENCES `sc_prizes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

