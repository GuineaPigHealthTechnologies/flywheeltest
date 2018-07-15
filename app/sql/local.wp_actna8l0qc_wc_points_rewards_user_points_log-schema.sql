/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wc_points_rewards_user_points_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `points` bigint(20) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_points_id` bigint(20) DEFAULT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `admin_user_id` bigint(20) DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_520_ci,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wc_points_rewards_user_points_log_date` (`date`),
  KEY `idx_wc_points_rewards_user_points_log_type` (`type`(191)),
  KEY `idx_wc_points_rewards_user_points_log_points` (`points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
