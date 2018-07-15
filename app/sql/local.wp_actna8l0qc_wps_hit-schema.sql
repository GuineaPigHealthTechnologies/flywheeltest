/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wps_hit` (
  `hit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  `src_uri_id` bigint(20) unsigned DEFAULT NULL,
  `dest_uri_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `query_id` bigint(20) unsigned DEFAULT NULL,
  `user_agent_id` bigint(20) unsigned DEFAULT NULL,
  `count` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`hit_id`),
  KEY `date` (`date`),
  KEY `datetime` (`datetime`),
  KEY `ip` (`ip`),
  KEY `src` (`src_uri_id`),
  KEY `dest` (`dest_uri_id`),
  KEY `user` (`user_id`),
  KEY `query` (`query_id`),
  KEY `ua` (`user_agent_id`),
  KEY `count` (`count`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
