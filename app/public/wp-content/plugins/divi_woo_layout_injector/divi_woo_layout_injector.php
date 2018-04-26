<?php

/*
 * Plugin Name: Woo Layout Injector
 * Plugin URI:  http://www.sean-barton.co.uk
 * Description: A plugin to handle the layouts of WooCommerce pages using the ET layout builder system. 
 * Author:      Sean Barton - Tortoise IT
 * Version:     4.3
 * Author URI:  http://www.sean-barton.co.uk
 */

//global variables
$sb_et_woo_li_image_size = '';
$sb_et_woo_li_requested = '';
$sb_et_woo_li_account_layouts = array();
$sb_woo_li_id_override = 0; //for use by the single product widget to temporarily override the current ID to get things like images and prices

//constants
define('SB_ET_WOO_LI_VERSION', '4.3');
define('SB_ET_WOO_LI_STORE_URL', 'https://elegantmarketplace.com');
define('SB_ET_WOO_LI_ITEM_NAME', 'Woo Layout Injector');
define('SB_ET_WOO_LI_ITEM_ID', 50273);
define('SB_ET_WOO_LI_AUTHOR_NAME', 'Sean Barton');
define('SB_ET_WOO_LI_FILE', __FILE__);

//requires
require_once('includes/divi_woo_li_admin.php');
require_once('includes/divi_woo_li_injector.php');

//licensing
require_once('includes/emp-licensing.php');

//actions
add_action('plugins_loaded', 'sb_et_woo_li_init');
add_action('wp_loaded', 'sb_et_woo_li_init_remove');
add_action('wp_loaded', 'sb_et_woo_li_hook_replacement');

function sb_et_woo_li_init()
{
    add_shortcode('wli_content', 'sb_et_woo_li_shortcode_content'); //[wli_content] will output the content itself
    add_shortcode('wli_hook', 'sb_et_woo_li_shortcode_hook'); // call a php action [wli hook="before_content" argument_1="123" argument_2="123" argument_3="123"]

    add_action('admin_menu', 'sb_et_woo_li_submenu');
    add_action('et_builder_ready', 'sb_et_woo_li_theme_setup', 11);
    add_action('admin_head', 'sb_et_woo_li_admin_head', 9999);
    add_action('wp_enqueue_scripts', 'sb_et_woo_li_enqueue', 9999);
    add_action("save_post", "sb_et_woo_li_meta_box_save", 10, 3);
    add_action("add_meta_boxes", "sb_et_woo_li_meta_box");
    add_action('admin_notices', 'sb_et_woo_li_woo_active');

    add_filter('woocommerce_product_single_add_to_cart_text', 'sb_et_woo_li_cart_button_text', 99, 1);
    add_filter('template_include', 'sb_et_woo_li_template_include', 99);
    add_filter('wp_calculate_image_srcset', 'sb_et_woo_li_disable_srcset');
    add_filter('admin_footer_text', '__return_empty_string');
    add_filter('update_footer', '__return_empty_string');
    add_filter('wc_get_template', 'sb_et_woo_li_handle_templates', 10, 10);
    add_filter('the_content', 'sb_et_woo_li_content_filter', 999, 999);
    add_filter('woocommerce_add_to_cart_fragments', '__return_false', 999, 999); //was causing issues with the cart/checkout. maybe make this optional whether the header cart/cart layout/checkout layout is used
    add_filter('woocommerce_sale_flash', 'sb_et_woo_li_sale_label');
    //add_filter('woocommerce_loop_add_to_cart_args', 'sb_et_woo_li_atc_args');
    add_filter('single_product_archive_thumbnail_size', 'sb_single_product_archive_thumbnail_size');

    //add_action('admin_footer', 'sb_et_woo_li_builder_css');
    add_filter('et_builder_post_types', 'sb_et_woo_li_add_builder');
    add_filter('et_fb_post_types', 'sb_et_woo_li_add_builder');

    define('SB_ET_WOO_LI_PRODUCT_COUNT', apply_filters('sb_et_woo_li_product_count', 100)); //used by the single product module to avoid having to cache a list of thousands of products for larger stores. Defaulted to 100 but able to be filtered for larger stores
}

function sb_single_product_archive_thumbnail_size()
{
    return 'large'; //sets the related image size to large for quality reasons
}

function sb_et_woo_li_atc_args($args)
{
    $args['class'] = str_replace('button', '', $args['class']);

    $args['class'] .= ' et_pb_button et_pb_atc_button';

    return $args;
}

function sb_et_woo_li_handle_templates($located, $template_name, $args, $template_path, $default_path)
{
    global $sb_et_woo_li_requested;

    if ($template_name == 'checkout/form-checkout.php' && !isset($_GET['key'])) {
        if (sb_et_woo_li_get_checkout_layout()) {
            $sb_et_woo_li_requested = 'checkout';
            $located = dirname(__FILE__) . '/includes/empty.php';
        }
    } else if ($template_name == 'cart/cart.php') {
        if (sb_et_woo_li_get_cart_layout()) {
            $sb_et_woo_li_requested = 'cart';
            $located = dirname(__FILE__) . '/includes/empty.php';
        }
    } else if ($template_name == 'cart/cart-empty.php') {
        if (sb_et_woo_li_get_cart_layout_empty()) {
            $sb_et_woo_li_requested = 'cart_empty';
            $located = dirname(__FILE__) . '/includes/empty.php';
        }
    } else if ($template_name == 'myaccount/my-account.php') {
        if (sb_et_woo_li_get_acc_page_layout()) {
            $sb_et_woo_li_requested = 'account_page';
            $located = dirname(__FILE__) . '/includes/empty.php';
        }
    }

    //echo $template_name . '<br />';
    //echo $located;

    //my account etc.... and so on

    return $located;
}

function sb_et_woo_li_woo_active()
{
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-error">
						<p>Woo Layout Injector will not function without the WooCommerce plugin. Please add/activate it ASAP.</p>
					</div>';
    }
}

function sb_et_woo_li_builder_css()
{
    //$pt = get_post_type();

    //if ($pt != 'et_pb_layout') {
    //echo '<style>';
    //echo 'li.et_pb_woo_product_image8, li.et_pb_woo_title, li.et_pb_woo_text { display: none; }';
    //echo '</style>';
    //}
}

function sb_et_woo_li_disable_srcset($sources)
{
    return array();
}

function sb_et_woo_li_init_remove()
{
    if (get_option('sb_et_woo_li_disable_cart_cross_sell', 0)) {
        remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display'); //remove cross sell from cart
    }
}

function sb_et_woo_li_hook_replacement()
{
    //sale badge - archive
    if ($loc = get_option('sb_et_woo_li_sale_loop_location', 'none')) {
        if ($loc != 'none') {
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

            $hook = '';
            if ($loc == 'title') {
                $hook = 'sb_et_woo_li_loop_after_title';
            } else if ($loc == 'image') {
                $hook = 'sb_et_woo_li_loop_after_product_image';
            } else if ($loc == 'content') {
                $hook = 'sb_et_woo_li_loop_after_content';
            }

            if ($hook) {
                add_action($hook, 'woocommerce_show_product_loop_sale_flash', 10);
            }
        }
    }

    //sale badge - single
    if ($loc = get_option('sb_et_woo_li_sale_single_location', 'none')) {
        if ($loc != 'none') {
            remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

            $hook = '';
            if ($loc == 'title') {
                $hook = 'sb_et_woo_li_after_title';
            } else if ($loc == 'image') {
                $hook = 'sb_et_woo_li_after_product_image';
            } else if ($loc == 'content') {
                $hook = 'sb_et_woo_li_after_content';
            }

            if ($hook) {
                add_action($hook, 'woocommerce_show_product_sale_flash', 10);
            }
        }
    }

}

function sb_et_woo_li_sale_label($label)
{

    if ($new_label = get_option('sb_et_woo_li_sale_label')) {
        $label = '<span class="onsale">' . $new_label . '</span>';
    }

    return $label;
}

function sb_woo_get_product_image($product_id, $image_size = 'medium')
{
    if (!$url = get_the_post_thumbnail_url($product_id, $image_size)) {
        $url = sb_et_woo_li_placeholder_url('');
    }

    return $url;
}

function sb_et_woo_li_placeholder_url($url)
{
    if ($placeholder_url = get_option('sb_et_woo_li_placeholder_url')) {
        $url = $placeholder_url;
    }

    return $url;
}

function sb_et_woo_li_cart_button_text($label)
{

    if ($new_label = get_option('sb_et_woo_li_button_label')) {
        $label = $new_label;
    }

    return $label;
}

function sb_et_woo_li_get_gallery($product_id, $thumbnail_cols, $thumbnail_size = 'medium', $limit = 0, $offset = 0)
{
    $images = '';

    if ($gallery = get_post_meta($product_id, '_product_image_gallery', true)) {
        $gallery = explode(',', $gallery);
        $main_image = get_post_thumbnail_id($product_id);
        array_unshift($gallery, $main_image); //add main image to the gallery
        $i = $j = $total = 0;

        foreach ($gallery as $gallery_id) {
            $i++;

            if ($limit && $total > $limit) {
                break;
            }

            $total++;

            if ($offset && $total <= $offset) {
                continue;
            }

            $j++;

            $image = wp_get_attachment_image_src($gallery_id, $thumbnail_size);
            $image_l = wp_get_attachment_image_src($gallery_id, 'large');
            $anchor = '';

            if (is_single()) {
                $anchor = 'onclick="sb_woo_product_thumb_replace(jQuery(this));"';
            } else {
                $anchor = 'href="' . get_permalink($product_id) . '"';
            }

            $images .= '<div class="sb_woo_product_thumb_col sb_woo_product_thumb_col_num_' . $j . ' sb_woo_product_thumb_col_' . $thumbnail_cols . '">
                            <a style="cursor: pointer;" rel="sb-woo-images" class="sb-woo-images" data-large_image="' . $image_l[0] . '" ' . $anchor . '>
                                <img src="' . $image[0] . '" />
                            </a>
                          </div>';

            if ($j == $thumbnail_cols) {
                $images .= '<div class="sb_woo_clear">&nbsp;</div>';
                $i = $j = 0;
            }

        }
    }

    return $images;
}

function sb_et_woo_li_remove_reviews($tabs)
{
    unset($tabs['reviews']);
    return $tabs;
}

function sb_et_woo_li_get_attributes()
{
    $return = array();

    if ($taxonomies = get_taxonomies(false, 'objects')) {
        foreach ($taxonomies as $taxonomy) {
            if (substr($taxonomy->name, 0, 3) == 'pa_') {
                $return[$taxonomy->name] = $taxonomy->label;
            }
        }
    }

    //echo '<pre>';
    //print_r($return);
    //echo '</pre>';

    return $return;
}

function sb_et_woo_li_set_id($id)
{
    global $sb_woo_li_id_override, $sb_woo_li_id_override_obj, $product;

    if ($sb_woo_li_id_override != $id) {
        $sb_woo_li_id_override = $id;
        $sb_woo_li_id_override_obj = get_post($sb_woo_li_id_override);
    }

    if (!$product) {
        $product = wc_get_product($sb_woo_li_id_override);
    }
}

function sb_et_woo_li_clear_id()
{
    global $sb_woo_li_id_override, $sb_woo_li_id_override_obj;

    $sb_woo_li_id_override = 0;
    $sb_woo_li_id_override_obj = false;
}

function sb_et_woo_li_get_id()
{
    global $sb_woo_li_id_override, $product;

    $return = get_the_ID();

    //echo '<pre>';
    //print_r($product);
    //echo '</pre>';

    if ($sb_woo_li_id_override) {

        if (!$product && $sb_woo_li_id_override) {
            sb_et_woo_li_set_id($sb_woo_li_id_override); //set the override again therefore triggering the setting of the global
        }

        $return = $sb_woo_li_id_override;
    }

    return $return;
}

function sb_et_woo_li_get_id_obj()
{
    global $sb_woo_li_id_override_obj, $sb_woo_li_id_override, $product;

    if (!$product && $sb_woo_li_id_override) {
        sb_et_woo_li_set_id($sb_woo_li_id_override); //set the override again therefore triggering the setting of the global
    }

    return $sb_woo_li_id_override_obj;
}

function sb_et_woo_li_shortcode_content() {
    $return = '';

    $content = get_the_content();
    $return = do_shortcode($content);

    $return = apply_filters('sb_et_woo_li_shortcode_content', $return);

    return $return;
}

function sb_et_woo_li_shortcode_hook($atts) {
    $return = '';

    ob_start();
    do_action($atts['hook'], @$atts['argument_1'], @$atts['argument_2'], @$atts['argument_3']);
    $return = ob_get_clean();

    return $return;
}

if (!function_exists('woocommerce_template_loop_product_thumbnail')):
    function woocommerce_template_loop_product_thumbnail()
    { //so that overlays are links
        printf('<span class="et_shop_image">%1$s<a href="' . get_permalink(get_the_ID()) . '" class="et_overlay"></a></span>',
            woocommerce_get_product_thumbnail()
        );
    }
endif;

//causing issues with related items. not showing under certain circumstances
/*if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ):
    function woocommerce_template_loop_product_thumbnail() { //so that overlays are links
        printf( '<span class="et_shop_image">%1$s<a href="' . get_permalink(get_the_ID()) . '" class="et_overlay"></a></span>',
            woocommerce_get_product_thumbnail()
        );
    }
endif;*/

if (!function_exists('woocommerce_checkout_coupon_form')) {
    function woocommerce_checkout_coupon_form()
    {
        $layout_id = sb_et_woo_li_get_checkout_layout();

        if ($layout_id) {
            echo '<div class="sb_et_woo_li_coupon_form">';
        }

        wc_get_template('checkout/form-coupon.php', array('checkout' => WC()->checkout()));

        if ($layout_id) {
            echo '</div>';
        }
    }
}

/*if ( ! function_exists( 'woocommerce_account_navigation' ) ) {
    if (sb_et_woo_li_get_acc_nav_layout()) {
        function woocommerce_account_navigation()
        {
            $layout_id = sb_et_woo_li_get_acc_nav_layout();

            if ($layout_content = do_shortcode('[et_pb_section global_module="' . $layout_id . '"][/et_pb_section]')) {
                wc_get_template('myaccount/navigation.php');
            }
        }
    }
}*/

if (!function_exists('et_show_cart_total')) {
    if (get_option('sb_et_woo_li_use_mini_cart', 0)) {
        function et_show_cart_total($args = array())
        {
            if (!class_exists('woocommerce') || !WC()->cart) {
                return;
            }

            $defaults = array(
                'no_text' => false,
            );

            $args = wp_parse_args($args, $defaults);

            if (!$items_number = WC()->cart->get_cart_contents_count()) {
                $items_number = '';
            }

            $url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : WC()->cart->get_cart_url();

            $divi = get_option('et_divi');

            //echo '<pre>';
            //print_r($divi);
            //echo '</pre>';

            if (@$divi['header_style'] == 'slide') {
                $no_text = 1;
            } else {
                $no_text = 0;
            }

            if ($no_text) {
                echo '<a href="' . esc_url($url) . '" class="et-cart-info">';
                echo '<span>' . esc_html(sprintf(_nx('%1$s Item', '%1$s Items', $items_number, 'WooCommerce items number', 'Divi'), number_format_i18n($items_number))) . '</span>';
                echo '</a>';
            } else {
                echo '<div class="sb_woo_prod_cart_container woocommerce">';

                echo '<a class="et-cart-info" href="' . esc_url($url) . '">
                        <span>' . $items_number . '</span>
                      </a>';

                if ($items_number) {

                    echo '<div class="sb_woo_mini_cart_container">';
                    echo '<div class="sb_woo_mini_cart">';

                    do_action('sb_et_woo_li_pre_mini_cart');
                    woocommerce_mini_cart();
                    do_action('sb_et_woo_li_post_mini_cart');

                    echo '</div>';
                    echo '</div>';
                }

                echo '</div>';
            }

        }
    }
}

?>