/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_followup_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `followup_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `followup_id` (`followup_id`),
  KEY `user_id` (`user_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
