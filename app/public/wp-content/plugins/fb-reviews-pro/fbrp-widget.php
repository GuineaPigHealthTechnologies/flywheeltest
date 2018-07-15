<?php

/**
 * Facebook Reviews Pro Widget
 *
 * @description: The Facebook Reviews Pro Widget
 * @since      : 1.0
 */

class Fb_Reviews_Widget_Pro extends WP_Widget {

    public $options;

    public $widget_fields = array(
        'title'                => '',
        'page_id'              => '',
        'page_name'            => '',
        'page_photo'       => '',
        'page_access_token'    => '',
        'dark_theme'           => '',
        'view_mode'            => '',
        'cache'                => '24',

        'rating_snippet'       => '',
        'text_size'            => '',
        'min_filter'           => '',
        'min_letter'           => '',
        'pagination'           => '25',
        'hide_photo'           => '',
        'hide_avatar'          => '',
        'disable_user_link'    => '',
        'slider_speed'         => '',
        'slider_count'         => '',
        'slider_hide_pagin'    => '',
        'open_link'            => true,
        'nofollow_link'        => true,
        'api_ratings_limit'    => FBRP_API_RATINGS_LIMIT,
        'hide_float_badge'     => '',
        'lazy_load_img'        => '',
    );

    public function __construct() {
        parent::__construct(
            'fbrp_widget', // Base ID
            'Facebook Reviews Pro', // Name
            array(
                'classname'   => 'fb-reviews-widget',
                'description' => fbrp_i('Display Facebook Reviews Pro on your website.', 'fbrp')
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'fbrp_widget_scripts'));

        wp_register_script('wpac_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));
        wp_enqueue_script('wpac_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));

        wp_register_style('fbrev_css', plugins_url('/static/css/facebook-review.css', __FILE__));
        wp_enqueue_style('fbrev_css', plugins_url('/static/css/facebook-review.css', __FILE__));
    }

    function fbrp_widget_scripts($hook) {
        if ($hook == 'widgets.php' || ($hook == 'post.php' && defined('SITEORIGIN_PANELS_VERSION'))) {

            wp_register_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));
            wp_enqueue_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));

            wp_enqueue_script('jquery');

            wp_register_script('wpac_js', plugins_url('/static/js/wpac.js', __FILE__));
            wp_enqueue_script('wpac_js', plugins_url('/static/js/wpac.js', __FILE__));

            wp_register_script('fbrev_connect_js', plugins_url('/static/js/fbrev-connect.js', __FILE__));
            wp_enqueue_script('fbrev_connect_js', plugins_url('/static/js/fbrev-connect.js', __FILE__));
        }
    }

    function widget($args, $instance) {
        global $wpdb;

        if (fbrp_enabled()) {
            extract($args);
            foreach ($this->widget_fields as $variable => $value) {
                ${$variable} = !isset($instance[$variable]) ? $this->widget_fields[$variable] : esc_attr($instance[$variable]);
            }

            if (empty($page_id)) { ?>
                <div class="fbrev-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
                    <?php echo fbrp_i('Please check that this widget <b>Facebook Reviews</b> has a connected Facebook.'); ?>
                </div> <?php
                return false;
            }

            echo $before_widget;
            $response = fbrp_api_rating($page_id, $page_access_token, $instance, $this->id, $cache, $api_ratings_limit);
            $response_data = $response['data'];
            $response_json = rplg_json_decode($response_data);
            if (isset($response_json->ratings) && isset($response_json->ratings->data)) {
                $reviews = $response_json->ratings->data;
                if ($title) { ?><h2 class="fbrev-widget-title widget-title"><?php echo $title; ?></h2><?php }
                include(dirname(__FILE__) . '/fbrp-reviews.php');
                if ($view_mode == 'badge') {
                    ?>
                    <style>
                    #<?php echo $this->id; ?> {
                      margin: 0;
                      padding: 0;
                      border: none;
                    }
                    </style>
                    <?php
                }
            } else {
                ?>
                <div class="fbrev-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
                    <?php echo fbrp_i('Facebook API Rating: ') . $response_data; ?>
                </div>
                <?php
            }
            echo $after_widget;
        }
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        foreach ($this->widget_fields as $field => $value) {
            $instance[$field] = strip_tags(stripslashes($new_instance[$field]));
        }
        return $instance;
    }

    function form($instance) {
        foreach ($this->widget_fields as $field => $value) {
            if (array_key_exists($field, $this->widget_fields)) {
                ${$field} = !isset($instance[$field]) ? $value : esc_attr($instance[$field]);
            }
        }
        ?>
        <div id="<?php echo $this->id; ?>" class="rplg-widget">
            <div class="form-group">
                <div class="col-sm-12">
                    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="form-control" placeholder="<?php echo fbrp_i('Widget title'); ?>" />
                </div>
            </div>
            <?php
            include(dirname(__FILE__) . '/fbrp-id-options.php');
            include(dirname(__FILE__) . '/fbrp-options.php');
            ?>
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-widget-id="<?php echo $this->id; ?>"
              onload="fbrev_init({widgetId: this.getAttribute('data-widget-id')})" style="display:none">
        </div>
        <?php
    }
}
?>