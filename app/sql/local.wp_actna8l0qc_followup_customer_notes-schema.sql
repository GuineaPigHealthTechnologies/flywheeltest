/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_customer_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `followup_customer_id` bigint(20) NOT NULL,
  `author_id` bigint(20) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_520_ci,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `followup_customer_id` (`followup_customer_id`),
  KEY `date_added` (`date_added`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
