<?php

/*
 * Plugin Name: Woo Layout Injector
 * Plugin URI:  http://www.sean-barton.co.uk
 * Description: A plugin to handle the layouts of WooCommerce pages using the ET layout builder system. 
 * Author:      Sean Barton - Tortoise IT
 * Version:     4.5
 * Author URI:  http://www.sean-barton.co.uk
 */

//global variables
$sb_et_woo_li_image_size = '';
$sb_et_woo_li_requested = '';
$sb_et_woo_li_is_wli_page = -1;
$sb_et_woo_li_account_layouts = array();
$sb_woo_li_id_override = 0; //for use by the single product widget to temporarily override the current ID to get things like images and prices

//constants
define('SB_ET_WOO_LI_VERSION', '4.5');
define('SB_ET_WOO_LI_STORE_URL', 'https://elegantmarketplace.com');
define('SB_ET_WOO_LI_ITEM_NAME', 'Woo Layout Injector');
define('SB_ET_WOO_LI_ITEM_ID', 50273);
define('SB_ET_WOO_LI_AUTHOR_NAME', 'Sean Barton');
define('SB_ET_WOO_LI_FILE', __FILE__);

//requires
require_once('includes/divi_woo_li_admin.php');
require_once('includes/divi_woo_li_admin_tabs.php');
require_once('includes/divi_woo_li_injector.php');

//licensing
require_once('includes/emp-licensing.php');

//actions
add_action('plugins_loaded', 'sb_et_woo_li_init');
add_action('wp_loaded', 'sb_et_woo_li_init_remove');
add_action('query_posts', 'sb_et_woo_li_is_wli_page', 5);
add_action('parse_query', 'sb_et_woo_li_hook_replacement');

function sb_et_woo_li_fragments()
{
    $layout_cart = get_option('sb_et_woo_li_cart_page');
    $layout_cart_empty = get_option('sb_et_woo_li_cart_page_empty');
    $layout_checkout = get_option('sb_et_woo_li_checkout_page');

    $remove = false;

    if (is_cart() && ($layout_cart || $layout_cart_empty)) {
        $remove = true;
    } else if (is_checkout() && $layout_checkout) {
        $remove = true;
    }

    if ($remove) {
        add_filter('woocommerce_add_to_cart_fragments', '__return_false', 999, 999); //was causing issues with the cart/checkout. maybe make this optional whether the header cart/cart layout/checkout layout is used
    }
}

function sb_et_woo_li_init()
{
    add_shortcode('wli_content', 'sb_et_woo_li_shortcode_content'); //[wli_content] will output the content itself
    add_shortcode('wli_hook', 'sb_et_woo_li_shortcode_hook'); // call a php action [wli hook="before_content" argument_1="123" argument_2="123" argument_3="123"]

    add_action('body_class', 'sb_et_woo_li_body_class');
    add_action('admin_menu', 'sb_et_woo_li_submenu');
    add_action('et_builder_ready', 'sb_et_woo_li_theme_setup', 11);
    add_action('admin_head', 'sb_et_woo_li_admin_head', 9999);
    add_action('wp_enqueue_scripts', 'sb_et_woo_li_enqueue', 9999);
    add_action('admin_enqueue_scripts', 'sb_et_woo_li_admin_enqueue', 9999);
    add_action("save_post", "sb_et_woo_li_meta_box_save", 10, 3);
    add_action("add_meta_boxes", "sb_et_woo_li_meta_box");
    add_action('admin_notices', 'sb_et_woo_li_woo_notices');
    add_action('wp_head', 'sb_et_woo_li_header_css');
    add_action('wp_loaded', 'sb_et_woo_li_fragments');
    add_action('save_post', 'sb_et_woo_li_save_tab_postdata');
    add_action('woocommerce_before_template_part', 'sb_et_woo_li_pre_template_part', 10, 4);
    add_action('woocommerce_after_template_part', 'sb_et_woo_li_post_template_part', 10, 4);

    add_filter('woocommerce_product_tabs', 'sb_et_woo_li_new_tabs', 50);
    add_filter('woocommerce_product_single_add_to_cart_text', 'sb_et_woo_li_cart_button_text', 99, 1);
    add_filter('woocommerce_product_add_to_cart_text', 'sb_et_woo_li_cart_button_text', 99, 1);
    add_filter('template_include', 'sb_et_woo_li_template_include', 99);
    add_filter('wp_calculate_image_srcset', 'sb_et_woo_li_disable_srcset');
    add_filter('admin_footer_text', '__return_empty_string');
    add_filter('update_footer', '__return_empty_string');
    add_filter('wc_get_template', 'sb_et_woo_li_handle_templates', 10, 10);
    add_filter('the_content', 'sb_et_woo_li_content_filter', 999, 999);
    add_filter('woocommerce_sale_flash', 'sb_et_woo_li_sale_label');
    add_filter('single_product_archive_thumbnail_size', 'sb_single_product_archive_thumbnail_size');
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sb_et_woo_li_action_links');
    add_filter('woocommerce_ajax_variation_threshold', 'sb_et_woo_li_variation_threshold', 10, 2);
    add_filter('et_builder_post_types', 'sb_et_woo_li_add_builder');
    add_filter('et_fb_post_types', 'sb_et_woo_li_add_builder');

    define('SB_ET_WOO_LI_PRODUCT_COUNT', apply_filters('sb_et_woo_li_product_count', 100)); //used by the single product module to avoid having to cache a list of thousands of products for larger stores. Defaulted to 100 but able to be filtered for larger stores
    define('SB_ET_WOO_LI_VARIATION_THRESHOLD', apply_filters('sb_et_woo_li_variation_threshold', 250));

    sb_et_woo_li_third_party_support();

}

function sb_et_woo_li_third_party_support()
{
    if (in_array('woo-variations-table/woo-variations-table.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        remove_action('plugins_loaded', 'remove_variable_product_add_to_cart');
        remove_action('woocommerce_single_product_summary', 'woo_variations_table_available_options_btn', 11);
        remove_filter('woocommerce_after_single_product_summary', 'variations_table_print_table', 9);
        add_filter('woocommerce_single_product_summary', 'variations_table_print_table', 9);
    }
}

function sb_et_woo_li_body_class($classes)
{

    if (sb_et_woo_li_is_wli_page(true)) {
        $classes[] = 'wli_injected';
    }

    return $classes;
}

function sb_et_woo_li_pre_template_part($template_name, $template_path, $located, $args)
{
    $premable = false;

    $preambles = array(
        'notices/success.php',
        //'cart/cart-empty.php'
    );

    //echo $template_name;
    if (in_array($template_name, $preambles)) {
        $premable = true;
    }

    if ($template_name == 'checkout/form-coupon.php') {
        if (sb_et_woo_li_get_checkout_layout()) {
            $premable = true;
        }
    }
    if ($template_name == 'checkout/form-login.php' && !is_user_logged_in()) {
        if (sb_et_woo_li_get_checkout_layout()) {
            $premable = true;
        }
    }

    //////////////////////////////////////////

    if ($premable) {
        echo '<div class="et_pb_section wli_wrapper wli_wrapper_' . sanitize_title(rtrim($template_name, '.php')) . ' et_section_regular">
				        <div class=" et_pb_row ">
				            <div class="et_pb_column et_pb_column_4_4 et_pb_column_4">';
    }
}

function sb_et_woo_li_post_template_part($template_name, $template_path, $located, $args)
{
    $suffix = false;
    $suffixes = array(
        'notices/success.php',
        //'cart/cart-empty.php'
    );

    //echo $template_name;
    if (in_array($template_name, $suffixes)) {
        $suffix = true;
    }

    if ($template_name == 'checkout/form-coupon.php') {
        if (sb_et_woo_li_get_checkout_layout()) {
            $suffix = true;
        }
    }
    if ($template_name == 'checkout/form-login.php') {
        if (sb_et_woo_li_get_checkout_layout() && !is_user_logged_in()) {
            $suffix = true;
        }
    }

    if ($suffix) {
        echo '</div></div></div>';
    }
}

function sb_et_woo_li_variation_threshold($qty, $product)
{
    return SB_ET_WOO_LI_VARIATION_THRESHOLD;
}

function sb_et_woo_li_action_links($links)
{
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=sb_et_woo_li')) . '">Settings</a>';
    $links[] = '<a href="https://www.facebook.com/groups/599390973725519" target="_blank">Support</a>';
    $links[] = '<a href="https://elegantmarketplace.com/vendor/sean" target="_blank">More from Tortoise IT</a>';
    return $links;
}


function sb_single_product_archive_thumbnail_size()
{
    return 'large'; //sets the related image size to large for quality reasons
}

//is this page related to woo and is the injector in use in this context
function sb_et_woo_li_is_wli_page($force = false)
{
    global $sb_et_woo_li_is_wli_page;

    if ($sb_et_woo_li_is_wli_page === -1 || $force) {

        $sb_et_woo_li_is_wli_page = false;

        $layout_shop = get_option('sb_et_woo_li_shop_archive_page');
        $layout_product = get_option('sb_et_woo_li_product_page');
        $cat_layout = get_option('sb_et_woo_li_product_cat_archive');
        $tag_layout = get_option('sb_et_woo_li_product_tag_archive');
        $layout_cart = get_option('sb_et_woo_li_cart_page');
        $layout_checkout = get_option('sb_et_woo_li_checkout_page', 0);
        $layout_account = get_option('sb_et_woo_li_acc_page_page', 0);

        if (is_product() && $layout_product) {
            $sb_et_woo_li_is_wli_page = true;
        } else if (is_cart() && $layout_cart) {
            $sb_et_woo_li_is_wli_page = true;
        } else if (is_checkout() && $layout_checkout) {
            $sb_et_woo_li_is_wli_page = true;
        } else if (is_account_page() && $layout_account) {
            $sb_et_woo_li_is_wli_page = true;
        } else if (is_shop() && $layout_shop) {
            $sb_et_woo_li_is_wli_page = true;
        } else if (is_tax('product_cat') && $cat_layout) {
            $sb_et_woo_li_is_wli_page = true;
        } else if (is_tax('product_tag') && $tag_layout) {
            $sb_et_woo_li_is_wli_page = true;
        }

    }

    return $sb_et_woo_li_is_wli_page;
}

function sb_et_woo_li_atc_args($args)
{
    $args['class'] = str_replace('button', '', $args['class']);

    $args['class'] .= ' et_pb_button et_pb_atc_button';

    return $args;
}

function sb_et_woo_li_handle_templates($located, $template_name)
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

    return $located;
}

function sb_et_woo_li_woo_notices()
{
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-error">
						<p>Woo Layout Injector will not function without the WooCommerce plugin. Please add/activate it ASAP.</p>
					</div>';
    } else {
        if (isset($_GET['post'])) {
            $id = $_GET['post'];

            $is_cart = ($id == wc_get_page_id('cart'));
            $is_checkout = ($id == wc_get_page_id('checkout'));
            $is_account = ($id == wc_get_page_id('myaccount'));
            $is_shop = ($id == wc_get_page_id('shop'));

            if ($is_cart || $is_checkout || $is_account || $is_shop) {
                $overridden = false;

                echo '<div class="notice notice-error">
                            <h2>' . SB_ET_WOO_LI_ITEM_NAME . '</h2>            
                            <p>If you\'d like to customise this page using Woo Layout Injector then please add a layout using the Divi Library and assign it using the <a href="' . admin_url('admin.php?page=sb_et_woo_li') . '" target="_blank">WLI settings page</a>. Once you\'ve done that the layout and configuration on this page won\'t be usable.</p>';

                if ($is_cart) {
                    if ($layout = get_option('sb_et_woo_li_cart_page')) {
                        $overridden = true;
                    }
                } else if ($is_checkout) {
                    if ($layout = get_option('sb_et_woo_li_checkout_page')) {
                        $overridden = true;
                    }
                } else if ($is_account) {
                    if ($layout = get_option('sb_et_woo_li_acc_page_page')) {
                        $overridden = true;
                    }
                } else if ($is_shop) {
                    if ($layout = get_option('sb_et_woo_li_shop_archive_page')) {
                        $overridden = true;
                    }
                }

                if (!$is_shop) {
                    echo '<p>Please leave the original WooCommerce shortcode in-tact or it might cause problems. You can turn on the Divi Builder in order to remove the sidebar which most will find preferable.</p>';
                }

                if ($overridden) {
                    if ($layout = get_post($layout)) {
                        echo '<p><strong>This page has been assigned to a Divi Library layout. Edit it by clicking this link: <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout->ID . '&action=edit') . '">' . $layout->post_title . '</a></strong></p>';
                    }
                }

                echo '</div>';
            }
        }
    }

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

function sb_et_woo_li_header_css()
{
    global $sb_et_woo_li_is_wli_page;

    if ($sb_et_woo_li_is_wli_page) {
        //sale badges
        $loc = get_option('sb_et_woo_li_sale_loop_location', 'none');
        $loc2 = get_option('sb_et_woo_li_sale_single_location', 'none');

        if ($loc != 'none' || $loc2 != 'none') {
            echo '<style>';

            $bg = get_option('sb_et_woo_li_sale_bg_colour');
            $text = get_option('sb_et_woo_li_sale_colour');

            if ($bg != $text) { //no we don't want them to be the same colour... that'll cause support tickets! Nope!
                echo '  .product span.onsale {
                            background: ' . $bg . ' !important;
                            color: ' . $text . ' !important;
                        }';
            }

            echo '</style>';
        }
    }
}

function sb_et_woo_li_hook_replacement()
{
    global $sb_et_woo_li_is_wli_page;

    if ($sb_et_woo_li_is_wli_page) {

        //sale badge - archive
        if ($loc = get_option('sb_et_woo_li_sale_loop_location', 'none')) {
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

            if ($loc != 'none') {
                //echo $loc;
                //remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

                $hook = '';
                if ($loc == 'title') {
                    $hook = array('sb_et_woo_li_before_archive_title', 'sb_et_woo_li_loop_after_title');
                } else if ($loc == 'image') {
                    $hook = array('sb_et_woo_li_archive_image', 'sb_et_woo_li_loop_after_product_image');
                } else if ($loc == 'content') {
                    $hook = array('woocommerce_after_shop_loop_item_title', 'sb_et_woo_li_loop_after_content');
                }

                if ($hook) {
                    if (is_array($hook)) {
                        foreach ($hook as $ho) {
                            add_action($ho, 'woocommerce_show_product_loop_sale_flash', 10);
                        }
                    } else {
                        add_action($hook, 'woocommerce_show_product_loop_sale_flash', 10);
                    }
                }
            }
        }

        //sale badge - single
        if ($loc = get_option('sb_et_woo_li_sale_single_location', 'none')) {
            remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

            if ($loc != 'none') {
                //echo $loc;
                //remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

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

}

function sb_et_woo_li_loop_image()
{
    global $sb_et_woo_li_image_size;

    return $sb_et_woo_li_image_size;
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

            $images .= '<div class="sb_woo_product_thumb_col sb_woo_product_thumb_col_num_' . $j . ' sb_woo_product_thumb_col_' . $thumbnail_cols . '">';

            $images .= apply_filters('woocommerce_single_product_image_html', '<a style="cursor: pointer;" rel="sb-woo-images" class="sb-woo-images" data-large_image="' . $image_l[0] . '" ' . $anchor . '>
                            <img src="' . $image[0] . '" />
                        </a>', $gallery_id);

            $images .= '</div>';

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

function sb_et_woo_li_remove_content($tabs)
{
    unset($tabs['description']);
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

function sb_et_woo_li_shortcode_content()
{
    $content = get_the_content();
    $return = do_shortcode($content);
    $return = wpautop($return);

    $return = apply_filters('sb_et_woo_li_shortcode_content', $return);

    return $return;
}

function sb_et_woo_li_shortcode_hook($atts)
{
    ob_start();
    do_action($atts['hook'], @$atts['argument_1'], @$atts['argument_2'], @$atts['argument_3']);
    $return = ob_get_clean();

    return $return;
}

if (!function_exists('woocommerce_template_loop_product_thumbnail')):
    function woocommerce_template_loop_product_thumbnail()
    { //so that overlays are links
        if ($thumb = woocommerce_get_product_thumbnail()) {
            echo '<span class="et_shop_image">' . $thumb . '<span onclick="document.location=\'' . get_permalink(get_the_ID()) . '\'" style="cursor: pointer;" class="et_overlay"></span></span>';

        }
    }
endif;

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

                //if ($items_number) {

                echo '<div class="sb_woo_mini_cart_container">';
                echo '<div class="sb_woo_mini_cart">';

                do_action('sb_et_woo_li_pre_mini_cart');
                echo '<div class="widget_shopping_cart_content"></div>';
                //woocommerce_mini_cart();
                do_action('sb_et_woo_li_post_mini_cart');

                echo '</div>';
                echo '</div>';
                //}

                echo '</div>';
            }

        }
    }
}

?>