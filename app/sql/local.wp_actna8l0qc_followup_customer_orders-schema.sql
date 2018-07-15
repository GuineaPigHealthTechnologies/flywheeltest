/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_customer_orders` (
  `followup_customer_id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `price` double(10,2) NOT NULL,
  KEY `followup_customer_id` (`followup_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
