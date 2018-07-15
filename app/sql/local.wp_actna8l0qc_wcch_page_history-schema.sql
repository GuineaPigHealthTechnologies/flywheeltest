/*!40101 SET NAMES binary*/;
/*!40014 SET FOREIGN_KEY_CHECKS=0*/;

CREATE TABLE `wp_actna8l0qc_wcch_page_history` (
  `user_hash` varchar(23) NOT NULL,
  `page_history` longtext,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
