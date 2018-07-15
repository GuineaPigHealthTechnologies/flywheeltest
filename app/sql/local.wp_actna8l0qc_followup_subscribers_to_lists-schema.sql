/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_subscribers_to_lists` (
  `subscriber_id` bigint(20) NOT NULL,
  `list_id` bigint(20) NOT NULL,
  KEY `subscriber_id` (`subscriber_id`),
  KEY `list_id` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
