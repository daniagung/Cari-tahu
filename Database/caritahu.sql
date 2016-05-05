/*
SQLyog Trial v12.2.1 (64 bit)
MySQL - 5.5.45-log : Database - caritahu
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`caritahu` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `caritahu`;

/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) COLLATE utf8_romanian_ci NOT NULL,
  `password` varchar(256) COLLATE utf8_romanian_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_romanian_ci;

/*Data for the table `admin` */

insert  into `admin`(`id`,`username`,`password`) values 
(1,'admin','dde424ea40cc8e2599259d5315f40138');

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `title` varchar(64) NOT NULL,
  `app` varchar(256) NOT NULL,
  `ad1` varchar(2048) NOT NULL,
  `ad2` varchar(2048) NOT NULL,
  `ad3` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `settings` */

insert  into `settings`(`title`,`app`,`ad1`,`ad2`,`ad3`) values 
('Tel-U Search','qnJUV50QDVatJzvybgW6nrk6PzrgutTVeYgQE013BDQ','<a href=\"?a=web&q=pricop+alexandru\">Pricop Alexandru</a> • <a href=\"?a=web&q=phpDolphin\">phpDolphin - Social Network Platform</a> • <a href=\"?a=web&q=phpSound\">phpSound - Music Sharing Platform</a> • <a href=\"?a=web&q=codecanyon\">CodeCanyon</a>','That you can write what ever you want here, even html <strong>bolded</strong>, or <i>italic</i> if you like it more... :)','<a href=\"?a=images&q=Transfagarasan\">Transfagarasan (images)</a> • <a href=\"?a=videos&q=funny+dogs\">Funny Dogs (video)</a> • <a href=\"?a=videos&q=sky+diving\">Sky Diving (video)</a> • <a href=\"?a=videos&q=Birth+of+a+Black+Hole\">Birth of a Black Hole (video)</a>');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
