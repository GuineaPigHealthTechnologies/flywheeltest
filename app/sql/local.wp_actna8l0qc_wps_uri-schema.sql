/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wps_uri` (
  `uri_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(2048) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`uri_id`),
  KEY `uri` (`uri`(64))
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
