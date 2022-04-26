-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.7.3-MariaDB-1:10.7.3+maria~focal - mariadb.org binary distribution
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             11.2.0.6292
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for jttproducts
CREATE DATABASE IF NOT EXISTS `jttproducts` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `jttproducts`;

-- Dumping structure for table jttproducts.junction_ptype_propname
CREATE TABLE IF NOT EXISTS `junction_ptype_propname` (
  `id_product_type` int(10) unsigned NOT NULL,
  `id_property_name` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_product_type`,`id_property_name`),
  KEY `id_property_name` (`id_property_name`),
  CONSTRAINT `junction_ptype_propname_ibfk_1` FOREIGN KEY (`id_product_type`) REFERENCES `product_types` (`id`),
  CONSTRAINT `junction_ptype_propname_ibfk_2` FOREIGN KEY (`id_property_name`) REFERENCES `property_names` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table jttproducts.junction_ptype_propname: ~5 rows (approximately)
/*!40000 ALTER TABLE `junction_ptype_propname` DISABLE KEYS */;
INSERT INTO `junction_ptype_propname` (`id_product_type`, `id_property_name`) VALUES
	(1, 2),
	(2, 1),
	(3, 3),
	(3, 4),
	(3, 5);
/*!40000 ALTER TABLE `junction_ptype_propname` ENABLE KEYS */;

-- Dumping structure for table jttproducts.products
CREATE TABLE IF NOT EXISTS `products` (
  `sku` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(20,2) unsigned NOT NULL,
  `id_product_type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`sku`),
  KEY `id_product_type` (`id_product_type`),
  KEY `sku` (`sku`,`id_product_type`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_product_type`) REFERENCES `product_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table jttproducts.products: ~3 rows (approximately)
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`sku`, `name`, `price`, `id_product_type`) VALUES
	('B7652', 'My 1st Book', 17.00, 1),
	('D8523875', 'My 1st Disk', 6.34, 2),
	('F82354', 'Chair', 30.00, 3),
	('F823542', 'Chair 2', 30.00, 3),
	('F9486300', 'Big Table', 86.00, 3),
	('F948638', 'Table', 64.99, 3);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;

-- Dumping structure for table jttproducts.product_types
CREATE TABLE IF NOT EXISTS `product_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_type` varchar(50) NOT NULL,
  `block_css_id` varchar(50) NOT NULL,
  `block_description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table jttproducts.product_types: ~3 rows (approximately)
/*!40000 ALTER TABLE `product_types` DISABLE KEYS */;
INSERT INTO `product_types` (`id`, `product_type`, `block_css_id`, `block_description`) VALUES
	(1, 'Book', 'Book', 'Please provide the book\'s weight in kg:'),
	(2, 'DVD', 'DVD', 'Please provide the data size in MB:'),
	(3, 'Furniture', 'Furniture', 'Please provide dimensions in HxWxL format:');
/*!40000 ALTER TABLE `product_types` ENABLE KEYS */;

-- Dumping structure for table jttproducts.property_names
CREATE TABLE IF NOT EXISTS `property_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `property_name` varchar(50) NOT NULL,
  `property_css_id` varchar(50) NOT NULL,
  `property_input_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table jttproducts.property_names: ~5 rows (approximately)
/*!40000 ALTER TABLE `property_names` DISABLE KEYS */;
INSERT INTO `property_names` (`id`, `property_name`, `property_css_id`, `property_input_name`) VALUES
	(1, 'Size (MB): ', 'size', 'size'),
	(2, 'Weight (KG): ', 'weight', 'weight'),
	(3, 'Height (CM): ', 'height', 'height'),
	(4, 'Width (CM): ', 'width', 'width'),
	(5, 'Length (CM): ', 'length', 'length');
/*!40000 ALTER TABLE `property_names` ENABLE KEYS */;

-- Dumping structure for table jttproducts.property_values
CREATE TABLE IF NOT EXISTS `property_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_sku` varchar(50) NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  `property_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `id_product_type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_sku` (`product_sku`,`property_id`),
  KEY `property_values_ibfk_1` (`product_sku`,`id_product_type`),
  KEY `property_values_ibfk_2` (`property_id`,`id_product_type`),
  CONSTRAINT `property_values_ibfk_1` FOREIGN KEY (`product_sku`, `id_product_type`) REFERENCES `products` (`sku`, `id_product_type`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `property_values_ibfk_2` FOREIGN KEY (`property_id`, `id_product_type`) REFERENCES `junction_ptype_propname` (`id_property_name`, `id_product_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table jttproducts.property_values: ~5 rows (approximately)
/*!40000 ALTER TABLE `property_values` DISABLE KEYS */;
INSERT INTO `property_values` (`id`, `product_sku`, `property_id`, `property_value`, `id_product_type`) VALUES
	(6, 'B7652', 2, 1.56, 1),
	(22, 'D8523875', 1, 2658.00, 2),
	(23, 'F82354', 3, 50.00, 3),
	(24, 'F82354', 4, 30.00, 3),
	(25, 'F82354', 5, 30.00, 3),
	(26, 'F823542', 3, 50.00, 3),
	(27, 'F823542', 4, 35.00, 3),
	(28, 'F823542', 5, 35.00, 3),
	(29, 'F948638', 3, 70.00, 3),
	(30, 'F948638', 4, 60.00, 3),
	(31, 'F948638', 5, 120.00, 3),
	(32, 'F9486300', 3, 70.00, 3),
	(33, 'F9486300', 4, 80.00, 3),
	(34, 'F9486300', 5, 150.00, 3);
/*!40000 ALTER TABLE `property_values` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
