<?php

class sb_et_woo_li_category_archive extends ET_Builder_Module
{
    function init()
    {
        $this->name = esc_html__('Woo Category Archive', 'et_builder');
        $this->slug = 'et_pb_woo_category_archive';

        $whitelisted_fields = array(
            'title',
            'fullwidth',
            'columns',
            'image_size',
            'show_description',
            'show_more',
            'read_more_label',
            'show_products_in_cat',
            'hide_empty_cats',
            'show_only_children',
            'show_siblings',
            'background_layout',
            'admin_label',
            'module_id',
            'module_class',
            'use_overlay',
            'overlay_icon_color',
            'hover_overlay_color',
            'hover_icon',
        );

        $this->whitelisted_fields = apply_filters('sb_et_divi_woo_category_archive_module_whitelisted_fields', $whitelisted_fields);

        $this->fields_defaults = array(
            'fullwidth' => array('on'),
            'show_only_children' => array('on'),
            'image_size' => array('medium_large'),
            'hide_empty_cats' => array('off'),
            'columns' => array(3),
            'posts_number' => array(10, 'add_default_setting'),
            'show_description' => array('off'),
            'show_more' => array('off'),
            'offset_number' => array(0, 'only_default_setting'),
            'background_layout' => array('light'),
        );

        $this->main_css_element = '%%order_class%%';

        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Description', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'title' => array(
                    'label' => esc_html__('Title', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2.module-title, {$this->main_css_element} h2.module-title a",
                    ),
                    'font_size' => array('default' => '30px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Category Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h2.category-title, {$this->main_css_element} h2.category-title a",
                    ),
                    'font_size' => array('default' => '24px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings_current' => array(
                    'label' => esc_html__('Current Category Heading', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} .current_category h2.category-title, {$this->main_css_element} .current_category h2.category-title a",
                    ),
                    'font_size' => array('default' => '24px'),
                    'line_height' => array('default' => '1.5em'),
                ),
            ),
            'button' => array(
                'button' => array(
                    'label' => esc_html__( 'Button', 'et_builder' ),
                    'css' => array(
                        'main' => $this->main_css_element . ' .et_pb_button.more-link',
                        'plugin_main' => "{$this->main_css_element}.et_pb_module",
                    ),
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
            'pagenavi' => array(
                'label' => esc_html__('Pagenavi', 'et_builder'),
                'selector' => '.wp_pagenavi',
            ),
            'featured_image' => array(
                'label' => esc_html__('Featured Image', 'et_builder'),
                'selector' => '.wli_image_container',
            ),
            'read_more' => array(
                'label' => esc_html__('Read More Button', 'et_builder'),
                'selector' => '.et_pb_post .more-link',
            ),
        );
    }

    function get_fields()
    {
        /*$orderby = array(
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
        );*/

        $image_options = array();
        $sizes = get_intermediate_image_sizes();

        foreach ($sizes as $size) {
            $image_options[$size] = $size;
        }

        $cols = array(
            2 => esc_html__('Two', 'et_builder'),
            3 => esc_html__('Three', 'et_builder'),
            4 => esc_html__('Four', 'et_builder'),
        );

        if (function_exists('sb_dcl_col_templates')) {
            $cols[5] = esc_html__('Five', 'et_builder');
            $cols[6] = esc_html__('Six', 'et_builder');
            $cols[7] = esc_html__('Seven', 'et_builder');
            $cols[8] = esc_html__('Eight', 'et_builder');
        }

        $fields = array(
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'description' => __('If you want a title to show above the module then enter it here', 'et_builder'),
            ),
            'fullwidth' => array(
                'label' => esc_html__('Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'options' => array(
                    'off' => esc_html__('Grid', 'et_builder'),
                    'list' => esc_html__('List', 'et_builder'),
                    'on' => esc_html__('Fullwidth', 'et_builder'),
                ),
                'description' => esc_html__('Toggle between the various layout types.', 'et_builder'),
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
                'options' => $cols,
                'description' => esc_html__('When in grid mode please select the number of columns you\'d like to see. For more than 4 cols please add the FREE Divi Extended Columns Layouts plugin', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'image_size' => array(
                'label' => __('Image Size', 'et_builder'),
                'type' => 'select',
                'options' => $image_options,
                'toggle_slug' => 'main_settings',
                'description' => __('Pick a size for the category image from the list. Leave blank for default.', 'et_builder'),
            ),
            'show_description' => array(
                'label' => esc_html__('Show Description', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => esc_html__('Show the description that may have been added against the category', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'show_products_in_cat' => array(
                'label' => esc_html__('Show Num Products', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => esc_html__('Show how many products are in the category', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'hide_empty_cats' => array(
                'label' => esc_html__('Hide Empty Categories', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'description' => esc_html__('Should empty categories be hidden?', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'show_only_children' => array(
                'label' => esc_html__('Show only child categories?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('Yes', 'et_builder'),
                    'off' => esc_html__('No', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_show_siblings'
                ),
                'description' => esc_html__('If set to yes then only categories related to the current page will show. If set to no then all top level categories will show.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'show_siblings' => array(
                'label' => esc_html__('Show Sibling Categories?', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'et_builder'),
                    'on' => esc_html__('Yes', 'et_builder'),
                ),
                'depends_show_if' => 'on',
                'description' => esc_html__('If there are no child categories to show then should sibling categories be shown? If set to No then the module will be hidden when there are no child categories to show.', 'et_builder'),
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
            'use_overlay' => array(
                'label' => esc_html__('Image Overlay on Hover', 'et_builder'),
                'type' => 'yes_no_button',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    'off' => esc_html__('Off', 'et_builder'),
                    'on' => esc_html__('On', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_overlay_icon_color',
                    '#et_pb_hover_overlay_color',
                    '#et_pb_hover_icon',
                ),
                'description' => esc_html__('If enabled, an overlay color and icon will be displayed when a visitors hovers over the featured image of a post.', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'overlay_icon_color' => array(
                'label' => esc_html__('Overlay Icon Color', 'et_builder'),
                'type' => 'color',
                'custom_color' => true,
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can define a custom color for the overlay icon', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'hover_overlay_color' => array(
                'label' => esc_html__('Hover Overlay Color', 'et_builder'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can define a custom color for the overlay', 'et_builder'),
                'toggle_slug' => 'main_settings',
            ),
            'hover_icon' => array(
                'label' => esc_html__('Hover Icon Picker', 'et_builder'),
                'type' => 'text',
                'option_category' => 'configuration',
                'class' => array('et-pb-font-icon'),
                'renderer' => 'et_pb_get_font_icon_list',
                'renderer_with_field' => true,
                'depends_show_if' => 'on',
                'toggle_slug' => 'main_settings',
                'description' => esc_html__('Here you can define a custom icon for the overlay', 'et_builder'),
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

        $fields = apply_filters('sb_et_divi_woo_category_archive_module_fields', $fields);

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {
        if (is_admin()) {
            return;
        }

        $this->shortcode_atts = apply_filters('sb_et_divi_woo_archive_module_shortcode_atts', $this->shortcode_atts);

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];
        $title = $this->shortcode_atts['title'];
        $fullwidth = $this->shortcode_atts['fullwidth'];
        $show_description = $this->shortcode_atts['show_description'];
        $show_products_in_cat = $this->shortcode_atts['show_products_in_cat'];
        $hide_empty_cats = $this->shortcode_atts['hide_empty_cats'];
        $show_only_children = $this->shortcode_atts['show_only_children'];
        $show_siblings = $this->shortcode_atts['show_siblings'];
        $background_layout = $this->shortcode_atts['background_layout'];
        $show_more = $this->shortcode_atts['show_more'];
        if (!$read_more_label = $this->shortcode_atts['read_more_label']) {
            $read_more_label = 'Read more';
        }
        if (!$image_size = $this->shortcode_atts['image_size']) {
            $image_size = 'medium_large';
        }

        $overlay_icon_color = $this->shortcode_atts['overlay_icon_color'];
        $hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];
        $hover_icon = $this->shortcode_atts['hover_icon'];
        $use_overlay = $this->shortcode_atts['use_overlay'];


        if (!$cols = @$this->shortcode_atts['columns']) {
            $cols = 4;
        }

        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

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

        $data_icon = ($hover_icon ? ' data-icon="' . esc_attr(et_pb_process_font_icon($hover_icon)) . '"' : '');
        $overlay_class = ('on' == $use_overlay ? ' et_pb_has_overlay' : '');

        if ($fullwidth == 'list') {
            $module_class .= ' et_pb_woo_archive_list';
        }

        $parent = 0;
        $current = '';

        if ($show_only_children == 'on') {
            if (is_tax('product_cat')) {
                $parent = get_queried_object()->term_id;
                $current = get_queried_object()->slug;

                if ($show_siblings == 'on') {
                        //$parent = get_queried_object()->term_id;
                        if ($term_parent = get_term($parent)) {
                            if (!is_wp_error($term_parent)) {
                                $parent = $term_parent->parent;
                            }
                        }
                }
            }

            //echo get_query_var('taxonomy') . '<br />';
            //echo get_query_var('term') . '<br />';
        }

        $args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => ($hide_empty_cats == 'on' ? '1' : '0'),
            'parent' => $parent,
        );

        $terms = get_terms($args);

        ob_start();

        //echo '<pre>';
        //print_r($args);
        //print_r($terms);
        //echo '</pre>';

        if ($terms) {

            $i = 0;
            $j = 0;

            if ($title) {
                echo '<h2 class="module-title">' . $title . '</h2>';
            }

            if ($fullwidth == 'off') { //grid
                echo '<div class="et_pb_row">';
            }

            foreach ($terms as $term) {

                if ($term->slug == 'uncategorized') {
                    continue; //no one uses this so let's skip it to save the support tickets!
                }

                $cont_classes = implode(' ', get_post_class('et_pb_post_type et_pb_post_type_product et_pb_post'));

                if ($term->slug && $term->slug == $current) {
                    $cont_classes .= ' current_category';
                }

                if ($fullwidth == 'off') { //grid
                    echo '<div class="et_woo_container_column et_pb_column et_pb_column_1_' . $cols . '  et_pb_column_' . $i . ' ' . $cont_classes . '">';
                } else {
                    echo '<div class="et_pb_row ' . $cont_classes . '">';
                }

                do_action('sb_et_woo_li_category_archive_start', $term->term_id);
                do_action('woocommerce_before_shop_loop_item');

                echo '<div class="et_pb_column">';

                do_action('sb_et_woo_li_category_archive_image');

                if ($fullwidth == 'list') {
                    echo '<div class="woo_content_column">';
                }

                if ($thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true)) {
                    $url = get_term_link($term);

                    echo '<div class="et_pb_module wli_image_container ' . $overlay_class . '">';

                    if ('on' === $use_overlay) {
                        echo '<a href="' . $url . '" class="et_overlay ' . ($hover_icon ? 'et_pb_inline_icon' : '') . '"' . $data_icon . '></a>';
                    }

                    echo '<a href="' . $url . '">';

                    echo wp_get_attachment_image($thumb_id, $image_size);

                    echo '</a>';
                    echo '</div>';
                }

                echo '<h2 class="entry-title category-title"><a href="' . get_term_link($term) . '">' . $term->name . ($show_products_in_cat == 'on' ? ' <span class="wli_prods_in_cat">(' . $term->count . ')</span>' : '') . '</a></h2>';

                if ('none' !== $show_description && $term->description) {
                    // do not display the content if it contains Blog, Post Slider, Fullwidth Post Slider, or Portfolio modules to avoid infinite loops
                    if ('on' === $show_description) {
                        echo wpautop(do_shortcode($term->description));
                    }

                }

                if ('on' == $show_more) {
                    echo '<p><a href="' . esc_url(get_term_link($term)) . '" class="et_pb_button more-link" >' . esc_html__($read_more_label, 'et_builder') . '</a></p>';
                }

                if ($fullwidth == 'list') {
                    echo '</div>';
                }

                do_action('sb_et_woo_li_category_archive_end', $term->term_id);

                echo '</div>';
                //</article> <!-- .et_pb_post -->

                if ($fullwidth == 'off') { //grid
                    echo '</div>';
                } else {
                    echo '</div>';
                }

                $i++;
                $j++;

                if ($i == $cols && ($fullwidth == 'off') && $j != count($terms)) {
                    $i = 0;

                    echo '</div>';
                    echo '<div class="et_pb_row">';
                }

            } // endwhile

            if ($fullwidth == 'off') { //grid
                echo '</div>';
            }
        }

        $posts = ob_get_contents();
        ob_end_clean();

        $class = ' et_pb_module et_pb_bg_layout_' . $background_layout;

        $output = '<div ' . ($module_id ? 'id="' . esc_attr($module_id) . '"' : '') . ' class="' . ('on' == $fullwidth || $fullwidth == 'list' ? 'et_pb_fullwidth_' . $fullwidth . ' clearfix' : 'et_pb_woo_category_archive_grid clearfix') . ' ' . esc_attr($class . $module_class) . '" ' . ('on' !== $fullwidth ? ' data-columns' : '') . '>' . $posts . '</div> <!-- .et_pb_posts -->';

        if ('off' == $fullwidth) {
            $output = sprintf('<div class="et_pb_blog_grid_wrapper et_pb_woo_archive et_pb_blog_grid_woo_archive_wrapper">%1$s</div>', $output);
        } else if ('list' == $fullwidth) {
            $output = sprintf('<div class="et_pb_woo_list_wrapper et_pb_woo_archive et_pb_blog_list_woo_archive_wrapper">%1$s</div>', $output);
        }

        return $output;
    }
}

new sb_et_woo_li_category_archive;

?>