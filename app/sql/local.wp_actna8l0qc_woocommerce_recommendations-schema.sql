/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_woocommerce_recommendations` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `rkey` varchar(255) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `related_product_id` bigint(20) NOT NULL,
  `score` float NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=latin1;
