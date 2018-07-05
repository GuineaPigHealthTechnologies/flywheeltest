<?php

function sb_et_woo_li_admin_enqueue()
{
    $file = dirname(__FILE__);

    wp_enqueue_style('sb_et_woo_li_spectrum', plugins_url('/includes/css/spectrum.css', $file));
    wp_enqueue_script('sb_et_woo_li_spectrum_js', plugins_url('/includes/js/spectrum.js', $file));
    wp_enqueue_script('sb_et_woo_li_admin_js', plugins_url('/includes/js/admin.js', $file));
}

function sb_et_woo_li_enqueue()
{
    $file = dirname(__FILE__);

    wp_enqueue_style('sb_et_woo_li_css', plugins_url('/includes/css/style.css', $file));

    if (get_option('sb_et_woo_li_disable_zoom', 0)) {
        wp_enqueue_script('sb_et_woo_li_zoom', plugins_url('/includes/js/jquery.zoom.min.js', $file));
    } else {
        remove_theme_support('wc-product-gallery-zoom');
        remove_theme_support('wc-product-gallery-lightbox');
        remove_theme_support('wc-product-gallery-slider');
    }

    if (get_post_type() == 'product' || is_checkout() || is_cart()) {
        wp_enqueue_style('sb_et_woo_li_cbox', plugins_url('/includes/js/example3/colorbox.css', $file));
        wp_enqueue_script('sb_et_woo_li_cbox', plugins_url('/includes/js/jquery.colorbox-min.js', $file));
    }

    wp_enqueue_script('sb_et_woo_li_iloaded', plugins_url('/includes/js/imagesloaded.pkgd.min.js', $file));
    wp_enqueue_script('sb_et_woo_li_js', plugins_url('/includes/js/script.js', $file));
}


function sb_et_woo_li_admin_head()
{
    if ((isset($_GET['post_type']) && $_GET['post_type'] == 'et_pb_layout') || stripos($_SERVER['PHP_SELF'], 'wp-admin/index.php') !== false || isset($_GET['sb_purge_cache'])) {

        $prop_to_remove = array(
            //'et_pb_templates_et_pb_woo_loop_archive'
            'et_pb_templates_et_pb_woo_archive'
        , 'et_pb_templates_et_pb_woo_checkout_billing'
        , 'et_pb_templates_et_pb_woo_checkout_shipping'
        , 'et_pb_templates_et_pb_woo_checkout_payment'
        , 'et_pb_templates_et_pb_woo_checkout_coupon'
        , 'et_pb_templates_et_pb_woo_checkout_review'
        , 'et_pb_templates_et_pb_woo_cart_products'
        , 'et_pb_templates_et_pb_woo_cart_totals'
        , 'et_pb_templates_et_pb_woo_account_page'
        , 'et_pb_templates_et_pb_woo_account_nav'
        , 'et_pb_templates_et_pb_woo_account_orders'
        , 'et_pb_templates_et_pb_woo_account_downloads'
        , 'et_pb_templates_et_pb_woo_account_details'
        , 'et_pb_templates_et_pb_woo_notices'
        , 'et_pb_templates_et_pb_woo_title'
        , 'et_pb_templates_et_pb_woo_short_content'
        , 'et_pb_templates_et_pb_woo_single_product'
        , 'et_pb_templates_et_pb_woo_atc'
        , 'et_pb_templates_et_pb_woo_read_more'
        , 'et_pb_templates_et_pb_woo_text'
        , 'et_pb_templates_et_pb_woo_breadcrumb'
        , 'et_pb_templates_et_pb_woo_product_image8'
        , 'et_pb_templates_et_pb_woo_info_meta'
        , 'et_pb_templates_et_pb_woo_meta'
        , 'et_pb_templates_et_pb_woo_price'
        , 'et_pb_templates_et_pb_woo_product_category'
        , 'et_pb_templates_et_pb_woo_related'
        , 'et_pb_templates_et_pb_woo_reviews'
        , 'et_pb_templates_et_pb_woo_tabs'
        , 'et_pb_templates_et_pb_woo_thumbs5'
        , 'et_pb_templates_et_pb_woo_upsell'
        , 'et_pb_templates_et_pb_woo_cross_sell'
        );

        $js_prop_to_remove = 'var sb_ls_remove = ["' . implode('","', $prop_to_remove) . '"];';

        echo '<script>
        
        ' . $js_prop_to_remove . '
        
        for (var prop in localStorage) {
            if (sb_ls_remove.indexOf(prop) != -1) {
                localStorage.removeItem(prop);
            }
        }
        
        </script>';
    }
}

function sb_et_woo_li_theme_setup()
{

    if (class_exists('ET_Builder_Module')) {

        $modules_path = trailingslashit(dirname(__FILE__)) . 'modules/';

        $is_layout = $is_woo = false;

        //if (isset($_GET['post'])) {
        //$pt = get_post_type($_GET['post']);

        //if (substr($pt, 0, 5) == 'et_pb') {
        //$is_layout = true;
        //} else if ($pt == 'product') {
        //$is_woo = true;
        //}
        //}

        if (1 || $is_woo || $is_layout || !is_admin()) {

            if (1 || $is_layout || !is_admin()) { //checkout, cart, account modules designed to be used from the library only

                //checkout
                require_once($modules_path . 'sb_et_woo_li_checkout_billing_module.php');
                require_once($modules_path . 'sb_et_woo_li_checkout_shipping_module.php');
                require_once($modules_path . 'sb_et_woo_li_checkout_review_module.php');
                require_once($modules_path . 'sb_et_woo_li_checkout_payment_module.php');
                require_once($modules_path . 'sb_et_woo_li_checkout_coupon_module.php');

                //cart
                require_once($modules_path . 'sb_et_woo_li_cart_products_module.php');
                require_once($modules_path . 'sb_et_woo_li_cart_totals_module.php');

                //account
                require_once($modules_path . 'sb_et_woo_li_account_page_module.php');
                require_once($modules_path . 'sb_et_woo_li_account_nav_module.php');
                require_once($modules_path . 'sb_et_woo_li_account_downloads_module.php');
                require_once($modules_path . 'sb_et_woo_li_account_orders_module.php');
                require_once($modules_path . 'sb_et_woo_li_account_details_module.php');
                require_once($modules_path . 'sb_et_woo_li_account_addresses_module.php');
            }

            //general
            require_once($modules_path . 'sb_et_woo_li_content_module.php');
            require_once($modules_path . 'sb_et_woo_li_short_content_module.php');
            require_once($modules_path . 'sb_et_woo_li_notices_module.php');
            require_once($modules_path . 'sb_et_woo_li_gallery_module.php');
            require_once($modules_path . 'sb_et_woo_li_title_module.php');
            require_once($modules_path . 'sb_et_woo_li_info_tab_module.php');
            require_once($modules_path . 'sb_et_woo_li_attribute_module.php');
            require_once($modules_path . 'sb_et_woo_li_reviews_module.php');
            require_once($modules_path . 'sb_et_woo_li_rating_module.php');
            require_once($modules_path . 'sb_et_woo_li_meta_module.php');
            require_once($modules_path . 'sb_et_woo_li_price_module.php');
            require_once($modules_path . 'sb_et_woo_li_atc_module.php');
            require_once($modules_path . 'sb_et_woo_li_general_module.php');
            require_once($modules_path . 'sb_et_woo_li_thumbnail_module.php');
            require_once($modules_path . 'sb_et_woo_li_tabs_module.php');
            require_once($modules_path . 'sb_et_woo_li_product_category_module.php');
            require_once($modules_path . 'sb_et_woo_li_related_module.php');
            require_once($modules_path . 'sb_et_woo_li_upsell_module.php');
            require_once($modules_path . 'sb_et_woo_li_cross_sell_module.php');
            require_once($modules_path . 'sb_et_woo_li_breadcrumb_module.php');
            require_once($modules_path . 'sb_et_woo_li_read_more_module.php');
            require_once($modules_path . 'sb_et_woo_li_shop_cat_title_module.php');

            //loops
            require_once($modules_path . 'sb_et_woo_li_loop_archive_module.php');
            require_once($modules_path . 'sb_et_woo_li_archive_module.php');
            require_once($modules_path . 'sb_et_woo_li_category_archive_module.php');
        }

        require_once($modules_path . 'sb_et_woo_li_single_product_module.php');
    }
}


function sb_et_woo_li_submenu()
{
    add_submenu_page(
        'woocommerce',
        'Woo Layout Injector',
        'Woo Layout Injector',
        'manage_options',
        'sb_et_woo_li',
        'sb_et_woo_li_submenu_cb');

    add_submenu_page(
        'woocommerce',
        'Woo Injector Tabs',
        'Woo Injector Tabs',
        'manage_options',
        'sb_et_woo_li_tabs',
        'sb_et_woo_li_submenu_tabs');
}

function sb_et_woo_li_box_start($title, $width = false, $float = 'left')
{
    return '<div class="postbox" style="' . ($width ? 'float: ' . $float . '; margin-bottom: 20px; width: ' . $width : 'clear: both;') . '">
                    <h2 class="hndle">' . $title . '</h2>
                    <div class="inside" style="clear: both;">';
}

function sb_et_woo_li_box_end()
{
    return '</div>
            </div>';
}

function sb_et_woo_li_submenu_cb()
{

    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    echo '<h2>Woo Layout Injector - V' . SB_ET_WOO_LI_VERSION . '</h2>';

    echo '<div id="poststuff">';

    echo '<div id="post-body" class="metabox-holder columns-2">';

    $terms = get_terms('product_cat', array(
        'hide_empty' => false,
    ));
    $tterms = get_terms('product_tag', array(
        'hide_empty' => false,
    ));
    $sale_locations = array(
        'none' => 'None'
    , 'image' => 'Over Product Image (top left)'
    , 'title' => 'Over Product Title (top right)'
    , 'content' => 'Over Product Description (top right)'
    );

    if (isset($_POST['sb_et_woo_li_edit_submit'])) {
        update_option('sb_et_woo_li_admin_mode', (isset($_POST['sb_et_woo_li_admin_mode']) ? 1 : 0));
        update_option('sb_et_woo_li_product_page', @$_POST['sb_et_woo_li_product_page']);
        update_option('sb_et_woo_li_use_mini_cart', @$_POST['sb_et_woo_li_use_mini_cart']);
        update_option('sb_et_woo_li_product_cat', @$_POST['sb_et_woo_li_product_cat']);
        update_option('sb_et_woo_li_product_tag', @$_POST['sb_et_woo_li_product_tag']);

        update_option('sb_et_woo_li_sale_loop_location', @$_POST['sb_et_woo_li_sale_loop_location']);
        update_option('sb_et_woo_li_sale_single_location', @$_POST['sb_et_woo_li_sale_single_location']);
        update_option('sb_et_woo_li_sale_label', @$_POST['sb_et_woo_li_sale_label']);
        update_option('sb_et_woo_li_sale_bg_colour', @$_POST['sb_et_woo_li_sale_bg_colour']);
        update_option('sb_et_woo_li_sale_colour', @$_POST['sb_et_woo_li_sale_colour']);

        //update_option('sb_et_woo_li_acc_nav_page', @$_POST['sb_et_woo_li_acc_nav_page']);
        update_option('sb_et_woo_li_acc_page_page', @$_POST['sb_et_woo_li_acc_page_page']);

        update_option('sb_et_woo_li_cart_page', @$_POST['sb_et_woo_li_cart_page']);
        update_option('sb_et_woo_li_cart_page_empty', @$_POST['sb_et_woo_li_cart_page_empty']);

        update_option('sb_et_woo_li_checkout_page', @$_POST['sb_et_woo_li_checkout_page']);

        update_option('sb_et_woo_li_product_cat_archive', @$_POST['sb_et_woo_li_product_cat_archive']);
        update_option('sb_et_woo_li_product_cat_archive_general', @$_POST['sb_et_woo_li_product_cat_archive_general']);
        update_option('sb_et_woo_li_product_tag_archive', @$_POST['sb_et_woo_li_product_tag_archive']);
        update_option('sb_et_woo_li_product_tag_archive_general', @$_POST['sb_et_woo_li_product_tag_archive_general']);

        update_option('sb_et_woo_li_shop_archive_page', @$_POST['sb_et_woo_li_shop_archive_page']);
        update_option('sb_et_woo_li_button_label', @$_POST['sb_et_woo_li_button_label']);
        update_option('sb_et_woo_li_disable_zoom', @(int)$_POST['sb_et_woo_li_disable_zoom']);
        update_option('sb_et_woo_li_disable_cart_cross_sell', @(int)$_POST['sb_et_woo_li_disable_cart_cross_sell']);
        update_option('sb_et_woo_li_add_builder', @(int)$_POST['sb_et_woo_li_add_builder']);
        update_option('sb_et_woo_li_placeholder_url', @$_POST['sb_et_woo_li_placeholder_url']);

        echo '<div id="message" class="updated fade"><p>Layouts edited successfully</p></div>';
    }

    echo '<p>This plugin allows you to edit the layouts of your Divi site without having to edit any core files. See each section below for more information</p>';
    echo '<p>A layout can be built within the Divi/Extra library using the page builder. You can then use these settings to set the appropriate layout to the WooCommerce page(s). You\'ll need to include a variety of new modules in the page builder to make the page work. This plugin will do the rest!</p>';

    echo '<form method="POST">';

    sb_et_woo_li_license_page();

    $layout_query = array(
        'post_type' => 'et_pb_layout'
    , 'posts_per_page' => -1
    , 'meta_query' => array(
            array(
                'key' => '_et_pb_predefined_layout',
                'compare' => 'NOT EXISTS',
            ),
        )
    );

    if ($layouts = get_posts($layout_query)) {

        echo '<div style="clear: both;">';
        echo '<p id="submit"><input type="submit" name="sb_et_woo_li_edit_submit" class="button-primary" value="Save Settings" /></p>';
        echo '</div>';

        echo '<div style="clear: both;">';

        echo sb_et_woo_li_box_start('General Settings', '49%', 'left');

        echo '<p>
                <label><input type="checkbox" name="sb_et_woo_li_admin_mode" ' . checked(1, get_option('sb_et_woo_li_admin_mode', 0), false) . ' value="1" /> Admin test mode?</label>
                <br /><small>Using admin test mode the layout will only be applied for admin users. Non-admin users will see the default product page layouts.</small>
            </p>';

        echo '<p>
            <label><input type="checkbox" name="sb_et_woo_li_add_builder" ' . checked(1, get_option('sb_et_woo_li_add_builder', 0), false) . ' value="1" /> Enable Divi Builder for WooCommerce Products</label>
            <br /><small>Allows you to enable the Divi Builder for WooCommerce product pages. This enables you to design your product pages using the builder and utilise the Woo Injector modules on a per product basis.</small>
        </p>';

        echo '<p>
            <label><input type="checkbox" name="sb_et_woo_li_use_mini_cart" ' . checked(1, get_option('sb_et_woo_li_use_mini_cart', 0), false) . ' value="1" /> Enable Header Mini Cart</label>
            <br /><small>Enables the mini cart on the Divi Header. Hover over the cart icon to reveal.</small>
        </p>';

        echo '<p>
            <label><input type="checkbox" name="sb_et_woo_li_disable_zoom" ' . checked(1, get_option('sb_et_woo_li_disable_zoom', 0), false) . ' value="1" /> Disable Zoom Feature</label>
            <br /><small>Turn off the zoom feature you get when hovering on a product image.</small>
        </p>';

        echo '<p>
                <label><input type="checkbox" name="sb_et_woo_li_disable_cart_cross_sell" ' . checked(1, get_option('sb_et_woo_li_disable_cart_cross_sell', 0), false) . ' value="1" /> Disable Cart Cross Sell (from beside totals)?</label>
                <br /><small>IF you intend to use the Woo Cross Sell module included with this plugin you may not want the default cross selling on the cart. If so then check this box and it will be removed.</small>
            </p>';

        echo '<p>
                <label>Add to Cart button label<br /><input type="text" style="width: 300px;" name="sb_et_woo_li_button_label" value="' . get_option('sb_et_woo_li_button_label') . '" /></label>
                <br /><small>If you\'d like to change the add to cart button labels around your site use this option. Defaults to "Add to Cart" if left empty.</small>
            </p>';

        echo '<p>
                <label>Placeholder Image URL<br /><input type="text" style="width: 300px;" name="sb_et_woo_li_placeholder_url" value="' . get_option('sb_et_woo_li_placeholder_url') . '" /></label>
                <br /><small>When no image is set for a product you will see the default placeholder offered by WooCommerce. Using this setting you can set your own for added flexibility.</small>
            </p>';

        echo sb_et_woo_li_box_end();

        echo sb_et_woo_li_box_start('Sale Badges', '49%', 'right');

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo '<p>
                <label>Sale Badge Location - On archive pages:<br />';

        echo '<select style="width: 250px;" name="sb_et_woo_li_sale_loop_location">';

        $sale_loc = get_option('sb_et_woo_li_sale_loop_location', 'none');

        foreach ($sale_locations as $sale_location => $sale_loc_label) {
            echo '<option ' . selected($sale_location, $sale_loc, false) . ' value="' . $sale_location . '">' . $sale_loc_label . '</option>';
        }

        echo '</select>';
        echo '</label>';
        echo '</p>';

        echo '<p>
                <label>Sale Badge Location - On single product pages:<br />';

        echo '<select style="width: 250px;" name="sb_et_woo_li_sale_single_location">';
        $sale_loc = get_option('sb_et_woo_li_sale_single_location', 'none');

        foreach ($sale_locations as $sale_location => $sale_loc_label) {
            echo '<option ' . selected($sale_location, $sale_loc, false) . ' value="' . $sale_location . '">' . $sale_loc_label . '</option>';
        }

        echo '</select>';
        echo '</label>';
        echo '</p>';

        echo '<p>
                <label>Sale badge label<br /><input type="text" style="width: 300px;" name="sb_et_woo_li_sale_label" value="' . get_option('sb_et_woo_li_sale_label') . '" /></label>
                <br /><small>If you\'d like to change the text on the sale label then enter it here. Defaults to "Sale!" if left empty.</small>
            </p>';

        echo '<p>
                <label>Sale Badge Background Colour<br /><input type="text" name="sb_et_woo_li_sale_bg_colour" value="' . get_option('sb_et_woo_li_sale_bg_colour') . '" class="wli_colorpicker" /></label>
                <br /><small>If you\'d like to change the background colour of the sale badge choose it here.</small>
            </p>';

        echo '<p>
                <label>Sale Badge Text Colour<br /><input type="text" name="sb_et_woo_li_sale_colour" value="' . get_option('sb_et_woo_li_sale_colour') . '" class="wli_colorpicker" /></label>
                <br /><small>If you\'d like to change the text colour of the sale badge choose it here.</small>
            </p>';

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo sb_et_woo_li_box_end();

        echo '</div>';

        echo sb_et_woo_li_box_start('Product Page Layout');

        echo '<select style="width: 250px;" name="sb_et_woo_li_product_page">';

        $layout_products = get_option('sb_et_woo_li_product_page');
        echo '<option value="">-- None --</option>';

        $selected = '';
        foreach ($layouts as $layout) {
            if ($layout_products == $layout->ID) {
                $selected = $layout->post_title;
            }
            echo '<option ' . selected($layout->ID, $layout_products, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select>';

        if ($selected) {
            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout_products . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
        }

        if ($terms) {

            $cat_layout = get_option('sb_et_woo_li_product_cat');
            $tag_layout = get_option('sb_et_woo_li_product_tag');

            echo '<p><strong>Category/Tag Overrides</strong></p>';

            echo '<p>The single product page layout is global but selecting layouts using the category/tag list below will override the first setting and use those instead. EG. If you had a category called books you might want to include a longer description and maybe a chapter whereas selling CDs might require a track listing. In this circumstance you would want to use a different layout for each category. Using the boxes below allows you to do that.</p>';
            echo '<p><strong><a style="cursor: pointer;" onclick="jQuery(\'.single_product_cat_layout\').slideToggle();">toggle terms +</a></strong></p>';

            echo '<div class="single_product_cat_layout" style="display: none;">';

            echo '<h2>Categories</h2>';

            if (count($terms) <= apply_filters('wli_term_count_limit', 100)) {

                echo '<table style="width: 100%;" class="widefat">';

                foreach ($terms as $term) {

                    echo '<tr><td style="width: 30%;">' . $term->name . '</td><td>';
                    echo '<select style="width: 250px;" name="sb_et_woo_li_product_cat[' . $term->slug . ']">';

                    echo '<option value="">-- Default --</option>';

                    $selected = '';
                    foreach ($layouts as $layout) {
                        if ($cat_layout[$term->slug] == $layout->ID) {
                            $selected = $layout->post_title;
                        }
                        echo '<option ' . selected($layout->ID, $cat_layout[$term->slug], false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
                    }

                    echo '</select>';

                    if ($selected) {
                        echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $cat_layout[$term->slug] . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
                    }

                    echo '</td></tr>';

                }

                echo '</table>';

            } else {
                echo '<p>Here you would ordinarily be able to override each category at will but because you have more than 100 it has been known to cause issues with the page load. Therefore this functionality has been disabled. If you would like to use this feature please temporarily delete some categories to below the 100 limit.</p>';
            }

            if ($tterms) {
                echo '<h2>Tags</h2>';

                if (count($tterms) <= apply_filters('wli_term_count_limit', 100)) {
                    echo '<table style="width: 100%;" class="widefat">';

                    foreach ($tterms as $term) {

                        echo '<tr><td style="width: 30%;">' . $term->name . '</td><td>';
                        echo '<select style="width: 250px;" name="sb_et_woo_li_product_tag[' . $term->slug . ']">';

                        echo '<option value="">-- Default --</option>';

                        $selected = '';
                        foreach ($layouts as $layout) {
                            if ($tag_layout[$term->slug] == $layout->ID) {
                                $selected = $layout->post_title;
                            }
                            echo '<option ' . selected($layout->ID, $tag_layout[$term->slug], false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
                        }

                        echo '</select>';

                        if ($selected) {
                            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $tag_layout[$term->slug] . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
                        }

                        echo '</td></tr>';

                    }

                    echo '</table>';

                } else {
                    echo '<p>Here you would ordinarily be able to override each tag at will but because you have more than 100 it has been known to cause issues with the page load. Therefore this functionality has been disabled. If you would like to use this feature please temporarily delete some tags to below the 100 limit.</p>';
                }
            }

            echo '</div>';

        }

        echo sb_et_woo_li_box_end();

        echo sb_et_woo_li_box_start('Shop Page / Product Archive / Product Categories / Product Tags Layout');
        $layout_products = get_option('sb_et_woo_li_shop_archive_page');

        echo '<p><strong>Note:</strong> To make an archive template work properly you will need to create TWO layouts. One for the archive page itself containing the title, sidebar if necessary and footer.. In addition to this you will need to include the loop layout module to select the secondary layout to use for the loop items. The second layout to create will be for individual items in the loop and contain the title, exceptm content and/or any individual page item modules. These are provided with this plugin but companion olugins such as the Advanced Custom Fields module and CPT Injector modules could be used here also. <strong>View a <a href="https://www.youtube.com/watch?v=hMMWhi2sIlI" target="_blank">video on YouTube here</a> which will explain it.</strong></p>';

        echo '<p><label><span style="display: inline-block; width: 150px;">Shop Page:</span> <select style="width: 250px;" name="sb_et_woo_li_shop_archive_page">';

        echo '<option value="">-- None --</option>';

        $selected = '';
        foreach ($layouts as $layout) {
            if ($layout_products == $layout->ID) {
                $selected = $layout->post_title;
            }
            echo '<option ' . selected($layout->ID, $layout_products, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select></label>';

        if ($selected) {
            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout_products . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
        }

        echo '</p>';

        if ($terms || $tterms) {

            $cat_layout = get_option('sb_et_woo_li_product_cat_archive');
            $tag_layout = get_option('sb_et_woo_li_product_tag_archive');
            $cat_layout_general = get_option('sb_et_woo_li_product_cat_archive_general');
            $tag_layout_general = get_option('sb_et_woo_li_product_tag_archive_general');

            echo '<p><label><span style="display: inline-block; width: 150px;">Category Achives:</span> <select style="width: 250px;" name="sb_et_woo_li_product_cat_archive_general">';
            echo '<option value="">-- Default --</option>';

            $selected = '';
            foreach ($layouts as $layout) {
                if ($cat_layout_general == $layout->ID) {
                    $selected = $layout->post_title;
                }
                echo '<option ' . selected($layout->ID, $cat_layout_general, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
            }
            echo '</select></label>';

            if ($selected) {
                echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $cat_layout_general . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
            }

            echo '</p>';

            echo '<p><label><span style="display: inline-block; width: 150px;">Tag Archives:</span> <select style="width: 250px;" name="sb_et_woo_li_product_tag_archive_general">';
            echo '<option value="">-- Default --</option>';

            $selected = '';
            foreach ($layouts as $layout) {
                if ($tag_layout_general == $layout->ID) {
                    $selected = $layout->post_title;
                }
                echo '<option ' . selected($layout->ID, $tag_layout_general, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
            }
            echo '</select></label>';

            if ($selected) {
                echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $tag_layout_general . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
            }

            echo '</p>';

            echo '<hr />';

            echo '<p><strong>Category/Tag Overrides</strong></p>';

            echo '<p>The product archive/shop page layout is global but selecting layouts using the category list below will override the first setting and use those instead. EG. If you had a category called books you might want to include a longer description and maybe a chapter whereas selling CDs might require a track listing. In this circumstance you would want to use a different layout for each category. Using the boxes below allows you to do that.</p>';
            echo '<p><strong><a style="cursor: pointer;" onclick="jQuery(\'.archive_product_cat_layout\').slideToggle();">toggle terms +</a></strong></p>';

            echo '<div class="archive_product_cat_layout" style="display: none;">';

            echo '<h2>Categories</h2>';

            if (count($terms) <= apply_filters('wli_term_count_limit', 100)) {
                echo '<table style="width: 100%;" class="widefat">';

                foreach ($terms as $term) {

                    echo '<tr><td style="width: 30%;">' . $term->name . '</td><td>';
                    echo '<select style="width: 250px;" name="sb_et_woo_li_product_cat_archive[' . $term->slug . ']">';

                    echo '<option value="">-- Default --</option>';

                    $selected = '';
                    foreach ($layouts as $layout) {
                        if ($cat_layout[$term->slug] == $layout->ID) {
                            $selected = $layout->post_title;
                        }
                        echo '<option ' . selected($layout->ID, $cat_layout[$term->slug], false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
                    }

                    echo '</select>';

                    if ($selected) {
                        echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $cat_layout[$term->slug] . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
                    }

                    echo '</td></tr>';

                }

                echo '</table>';
            } else {
                echo '<p>Here you would ordinarily be able to override each category at will but because you have more than 100 it has been known to cause issues with the page load. Therefore this functionality has been disabled. If you would like to use this feature please temporarily delete some categories to below the 100 limit.</p>';
            }

            if ($tterms) {
                if (count($tterms) <= apply_filters('wli_term_count_limit', 100)) {
                    echo '<h2>Tags</h2>';

                    echo '<table style="width: 100%;" class="widefat">';

                    foreach ($tterms as $term) {

                        echo '<tr><td style="width: 30%;">' . $term->name . '</td><td>';
                        echo '<select style="width: 250px;" name="sb_et_woo_li_product_tag_archive[' . $term->slug . ']">';

                        echo '<option value="">-- Default --</option>';

                        $selected = '';
                        foreach ($layouts as $layout) {
                            if ($tag_layout[$term->slug] == $layout->ID) {
                                $selected = $layout->post_title;
                            }
                            echo '<option ' . selected($layout->ID, $tag_layout[$term->slug], false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
                        }

                        echo '</select>';

                        if ($selected) {
                            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $tag_layout[$term->slug] . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
                        }

                        echo '</td></tr>';

                    }

                    echo '</table>';
                } else {
                    echo '<p>Here you would ordinarily be able to override each tag at will but because you have more than 100 it has been known to cause issues with the page load. Therefore this functionality has been disabled. If you would like to use this feature please temporarily delete some tags to below the 100 limit.</p>';
                }

            }


            echo '</div>';

        }

        echo sb_et_woo_li_box_end();

        echo '<div style="clear:both;">';

        echo sb_et_woo_li_box_start('Cart Page Layout', '49%', 'left');

        echo '<p>Cart layout when at least one product is in the cart</p>';
        echo '<p>';
        echo '<select style="width: 250px;" name="sb_et_woo_li_cart_page">';

        $layout_cart = get_option('sb_et_woo_li_cart_page');
        echo '<option value="">-- None --</option>';

        $selected = '';
        foreach ($layouts as $layout) {
            if ($layout_cart == $layout->ID) {
                $selected = $layout->post_title;
            }
            echo '<option ' . selected($layout->ID, $layout_cart, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select>';

        if ($selected) {
            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout_cart . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
        }

        echo '</p>';

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo '<p>Cart layout when no products are in the cart</p>';
        echo '<p>';
        echo '<select style="width: 250px;" name="sb_et_woo_li_cart_page_empty">';

        $layout_cart = get_option('sb_et_woo_li_cart_page_empty', 0);
        echo '<option value="">-- Default (None) --</option>';

        $selected = '';
        foreach ($layouts as $layout) {
            if ($layout_cart == $layout->ID) {
                $selected = $layout->post_title;
            }
            echo '<option ' . selected($layout->ID, $layout_cart, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select>';

        if ($selected) {
            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout_cart . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
        }

        echo '</p>';

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo sb_et_woo_li_box_end();

        echo sb_et_woo_li_box_start('Checkout Page Layout', '49%', 'right');

        echo '<p>';
        echo '<select style="width: 250px;" name="sb_et_woo_li_checkout_page">';

        $layout_checkout = get_option('sb_et_woo_li_checkout_page', 0);
        echo '<option value="">-- None --</option>';

        $selected = '';
        foreach ($layouts as $layout) {
            if ($layout_checkout == $layout->ID) {
                $selected = $layout->post_title;
            }
            echo '<option ' . selected($layout->ID, $layout_checkout, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select>';

        if ($selected) {
            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout_checkout . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
        }

        echo '</p>';

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo sb_et_woo_li_box_end();

        echo '</div>';
        echo '<div style="clear: both;">';

        echo sb_et_woo_li_box_start('My Account Page Layout', '49%', 'left');

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo '<p>';

        echo '<select style="width: 250px;" name="sb_et_woo_li_acc_page_page">';

        $layout_page = get_option('sb_et_woo_li_acc_page_page', 0);
        echo '<option value="">-- None --</option>';

        $selected = '';
        foreach ($layouts as $layout) {
            if ($layout_page == $layout->ID) {
                $selected = $layout->post_title;
            }
            echo '<option ' . selected($layout->ID, $layout_page, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select>';

        if ($selected) {
            echo '&nbsp; <a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $layout_page . '&action=edit') . '">Click to edit "' . $selected . '"</a>';
        }

        echo '</p>';

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo sb_et_woo_li_box_end();

        echo '</div>';

        echo '<div style="clear: both;">';
        echo '<p id="submit"><input type="submit" name="sb_et_woo_li_edit_submit" class="button-primary" value="Save Settings" /></p>';
        echo '</div>';

    } else {
        echo '<div style="padding:100px; border: 2px solid #999; text-align: center;">
                    <h1>Oops no layouts!</h1>
                    <p style="font-size: 16px;">Please visit the Divi Library to add your first layout and then this page will become available</p>
                    <p><a href="' . (admin_url('/edit.php?post_type=et_pb_layout')) . '" style="display: inline-block; padding: 10px 30px; border: 1px solid #999; font-size: 16px; background-color: #333; color: white; font-weight: bold; border-radius: 20px; text-decoration: none;">Click here to visit the Divi Library</a></p>
                </div>';
    }

    echo '</form>';

    echo '</div>';

    echo '</div>';
    echo '</div>';
}

function sb_et_woo_li_meta_box_content()
{
    echo '<p>Use this setting to override the layout of the product page using the Woo Layout Injector plugin. Simply choose a layout and publish/update the page and the layout will be set.</p>';

    $overrides = get_post_meta(sb_et_woo_li_get_id(), "sb_et_woo_li_layout_overrides", true);
    $selected = false;
    $layout_query = array(
        'post_type' => 'et_pb_layout'
    , 'posts_per_page' => -1
    , 'meta_query' => array(
            array(
                'key' => '_et_pb_predefined_layout',
                'compare' => 'NOT EXISTS',
            ),
        )
    );

    if ($layouts = get_posts($layout_query)) {
        echo '<select name="sb_et_woo_li_layout_overrides">';

        echo '<option value="0" ' . selected($overrides, 0, false) . '>-- Default --</option>';

        foreach ($layouts as $layout) {
            if ($layout->ID == $overrides) {
                $selected = $layout;
            }

            echo '<option  ' . selected($overrides, $layout->ID, false) . ' value="' . $layout->ID . '">' . $layout->post_title . '</option>';
        }

        echo '</select></p>';

        if ($selected) {
            echo '<p><a class="button-secondary" target="_blank" href="' . admin_url('post.php?post=' . $overrides . '&action=edit') . '">Click to edit "' . $selected->post_title . '"</a></p>';
        }
    }

}

function sb_et_woo_li_meta_box_save($post_id, $post, $update)
{

    if (!current_user_can("edit_post", $post_id)) {
        return $post_id;
    }

    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return $post_id;
    }

    if (isset($_POST['sb_et_woo_li_layout_overrides'])) {
        update_post_meta($post_id, 'sb_et_woo_li_layout_overrides', $_POST['sb_et_woo_li_layout_overrides']);
    }

}

function sb_et_woo_li_meta_box()
{
    add_meta_box('sb_et_woo_li_meta_box', 'Woo Layout Injector', 'sb_et_woo_li_meta_box_content', 'product', 'side');
    add_meta_box('sb_et_woo_li_sectionid', __('Additional Product Tabs', 'sb_awt'), 'sb_et_woo_li_meta_box_tab_content', 'product');
}

?>