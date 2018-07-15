<?php
/*
Plugin Name: Facebook Reviews Pro
Plugin URI: https://richplugins.com/facebook-reviews-pro-wordpress-plugin
Description: Instantly Facebook Business Reviews on your website to increase user confidence and SEO.
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

define('FBRP_VERSION',            '1.3');
define('FBRP_GRAPH_API',          'https://graph.facebook.com/');
define('FBRP_API_RATINGS_LIMIT',  '250');
define('FBRP_PLUGIN_URL',         plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));
define('FBRP_AVATAR',             FBRP_PLUGIN_URL . '/static/img/avatar.gif');

function fbrp_options() {
    return array(
        'fbrp_version',
        'fbrp_license',
        'fbrp_expired',
        'fbrp_active',
    );
}

/*-------------------------------- License --------------------------------*/
function fbrp_edd_sl_plugin_updater() {
    $fbrp_license = get_option('fbrp_license');
    if (strlen($fbrp_license) < 1) {
        return;
    }

    $fb_plugin_meta = get_plugin_data(untrailingslashit(plugin_dir_path(__FILE__)) . '/fbrp.php', false );
    $edd_updater = new EDD_SL_Plugin_Updater('https://api.richplugins.com/plugins/update-check', plugin_basename(__FILE__), array(
        'slug'      => 'fbrp',
        'author'    => 'RichPlugins',
        'version'   => $fb_plugin_meta['Version'],
        'license'   => $fbrp_license
    ));
}

add_action('admin_init', 'fbrp_edd_sl_plugin_updater');

/*-------------------------------- Widget --------------------------------*/
function fbrp_init_widget() {
    if (!class_exists('Fb_Reviews_Widget_Pro' ) ) {
        require 'fbrp-widget.php';
    }
}
add_action('widgets_init', 'fbrp_init_widget');

function fbrp_register_widget() {
    return register_widget("Fb_Reviews_Widget_Pro");
}
add_action('widgets_init', 'fbrp_register_widget');

/*-------------------------------- Menu --------------------------------*/
function fbrp_setting_menu() {
     add_submenu_page(
         'options-general.php',
         'Facebook Reviews Pro',
         'Facebook Reviews Pro',
         'moderate_comments',
         'fbrp',
         'fbrp_setting'
     );
}
add_action('admin_menu', 'fbrp_setting_menu', 10);

function fbrp_setting() {
    include_once(dirname(__FILE__) . '/fbrp-setting.php');
}

/*-------------------------------- Links --------------------------------*/
function fbrp_plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=fbrp') . '">' . fbrp_i('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'fbrp_plugin_action_links', 10, 2);

/*-------------------------------- Activation --------------------------------*/
function fbrp_activation() {
    if (fbrp_does_need_update()) {
        fbrp_install();
    }
}
register_activation_hook(__FILE__, 'fbrp_activation');

function fbrp_install() {
    $version = (string)get_option('fbrp_version');
    if (!$version) {
        $version = '0';
    }

    if (version_compare($version, FBRP_VERSION, '=')) {
        return;
    }

    add_option('fbrp_active', '1');
    update_option('fbrp_version', FBRP_VERSION);
}

function fbrp_lang_init() {
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('fbrp', false, basename( dirname( __FILE__ ) ) . '/languages');
}
add_action('plugins_loaded', 'fbrp_lang_init');

/*-------------------------------- Shortcode --------------------------------*/
function fbrp_shortcode($atts) {
    global $wpdb;

    if (!fbrp_enabled()) return '';

    $shortcode_atts = shortcode_atts(array(
        'page_id'              => '',
        'page_name'            => '',
        'page_photo'           => '',
        'page_access_token'    => '',
        'access_token'         => '', // support old version
        'rating_snippet'       => '',
        'pagination'           => '25',
        'min_filter'           => '',
        'min_letter'           => '',
        'max_width'            => '',
        'max_height'           => '',
        'text_size'            => '',
        'dark_theme'           => '',
        'view_mode'            => 'list',
        'hide_photo'           => '',
        'hide_avatar'          => '',
        'disable_user_link'    => '',
        'slider_speed'         => '',
        'slider_count'         => '',
        'slider_hide_pagin'    => '',
        'open_link'            => '',
        'nofollow_link'        => '',
        'cache'                => '',
        'api_ratings_limit'    => FBRP_API_RATINGS_LIMIT,
        'hide_float_badge'     => '',
        'lazy_load_img'        => '',
    ), $atts);

    foreach ($shortcode_atts as $variable => $value) {
        ${$variable} = esc_attr($shortcode_atts[$variable]);
    }

    if (empty($page_id)) {
        ob_start();
        ?>
        <div class="fbrev-error" style="padding:10px;color:#b94a48;background-color:#f2dede;border-color:#eed3d7;max-width:200px;">
            <?php echo fbrp_i('<b>Facebook Reviews Pro</b>: required attribute page_id is not defined'); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    // support old version
    $page_access_token = $page_access_token ? $page_access_token : $access_token;
    $limit = ($view_mode == 'grid' && $pagination > 0) ? $pagination : $api_ratings_limit;

    $response = fbrp_api_rating($page_id, $page_access_token, $atts, 'shortcode2', $cache, $limit);
    $response_data = $response['data'];
    $response_json = rplg_json_decode($response_data);

    ob_start();
    if (isset($response_json->ratings) && isset($response_json->ratings->data)) {
        $reviews = $response_json->ratings->data;
        include(dirname(__FILE__) . '/fbrp-reviews.php');
    } else {
        ?>
        <div class="fbrev-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
            <?php echo fbrp_i('Facebook API Rating: ') . $response_data; ?>
        </div>
        <?php
    }
    return preg_replace('/[\n\r]/', '', ob_get_clean());
}
add_shortcode("facebook-reviews-pro", "fbrp_shortcode");

/*-------------------------------- Request --------------------------------*/
function fbrp_request_handler() {
    if (!empty($_GET['cf_action'])) {
        switch ($_GET['cf_action']) {
            case 'fbrp_embed':
                $response = fbrp_shortcode($_GET);
                header('Content-type: text/html');
                header('Access-Control-Allow-Origin: *');
                echo $response;
                die();
            break;
        }
    }
}
add_action('init', 'fbrp_request_handler');

/*-------------------------------- Helpers --------------------------------*/
function fbrp_enabled() {
    $active = get_option('fbrp_active');
    if (empty($active) || $active === '0') { return false; }
    return true;
}

function fbrp_does_need_update() {
    $version = (string)get_option('fbrp_version');
    if (empty($version)) {
        $version = '0';
    }
    if (version_compare($version, '1.0', '<')) {
        return true;
    }
    return false;
}

function fbrp_api_rating($page_id, $page_access_token, $options, $cache_name, $cache_option, $limit) {

    $response_cache_key = 'fbrp_' . $cache_name . '_api_' . $page_id;
    $options_cache_key = 'fbrp_' . $cache_name . '_options_' . $page_id;

    if (!isset($limit) || $limit == null) {
        $limit=FBRP_API_RATINGS_LIMIT;
    }

    $api_response = get_transient($response_cache_key);
    $widget_options = get_transient($options_cache_key);
    $serialized_instance = serialize($options);

    if ($api_response === false || $serialized_instance !== $widget_options) {
        $expiration = $cache_option;
        switch ($expiration) {
            case '1':
                $expiration = 3600;
                break;
            case '3':
                $expiration = 3600 * 3;
                break;
            case '6':
                $expiration = 3600 * 6;
                break;
            case '12':
                $expiration = 3600 * 12;
                break;
            case '24':
                $expiration = 3600 * 24;
                break;
            case '48':
                $expiration = 3600 * 48;
                break;
            case '168':
                $expiration = 3600 * 168;
                break;
            default:
                $expiration = 3600 * 24;
        }

        $api_url = FBRP_GRAPH_API . $page_id . "?access_token=" . $page_access_token . "&fields=ratings.limit(" . $limit . ")";
        $api_response = rplg_urlopen($api_url);

        set_transient($response_cache_key, $api_response, $expiration);
        set_transient($options_cache_key, $serialized_instance, $expiration);
    }
    return $api_response;
}

function fbrp_i($text, $params=null) {
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'fbrp'), $params);
}

?>