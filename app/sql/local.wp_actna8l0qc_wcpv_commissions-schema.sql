/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wcpv_commissions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `order_item_id` bigint(20) NOT NULL,
  `order_date` datetime DEFAULT NULL,
  `vendor_id` bigint(20) NOT NULL,
  `vendor_name` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `variation_id` bigint(20) NOT NULL,
  `product_name` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `variation_attributes` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_amount` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_quantity` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_shipping_amount` longtext COLLATE utf8mb4_unicode_520_ci,
  `product_shipping_tax_amount` longtext COLLATE utf8mb4_unicode_520_ci,
  `product_tax_amount` longtext COLLATE utf8mb4_unicode_520_ci,
  `product_commission_amount` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `total_commission_amount` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `commission_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'unpaid',
  `paid_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
