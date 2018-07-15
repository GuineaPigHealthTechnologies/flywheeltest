/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_advanced_notifications` (
  `notification_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `recipient_name` longtext COLLATE utf8mb4_unicode_520_ci,
  `recipient_email` longtext COLLATE utf8mb4_unicode_520_ci,
  `recipient_address` longtext COLLATE utf8mb4_unicode_520_ci,
  `recipient_phone` varchar(240) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `recipient_website` varchar(240) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `notification_type` varchar(240) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `notification_plain_text` int(1) NOT NULL,
  `notification_totals` int(1) NOT NULL,
  `notification_prices` int(1) NOT NULL,
  `notification_sent_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
