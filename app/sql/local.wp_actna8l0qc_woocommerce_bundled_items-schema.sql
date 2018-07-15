/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_woocommerce_bundled_items` (
  `bundled_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `bundle_id` bigint(20) unsigned NOT NULL,
  `menu_order` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`bundled_item_id`),
  KEY `product_id` (`product_id`),
  KEY `bundle_id` (`bundle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
