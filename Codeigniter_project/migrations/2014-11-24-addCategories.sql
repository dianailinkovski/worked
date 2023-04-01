--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `marketplaceCount` int(11) default NULL,
  `parentid` int(11) NOT NULL default '-1',
  `level` tinyint(2) NOT NULL default '0',
  `weight` float NOT NULL default '1',
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=latin1 COMMENT='';
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `categories`
--

/*!40000 ALTER TABLE `categories` DISABLE KEYS */;

INSERT INTO `categories` VALUES 
(1,'Miscellaneous',1,-1,0,1),
(15,'Cars',149042,-1,0,1),
(40,'Art Supplies',0,20,1,1),
(19,'Babies & Kids',60572,-1,0,1),
(20,'Art & Art Supplies',29,-1,0,1),
(21,'Beauty',66,-1,0,1),
(22,'Books & Magazines',39152,-1,0,1),
(23,'Clothing',89,-1,0,1),
(24,'Food',3400,-1,0,1),
(25,'Computers & Software',0,-1,0,1),
(26,'Home & Bath',24656,-1,0,1),
(27,'Health & Fitness',0,-1,0,1),
(41,'Cosmetics',0,21,1,1),
(29,'Jewelry',38095,-1,0,1),
(30,'Gifts',4517,-1,0,1),
(31,'Movies',13,-1,0,1),
(32,'Music',0,-1,0,1),
(33,'Office Supplies',0,-1,0,1),
(34,'Pet Supplies',48176,-1,0,1),
(35,'Sports & Outdoors',0,-1,0,1),
(36,'Toys & Games',126160,-1,0,1),
(37,'Travel',0,-1,0,1),
(38,'Video Games',0,126,1,1),
(42,'Skin Care',0,21,1,1),
(43,'Hair Care',0,21,1,1),
(44,'Auto Parts',77714,15,1,1),
(45,'Car Rental',0,15,1,1),
(46,'Car Sales',0,15,1,1),
(47,'Men\'s',0,23,1,1),
(48,'Women\'s',6,23,1,1),
(54,'Shoes',49,23,1,1),
(50,'GPS Devices',0,126,2,1),
(51,'Computer Hardware',0,25,1,1),
(52,'Computer Software',0,25,1,1),
(61,'Eyewear',45062,-1,0,1),
(55,'Belts',0,23,1,1),
(56,'Wallets',0,23,1,1),
(57,'Handbags',0,23,1,1),
(58,'Cell Phones',0,126,1,1),
(59,'Collectibles',0,-1,0,1),
(60,'Cooking',0,-1,0,1),
(64,'Consoles',0,38,1,1),
(65,'Athletic',0,47,1,1),
(66,'Shorts',0,65,1,1),
(67,'Accessories',0,38,1,0.1),
(68,'Games',0,38,1,1),
(69,'Men\'s',0,54,1,1),
(70,'Women\'s',0,54,1,1),
(71,'Kids\'',0,54,1,1),
(72,'Men\'s Casual',0,69,1,1),
(73,'Men\'s Dress',0,69,1,1),
(116,'Camcorders',0,114,1,1),
(115,'Digital Cameras',0,114,1,1),
(114,'Cameras',0,126,1,1),
(77,'Men\'s Athletic',0,69,1,1),
(78,'Boys',0,71,1,1),
(79,'Girls',0,71,1,1),
(81,'Infants',0,54,1,1),
(82,'Clearance',0,-1,0,1),
(83,'Action',0,31,1,1),
(84,'Sports',0,31,1,1),
(85,'Family',13,31,1,1),
(86,'Comedy',0,31,1,1),
(87,'Western',0,31,1,1),
(88,'Drama',0,31,1,1),
(89,'Television',0,31,1,1),
(90,'Fitness',0,31,1,1),
(91,'Sci-Fi',0,31,1,1),
(92,'Horror',0,31,1,1),
(93,'Documentary',0,31,1,1),
(94,'Anime',0,31,1,1),
(95,'Musicals',0,31,1,1),
(96,'Mature',0,31,1,1),
(97,'Foreign',0,31,1,1),
(98,'Horror',0,31,1,1),
(99,'Romance',0,31,1,1),
(100,'Soundtracks',0,31,1,1),
(101,'Men\'s',0,55,1,1),
(102,'Women\'s',0,55,1,1),
(103,'New',0,-1,0,1),
(104,'CD Players',0,107,1,1),
(105,'DVD Players & Recorders',0,126,1,1),
(106,'Telephones',0,126,1,1),
(107,'Audio',15,126,1,1),
(108,'Computer Accessories',0,25,1,0.1),
(109,'Printers',0,25,1,1),
(110,'TVs',0,126,1,1),
(111,'HDTVs',0,110,1,1),
(112,'Women\'s Shoes',0,70,1,1),
(113,'Women\'s Boots',0,70,1,1),
(117,'Film Cameras',0,114,1,1),
(118,'Camera Accessories',0,114,1,1),
(119,'Car Audio & Video',0,107,2,1),
(120,'MP3 Players',15,107,1,1),
(121,'Stereos',0,107,1,1),
(122,'Personal Radios',0,107,1,1),
(123,'Clock Radios',0,107,1,1),
(124,'Headphones',0,107,1,1),
(125,'Electronics Accessories',0,126,1,0.1),
(126,'Electronics',0,-1,0,1),
(130,'Boy\'s Clothing',1,23,1,1),
(131,'Girl\'s Clothing',0,23,1,1),
(133,'Party Supplies',19,-1,0,1),
(134,'Kids',0,31,2,1),
(135,'Video Games',47,-1,0,1);
*/
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;


--
-- Table structure for table `categories_marketplaces`
--
 
DROP TABLE IF EXISTS `categories_marketplaces`;
CREATE TABLE `categories_marketplaces` (
  `mId` int(10) default NULL,
  `cId` int(10) default NULL,
  UNIQUE KEY `pId` (`pId`,`cId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='each marketplace can be in more than one category'
 

--
-- Table structure for table `categories_stores`
--
 
DROP TABLE IF EXISTS `categories_stores`;
CREATE TABLE `categories_stores` (
  `sId` int(10) default NULL,
  `cId` int(10) default NULL,
  UNIQUE KEY `sId` (`sId`,`cId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='each store can be in more than one category'
 