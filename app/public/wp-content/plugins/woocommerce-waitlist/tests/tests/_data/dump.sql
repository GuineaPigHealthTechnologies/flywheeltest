# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 192.168.75.100 (MySQL 5.6.34)
# Database: local
# Generation Time: 2018-05-31 21:12:19 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wp_commentmeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_commentmeta`;

CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_comments`;

CREATE TABLE `wp_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10)),
  KEY `woo_idx_comment_type` (`comment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_comments` WRITE;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;

INSERT INTO `wp_comments` (`comment_ID`, `comment_post_ID`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id`)
VALUES
	(1,1,'A WordPress Commenter','wapuu@wordpress.example','https://wordpress.org/','','2018-05-18 01:34:24','2018-05-18 01:34:24','Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com\">Gravatar</a>.',0,'1','','',0,0);

/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_links`;

CREATE TABLE `wp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_options`;

CREATE TABLE `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_options` WRITE;
/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;

INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`)
VALUES
	(1,'siteurl','http://waitlisttest.local','yes'),
	(2,'home','http://waitlisttest.local','yes'),
	(3,'blogname','waitlist-test','yes'),
	(4,'blogdescription','Just another WordPress site','yes'),
	(5,'users_can_register','0','yes'),
	(6,'admin_email','joey@pie.co.de','yes'),
	(7,'start_of_week','1','yes'),
	(8,'use_balanceTags','0','yes'),
	(9,'use_smilies','1','yes'),
	(10,'require_name_email','1','yes'),
	(11,'comments_notify','1','yes'),
	(12,'posts_per_rss','10','yes'),
	(13,'rss_use_excerpt','0','yes'),
	(14,'mailserver_url','mail.example.com','yes'),
	(15,'mailserver_login','login@example.com','yes'),
	(16,'mailserver_pass','password','yes'),
	(17,'mailserver_port','110','yes'),
	(18,'default_category','1','yes'),
	(19,'default_comment_status','open','yes'),
	(20,'default_ping_status','open','yes'),
	(21,'default_pingback_flag','1','yes'),
	(22,'posts_per_page','10','yes'),
	(23,'date_format','F j, Y','yes'),
	(24,'time_format','g:i a','yes'),
	(25,'links_updated_date_format','F j, Y g:i a','yes'),
	(26,'comment_moderation','0','yes'),
	(27,'moderation_notify','1','yes'),
	(28,'permalink_structure','/%postname%/','yes'),
	(29,'rewrite_rules','a:156:{s:24:\"^wc-auth/v([1]{1})/(.*)?\";s:63:\"index.php?wc-auth-version=$matches[1]&wc-auth-route=$matches[2]\";s:22:\"^wc-api/v([1-3]{1})/?$\";s:51:\"index.php?wc-api-version=$matches[1]&wc-api-route=/\";s:24:\"^wc-api/v([1-3]{1})(.*)?\";s:61:\"index.php?wc-api-version=$matches[1]&wc-api-route=$matches[2]\";s:7:\"shop/?$\";s:27:\"index.php?post_type=product\";s:37:\"shop/feed/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?post_type=product&feed=$matches[1]\";s:32:\"shop/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?post_type=product&feed=$matches[1]\";s:24:\"shop/page/([0-9]{1,})/?$\";s:45:\"index.php?post_type=product&paged=$matches[1]\";s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:32:\"category/(.+?)/wc-api(/(.*))?/?$\";s:54:\"index.php?category_name=$matches[1]&wc-api=$matches[3]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:29:\"tag/([^/]+)/wc-api(/(.*))?/?$\";s:44:\"index.php?tag=$matches[1]&wc-api=$matches[3]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:55:\"product-category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?product_cat=$matches[1]&feed=$matches[2]\";s:50:\"product-category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?product_cat=$matches[1]&feed=$matches[2]\";s:31:\"product-category/(.+?)/embed/?$\";s:44:\"index.php?product_cat=$matches[1]&embed=true\";s:43:\"product-category/(.+?)/page/?([0-9]{1,})/?$\";s:51:\"index.php?product_cat=$matches[1]&paged=$matches[2]\";s:25:\"product-category/(.+?)/?$\";s:33:\"index.php?product_cat=$matches[1]\";s:52:\"product-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?product_tag=$matches[1]&feed=$matches[2]\";s:47:\"product-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?product_tag=$matches[1]&feed=$matches[2]\";s:28:\"product-tag/([^/]+)/embed/?$\";s:44:\"index.php?product_tag=$matches[1]&embed=true\";s:40:\"product-tag/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?product_tag=$matches[1]&paged=$matches[2]\";s:22:\"product-tag/([^/]+)/?$\";s:33:\"index.php?product_tag=$matches[1]\";s:35:\"product/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:45:\"product/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:65:\"product/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:60:\"product/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:60:\"product/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:41:\"product/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:24:\"product/([^/]+)/embed/?$\";s:40:\"index.php?product=$matches[1]&embed=true\";s:28:\"product/([^/]+)/trackback/?$\";s:34:\"index.php?product=$matches[1]&tb=1\";s:48:\"product/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:46:\"index.php?product=$matches[1]&feed=$matches[2]\";s:43:\"product/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:46:\"index.php?product=$matches[1]&feed=$matches[2]\";s:36:\"product/([^/]+)/page/?([0-9]{1,})/?$\";s:47:\"index.php?product=$matches[1]&paged=$matches[2]\";s:43:\"product/([^/]+)/comment-page-([0-9]{1,})/?$\";s:47:\"index.php?product=$matches[1]&cpage=$matches[2]\";s:33:\"product/([^/]+)/wc-api(/(.*))?/?$\";s:48:\"index.php?product=$matches[1]&wc-api=$matches[3]\";s:39:\"product/[^/]+/([^/]+)/wc-api(/(.*))?/?$\";s:51:\"index.php?attachment=$matches[1]&wc-api=$matches[3]\";s:50:\"product/[^/]+/attachment/([^/]+)/wc-api(/(.*))?/?$\";s:51:\"index.php?attachment=$matches[1]&wc-api=$matches[3]\";s:32:\"product/([^/]+)(?:/([0-9]+))?/?$\";s:46:\"index.php?product=$matches[1]&page=$matches[2]\";s:24:\"product/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:34:\"product/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:54:\"product/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:49:\"product/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:49:\"product/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:30:\"product/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:17:\"wc-api(/(.*))?/?$\";s:29:\"index.php?&wc-api=$matches[2]\";s:31:\"woocommerce-waitlist(/(.*))?/?$\";s:43:\"index.php?&woocommerce-waitlist=$matches[2]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:26:\"comments/wc-api(/(.*))?/?$\";s:29:\"index.php?&wc-api=$matches[2]\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:29:\"search/(.+)/wc-api(/(.*))?/?$\";s:42:\"index.php?s=$matches[1]&wc-api=$matches[3]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:32:\"author/([^/]+)/wc-api(/(.*))?/?$\";s:52:\"index.php?author_name=$matches[1]&wc-api=$matches[3]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:54:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/wc-api(/(.*))?/?$\";s:82:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&wc-api=$matches[5]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:41:\"([0-9]{4})/([0-9]{1,2})/wc-api(/(.*))?/?$\";s:66:\"index.php?year=$matches[1]&monthnum=$matches[2]&wc-api=$matches[4]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:28:\"([0-9]{4})/wc-api(/(.*))?/?$\";s:45:\"index.php?year=$matches[1]&wc-api=$matches[3]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:25:\"(.?.+?)/wc-api(/(.*))?/?$\";s:49:\"index.php?pagename=$matches[1]&wc-api=$matches[3]\";s:28:\"(.?.+?)/order-pay(/(.*))?/?$\";s:52:\"index.php?pagename=$matches[1]&order-pay=$matches[3]\";s:33:\"(.?.+?)/order-received(/(.*))?/?$\";s:57:\"index.php?pagename=$matches[1]&order-received=$matches[3]\";s:25:\"(.?.+?)/orders(/(.*))?/?$\";s:49:\"index.php?pagename=$matches[1]&orders=$matches[3]\";s:29:\"(.?.+?)/view-order(/(.*))?/?$\";s:53:\"index.php?pagename=$matches[1]&view-order=$matches[3]\";s:28:\"(.?.+?)/downloads(/(.*))?/?$\";s:52:\"index.php?pagename=$matches[1]&downloads=$matches[3]\";s:31:\"(.?.+?)/edit-account(/(.*))?/?$\";s:55:\"index.php?pagename=$matches[1]&edit-account=$matches[3]\";s:31:\"(.?.+?)/edit-address(/(.*))?/?$\";s:55:\"index.php?pagename=$matches[1]&edit-address=$matches[3]\";s:34:\"(.?.+?)/payment-methods(/(.*))?/?$\";s:58:\"index.php?pagename=$matches[1]&payment-methods=$matches[3]\";s:32:\"(.?.+?)/lost-password(/(.*))?/?$\";s:56:\"index.php?pagename=$matches[1]&lost-password=$matches[3]\";s:34:\"(.?.+?)/customer-logout(/(.*))?/?$\";s:58:\"index.php?pagename=$matches[1]&customer-logout=$matches[3]\";s:37:\"(.?.+?)/add-payment-method(/(.*))?/?$\";s:61:\"index.php?pagename=$matches[1]&add-payment-method=$matches[3]\";s:40:\"(.?.+?)/delete-payment-method(/(.*))?/?$\";s:64:\"index.php?pagename=$matches[1]&delete-payment-method=$matches[3]\";s:45:\"(.?.+?)/set-default-payment-method(/(.*))?/?$\";s:69:\"index.php?pagename=$matches[1]&set-default-payment-method=$matches[3]\";s:39:\"(.?.+?)/woocommerce-waitlist(/(.*))?/?$\";s:63:\"index.php?pagename=$matches[1]&woocommerce-waitlist=$matches[3]\";s:31:\".?.+?/([^/]+)/wc-api(/(.*))?/?$\";s:51:\"index.php?attachment=$matches[1]&wc-api=$matches[3]\";s:42:\".?.+?/attachment/([^/]+)/wc-api(/(.*))?/?$\";s:51:\"index.php?attachment=$matches[1]&wc-api=$matches[3]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";s:27:\"[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\"[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\"[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\"[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"([^/]+)/embed/?$\";s:37:\"index.php?name=$matches[1]&embed=true\";s:20:\"([^/]+)/trackback/?$\";s:31:\"index.php?name=$matches[1]&tb=1\";s:40:\"([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:35:\"([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:28:\"([^/]+)/page/?([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&paged=$matches[2]\";s:35:\"([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&cpage=$matches[2]\";s:25:\"([^/]+)/wc-api(/(.*))?/?$\";s:45:\"index.php?name=$matches[1]&wc-api=$matches[3]\";s:31:\"[^/]+/([^/]+)/wc-api(/(.*))?/?$\";s:51:\"index.php?attachment=$matches[1]&wc-api=$matches[3]\";s:42:\"[^/]+/attachment/([^/]+)/wc-api(/(.*))?/?$\";s:51:\"index.php?attachment=$matches[1]&wc-api=$matches[3]\";s:24:\"([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?name=$matches[1]&page=$matches[2]\";s:16:\"[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:26:\"[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:46:\"[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:22:\"[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";}','yes'),
	(30,'hack_file','0','yes'),
	(31,'blog_charset','UTF-8','yes'),
	(32,'moderation_keys','','no'),
	(33,'active_plugins','a:2:{i:0;s:45:\"woocommerce-waitlist/woocommerce-waitlist.php\";i:1;s:27:\"woocommerce/woocommerce.php\";}','yes'),
	(34,'category_base','','yes'),
	(35,'ping_sites','http://rpc.pingomatic.com/','yes'),
	(36,'comment_max_links','2','yes'),
	(37,'gmt_offset','0','yes'),
	(38,'default_email_category','1','yes'),
	(39,'recently_edited','','no'),
	(40,'template','twentyseventeen','yes'),
	(41,'stylesheet','twentyseventeen','yes'),
	(42,'comment_whitelist','1','yes'),
	(43,'blacklist_keys','','no'),
	(44,'comment_registration','0','yes'),
	(45,'html_type','text/html','yes'),
	(46,'use_trackback','0','yes'),
	(47,'default_role','subscriber','yes'),
	(48,'db_version','38590','yes'),
	(49,'uploads_use_yearmonth_folders','1','yes'),
	(50,'upload_path','','yes'),
	(51,'blog_public','1','yes'),
	(52,'default_link_category','2','yes'),
	(53,'show_on_front','posts','yes'),
	(54,'tag_base','','yes'),
	(55,'show_avatars','1','yes'),
	(56,'avatar_rating','G','yes'),
	(57,'upload_url_path','','yes'),
	(58,'thumbnail_size_w','150','yes'),
	(59,'thumbnail_size_h','150','yes'),
	(60,'thumbnail_crop','1','yes'),
	(61,'medium_size_w','300','yes'),
	(62,'medium_size_h','300','yes'),
	(63,'avatar_default','mystery','yes'),
	(64,'large_size_w','1024','yes'),
	(65,'large_size_h','1024','yes'),
	(66,'image_default_link_type','none','yes'),
	(67,'image_default_size','','yes'),
	(68,'image_default_align','','yes'),
	(69,'close_comments_for_old_posts','0','yes'),
	(70,'close_comments_days_old','14','yes'),
	(71,'thread_comments','1','yes'),
	(72,'thread_comments_depth','5','yes'),
	(73,'page_comments','0','yes'),
	(74,'comments_per_page','50','yes'),
	(75,'default_comments_page','newest','yes'),
	(76,'comment_order','asc','yes'),
	(77,'sticky_posts','a:0:{}','yes'),
	(78,'widget_categories','a:2:{i:2;a:4:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:12:\"hierarchical\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}','yes'),
	(79,'widget_text','a:0:{}','yes'),
	(80,'widget_rss','a:0:{}','yes'),
	(81,'uninstall_plugins','a:0:{}','no'),
	(82,'timezone_string','','yes'),
	(83,'page_for_posts','0','yes'),
	(84,'page_on_front','0','yes'),
	(85,'default_post_format','0','yes'),
	(86,'link_manager_enabled','0','yes'),
	(87,'finished_splitting_shared_terms','1','yes'),
	(88,'site_icon','0','yes'),
	(89,'medium_large_size_w','768','yes'),
	(90,'medium_large_size_h','0','yes'),
	(91,'wp_page_for_privacy_policy','3','yes'),
	(92,'initial_db_version','38590','yes'),
	(93,'wp_user_roles','a:7:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:114:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:18:\"manage_woocommerce\";b:1;s:24:\"view_woocommerce_reports\";b:1;s:12:\"edit_product\";b:1;s:12:\"read_product\";b:1;s:14:\"delete_product\";b:1;s:13:\"edit_products\";b:1;s:20:\"edit_others_products\";b:1;s:16:\"publish_products\";b:1;s:21:\"read_private_products\";b:1;s:15:\"delete_products\";b:1;s:23:\"delete_private_products\";b:1;s:25:\"delete_published_products\";b:1;s:22:\"delete_others_products\";b:1;s:21:\"edit_private_products\";b:1;s:23:\"edit_published_products\";b:1;s:20:\"manage_product_terms\";b:1;s:18:\"edit_product_terms\";b:1;s:20:\"delete_product_terms\";b:1;s:20:\"assign_product_terms\";b:1;s:15:\"edit_shop_order\";b:1;s:15:\"read_shop_order\";b:1;s:17:\"delete_shop_order\";b:1;s:16:\"edit_shop_orders\";b:1;s:23:\"edit_others_shop_orders\";b:1;s:19:\"publish_shop_orders\";b:1;s:24:\"read_private_shop_orders\";b:1;s:18:\"delete_shop_orders\";b:1;s:26:\"delete_private_shop_orders\";b:1;s:28:\"delete_published_shop_orders\";b:1;s:25:\"delete_others_shop_orders\";b:1;s:24:\"edit_private_shop_orders\";b:1;s:26:\"edit_published_shop_orders\";b:1;s:23:\"manage_shop_order_terms\";b:1;s:21:\"edit_shop_order_terms\";b:1;s:23:\"delete_shop_order_terms\";b:1;s:23:\"assign_shop_order_terms\";b:1;s:16:\"edit_shop_coupon\";b:1;s:16:\"read_shop_coupon\";b:1;s:18:\"delete_shop_coupon\";b:1;s:17:\"edit_shop_coupons\";b:1;s:24:\"edit_others_shop_coupons\";b:1;s:20:\"publish_shop_coupons\";b:1;s:25:\"read_private_shop_coupons\";b:1;s:19:\"delete_shop_coupons\";b:1;s:27:\"delete_private_shop_coupons\";b:1;s:29:\"delete_published_shop_coupons\";b:1;s:26:\"delete_others_shop_coupons\";b:1;s:25:\"edit_private_shop_coupons\";b:1;s:27:\"edit_published_shop_coupons\";b:1;s:24:\"manage_shop_coupon_terms\";b:1;s:22:\"edit_shop_coupon_terms\";b:1;s:24:\"delete_shop_coupon_terms\";b:1;s:24:\"assign_shop_coupon_terms\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}s:8:\"customer\";a:2:{s:4:\"name\";s:8:\"Customer\";s:12:\"capabilities\";a:1:{s:4:\"read\";b:1;}}s:12:\"shop_manager\";a:2:{s:4:\"name\";s:12:\"Shop manager\";s:12:\"capabilities\";a:92:{s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:4:\"read\";b:1;s:18:\"read_private_pages\";b:1;s:18:\"read_private_posts\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_posts\";b:1;s:10:\"edit_pages\";b:1;s:20:\"edit_published_posts\";b:1;s:20:\"edit_published_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"edit_private_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:17:\"edit_others_pages\";b:1;s:13:\"publish_posts\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_posts\";b:1;s:12:\"delete_pages\";b:1;s:20:\"delete_private_pages\";b:1;s:20:\"delete_private_posts\";b:1;s:22:\"delete_published_pages\";b:1;s:22:\"delete_published_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:19:\"delete_others_pages\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:17:\"moderate_comments\";b:1;s:12:\"upload_files\";b:1;s:6:\"export\";b:1;s:6:\"import\";b:1;s:10:\"list_users\";b:1;s:18:\"manage_woocommerce\";b:1;s:24:\"view_woocommerce_reports\";b:1;s:12:\"edit_product\";b:1;s:12:\"read_product\";b:1;s:14:\"delete_product\";b:1;s:13:\"edit_products\";b:1;s:20:\"edit_others_products\";b:1;s:16:\"publish_products\";b:1;s:21:\"read_private_products\";b:1;s:15:\"delete_products\";b:1;s:23:\"delete_private_products\";b:1;s:25:\"delete_published_products\";b:1;s:22:\"delete_others_products\";b:1;s:21:\"edit_private_products\";b:1;s:23:\"edit_published_products\";b:1;s:20:\"manage_product_terms\";b:1;s:18:\"edit_product_terms\";b:1;s:20:\"delete_product_terms\";b:1;s:20:\"assign_product_terms\";b:1;s:15:\"edit_shop_order\";b:1;s:15:\"read_shop_order\";b:1;s:17:\"delete_shop_order\";b:1;s:16:\"edit_shop_orders\";b:1;s:23:\"edit_others_shop_orders\";b:1;s:19:\"publish_shop_orders\";b:1;s:24:\"read_private_shop_orders\";b:1;s:18:\"delete_shop_orders\";b:1;s:26:\"delete_private_shop_orders\";b:1;s:28:\"delete_published_shop_orders\";b:1;s:25:\"delete_others_shop_orders\";b:1;s:24:\"edit_private_shop_orders\";b:1;s:26:\"edit_published_shop_orders\";b:1;s:23:\"manage_shop_order_terms\";b:1;s:21:\"edit_shop_order_terms\";b:1;s:23:\"delete_shop_order_terms\";b:1;s:23:\"assign_shop_order_terms\";b:1;s:16:\"edit_shop_coupon\";b:1;s:16:\"read_shop_coupon\";b:1;s:18:\"delete_shop_coupon\";b:1;s:17:\"edit_shop_coupons\";b:1;s:24:\"edit_others_shop_coupons\";b:1;s:20:\"publish_shop_coupons\";b:1;s:25:\"read_private_shop_coupons\";b:1;s:19:\"delete_shop_coupons\";b:1;s:27:\"delete_private_shop_coupons\";b:1;s:29:\"delete_published_shop_coupons\";b:1;s:26:\"delete_others_shop_coupons\";b:1;s:25:\"edit_private_shop_coupons\";b:1;s:27:\"edit_published_shop_coupons\";b:1;s:24:\"manage_shop_coupon_terms\";b:1;s:22:\"edit_shop_coupon_terms\";b:1;s:24:\"delete_shop_coupon_terms\";b:1;s:24:\"assign_shop_coupon_terms\";b:1;}}}','yes'),
	(94,'fresh_site','0','yes'),
	(95,'widget_search','a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}','yes'),
	(96,'widget_recent-posts','a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}','yes'),
	(97,'widget_recent-comments','a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}','yes'),
	(98,'widget_archives','a:2:{i:2;a:3:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}','yes'),
	(99,'widget_meta','a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}','yes'),
	(100,'sidebars_widgets','a:5:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:6:{i:0;s:8:\"search-2\";i:1;s:14:\"recent-posts-2\";i:2;s:17:\"recent-comments-2\";i:3;s:10:\"archives-2\";i:4;s:12:\"categories-2\";i:5;s:6:\"meta-2\";}s:9:\"sidebar-2\";a:0:{}s:9:\"sidebar-3\";a:0:{}s:13:\"array_version\";i:3;}','yes'),
	(101,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(102,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(103,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(104,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(105,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(106,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(107,'nonce_key',']_c]YTmHuV.wTE`{H?!HNHfG:C[xd94Ziqm8U=DrEFfQq-TK-lu<(Xe5+_eOECYT','no'),
	(108,'nonce_salt','F*$=2d7@G6ko&tZ?@pud?6M w%DNjcPkP<(N3s 8NJe>B@[nfv!,ws*%]~o^XDL/','no'),
	(109,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(110,'widget_nav_menu','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(111,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(112,'cron','a:13:{i:1527801401;a:1:{s:25:\"woocommerce_geoip_updater\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:2:{s:8:\"schedule\";b:0;s:4:\"args\";a:0:{}}}}i:1527802464;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1527804701;a:1:{s:32:\"woocommerce_cancel_unpaid_orders\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:2:{s:8:\"schedule\";b:0;s:4:\"args\";a:0:{}}}}i:1527811200;a:1:{s:27:\"woocommerce_scheduled_sales\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1527811901;a:1:{s:24:\"woocommerce_cleanup_logs\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1527816864;a:3:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1527817072;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1527822701;a:1:{s:28:\"woocommerce_cleanup_sessions\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1527864800;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1527887501;a:1:{s:33:\"woocommerce_cleanup_personal_data\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1527887511;a:1:{s:30:\"woocommerce_tracker_send_event\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1528156800;a:1:{s:25:\"woocommerce_geoip_updater\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:7:\"monthly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:2635200;}}}s:7:\"version\";i:2;}','yes'),
	(113,'theme_mods_twentyseventeen','a:3:{s:18:\"custom_css_post_id\";i:-1;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1526607512;s:4:\"data\";a:4:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:6:{i:0;s:8:\"search-2\";i:1;s:14:\"recent-posts-2\";i:2;s:17:\"recent-comments-2\";i:3;s:10:\"archives-2\";i:4;s:12:\"categories-2\";i:5;s:6:\"meta-2\";}s:9:\"sidebar-2\";a:0:{}s:9:\"sidebar-3\";a:0:{}}}s:18:\"nav_menu_locations\";a:0:{}}','yes'),
	(117,'_site_transient_update_core','O:8:\"stdClass\":4:{s:7:\"updates\";a:1:{i:0;O:8:\"stdClass\":10:{s:8:\"response\";s:6:\"latest\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-4.9.6.zip\";s:6:\"locale\";s:5:\"en_US\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-4.9.6.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-4.9.6-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-4.9.6-new-bundled.zip\";s:7:\"partial\";b:0;s:8:\"rollback\";b:0;}s:7:\"current\";s:5:\"4.9.6\";s:7:\"version\";s:5:\"4.9.6\";s:11:\"php_version\";s:5:\"5.2.4\";s:13:\"mysql_version\";s:3:\"5.0\";s:11:\"new_bundled\";s:3:\"4.7\";s:15:\"partial_version\";s:0:\"\";}}s:12:\"last_checked\";i:1527801101;s:15:\"version_checked\";s:5:\"4.9.6\";s:12:\"translations\";a:0:{}}','no'),
	(128,'can_compress_scripts','1','no'),
	(141,'recently_activated','a:0:{}','yes'),
	(144,'woocommerce_store_address','123 test way','yes'),
	(145,'woocommerce_store_address_2','','yes'),
	(146,'woocommerce_store_city','Testing','yes'),
	(147,'woocommerce_default_country','GB:*','yes'),
	(148,'woocommerce_store_postcode','SS11SS','yes'),
	(149,'woocommerce_allowed_countries','all','yes'),
	(150,'woocommerce_all_except_countries','','yes'),
	(151,'woocommerce_specific_allowed_countries','','yes'),
	(152,'woocommerce_ship_to_countries','','yes'),
	(153,'woocommerce_specific_ship_to_countries','','yes'),
	(154,'woocommerce_default_customer_address','geolocation','yes'),
	(155,'woocommerce_calc_taxes','no','yes'),
	(156,'woocommerce_enable_coupons','yes','yes'),
	(157,'woocommerce_calc_discounts_sequentially','no','no'),
	(158,'woocommerce_currency','GBP','yes'),
	(159,'woocommerce_currency_pos','left','yes'),
	(160,'woocommerce_price_thousand_sep',',','yes'),
	(161,'woocommerce_price_decimal_sep','.','yes'),
	(162,'woocommerce_price_num_decimals','2','yes'),
	(163,'woocommerce_shop_page_id','5','yes'),
	(164,'woocommerce_cart_redirect_after_add','no','yes'),
	(165,'woocommerce_enable_ajax_add_to_cart','yes','yes'),
	(166,'woocommerce_weight_unit','kg','yes'),
	(167,'woocommerce_dimension_unit','cm','yes'),
	(168,'woocommerce_enable_reviews','yes','yes'),
	(169,'woocommerce_review_rating_verification_label','yes','no'),
	(170,'woocommerce_review_rating_verification_required','no','no'),
	(171,'woocommerce_enable_review_rating','yes','yes'),
	(172,'woocommerce_review_rating_required','yes','no'),
	(173,'woocommerce_manage_stock','yes','yes'),
	(174,'woocommerce_hold_stock_minutes','60','no'),
	(175,'woocommerce_notify_low_stock','yes','no'),
	(176,'woocommerce_notify_no_stock','yes','no'),
	(177,'woocommerce_stock_email_recipient','joey@pie.co.de','no'),
	(178,'woocommerce_notify_low_stock_amount','2','no'),
	(179,'woocommerce_notify_no_stock_amount','0','yes'),
	(180,'woocommerce_hide_out_of_stock_items','no','yes'),
	(181,'woocommerce_stock_format','','yes'),
	(182,'woocommerce_file_download_method','force','no'),
	(183,'woocommerce_downloads_require_login','no','no'),
	(184,'woocommerce_downloads_grant_access_after_payment','yes','no'),
	(185,'woocommerce_prices_include_tax','no','yes'),
	(186,'woocommerce_tax_based_on','shipping','yes'),
	(187,'woocommerce_shipping_tax_class','inherit','yes'),
	(188,'woocommerce_tax_round_at_subtotal','no','yes'),
	(189,'woocommerce_tax_classes','Reduced rate\nZero rate','yes'),
	(190,'woocommerce_tax_display_shop','excl','yes'),
	(191,'woocommerce_tax_display_cart','excl','yes'),
	(192,'woocommerce_price_display_suffix','','yes'),
	(193,'woocommerce_tax_total_display','itemized','no'),
	(194,'woocommerce_enable_shipping_calc','yes','no'),
	(195,'woocommerce_shipping_cost_requires_address','no','yes'),
	(196,'woocommerce_ship_to_destination','billing','no'),
	(197,'woocommerce_shipping_debug_mode','no','yes'),
	(198,'woocommerce_enable_guest_checkout','yes','no'),
	(199,'woocommerce_enable_checkout_login_reminder','no','no'),
	(200,'woocommerce_enable_signup_and_login_from_checkout','no','no'),
	(201,'woocommerce_enable_myaccount_registration','no','no'),
	(202,'woocommerce_registration_generate_username','yes','no'),
	(203,'woocommerce_registration_generate_password','yes','no'),
	(204,'woocommerce_erasure_request_removes_order_data','no','no'),
	(205,'woocommerce_erasure_request_removes_download_data','no','no'),
	(206,'woocommerce_registration_privacy_policy_text','Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our [privacy_policy].','yes'),
	(207,'woocommerce_checkout_privacy_policy_text','Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our [privacy_policy].','yes'),
	(208,'woocommerce_delete_inactive_accounts','a:2:{s:6:\"number\";s:0:\"\";s:4:\"unit\";s:6:\"months\";}','no'),
	(209,'woocommerce_trash_pending_orders','','no'),
	(210,'woocommerce_trash_failed_orders','','no'),
	(211,'woocommerce_trash_cancelled_orders','','no'),
	(212,'woocommerce_anonymize_completed_orders','a:2:{s:6:\"number\";s:0:\"\";s:4:\"unit\";s:6:\"months\";}','no'),
	(213,'woocommerce_email_from_name','waitlist-test','no'),
	(214,'woocommerce_email_from_address','joey@pie.co.de','no'),
	(215,'woocommerce_email_header_image','','no'),
	(216,'woocommerce_email_footer_text','{site_title}','no'),
	(217,'woocommerce_email_base_color','#96588a','no'),
	(218,'woocommerce_email_background_color','#f7f7f7','no'),
	(219,'woocommerce_email_body_background_color','#ffffff','no'),
	(220,'woocommerce_email_text_color','#3c3c3c','no'),
	(221,'woocommerce_cart_page_id','6','yes'),
	(222,'woocommerce_checkout_page_id','7','yes'),
	(223,'woocommerce_myaccount_page_id','8','yes'),
	(224,'woocommerce_terms_page_id','','no'),
	(225,'woocommerce_force_ssl_checkout','no','yes'),
	(226,'woocommerce_unforce_ssl_checkout','no','yes'),
	(227,'woocommerce_checkout_pay_endpoint','order-pay','yes'),
	(228,'woocommerce_checkout_order_received_endpoint','order-received','yes'),
	(229,'woocommerce_myaccount_add_payment_method_endpoint','add-payment-method','yes'),
	(230,'woocommerce_myaccount_delete_payment_method_endpoint','delete-payment-method','yes'),
	(231,'woocommerce_myaccount_set_default_payment_method_endpoint','set-default-payment-method','yes'),
	(232,'woocommerce_myaccount_orders_endpoint','orders','yes'),
	(233,'woocommerce_myaccount_view_order_endpoint','view-order','yes'),
	(234,'woocommerce_myaccount_downloads_endpoint','downloads','yes'),
	(235,'woocommerce_myaccount_edit_account_endpoint','edit-account','yes'),
	(236,'woocommerce_myaccount_edit_address_endpoint','edit-address','yes'),
	(237,'woocommerce_myaccount_payment_methods_endpoint','payment-methods','yes'),
	(238,'woocommerce_myaccount_lost_password_endpoint','lost-password','yes'),
	(239,'woocommerce_logout_endpoint','customer-logout','yes'),
	(240,'woocommerce_api_enabled','no','yes'),
	(241,'woocommerce_single_image_width','600','yes'),
	(242,'woocommerce_thumbnail_image_width','300','yes'),
	(243,'woocommerce_checkout_highlight_required_fields','yes','yes'),
	(244,'woocommerce_demo_store','no','no'),
	(245,'woocommerce_permalinks','a:5:{s:12:\"product_base\";s:7:\"product\";s:13:\"category_base\";s:16:\"product-category\";s:8:\"tag_base\";s:11:\"product-tag\";s:14:\"attribute_base\";s:0:\"\";s:22:\"use_verbose_page_rules\";b:0;}','yes'),
	(246,'current_theme_supports_woocommerce','yes','yes'),
	(247,'woocommerce_queue_flush_rewrite_rules','no','yes'),
	(250,'default_product_cat','15','yes'),
	(255,'woocommerce_admin_notices','a:1:{i:0;s:20:\"no_secure_connection\";}','yes'),
	(256,'_transient_woocommerce_webhook_ids','a:0:{}','yes'),
	(257,'widget_woocommerce_widget_cart','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(258,'widget_woocommerce_layered_nav_filters','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(259,'widget_woocommerce_layered_nav','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(260,'widget_woocommerce_price_filter','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(261,'widget_woocommerce_product_categories','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(262,'widget_woocommerce_product_search','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(263,'widget_woocommerce_product_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(264,'widget_woocommerce_products','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(265,'widget_woocommerce_recently_viewed_products','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(266,'widget_woocommerce_top_rated_products','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(267,'widget_woocommerce_recent_reviews','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(268,'widget_woocommerce_rating_filter','a:1:{s:12:\"_multiwidget\";i:1;}','yes'),
	(275,'woocommerce_meta_box_errors','a:0:{}','yes'),
	(277,'woocommerce_product_type','both','yes'),
	(278,'woocommerce_allow_tracking','no','yes'),
	(280,'woocommerce_stripe_settings','a:3:{s:7:\"enabled\";s:2:\"no\";s:14:\"create_account\";b:0;s:5:\"email\";b:0;}','yes'),
	(281,'woocommerce_ppec_paypal_settings','a:2:{s:16:\"reroute_requests\";b:0;s:5:\"email\";b:0;}','yes'),
	(282,'woocommerce_cheque_settings','a:1:{s:7:\"enabled\";s:2:\"no\";}','yes'),
	(283,'woocommerce_bacs_settings','a:1:{s:7:\"enabled\";s:2:\"no\";}','yes'),
	(284,'woocommerce_cod_settings','a:1:{s:7:\"enabled\";s:2:\"no\";}','yes'),
	(285,'_transient_shipping-transient-version','1526607502','yes'),
	(286,'woocommerce_flat_rate_1_settings','a:3:{s:5:\"title\";s:9:\"Flat rate\";s:10:\"tax_status\";s:7:\"taxable\";s:4:\"cost\";s:2:\"10\";}','yes'),
	(287,'woocommerce_flat_rate_2_settings','a:3:{s:5:\"title\";s:9:\"Flat rate\";s:10:\"tax_status\";s:7:\"taxable\";s:4:\"cost\";s:2:\"15\";}','yes'),
	(293,'current_theme','Twenty Seventeen','yes'),
	(294,'theme_mods_storefront','a:4:{i:0;b:0;s:18:\"nav_menu_locations\";a:0:{}s:18:\"custom_css_post_id\";i:-1;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1526609358;s:4:\"data\";a:7:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:6:{i:0;s:8:\"search-2\";i:1;s:14:\"recent-posts-2\";i:2;s:17:\"recent-comments-2\";i:3;s:10:\"archives-2\";i:4;s:12:\"categories-2\";i:5;s:6:\"meta-2\";}s:8:\"header-1\";a:0:{}s:8:\"footer-1\";a:0:{}s:8:\"footer-2\";a:0:{}s:8:\"footer-3\";a:0:{}s:8:\"footer-4\";a:0:{}}}}','yes'),
	(295,'theme_switched','','yes'),
	(296,'storefront_nux_fresh_site','0','yes'),
	(297,'woocommerce_catalog_rows','4','yes'),
	(298,'woocommerce_catalog_columns','3','yes'),
	(299,'woocommerce_maybe_regenerate_images_hash','494bdb3708d5a8a5b3be00abab75910e','yes'),
	(301,'_site_transient_update_themes','O:8:\"stdClass\":4:{s:12:\"last_checked\";i:1527801103;s:7:\"checked\";a:4:{s:10:\"storefront\";s:5:\"2.3.2\";s:13:\"twentyfifteen\";s:3:\"2.0\";s:15:\"twentyseventeen\";s:3:\"1.6\";s:13:\"twentysixteen\";s:3:\"1.5\";}s:8:\"response\";a:0:{}s:12:\"translations\";a:0:{}}','no'),
	(304,'storefront_nux_dismissed','1','yes'),
	(305,'_transient_product_query-transient-version','1527801119','yes'),
	(306,'_transient_product-transient-version','1527801119','yes'),
	(312,'product_cat_children','a:1:{i:16;a:3:{i:0;i:17;i:1;i:18;i:2;i:19;}}','yes'),
	(314,'_transient_wc_attribute_taxonomies','a:2:{i:0;O:8:\"stdClass\":6:{s:12:\"attribute_id\";s:1:\"1\";s:14:\"attribute_name\";s:5:\"color\";s:15:\"attribute_label\";s:5:\"Color\";s:14:\"attribute_type\";s:6:\"select\";s:17:\"attribute_orderby\";s:10:\"menu_order\";s:16:\"attribute_public\";s:1:\"0\";}i:1;O:8:\"stdClass\":6:{s:12:\"attribute_id\";s:1:\"2\";s:14:\"attribute_name\";s:4:\"size\";s:15:\"attribute_label\";s:4:\"Size\";s:14:\"attribute_type\";s:6:\"select\";s:17:\"attribute_orderby\";s:10:\"menu_order\";s:16:\"attribute_public\";s:1:\"0\";}}','yes'),
	(320,'pa_size_children','a:0:{}','yes'),
	(330,'pa_color_children','a:0:{}','yes'),
	(422,'_transient_timeout_wc_product_children_11','1529199796','no'),
	(423,'_transient_wc_product_children_11','a:2:{s:3:\"all\";a:3:{i:0;i:63;i:1;i:64;i:2;i:65;}s:7:\"visible\";a:3:{i:0;i:63;i:1;i:64;i:2;i:65;}}','no'),
	(424,'_transient_timeout_wc_var_prices_11','1529778547','no'),
	(425,'_transient_wc_var_prices_11','{\"version\":\"1527176498\",\"560ac1a85bd86b447f56a90c5993d587\":{\"price\":{\"63\":\"30.00\",\"64\":\"30.00\",\"65\":\"30.00\"},\"regular_price\":{\"63\":\"30.00\",\"64\":\"30.00\",\"65\":\"30.00\"},\"sale_price\":{\"63\":\"30.00\",\"64\":\"30.00\",\"65\":\"30.00\"}}}','no'),
	(426,'_transient_timeout_wc_child_has_weight_11','1529199796','no'),
	(427,'_transient_wc_child_has_weight_11','','no'),
	(428,'_transient_timeout_wc_child_has_dimensions_11','1529199796','no'),
	(429,'_transient_wc_child_has_dimensions_11','','no'),
	(456,'_transient_timeout_wc_shipping_method_count_1_1526607502','1529200001','no'),
	(457,'_transient_wc_shipping_method_count_1_1526607502','2','no'),
	(466,'_woocommerce_waitlist_metadata_updated','1','yes'),
	(467,'woocommerce_waitlist_archive_on','yes','yes'),
	(468,'woocommerce_waitlist_registration_needed','no','yes'),
	(469,'_woocommerce_waitlist_counts_updated','1','yes'),
	(470,'woocommerce_waitlist','a:1:{s:7:\"version\";s:5:\"1.8.0\";}','yes'),
	(516,'woocommerce_waitlist_new_user_opt-in','no','yes'),
	(517,'woocommerce_waitlist_registered_user_opt-in','no','yes'),
	(518,'woocommerce_waitlist_show_on_shop','no','yes'),
	(519,'woocommerce_waitlist_minimum_stock','1','yes'),
	(520,'woocommerce_waitlist_notify_admin','no','yes'),
	(521,'woocommerce_waitlist_admin_email','','yes'),
	(530,'_site_transient_update_plugins','O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1527801103;s:7:\"checked\";a:2:{s:27:\"woocommerce/woocommerce.php\";s:5:\"3.4.1\";s:45:\"woocommerce-waitlist/woocommerce-waitlist.php\";s:5:\"1.8.0\";}s:8:\"response\";a:0:{}s:12:\"translations\";a:0:{}s:9:\"no_update\";a:1:{s:27:\"woocommerce/woocommerce.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:25:\"w.org/plugins/woocommerce\";s:4:\"slug\";s:11:\"woocommerce\";s:6:\"plugin\";s:27:\"woocommerce/woocommerce.php\";s:11:\"new_version\";s:5:\"3.4.1\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/woocommerce/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/plugin/woocommerce.3.4.1.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:64:\"https://ps.w.org/woocommerce/assets/icon-256x256.png?rev=1440831\";s:2:\"1x\";s:64:\"https://ps.w.org/woocommerce/assets/icon-128x128.png?rev=1440831\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/woocommerce/assets/banner-1544x500.png?rev=1629184\";s:2:\"1x\";s:66:\"https://ps.w.org/woocommerce/assets/banner-772x250.png?rev=1629184\";}s:11:\"banners_rtl\";a:0:{}}}}','no'),
	(575,'_transient_timeout_external_ip_address_192.168.75.1','1528405900','no'),
	(576,'_transient_external_ip_address_192.168.75.1','90.253.147.85','no'),
	(579,'woocommerce_version','3.4.1','yes'),
	(580,'woocommerce_db_version','3.4.1','yes'),
	(583,'_site_transient_timeout_theme_roots','1527802902','no'),
	(584,'_site_transient_theme_roots','a:4:{s:10:\"storefront\";s:7:\"/themes\";s:13:\"twentyfifteen\";s:7:\"/themes\";s:15:\"twentyseventeen\";s:7:\"/themes\";s:13:\"twentysixteen\";s:7:\"/themes\";}','no'),
	(586,'_transient_timeout_external_ip_address_172.18.0.2','1528405904','no'),
	(587,'_transient_external_ip_address_172.18.0.2','90.253.147.85','no'),
	(598,'_transient_wc_count_comments','O:8:\"stdClass\":7:{s:14:\"total_comments\";i:1;s:3:\"all\";i:1;s:8:\"approved\";s:1:\"1\";s:9:\"moderated\";i:0;s:4:\"spam\";i:0;s:5:\"trash\";i:0;s:12:\"post-trashed\";i:0;}','yes'),
	(599,'_transient_timeout_wc_product_children_10','1530393119','no'),
	(600,'_transient_wc_product_children_10','a:2:{s:3:\"all\";a:3:{i:0;i:25;i:1;i:24;i:2;i:26;}s:7:\"visible\";a:3:{i:0;i:25;i:1;i:24;i:2;i:26;}}','no');

/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_postmeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_postmeta`;

CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;

INSERT INTO `wp_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`)
VALUES
	(1,2,'_wp_page_template','default'),
	(2,3,'_wp_page_template','default'),
	(5,10,'_sku','woo-vneck-tee'),
	(6,10,'_regular_price',''),
	(7,10,'_sale_price',''),
	(8,10,'_sale_price_dates_from',''),
	(9,10,'_sale_price_dates_to',''),
	(10,10,'total_sales','0'),
	(11,10,'_tax_status','taxable'),
	(12,10,'_tax_class',''),
	(13,10,'_manage_stock','no'),
	(14,10,'_backorders','no'),
	(15,10,'_sold_individually','no'),
	(16,10,'_weight',''),
	(17,10,'_length',''),
	(18,10,'_width',''),
	(19,10,'_height',''),
	(20,10,'_upsell_ids','a:0:{}'),
	(21,10,'_crosssell_ids','a:0:{}'),
	(22,10,'_purchase_note',''),
	(23,10,'_default_attributes','a:0:{}'),
	(24,10,'_virtual','no'),
	(25,10,'_downloadable','no'),
	(26,10,'_product_image_gallery','36,37'),
	(27,10,'_download_limit','0'),
	(28,10,'_download_expiry','0'),
	(29,10,'_stock',NULL),
	(30,10,'_stock_status','outofstock'),
	(31,10,'_wc_average_rating','0'),
	(32,10,'_wc_rating_count','a:0:{}'),
	(33,10,'_wc_review_count','0'),
	(34,10,'_downloadable_files','a:0:{}'),
	(35,10,'_product_attributes','a:2:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:1;s:11:\"is_taxonomy\";i:1;}s:7:\"pa_size\";a:6:{s:4:\"name\";s:7:\"pa_size\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:1;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:1;s:11:\"is_taxonomy\";i:1;}}'),
	(36,10,'_product_version','3.4.1'),
	(39,11,'_sku','woo-hoodie'),
	(40,11,'_regular_price',''),
	(41,11,'_sale_price',''),
	(42,11,'_sale_price_dates_from',''),
	(43,11,'_sale_price_dates_to',''),
	(44,11,'total_sales','0'),
	(45,11,'_tax_status','taxable'),
	(46,11,'_tax_class',''),
	(47,11,'_manage_stock','no'),
	(48,11,'_backorders','no'),
	(49,11,'_sold_individually','no'),
	(50,11,'_weight',''),
	(51,11,'_length',''),
	(52,11,'_width',''),
	(53,11,'_height',''),
	(54,11,'_upsell_ids','a:0:{}'),
	(55,11,'_crosssell_ids','a:0:{}'),
	(56,11,'_purchase_note',''),
	(57,11,'_default_attributes','a:0:{}'),
	(58,11,'_virtual','no'),
	(59,11,'_downloadable','no'),
	(60,11,'_product_image_gallery','39,40,41'),
	(61,11,'_download_limit','0'),
	(62,11,'_download_expiry','0'),
	(63,11,'_stock',NULL),
	(64,11,'_stock_status','instock'),
	(65,11,'_wc_average_rating','0'),
	(66,11,'_wc_rating_count','a:0:{}'),
	(67,11,'_wc_review_count','0'),
	(68,11,'_downloadable_files','a:0:{}'),
	(69,11,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:1;s:11:\"is_taxonomy\";i:1;}}'),
	(70,11,'_product_version','3.4.0'),
	(73,12,'_sku','woo-hoodie-with-logo'),
	(74,12,'_regular_price','45'),
	(75,12,'_sale_price',''),
	(76,12,'_sale_price_dates_from',''),
	(77,12,'_sale_price_dates_to',''),
	(78,12,'total_sales','0'),
	(79,12,'_tax_status','taxable'),
	(80,12,'_tax_class',''),
	(81,12,'_manage_stock','no'),
	(82,12,'_backorders','no'),
	(83,12,'_sold_individually','no'),
	(84,12,'_weight',''),
	(85,12,'_length',''),
	(86,12,'_width',''),
	(87,12,'_height',''),
	(88,12,'_upsell_ids','a:0:{}'),
	(89,12,'_crosssell_ids','a:0:{}'),
	(90,12,'_purchase_note',''),
	(91,12,'_default_attributes','a:0:{}'),
	(92,12,'_virtual','no'),
	(93,12,'_downloadable','no'),
	(94,12,'_product_image_gallery',''),
	(95,12,'_download_limit','0'),
	(96,12,'_download_expiry','0'),
	(97,12,'_stock',NULL),
	(98,12,'_stock_status','outofstock'),
	(99,12,'_wc_average_rating','0'),
	(100,12,'_wc_rating_count','a:0:{}'),
	(101,12,'_wc_review_count','0'),
	(102,12,'_downloadable_files','a:0:{}'),
	(103,12,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(104,12,'_product_version','3.4.0'),
	(105,12,'_price','45'),
	(107,13,'_sku','woo-tshirt'),
	(108,13,'_regular_price','18'),
	(109,13,'_sale_price',''),
	(110,13,'_sale_price_dates_from',''),
	(111,13,'_sale_price_dates_to',''),
	(112,13,'total_sales','0'),
	(113,13,'_tax_status','taxable'),
	(114,13,'_tax_class',''),
	(115,13,'_manage_stock','no'),
	(116,13,'_backorders','no'),
	(117,13,'_sold_individually','no'),
	(118,13,'_weight',''),
	(119,13,'_length',''),
	(120,13,'_width',''),
	(121,13,'_height',''),
	(122,13,'_upsell_ids','a:0:{}'),
	(123,13,'_crosssell_ids','a:0:{}'),
	(124,13,'_purchase_note',''),
	(125,13,'_default_attributes','a:0:{}'),
	(126,13,'_virtual','no'),
	(127,13,'_downloadable','no'),
	(128,13,'_product_image_gallery',''),
	(129,13,'_download_limit','0'),
	(130,13,'_download_expiry','0'),
	(131,13,'_stock',NULL),
	(132,13,'_stock_status','instock'),
	(133,13,'_wc_average_rating','0'),
	(134,13,'_wc_rating_count','a:0:{}'),
	(135,13,'_wc_review_count','0'),
	(136,13,'_downloadable_files','a:0:{}'),
	(137,13,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(138,13,'_product_version','3.4.0'),
	(139,13,'_price','18'),
	(141,14,'_sku','woo-beanie'),
	(142,14,'_regular_price','20'),
	(143,14,'_sale_price','18'),
	(144,14,'_sale_price_dates_from',''),
	(145,14,'_sale_price_dates_to',''),
	(146,14,'total_sales','0'),
	(147,14,'_tax_status','taxable'),
	(148,14,'_tax_class',''),
	(149,14,'_manage_stock','no'),
	(150,14,'_backorders','no'),
	(151,14,'_sold_individually','no'),
	(152,14,'_weight',''),
	(153,14,'_length',''),
	(154,14,'_width',''),
	(155,14,'_height',''),
	(156,14,'_upsell_ids','a:0:{}'),
	(157,14,'_crosssell_ids','a:0:{}'),
	(158,14,'_purchase_note',''),
	(159,14,'_default_attributes','a:0:{}'),
	(160,14,'_virtual','no'),
	(161,14,'_downloadable','no'),
	(162,14,'_product_image_gallery',''),
	(163,14,'_download_limit','0'),
	(164,14,'_download_expiry','0'),
	(165,14,'_stock',NULL),
	(166,14,'_stock_status','outofstock'),
	(167,14,'_wc_average_rating','0'),
	(168,14,'_wc_rating_count','a:0:{}'),
	(169,14,'_wc_review_count','0'),
	(170,14,'_downloadable_files','a:0:{}'),
	(171,14,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(172,14,'_product_version','3.4.0'),
	(173,14,'_price','18'),
	(175,15,'_sku','woo-belt'),
	(176,15,'_regular_price','65'),
	(177,15,'_sale_price','55'),
	(178,15,'_sale_price_dates_from',''),
	(179,15,'_sale_price_dates_to',''),
	(180,15,'total_sales','0'),
	(181,15,'_tax_status','taxable'),
	(182,15,'_tax_class',''),
	(183,15,'_manage_stock','no'),
	(184,15,'_backorders','no'),
	(185,15,'_sold_individually','no'),
	(186,15,'_weight',''),
	(187,15,'_length',''),
	(188,15,'_width',''),
	(189,15,'_height',''),
	(190,15,'_upsell_ids','a:0:{}'),
	(191,15,'_crosssell_ids','a:0:{}'),
	(192,15,'_purchase_note',''),
	(193,15,'_default_attributes','a:0:{}'),
	(194,15,'_virtual','no'),
	(195,15,'_downloadable','no'),
	(196,15,'_product_image_gallery',''),
	(197,15,'_download_limit','0'),
	(198,15,'_download_expiry','0'),
	(199,15,'_stock',NULL),
	(200,15,'_stock_status','outofstock'),
	(201,15,'_wc_average_rating','0'),
	(202,15,'_wc_rating_count','a:0:{}'),
	(203,15,'_wc_review_count','0'),
	(204,15,'_downloadable_files','a:0:{}'),
	(205,15,'_product_attributes','a:0:{}'),
	(206,15,'_product_version','3.4.0'),
	(207,15,'_price','55'),
	(209,16,'_sku','woo-cap'),
	(210,16,'_regular_price','18'),
	(211,16,'_sale_price','16'),
	(212,16,'_sale_price_dates_from',''),
	(213,16,'_sale_price_dates_to',''),
	(214,16,'total_sales','0'),
	(215,16,'_tax_status','taxable'),
	(216,16,'_tax_class',''),
	(217,16,'_manage_stock','no'),
	(218,16,'_backorders','no'),
	(219,16,'_sold_individually','no'),
	(220,16,'_weight',''),
	(221,16,'_length',''),
	(222,16,'_width',''),
	(223,16,'_height',''),
	(224,16,'_upsell_ids','a:0:{}'),
	(225,16,'_crosssell_ids','a:0:{}'),
	(226,16,'_purchase_note',''),
	(227,16,'_default_attributes','a:0:{}'),
	(228,16,'_virtual','no'),
	(229,16,'_downloadable','no'),
	(230,16,'_product_image_gallery',''),
	(231,16,'_download_limit','0'),
	(232,16,'_download_expiry','0'),
	(233,16,'_stock',NULL),
	(234,16,'_stock_status','outofstock'),
	(235,16,'_wc_average_rating','0'),
	(236,16,'_wc_rating_count','a:0:{}'),
	(237,16,'_wc_review_count','0'),
	(238,16,'_downloadable_files','a:0:{}'),
	(239,16,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(240,16,'_product_version','3.4.0'),
	(241,16,'_price','16'),
	(243,17,'_sku','woo-sunglasses'),
	(244,17,'_regular_price','90'),
	(245,17,'_sale_price',''),
	(246,17,'_sale_price_dates_from',''),
	(247,17,'_sale_price_dates_to',''),
	(248,17,'total_sales','0'),
	(249,17,'_tax_status','taxable'),
	(250,17,'_tax_class',''),
	(251,17,'_manage_stock','no'),
	(252,17,'_backorders','no'),
	(253,17,'_sold_individually','no'),
	(254,17,'_weight',''),
	(255,17,'_length',''),
	(256,17,'_width',''),
	(257,17,'_height',''),
	(258,17,'_upsell_ids','a:0:{}'),
	(259,17,'_crosssell_ids','a:0:{}'),
	(260,17,'_purchase_note',''),
	(261,17,'_default_attributes','a:0:{}'),
	(262,17,'_virtual','no'),
	(263,17,'_downloadable','no'),
	(264,17,'_product_image_gallery',''),
	(265,17,'_download_limit','0'),
	(266,17,'_download_expiry','0'),
	(267,17,'_stock',NULL),
	(268,17,'_stock_status','instock'),
	(269,17,'_wc_average_rating','0'),
	(270,17,'_wc_rating_count','a:0:{}'),
	(271,17,'_wc_review_count','0'),
	(272,17,'_downloadable_files','a:0:{}'),
	(273,17,'_product_attributes','a:0:{}'),
	(274,17,'_product_version','3.4.0'),
	(275,17,'_price','90'),
	(277,18,'_sku','woo-hoodie-with-pocket'),
	(278,18,'_regular_price','45'),
	(279,18,'_sale_price','35'),
	(280,18,'_sale_price_dates_from',''),
	(281,18,'_sale_price_dates_to',''),
	(282,18,'total_sales','0'),
	(283,18,'_tax_status','taxable'),
	(284,18,'_tax_class',''),
	(285,18,'_manage_stock','no'),
	(286,18,'_backorders','no'),
	(287,18,'_sold_individually','no'),
	(288,18,'_weight',''),
	(289,18,'_length',''),
	(290,18,'_width',''),
	(291,18,'_height',''),
	(292,18,'_upsell_ids','a:0:{}'),
	(293,18,'_crosssell_ids','a:0:{}'),
	(294,18,'_purchase_note',''),
	(295,18,'_default_attributes','a:0:{}'),
	(296,18,'_virtual','no'),
	(297,18,'_downloadable','no'),
	(298,18,'_product_image_gallery',''),
	(299,18,'_download_limit','0'),
	(300,18,'_download_expiry','0'),
	(301,18,'_stock',NULL),
	(302,18,'_stock_status','outofstock'),
	(303,18,'_wc_average_rating','0'),
	(304,18,'_wc_rating_count','a:0:{}'),
	(305,18,'_wc_review_count','0'),
	(306,18,'_downloadable_files','a:0:{}'),
	(307,18,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(308,18,'_product_version','3.4.0'),
	(309,18,'_price','35'),
	(311,19,'_sku','woo-hoodie-with-zipper'),
	(312,19,'_regular_price','45'),
	(313,19,'_sale_price',''),
	(314,19,'_sale_price_dates_from',''),
	(315,19,'_sale_price_dates_to',''),
	(316,19,'total_sales','0'),
	(317,19,'_tax_status','taxable'),
	(318,19,'_tax_class',''),
	(319,19,'_manage_stock','no'),
	(320,19,'_backorders','no'),
	(321,19,'_sold_individually','no'),
	(322,19,'_weight',''),
	(323,19,'_length',''),
	(324,19,'_width',''),
	(325,19,'_height',''),
	(326,19,'_upsell_ids','a:0:{}'),
	(327,19,'_crosssell_ids','a:0:{}'),
	(328,19,'_purchase_note',''),
	(329,19,'_default_attributes','a:0:{}'),
	(330,19,'_virtual','no'),
	(331,19,'_downloadable','no'),
	(332,19,'_product_image_gallery',''),
	(333,19,'_download_limit','0'),
	(334,19,'_download_expiry','0'),
	(335,19,'_stock',NULL),
	(336,19,'_stock_status','outofstock'),
	(337,19,'_wc_average_rating','0'),
	(338,19,'_wc_rating_count','a:0:{}'),
	(339,19,'_wc_review_count','0'),
	(340,19,'_downloadable_files','a:0:{}'),
	(341,19,'_product_attributes','a:0:{}'),
	(342,19,'_product_version','3.4.0'),
	(343,19,'_price','45'),
	(345,20,'_sku','woo-long-sleeve-tee'),
	(346,20,'_regular_price','25'),
	(347,20,'_sale_price',''),
	(348,20,'_sale_price_dates_from',''),
	(349,20,'_sale_price_dates_to',''),
	(350,20,'total_sales','0'),
	(351,20,'_tax_status','taxable'),
	(352,20,'_tax_class',''),
	(353,20,'_manage_stock','no'),
	(354,20,'_backorders','no'),
	(355,20,'_sold_individually','no'),
	(356,20,'_weight',''),
	(357,20,'_length',''),
	(358,20,'_width',''),
	(359,20,'_height',''),
	(360,20,'_upsell_ids','a:0:{}'),
	(361,20,'_crosssell_ids','a:0:{}'),
	(362,20,'_purchase_note',''),
	(363,20,'_default_attributes','a:0:{}'),
	(364,20,'_virtual','no'),
	(365,20,'_downloadable','no'),
	(366,20,'_product_image_gallery',''),
	(367,20,'_download_limit','0'),
	(368,20,'_download_expiry','0'),
	(369,20,'_stock',NULL),
	(370,20,'_stock_status','outofstock'),
	(371,20,'_wc_average_rating','0'),
	(372,20,'_wc_rating_count','a:0:{}'),
	(373,20,'_wc_review_count','0'),
	(374,20,'_downloadable_files','a:0:{}'),
	(375,20,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(376,20,'_product_version','3.4.0'),
	(377,20,'_price','25'),
	(379,21,'_sku','woo-polo'),
	(380,21,'_regular_price','20'),
	(381,21,'_sale_price',''),
	(382,21,'_sale_price_dates_from',''),
	(383,21,'_sale_price_dates_to',''),
	(384,21,'total_sales','0'),
	(385,21,'_tax_status','taxable'),
	(386,21,'_tax_class',''),
	(387,21,'_manage_stock','no'),
	(388,21,'_backorders','no'),
	(389,21,'_sold_individually','no'),
	(390,21,'_weight',''),
	(391,21,'_length',''),
	(392,21,'_width',''),
	(393,21,'_height',''),
	(394,21,'_upsell_ids','a:0:{}'),
	(395,21,'_crosssell_ids','a:0:{}'),
	(396,21,'_purchase_note',''),
	(397,21,'_default_attributes','a:0:{}'),
	(398,21,'_virtual','no'),
	(399,21,'_downloadable','no'),
	(400,21,'_product_image_gallery',''),
	(401,21,'_download_limit','0'),
	(402,21,'_download_expiry','0'),
	(403,21,'_stock',NULL),
	(404,21,'_stock_status','outofstock'),
	(405,21,'_wc_average_rating','0'),
	(406,21,'_wc_rating_count','a:0:{}'),
	(407,21,'_wc_review_count','0'),
	(408,21,'_downloadable_files','a:0:{}'),
	(409,21,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(410,21,'_product_version','3.4.0'),
	(411,21,'_price','20'),
	(413,22,'_sku','woo-album'),
	(414,22,'_regular_price','15'),
	(415,22,'_sale_price',''),
	(416,22,'_sale_price_dates_from',''),
	(417,22,'_sale_price_dates_to',''),
	(418,22,'total_sales','0'),
	(419,22,'_tax_status','taxable'),
	(420,22,'_tax_class',''),
	(421,22,'_manage_stock','no'),
	(422,22,'_backorders','no'),
	(423,22,'_sold_individually','no'),
	(424,22,'_weight',''),
	(425,22,'_length',''),
	(426,22,'_width',''),
	(427,22,'_height',''),
	(428,22,'_upsell_ids','a:0:{}'),
	(429,22,'_crosssell_ids','a:0:{}'),
	(430,22,'_purchase_note',''),
	(431,22,'_default_attributes','a:0:{}'),
	(432,22,'_virtual','yes'),
	(433,22,'_downloadable','yes'),
	(434,22,'_product_image_gallery',''),
	(435,22,'_download_limit','1'),
	(436,22,'_download_expiry','1'),
	(437,22,'_stock',NULL),
	(438,22,'_stock_status','outofstock'),
	(439,22,'_wc_average_rating','0'),
	(440,22,'_wc_rating_count','a:0:{}'),
	(441,22,'_wc_review_count','0'),
	(442,22,'_downloadable_files','a:2:{s:36:\"63dd0d49-62f4-46c7-ac22-5b5245a5f12b\";a:3:{s:2:\"id\";s:36:\"63dd0d49-62f4-46c7-ac22-5b5245a5f12b\";s:4:\"name\";s:8:\"Single 1\";s:4:\"file\";s:85:\"https://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2017/08/single.jpg\";}s:36:\"77ae3356-2439-4ebe-8529-33d095a8846f\";a:3:{s:2:\"id\";s:36:\"77ae3356-2439-4ebe-8529-33d095a8846f\";s:4:\"name\";s:8:\"Single 2\";s:4:\"file\";s:84:\"https://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2017/08/album.jpg\";}}'),
	(443,22,'_product_attributes','a:0:{}'),
	(444,22,'_product_version','3.4.0'),
	(445,22,'_price','15'),
	(447,23,'_sku','woo-single'),
	(448,23,'_regular_price','3'),
	(449,23,'_sale_price','2'),
	(450,23,'_sale_price_dates_from',''),
	(451,23,'_sale_price_dates_to',''),
	(452,23,'total_sales','0'),
	(453,23,'_tax_status','taxable'),
	(454,23,'_tax_class',''),
	(455,23,'_manage_stock','no'),
	(456,23,'_backorders','no'),
	(457,23,'_sold_individually','no'),
	(458,23,'_weight',''),
	(459,23,'_length',''),
	(460,23,'_width',''),
	(461,23,'_height',''),
	(462,23,'_upsell_ids','a:0:{}'),
	(463,23,'_crosssell_ids','a:0:{}'),
	(464,23,'_purchase_note',''),
	(465,23,'_default_attributes','a:0:{}'),
	(466,23,'_virtual','yes'),
	(467,23,'_downloadable','yes'),
	(468,23,'_product_image_gallery',''),
	(469,23,'_download_limit','1'),
	(470,23,'_download_expiry','1'),
	(471,23,'_stock',NULL),
	(472,23,'_stock_status','outofstock'),
	(473,23,'_wc_average_rating','0'),
	(474,23,'_wc_rating_count','a:0:{}'),
	(475,23,'_wc_review_count','0'),
	(476,23,'_downloadable_files','a:1:{s:36:\"c2d240f7-ae20-43f6-a809-28c747d977da\";a:3:{s:2:\"id\";s:36:\"c2d240f7-ae20-43f6-a809-28c747d977da\";s:4:\"name\";s:6:\"Single\";s:4:\"file\";s:85:\"https://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2017/08/single.jpg\";}}'),
	(477,23,'_product_attributes','a:0:{}'),
	(478,23,'_product_version','3.4.0'),
	(479,23,'_price','2'),
	(481,24,'_sku','woo-vneck-tee-red'),
	(482,24,'_regular_price','20'),
	(483,24,'_sale_price',''),
	(484,24,'_sale_price_dates_from',''),
	(485,24,'_sale_price_dates_to',''),
	(486,24,'total_sales','0'),
	(487,24,'_tax_status','taxable'),
	(488,24,'_tax_class',''),
	(489,24,'_manage_stock','no'),
	(490,24,'_backorders','no'),
	(491,24,'_sold_individually','no'),
	(492,24,'_weight',''),
	(493,24,'_length',''),
	(494,24,'_width',''),
	(495,24,'_height',''),
	(496,24,'_upsell_ids','a:0:{}'),
	(497,24,'_crosssell_ids','a:0:{}'),
	(498,24,'_purchase_note',''),
	(499,24,'_default_attributes','a:0:{}'),
	(500,24,'_virtual','no'),
	(501,24,'_downloadable','no'),
	(502,24,'_product_image_gallery',''),
	(503,24,'_download_limit','0'),
	(504,24,'_download_expiry','0'),
	(505,24,'_stock',NULL),
	(506,24,'_stock_status','outofstock'),
	(507,24,'_wc_average_rating','0'),
	(508,24,'_wc_rating_count','a:0:{}'),
	(509,24,'_wc_review_count','0'),
	(510,24,'_downloadable_files','a:0:{}'),
	(511,24,'_product_attributes','a:0:{}'),
	(512,24,'_product_version','3.4.0'),
	(513,24,'_price','20'),
	(515,25,'_sku','woo-vneck-tee-green'),
	(516,25,'_regular_price','20'),
	(517,25,'_sale_price',''),
	(518,25,'_sale_price_dates_from',''),
	(519,25,'_sale_price_dates_to',''),
	(520,25,'total_sales','0'),
	(521,25,'_tax_status','taxable'),
	(522,25,'_tax_class',''),
	(523,25,'_manage_stock','no'),
	(524,25,'_backorders','no'),
	(525,25,'_sold_individually','no'),
	(526,25,'_weight',''),
	(527,25,'_length',''),
	(528,25,'_width',''),
	(529,25,'_height',''),
	(530,25,'_upsell_ids','a:0:{}'),
	(531,25,'_crosssell_ids','a:0:{}'),
	(532,25,'_purchase_note',''),
	(533,25,'_default_attributes','a:0:{}'),
	(534,25,'_virtual','no'),
	(535,25,'_downloadable','no'),
	(536,25,'_product_image_gallery',''),
	(537,25,'_download_limit','0'),
	(538,25,'_download_expiry','0'),
	(539,25,'_stock',NULL),
	(540,25,'_stock_status','outofstock'),
	(541,25,'_wc_average_rating','0'),
	(542,25,'_wc_rating_count','a:0:{}'),
	(543,25,'_wc_review_count','0'),
	(544,25,'_downloadable_files','a:0:{}'),
	(545,25,'_product_attributes','a:0:{}'),
	(546,25,'_product_version','3.4.1'),
	(547,25,'_price','20'),
	(549,26,'_sku','woo-vneck-tee-blue'),
	(550,26,'_regular_price','15'),
	(551,26,'_sale_price',''),
	(552,26,'_sale_price_dates_from',''),
	(553,26,'_sale_price_dates_to',''),
	(554,26,'total_sales','0'),
	(555,26,'_tax_status','taxable'),
	(556,26,'_tax_class',''),
	(557,26,'_manage_stock','no'),
	(558,26,'_backorders','no'),
	(559,26,'_sold_individually','no'),
	(560,26,'_weight',''),
	(561,26,'_length',''),
	(562,26,'_width',''),
	(563,26,'_height',''),
	(564,26,'_upsell_ids','a:0:{}'),
	(565,26,'_crosssell_ids','a:0:{}'),
	(566,26,'_purchase_note',''),
	(567,26,'_default_attributes','a:0:{}'),
	(568,26,'_virtual','no'),
	(569,26,'_downloadable','no'),
	(570,26,'_product_image_gallery',''),
	(571,26,'_download_limit','0'),
	(572,26,'_download_expiry','0'),
	(573,26,'_stock',NULL),
	(574,26,'_stock_status','outofstock'),
	(575,26,'_wc_average_rating','0'),
	(576,26,'_wc_rating_count','a:0:{}'),
	(577,26,'_wc_review_count','0'),
	(578,26,'_downloadable_files','a:0:{}'),
	(579,26,'_product_attributes','a:0:{}'),
	(580,26,'_product_version','3.4.0'),
	(581,26,'_price','15'),
	(685,30,'_sku','Woo-tshirt-logo'),
	(686,30,'_regular_price','18'),
	(687,30,'_sale_price',''),
	(688,30,'_sale_price_dates_from',''),
	(689,30,'_sale_price_dates_to',''),
	(690,30,'total_sales','0'),
	(691,30,'_tax_status','taxable'),
	(692,30,'_tax_class',''),
	(693,30,'_manage_stock','no'),
	(694,30,'_backorders','no'),
	(695,30,'_sold_individually','no'),
	(696,30,'_weight',''),
	(697,30,'_length',''),
	(698,30,'_width',''),
	(699,30,'_height',''),
	(700,30,'_upsell_ids','a:0:{}'),
	(701,30,'_crosssell_ids','a:0:{}'),
	(702,30,'_purchase_note',''),
	(703,30,'_default_attributes','a:0:{}'),
	(704,30,'_virtual','no'),
	(705,30,'_downloadable','no'),
	(706,30,'_product_image_gallery',''),
	(707,30,'_download_limit','0'),
	(708,30,'_download_expiry','0'),
	(709,30,'_stock',NULL),
	(710,30,'_stock_status','outofstock'),
	(711,30,'_wc_average_rating','0'),
	(712,30,'_wc_rating_count','a:0:{}'),
	(713,30,'_wc_review_count','0'),
	(714,30,'_downloadable_files','a:0:{}'),
	(715,30,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(716,30,'_product_version','3.4.0'),
	(717,30,'_price','18'),
	(719,31,'_sku','Woo-beanie-logo'),
	(720,31,'_regular_price','20'),
	(721,31,'_sale_price','18'),
	(722,31,'_sale_price_dates_from',''),
	(723,31,'_sale_price_dates_to',''),
	(724,31,'total_sales','0'),
	(725,31,'_tax_status','taxable'),
	(726,31,'_tax_class',''),
	(727,31,'_manage_stock','no'),
	(728,31,'_backorders','no'),
	(729,31,'_sold_individually','no'),
	(730,31,'_weight',''),
	(731,31,'_length',''),
	(732,31,'_width',''),
	(733,31,'_height',''),
	(734,31,'_upsell_ids','a:0:{}'),
	(735,31,'_crosssell_ids','a:0:{}'),
	(736,31,'_purchase_note',''),
	(737,31,'_default_attributes','a:0:{}'),
	(738,31,'_virtual','no'),
	(739,31,'_downloadable','no'),
	(740,31,'_product_image_gallery',''),
	(741,31,'_download_limit','0'),
	(742,31,'_download_expiry','0'),
	(743,31,'_stock',NULL),
	(744,31,'_stock_status','instock'),
	(745,31,'_wc_average_rating','0'),
	(746,31,'_wc_rating_count','a:0:{}'),
	(747,31,'_wc_review_count','0'),
	(748,31,'_downloadable_files','a:0:{}'),
	(749,31,'_product_attributes','a:1:{s:8:\"pa_color\";a:6:{s:4:\"name\";s:8:\"pa_color\";s:5:\"value\";s:0:\"\";s:8:\"position\";i:0;s:10:\"is_visible\";i:1;s:12:\"is_variation\";i:0;s:11:\"is_taxonomy\";i:1;}}'),
	(750,31,'_product_version','3.4.0'),
	(751,31,'_price','18'),
	(753,32,'_sku','logo-collection'),
	(754,32,'_regular_price',''),
	(755,32,'_sale_price',''),
	(756,32,'_sale_price_dates_from',''),
	(757,32,'_sale_price_dates_to',''),
	(758,32,'total_sales','0'),
	(759,32,'_tax_status','taxable'),
	(760,32,'_tax_class',''),
	(761,32,'_manage_stock','no'),
	(762,32,'_backorders','no'),
	(763,32,'_sold_individually','no'),
	(764,32,'_weight',''),
	(765,32,'_length',''),
	(766,32,'_width',''),
	(767,32,'_height',''),
	(768,32,'_upsell_ids','a:0:{}'),
	(769,32,'_crosssell_ids','a:0:{}'),
	(770,32,'_purchase_note',''),
	(771,32,'_default_attributes','a:0:{}'),
	(772,32,'_virtual','no'),
	(773,32,'_downloadable','no'),
	(774,32,'_product_image_gallery','54,53,41'),
	(775,32,'_download_limit','0'),
	(776,32,'_download_expiry','0'),
	(777,32,'_stock',NULL),
	(778,32,'_stock_status','outofstock'),
	(779,32,'_wc_average_rating','0'),
	(780,32,'_wc_rating_count','a:0:{}'),
	(781,32,'_wc_review_count','0'),
	(782,32,'_downloadable_files','a:0:{}'),
	(783,32,'_product_attributes','a:0:{}'),
	(784,32,'_product_version','3.4.0'),
	(787,33,'_sku','wp-pennant'),
	(788,33,'_regular_price','11.05'),
	(789,33,'_sale_price',''),
	(790,33,'_sale_price_dates_from',''),
	(791,33,'_sale_price_dates_to',''),
	(792,33,'total_sales','0'),
	(793,33,'_tax_status','taxable'),
	(794,33,'_tax_class',''),
	(795,33,'_manage_stock','no'),
	(796,33,'_backorders','no'),
	(797,33,'_sold_individually','no'),
	(798,33,'_weight',''),
	(799,33,'_length',''),
	(800,33,'_width',''),
	(801,33,'_height',''),
	(802,33,'_upsell_ids','a:0:{}'),
	(803,33,'_crosssell_ids','a:0:{}'),
	(804,33,'_purchase_note',''),
	(805,33,'_default_attributes','a:0:{}'),
	(806,33,'_virtual','no'),
	(807,33,'_downloadable','no'),
	(808,33,'_product_image_gallery',''),
	(809,33,'_download_limit','0'),
	(810,33,'_download_expiry','0'),
	(811,33,'_stock',NULL),
	(812,33,'_stock_status','instock'),
	(813,33,'_wc_average_rating','0'),
	(814,33,'_wc_rating_count','a:0:{}'),
	(815,33,'_wc_review_count','0'),
	(816,33,'_downloadable_files','a:0:{}'),
	(817,33,'_product_attributes','a:0:{}'),
	(818,33,'_product_version','3.4.0'),
	(819,33,'_price','11.05'),
	(855,35,'_wp_attached_file','2018/05/vneck-tee-2.jpg'),
	(856,35,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:800;s:4:\"file\";s:23:\"2018/05/vneck-tee-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:23:\"vneck-tee-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-768x767.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:767;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:23:\"vneck-tee-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:23:\"vneck-tee-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(857,35,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vneck-tee-2.jpg'),
	(858,36,'_wp_attached_file','2018/05/vnech-tee-green-1.jpg'),
	(859,36,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:29:\"2018/05/vnech-tee-green-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:29:\"vnech-tee-green-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:29:\"vnech-tee-green-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:29:\"vnech-tee-green-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(860,36,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vnech-tee-green-1.jpg'),
	(861,37,'_wp_attached_file','2018/05/vnech-tee-blue-1.jpg'),
	(862,37,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:28:\"2018/05/vnech-tee-blue-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:28:\"vnech-tee-blue-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:28:\"vnech-tee-blue-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:28:\"vnech-tee-blue-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(863,37,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/vnech-tee-blue-1.jpg'),
	(864,10,'_wpcom_is_markdown','1'),
	(865,10,'_wp_old_slug','import-placeholder-for-44'),
	(866,10,'_thumbnail_id','35'),
	(867,38,'_wp_attached_file','2018/05/hoodie-2.jpg'),
	(868,38,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:20:\"2018/05/hoodie-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:20:\"hoodie-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:20:\"hoodie-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:20:\"hoodie-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:20:\"hoodie-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:20:\"hoodie-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:20:\"hoodie-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:20:\"hoodie-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:20:\"hoodie-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:20:\"hoodie-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(869,38,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-2.jpg'),
	(870,39,'_wp_attached_file','2018/05/hoodie-blue-1.jpg'),
	(871,39,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:25:\"2018/05/hoodie-blue-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:25:\"hoodie-blue-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:25:\"hoodie-blue-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:25:\"hoodie-blue-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(872,39,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-blue-1.jpg'),
	(873,40,'_wp_attached_file','2018/05/hoodie-green-1.jpg'),
	(874,40,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:26:\"2018/05/hoodie-green-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:26:\"hoodie-green-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:26:\"hoodie-green-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:26:\"hoodie-green-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(875,40,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-green-1.jpg'),
	(876,41,'_wp_attached_file','2018/05/hoodie-with-logo-2.jpg'),
	(877,41,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:30:\"2018/05/hoodie-with-logo-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:30:\"hoodie-with-logo-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:30:\"hoodie-with-logo-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:30:\"hoodie-with-logo-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(878,41,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-with-logo-2.jpg'),
	(879,11,'_wpcom_is_markdown','1'),
	(880,11,'_wp_old_slug','import-placeholder-for-45'),
	(881,11,'_thumbnail_id','38'),
	(882,12,'_wpcom_is_markdown','1'),
	(883,12,'_wp_old_slug','import-placeholder-for-46'),
	(884,12,'_thumbnail_id','41'),
	(885,42,'_wp_attached_file','2018/05/tshirt-2.jpg'),
	(886,42,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:20:\"2018/05/tshirt-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:20:\"tshirt-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:20:\"tshirt-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:20:\"tshirt-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:20:\"tshirt-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:20:\"tshirt-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:20:\"tshirt-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:20:\"tshirt-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:20:\"tshirt-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:20:\"tshirt-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(887,42,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/tshirt-2.jpg'),
	(888,13,'_wpcom_is_markdown','1'),
	(889,13,'_wp_old_slug','import-placeholder-for-47'),
	(890,13,'_thumbnail_id','42'),
	(891,43,'_wp_attached_file','2018/05/beanie-2.jpg'),
	(892,43,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:20:\"2018/05/beanie-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:20:\"beanie-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:20:\"beanie-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:20:\"beanie-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:20:\"beanie-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:20:\"beanie-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:20:\"beanie-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:20:\"beanie-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:20:\"beanie-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:20:\"beanie-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(893,43,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/beanie-2.jpg'),
	(894,14,'_wpcom_is_markdown','1'),
	(895,14,'_wp_old_slug','import-placeholder-for-48'),
	(896,14,'_thumbnail_id','43'),
	(897,44,'_wp_attached_file','2018/05/belt-2.jpg'),
	(898,44,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:18:\"2018/05/belt-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:18:\"belt-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:18:\"belt-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:18:\"belt-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:18:\"belt-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:18:\"belt-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:18:\"belt-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:18:\"belt-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:18:\"belt-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:18:\"belt-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(899,44,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/belt-2.jpg'),
	(900,15,'_wpcom_is_markdown','1'),
	(901,15,'_wp_old_slug','import-placeholder-for-58'),
	(902,15,'_thumbnail_id','44'),
	(903,45,'_wp_attached_file','2018/05/cap-2.jpg'),
	(904,45,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:17:\"2018/05/cap-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:17:\"cap-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:17:\"cap-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:17:\"cap-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:17:\"cap-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:17:\"cap-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:17:\"cap-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:17:\"cap-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:17:\"cap-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:17:\"cap-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(905,45,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/cap-2.jpg'),
	(906,16,'_wpcom_is_markdown','1'),
	(907,16,'_wp_old_slug','import-placeholder-for-60'),
	(908,16,'_thumbnail_id','45'),
	(909,46,'_wp_attached_file','2018/05/sunglasses-2.jpg'),
	(910,46,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:24:\"2018/05/sunglasses-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:24:\"sunglasses-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:24:\"sunglasses-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:24:\"sunglasses-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(911,46,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/sunglasses-2.jpg'),
	(912,17,'_wpcom_is_markdown','1'),
	(913,17,'_wp_old_slug','import-placeholder-for-62'),
	(914,17,'_thumbnail_id','46'),
	(915,47,'_wp_attached_file','2018/05/hoodie-with-pocket-2.jpg'),
	(916,47,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:32:\"2018/05/hoodie-with-pocket-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:32:\"hoodie-with-pocket-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(917,47,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-with-pocket-2.jpg'),
	(918,18,'_wpcom_is_markdown','1'),
	(919,18,'_wp_old_slug','import-placeholder-for-64'),
	(920,18,'_thumbnail_id','47'),
	(921,48,'_wp_attached_file','2018/05/hoodie-with-zipper-2.jpg'),
	(922,48,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:32:\"2018/05/hoodie-with-zipper-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:32:\"hoodie-with-zipper-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(923,48,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/hoodie-with-zipper-2.jpg'),
	(924,19,'_wpcom_is_markdown','1'),
	(925,19,'_wp_old_slug','import-placeholder-for-66'),
	(926,19,'_thumbnail_id','48'),
	(927,49,'_wp_attached_file','2018/05/long-sleeve-tee-2.jpg'),
	(928,49,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:801;s:4:\"file\";s:29:\"2018/05/long-sleeve-tee-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:29:\"long-sleeve-tee-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:29:\"long-sleeve-tee-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:29:\"long-sleeve-tee-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(929,49,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/long-sleeve-tee-2.jpg'),
	(930,20,'_wpcom_is_markdown','1'),
	(931,20,'_wp_old_slug','import-placeholder-for-68'),
	(932,20,'_thumbnail_id','49'),
	(933,50,'_wp_attached_file','2018/05/polo-2.jpg'),
	(934,50,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:801;s:6:\"height\";i:800;s:4:\"file\";s:18:\"2018/05/polo-2.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:18:\"polo-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:18:\"polo-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:18:\"polo-2-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:18:\"polo-2-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:18:\"polo-2-768x767.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:767;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:18:\"polo-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:18:\"polo-2-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:18:\"polo-2-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:18:\"polo-2-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(935,50,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/polo-2.jpg'),
	(936,21,'_wpcom_is_markdown','1'),
	(937,21,'_wp_old_slug','import-placeholder-for-70'),
	(938,21,'_thumbnail_id','50'),
	(939,51,'_wp_attached_file','2018/05/album-1.jpg'),
	(940,51,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:19:\"2018/05/album-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:19:\"album-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:19:\"album-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:19:\"album-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:19:\"album-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:19:\"album-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:19:\"album-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:19:\"album-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:19:\"album-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:19:\"album-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(941,51,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/album-1.jpg'),
	(942,22,'_wpcom_is_markdown','1'),
	(943,22,'_wp_old_slug','import-placeholder-for-73'),
	(944,22,'_thumbnail_id','51'),
	(945,52,'_wp_attached_file','2018/05/single-1.jpg'),
	(946,52,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:20:\"2018/05/single-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:20:\"single-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:20:\"single-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:20:\"single-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:20:\"single-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:20:\"single-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:20:\"single-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:20:\"single-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:20:\"single-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:20:\"single-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(947,52,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/single-1.jpg'),
	(948,23,'_wpcom_is_markdown','1'),
	(949,23,'_wp_old_slug','import-placeholder-for-75'),
	(950,23,'_thumbnail_id','52'),
	(951,24,'_wpcom_is_markdown',''),
	(952,24,'_wp_old_slug','import-placeholder-for-76'),
	(953,24,'_variation_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt. Donec ut metus leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed luctus, dui eu sagittis sodales, nulla nibh sagittis augue, vel porttitor diam enim non metus. Vestibulum aliquam augue neque. Phasellus tincidunt odio eget ullamcorper efficitur. Cras placerat ut turpis pellentesque vulputate. Nam sed consequat tortor. Curabitur finibus sapien dolor. Ut eleifend tellus nec erat pulvinar dignissim. Nam non arcu purus. Vivamus et massa massa.'),
	(954,24,'_thumbnail_id','35'),
	(955,24,'attribute_pa_color','red'),
	(956,24,'attribute_pa_size',''),
	(957,25,'_wpcom_is_markdown',''),
	(958,25,'_wp_old_slug','import-placeholder-for-77'),
	(959,25,'_variation_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt. Donec ut metus leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed luctus, dui eu sagittis sodales, nulla nibh sagittis augue, vel porttitor diam enim non metus. Vestibulum aliquam augue neque. Phasellus tincidunt odio eget ullamcorper efficitur. Cras placerat ut turpis pellentesque vulputate. Nam sed consequat tortor. Curabitur finibus sapien dolor. Ut eleifend tellus nec erat pulvinar dignissim. Nam non arcu purus. Vivamus et massa massa.'),
	(960,25,'_thumbnail_id','36'),
	(961,25,'attribute_pa_color','green'),
	(962,25,'attribute_pa_size',''),
	(963,26,'_wpcom_is_markdown',''),
	(964,26,'_wp_old_slug','import-placeholder-for-78'),
	(965,26,'_variation_description','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt. Donec ut metus leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed luctus, dui eu sagittis sodales, nulla nibh sagittis augue, vel porttitor diam enim non metus. Vestibulum aliquam augue neque. Phasellus tincidunt odio eget ullamcorper efficitur. Cras placerat ut turpis pellentesque vulputate. Nam sed consequat tortor. Curabitur finibus sapien dolor. Ut eleifend tellus nec erat pulvinar dignissim. Nam non arcu purus. Vivamus et massa massa.'),
	(966,26,'_thumbnail_id','37'),
	(967,26,'attribute_pa_color','blue'),
	(968,26,'attribute_pa_size',''),
	(987,53,'_wp_attached_file','2018/05/t-shirt-with-logo-1.jpg'),
	(988,53,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:31:\"2018/05/t-shirt-with-logo-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:31:\"t-shirt-with-logo-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(989,53,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/t-shirt-with-logo-1.jpg'),
	(990,30,'_wpcom_is_markdown','1'),
	(991,30,'_wp_old_slug','import-placeholder-for-83'),
	(992,30,'_thumbnail_id','53'),
	(993,54,'_wp_attached_file','2018/05/beanie-with-logo-1.jpg'),
	(994,54,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:30:\"2018/05/beanie-with-logo-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:30:\"beanie-with-logo-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:30:\"beanie-with-logo-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:30:\"beanie-with-logo-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(995,54,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/beanie-with-logo-1.jpg'),
	(996,31,'_wpcom_is_markdown','1'),
	(997,31,'_wp_old_slug','import-placeholder-for-85'),
	(998,31,'_thumbnail_id','54'),
	(999,55,'_wp_attached_file','2018/05/logo-1.jpg'),
	(1000,55,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:799;s:4:\"file\";s:18:\"2018/05/logo-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:18:\"logo-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:18:\"logo-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:18:\"logo-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:18:\"logo-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:18:\"logo-1-768x767.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:767;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:18:\"logo-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:18:\"logo-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:18:\"logo-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:18:\"logo-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(1001,55,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/logo-1.jpg'),
	(1002,32,'_wpcom_is_markdown','1'),
	(1003,32,'_wp_old_slug','import-placeholder-for-87'),
	(1004,32,'_children','a:3:{i:0;i:12;i:1;i:13;i:2;i:14;}'),
	(1005,32,'_thumbnail_id','55'),
	(1006,32,'_price',''),
	(1007,32,'_price',''),
	(1008,56,'_wp_attached_file','2018/05/pennant-1.jpg'),
	(1009,56,'_wp_attachment_metadata','a:5:{s:5:\"width\";i:800;s:6:\"height\";i:800;s:4:\"file\";s:21:\"2018/05/pennant-1.jpg\";s:5:\"sizes\";a:9:{s:21:\"woocommerce_thumbnail\";a:5:{s:4:\"file\";s:21:\"pennant-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:18:\"woocommerce_single\";a:4:{s:4:\"file\";s:21:\"pennant-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:9:\"thumbnail\";a:4:{s:4:\"file\";s:21:\"pennant-1-150x150.jpg\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:6:\"medium\";a:4:{s:4:\"file\";s:21:\"pennant-1-300x300.jpg\";s:5:\"width\";i:300;s:6:\"height\";i:300;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"medium_large\";a:4:{s:4:\"file\";s:21:\"pennant-1-768x768.jpg\";s:5:\"width\";i:768;s:6:\"height\";i:768;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:29:\"woocommerce_gallery_thumbnail\";a:4:{s:4:\"file\";s:21:\"pennant-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:12:\"shop_catalog\";a:5:{s:4:\"file\";s:21:\"pennant-1-250x250.jpg\";s:5:\"width\";i:250;s:6:\"height\";i:250;s:9:\"mime-type\";s:10:\"image/jpeg\";s:9:\"uncropped\";b:1;}s:11:\"shop_single\";a:4:{s:4:\"file\";s:21:\"pennant-1-350x350.jpg\";s:5:\"width\";i:350;s:6:\"height\";i:350;s:9:\"mime-type\";s:10:\"image/jpeg\";}s:14:\"shop_thumbnail\";a:4:{s:4:\"file\";s:21:\"pennant-1-100x100.jpg\";s:5:\"width\";i:100;s:6:\"height\";i:100;s:9:\"mime-type\";s:10:\"image/jpeg\";}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}'),
	(1010,56,'_wc_attachment_source','https://woocommercecore.mystagingwebsite.com/wp-content/uploads/2017/12/pennant-1.jpg'),
	(1011,33,'_wpcom_is_markdown','1'),
	(1012,33,'_wp_old_slug','import-placeholder-for-89'),
	(1013,33,'_thumbnail_id','56'),
	(1014,33,'_product_url','https://mercantile.wordpress.org/product/wordpress-pennant/'),
	(1015,33,'_button_text','Buy on the WordPress swag store!'),
	(1034,10,'_edit_lock','1527801136:1'),
	(1037,10,'_edit_last','1'),
	(1038,11,'_edit_lock','1526613527:1'),
	(1249,11,'_edit_last','1'),
	(1250,63,'_variation_description',''),
	(1251,63,'_sku',''),
	(1252,63,'_regular_price','30'),
	(1253,63,'_sale_price',''),
	(1254,63,'_sale_price_dates_from',''),
	(1255,63,'_sale_price_dates_to',''),
	(1256,63,'total_sales','0'),
	(1257,63,'_tax_status','taxable'),
	(1258,63,'_tax_class','parent'),
	(1259,63,'_manage_stock','no'),
	(1260,63,'_backorders','no'),
	(1261,63,'_sold_individually','no'),
	(1262,63,'_weight',''),
	(1263,63,'_length',''),
	(1264,63,'_width',''),
	(1265,63,'_height',''),
	(1266,63,'_upsell_ids','a:0:{}'),
	(1267,63,'_crosssell_ids','a:0:{}'),
	(1268,63,'_purchase_note',''),
	(1269,63,'_default_attributes','a:0:{}'),
	(1270,63,'_virtual','no'),
	(1271,63,'_downloadable','no'),
	(1272,63,'_product_image_gallery',''),
	(1273,63,'_download_limit','-1'),
	(1274,63,'_download_expiry','-1'),
	(1275,63,'_stock',NULL),
	(1276,63,'_stock_status','outofstock'),
	(1277,63,'_wc_average_rating','0'),
	(1278,63,'_wc_rating_count','a:0:{}'),
	(1279,63,'_wc_review_count','0'),
	(1280,63,'_downloadable_files','a:0:{}'),
	(1281,63,'attribute_pa_color','blue'),
	(1282,63,'_price','30'),
	(1283,63,'_product_version','3.4.0'),
	(1284,64,'_variation_description',''),
	(1285,64,'_sku',''),
	(1286,64,'_regular_price','30'),
	(1287,64,'_sale_price',''),
	(1288,64,'_sale_price_dates_from',''),
	(1289,64,'_sale_price_dates_to',''),
	(1290,64,'total_sales','0'),
	(1291,64,'_tax_status','taxable'),
	(1292,64,'_tax_class','parent'),
	(1293,64,'_manage_stock','no'),
	(1294,64,'_backorders','no'),
	(1295,64,'_sold_individually','no'),
	(1296,64,'_weight',''),
	(1297,64,'_length',''),
	(1298,64,'_width',''),
	(1299,64,'_height',''),
	(1300,64,'_upsell_ids','a:0:{}'),
	(1301,64,'_crosssell_ids','a:0:{}'),
	(1302,64,'_purchase_note',''),
	(1303,64,'_default_attributes','a:0:{}'),
	(1304,64,'_virtual','no'),
	(1305,64,'_downloadable','no'),
	(1306,64,'_product_image_gallery',''),
	(1307,64,'_download_limit','-1'),
	(1308,64,'_download_expiry','-1'),
	(1309,64,'_stock',NULL),
	(1310,64,'_stock_status','instock'),
	(1311,64,'_wc_average_rating','0'),
	(1312,64,'_wc_rating_count','a:0:{}'),
	(1313,64,'_wc_review_count','0'),
	(1314,64,'_downloadable_files','a:0:{}'),
	(1315,64,'attribute_pa_color','green'),
	(1316,64,'_price','30'),
	(1317,64,'_product_version','3.4.0'),
	(1318,65,'_variation_description',''),
	(1319,65,'_sku',''),
	(1320,65,'_regular_price','30'),
	(1321,65,'_sale_price',''),
	(1322,65,'_sale_price_dates_from',''),
	(1323,65,'_sale_price_dates_to',''),
	(1324,65,'total_sales','0'),
	(1325,65,'_tax_status','taxable'),
	(1326,65,'_tax_class','parent'),
	(1327,65,'_manage_stock','no'),
	(1328,65,'_backorders','no'),
	(1329,65,'_sold_individually','no'),
	(1330,65,'_weight',''),
	(1331,65,'_length',''),
	(1332,65,'_width',''),
	(1333,65,'_height',''),
	(1334,65,'_upsell_ids','a:0:{}'),
	(1335,65,'_crosssell_ids','a:0:{}'),
	(1336,65,'_purchase_note',''),
	(1337,65,'_default_attributes','a:0:{}'),
	(1338,65,'_virtual','no'),
	(1339,65,'_downloadable','no'),
	(1340,65,'_product_image_gallery',''),
	(1341,65,'_download_limit','-1'),
	(1342,65,'_download_expiry','-1'),
	(1343,65,'_stock',NULL),
	(1344,65,'_stock_status','instock'),
	(1345,65,'_wc_average_rating','0'),
	(1346,65,'_wc_rating_count','a:0:{}'),
	(1347,65,'_wc_review_count','0'),
	(1348,65,'_downloadable_files','a:0:{}'),
	(1349,65,'attribute_pa_color','red'),
	(1350,65,'_price','30'),
	(1351,65,'_product_version','3.4.0'),
	(1355,11,'_price','30'),
	(1356,31,'_edit_lock','1526607709:1'),
	(1357,31,'_edit_last','1'),
	(1358,30,'_edit_lock','1526613541:1'),
	(1359,30,'woocommerce_waitlist','a:1:{i:2;i:1527176210;}'),
	(1360,30,'woocommerce_waitlist_has_dates','1'),
	(1361,30,'_woocommerce_waitlist_count','1'),
	(1362,63,'woocommerce_waitlist','a:1:{i:2;i:1527176283;}'),
	(1363,63,'woocommerce_waitlist_has_dates','1'),
	(1364,63,'_woocommerce_waitlist_count','1'),
	(1365,11,'_woocommerce_waitlist_count','1'),
	(1366,13,'_edit_lock','1526611156:1'),
	(1367,13,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";b:0;}'),
	(1368,13,'_edit_last','1'),
	(1369,14,'woocommerce_waitlist','a:0:{}'),
	(1370,14,'woocommerce_waitlist_has_dates','1'),
	(1371,14,'_woocommerce_waitlist_count','0'),
	(1372,32,'_woocommerce_waitlist_count','4'),
	(1373,12,'woocommerce_waitlist','a:4:{i:2;i:1527176073;i:4;i:1527186651;i:5;i:1527186659;i:6;i:1527186675;}'),
	(1374,12,'woocommerce_waitlist_has_dates','1'),
	(1375,12,'_woocommerce_waitlist_count','4'),
	(1376,63,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";b:0;}'),
	(1377,64,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";b:0;}'),
	(1378,65,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";b:0;}'),
	(1379,30,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";b:0;}'),
	(1380,12,'_edit_lock','1527186535:1'),
	(1381,12,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";b:0;}'),
	(1382,22,'woocommerce_waitlist','a:2:{i:3;i:1527176154;i:7;i:1527186697;}'),
	(1383,22,'woocommerce_waitlist_has_dates','1'),
	(1384,22,'_woocommerce_waitlist_count','2'),
	(1385,17,'_edit_lock','1527176514:1'),
	(1386,17,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";s:1:\"1\";}'),
	(1387,17,'woocommerce_waitlist','a:0:{}'),
	(1388,17,'woocommerce_waitlist_has_dates','1'),
	(1389,17,'_woocommerce_waitlist_count','0'),
	(1390,17,'_edit_last','1'),
	(1391,17,'wcwl_waitlist_archive','a:1:{i:1527120000;a:1:{i:1;i:1;}}'),
	(1392,66,'_edit_last','1'),
	(1393,66,'_edit_lock','1527173485:1'),
	(1394,68,'_edit_last','1'),
	(1395,68,'_edit_lock','1527173686:1'),
	(1396,65,'_woocommerce_waitlist_count','0'),
	(1397,64,'_woocommerce_waitlist_count','0'),
	(1398,33,'_woocommerce_waitlist_count','0'),
	(1399,25,'_woocommerce_waitlist_count','0'),
	(1400,26,'_woocommerce_waitlist_count','0'),
	(1401,31,'_woocommerce_waitlist_count','0'),
	(1402,24,'_woocommerce_waitlist_count','0'),
	(1403,23,'_woocommerce_waitlist_count','0'),
	(1404,21,'_woocommerce_waitlist_count','0'),
	(1405,20,'_woocommerce_waitlist_count','0'),
	(1406,19,'_woocommerce_waitlist_count','0'),
	(1407,18,'_woocommerce_waitlist_count','0'),
	(1408,13,'_woocommerce_waitlist_count','0'),
	(1409,15,'_woocommerce_waitlist_count','0'),
	(1410,16,'_woocommerce_waitlist_count','0'),
	(1411,10,'_woocommerce_waitlist_count','0'),
	(1412,22,'_edit_lock','1527186555:1'),
	(1413,22,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";s:1:\"1\";}'),
	(1414,25,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";s:1:\"1\";}'),
	(1415,24,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";s:1:\"1\";}'),
	(1416,26,'wcwl_options','a:3:{s:15:\"enable_waitlist\";s:4:\"true\";s:20:\"enable_stock_trigger\";s:5:\"false\";s:13:\"minimum_stock\";s:1:\"1\";}'),
	(1417,10,'_price','15'),
	(1418,10,'_price','20');

/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_posts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_posts`;

CREATE TABLE `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_posts` WRITE;
/*!40000 ALTER TABLE `wp_posts` DISABLE KEYS */;

INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`)
VALUES
	(1,1,'2018-05-18 01:34:24','2018-05-18 01:34:24','Welcome to WordPress. This is your first post. Edit or delete it, then start writing!','Hello world!','','publish','open','open','','hello-world','','','2018-05-18 01:34:24','2018-05-18 01:34:24','',0,'http://waitlisttest.local/?p=1',0,'post','',1),
	(2,1,'2018-05-18 01:34:24','2018-05-18 01:34:24','This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\n\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</blockquote>\n\n...or something like this:\n\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\n\nAs a new WordPress user, you should go to <a href=\"http://waitlisttest.local/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!','Sample Page','','publish','closed','open','','sample-page','','','2018-05-18 01:34:24','2018-05-18 01:34:24','',0,'http://waitlisttest.local/?page_id=2',0,'page','',0),
	(3,1,'2018-05-18 01:34:24','2018-05-18 01:34:24','<h2>Who we are</h2><p>Our website address is: http://waitlisttest.local.</p><h2>What personal data we collect and why we collect it</h2><h3>Comments</h3><p>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p><p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p><h3>Media</h3><p>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p><h3>Contact forms</h3><h3>Cookies</h3><p>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p><p>If you have an account and you log in to this site, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p><p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p><p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p><h3>Embedded content from other websites</h3><p>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p><p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracing your interaction with the embedded content if you have an account and are logged in to that website.</p><h3>Analytics</h3><h2>Who we share your data with</h2><h2>How long we retain your data</h2><p>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p><p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p><h2>What rights you have over your data</h2><p>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p><h2>Where we send your data</h2><p>Visitor comments may be checked through an automated spam detection service.</p><h2>Your contact information</h2><h2>Additional information</h2><h3>How we protect your data</h3><h3>What data breach procedures we have in place</h3><h3>What third parties we receive data from</h3><h3>What automated decision making and/or profiling we do with user data</h3><h3>Industry regulatory disclosure requirements</h3>','Privacy Policy','','draft','closed','open','','privacy-policy','','','2018-05-18 01:34:24','2018-05-18 01:34:24','',0,'http://waitlisttest.local/?page_id=3',0,'page','',0),
	(5,1,'2018-05-18 01:38:10','2018-05-18 01:38:10','','Shop','','publish','closed','closed','','shop','','','2018-05-18 01:38:10','2018-05-18 01:38:10','',0,'http://waitlisttest.local/shop/',0,'page','',0),
	(6,1,'2018-05-18 01:38:10','2018-05-18 01:38:10','[woocommerce_cart]','Cart','','publish','closed','closed','','cart','','','2018-05-18 01:38:10','2018-05-18 01:38:10','',0,'http://waitlisttest.local/cart/',0,'page','',0),
	(7,1,'2018-05-18 01:38:10','2018-05-18 01:38:10','[woocommerce_checkout]','Checkout','','publish','closed','closed','','checkout','','','2018-05-18 01:38:10','2018-05-18 01:38:10','',0,'http://waitlisttest.local/checkout/',0,'page','',0),
	(8,1,'2018-05-18 01:38:10','2018-05-18 01:38:10','[woocommerce_my_account]','My account','','publish','closed','closed','','my-account','','','2018-05-18 01:38:10','2018-05-18 01:38:10','',0,'http://waitlisttest.local/my-account/',0,'page','',0),
	(10,1,'2018-05-18 01:39:07','2018-05-18 01:39:07','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','V-Neck T-Shirt','This is a variable product.','publish','open','closed','','v-neck-t-shirt','','','2018-05-31 21:11:59','2018-05-31 21:11:59','',0,'http://waitlisttest.local/product/import-placeholder-for-44/',0,'product','',0),
	(11,1,'2018-05-18 01:39:07','2018-05-18 01:39:07','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Hoodie','This is a variable product.','publish','open','closed','','hoodie','','','2018-05-18 01:42:16','2018-05-18 01:42:16','',0,'http://waitlisttest.local/product/import-placeholder-for-45/',0,'product','',0),
	(12,1,'2018-05-18 01:39:08','2018-05-18 01:39:08','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Hoodie with Logo','This is a simple product.','publish','open','closed','','hoodie-with-logo','','','2018-05-18 01:39:58','2018-05-18 01:39:58','',0,'http://waitlisttest.local/product/import-placeholder-for-46/',0,'product','',0),
	(13,1,'2018-05-18 01:39:08','2018-05-18 01:39:08','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','T-Shirt','This is a simple product.','publish','open','closed','','t-shirt','','','2018-05-18 02:38:59','2018-05-18 02:38:59','',0,'http://waitlisttest.local/product/import-placeholder-for-47/',0,'product','',0),
	(14,1,'2018-05-18 01:39:08','2018-05-18 01:39:08','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Beanie','This is a simple product.','publish','open','closed','','beanie','','','2018-05-18 01:39:58','2018-05-18 01:39:58','',0,'http://waitlisttest.local/product/import-placeholder-for-48/',0,'product','',0),
	(15,1,'2018-05-18 01:39:08','2018-05-18 01:39:08','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Belt','This is a simple product.','publish','open','closed','','belt','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-58/',0,'product','',0),
	(16,1,'2018-05-18 01:39:08','2018-05-18 01:39:08','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Cap','This is a simple product.','publish','open','closed','','cap','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-60/',0,'product','',0),
	(17,1,'2018-05-18 01:39:08','2018-05-18 01:39:08','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Sunglasses','This is a simple product.','publish','open','closed','','sunglasses','','','2018-05-24 15:41:38','2018-05-24 15:41:38','',0,'http://waitlisttest.local/product/import-placeholder-for-62/',0,'product','',0),
	(18,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Hoodie with Pocket','This is a simple product.','publish','open','closed','','hoodie-with-pocket','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-64/',0,'product','',0),
	(19,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Hoodie with Zipper','This is a simple product.','publish','open','closed','','hoodie-with-zipper','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-66/',0,'product','',0),
	(20,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Long Sleeve Tee','This is a simple product.','publish','open','closed','','long-sleeve-tee','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-68/',0,'product','',0),
	(21,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Polo','This is a simple product.','publish','open','closed','','polo','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-70/',0,'product','',0),
	(22,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt. Donec ut metus leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed luctus, dui eu sagittis sodales, nulla nibh sagittis augue, vel porttitor diam enim non metus. Vestibulum aliquam augue neque. Phasellus tincidunt odio eget ullamcorper efficitur. Cras placerat ut turpis pellentesque vulputate. Nam sed consequat tortor. Curabitur finibus sapien dolor. Ut eleifend tellus nec erat pulvinar dignissim. Nam non arcu purus. Vivamus et massa massa.','Album','This is a simple, virtual product.','publish','open','closed','','album','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-73/',0,'product','',0),
	(23,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sagittis orci ac odio dictum tincidunt. Donec ut metus leo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed luctus, dui eu sagittis sodales, nulla nibh sagittis augue, vel porttitor diam enim non metus. Vestibulum aliquam augue neque. Phasellus tincidunt odio eget ullamcorper efficitur. Cras placerat ut turpis pellentesque vulputate. Nam sed consequat tortor. Curabitur finibus sapien dolor. Ut eleifend tellus nec erat pulvinar dignissim. Nam non arcu purus. Vivamus et massa massa.','Single','This is a simple, virtual product.','publish','open','closed','','single','','','2018-05-18 01:39:57','2018-05-18 01:39:57','',0,'http://waitlisttest.local/product/import-placeholder-for-75/',0,'product','',0),
	(24,1,'2018-05-18 01:39:09','2018-05-18 01:39:09','','V-Neck T-Shirt - Red','','publish','closed','closed','','v-neck-t-shirt-red','','','2018-05-18 01:39:58','2018-05-18 01:39:58','',10,'http://waitlisttest.local/product/import-placeholder-for-76/',2,'product_variation','',0),
	(25,1,'2018-05-18 01:39:10','2018-05-18 01:39:10','','V-Neck T-Shirt - Green','','publish','closed','closed','','v-neck-t-shirt-green','','','2018-05-31 21:11:58','2018-05-31 21:11:58','',10,'http://waitlisttest.local/product/import-placeholder-for-77/',0,'product_variation','',0),
	(26,1,'2018-05-18 01:39:10','2018-05-18 01:39:10','','V-Neck T-Shirt - Blue','','publish','closed','closed','','v-neck-t-shirt-blue','','','2018-05-18 01:39:58','2018-05-18 01:39:58','',10,'http://waitlisttest.local/product/import-placeholder-for-78/',3,'product_variation','',0),
	(30,1,'2018-05-18 01:39:10','2018-05-18 01:39:10','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','T-Shirt with Logo','This is a simple product.','publish','open','closed','','t-shirt-with-logo','','','2018-05-18 01:39:56','2018-05-18 01:39:56','',0,'http://waitlisttest.local/product/import-placeholder-for-83/',0,'product','',0),
	(31,1,'2018-05-18 01:39:10','2018-05-18 01:39:10','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Beanie with Logo','This is a simple product.','publish','open','closed','','beanie-with-logo','','','2018-05-18 01:44:12','2018-05-18 01:44:12','',0,'http://waitlisttest.local/product/import-placeholder-for-85/',0,'product','',0),
	(32,1,'2018-05-18 01:39:11','2018-05-18 01:39:11','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','Logo Collection','This is a grouped product.','publish','open','closed','','logo-collection','','','2018-05-18 01:39:56','2018-05-18 01:39:56','',0,'http://waitlisttest.local/product/import-placeholder-for-87/',0,'product','',0),
	(33,1,'2018-05-18 01:39:11','2018-05-18 01:39:11','Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.','WordPress Pennant','This is an external product.','publish','open','closed','','wordpress-pennant','','','2018-05-18 01:39:56','2018-05-18 01:39:56','',0,'http://waitlisttest.local/product/import-placeholder-for-89/',0,'product','',0),
	(35,1,'2018-05-18 01:39:12','2018-05-18 01:39:12','','vneck-tee-2.jpg','','inherit','open','closed','','vneck-tee-2-jpg','','','2018-05-18 01:39:12','2018-05-18 01:39:12','',10,'http://waitlisttest.local/wp-content/uploads/2018/05/vneck-tee-2.jpg',0,'attachment','image/jpeg',0),
	(36,1,'2018-05-18 01:39:13','2018-05-18 01:39:13','','vnech-tee-green-1.jpg','','inherit','open','closed','','vnech-tee-green-1-jpg','','','2018-05-18 01:39:13','2018-05-18 01:39:13','',10,'http://waitlisttest.local/wp-content/uploads/2018/05/vnech-tee-green-1.jpg',0,'attachment','image/jpeg',0),
	(37,1,'2018-05-18 01:39:15','2018-05-18 01:39:15','','vnech-tee-blue-1.jpg','','inherit','open','closed','','vnech-tee-blue-1-jpg','','','2018-05-18 01:39:15','2018-05-18 01:39:15','',10,'http://waitlisttest.local/wp-content/uploads/2018/05/vnech-tee-blue-1.jpg',0,'attachment','image/jpeg',0),
	(38,1,'2018-05-18 01:39:16','2018-05-18 01:39:16','','hoodie-2.jpg','','inherit','open','closed','','hoodie-2-jpg','','','2018-05-18 01:39:16','2018-05-18 01:39:16','',11,'http://waitlisttest.local/wp-content/uploads/2018/05/hoodie-2.jpg',0,'attachment','image/jpeg',0),
	(39,1,'2018-05-18 01:39:17','2018-05-18 01:39:17','','hoodie-blue-1.jpg','','inherit','open','closed','','hoodie-blue-1-jpg','','','2018-05-18 01:39:17','2018-05-18 01:39:17','',11,'http://waitlisttest.local/wp-content/uploads/2018/05/hoodie-blue-1.jpg',0,'attachment','image/jpeg',0),
	(40,1,'2018-05-18 01:39:19','2018-05-18 01:39:19','','hoodie-green-1.jpg','','inherit','open','closed','','hoodie-green-1-jpg','','','2018-05-18 01:39:19','2018-05-18 01:39:19','',11,'http://waitlisttest.local/wp-content/uploads/2018/05/hoodie-green-1.jpg',0,'attachment','image/jpeg',0),
	(41,1,'2018-05-18 01:39:20','2018-05-18 01:39:20','','hoodie-with-logo-2.jpg','','inherit','open','closed','','hoodie-with-logo-2-jpg','','','2018-05-18 01:39:20','2018-05-18 01:39:20','',11,'http://waitlisttest.local/wp-content/uploads/2018/05/hoodie-with-logo-2.jpg',0,'attachment','image/jpeg',0),
	(42,1,'2018-05-18 01:39:21','2018-05-18 01:39:21','','tshirt-2.jpg','','inherit','open','closed','','tshirt-2-jpg','','','2018-05-18 01:39:21','2018-05-18 01:39:21','',13,'http://waitlisttest.local/wp-content/uploads/2018/05/tshirt-2.jpg',0,'attachment','image/jpeg',0),
	(43,1,'2018-05-18 01:39:22','2018-05-18 01:39:22','','beanie-2.jpg','','inherit','open','closed','','beanie-2-jpg','','','2018-05-18 01:39:22','2018-05-18 01:39:22','',14,'http://waitlisttest.local/wp-content/uploads/2018/05/beanie-2.jpg',0,'attachment','image/jpeg',0),
	(44,1,'2018-05-18 01:39:23','2018-05-18 01:39:23','','belt-2.jpg','','inherit','open','closed','','belt-2-jpg','','','2018-05-18 01:39:23','2018-05-18 01:39:23','',15,'http://waitlisttest.local/wp-content/uploads/2018/05/belt-2.jpg',0,'attachment','image/jpeg',0),
	(45,1,'2018-05-18 01:39:25','2018-05-18 01:39:25','','cap-2.jpg','','inherit','open','closed','','cap-2-jpg','','','2018-05-18 01:39:25','2018-05-18 01:39:25','',16,'http://waitlisttest.local/wp-content/uploads/2018/05/cap-2.jpg',0,'attachment','image/jpeg',0),
	(46,1,'2018-05-18 01:39:26','2018-05-18 01:39:26','','sunglasses-2.jpg','','inherit','open','closed','','sunglasses-2-jpg','','','2018-05-18 01:39:26','2018-05-18 01:39:26','',17,'http://waitlisttest.local/wp-content/uploads/2018/05/sunglasses-2.jpg',0,'attachment','image/jpeg',0),
	(47,1,'2018-05-18 01:39:27','2018-05-18 01:39:27','','hoodie-with-pocket-2.jpg','','inherit','open','closed','','hoodie-with-pocket-2-jpg','','','2018-05-18 01:39:27','2018-05-18 01:39:27','',18,'http://waitlisttest.local/wp-content/uploads/2018/05/hoodie-with-pocket-2.jpg',0,'attachment','image/jpeg',0),
	(48,1,'2018-05-18 01:39:28','2018-05-18 01:39:28','','hoodie-with-zipper-2.jpg','','inherit','open','closed','','hoodie-with-zipper-2-jpg','','','2018-05-18 01:39:28','2018-05-18 01:39:28','',19,'http://waitlisttest.local/wp-content/uploads/2018/05/hoodie-with-zipper-2.jpg',0,'attachment','image/jpeg',0),
	(49,1,'2018-05-18 01:39:30','2018-05-18 01:39:30','','long-sleeve-tee-2.jpg','','inherit','open','closed','','long-sleeve-tee-2-jpg','','','2018-05-18 01:39:30','2018-05-18 01:39:30','',20,'http://waitlisttest.local/wp-content/uploads/2018/05/long-sleeve-tee-2.jpg',0,'attachment','image/jpeg',0),
	(50,1,'2018-05-18 01:39:31','2018-05-18 01:39:31','','polo-2.jpg','','inherit','open','closed','','polo-2-jpg','','','2018-05-18 01:39:31','2018-05-18 01:39:31','',21,'http://waitlisttest.local/wp-content/uploads/2018/05/polo-2.jpg',0,'attachment','image/jpeg',0),
	(51,1,'2018-05-18 01:39:33','2018-05-18 01:39:33','','album-1.jpg','','inherit','open','closed','','album-1-jpg','','','2018-05-18 01:39:33','2018-05-18 01:39:33','',22,'http://waitlisttest.local/wp-content/uploads/2018/05/album-1.jpg',0,'attachment','image/jpeg',0),
	(52,1,'2018-05-18 01:39:34','2018-05-18 01:39:34','','single-1.jpg','','inherit','open','closed','','single-1-jpg','','','2018-05-18 01:39:34','2018-05-18 01:39:34','',23,'http://waitlisttest.local/wp-content/uploads/2018/05/single-1.jpg',0,'attachment','image/jpeg',0),
	(53,1,'2018-05-18 01:39:36','2018-05-18 01:39:36','','t-shirt-with-logo-1.jpg','','inherit','open','closed','','t-shirt-with-logo-1-jpg','','','2018-05-18 01:39:36','2018-05-18 01:39:36','',30,'http://waitlisttest.local/wp-content/uploads/2018/05/t-shirt-with-logo-1.jpg',0,'attachment','image/jpeg',0),
	(54,1,'2018-05-18 01:39:37','2018-05-18 01:39:37','','beanie-with-logo-1.jpg','','inherit','open','closed','','beanie-with-logo-1-jpg','','','2018-05-18 01:39:37','2018-05-18 01:39:37','',31,'http://waitlisttest.local/wp-content/uploads/2018/05/beanie-with-logo-1.jpg',0,'attachment','image/jpeg',0),
	(55,1,'2018-05-18 01:39:39','2018-05-18 01:39:39','','logo-1.jpg','','inherit','open','closed','','logo-1-jpg','','','2018-05-18 01:39:39','2018-05-18 01:39:39','',32,'http://waitlisttest.local/wp-content/uploads/2018/05/logo-1.jpg',0,'attachment','image/jpeg',0),
	(56,1,'2018-05-18 01:39:40','2018-05-18 01:39:40','','pennant-1.jpg','','inherit','open','closed','','pennant-1-jpg','','','2018-05-18 01:39:40','2018-05-18 01:39:40','',33,'http://waitlisttest.local/wp-content/uploads/2018/05/pennant-1.jpg',0,'attachment','image/jpeg',0),
	(63,1,'2018-05-18 01:41:40','2018-05-18 01:41:40','','Hoodie - Blue','','publish','closed','closed','','hoodie-blue','','','2018-05-18 01:42:12','2018-05-18 01:42:12','',11,'http://waitlisttest.local/product/hoodie/',1,'product_variation','',0),
	(64,1,'2018-05-18 01:41:40','2018-05-18 01:41:40','','Hoodie - Green','','publish','closed','closed','','hoodie-green','','','2018-05-18 01:41:55','2018-05-18 01:41:55','',11,'http://waitlisttest.local/product/hoodie/',2,'product_variation','',0),
	(65,1,'2018-05-18 01:41:40','2018-05-18 01:41:40','','Hoodie - Red','','publish','closed','closed','','hoodie-red','','','2018-05-18 01:41:55','2018-05-18 01:41:55','',11,'http://waitlisttest.local/product/hoodie/',3,'product_variation','',0),
	(66,1,'2018-05-24 14:53:38','2018-05-24 14:53:38','[product_page id=\"12\"]','Product Shortcode','','publish','closed','closed','','product-shortcode','','','2018-05-24 14:53:38','2018-05-24 14:53:38','',0,'http://waitlisttest.local/?page_id=66',0,'page','',0),
	(67,1,'2018-05-24 14:53:38','2018-05-24 14:53:38','[product_page id=\"12\"]','Product Shortcode','','inherit','closed','closed','','66-revision-v1','','','2018-05-24 14:53:38','2018-05-24 14:53:38','',66,'http://waitlisttest.local/66-revision-v1/',0,'revision','',0),
	(68,1,'2018-05-24 14:57:09','2018-05-24 14:57:09','[woocommerce_my_waitlist]','Waitlist Shortcode','','publish','closed','closed','','waitlist-shortcode','','','2018-05-24 14:57:09','2018-05-24 14:57:09','',0,'http://waitlisttest.local/?page_id=68',0,'page','',0),
	(69,1,'2018-05-24 14:57:09','2018-05-24 14:57:09','[woocommerce_my_waitlist]','Waitlist Shortcode','','inherit','closed','closed','','68-revision-v1','','','2018-05-24 14:57:09','2018-05-24 14:57:09','',68,'http://waitlisttest.local/68-revision-v1/',0,'revision','',0);

/*!40000 ALTER TABLE `wp_posts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_term_relationships
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_term_relationships`;

CREATE TABLE `wp_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_term_relationships` WRITE;
/*!40000 ALTER TABLE `wp_term_relationships` DISABLE KEYS */;

INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`)
VALUES
	(1,1,0),
	(10,4,0),
	(10,8,0),
	(10,9,0),
	(10,17,0),
	(10,22,0),
	(10,23,0),
	(10,24,0),
	(10,25,0),
	(10,26,0),
	(10,27,0),
	(11,4,0),
	(11,18,0),
	(11,22,0),
	(11,23,0),
	(11,24,0),
	(12,2,0),
	(12,9,0),
	(12,18,0),
	(12,22,0),
	(13,2,0),
	(13,17,0),
	(13,28,0),
	(14,2,0),
	(14,9,0),
	(14,19,0),
	(14,24,0),
	(15,2,0),
	(15,9,0),
	(15,19,0),
	(16,2,0),
	(16,8,0),
	(16,9,0),
	(16,19,0),
	(16,29,0),
	(17,2,0),
	(17,8,0),
	(17,19,0),
	(18,2,0),
	(18,6,0),
	(18,7,0),
	(18,8,0),
	(18,9,0),
	(18,18,0),
	(18,28,0),
	(19,2,0),
	(19,8,0),
	(19,9,0),
	(19,18,0),
	(20,2,0),
	(20,9,0),
	(20,17,0),
	(20,23,0),
	(21,2,0),
	(21,9,0),
	(21,17,0),
	(21,22,0),
	(22,2,0),
	(22,9,0),
	(22,20,0),
	(23,2,0),
	(23,9,0),
	(23,20,0),
	(24,9,0),
	(24,15,0),
	(25,9,0),
	(25,15,0),
	(26,9,0),
	(26,15,0),
	(27,15,0),
	(28,15,0),
	(29,15,0),
	(30,2,0),
	(30,9,0),
	(30,17,0),
	(30,28,0),
	(31,2,0),
	(31,19,0),
	(31,24,0),
	(32,3,0),
	(32,9,0),
	(32,16,0),
	(33,5,0),
	(33,21,0),
	(34,15,0),
	(63,9,0);

/*!40000 ALTER TABLE `wp_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_term_taxonomy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_term_taxonomy`;

CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;

INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`)
VALUES
	(1,1,'category','',0,1),
	(2,2,'product_type','',0,14),
	(3,3,'product_type','',0,1),
	(4,4,'product_type','',0,2),
	(5,5,'product_type','',0,1),
	(6,6,'product_visibility','',0,1),
	(7,7,'product_visibility','',0,1),
	(8,8,'product_visibility','',0,5),
	(9,9,'product_visibility','',0,17),
	(10,10,'product_visibility','',0,0),
	(11,11,'product_visibility','',0,0),
	(12,12,'product_visibility','',0,0),
	(13,13,'product_visibility','',0,0),
	(14,14,'product_visibility','',0,0),
	(15,15,'product_cat','',0,0),
	(16,16,'product_cat','',0,1),
	(17,17,'product_cat','',16,5),
	(18,18,'product_cat','',16,4),
	(19,19,'product_cat','',16,5),
	(20,20,'product_cat','',0,2),
	(21,21,'product_cat','',0,1),
	(22,22,'pa_color','',0,4),
	(23,23,'pa_color','',0,3),
	(24,24,'pa_color','',0,4),
	(25,25,'pa_size','',0,1),
	(26,26,'pa_size','',0,1),
	(27,27,'pa_size','',0,1),
	(28,28,'pa_color','',0,3),
	(29,29,'pa_color','',0,1);

/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_termmeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_termmeta`;

CREATE TABLE `wp_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_termmeta` WRITE;
/*!40000 ALTER TABLE `wp_termmeta` DISABLE KEYS */;

INSERT INTO `wp_termmeta` (`meta_id`, `term_id`, `meta_key`, `meta_value`)
VALUES
	(1,15,'product_count_product_cat','0'),
	(2,16,'order','0'),
	(3,17,'order','0'),
	(4,18,'order','0'),
	(5,19,'order','0'),
	(6,20,'order','0'),
	(7,21,'order','0'),
	(8,17,'product_count_product_cat','5'),
	(9,16,'product_count_product_cat','14'),
	(10,22,'order_pa_color','0'),
	(11,23,'order_pa_color','0'),
	(12,24,'order_pa_color','0'),
	(13,25,'order_pa_size','0'),
	(14,26,'order_pa_size','0'),
	(15,27,'order_pa_size','0'),
	(16,18,'product_count_product_cat','3'),
	(17,28,'order_pa_color','0'),
	(18,19,'product_count_product_cat','5'),
	(19,29,'order_pa_color','0'),
	(20,20,'product_count_product_cat','2'),
	(21,21,'product_count_product_cat','1');

/*!40000 ALTER TABLE `wp_termmeta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_terms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_terms`;

CREATE TABLE `wp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_terms` WRITE;
/*!40000 ALTER TABLE `wp_terms` DISABLE KEYS */;

INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`)
VALUES
	(1,'Uncategorized','uncategorized',0),
	(2,'simple','simple',0),
	(3,'grouped','grouped',0),
	(4,'variable','variable',0),
	(5,'external','external',0),
	(6,'exclude-from-search','exclude-from-search',0),
	(7,'exclude-from-catalog','exclude-from-catalog',0),
	(8,'featured','featured',0),
	(9,'outofstock','outofstock',0),
	(10,'rated-1','rated-1',0),
	(11,'rated-2','rated-2',0),
	(12,'rated-3','rated-3',0),
	(13,'rated-4','rated-4',0),
	(14,'rated-5','rated-5',0),
	(15,'Uncategorized','uncategorized',0),
	(16,'Clothing','clothing',0),
	(17,'Tshirts','tshirts',0),
	(18,'Hoodies','hoodies',0),
	(19,'Accessories','accessories',0),
	(20,'Music','music',0),
	(21,'Decor','decor',0),
	(22,'Blue','blue',0),
	(23,'Green','green',0),
	(24,'Red','red',0),
	(25,'Large','large',0),
	(26,'Medium','medium',0),
	(27,'Small','small',0),
	(28,'Gray','gray',0),
	(29,'Yellow','yellow',0);

/*!40000 ALTER TABLE `wp_terms` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_usermeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_usermeta`;

CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;

INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`)
VALUES
	(1,1,'nickname','joey'),
	(2,1,'first_name',''),
	(3,1,'last_name',''),
	(4,1,'description',''),
	(5,1,'rich_editing','true'),
	(6,1,'syntax_highlighting','true'),
	(7,1,'comment_shortcuts','false'),
	(8,1,'admin_color','fresh'),
	(9,1,'use_ssl','0'),
	(10,1,'show_admin_bar_front','true'),
	(11,1,'locale',''),
	(12,1,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}'),
	(13,1,'wp_user_level','10'),
	(14,1,'dismissed_wp_pointers','wp496_privacy'),
	(15,1,'show_welcome_panel','1'),
	(16,1,'session_tokens','a:1:{s:64:\"4c508980d26f5f578422e2c3b9ad61e00f6e9b75fa53e44316dffbec7bda4653\";a:4:{s:10:\"expiration\";i:1527973903;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:121:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36\";s:5:\"login\";i:1527801103;}}'),
	(17,1,'wp_dashboard_quick_press_last_post_id','4'),
	(18,1,'community-events-location','a:1:{s:2:\"ip\";s:12:\"192.168.75.0\";}'),
	(19,1,'_woocommerce_persistent_cart_1','a:1:{s:4:\"cart\";a:1:{s:32:\"12109b097d777d5d7c0dd97527f89214\";a:11:{s:3:\"key\";s:32:\"12109b097d777d5d7c0dd97527f89214\";s:10:\"product_id\";i:11;s:12:\"variation_id\";i:64;s:9:\"variation\";a:1:{s:18:\"attribute_pa_color\";s:5:\"green\";}s:8:\"quantity\";i:4;s:9:\"data_hash\";s:32:\"9d5a1e37109e53bd5611b6b4439c7d2f\";s:13:\"line_tax_data\";a:2:{s:8:\"subtotal\";a:0:{}s:5:\"total\";a:0:{}}s:13:\"line_subtotal\";d:120;s:17:\"line_subtotal_tax\";i:0;s:10:\"line_total\";d:120;s:8:\"line_tax\";i:0;}}}'),
	(20,1,'dismissed_no_secure_connection_notice','1'),
	(21,1,'wp_woocommerce_product_import_mapping','a:51:{i:0;s:2:\"id\";i:1;s:4:\"type\";i:2;s:3:\"sku\";i:3;s:4:\"name\";i:4;s:9:\"published\";i:5;s:8:\"featured\";i:6;s:18:\"catalog_visibility\";i:7;s:17:\"short_description\";i:8;s:11:\"description\";i:9;s:17:\"date_on_sale_from\";i:10;s:15:\"date_on_sale_to\";i:11;s:10:\"tax_status\";i:12;s:9:\"tax_class\";i:13;s:12:\"stock_status\";i:14;s:14:\"stock_quantity\";i:15;s:10:\"backorders\";i:16;s:17:\"sold_individually\";i:17;s:0:\"\";i:18;s:0:\"\";i:19;s:0:\"\";i:20;s:0:\"\";i:21;s:15:\"reviews_allowed\";i:22;s:13:\"purchase_note\";i:23;s:10:\"sale_price\";i:24;s:13:\"regular_price\";i:25;s:12:\"category_ids\";i:26;s:7:\"tag_ids\";i:27;s:17:\"shipping_class_id\";i:28;s:6:\"images\";i:29;s:14:\"download_limit\";i:30;s:15:\"download_expiry\";i:31;s:9:\"parent_id\";i:32;s:16:\"grouped_products\";i:33;s:10:\"upsell_ids\";i:34;s:14:\"cross_sell_ids\";i:35;s:11:\"product_url\";i:36;s:11:\"button_text\";i:37;s:10:\"menu_order\";i:38;s:16:\"attributes:name1\";i:39;s:17:\"attributes:value1\";i:40;s:19:\"attributes:visible1\";i:41;s:20:\"attributes:taxonomy1\";i:42;s:16:\"attributes:name2\";i:43;s:17:\"attributes:value2\";i:44;s:19:\"attributes:visible2\";i:45;s:20:\"attributes:taxonomy2\";i:46;s:23:\"meta:_wpcom_is_markdown\";i:47;s:15:\"downloads:name1\";i:48;s:14:\"downloads:url1\";i:49;s:15:\"downloads:name2\";i:50;s:14:\"downloads:url2\";}'),
	(22,1,'wp_product_import_error_log','a:0:{}'),
	(23,1,'wc_last_active','1527724800'),
	(24,1,'edit_product_per_page','20'),
	(25,1,'manageedit-productcolumnshidden','a:1:{i:0;s:8:\"featured\";}'),
	(26,1,'wp_user-settings','editor=html'),
	(27,1,'wp_user-settings-time','1527173613'),
	(28,2,'nickname','new'),
	(29,2,'first_name',''),
	(30,2,'last_name',''),
	(31,2,'description',''),
	(32,2,'rich_editing','true'),
	(33,2,'syntax_highlighting','true'),
	(34,2,'comment_shortcuts','false'),
	(35,2,'admin_color','fresh'),
	(36,2,'use_ssl','0'),
	(37,2,'show_admin_bar_front','true'),
	(38,2,'locale',''),
	(39,2,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),
	(40,2,'wp_user_level','0'),
	(41,3,'nickname','new1'),
	(42,3,'first_name',''),
	(43,3,'last_name',''),
	(44,3,'description',''),
	(45,3,'rich_editing','true'),
	(46,3,'syntax_highlighting','true'),
	(47,3,'comment_shortcuts','false'),
	(48,3,'admin_color','fresh'),
	(49,3,'use_ssl','0'),
	(50,3,'show_admin_bar_front','true'),
	(51,3,'locale',''),
	(52,3,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),
	(53,3,'wp_user_level','0'),
	(54,4,'nickname','nerijenv'),
	(55,4,'first_name',''),
	(56,4,'last_name',''),
	(57,4,'description',''),
	(58,4,'rich_editing','true'),
	(59,4,'syntax_highlighting','true'),
	(60,4,'comment_shortcuts','false'),
	(61,4,'admin_color','fresh'),
	(62,4,'use_ssl','0'),
	(63,4,'show_admin_bar_front','true'),
	(64,4,'locale',''),
	(65,4,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),
	(66,4,'wp_user_level','0'),
	(67,4,'dismissed_wp_pointers','wp496_privacy'),
	(68,5,'nickname','fjeiuf'),
	(69,5,'first_name',''),
	(70,5,'last_name',''),
	(71,5,'description',''),
	(72,5,'rich_editing','true'),
	(73,5,'syntax_highlighting','true'),
	(74,5,'comment_shortcuts','false'),
	(75,5,'admin_color','fresh'),
	(76,5,'use_ssl','0'),
	(77,5,'show_admin_bar_front','true'),
	(78,5,'locale',''),
	(79,5,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),
	(80,5,'wp_user_level','0'),
	(81,5,'dismissed_wp_pointers','wp496_privacy'),
	(82,6,'nickname','fejk'),
	(83,6,'first_name',''),
	(84,6,'last_name',''),
	(85,6,'description',''),
	(86,6,'rich_editing','true'),
	(87,6,'syntax_highlighting','true'),
	(88,6,'comment_shortcuts','false'),
	(89,6,'admin_color','fresh'),
	(90,6,'use_ssl','0'),
	(91,6,'show_admin_bar_front','true'),
	(92,6,'locale',''),
	(93,6,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),
	(94,6,'wp_user_level','0'),
	(95,6,'dismissed_wp_pointers','wp496_privacy'),
	(96,7,'nickname','vjeuijdf'),
	(97,7,'first_name',''),
	(98,7,'last_name',''),
	(99,7,'description',''),
	(100,7,'rich_editing','true'),
	(101,7,'syntax_highlighting','true'),
	(102,7,'comment_shortcuts','false'),
	(103,7,'admin_color','fresh'),
	(104,7,'use_ssl','0'),
	(105,7,'show_admin_bar_front','true'),
	(106,7,'locale',''),
	(107,7,'wp_capabilities','a:1:{s:8:\"customer\";b:1;}'),
	(108,7,'wp_user_level','0'),
	(109,7,'dismissed_wp_pointers','wp496_privacy');

/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_users`;

CREATE TABLE `wp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;

INSERT INTO `wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`)
VALUES
	(1,'joey','$P$B/dGcfcKbbcObKTuE.QoicWO7Ji6ex/','joey','joey@pie.co.de','','2018-05-18 01:34:23','',0,'joey'),
	(2,'new','$P$BRzvAimVTmUyQeJnGb9ZaIA6gtfFzg/','new','new@testuser.com','','2018-05-24 15:34:33','',0,'new'),
	(3,'new1','$P$BuDbOtIpB8FByptgHHUGvoKMQv5K0n1','new1','new@pie.co.de','','2018-05-24 15:35:53','',0,'new1'),
	(4,'nerijenv','$P$BmgoDG0eKBXzOo8mDGFXKRf2a/OmKc.','nerijenv','nerijenv@ncie.fjeiru','','2018-05-24 18:30:51','',0,'nerijenv'),
	(5,'fjeiuf','$P$Bth/.UHs3kLcnC8qGMfwepVjBEtu6i.','fjeiuf','fjeiuf@nfhiuer.gnjrei','','2018-05-24 18:30:59','',0,'fjeiuf'),
	(6,'fejk','$P$BzlnZr7bsZt4IFjb0nklk/gQQ8s48W1','fejk','fejk@neruij.fnbhrj','','2018-05-24 18:31:15','',0,'fejk'),
	(7,'vjeuijdf','$P$B789riEwp3amBAGccANqPX.Rp6BJNe1','vjeuijdf','vjeuijdf@cneij.vnerui','','2018-05-24 18:31:37','',0,'vjeuijdf');

/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_wc_download_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_wc_download_log`;

CREATE TABLE `wp_wc_download_log` (
  `download_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_ip_address` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  PRIMARY KEY (`download_log_id`),
  KEY `permission_id` (`permission_id`),
  KEY `timestamp` (`timestamp`),
  CONSTRAINT `wp_wc_download_log_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `wp_woocommerce_downloadable_product_permissions` (`permission_id`) ON DELETE CASCADE,
  CONSTRAINT `wp_wc_download_log_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `wp_woocommerce_downloadable_product_permissions` (`permission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_wc_webhooks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_wc_webhooks`;

CREATE TABLE `wp_wc_webhooks` (
  `webhook_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `delivery_url` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `secret` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `topic` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `api_version` smallint(4) NOT NULL,
  `failure_count` smallint(10) NOT NULL DEFAULT '0',
  `pending_delivery` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`webhook_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_api_keys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_api_keys`;

CREATE TABLE `wp_woocommerce_api_keys` (
  `key_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `permissions` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `consumer_key` char(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `consumer_secret` char(43) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `nonces` longtext COLLATE utf8mb4_unicode_520_ci,
  `truncated_key` char(7) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_access` datetime DEFAULT NULL,
  PRIMARY KEY (`key_id`),
  KEY `consumer_key` (`consumer_key`),
  KEY `consumer_secret` (`consumer_secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_attribute_taxonomies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_attribute_taxonomies`;

CREATE TABLE `wp_woocommerce_attribute_taxonomies` (
  `attribute_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attribute_label` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `attribute_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attribute_orderby` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attribute_public` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`attribute_id`),
  KEY `attribute_name` (`attribute_name`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_woocommerce_attribute_taxonomies` WRITE;
/*!40000 ALTER TABLE `wp_woocommerce_attribute_taxonomies` DISABLE KEYS */;

INSERT INTO `wp_woocommerce_attribute_taxonomies` (`attribute_id`, `attribute_name`, `attribute_label`, `attribute_type`, `attribute_orderby`, `attribute_public`)
VALUES
	(1,'color','Color','select','menu_order',0),
	(2,'size','Size','select','menu_order',0);

/*!40000 ALTER TABLE `wp_woocommerce_attribute_taxonomies` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_woocommerce_downloadable_product_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_downloadable_product_permissions`;

CREATE TABLE `wp_woocommerce_downloadable_product_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `download_id` varchar(36) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `order_key` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_email` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `downloads_remaining` varchar(9) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `access_granted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_expires` datetime DEFAULT NULL,
  `download_count` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`permission_id`),
  KEY `download_order_key_product` (`product_id`,`order_id`,`order_key`(16),`download_id`),
  KEY `download_order_product` (`download_id`,`order_id`,`product_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_log`;

CREATE TABLE `wp_woocommerce_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `level` smallint(4) NOT NULL,
  `source` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `context` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`log_id`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_order_itemmeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_order_itemmeta`;

CREATE TABLE `wp_woocommerce_order_itemmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `meta_key` (`meta_key`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_order_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_order_items`;

CREATE TABLE `wp_woocommerce_order_items` (
  `order_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `order_item_type` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `order_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_payment_tokenmeta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_payment_tokenmeta`;

CREATE TABLE `wp_woocommerce_payment_tokenmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_token_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `payment_token_id` (`payment_token_id`),
  KEY `meta_key` (`meta_key`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_payment_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_payment_tokens`;

CREATE TABLE `wp_woocommerce_payment_tokens` (
  `token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gateway_id` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `token` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_sessions`;

CREATE TABLE `wp_woocommerce_sessions` (
  `session_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_key` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `session_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `session_expiry` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`session_key`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_woocommerce_sessions` WRITE;
/*!40000 ALTER TABLE `wp_woocommerce_sessions` DISABLE KEYS */;

INSERT INTO `wp_woocommerce_sessions` (`session_id`, `session_key`, `session_value`, `session_expiry`)
VALUES
	(114,'1','a:12:{s:4:\"cart\";s:451:\"a:1:{s:32:\"12109b097d777d5d7c0dd97527f89214\";a:11:{s:3:\"key\";s:32:\"12109b097d777d5d7c0dd97527f89214\";s:10:\"product_id\";i:11;s:12:\"variation_id\";i:64;s:9:\"variation\";a:1:{s:18:\"attribute_pa_color\";s:5:\"green\";}s:8:\"quantity\";i:4;s:9:\"data_hash\";s:32:\"9d5a1e37109e53bd5611b6b4439c7d2f\";s:13:\"line_tax_data\";a:2:{s:8:\"subtotal\";a:0:{}s:5:\"total\";a:0:{}}s:13:\"line_subtotal\";d:120;s:17:\"line_subtotal_tax\";i:0;s:10:\"line_total\";d:120;s:8:\"line_tax\";i:0;}}\";s:11:\"cart_totals\";s:409:\"a:15:{s:8:\"subtotal\";s:6:\"120.00\";s:12:\"subtotal_tax\";d:0;s:14:\"shipping_total\";s:5:\"10.00\";s:12:\"shipping_tax\";d:0;s:14:\"shipping_taxes\";a:0:{}s:14:\"discount_total\";d:0;s:12:\"discount_tax\";d:0;s:19:\"cart_contents_total\";s:6:\"120.00\";s:17:\"cart_contents_tax\";d:0;s:19:\"cart_contents_taxes\";a:0:{}s:9:\"fee_total\";s:4:\"0.00\";s:7:\"fee_tax\";d:0;s:9:\"fee_taxes\";a:0:{}s:5:\"total\";s:6:\"130.00\";s:9:\"total_tax\";d:0;}\";s:15:\"applied_coupons\";s:6:\"a:0:{}\";s:22:\"coupon_discount_totals\";s:6:\"a:0:{}\";s:26:\"coupon_discount_tax_totals\";s:6:\"a:0:{}\";s:21:\"removed_cart_contents\";s:6:\"a:0:{}\";s:8:\"customer\";s:702:\"a:26:{s:2:\"id\";s:1:\"1\";s:13:\"date_modified\";s:0:\"\";s:8:\"postcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:9:\"address_1\";s:0:\"\";s:7:\"address\";s:0:\"\";s:9:\"address_2\";s:0:\"\";s:5:\"state\";s:0:\"\";s:7:\"country\";s:2:\"GB\";s:17:\"shipping_postcode\";s:0:\"\";s:13:\"shipping_city\";s:0:\"\";s:18:\"shipping_address_1\";s:0:\"\";s:16:\"shipping_address\";s:0:\"\";s:18:\"shipping_address_2\";s:0:\"\";s:14:\"shipping_state\";s:0:\"\";s:16:\"shipping_country\";s:2:\"GB\";s:13:\"is_vat_exempt\";s:0:\"\";s:19:\"calculated_shipping\";s:0:\"\";s:10:\"first_name\";s:0:\"\";s:9:\"last_name\";s:0:\"\";s:7:\"company\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"email\";s:14:\"joey@pie.co.de\";s:19:\"shipping_first_name\";s:0:\"\";s:18:\"shipping_last_name\";s:0:\"\";s:16:\"shipping_company\";s:0:\"\";}\";s:22:\"shipping_for_package_0\";s:381:\"a:2:{s:12:\"package_hash\";s:40:\"wc_ship_35124eed34cb458b601ea2180956e3dc\";s:5:\"rates\";a:1:{s:11:\"flat_rate:1\";O:16:\"WC_Shipping_Rate\":2:{s:7:\"\0*\0data\";a:6:{s:2:\"id\";s:11:\"flat_rate:1\";s:9:\"method_id\";s:9:\"flat_rate\";s:11:\"instance_id\";i:1;s:5:\"label\";s:9:\"Flat rate\";s:4:\"cost\";s:5:\"10.00\";s:5:\"taxes\";a:0:{}}s:12:\"\0*\0meta_data\";a:1:{s:5:\"Items\";s:24:\"Hoodie - Green &times; 4\";}}}}\";s:25:\"previous_shipping_methods\";s:39:\"a:1:{i:0;a:1:{i:0;s:11:\"flat_rate:1\";}}\";s:23:\"chosen_shipping_methods\";s:29:\"a:1:{i:0;s:11:\"flat_rate:1\";}\";s:22:\"shipping_method_counts\";s:14:\"a:1:{i:0;i:1;}\";s:10:\"wc_notices\";N;}',1527937875),
	(85,'1a20e879014af8f5880c294b913b7e72','a:12:{s:4:\"cart\";s:410:\"a:1:{s:32:\"c16a5320fa475530d9583c34fd356ef5\";a:11:{s:3:\"key\";s:32:\"c16a5320fa475530d9583c34fd356ef5\";s:10:\"product_id\";i:31;s:12:\"variation_id\";i:0;s:9:\"variation\";a:0:{}s:8:\"quantity\";i:1;s:9:\"data_hash\";s:32:\"b5c1d5ca8bae6d4896cf1807cdf763f0\";s:13:\"line_tax_data\";a:2:{s:8:\"subtotal\";a:0:{}s:5:\"total\";a:0:{}}s:13:\"line_subtotal\";d:18;s:17:\"line_subtotal_tax\";i:0;s:10:\"line_total\";d:18;s:8:\"line_tax\";i:0;}}\";s:11:\"cart_totals\";s:406:\"a:15:{s:8:\"subtotal\";s:5:\"18.00\";s:12:\"subtotal_tax\";d:0;s:14:\"shipping_total\";s:5:\"10.00\";s:12:\"shipping_tax\";d:0;s:14:\"shipping_taxes\";a:0:{}s:14:\"discount_total\";d:0;s:12:\"discount_tax\";d:0;s:19:\"cart_contents_total\";s:5:\"18.00\";s:17:\"cart_contents_tax\";d:0;s:19:\"cart_contents_taxes\";a:0:{}s:9:\"fee_total\";s:4:\"0.00\";s:7:\"fee_tax\";d:0;s:9:\"fee_taxes\";a:0:{}s:5:\"total\";s:5:\"28.00\";s:9:\"total_tax\";d:0;}\";s:15:\"applied_coupons\";s:6:\"a:0:{}\";s:22:\"coupon_discount_totals\";s:6:\"a:0:{}\";s:26:\"coupon_discount_tax_totals\";s:6:\"a:0:{}\";s:21:\"removed_cart_contents\";s:6:\"a:0:{}\";s:22:\"shipping_for_package_0\";s:383:\"a:2:{s:12:\"package_hash\";s:40:\"wc_ship_76ab105923ca8bf2a5045bdfdf9b8e34\";s:5:\"rates\";a:1:{s:11:\"flat_rate:1\";O:16:\"WC_Shipping_Rate\":2:{s:7:\"\0*\0data\";a:6:{s:2:\"id\";s:11:\"flat_rate:1\";s:9:\"method_id\";s:9:\"flat_rate\";s:11:\"instance_id\";i:1;s:5:\"label\";s:9:\"Flat rate\";s:4:\"cost\";s:5:\"10.00\";s:5:\"taxes\";a:0:{}}s:12:\"\0*\0meta_data\";a:1:{s:5:\"Items\";s:26:\"Beanie with Logo &times; 1\";}}}}\";s:25:\"previous_shipping_methods\";s:39:\"a:1:{i:0;a:1:{i:0;s:11:\"flat_rate:1\";}}\";s:23:\"chosen_shipping_methods\";s:29:\"a:1:{i:0;s:11:\"flat_rate:1\";}\";s:22:\"shipping_method_counts\";s:14:\"a:1:{i:0;i:1;}\";s:10:\"wc_notices\";N;s:8:\"customer\";s:687:\"a:26:{s:2:\"id\";s:1:\"0\";s:13:\"date_modified\";s:0:\"\";s:8:\"postcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:9:\"address_1\";s:0:\"\";s:7:\"address\";s:0:\"\";s:9:\"address_2\";s:0:\"\";s:5:\"state\";s:0:\"\";s:7:\"country\";s:2:\"GB\";s:17:\"shipping_postcode\";s:0:\"\";s:13:\"shipping_city\";s:0:\"\";s:18:\"shipping_address_1\";s:0:\"\";s:16:\"shipping_address\";s:0:\"\";s:18:\"shipping_address_2\";s:0:\"\";s:14:\"shipping_state\";s:0:\"\";s:16:\"shipping_country\";s:2:\"GB\";s:13:\"is_vat_exempt\";s:0:\"\";s:19:\"calculated_shipping\";s:0:\"\";s:10:\"first_name\";s:0:\"\";s:9:\"last_name\";s:0:\"\";s:7:\"company\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"email\";s:0:\"\";s:19:\"shipping_first_name\";s:0:\"\";s:18:\"shipping_last_name\";s:0:\"\";s:16:\"shipping_company\";s:0:\"\";}\";}',1527349062),
	(78,'9fc9b28466416aee6d275ed892cf92ec','a:11:{s:4:\"cart\";s:410:\"a:1:{s:32:\"c16a5320fa475530d9583c34fd356ef5\";a:11:{s:3:\"key\";s:32:\"c16a5320fa475530d9583c34fd356ef5\";s:10:\"product_id\";i:31;s:12:\"variation_id\";i:0;s:9:\"variation\";a:0:{}s:8:\"quantity\";i:1;s:9:\"data_hash\";s:32:\"b5c1d5ca8bae6d4896cf1807cdf763f0\";s:13:\"line_tax_data\";a:2:{s:8:\"subtotal\";a:0:{}s:5:\"total\";a:0:{}}s:13:\"line_subtotal\";d:18;s:17:\"line_subtotal_tax\";i:0;s:10:\"line_total\";d:18;s:8:\"line_tax\";i:0;}}\";s:11:\"cart_totals\";s:406:\"a:15:{s:8:\"subtotal\";s:5:\"18.00\";s:12:\"subtotal_tax\";d:0;s:14:\"shipping_total\";s:5:\"10.00\";s:12:\"shipping_tax\";d:0;s:14:\"shipping_taxes\";a:0:{}s:14:\"discount_total\";d:0;s:12:\"discount_tax\";d:0;s:19:\"cart_contents_total\";s:5:\"18.00\";s:17:\"cart_contents_tax\";d:0;s:19:\"cart_contents_taxes\";a:0:{}s:9:\"fee_total\";s:4:\"0.00\";s:7:\"fee_tax\";d:0;s:9:\"fee_taxes\";a:0:{}s:5:\"total\";s:5:\"28.00\";s:9:\"total_tax\";d:0;}\";s:15:\"applied_coupons\";s:6:\"a:0:{}\";s:22:\"coupon_discount_totals\";s:6:\"a:0:{}\";s:26:\"coupon_discount_tax_totals\";s:6:\"a:0:{}\";s:21:\"removed_cart_contents\";s:6:\"a:0:{}\";s:22:\"shipping_for_package_0\";s:383:\"a:2:{s:12:\"package_hash\";s:40:\"wc_ship_76ab105923ca8bf2a5045bdfdf9b8e34\";s:5:\"rates\";a:1:{s:11:\"flat_rate:1\";O:16:\"WC_Shipping_Rate\":2:{s:7:\"\0*\0data\";a:6:{s:2:\"id\";s:11:\"flat_rate:1\";s:9:\"method_id\";s:9:\"flat_rate\";s:11:\"instance_id\";i:1;s:5:\"label\";s:9:\"Flat rate\";s:4:\"cost\";s:5:\"10.00\";s:5:\"taxes\";a:0:{}}s:12:\"\0*\0meta_data\";a:1:{s:5:\"Items\";s:26:\"Beanie with Logo &times; 1\";}}}}\";s:25:\"previous_shipping_methods\";s:39:\"a:1:{i:0;a:1:{i:0;s:11:\"flat_rate:1\";}}\";s:23:\"chosen_shipping_methods\";s:29:\"a:1:{i:0;s:11:\"flat_rate:1\";}\";s:22:\"shipping_method_counts\";s:14:\"a:1:{i:0;i:1;}\";s:8:\"customer\";s:687:\"a:26:{s:2:\"id\";s:1:\"0\";s:13:\"date_modified\";s:0:\"\";s:8:\"postcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:9:\"address_1\";s:0:\"\";s:7:\"address\";s:0:\"\";s:9:\"address_2\";s:0:\"\";s:5:\"state\";s:0:\"\";s:7:\"country\";s:2:\"GB\";s:17:\"shipping_postcode\";s:0:\"\";s:13:\"shipping_city\";s:0:\"\";s:18:\"shipping_address_1\";s:0:\"\";s:16:\"shipping_address\";s:0:\"\";s:18:\"shipping_address_2\";s:0:\"\";s:14:\"shipping_state\";s:0:\"\";s:16:\"shipping_country\";s:2:\"GB\";s:13:\"is_vat_exempt\";s:0:\"\";s:19:\"calculated_shipping\";s:0:\"\";s:10:\"first_name\";s:0:\"\";s:9:\"last_name\";s:0:\"\";s:7:\"company\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"email\";s:0:\"\";s:19:\"shipping_first_name\";s:0:\"\";s:18:\"shipping_last_name\";s:0:\"\";s:16:\"shipping_company\";s:0:\"\";}\";}',1527349002),
	(66,'e2dd254413a18948e94949878ed638ce','a:12:{s:4:\"cart\";s:414:\"a:1:{s:32:\"c51ce410c124a10e0db5e4b97fc2af39\";a:11:{s:3:\"key\";s:32:\"c51ce410c124a10e0db5e4b97fc2af39\";s:10:\"product_id\";i:13;s:12:\"variation_id\";i:0;s:9:\"variation\";a:0:{}s:8:\"quantity\";s:1:\"1\";s:9:\"data_hash\";s:32:\"b5c1d5ca8bae6d4896cf1807cdf763f0\";s:13:\"line_tax_data\";a:2:{s:8:\"subtotal\";a:0:{}s:5:\"total\";a:0:{}}s:13:\"line_subtotal\";d:18;s:17:\"line_subtotal_tax\";i:0;s:10:\"line_total\";d:18;s:8:\"line_tax\";i:0;}}\";s:11:\"cart_totals\";s:406:\"a:15:{s:8:\"subtotal\";s:5:\"18.00\";s:12:\"subtotal_tax\";d:0;s:14:\"shipping_total\";s:5:\"10.00\";s:12:\"shipping_tax\";d:0;s:14:\"shipping_taxes\";a:0:{}s:14:\"discount_total\";d:0;s:12:\"discount_tax\";d:0;s:19:\"cart_contents_total\";s:5:\"18.00\";s:17:\"cart_contents_tax\";d:0;s:19:\"cart_contents_taxes\";a:0:{}s:9:\"fee_total\";s:4:\"0.00\";s:7:\"fee_tax\";d:0;s:9:\"fee_taxes\";a:0:{}s:5:\"total\";s:5:\"28.00\";s:9:\"total_tax\";d:0;}\";s:15:\"applied_coupons\";s:6:\"a:0:{}\";s:22:\"coupon_discount_totals\";s:6:\"a:0:{}\";s:26:\"coupon_discount_tax_totals\";s:6:\"a:0:{}\";s:21:\"removed_cart_contents\";s:6:\"a:0:{}\";s:10:\"wc_notices\";N;s:22:\"shipping_for_package_0\";s:374:\"a:2:{s:12:\"package_hash\";s:40:\"wc_ship_1c37f5796587fea29aceb9685c4fa147\";s:5:\"rates\";a:1:{s:11:\"flat_rate:1\";O:16:\"WC_Shipping_Rate\":2:{s:7:\"\0*\0data\";a:6:{s:2:\"id\";s:11:\"flat_rate:1\";s:9:\"method_id\";s:9:\"flat_rate\";s:11:\"instance_id\";i:1;s:5:\"label\";s:9:\"Flat rate\";s:4:\"cost\";s:5:\"10.00\";s:5:\"taxes\";a:0:{}}s:12:\"\0*\0meta_data\";a:1:{s:5:\"Items\";s:17:\"T-Shirt &times; 1\";}}}}\";s:25:\"previous_shipping_methods\";s:39:\"a:1:{i:0;a:1:{i:0;s:11:\"flat_rate:1\";}}\";s:23:\"chosen_shipping_methods\";s:29:\"a:1:{i:0;s:11:\"flat_rate:1\";}\";s:22:\"shipping_method_counts\";s:14:\"a:1:{i:0;i:1;}\";s:8:\"customer\";s:687:\"a:26:{s:2:\"id\";s:1:\"0\";s:13:\"date_modified\";s:0:\"\";s:8:\"postcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:9:\"address_1\";s:0:\"\";s:7:\"address\";s:0:\"\";s:9:\"address_2\";s:0:\"\";s:5:\"state\";s:0:\"\";s:7:\"country\";s:2:\"GB\";s:17:\"shipping_postcode\";s:0:\"\";s:13:\"shipping_city\";s:0:\"\";s:18:\"shipping_address_1\";s:0:\"\";s:16:\"shipping_address\";s:0:\"\";s:18:\"shipping_address_2\";s:0:\"\";s:14:\"shipping_state\";s:0:\"\";s:16:\"shipping_country\";s:2:\"GB\";s:13:\"is_vat_exempt\";s:0:\"\";s:19:\"calculated_shipping\";s:0:\"\";s:10:\"first_name\";s:0:\"\";s:9:\"last_name\";s:0:\"\";s:7:\"company\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"email\";s:0:\"\";s:19:\"shipping_first_name\";s:0:\"\";s:18:\"shipping_last_name\";s:0:\"\";s:16:\"shipping_company\";s:0:\"\";}\";}',1527348915);

/*!40000 ALTER TABLE `wp_woocommerce_sessions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_woocommerce_shipping_zone_locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_shipping_zone_locations`;

CREATE TABLE `wp_woocommerce_shipping_zone_locations` (
  `location_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` bigint(20) unsigned NOT NULL,
  `location_code` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `location_type` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_id` (`location_id`),
  KEY `location_type_code` (`location_type`(10),`location_code`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_woocommerce_shipping_zone_locations` WRITE;
/*!40000 ALTER TABLE `wp_woocommerce_shipping_zone_locations` DISABLE KEYS */;

INSERT INTO `wp_woocommerce_shipping_zone_locations` (`location_id`, `zone_id`, `location_code`, `location_type`)
VALUES
	(1,1,'GB','country');

/*!40000 ALTER TABLE `wp_woocommerce_shipping_zone_locations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_woocommerce_shipping_zone_methods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_shipping_zone_methods`;

CREATE TABLE `wp_woocommerce_shipping_zone_methods` (
  `zone_id` bigint(20) unsigned NOT NULL,
  `instance_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `method_id` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `method_order` bigint(20) unsigned NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_woocommerce_shipping_zone_methods` WRITE;
/*!40000 ALTER TABLE `wp_woocommerce_shipping_zone_methods` DISABLE KEYS */;

INSERT INTO `wp_woocommerce_shipping_zone_methods` (`zone_id`, `instance_id`, `method_id`, `method_order`, `is_enabled`)
VALUES
	(1,1,'flat_rate',1,1),
	(0,2,'flat_rate',1,1);

/*!40000 ALTER TABLE `wp_woocommerce_shipping_zone_methods` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_woocommerce_shipping_zones
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_shipping_zones`;

CREATE TABLE `wp_woocommerce_shipping_zones` (
  `zone_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `zone_order` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_woocommerce_shipping_zones` WRITE;
/*!40000 ALTER TABLE `wp_woocommerce_shipping_zones` DISABLE KEYS */;

INSERT INTO `wp_woocommerce_shipping_zones` (`zone_id`, `zone_name`, `zone_order`)
VALUES
	(1,'United Kingdom (UK)',0);

/*!40000 ALTER TABLE `wp_woocommerce_shipping_zones` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wp_woocommerce_tax_rate_locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_tax_rate_locations`;

CREATE TABLE `wp_woocommerce_tax_rate_locations` (
  `location_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_code` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `tax_rate_id` bigint(20) unsigned NOT NULL,
  `location_type` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `tax_rate_id` (`tax_rate_id`),
  KEY `location_type_code` (`location_type`(10),`location_code`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table wp_woocommerce_tax_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wp_woocommerce_tax_rates`;

CREATE TABLE `wp_woocommerce_tax_rates` (
  `tax_rate_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_rate_country` varchar(2) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate_state` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate` varchar(8) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate_priority` bigint(20) unsigned NOT NULL,
  `tax_rate_compound` int(1) NOT NULL DEFAULT '0',
  `tax_rate_shipping` int(1) NOT NULL DEFAULT '1',
  `tax_rate_order` bigint(20) unsigned NOT NULL,
  `tax_rate_class` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tax_rate_id`),
  KEY `tax_rate_country` (`tax_rate_country`),
  KEY `tax_rate_state` (`tax_rate_state`(2)),
  KEY `tax_rate_class` (`tax_rate_class`(10)),
  KEY `tax_rate_priority` (`tax_rate_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
