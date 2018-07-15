<?php

/**
 * Yelp Reviews Pro
 *
 * @description: The Yelp Reviews Pro
 * @since      : 1.0
 */

class Yelp_Reviews_Pro extends WP_Widget {

    public $options;
    public $api_key;

    public $widget_fields = array(
        'title'                => '',
        'business_id'          => '',
        'dark_theme'           => '',
        'open_link'            => true,
        'nofollow_link'        => true,

        'business_photo'       => '',
        'auto_load'            => '',
        'rating_snippet'       => '',
        'pagination'           => '',
        'sort'                 => '',
        'min_filter'           => '',
        'text_size'            => '',
        'hide_photo'           => '',
        'hide_avatar'          => '',
        'view_mode'            => '',
        'hide_float_badge'     => '',
        'lazy_load_img'        => '',
    );

    public function __construct() {
        parent::__construct(
            'yrp_widget', // Base ID
            'Yelp Reviews Pro', // Name
            array(
                'classname'   => 'yelp-reviews-pro',
                'description' => yrp_i('Display Yelp Business Reviews on your website.', 'yrp')
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'yrp_widget_scripts'));

        wp_register_script('yrw_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));
        wp_enqueue_script('yrw_time_js', plugins_url('/static/js/wpac-time.js', __FILE__));

        wp_register_style('yrw_widget_css', plugins_url('/static/css/yrw-widget.css', __FILE__));
        wp_enqueue_style('yrw_widget_css', plugins_url('/static/css/yrw-widget.css', __FILE__));
    }

    function yrp_widget_scripts($hook) {
        if ($hook == 'widgets.php' || ($hook == 'post.php' && defined('SITEORIGIN_PANELS_VERSION'))) {

            wp_register_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));
            wp_enqueue_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));

            wp_enqueue_script('jquery');

            wp_register_script('yrw_wpac_js', plugins_url('/static/js/wpac.js', __FILE__));
            wp_enqueue_script('yrw_wpac_js', plugins_url('/static/js/wpac.js', __FILE__));

            wp_register_script('yrw_finder_js', plugins_url('/static/js/yrw-finder.js', __FILE__));
            wp_localize_script('yrw_finder_js', 'finderVars', array(
                'YELP_AVATAR' => YRP_AVATAR,
                'handlerUrl' => admin_url('options-general.php?page=yrp'),
                'actionPrefix' => 'yrp'
            ));
            wp_enqueue_script('yrw_finder_js', plugins_url('/static/js/yrw-finder.js', __FILE__));
        }
    }

    function widget($args, $instance) {
        global $wpdb;

        if (yrp_enabled()) {
            extract($args);
            foreach ($this->widget_fields as $variable => $value) {
                ${$variable} = !isset($instance[$variable]) ? $this->widget_fields[$variable] : esc_attr($instance[$variable]);
            }

            echo $before_widget;
            if ($title) { ?><h2 class="yrw-widget-title widget-title"><?php echo $title; ?></h2><?php }
            if ($business_id) {
                include(dirname(__FILE__) . '/yrp-reviews.php');
            } else {
                ?>
                <div class="yrw-error" style="padding:10px;color:#B94A48;background-color:#F2DEDE;border-color:#EED3D7;">
                    <?php echo yrp_i('Please first find and save your Yelp Business.'); ?>
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
        global $wp_version;
        foreach ($this->widget_fields as $field => $value) {
            ${$field} = !isset($instance[$field]) ? $value : esc_attr($instance[$field]);
        }

        wp_nonce_field('yrw_wpnonce', 'yrw_nonce');

        $yrp_api_key = get_option('yrp_api_key');
        if ($yrp_api_key) { ?>

            <div id="<?php echo $this->id; ?>">
                <?php
                if (!$business_id) {
                    include(dirname(__FILE__) . '/yrp-finder.php');
                } else { ?>
                    <script type="text/javascript">
                        jQuery('.yrw-tooltip').remove();
                    </script> <?php
                }
                include(dirname(__FILE__) . '/yrp-options.php'); ?>
                <br>
            </div>

            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-widget-id="<?php echo $this->id; ?>" onload="yrw_sidebar_init({widgetId: this.getAttribute('data-widget-id')})" style="display:none"><?php

        } else { ?>
            <h4 class="text-left"><?php echo yrp_i('First configure Yelp API Key'); ?></h4>
            <ul style="line-height:20px">
                <li><?php echo yrp_i('1. Go to Yelp developers and '); ?><a href="https://www.yelp.com/developers/v3/manage_app" target="_blank"><?php echo yrp_i('Create New App'); ?></a></li>
                <li>
                    <?php echo yrp_i('2. Enter \'API Key\':'); ?>
                    <input type="text" class="yrw-app" name="api_key" placeholder="<?php echo yrp_i('API Key'); ?>" />
                </li>
                <li><?php echo yrp_i('3. Save the widget'); ?></li>
            </ul>

            <script type="text/javascript">
                var appinputs = document.querySelectorAll('.yrw-app');
                if (appinputs) {
                    WPacFastjs.onall(appinputs, 'change', function() {
                        if (!this.value) return;
                        jQuery.post('<?php echo admin_url('options-general.php?page=yrp'); ?>&cf_action=yrp_' + this.getAttribute('name'), {
                            app_key: this.value,
                            yrw_wpnonce: jQuery('#yrw_nonce').val()
                        }, function(res) {
                            console.log('RESPONSE', res);
                        }, 'json');
                    });
                }
            </script> <?php
        }
    }
}
?>