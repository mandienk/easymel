# Tables for example-basic.php
CREATE DATABASE /*!32312 IF NOT EXISTS*/`test` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `test`;

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT 'guest',
  `description` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `roles` */

insert  into `roles`(`role_id`,`name`,`description`) values (1,'guest','guest'),(2,'user','users with restricted access rights'),(3,'superuser','user with special access rights'),(4,'admin','administrator');

/*Table structure for table `sex` */

DROP TABLE IF EXISTS `sex`;

CREATE TABLE `sex` (
  `sex_id` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(6) NOT NULL DEFAULT 'male',
  PRIMARY KEY (`sex_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `sex` */

insert  into `sex`(`sex_id`,`name`) values (1,'male'),(2,'female');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `sex` char(6) DEFAULT NULL COMMENT 'sex',
  `role` varchar(50) NOT NULL COMMENT 'role',
  PRIMARY KEY (`user_id`,`firstname`,`lastname`,`role`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`user_id`,`firstname`,`lastname`,`sex`,`role`) values (1,'John','Smith','male','guest');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
