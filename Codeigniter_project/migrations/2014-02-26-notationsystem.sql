/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.25a : Database - svtest_new
*********************************************************************
*/
ALTER TABLE `store`   
  ADD COLUMN `note_enable` ENUM('0','1') DEFAULT '0';

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `crowl_merchant_notes` */

DROP TABLE IF EXISTS `crowl_merchant_notes`;

CREATE TABLE `crowl_merchant_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_name_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reporter_name` varchar(50) NOT NULL,
  `company` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_of_entry` varchar(50) NOT NULL,
  `entry` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `crowl_merchant_notes` */

insert  into `crowl_merchant_notes`(`id`,`merchant_name_id`,`user_id`,`reporter_name`,`company`,`date`,`type_of_entry`,`entry`) values (1,27,58,'Tester1','Company1','2014-03-01 02:45:25','Suspected Source','testing......');
insert  into `crowl_merchant_notes`(`id`,`merchant_name_id`,`user_id`,`reporter_name`,`company`,`date`,`type_of_entry`,`entry`) values (2,27,58,'Tester2','Company2','2014-03-01 02:45:56','New Information','This is good!!!');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
