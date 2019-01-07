/*
SQLyog Ultimate v11.33 (64 bit)
MySQL - 5.6.24 : Database - shopifyshipping
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`shopifyshipping` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `shopifyshipping`;

/*Table structure for table `fullfilment_api_response` */

DROP TABLE IF EXISTS `fullfilment_api_response`;

CREATE TABLE `fullfilment_api_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL,
  `response` text,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `fullfilment_api_response` */

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address1` text,
  `address2` text,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `province_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `weight` double(10,4) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `total_price` double(10,4) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_sync` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `orders` */

/*Table structure for table `product_order` */

DROP TABLE IF EXISTS `product_order`;

CREATE TABLE `product_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `product_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `long` varchar(255) DEFAULT NULL,
  `wide` varchar(255) DEFAULT NULL,
  `high` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_sync` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `product_order` */

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `country_text` varchar(255) DEFAULT NULL,
  `banner_image` varchar(100) DEFAULT NULL,
  `reward_image_1` varchar(100) DEFAULT NULL,
  `reward_image_2` varchar(100) DEFAULT NULL,
  `reward_image_3` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `settings` */

insert  into `settings`(`id`,`country_text`,`banner_image`,`reward_image_1`,`reward_image_2`,`reward_image_3`) values (1,'This App is not available in your Country',NULL,NULL,NULL,NULL);

/*Table structure for table `shipments` */

DROP TABLE IF EXISTS `shipments`;

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `shipment_number` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'new',
  `request_create` text,
  `response_create` text,
  `create_code` varchar(255) DEFAULT NULL,
  `create_status` varchar(255) DEFAULT 'success',
  `response_info` text,
  `shipment_code` varchar(255) DEFAULT NULL,
  `response_assign` text,
  `assign_code` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `shipments` */

/*Table structure for table `shipping_address_order` */

DROP TABLE IF EXISTS `shipping_address_order`;

CREATE TABLE `shipping_address_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address1` text,
  `address2` text,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `province_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `updated_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `shipping_address_order` */

/*Table structure for table `shipping_rate` */

DROP TABLE IF EXISTS `shipping_rate`;

CREATE TABLE `shipping_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_city` varchar(255) DEFAULT NULL,
  `to_city` varchar(255) DEFAULT NULL,
  `standard_price` decimal(10,2) DEFAULT '0.00',
  `priority_price` decimal(10,2) DEFAULT '0.00',
  `express_price` decimal(10,2) DEFAULT '0.00',
  `weight` decimal(10,2) DEFAULT '0.00',
  `weight_unit` enum('gram','kilogram') DEFAULT 'gram',
  `shipping_name` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `shipping_rate` */

/*Table structure for table `stores` */

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `access_token` text,
  `is_singapore_store` int(11) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address1` text,
  `address2` text,
  `province` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `markup` double(10,4) DEFAULT NULL,
  `charge_id` varchar(255) DEFAULT NULL,
  `charge_name` varchar(255) DEFAULT NULL,
  `last_sync` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `stores` */

/*Table structure for table `stores_address` */

DROP TABLE IF EXISTS `stores_address`;

CREATE TABLE `stores_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address1` text,
  `province` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `stores_address` */

/*Table structure for table `t_menu` */

DROP TABLE IF EXISTS `t_menu`;

CREATE TABLE `t_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `fa_icon` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `parent` tinyint(4) DEFAULT '1',
  `status` enum('published','draft') DEFAULT 'published',
  `date_created` datetime NOT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `t_menu` */

insert  into `t_menu`(`id`,`name`,`fa_icon`,`link`,`parent`,`status`,`date_created`,`deleted_by`,`date_deleted`,`is_deleted`) values (1,'Manage Admin','fa-user-plus','user',1,'published','0000-00-00 00:00:00',NULL,NULL,'n'),(2,'Manage Merchant','fa-print','merchant',1,'published','0000-00-00 00:00:00',NULL,NULL,'n'),(3,'Manage Shipping Rate','fa-file-text-o','shipping_rate',1,'published','0000-00-00 00:00:00',NULL,NULL,'n'),(4,'Shipment List','fa-th-list','shipments',1,'published','0000-00-00 00:00:00',NULL,NULL,'n'),(5,'Users','fa-user-plus','users',1,'published','0000-00-00 00:00:00',NULL,NULL,'n'),(6,'User Group','fa-users','user_group',1,'published','0000-00-00 00:00:00',NULL,NULL,'n');

/*Table structure for table `t_user` */

DROP TABLE IF EXISTS `t_user`;

CREATE TABLE `t_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `image` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_group_id` int(3) NOT NULL,
  `params` text,
  `token` varchar(225) DEFAULT NULL,
  `token_type` varchar(100) DEFAULT NULL,
  `status` enum('active','disabled','banned') NOT NULL DEFAULT 'active',
  `publish` enum('yes','no') DEFAULT 'yes',
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `t_user_id` (`id`),
  KEY `status` (`status`),
  KEY `delete` (`date_deleted`),
  KEY `get_by_id` (`id`,`status`,`date_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `t_user` */

insert  into `t_user`(`id`,`fullname`,`email`,`image`,`password`,`user_group_id`,`params`,`token`,`token_type`,`status`,`publish`,`created_by`,`date_created`,`modified_by`,`date_modified`,`deleted_by`,`date_deleted`,`is_deleted`) values (1,'Admin','admin@shopifyshipping.com',NULL,'c287ac5aa92e43841a9054781da82d2eb9943810',2,NULL,NULL,NULL,'active','yes',NULL,'0000-00-00 00:00:00',NULL,'2018-06-25 15:39:32',NULL,NULL,'n');

/*Table structure for table `t_user_group` */

DROP TABLE IF EXISTS `t_user_group`;

CREATE TABLE `t_user_group` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `privilege` varchar(150) CHARACTER SET latin1 NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_by` int(11) DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `is_deleted` enum('y','n') CHARACTER SET latin1 NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `t_user_group_id` (`id`),
  KEY `delete` (`date_deleted`),
  KEY `get_by_id` (`id`,`date_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `t_user_group` */

insert  into `t_user_group`(`id`,`name`,`privilege`,`created_by`,`date_created`,`modified_by`,`date_modified`,`deleted_by`,`date_deleted`,`is_deleted`) values (1,'Master Admin','1,2,3,4,5,6',NULL,'0000-00-00 00:00:00',NULL,'2018-06-25 14:44:09',NULL,NULL,'n'),(2,'Admin','1,2,3,4',NULL,'0000-00-00 00:00:00',NULL,'2018-06-25 14:44:26',NULL,NULL,'n');

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop` varchar(255) DEFAULT NULL,
  `charge_id` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `billing` varchar(255) DEFAULT NULL,
  `description` text,
  `price` double(10,2) DEFAULT NULL,
  `balance_used` double(10,2) DEFAULT NULL,
  `balance_remaining` double(10,2) DEFAULT NULL,
  `confirmation_url` varchar(255) DEFAULT NULL,
  `capped_amount` double(10,2) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `activated_on` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT '0000-00-00 00:00:00',
  `last_sync` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `transactions` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
