/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `total_purchase_price` double(10,2) NOT NULL DEFAULT '0.00',
  `total_orders` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `email_address` (`email_address`(191)),
  KEY `total_purchase_price` (`total_purchase_price`),
  KEY `total_orders` (`total_orders`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
