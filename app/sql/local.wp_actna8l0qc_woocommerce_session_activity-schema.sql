/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_woocommerce_session_activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(32) NOT NULL,
  `activity_type` varchar(255) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `order_id` varchar(45) NOT NULL DEFAULT '0',
  `user_id` varchar(45) NOT NULL DEFAULT '0',
  `activity_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=latin1;
