/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_et_bloom_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `record_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `record_type` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `optin_id` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `list_id` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `page_id` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `removed_flag` tinyint(1) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
