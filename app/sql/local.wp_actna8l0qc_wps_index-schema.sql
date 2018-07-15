/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wps_index` (
  `index_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key_id` bigint(20) unsigned NOT NULL,
  `object_id` bigint(20) unsigned NOT NULL,
  `object_type_id` bigint(20) unsigned NOT NULL,
  `count` int(10) unsigned DEFAULT '1',
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`index_id`),
  KEY `key_id` (`key_id`),
  KEY `object_id` (`object_id`),
  KEY `object_type_id` (`object_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8694 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
