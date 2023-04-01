-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: vbox
-- ------------------------------------------------------
-- Server version	5.5.41-0ubuntu0.14.04.1

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(60) DEFAULT NULL,
  `last_name` varchar(60) DEFAULT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `password` varchar(64) NOT NULL DEFAULT '',
  `gender` varchar(10) NOT NULL DEFAULT '',
  `activated` int(11) NOT NULL DEFAULT '0',
  `country_short` varchar(10) DEFAULT NULL,
  `country_long` varchar(100) DEFAULT NULL,
  `locality_short` varchar(10) DEFAULT NULL,
  `locality_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_1_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_1_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_2_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_2_long` varchar(100) DEFAULT NULL,
  `session` varchar(100) DEFAULT NULL,
  `formatted_address` varchar(1000) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'no-profile.png',
  `online` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (3,'Muhammad','Ashfaq','','leoash842@hotmail.com','3147574731','7c4a8d09ca3762af61e59520943dc26494f8941b','male',1,'US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','94b122b45ac3acf525224b3854088f70','San Francisco, CA, USA','6b02ab4d740a8c8ab7cdc78cfbf0e314.jpg',0),(5,'Ash','Makk','','makk4@hotmail.co.uk','3147574731','7c4a8d09ca3762af61e59520943dc26494f8941b','male',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'d415084cea3dc3618907dcaabb9d3503.jpg',0),(6,'Deb','Jones','','deb17276@yahoo.com','987654321','dd2a5114694b5ab373a380ab846f0d18774a24ab','female',1,'US','United States','Madison','Madison','AL','Alabama','Madison Co','Madison County','b02e1cf0f7de2a30e0a023ffb2879d3b','305 Intergraph Way, Madison, AL 35758, USA','529bbcd48177f8319ac5b68e45789bae.jpg',0),(7,'Deb2','Jones2','','deb17276@gmail.com','deb17276@gmail.com','dd2a5114694b5ab373a380ab846f0d18774a24ab','female',1,'US','United States','NY','New York','NY','New York',NULL,NULL,NULL,'New York, NY, USA','667f459dac800bf45c93d00384a494c6.jpg',0),(8,'M','A','','makk042@gmail.com','3147574731','7f11b9d2dde53996560e3714eea7314ac248b6d7','male',1,'US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County',NULL,'San Francisco, CA, USA','53a51101448ae9c9d58f49e6fa069224.jpg',0);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_comments`
--

DROP TABLE IF EXISTS `ad_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author` bigint(20) unsigned NOT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `ad` varchar(100) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_comments`
--

LOCK TABLES `ad_comments` WRITE;
/*!40000 ALTER TABLE `ad_comments` DISABLE KEYS */;
INSERT INTO `ad_comments` VALUES (1,3,'Amazing car','i7r8ksyz','0000-00-00 00:00:00'),(2,6,'Test Comment for this ads','i89bo5k2','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `ad_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_fields`
--

DROP TABLE IF EXISTS `ad_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_fields` (
  `id` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) DEFAULT NULL,
  `value` varchar(300) DEFAULT NULL,
  `ad` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_fields`
--

LOCK TABLES `ad_fields` WRITE;
/*!40000 ALTER TABLE `ad_fields` DISABLE KEYS */;
INSERT INTO `ad_fields` VALUES ('i7jeypem','brand','0','i7jeypel'),('i7jeypen','body_type','0','i7jeypel'),('i7jeypeo','transmission','0','i7jeypel'),('i7jeypep','fuel_type','0','i7jeypel'),('i7wcwlmu','brand','Lamborghini','i7wcwlmt'),('i7wcwlmv','body_type','Sports','i7wcwlmt'),('i7wcwlmw','transmission','Automatic','i7wcwlmt'),('i7wcwlmx','fuel_type','Petrol','i7wcwlmt'),('i7wcwlmy','model_year','2015','i7wcwlmt'),('i7wcwlmz','engine_capacity','8000','i7wcwlmt'),('i7wcwln0','mileage','4000','i7wcwlmt'),('i8ajzffx','brand','Other Brand','i8ajzffw'),('i8ajzffy','body_type','4 Door Saloon','i8ajzffw'),('i8ajzffz','transmission','Automatic','i8ajzffw'),('i8ajzfg0','fuel_type','Other fuel type','i8ajzffw'),('i8ajzfg1','model_year','2015','i8ajzffw'),('i8ajzfg2','engine_capacity','electric','i8ajzffw'),('i8ajzfg3','mileage','1000','i8ajzffw'),('i8cotxfm','brand','Other Brand','i8cotxfl'),('i8cotxfn','body_type','0','i8cotxfl'),('i8cotxfo','transmission','Automatic','i8cotxfl'),('i8cotxfp','fuel_type','Petrol','i8cotxfl'),('i8cotxfq','model_year','2015','i8cotxfl'),('i8cotxfr','engine_capacity','4000','i8cotxfl'),('i8cotxfs','mileage','1000','i8cotxfl'),('i8cphfzk','address','1012 atherton st','i8cphfzj'),('i8cphfzl','size_unit','Sq Foot','i8cphfzj'),('i8gjp0e5','sub_category','Clothes & Shoes','i8gjp0e4');
/*!40000 ALTER TABLE `ad_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_pictures`
--

DROP TABLE IF EXISTS `ad_pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ad_pictures` (
  `id` varchar(100) NOT NULL DEFAULT '',
  `source` varchar(300) DEFAULT NULL,
  `type` varchar(300) DEFAULT NULL,
  `size` bigint(20) DEFAULT NULL,
  `ad` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_pictures`
--

LOCK TABLES `ad_pictures` WRITE;
/*!40000 ALTER TABLE `ad_pictures` DISABLE KEYS */;
INSERT INTO `ad_pictures` VALUES ('i7jeyper','17abe440cebed10fe2df6c56f683311a.jpg','image/jpeg',3414,'i7jeypel'),('i7wcwln2','044d384e6d7d3d27873d2388462598f0.jpg','image/jpeg',88021,'i7wcwlmt'),('i89bo5k3','a2054ef28c454ce7aa02be6a4bd4fcd4.jpg','image/jpeg',11546,'i89bo5k2'),('i8ajzfga','0513454166b755209ddbdd2ee1c46f24.jpg','image/jpeg',114458,'i8ajzffw'),('i8ajzfgb','7a686081c0c8c51b050a5a49b807cecf.jpg','image/jpeg',102316,'i8ajzffw'),('i8cotxfu','db226b5477b610f6cb467d226e7928f6.jpg','image/jpeg',116887,'i8cotxfl'),('i8cotxfv','59fae2bb770735df02121a722ebc562f.jpg','image/jpeg',7974,'i8cotxfl'),('i8cphfzm','2bc949fc4888de17557d3a3c3bf5d62a.jpg','image/jpeg',408520,'i8cphfzj'),('i8cphfzn','8c493a2ea9a5192279cf672252bf0c69.jpg','image/jpeg',112162,'i8cphfzj'),('i8gjp0e6','a73ad0a349db3ab050148295c3e318e4.jpg','image/jpeg',289180,'i8gjp0e4');
/*!40000 ALTER TABLE `ad_pictures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `id` varchar(100) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `trade_type` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(18,2) DEFAULT NULL,
  `formatted_price` varchar(50) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `currency_symbol` varchar(10) DEFAULT NULL,
  `route_short` varchar(10) DEFAULT NULL,
  `route_long` varchar(100) DEFAULT NULL,
  `postal_code_short` varchar(10) DEFAULT NULL,
  `postal_code_long` varchar(100) DEFAULT NULL,
  `neighborhood_short` varchar(10) DEFAULT NULL,
  `neighborhood_long` varchar(100) DEFAULT NULL,
  `street_number_short` varchar(10) DEFAULT NULL,
  `street_number_long` varchar(100) DEFAULT NULL,
  `country_short` varchar(10) DEFAULT NULL,
  `country_long` varchar(100) DEFAULT NULL,
  `locality_short` varchar(10) DEFAULT NULL,
  `locality_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_1_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_1_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_2_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_2_long` varchar(100) DEFAULT NULL,
  `formatted_address` varchar(300) DEFAULT NULL,
  `latitude` decimal(18,16) NOT NULL,
  `longitude` decimal(18,16) NOT NULL,
  `show_vbox` int(4) DEFAULT NULL,
  `show_email` int(4) DEFAULT NULL,
  `show_contact_form` int(4) DEFAULT NULL,
  `show_phone` int(4) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seller_type` int(4) DEFAULT NULL,
  `seller` bigint(20) DEFAULT NULL,
  `agent` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
INSERT INTO `ads` VALUES ('i7wcwlmt','Lamborghini Aventador 2015 For Sale ','lamborghini_aventador_2015_for_sale','Lamborghini Aventador 2015 For Sale ','1','1',400000.00,'$400,000.00','USD','$','Post St','Post St','94109','94109','Western Ad','Western Addition','1400','1400','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','1400 Post St, San Francisco, CA 94109, USA',37.7862281000000000,-99.9999999999999999,1,1,1,1,'0000-00-00 00:00:00',1,3,3),('i8ajzffw','Tesla 2015','tesla_2015','Tesla for sale','1','1',80000.00,'$80,000.00','USD','$','Market St','Market St','94102','94102','Civic Cent','Civic Center','1200','1200','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','1200 Market St, San Francisco, CA 94102, USA',37.7786430000000000,-99.9999999999999999,1,1,1,1,'0000-00-00 00:00:00',0,5,0),('i8cotxfl','Mustang for sale','mustang_for_sale','2015 Mustang for sale','1','1',35000.00,'$35,000.00','USD','$','El Camino ','El Camino Real','94109','94109','Polk Gulch','Polk Gulch','1200','1200','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','1200 El Camino Real, San Francisco, CA 94109, USA',37.7866741000000000,-99.9999999999999999,1,1,1,1,'0000-00-00 00:00:00',0,3,0),('i8cphfzj','Beautiful house for sale','beautiful_house_for_sale','Beautiful house newly renovated for sale','1','8',2000000.00,'$2,000,000.00','USD','$','Atherton A','Atherton Ave','94027','94027','West Ather','West Atherton','100','100','US','United States','Atherton','Atherton','CA','California','San Mateo ','San Mateo County','100 Atherton Ave, Atherton, CA 94027, USA',37.4556697000000000,-99.9999999999999999,1,1,1,1,'0000-00-00 00:00:00',1,3,3),('i8gjp0e4','despicable me costumes','despicable_me_costumes','despicable me costumes for sale','1','23',25.00,'$25.00','USD','$','Henry Adam','Henry Adams St','94103','94103','South of M','South of Market',NULL,NULL,'US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','Henry Adams St, San Francisco, CA 94103, USA',37.7686049000000000,-99.9999999999999999,1,1,1,1,'0000-00-00 00:00:00',0,8,8);
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_employees`
--

DROP TABLE IF EXISTS `business_employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `confirmed` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `account` int(11) unsigned DEFAULT NULL,
  `business` bigint(20) unsigned DEFAULT NULL,
  `contact_number` varchar(50) NOT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `store` varchar(100) DEFAULT NULL,
  `session` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_employees`
--

LOCK TABLES `business_employees` WRITE;
/*!40000 ALTER TABLE `business_employees` DISABLE KEYS */;
INSERT INTO `business_employees` VALUES (2,1,0,3,2,'','leoash842@hotmail.com','0000-00-00 00:00:00',NULL,'i8hr6j7a'),(3,1,0,3,3,'','leoash842@hotmail.com','0000-00-00 00:00:00',NULL,'i8hr6s1l'),(4,1,0,3,4,'','leoash842@hotmail.com','0000-00-00 00:00:00','6','i94i7ssd'),(11,1,0,6,5,'','deb17276@yahoo.com','0000-00-00 00:00:00','4','i8mh6qfr'),(22,1,1,0,5,'','deb17276@gmail.com','0000-00-00 00:00:00',NULL,''),(25,1,1,0,4,'','makk4@hotmail.co.uk','0000-00-00 00:00:00',NULL,'i8ives6u'),(26,1,1,0,4,'','makk042@gmail.com','0000-00-00 00:00:00',NULL,'');
/*!40000 ALTER TABLE `business_employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `businesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `avatar` varchar(1000) DEFAULT NULL,
  `route_short` varchar(10) DEFAULT NULL,
  `route_long` varchar(100) DEFAULT NULL,
  `postal_code_short` varchar(10) DEFAULT NULL,
  `postal_code_long` varchar(100) DEFAULT NULL,
  `neighborhood_short` varchar(10) DEFAULT NULL,
  `neighborhood_long` varchar(100) DEFAULT NULL,
  `street_number_short` varchar(10) DEFAULT NULL,
  `street_number_long` varchar(100) DEFAULT NULL,
  `country_short` varchar(10) DEFAULT NULL,
  `country_long` varchar(100) DEFAULT NULL,
  `locality_short` varchar(10) DEFAULT NULL,
  `locality_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_1_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_1_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_2_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_2_long` varchar(100) DEFAULT NULL,
  `formatted_address` varchar(1000) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(10,8) NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES (2,'google','google','3147574731','63010001e62006c8180ea5e113d65509.jpg','Amphitheat','Amphitheatre Pkwy','94043','94043',NULL,NULL,NULL,'1600','US','United States','Mountain V','Mountain View','CA','California','Santa Clar','Santa Clara County','1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA',37.42247640,-99.99999999,'0000-00-00 00:00:00','google','info@google.com','www.google.com','17'),(3,'google','google2','3147574731','84998ab0823ed5b11b6e1142f7df59ce.jpg','Amphitheat','Amphitheatre Pkwy','94043','94043',NULL,NULL,NULL,'1600','US','United States','Mountain V','Mountain View','CA','California','Santa Clar','Santa Clara County',NULL,37.42247640,-99.99999999,'0000-00-00 00:00:00','google','info@google.com','www.google.com',NULL),(4,'VBOX, Inc.','vbox','3147574731','ed4219f78f9c79a7bcd6dcef65a490ea.jpg','Polk St','Polk St','94109','94109','Polk Gulch','Polk Gulch','Polk Gulch','1424','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','1424 Polk St, San Francisco, CA 94109, USA',37.79008340,-99.99999999,'0000-00-00 00:00:00','Startup','info@livevbox.com','livevbox.com','17'),(5,'Debdesk','debdesk','987645321','f26257eae95c135d26ef52b0be3a1698.jpg','Post St','Post St','94109','94109','Lower Nob ','Lower Nob Hill','Lower Nob ','711','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','711 Post St, San Francisco, CA 94109, USA',37.78735680,-99.99999999,'0000-00-00 00:00:00','Web design and Web development','webmaster@debdesk.com','http://debdesk.com','48');
/*!40000 ALTER TABLE `businesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `follows`
--

DROP TABLE IF EXISTS `follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `follows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `follower_type` varchar(100) NOT NULL,
  `follower` varchar(400) DEFAULT NULL,
  `following_type` varchar(100) NOT NULL,
  `following` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follows`
--

LOCK TABLES `follows` WRITE;
/*!40000 ALTER TABLE `follows` DISABLE KEYS */;
INSERT INTO `follows` VALUES (2,'personal','4','business','4'),(3,'personal',NULL,'business','4'),(4,'personal',NULL,'business','4'),(5,'personal',NULL,'business','4'),(6,'personal',NULL,'business','4'),(7,'personal',NULL,'business','4'),(8,'personal',NULL,'business','4'),(9,'personal',NULL,'business','4'),(11,'personal',NULL,'business','4'),(12,'personal',NULL,'business','4'),(13,'personal',NULL,'business','4'),(14,'personal',NULL,'business','4'),(15,'personal',NULL,'business','4'),(16,'personal',NULL,'business','4'),(17,'personal',NULL,'business','4'),(18,'personal',NULL,'business','4'),(19,'personal',NULL,'business','4'),(20,'personal',NULL,'business','4');
/*!40000 ALTER TABLE `follows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner` bigint(20) unsigned NOT NULL,
  `author` bigint(20) unsigned NOT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,3,3,'This is a new business we are launching.','0000-00-00 00:00:00'),(6,5,6,'test wall post','0000-00-00 00:00:00'),(7,6,6,'test wall post','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `business` bigint(20) DEFAULT NULL,
  `route_short` varchar(10) DEFAULT NULL,
  `route_long` varchar(100) DEFAULT NULL,
  `postal_code_short` varchar(10) DEFAULT NULL,
  `postal_code_long` varchar(100) DEFAULT NULL,
  `neighborhood_short` varchar(10) DEFAULT NULL,
  `neighborhood_long` varchar(100) DEFAULT NULL,
  `street_number_short` varchar(10) DEFAULT NULL,
  `street_number_long` varchar(100) DEFAULT NULL,
  `country_short` varchar(100) NOT NULL,
  `country_long` varchar(100) DEFAULT NULL,
  `locality_short` varchar(10) DEFAULT NULL,
  `locality_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_1_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_1_long` varchar(100) DEFAULT NULL,
  `administrative_area_level_2_short` varchar(10) DEFAULT NULL,
  `administrative_area_level_2_long` varchar(100) DEFAULT NULL,
  `formatted_address` varchar(300) DEFAULT NULL,
  `latitude` decimal(18,16) NOT NULL,
  `longitude` decimal(18,16) NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` VALUES (2,'Store 2',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,12.3400000000000000,12.3500000000000000,'2015-03-21 22:11:01'),(3,'Store3',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,12.3400000000000000,12.3500000000000000,'2015-03-21 22:11:01'),(4,'Store 1',5,'Geary St','Geary St','94102','94102','Lower Nob ','Lower Nob Hill','580','580','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','580 Geary St, San Francisco, CA 94102, USA',37.7869422000000000,-99.9999999999999999,'0000-00-00 00:00:00'),(5,'post street',4,'Post St','Post St','94109','94109','Western Ad','Western Addition','1400','1400','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','1400 Post St, San Francisco, CA 94109, USA',37.7862281000000000,-99.9999999999999999,'0000-00-00 00:00:00'),(6,'Market street',4,'Market St','Market St','94114','94114','The Castro','The Castro','2500','2500','US','United States','SF','San Francisco','CA','California','San Franci','San Francisco County','2500 Market St, San Francisco, CA 94114, USA',37.7622425000000000,-99.9999999999999999,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-04-30 15:06:05
