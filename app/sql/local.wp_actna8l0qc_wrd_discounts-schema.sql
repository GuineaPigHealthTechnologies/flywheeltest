/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wrd_discounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(25) NOT NULL,
  `amount` double(12,2) NOT NULL DEFAULT '0.00',
  `individual` int(1) NOT NULL DEFAULT '0',
  `free_shipping` int(1) NOT NULL DEFAULT '0',
  `send_mode` varchar(25) NOT NULL,
  `send_to_verified` int(1) NOT NULL DEFAULT '0',
  `product_ids` text NOT NULL,
  `category_ids` text NOT NULL,
  `usage_limit` int(1) NOT NULL DEFAULT '0',
  `expiry_value` int(3) NOT NULL DEFAULT '0',
  `expiry_type` varchar(25) NOT NULL DEFAULT '',
  `usage_count` bigint(20) NOT NULL DEFAULT '0',
  `exclude_sale_items` varchar(20) NOT NULL DEFAULT 'no',
  `all_products` varchar(20) NOT NULL DEFAULT 'no',
  `unique_email` int(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
