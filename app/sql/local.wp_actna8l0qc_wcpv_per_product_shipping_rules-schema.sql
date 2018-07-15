/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wcpv_per_product_shipping_rules` (
  `rule_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `rule_country` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `rule_state` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `rule_postcode` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `rule_cost` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `rule_item_cost` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `rule_order` bigint(20) NOT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
