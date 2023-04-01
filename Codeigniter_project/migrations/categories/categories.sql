-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: test_mv2
-- ------------------------------------------------------
-- Server version	5.1.73-log

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

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `marketplaceCount` int(11) DEFAULT NULL,
  `storeCount` int(11) DEFAULT NULL,
  `parentid` int(11) NOT NULL DEFAULT '-1',
  `level` tinyint(2) NOT NULL DEFAULT '0',
  `weight` float NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Appliances',NULL,NULL,-1,0,1),(2,'Apps & Games',NULL,NULL,-1,0,1),(3,'Arts, Crafts & Sewing',NULL,NULL,-1,0,1),(4,'Automotive',NULL,NULL,-1,0,1),(5,'Baby',NULL,NULL,10,0,1),(6,'Beauty',NULL,NULL,-1,0,1),(7,'Books',NULL,NULL,-1,0,1),(8,'CDs & Vinyl',NULL,NULL,-1,0,1),(9,'Cell Phones & Accessories',NULL,NULL,-1,0,1),(10,'Clothing, Shoes & Jewelry',NULL,NULL,-1,0,1),(11,'Women',NULL,NULL,10,0,1),(12,'Men',NULL,NULL,10,0,1),(13,'Girls',NULL,NULL,10,0,1),(14,'Boys',NULL,NULL,10,0,1),(15,'Baby',NULL,NULL,-1,0,1),(16,'Collectibles & Fine Art',NULL,NULL,-1,0,1),(17,'Computers',NULL,NULL,-1,0,1),(18,'Credit and Payment Cards',NULL,NULL,-1,0,1),(19,'Digital Music',NULL,NULL,-1,0,1),(20,'Electronics',NULL,NULL,-1,0,1),(21,'Gift Cards',NULL,NULL,-1,0,1),(22,'Grocery & Gourmet Food',NULL,NULL,-1,0,1),(23,'Health & Personal Care',NULL,NULL,-1,0,1),(24,'Home & Kitchen',NULL,NULL,-1,0,1),(25,'Industrial & Scientific',NULL,NULL,-1,0,1),(26,'Kindle Store',NULL,NULL,-1,0,1),(27,'Luggage & Travel Gear',NULL,NULL,-1,0,1),(28,'Magazine Subscriptions',NULL,NULL,-1,0,1),(29,'Movies & TV',NULL,NULL,-1,0,1),(30,'Musical Instruments',NULL,NULL,-1,0,1),(31,'Office Products',NULL,NULL,-1,0,1),(32,'Patio, Lawn & Garden',NULL,NULL,-1,0,1),(33,'Pet Supplies',NULL,NULL,-1,0,1),(34,'Prime Pantry',NULL,NULL,-1,0,1),(35,'Software',NULL,NULL,-1,0,1),(36,'Sports & Outdoors',NULL,NULL,-1,0,1),(37,'Tools & Home Improvement',NULL,NULL,-1,0,1),(38,'Toys & Games',NULL,NULL,-1,0,1),(39,'Video Games',NULL,NULL,-1,0,1),(40,'Wine',NULL,NULL,-1,0,1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-12-02 13:32:00
