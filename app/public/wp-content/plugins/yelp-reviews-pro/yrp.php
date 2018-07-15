<?php
/*
Plugin Name: Yelp Reviews Pro
Plugin URI: https://richplugins.com
Description: Instantly Yelp rating and reviews on your website to increase user confidence and SEO.
Author: RichPlugins <support@richplugins.com>
Version: 1.3
Author URI: https://richplugins.com
*/

require(ABSPATH . 'wp-includes/version.php');

include_once(dirname(__FILE__) . '/api/urlopen.php');
include_once(dirname(__FILE__) . '/helper/debug.php');

if (!class_exists('EDD_SL_Plugin_Updater')) {
    include_once(dirname(__FILE__) . '/license/EDD_SL_Plugin_Updater.php');
}

define('YRP_VERSION',             '1.3');
define('YRP_API',                 'https://api.yelp.com/v3/businesses');
define('YRP_AVATAR',              'https://s3-media3.fl.yelpcdn.com/assets/srv0/yelp_styleguide/bf5ff8a79310/assets/img/default_avatars/user_medium_square.png');
define('YRP_PLUGIN_URL',          plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));

function yrp_options() {
    return array(
        'yrp_license',
        'yrp_expired',
        'yrp_version',
        'yrp_active',
        'yrp_api_key',
        'yrp_language',
    );
}

/*-------------------------------- License --------------------------------*/
function yrp_edd_sl_plugin_updater() {
    $yrp_license = get_option('yrp_license');
    if (strlen($yrp_license) < 1) {
        return;
    }

    $yrp_plugin_meta = get_plugin_data(untrailingslashit(plugin_dir_path(__FILE__)) . '/yrp.php', false );
    $edd_updater = new EDD_SL_Plugin_Updater('https://api.richplugins.com/plugins/update-check', plugin_basename(__FILE__), array(
        'slug'      => 'yrp',
        'author'    => 'RichPlugins',
        'version'   => $yrp_plugin_meta['Version'],
        'license'   => $yrp_license
    ));
}

add_action('admin_init', 'yrp_edd_sl_plugin_updater');

/*-------------------------------- Widget --------------------------------*/
function yrp_init_widget() {
    if (!class_exists('Yelp_Reviews_Pro' ) ) {
        require 'yrp-widget.php';
    }
}
add_action('widgets_init', 'yrp_init_widget');

function yrp_register_widget() {
    return register_widget("Yelp_Reviews_Pro");
}
add_action('widgets_init', 'yrp_register_widget');

/*-------------------------------- Menu --------------------------------*/
function yrp_setting_menu() {
     add_submenu_page(
         'options-general.php',
         'Yelp Reviews Pro',
         'Yelp Reviews Pro',
         'moderate_comments',
         'yrp',
         'yrp_setting'
     );
}
add_action('admin_menu', 'yrp_setting_menu', 10);

function yrp_setting() {
    include_once(dirname(__FILE__) . '/yrp-setting.php');
}

/*-------------------------------- Links --------------------------------*/
function yrp_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=yrp') . '">'.yrp_i('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'yrp_plugin_action_links', 10, 2);

/*-------------------------------- Database --------------------------------*/
function yrp_activation($network_wide) {
    if (yrp_does_need_update()) {
        yrp_install($network_wide);
    }
}
register_activation_hook(__FILE__, 'yrp_activation');

function yrp_install($network_wide, $allow_db_install=true) {
    global $wpdb;

    $version = (string)get_option('yrp_version');
    if (!$version) {
        $version = '0';
    }

    if ($allow_db_install) {
        if (function_exists('is_multisite') && is_multisite() && $network_wide) {
            $current_blog_id = get_current_blog_id();
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                yrp_install_db();
            }
            switch_to_blog($current_blog_id);
        } else {
            yrp_install_db();
        }
    }

    if (version_compare($version, YRP_VERSION, '=')) {
        return;
    }

    add_option('yrp_active', '1');
    update_option('yrp_version', YRP_VERSION);
}

function yrp_install_db() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "yrw_yelp_business (".
           "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
           "business_id VARCHAR(100) NOT NULL,".
           "name VARCHAR(255) NOT NULL,".
           "photo VARCHAR(255),".
           "address VARCHAR(255),".
           "rating DOUBLE PRECISION,".
           "url VARCHAR(255),".
           "website VARCHAR(255),".
           "review_count INTEGER NOT NULL,".
           "PRIMARY KEY (`id`),".
           "UNIQUE INDEX yrw_business_id (`business_id`)".
           ") " . $charset_collate . ";";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);

    $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "yrw_yelp_review (".
           "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
           "yelp_business_id BIGINT(20) UNSIGNED NOT NULL,".
           "hash VARCHAR(40) NOT NULL,".
           "rating INTEGER NOT NULL,".
           "text VARCHAR(10000),".
           "url VARCHAR(255),".
           "time VARCHAR(20) NOT NULL,".
           "author_name VARCHAR(255),".
           "author_img VARCHAR(255),".
           "PRIMARY KEY (`id`),".
           "UNIQUE INDEX yrw_yelp_review_hash (`hash`),".
           "INDEX yrw_yelp_business_id (`yelp_business_id`)".
           ") " . $charset_collate . ";";

    dbDelta($sql);
}

function yrp_reset($reset_db) {
    global $wpdb;

    if (function_exists('is_multisite') && is_multisite()) {
        $current_blog_id = get_current_blog_id();
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            yrp_reset_data($reset_db);
        }
        switch_to_blog($current_blog_id);
    } else {
        yrp_reset_data($reset_db);
    }
}

function yrp_reset_data($reset_db) {
    global $wpdb;

    foreach (yrp_options() as $opt) {
        delete_option($opt);
    }
    if ($reset_db) {
        $wpdb->query("DROP TABLE " . $wpdb->prefix . "yrw_yelp_business;");
        $wpdb->query("DROP TABLE " . $wpdb->prefix . "yrw_yelp_review;");
    }
}

/*-------------------------------- Shortcode --------------------------------*/
function yrp_shortcode($atts) {
    global $wpdb;

    if (!yrp_enabled()) return '';

    $shortcode_atts = shortcode_atts(array(
        'business_id'          => '',
        'business_photo'       => '',
        'dark_theme'           => '',
        'open_link'            => '',
        'nofollow_link'        => '',
        'auto_load'            => '',
        'rating_snippet'       => '',
        'pagination'           => '',
        'sort'                 => '',
        'min_filter'           => '',
        'text_size'            => '',
        'hide_photo'           => '',
        'hide_avatar'          => '',
        'view_mode'            => 'list',
        'max_width'            => 'auto',
        'hide_float_badge'     => '',
        'lazy_load_img'        => '',
    ), $atts);

    foreach ($shortcode_atts as $variable => $value) {
        ${$variable} = esc_attr($shortcode_atts[$variable]);
    }

    if ($business_id) {
        ob_start();
        include(dirname(__FILE__) . '/yrp-reviews.php');
        return preg_replace('/[\n\r]/', '', ob_get_clean());
    } else {
        return yrp_i('<b>Yelp Reviews Pro:</b> the required attribute business_id is not setted');
    }
}
add_shortcode("yelp-reviews-pro", "yrp_shortcode");

/*-------------------------------- Request --------------------------------*/
function yrp_request_handler() {
    global $wpdb;

    if (!empty($_GET['cf_action'])) {

        switch ($_GET['cf_action']) {
            case 'yrp_api_key':
                if (current_user_can('manage_options')) {
                    if (isset($_POST['yrw_wpnonce']) === false) {
                        $error = yrp_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('yrw_wpnonce', 'yrw_wpnonce');
                        update_option('yrp_api_key', trim(sanitize_text_field($_POST['app_key'])));
                        $status = 'success';
                        $response = compact('status');
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'yrp_search':
                if (current_user_can('manage_options')) {
                    if (isset($_GET['yrw_wpnonce']) === false) {
                        $error = yrp_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('yrw_wpnonce', 'yrw_wpnonce');
                        $term = $_GET['term'];
                        $location = $_GET['location'];
                        $api_url = YRP_API . '/search?term=' . $term . '&location=' . $location;
                        $api_key = get_option('yrp_api_key');
                        $response = rplg_json_urlopen($api_url, null, array(
                            'Authorization: Bearer ' . $api_key
                        ));
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'yrp_reviews':
                if (current_user_can('manage_options')) {
                    if (isset($_GET['yrw_wpnonce']) === false) {
                        $error = yrp_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('yrw_wpnonce', 'yrw_wpnonce');
                        $api_url = yrp_api_url($_GET['business_id']);
                        $api_key = get_option('yrp_api_key');
                        $response = rplg_json_urlopen($api_url, null, array(
                            'Authorization: Bearer ' . $api_key
                        ));
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'yrp_save':
                if (current_user_can('manage_options')) {
                    if (isset($_POST['yrw_wpnonce']) === false) {
                        $error = yrp_i('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.');
                        $response = compact('error');
                    } else {
                        check_admin_referer('yrw_wpnonce', 'yrw_wpnonce');
                        $api_key = get_option('yrp_api_key');
                        $business = rplg_json_urlopen(YRP_API . '/' . $_POST['business_id'], null, array(
                            'Authorization: Bearer ' . $api_key
                        ));
                        $reviews = rplg_json_urlopen(yrp_api_url($_POST['business_id']), null, array(
                            'Authorization: Bearer ' . $api_key
                        ));
                        yrp_save_reviews($business, $reviews);
                        $response = 'success';
                    }
                    header('Content-type: text/javascript');
                    echo cf_json_encode($response);
                    die();
                }
            break;
            case 'yrp_auto_save':
                if(!($business_id = $_GET['business_id'])) {
                    header("HTTP/1.0 400 Bad Request");
                    die();
                }
                // Auto-update Yelp reviews daily schedule
                $ts = time() + 7200;
                wp_schedule_single_event($ts, 'yrp_auto_save', array($business_id, $_GET['min_filter']));
                header('Content-type: text/javascript');
                die('// yrp_auto_save scheduled');
            break;
        }
    }
}
add_action('init', 'yrp_request_handler');

function yrp_auto_save($business_id, $min_filter = 0, $reviews_lang = '') {
    $api_key = get_option('yrp_api_key');
    $business = rplg_json_urlopen(YRP_API . '/' . $business_id, null, array(
        'Authorization: Bearer ' . $api_key
    ));
    $reviews = rplg_json_urlopen(yrp_api_url($business_id, $reviews_lang), null, array(
        'Authorization: Bearer ' . $api_key
    ));
    yrp_save_reviews($business, $reviews, false);
}
add_action('yrp_auto_save', 'yrp_auto_save');

function yrp_save_reviews($business, $reviews, $allow_business_create=true) {
    global $wpdb;

    $yelp_business_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "yrw_yelp_business WHERE business_id = %s", $business->id));
    if ($yelp_business_id) {
        $wpdb->update($wpdb->prefix . 'yrw_yelp_business', array('rating' => $business->rating, 'review_count' => $business->review_count), array('ID' => $yelp_business_id));
    } else if ($allow_business_create) {
        $address = implode(", ", array($business->location->address1, $business->location->city, $business->location->state, $business->location->zip_code));
        $wpdb->insert($wpdb->prefix . 'yrw_yelp_business', array(
            'business_id' => $business->id,
            'name' => $business->name,
            'photo' => $business->image_url,
            'address' => $address,
            'rating' => $business->rating,
            'url' => $business->url,
            //'website' => $business->url,
            'review_count' => $business->review_count
        ));
        $yelp_business_id = $wpdb->insert_id;
    } else {
        return;
    }

    if ($reviews && $reviews->reviews) {
        foreach ($reviews->reviews as $review) {
            $hash = sha1($business->id . $review->time_created);
            $yelp_review_hash = $wpdb->get_var($wpdb->prepare("SELECT hash FROM " . $wpdb->prefix . "yrw_yelp_review WHERE hash = %s", $hash));
            if (!$yelp_review_hash) {
                $wpdb->insert($wpdb->prefix . 'yrw_yelp_review', array(
                    'yelp_business_id' => $yelp_business_id,
                    'hash' => $hash,
                    'rating' => $review->rating,
                    'text' => $review->text,
                    'url' => $review->url,
                    'time' => $review->time_created,
                    'author_name' => $review->user->name,
                    'author_img' => $review->user->image_url
                ));
            }
        }
    }
}

function yrp_lang_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('yrp', false, basename( dirname( __FILE__ ) ) . '/languages');
}
add_action('plugins_loaded', 'yrp_lang_init');

/*-------------------------------- Helpers --------------------------------*/
function yrp_enabled() {
    global $id, $post;

    $active = get_option('yrp_active');
    if (empty($active) || $active === '0') { return false; }
    return true;
}

function yrp_api_url($business_id, $reviews_lang = '') {
    $url = YRP_API . '/' . $business_id . '/reviews';

    $yrp_language = strlen($reviews_lang) > 0 ? $reviews_lang : get_option('yrp_language');
    if (strlen($yrp_language) > 0) {
        $url = $url . '?locale=' . $yrp_language;
    }
    return $url;
}

function yrp_does_need_update() {
    $version = (string)get_option('yrp_version');
    if (empty($version)) {
        $version = '0';
    }
    if (version_compare($version, '1.0', '<')) {
        return true;
    }
    return false;
}

function yrp_i($text, $params=null) {
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'yrp'), $params);
}

if (!function_exists('esc_html')) {
function esc_html( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
    $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
    return apply_filters( 'esc_html', $safe_text, $text );
}
}

if (!function_exists('esc_attr')) {
function esc_attr( $text ) {
    $safe_text = wp_check_invalid_utf8( $text );
    $safe_text = _wp_specialchars( $safe_text, ENT_QUOTES );
    return apply_filters( 'attribute_escape', $safe_text, $text );
}
}

/**
 * JSON ENCODE for PHP < 5.2.0
 * Checks if json_encode is not available and defines json_encode
 * to use php_json_encode in its stead
 * Works on iteratable objects as well - stdClass is iteratable, so all WP objects are gonna be iteratable
 */
if(!function_exists('cf_json_encode')) {
    function cf_json_encode($data) {

        // json_encode is sending an application/x-javascript header on Joyent servers
        // for some unknown reason.
        return cfjson_encode($data);
    }

    function cfjson_encode_string($str) {
        if(is_bool($str)) {
            return $str ? 'true' : 'false';
        }

        return str_replace(
            array(
                '\\'
                , '"'
                //, '/'
                , "\n"
                , "\r"
            )
            , array(
                '\\\\'
                , '\"'
                //, '\/'
                , '\n'
                , '\r'
            )
            , $str
        );
    }

    function cfjson_encode($arr) {
        $json_str = '';
        if (is_array($arr)) {
            $pure_array = true;
            $array_length = count($arr);
            for ( $i = 0; $i < $array_length ; $i++) {
                if (!isset($arr[$i])) {
                    $pure_array = false;
                    break;
                }
            }
            if ($pure_array) {
                $json_str = '[';
                $temp = array();
                for ($i=0; $i < $array_length; $i++) {
                    $temp[] = sprintf("%s", cfjson_encode($arr[$i]));
                }
                $json_str .= implode(',', $temp);
                $json_str .="]";
            }
            else {
                $json_str = '{';
                $temp = array();
                foreach ($arr as $key => $value) {
                    $temp[] = sprintf("\"%s\":%s", $key, cfjson_encode($value));
                }
                $json_str .= implode(',', $temp);
                $json_str .= '}';
            }
        }
        else if (is_object($arr)) {
            $json_str = '{';
            $temp = array();
            foreach ($arr as $k => $v) {
                $temp[] = '"'.$k.'":'.cfjson_encode($v);
            }
            $json_str .= implode(',', $temp);
            $json_str .= '}';
        }
        else if (is_string($arr)) {
            $json_str = '"'. cfjson_encode_string($arr) . '"';
        }
        else if (is_numeric($arr)) {
            $json_str = $arr;
        }
        else if (is_bool($arr)) {
            $json_str = $arr ? 'true' : 'false';
        }
        else {
            $json_str = '"'. cfjson_encode_string($arr) . '"';
        }
        return $json_str;
    }
}
?>