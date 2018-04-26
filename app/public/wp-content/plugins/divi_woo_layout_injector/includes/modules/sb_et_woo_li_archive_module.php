<?php

class sb_et_woo_li_archive extends ET_Builder_Module
{
    function init()
    {
        $this->name = esc_html__('Woo Archive (Basic)', 'et_builder');
        $this->slug = 'et_pb_woo_archive';

        $whitelisted_fields = array(
            'fullwidth',
            'columns',
            'show_content',
            'show_more',
            'read_more_label',
            'show_pagination',
            'new_query',
            'posts_number',
            'offset_number',
            'include_tax',
            'include_tax_terms',
            'order_by',
            'order',
            'background_layout',
            'admin_label',
            'module_id',
            'module_class',
            'overlay_icon_color',
            'hover_overlay_color',
        );

        $this->whitelisted_fields = apply_filters('sb_et_divi_woo_archive_module_whitelisted_fields', $whitelisted_fields);

        $this->fields_defaults = array(
            'fullwidth' => array('on'),
            'posts_number' => array(10, 'add_default_setting'),
            'show_content' => array('off'),
            'show_more' => array('off'),
            'show_pagination' => array('on'),
            'offset_number' => array(0, 'only_default_setting'),
            'background_layout' => array('light'),
        );

        $this->main_css_element = '%%order_class%%';

        $this->advanced_options = array(
            'fonts' => array(
                'text' => array(
                    'label' => esc_html__('Text', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h1, {$this->main_css_element} h2, {$this->main_css_element} h1 a, {$this->main_css_element} h2 a, {$this->main_css_element} h3, {$this->main_css_element} h4",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'meta' => array(
                    'label' => esc_html__('Meta', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'read_more' => array(
                    'label' => esc_html__('Read More Button', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .et_pb_button.more-link",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
            ),
            'background' => array(
                'settings' => array(
                    'color' => 'alpha',
                ),
            ),
            'border' => array(),
            'custom_margin_padding' => array(
                'css' => array(
                    'important' => 'all',
                ),
            ),
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->custom_css_options = array(
            'title' => array(
                'label' => esc_html__('Title', 'et_builder'),
                'selector' => '.et_pb_post h2',
            ),
            'post_meta' => array(
                'label' => esc_html__('Post Meta', 'et_builder'),
                'selector' => '.et_pb_post .post-meta',
            ),
            'pagenavi' => array(
                'label' => esc_html__('Pagenavi', 'et_builder'),
                'selector' => '.wp_pagenavi',
            ),
            'featured_image' => array(
                'label' => esc_html__('Featured Image', 'et_builder'),
                'selector' => '.et_pb_image_container',
            ),
            'read_more' => array(
                'label' => esc_html__('Read More Button', 'et_builder'),
                'selector' => '.et_pb_post .more-link',
            ),
        );
    }

    function get_fields()
    {
        $orderby = array(
            'date' => 'Order by date'
        , 'ID' => 'Order by post id'
        , 'author' => 'Order by author'
        , 'title' => 'Order by title'
        , 'name' => 'Order by post name (post slug)'
        , 'modified' => 'Order by last modified date'
        , 'rand' => 'Random order'
        , 'comment_count' => 'Order by number of comments'
        );
        $order = array(
            'desc' => 'Descending'
        , 'asc' => 'Ascending'
        );

        $image_options = array();
        $sizes = get_intermediate_image_sizes();

        foreach ($sizes as $size) {
            $image_options[$size] = $size;
        }

        $fields = array(
            'fullwidth' => array(
                'label' => esc_html__('Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'options' => array(
                    'off' => esc_html__('Grid', 'et_builder'),
                    'list' => esc_html__('List', 'et_builder'),
                    'on' => esc_html__('Fullwidth', 'et_builder'),
                ),
                'description' => esc_html__('Toggle between the various blog layout types.', 'et_builder'),
                'affects' => array(
                    '#et_pb_columns'
                ),
                'toggle_slug' => 'main_settings',
            ),
            'columns' => array(
                'label' => esc_html__('Grid Columns', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'depends_show_if' => 'off',
                'options' => array(
                    2 => esc_html__('Two', 'et_builder'),
                    3 => esc_html__('Three', 'et_builder'),
                    4 => esc_html__('Four', 'et_builder'),
                ),
                'description' => esc_html__('When in grid mode please select the number of columns you\'d like to see.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'show_content' => array(
                'label' => esc_html__('Content', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('Show Excerpt', 'et_builder'),
                    'on' => esc_html__('Show Content', 'et_builder'),
                    'none' => esc_html__('Show None', 'et_builder'),
                ),
                'description' => esc_html__('Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'show_more' => array(
                'label' => esc_html__('Read More Button', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('Off', 'et_builder'),
                    'on' => esc_html__('On', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_read_more_label'
                ),
                'description' => esc_html__('Here you can define whether to show "read more" link after the excerpts or not.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'read_more_label' => array(
                'label' => esc_html__('Read more button label', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'depends_show_if' => 'on',
                'description' => esc_html__('The wording for the read more button. Defaults to "Read more"', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'show_pagination' => array(
                'label' => esc_html__('Show Pagination', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('Yes', 'et_builder'),
                    'off' => esc_html__('No', 'et_builder'),
                ),
                'description' => esc_html__('Turn pagination on and off.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'overlay_icon_color' => array(
                'label' => esc_html__('Overlay Icon Color', 'et_builder'),
                'type' => 'color',
                'custom_color' => true,
                'depends_show_if' => 'on',
                'description' => esc_html__('Here you can define a custom color for the overlay icon', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'hover_overlay_color' => array(
                'label' => esc_html__('Hover Overlay Color', 'et_builder'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'depends_show_if' => 'on',
                'description' => esc_html__('Here you can define a custom color for the overlay', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'background_layout' => array(
                'label' => esc_html__('Text Color', 'et_builder'),
                'type' => 'select',
                'option_category' => 'color_option',
                'options' => array(
                    'light' => esc_html__('Dark', 'et_builder'),
                    'dark' => esc_html__('Light', 'et_builder'),
                ),
                'depends_default' => true,
                'description' => esc_html__('Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'new_query' => array(
                'label' => esc_html__('Custom Query', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_post_type'
                , '#et_pb_posts_number'
                , '#et_pb_offset_number'
                , '#et_pb_include_tax'
                , '#et_pb_include_tax_terms'
                , '#et_pb_order_by'
                , '#et_pb_order'
                ),
                'description' => esc_html__('When used on an archive page turn this off. If you want to use on a normal WP page then select "ON" here and complete the settings below.', 'et_builder'),
            ),
            'posts_number' => array(
                'label' => esc_html__('Posts Number', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'description' => esc_html__('Choose how many posts you would like to display per page.', 'et_builder'),
            ),
            'offset_number' => array(
                'label' => esc_html__('Offset Number', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'description' => esc_html__('Choose how many posts you would like to offset by', 'et_builder'),
            ),
            'include_tax' => array(
                'label' => esc_html__('Include Taxonomy Only', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'description' => esc_html__('This will filter the query by this taxonomy slug (advanced users only).', 'et_builder'),
            ),
            'include_tax_terms' => array(
                'label' => esc_html__('Include Taxonomy Terms', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'description' => esc_html__('This will filter the query by the above taxonomy and these comma separated term slugs (advanced users only).', 'et_builder'),
            ),
            'order_by' => array(
                'label' => esc_html__('Results Order Field', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'options' => $orderby,
                'description' => esc_html__('Choose how you\'d like the results to be ordered.. title, date, etc...', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'order' => array(
                'label' => esc_html__('Results Sort Order', 'et_builder'),
                'type' => 'select',
                'options' => $order,
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Choose the order of the results.. Ascending or Descending.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'disabled_on' => array(
                'label' => esc_html__('Disable on', 'et_builder'),
                'type' => 'multiple_checkboxes',
                'options' => array(
                    'phone' => esc_html__('Phone', 'et_builder'),
                    'tablet' => esc_html__('Tablet', 'et_builder'),
                    'desktop' => esc_html__('Desktop', 'et_builder'),
                ),
                'additional_att' => 'disable_on',
                'option_category' => 'configuration',
                'description' => esc_html__('This will disable the module on selected devices', 'et_builder'),
            ),
            'admin_label' => array(
                'label' => esc_html__('Admin Label', 'et_builder'),
                'type' => 'text',
                'description' => esc_html__('This will change the label of the module in the builder for easy identification.', 'et_builder'),
            ),
            'module_id' => array(
                'label' => esc_html__('CSS ID', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'tab_slug' => 'custom_css',
                'option_class' => 'et_pb_custom_css_regular',
            ),
            'module_class' => array(
                'label' => esc_html__('CSS Class', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'tab_slug' => 'custom_css',
                'option_class' => 'et_pb_custom_css_regular',
            ),
        );

        $fields = apply_filters('sb_et_divi_woo_archive_module_fields', $fields);

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {

        $this->shortcode_atts = apply_filters('sb_et_divi_woo_archive_module_shortcode_atts', $this->shortcode_atts);

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $fullwidth = $this->shortcode_atts['fullwidth'];
        $show_content = $this->shortcode_atts['show_content'];
        $background_layout = $this->shortcode_atts['background_layout'];
        $show_more = $this->shortcode_atts['show_more'];
        if (!$read_more_label = $this->shortcode_atts['read_more_label']) {
            $read_more_label = 'Read more';
        }
        $overlay_icon_color = $this->shortcode_atts['overlay_icon_color'];
        $hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];

        $custom_query = $this->shortcode_atts['new_query'];
        $show_pagination = $this->shortcode_atts['show_pagination'];
        $offset_number = $this->shortcode_atts['offset_number'];
        $include_tax = $this->shortcode_atts['include_tax'];
        $include_tax_terms = $this->shortcode_atts['include_tax_terms'];
        $posts_number = $this->shortcode_atts['posts_number'];
        $order_by = $this->shortcode_atts['order_by'];
        $order = $this->shortcode_atts['order'];

        if (!$cols = @$this->shortcode_atts['columns']) {
            $cols = 4;
        }

        global $paged;

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        $container_is_closed = false;

        // remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
        remove_all_filters('wp_audio_shortcode_library');
        remove_all_filters('wp_audio_shortcode');
        remove_all_filters('wp_audio_shortcode_class');

        if ('' !== $overlay_icon_color) {
            ET_Builder_Element::set_style($function_name, array(
                'selector' => '%%order_class%% .et_overlay:before',
                'declaration' => sprintf(
                    'color: %1$s !important;',
                    esc_html($overlay_icon_color)
                ),
            ));
        }

        if ('' !== $hover_overlay_color) {
            ET_Builder_Element::set_style($function_name, array(
                'selector' => '%%order_class%% .et_overlay',
                'declaration' => sprintf(
                    'background-color: %1$s;',
                    esc_html($hover_overlay_color)
                ),
            ));
        }

        if ($fullwidth == 'list') {
            $module_class .= ' et_pb_woo_archive_list';
        }

        if ($custom_query == 'on') {
            $args = array('posts_per_page' => (int)$posts_number);

            $et_paged = is_front_page() ? get_query_var('page') : get_query_var('paged');

            if (is_front_page()) {
                $paged = $et_paged;
            }

            $args['post_type'] = 'product';

            if (!is_search()) {
                $args['paged'] = $et_paged;
            }

            if ($order_by && $order) { //sort ordering
                $args['orderby'] = $order_by;
                $args['order'] = $order;
            }

            if ('' !== $offset_number && !empty($offset_number)) {
                /**
                 * Offset + pagination don't play well. Manual offset calculation required
                 * @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
                 */
                if ($paged > 1) {
                    $args['offset'] = (($et_paged - 1) * intval($posts_number)) + intval($offset_number);
                } else {
                    $args['offset'] = intval($offset_number);
                }
            }

            if ($include_tax && $include_tax_terms) {
                if (strpos($include_tax, '|') !== false) {
                    $include_tax = explode('|', $include_tax);
                    $include_tax_terms = explode('|', $include_tax_terms);

                    $args['tax_query'] = array();

                    for ($i = 0; $i < count($include_tax); $i++) {
                        $args['tax_query'][] = array(
                            'taxonomy' => $include_tax[$i],
                            'field' => 'slug',
                            'terms' => explode(',', $include_tax_terms[$i]),
                        );
                    }
                } else {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => $include_tax,
                            'field' => 'slug',
                            'terms' => explode(',', $include_tax_terms),
                        )
                    );
                }
            }

            if (is_single() && !isset($args['post__not_in'])) {
                $args['post__not_in'] = array(sb_et_woo_li_get_id());
            }

            $args = apply_filters('sb_et_divi_woo_archive_module_args', $args);

            query_posts($args);

        }

        ob_start();

        add_action('sb_et_woo_li_archive_image', 'woocommerce_template_loop_product_thumbnail', 10);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

        if (have_posts()) {

            $i = 0;

            if ($fullwidth == 'off') { //grid
                echo '<div class="et_pb_row">';
            }

            while (have_posts()) {
                the_post();

                $pb_used = et_pb_is_pagebuilder_used(get_the_ID());

                if ($fullwidth == 'off') { //grid
                    echo '<div class="et_woo_container_column et_pb_column et_pb_column_1_' . $cols . '  et_pb_column_' . $i . ' ' . implode(' ', get_post_class('et_pb_post_type et_pb_post_type_product et_pb_post')) . '">';
                } else {
                    echo '<div class="et_pb_row ' . implode(' ', get_post_class('et_pb_post_type et_pb_post_type_product et_pb_post')) . '">';
                }

                do_action('sb_et_woo_li_archive_start', get_the_ID());
                do_action('woocommerce_before_shop_loop_item');

                echo '<div class="et_pb_column">';

                do_action('sb_et_woo_li_archive_image');

                if ($fullwidth == 'list') {
                    echo '<div class="woo_content_column">';
                }

                do_action('woocommerce_before_shop_loop_item_title');
                echo '<h2 class="entry-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h2>';
                do_action('woocommerce_after_shop_loop_item_title');

                $post_content = get_the_content();

                if ('none' !== $show_content) {
                    // do not display the content if it contains Blog, Post Slider, Fullwidth Post Slider, or Portfolio modules to avoid infinite loops
                    if ('on' === $show_content && !$pb_used) {
                        echo wpautop(do_shortcode(get_the_content(esc_html__('read more...', 'et_builder'))));
                    } else {
                        if (has_excerpt()) {
                            echo wpautop(get_the_excerpt());
                        } else {
                            echo wpautop(truncate_post(270, apply_filters('excerpt_more', ''), false));
                        }
                    }

                }

                if ('on' == $show_more) {
                    echo '<p><a href="' . esc_url(get_permalink()) . '" class="et_pb_button more-link" >' . esc_html__($read_more_label, 'et_builder') . '</a></p>';
                }

                echo '<p>';
                do_action('woocommerce_after_shop_loop_item');
                echo '</p>';

                if ($fullwidth == 'list') {
                    echo '</div>';
                }

                do_action('sb_et_woo_li_archive_end', get_the_ID());

                echo '</div>';
                //</article> <!-- .et_pb_post -->

                if ($fullwidth == 'off') { //grid
                    echo '</div>';
                } else {
                    echo '</div>';
                }

                $i++;

                if ($i == $cols && ($fullwidth == 'off')) {
                    $i = 0;

                    echo '</div>';
                    echo '<div class="et_pb_row">';
                }

            } // endwhile

            if ($fullwidth == 'off') { //grid
                echo '</div>';
            }

            if ('on' === $show_pagination && !is_search()) {
                echo '</div> <!-- .et_pb_posts -->';

                $container_is_closed = true;

                if (function_exists('wp_pagenavi')) {
                    wp_pagenavi();
                } else {
                    if (et_is_builder_plugin_active()) {
                        include(ET_BUILDER_PLUGIN_DIR . 'includes/navigation.php');
                    } else {
                        get_template_part('includes/navigation', 'index');
                    }
                }
            }

            wp_reset_query();
        } else {
            if (et_is_builder_plugin_active()) {
                include(ET_BUILDER_PLUGIN_DIR . 'includes/no-results.php');
            } else {
                get_template_part('includes/no-results', 'index');
            }
        }

        $posts = ob_get_contents();

        ob_end_clean();

        $class = " et_pb_module et_pb_bg_layout_{$background_layout}";

        $output = sprintf(
            '<div%5$s class="%1$s%3$s%6$s"%7$s>
				%2$s
			%4$s',
            ('on' == $fullwidth || $fullwidth == 'list' ? 'et_pb_fullwidth_' . $fullwidth . ' clearfix' : 'et_pb_woo_archive_grid clearfix'),
            $posts,
            esc_attr($class),
            (!$container_is_closed ? '</div> <!-- .et_pb_posts -->' : ''),
            ('' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''),
            ('' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : ''),
            ('on' !== $fullwidth ? ' data-columns' : '')
        );

        if ('off' == $fullwidth) {
            $output = sprintf('<div class="et_pb_blog_grid_wrapper et_pb_woo_archive et_pb_blog_grid_woo_archive_wrapper">%1$s</div>', $output);
        } else if ('list' == $fullwidth) {
            $output = sprintf('<div class="et_pb_woo_list_wrapper et_pb_woo_archive et_pb_blog_list_woo_archive_wrapper">%1$s</div>', $output);
        }

        return $output;
    }
}

new sb_et_woo_li_archive;

?>