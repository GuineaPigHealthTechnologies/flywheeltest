<?php

function sb_et_woo_li_get_layout()
{
    $product_layout = get_option('sb_et_woo_li_product_page');
    $cat_layout = get_option('sb_et_woo_li_product_cat');
    $tag_layout = get_option('sb_et_woo_li_product_tag');

    if (!$override = get_post_meta(sb_et_woo_li_get_id(), "sb_et_woo_li_layout_overrides", true)) {
        $product_terms = wp_get_object_terms(sb_et_woo_li_get_id(), 'product_cat');
        if (!empty($product_terms)) {
            if (!is_wp_error($product_terms)) {
                foreach ($product_terms as $term) {
                    if (isset($cat_layout[$term->slug]) && $cat_layout[$term->slug]) {
                        $product_layout = $cat_layout[$term->slug];
                        break;
                    }
                }
            }
        }

        $product_terms = wp_get_object_terms(sb_et_woo_li_get_id(), 'product_tag');
        if (!empty($product_terms)) {
            if (!is_wp_error($product_terms)) {
                foreach ($product_terms as $term) {
                    if (isset($tag_layout[$term->slug]) && $tag_layout[$term->slug]) {
                        $product_layout = $tag_layout[$term->slug];
                        break;
                    }
                }
            }
        }
    } else {
        $product_layout = $override;
    }

    return $product_layout;
}

function sb_et_woo_li_pagination()
{
    if (function_exists('wp_pagenavi')) {
        wp_pagenavi();
    } else {
        global $wp_query;
        $total = $wp_query->max_num_pages;
        $current = get_query_var('paged');
        $base = isset($base) ? $base : esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false))));
        $format = isset($format) ? $format : '';

        echo '<nav class="woocommerce-pagination">';
        echo paginate_links(apply_filters('woocommerce_pagination_args', array( // WPCS: XSS ok.
            'base' => $base,
            'format' => $format,
            'add_args' => false,
            'current' => max(1, $current),
            'total' => $total,
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
            'type' => 'list',
            'end_size' => 3,
            'mid_size' => 3,
        )));
        echo '</nav>';
    }
}

function sb_et_woo_li_get_cart_layout()
{
    return get_option('sb_et_woo_li_cart_page', 0);
}

function sb_et_woo_li_get_checkout_layout()
{
    return get_option('sb_et_woo_li_checkout_page', 0);
}

function sb_et_woo_li_get_cart_layout_empty()
{
    return get_option('sb_et_woo_li_cart_page_empty', 0);
}

/*function sb_et_woo_li_get_acc_nav_layout()
{
    return get_option('sb_et_woo_li_acc_nav_page', 0);
}*/

function sb_et_woo_li_get_acc_page_layout()
{
    return get_option('sb_et_woo_li_acc_page_page', 0);
}

function sb_et_woo_li_get_archive_layout()
{
    $product_layout = get_option('sb_et_woo_li_shop_archive_page');
    $cat_layout = get_option('sb_et_woo_li_product_cat_archive');
    $tag_layout = get_option('sb_et_woo_li_product_tag_archive');
    $cat_layout_general = get_option('sb_et_woo_li_product_cat_archive_general');
    $tag_layout_general = get_option('sb_et_woo_li_product_tag_archive_general');

    if (is_tax('product_cat')) {
        global $wp_query;
        if (isset($wp_query->queried_object->slug)) {
            $slug = $wp_query->queried_object->slug;

            if (isset($cat_layout[$slug]) && $cat_layout[$slug]) {
                $product_layout = $cat_layout[$slug];
            } else if ($cat_layout_general) {
                $product_layout = $cat_layout_general;
            }
        }
    } else if (is_tax('product_tag')) {
        global $wp_query;
        if (isset($wp_query->queried_object->slug)) {
            $slug = $wp_query->queried_object->slug;

            if (isset($tag_layout[$slug]) && $tag_layout[$slug]) {
                $product_layout = $tag_layout[$slug];
            } else if ($tag_layout_general) {
                $product_layout = $tag_layout_general;
            }
        }
    }

    return $product_layout;
}

function sb_et_woo_li_remove_actions($remove_only = false, $product_layout = false)
{
    global $post;

    //if the legacy module is enabled then don't turn off these actions
    if ($product_layout) {
        $layout = get_post($product_layout);
        if (stripos($layout->post_content, 'et_pb_woo_general') !== false) {
            return false;
        }
    } else {
        if (stripos($post->post_content, 'et_pb_woo_general') !== false) {
            return false;
        }
    }

    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

    remove_action('woocommerce_before_single_product', 'wc_print_notices', 10);

    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

    if (!$remove_only) {
        add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    }
}

function sb_et_woo_li_template_include($template)
{

    $is_page_builder_used = et_pb_is_pagebuilder_used(sb_et_woo_li_get_id());
    $base_template = basename($template);
    $admin_mode = get_option('sb_et_woo_li_admin_mode');
    $is_admin = current_user_can('administrator');

    if ($base_template == 'single-product.php') {
        $product_layout = sb_et_woo_li_get_layout();

        if (!$admin_mode || ($admin_mode && $is_admin)) {
            if ($product_layout || $is_page_builder_used) {
                sb_et_woo_li_remove_actions(null, $product_layout); //only when we have a layout should we remove the actions

                $template = dirname(__FILE__) . '/empty.php'; //stops woo from taking over the layout
            }
        }

    } else if ($base_template == 'archive-product.php' || $base_template == 'taxonomy-product_cat.php' || $base_template == 'taxonomy-product_tag.php') {
        //$shop = get_option( 'woocommerce_shop_page_id' );
        $product_layout = sb_et_woo_li_get_archive_layout();

        if (!$admin_mode || ($admin_mode && $is_admin)) {
            if ($product_layout) {
                sb_et_woo_li_remove_actions(null, $product_layout); //only when we have a layout should we remove the actions

                $template = dirname(__FILE__) . '/empty.php'; //stops woo from taking over the layout
            }
        }
    }

    return $template;
}

function sb_et_woo_li_template()
{
    global $sb_et_woo_li_requested;

    if (!$sb_et_woo_li_requested) {
        $product_layout = false;
        $is_page_builder_used = false;
        $layout_shortcodes = '';

        get_header('shop');

        if (is_single()) {
            $is_page_builder_used = et_pb_is_pagebuilder_used(sb_et_woo_li_get_id());

            if (!$is_page_builder_used) {
                $product_layout = sb_et_woo_li_get_layout();
            }
        } else if (is_archive()) {
            $product_layout = sb_et_woo_li_get_archive_layout();
        }

        if ($product_layout) {
            if ($section = do_shortcode('[et_pb_section global_module="' . $product_layout . '"][/et_pb_section]')) {
                $layout_shortcodes = $section;
            }
        } else {
            global $post;
            global $product;

            $product = wc_get_product($post->ID);
            $layout_shortcodes = do_shortcode($post->post_content);
        }

        if ($layout_shortcodes) {
            echo '<div id="main-content">';
            echo '<div class="product ' . (get_post_type() == 'product' ? 'post-' . sb_et_woo_li_get_id() : '') . '">
                    <div class="entry-summary">';

            if (stristr($layout_shortcodes, 'woocommerce-message') === false) {
                wc_print_notices();
            }

            echo $layout_shortcodes . '</div></div></div>'; //single product layout
        }

        get_footer('shop');
    }
}

function sb_et_woo_li_get_orders_layout_html()
{
    global $sb_et_woo_li_account_layouts;

    if (isset($sb_et_woo_li_account_layouts['orders'])) {
        echo sb_et_woo_li_get_layout_html($sb_et_woo_li_account_layouts['orders']);
    }

}

function sb_et_woo_li_get_downloads_layout_html()
{
    global $sb_et_woo_li_account_layouts;

    if (isset($sb_et_woo_li_account_layouts['downloads'])) {
        echo sb_et_woo_li_get_layout_html($sb_et_woo_li_account_layouts['downloads']);
    }

}

function sb_et_woo_li_get_edit_account_layout_html()
{
    global $sb_et_woo_li_account_layouts;

    if (isset($sb_et_woo_li_account_layouts['edit_account'])) {
        echo sb_et_woo_li_get_layout_html($sb_et_woo_li_account_layouts['edit_account']);
    }

}

function sb_et_woo_li_get_addresses_layout_html()
{
    global $sb_et_woo_li_account_layouts;

    if (isset($sb_et_woo_li_account_layouts['addresses'])) {
        echo sb_et_woo_li_get_layout_html($sb_et_woo_li_account_layouts['addresses']);
    }

}

function sb_et_woo_li_get_layout_html($layout_id)
{
    $content = '';

    if ($section = do_shortcode('[et_pb_section global_module="' . $layout_id . '"][/et_pb_section]')) {
        $content = $section;
    }

    return $content;
}

function sb_et_woo_li_content_filter($content)
{
    global $sb_et_woo_li_requested;
    $layout_id = 0;

    if ($sb_et_woo_li_requested == 'checkout') {
        $layout_id = sb_et_woo_li_get_checkout_layout();
    } else if ($sb_et_woo_li_requested == 'cart') {
        $layout_id = sb_et_woo_li_get_cart_layout();
    } else if ($sb_et_woo_li_requested == 'cart_empty') {
        $layout_id = sb_et_woo_li_get_cart_layout_empty();
    } else if ($sb_et_woo_li_requested == 'account_page') {
        $layout_id = sb_et_woo_li_get_acc_page_layout();
    }

    if ($layout_id) {
        if ($sb_et_woo_li_requested == 'checkout') {  //makes the coupon functionality only kick in when the checkout itself it overridden
            //moves coupon fields below the form
            add_action('woocommerce_after_checkout_form', 'woocommerce_checkout_coupon_form', 10);
            remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form');

            //attach actions to their respective modules instead of being together
            remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
            remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
            add_action('sb_et_woo_li_checkout_order_review', 'woocommerce_order_review');
            add_action('sb_et_woo_li_checkout_order_payment', 'woocommerce_checkout_payment');
        }

        if ($layout_content = do_shortcode('[et_pb_section global_module="' . $layout_id . '"][/et_pb_section]')) {

            if ($sb_et_woo_li_requested == 'checkout') {
                ////////////////////////////////////////////////////////////

                //if (!WC()->cart->is_empty()) { // Check cart has contents - no need as it's automatic i think

                do_action('woocommerce_check_cart_items'); // Check cart contents for errors
                WC()->cart->calculate_totals(); // Calc totals
                $checkout = WC()->checkout(); // Get checkout object

                ////////////////////////////////////////////////////////////

                ob_start();
                wc_print_notices(); // Show non-cart errors

                echo '<div class="woocommerce">';
                do_action('woocommerce_before_checkout_form', $checkout); //for the coupon form etc.
                echo '<form name="checkout" method="post" class="checkout woocommerce-checkout" action="' . esc_url(wc_get_checkout_url()) . '" enctype="multipart/form-data">';
                echo do_shortcode('[shop_messages]');

                $form_start = ob_get_clean();

                ////////////////////////////////////////////////////////////

                ob_start();
                do_action('woocommerce_after_checkout_form', $checkout);

                if ($after_checkout = ob_get_clean()) {
                    $after_checkout = '<div class="et_pb_section woocommerce_after_checkout_form et_section_regular">
				        <div class=" et_pb_row ">
				            <div class="et_pb_column et_pb_column_4_4 et_pb_column_4">
				                <div class="et_pb_module">' . $after_checkout . '</div>
			                </div>
			            </div>
			        </div>';
                }

                //$after_checkout .= '</div></div></div>'; // to end the containing element

                ////////////////////////////////////////////////////////////

                $content = $form_start . $layout_content . '</form>' . $after_checkout . '</div>';

                ////////////////////////////////////////////////////////////
            } else if ($sb_et_woo_li_requested == 'cart_empty') {
                $content = '<div class="woocommerce sb_et_woo_li_cart_empty">' . $layout_content . '</div>';

                ////////////////////////////////////////////////////////////
            } else if ($sb_et_woo_li_requested == 'account_page') {
                $content = '<div class="woocommerce sb_et_woo_li_account_page">' . $layout_content . '</div>';

            } else if ($sb_et_woo_li_requested == 'cart') {
                ////////////////////////////////////////////////////////////////

                ob_start();
                echo '<div class="woocommerce">';
                wc_print_notices(); // Show non-cart errors
                do_action('woocommerce_before_cart');
                echo '<form class="woocommerce-cart-form" action="' . esc_url(wc_get_cart_url()) . '" method="post">';
                do_action('woocommerce_before_cart_table');
                $form_start = ob_get_clean();

                ////////////////////////////////////////////////////////////////

                $content = $form_start . $layout_content . '</form></div>';
            }
        }
    }

    return $content;
}

function sb_et_woo_li_checkout_remove_company($fields)
{

    unset($fields['billing']['billing_company']);
    unset($fields['shipping']['shipping_company']);

    return $fields;
}

function sb_et_woo_li_add_builder($post_types)
{
    if (get_option('sb_et_woo_li_add_builder')) {
        $post_types[] = 'product';
    }

    return $post_types;
}

function sb_et_woo_li_get_current_account_page()
{
    global $wp;

    $name = 'dashboard';

    if (!empty($wp->query_vars)) {
        foreach ($wp->query_vars as $key => $value) {
            // Ignore pagename param.
            if ('pagename' === $key) {
                continue;
            }
            $name = $key;
            //break;
        }
    }

    if ($name == 'page') {
        $name = 'dashboard';
    }

    return $name;
}


?>