/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wps_query` (
  `query_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `query` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`query_id`),
  KEY `query` (`query`(20))
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
