/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wrd_sent_coupons` (
  `comment_id` bigint(20) NOT NULL,
  `discount_id` bigint(20) NOT NULL,
  `coupon_id` bigint(20) NOT NULL,
  `author_email` varchar(255) NOT NULL,
  KEY `comment_id` (`comment_id`),
  KEY `discount_id` (`discount_id`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
