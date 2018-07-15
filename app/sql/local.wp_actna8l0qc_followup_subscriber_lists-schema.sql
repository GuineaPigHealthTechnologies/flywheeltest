/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_subscriber_lists` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `list_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `access` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `list_name` (`list_name`),
  KEY `access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
