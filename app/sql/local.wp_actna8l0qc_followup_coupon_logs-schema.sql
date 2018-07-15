/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_coupon_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint(20) NOT NULL,
  `coupon_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coupon_code` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coupon_used` int(1) NOT NULL DEFAULT '0',
  `date_sent` datetime NOT NULL,
  `date_used` datetime NOT NULL,
  `email_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_id` (`coupon_id`),
  KEY `date_sent` (`date_sent`),
  KEY `email_id` (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
