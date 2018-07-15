/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wc_points_rewards_user_points` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `points` bigint(20) NOT NULL,
  `points_balance` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wc_points_rewards_user_points_user_id_points_balance` (`user_id`,`points_balance`),
  KEY `idx_wc_points_rewards_user_points_date_points_balance` (`date`,`points_balance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
