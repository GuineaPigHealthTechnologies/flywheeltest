/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_followup_coupons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `coupon_name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `coupon_type` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `coupon_prefix` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `amount` double(12,2) NOT NULL DEFAULT '0.00',
  `individual` int(1) NOT NULL DEFAULT '0',
  `exclude_sale_items` int(1) NOT NULL DEFAULT '0',
  `before_tax` int(1) NOT NULL DEFAULT '0',
  `free_shipping` int(1) NOT NULL DEFAULT '0',
  `usage_count` bigint(20) NOT NULL DEFAULT '0',
  `expiry_value` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',
  `expiry_type` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `product_ids` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `exclude_product_ids` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `product_categories` text COLLATE utf8mb4_unicode_520_ci,
  `exclude_product_categories` text COLLATE utf8mb4_unicode_520_ci,
  `minimum_amount` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `maximum_amount` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `usage_limit` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `usage_limit_per_user` varchar(3) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `coupon_name` (`coupon_name`),
  KEY `usage_count` (`usage_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
