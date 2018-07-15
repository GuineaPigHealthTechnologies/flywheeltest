/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_email_tracking` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email_order_id` bigint(20) NOT NULL DEFAULT '0',
  `email_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_email` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `target_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `client_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `client_version` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `client_type` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_ip` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_country` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`),
  KEY `user_id` (`user_id`),
  KEY `user_email` (`user_email`(191)),
  KEY `date_added` (`date_added`),
  KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
