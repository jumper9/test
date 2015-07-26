-- Adminer 4.2.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `fm_clients`;
CREATE TABLE `fm_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `fm_forms`;
CREATE TABLE `fm_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `local_domain` varchar(255) CHARACTER SET utf8 NOT NULL,
  `dev_domain` varchar(255) CHARACTER SET utf8 NOT NULL,
  `qa_domain` varchar(255) CHARACTER SET utf8 NOT NULL,
  `prod_domain` varchar(255) CHARACTER SET utf8 NOT NULL,
  `detail` text CHARACTER SET utf8 NOT NULL,
  `available_from` datetime NOT NULL,
  `available_to` datetime NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `fm_forms_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `fm_clients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fm_userdata`;
CREATE TABLE `fm_userdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `document` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `phone2` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `extra_data` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  CONSTRAINT `fm_userdata_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `fm_forms` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fm_users`;
CREATE TABLE `fm_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `fm_users_clients`;
CREATE TABLE `fm_users_clients` (
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `can_modify_forms` int(11) NOT NULL,
  `can_read_data` int(11) NOT NULL,
  KEY `client_id` (`client_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fm_users_clients_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `fm_clients` (`id`),
  CONSTRAINT `fm_users_clients_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `fm_users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2015-07-20 21:01:49
