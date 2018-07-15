/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_email_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_order_id` bigint(20) NOT NULL DEFAULT '0',
  `email_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `email_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_sent` datetime NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `email_trigger` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_name` (`email_name`),
  KEY `user_id` (`user_id`),
  KEY `date_sent` (`date_sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
