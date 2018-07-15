<?php

class sb_et_woo_li_related_module extends ET_Builder_Module
{
    function init()
    {
        $this->name = __('Woo Related', 'et_builder');
        $this->slug = 'et_pb_woo_related';

        $this->whitelisted_fields = array(
            'use_loop',
            'title',
            'loop_layout',
            'fullwidth',
            'columns',
            'to_show',
            'module_id',
            'module_class',
        );

        $this->options_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_settings' => esc_html__('Main Settings', 'et_builder'),
                ),
            ),
        );

        $this->fields_defaults = array('to_show' => 3);

        $this->main_css_element = '%%order_class%%';

        $this->advanced_options = array(
            'fonts' => array(
                'cntnt' => array(
                    'label' => esc_html__('Content', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} p",
                    ),
                    'font_size' => array('default' => '14px'),
                    'line_height' => array('default' => '1.5em'),
                ),
                'headings' => array(
                    'label' => esc_html__('Headings', 'et_builder'),
                    'css' => array(
                        'main' => "{$this->main_css_element} h1, {$this->main_css_element} h2, {$this->main_css_element} h3, {$this->main_css_element} h4",
                    ),
                    'font_size' => array('default' => '30px'),
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
        $this->custom_css_options = array();
    }

    function get_fields()
    {
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
            foreach ($layouts as $layout) {
                $options[$layout->ID] = $layout->post_title;
            }
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
            'use_loop' => array(
                'label' => esc_html__('Use Loop Method?', 'et_builder'),
                'type' => 'yes_no_button',
                'toggle_slug' => 'main_settings',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('Yes', 'et_builder'),
                    'off' => esc_html__('No', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_title'
                , '#et_pb_loop_layout'
                , '#et_pb_fullwidth'
                ),
                'description' => 'If this is turned on then you have complete control over the output of the items. You need to make a sub layout which you can find out how to do by following the Loop Archive tutorial (youtube video) ont he WLI settings page. If you leave this off then the standard items will show with little confoguration.',
            ),
            'title' => array(
                'label' => __('Title', 'et_builder'),
                'type' => 'text',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'description' => __('If you want a title to show above the module then enter it here', 'et_builder'),
            ),
            'loop_layout' => array(
                'label' => esc_html__('Loop Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => $options,
                'depends_show_if' => 'on',
                'description' => esc_html__('Choose a layout to use for each post in this archive/taxonomy loop', 'et_builder'),
            ),
            'fullwidth' => array(
                'label' => esc_html__('Layout', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'depends_show_if' => 'on',
                'options' => array(
                    'list' => esc_html__('List', 'et_builder'),
                    'off' => esc_html__('Grid', 'et_builder'),
                ),
                'affects' => array(
                    '#et_pb_columns'
                ),
                'description' => esc_html__('Toggle between the various blog layout types.', 'et_builder'),
            ),
            'columns' => array(
                'label' => esc_html__('Grid Columns', 'et_builder'),
                'type' => 'select',
                'option_category' => 'layout',
                'toggle_slug' => 'main_settings',
                'options' => $cols,
                'depends_show_if' => 'off',
                'description' => esc_html__('When in grid mode please select the number of columns you\'d like to see. For more than 4 cols please add the FREE Divi Extended Columns Layouts plugin', 'et_builder'),
            ),
            'to_show' => array(
                'label' => __('Items to show', 'et_builder'),
                'type' => 'select',
                'toggle_slug' => 'main_settings',
                'options' => array(
                    1 => 1
                , 2 => 2
                , 3 => 3
                , 4 => 4
                , 5 => 5
                , 6 => 6
                , 7 => 7
                , 8 => 8
                ),
                'description' => __('the number of related items to show. Defaults to 3', 'et_builder'),
            ),
            'admin_label' => array(
                'label' => __('Admin Label', 'et_builder'),
                'type' => 'text',
                'description' => __('This will change the label of the module in the builder for easy identification.', 'et_builder'),
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

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {

        if (!is_singular('product') || get_post_type() != 'product' || is_admin()) {
            return;
        }

        $module_id = $this->shortcode_atts['module_id'];
        $module_class = $this->shortcode_atts['module_class'];

        if (!$to_show = $this->shortcode_atts['to_show']) {
            $to_show = 3;
        }

        $fullwidth = $this->shortcode_atts['fullwidth'];
        $use_loop = $this->shortcode_atts['use_loop'];
        $loop_layout = $this->shortcode_atts['loop_layout'];
        $title = $this->shortcode_atts['title'];
        $cols = $this->shortcode_atts['columns'];

        $output = '';
        $module_class = ET_Builder_Element::add_module_order_class($module_class, $function_name);

        if ($fullwidth == 'list') {
            $module_class .= ' et_pb_woo_archive_list';
        } else {
            $module_class .= ' et_pb_woo_archive_grid';
        }

        //////////////////////////////////////////////////////////////////////

        ob_start();

        if ($use_loop == 'on') {

            //global $product;
            //$product = wc_get_product(sb_et_woo_li_get_id());
            global $product;
            $related_products = wc_get_related_products(get_the_ID(), $to_show, $product->get_upsell_ids());

            $args = array('post_type' => 'product', 'post__in' => $related_products);

            //echo '<pre>';
            //print_r($args);
            //echo '</pre>';

            query_posts($args);

            if (have_posts()) {
                $i = 0;
                $j = 0;

                if ($title) {
                    echo '<h2 class="module-title">' . $title . '</h2>';
                }

                if ($fullwidth == 'off') { //grid
                    echo '<div class="et_pb_row et_pb_row_woo">';
                }

                while (have_posts()) {
                    the_post();

                    if ($fullwidth == 'off') { //grid
                        echo '<div class="et_woo_container_column et_pb_column et_pb_column_1_' . $cols . '  et_pb_column_' . $i . '">';
                    }

                    echo do_shortcode('[et_pb_section global_module="' . $loop_layout . '"][/et_pb_section]');

                    if ($fullwidth == 'off') { //grid
                        echo '</div>';
                    }

                    $i++;
                    $j++;

                    if ($i == $cols && ($fullwidth == 'off') && $j != $to_show) {
                        $i = 0;

                        echo '</div>';
                        echo '<div class="et_pb_row et_pb_row_woo">';
                    }
                } // endwhile

                if ($fullwidth == 'off') { //grid
                    echo '</div>';
                }

                wp_reset_query();
            }

        } else {
            $args = array();
            $defaults = array(
                'posts_per_page' => $to_show,
                'columns' => 3,
                'orderby' => 'rand'
            );

            $args = wp_parse_args($args, $defaults);

            woocommerce_related_products($args);
        }

        $content = ob_get_clean();

        //////////////////////////////////////////////////////////////////////

        if ($content) {
            $output = sprintf(
                '<div%5$s class="%1$s%3$s%6$s">
                            %2$s
                        %4$s',
                'clearfix ',
                $content,
                esc_attr('et_pb_module'),
                '</div>',
                ('' !== $module_id ? sprintf(' id="%1$s"', esc_attr($module_id)) : ''),
                ('' !== $module_class ? sprintf(' %1$s', esc_attr($module_class)) : '')
            );

            if ($use_loop == 'on') {
                if ('off' == $fullwidth) {
                    $output = sprintf('<div class="woocommerce et_pb_blog_grid_wrapper">%1$s</div>', $output);
                } else if ('list' == $fullwidth) {
                    $output = sprintf('<div class="woocommerce et_pb_woo_list_wrapper">%1$s</div>', $output);
                }
            }

        }

        return $output;
    }
}

new sb_et_woo_li_related_module();

?>