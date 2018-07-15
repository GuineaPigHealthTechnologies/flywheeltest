/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wps_object_type` (
  `object_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_type` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `context` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `context_table` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `context_column` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `context_key` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`object_type_id`),
  KEY `object_type` (`object_type`(10)),
  KEY `context` (`context`(10)),
  KEY `context_table` (`context_table`(10)),
  KEY `context_column` (`context_column`(10)),
  KEY `context_key` (`context_key`(10))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
