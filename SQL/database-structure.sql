/*
Navicat MariaDB Data Transfer

Source Server         : localmariadb
Source Server Version : 100108
Source Host           : localhost:3306
Source Database       : bod_core

Target Server Type    : MariaDB
Target Server Version : 100108
File Encoding         : 65001

Date: 2015-12-12 11:55:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for address
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `title` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Title of address.',
  `zip` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'Zip / postal code of the address.',
  `city` int(10) unsigned NOT NULL COMMENT 'City of address.',
  `state` int(10) unsigned DEFAULT NULL COMMENT 'State of address.',
  `country` int(10) unsigned NOT NULL COMMENT 'Country of address.',
  `site` int(10) unsigned NOT NULL COMMENT 'Site of address.',
  `neighborhood` int(10) unsigned DEFAULT NULL COMMENT 'Neighborhood of address.',
  `district` int(10) unsigned DEFAULT NULL COMMENT 'District of address.',
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idxFCityOfAddress` (`city`),
  KEY `idxFStateOfAddress` (`state`),
  KEY `idxFSiteOfAddress` (`site`),
  KEY `idxFCountryOfAddress` (`country`),
  KEY `idxFNeighborhoodOfAddress` (`neighborhood`),
  KEY `idxFDistrictOfCity` (`district`),
  CONSTRAINT `idxFCityOfAddress` FOREIGN KEY (`city`) REFERENCES `city` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFCountryOfAddress` FOREIGN KEY (`country`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFDistrictOfCity` FOREIGN KEY (`district`) REFERENCES `district` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFNeighborhoodOfAddress` FOREIGN KEY (`neighborhood`) REFERENCES `state` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSiteOfAddress` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFStateOfAddress` FOREIGN KEY (`state`) REFERENCES `state` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for address_type
-- ----------------------------
DROP TABLE IF EXISTS `address_type`;
CREATE TABLE `address_type` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for address_type_localization
-- ----------------------------
DROP TABLE IF EXISTS `address_type_localization`;
CREATE TABLE `address_type_localization` (
  `address_type` int(5) unsigned NOT NULL,
  `language` int(5) unsigned NOT NULL,
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL,
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`address_type`,`language`),
  KEY `idxFLanguageOfAddressTypeLocalization` (`language`),
  CONSTRAINT `idxFLanguageOfAddressTypeLocalization` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedAddressType` FOREIGN KEY (`address_type`) REFERENCES `address_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for addresses_of_member
-- ----------------------------
DROP TABLE IF EXISTS `addresses_of_member`;
CREATE TABLE `addresses_of_member` (
  `address` int(20) unsigned NOT NULL,
  `member` int(10) unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `type` int(5) unsigned NOT NULL,
  PRIMARY KEY (`address`,`member`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Table structure for phone_numbers_of_addresses
-- ----------------------------
DROP TABLE IF EXISTS `phone_numbers_of_addresses`;
CREATE TABLE `phone_numbers_of_addresses` (
  `address` int(20) unsigned NOT NULL,
  `phone` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`address`,`phone`),
  KEY `idxFPhoneNumberOfAddress` (`phone`),
  CONSTRAINT `idxFAddressOfPhoneNumber` FOREIGN KEY (`address`) REFERENCES `address` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFPhoneNumberOfAddress` FOREIGN KEY (`phone`) REFERENCES `phone_number` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;
