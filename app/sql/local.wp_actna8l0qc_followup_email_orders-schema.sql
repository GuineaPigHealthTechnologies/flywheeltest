/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_email_orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `email_id` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `send_on` bigint(20) NOT NULL,
  `is_cart` int(1) NOT NULL DEFAULT '0',
  `is_sent` int(1) NOT NULL DEFAULT '0',
  `date_sent` datetime NOT NULL,
  `email_trigger` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `meta` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_email` (`user_email`(191)),
  KEY `order_id` (`order_id`),
  KEY `is_sent` (`is_sent`),
  KEY `date_sent` (`date_sent`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
