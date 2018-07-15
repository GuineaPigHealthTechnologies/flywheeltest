<?php

if (!current_user_can('manage_options')) {
    die('The account you\'re logged in to doesn\'t have permission to access this page.');
}

function fbrp_has_valid_nonce() {
    $nonce_actions = array('fbrp_settings', 'fbrp_active');
    $nonce_form_prefix = 'fbrp-form_nonce_';
    $nonce_action_prefix = 'fbrp-wpnonce_';
    foreach ($nonce_actions as $key => $value) {
        if (isset($_POST[$nonce_form_prefix.$value])) {
            check_admin_referer($nonce_action_prefix.$value, $nonce_form_prefix.$value);
            return true;
        }
    }
    return false;
}

if (!empty($_POST)) {
    $nonce_result_check = fbrp_has_valid_nonce();
    if ($nonce_result_check === false) {
        die('Unable to save changes. Make sure you are accessing this page from the Wordpress dashboard.');
    }
}

// Post fields that require verification.
$valid_fields = array(
    'fbrp_active' => array(
        'key_name' => 'fbrp_active',
        'values' => array('Disable', 'Enable')
    ));

// Check POST fields and remove bad input.
foreach ($valid_fields as $key) {

    if (isset($_POST[$key['key_name']]) ) {

        // SANITIZE first
        $_POST[$key['key_name']] = trim(sanitize_text_field($_POST[$key['key_name']]));

        // Validate
        if (isset($key['regexp']) && $key['regexp']) {
            if (!preg_match($key['regexp'], $_POST[$key['key_name']])) {
                unset($_POST[$key['key_name']]);
            }

        } else if (isset($key['type']) && $key['type'] == 'int') {
            if (!intval($_POST[$key['key_name']])) {
                unset($_POST[$key['key_name']]);
            }

        } else {
            $valid = false;
            $vals = $key['values'];
            foreach ($vals as $val) {
                if ($_POST[$key['key_name']] == $val) {
                    $valid = true;
                }
            }
            if (!$valid) {
                unset($_POST[$key['key_name']]);
            }
        }
    }
}

if (isset($_POST['fbrp_active']) && isset($_GET['fbrp_active'])) {
    update_option('fbrp_active', ($_GET['fbrp_active'] == '1' ? '1' : '0'));
}

if (isset($_POST['fbrp_setting'])) {
    update_option('fbrp_license', $_POST['fbrp_license']);
    update_option('fbrp_expired', '');
    $fbrp_setting_page = true;
} else {
    $fbrp_setting_page = false;
}

wp_register_style('twitter_bootstrap3_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));
wp_enqueue_style('twitter_bootstrap3_css', plugins_url('/static/css/bootstrap.min.css', __FILE__));

wp_register_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));
wp_enqueue_style('rplg_wp_css', plugins_url('/static/css/rplg-wp.css', __FILE__));

wp_register_style('rplg_setting_css', plugins_url('/static/css/rplg-setting.css', __FILE__));
wp_enqueue_style('rplg_setting_css', plugins_url('/static/css/rplg-setting.css', __FILE__));

wp_enqueue_script('jquery');

wp_register_script('wpac_js', plugins_url('/static/js/wpac.js', __FILE__));
wp_enqueue_script('wpac_js', plugins_url('/static/js/wpac.js', __FILE__));

wp_register_script('fbrev_connect_js', plugins_url('/static/js/fbrev-connect.js', __FILE__));
wp_enqueue_script('fbrev_connect_js', plugins_url('/static/js/fbrev-connect.js', __FILE__));

$fbrp_license = get_option('fbrp_license');
$fbrp_expired = get_option('fbrp_expired');
$fbrp_enabled = get_option('fbrp_active') == '1';

if (strlen($fbrp_license) > 1 && strlen($fbrp_expired) < 1) {
    $request = wp_remote_post('https://api.richplugins.com/plugins/license-expired', array(
        'timeout' => 15,
        'sslverify' => false,
        'body' => array(
            'license' => $fbrp_license,
            'slug' => 'fbrp',
            'plugin' => 'Facebook Reviews Pro',
            'active' => '1',
            'siteurl' => get_option('siteurl')
        )
    ));

    if (!is_wp_error($request)) {
        $request = json_decode(wp_remote_retrieve_body($request));
    }
    if ($request && isset($request->expired)) {
        $fbrp_expired = $request->expired;
        update_option('fbrp_expired', $request->expired);
    } else {
        if (isset($request->error)) {
            $fbrp_license_error = $request->error;
        }
        update_option('fbrp_expired', 'false');
    }
}

if (isset($_POST['fbrp_license_deactivate'])) {
    $request = wp_remote_post('https://api.richplugins.com/plugins/license-expired', array(
        'timeout' => 15,
        'sslverify' => false,
        'body' => array(
            'license' => $fbrp_license,
            'slug' => 'fbrp',
            'plugin' => 'Facebook Reviews Pro',
            'active' => '0'
        )
    ));

    if (!is_wp_error($request)) {
        $request = json_decode(wp_remote_retrieve_body($request));
    }
    if ($request && isset($request->error)) {
        $fbrp_license_error = $request->error;
    } else {
        $fbrp_license_deactivated = $fbrp_license;
        update_option('fbrp_expired', '');
        update_option('fbrp_license', '');
        $fbrp_expired = '';
        $fbrp_license = '';
        $fbrp_setting_page = true;
    }
}
?>

<span class="rplg-version rplg-pro"><?php echo fbrp_i('Pro Version: %s', esc_html(FBRP_VERSION)); ?></span>
<div class="rplg-setting container-fluid">
    <div class="rplg-setting-facebook">Facebook Reviews Pro</div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"<?php if (!$fbrp_setting_page) { ?> class="active"<?php } ?>>
            <a href="#about" aria-controls="about" role="tab" data-toggle="tab"><?php echo fbrp_i('About'); ?></a>
        </li>
        <li role="presentation"<?php if ($fbrp_setting_page) { ?> class="active"<?php } ?>>
            <a href="#setting" aria-controls="setting" role="tab" data-toggle="tab"><?php echo fbrp_i('Setting'); ?></a>
        </li>
        <li role="presentation">
            <a href="#shortcode" aria-controls="shortcode" role="tab" data-toggle="tab"><?php echo fbrp_i('Shortcode Builder'); ?></a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane<?php if (!$fbrp_setting_page) { ?> active<?php } ?>" id="about">
            <div class="row">
                <div class="col-sm-6">
                    <h4><?php echo fbrp_i('Facebook Reviews for WordPress'); ?></h4>
                    <p><?php echo fbrp_i('Facebook Reviews plugin is an easy and fast way to integrate Facebook Page reviews right into your WordPress website. This plugin works instantly and show Facebook reviews in sidebar widget or on any page via shortcode.'); ?></p>
                    <ol>
                        <li>Go to menu <b>"Appearance"</b> -> <b>"Widgets"</b></li>
                        <li>Move "Facebook Reviews" widget to sidebar</li>
                        <li>Click by 'Connect to Facebook' button</li>
                        <li>Log in via Facebook and agree manage pages permission</li>
                        <li>After log in under the button you will see a list of your Facebook pages</li>
                        <li>Click on the needed page and <b>Save</b> the widget</li>
                    </ol>
                    <p><?php echo fbrp_i('To add Facebook reviews on any page, please use shortcode which can create in <b>Shortcode Builder</b> tab.'); ?></p>
                    <p><?php echo fbrp_i('Feel free to contact us by email <a href="mailto:support@richplugins.com">support@richplugins.com</a>.'); ?></p>
                    <div class="row">
                        <div class="col-sm-4">
                            <div id="fb-root"></div>
                            <script>(function(d, s, id) {
                              var js, fjs = d.getElementsByTagName(s)[0];
                              if (d.getElementById(id)) return;
                              js = d.createElement(s); js.id = id;
                              js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.6&appId=1501100486852897";
                              fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));</script>
                            <div class="fb-like" data-href="https://richplugins.com/" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
                        </div>
                        <div class="col-sm-4 twitter">
                            <a href="https://twitter.com/richplugins" class="twitter-follow-button" data-show-count="false">Follow @RichPlugins</a>
                            <script>!function (d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                    if (!d.getElementById(id)) {
                                        js = d.createElement(s);
                                        js.id = id;
                                        js.src = p + '://platform.twitter.com/widgets.js';
                                        fjs.parentNode.insertBefore(js, fjs);
                                    }
                                }(document, 'script', 'twitter-wjs');</script>
                        </div>
                        <div class="col-sm-4 googleplus">
                            <div class="g-plusone" data-size="medium" data-annotation="inline" data-width="200" data-href="https://plus.google.com/101080686931597182099"></div>
                            <script type="text/javascript">
                                window.___gcfg = { lang: 'en-US' };
                                (function () {
                                    var po = document.createElement('script');
                                    po.type = 'text/javascript';
                                    po.async = true;
                                    po.src = 'https://apis.google.com/js/plusone.js';
                                    var s = document.getElementsByTagName('script')[0];
                                    s.parentNode.insertBefore(po, s);
                                })();
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <br>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="//www.youtube.com/embed/o0HV-bJ6_qE?rel=0" allowfullscreen=""></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane<?php if ($fbrp_setting_page) { ?> active<?php } ?>" id="setting">
            <h4><?php echo fbrp_i('Facebook Reviews Pro Setting'); ?></h4>
            <!-- Configuration form -->
            <form method="POST" enctype="multipart/form-data">
            <?php wp_nonce_field('fbrp-wpnonce_fbrp_settings', 'fbrp-form_nonce_fbrp_settings'); ?>
                <div class="form-group">
                    <label class="control-label" for="fbrp_license"><?php echo fbrp_i('Pro license'); ?></label>
                    <input class="form-control" type="text" id="fbrp_license" name="fbrp_license" value="<?php echo esc_attr($fbrp_license); ?>">
                </div>
                <?php if (strlen($fbrp_expired) > 0 && $fbrp_expired != 'false') { ?>
                <div class="alert alert-dismissible alert-success">
                    <strong>Your license is active until <u><?php echo date(get_option('date_format'), $fbrp_expired / 1000); ?></u></strong><br>
                    * Plugin automatically updates<br>
                    * Access to priority support <a href="mailto:priority@richplugins.com">priority@richplugins.com</a><br>
                    <button name="fbrp_license_deactivate" type="submit" class="button-primary button" onclick="return confirm('Are you sure you want to deactivate the license?');">Deactivate License</button>
                </div>
                <?php } elseif (isset($fbrp_license_error) && strlen($fbrp_license_error) > 0) { ?>
                <div class="alert alert-dismissible alert-danger">
                    <strong>Activation error </strong><br><?php echo $fbrp_license_error; ?>
                </div>
                <?php } elseif (isset($fbrp_license_deactivated) && strlen($fbrp_license_deactivated) > 0) { ?>
                <div class="alert alert-dismissible alert-success">
                    <strong>The license was deactivated: </strong> <?php echo $fbrp_license_deactivated; ?>
                </div>
                <?php } else { ?>
                <p>Activate your license to receive automatic plugin updates and priority support for the life of your license.</p>
                <?php } ?>
                <p class="submit" style="text-align: left">
                    <input name="fbrp_setting" type="submit" value="Save" class="button-primary button" tabindex="4">
                </p>
            </form>
            <hr>
            <!-- Enable/disable Facebook Reviews Widget toggle -->
            <form method="POST" action="?page=fbrp&amp;fbrp_active=<?php echo (string)((int)($fbrp_enabled != true)); ?>">
                <?php wp_nonce_field('fbrp-wpnonce_fbrp_active', 'fbrp-form_nonce_fbrp_active'); ?>
                <span class="status">
                    <?php echo fbrp_i('Facebook Reviews Pro are currently <b>'). ($fbrp_enabled ? fbrp_i('enable') : fbrp_i('disable')) . '</b>'; ?>
                </span>
                <input type="submit" name="fbrp_active" class="button" value="<?php echo $fbrp_enabled ? fbrp_i('Disable') : fbrp_i('Enable'); ?>" />
            </form>
            <hr>
            <button class="btn btn-primary btn-small" type="button" data-toggle="collapse" data-target="#debug" aria-expanded="false" aria-controls="debug">
                <?php echo fbrp_i('Debug Information'); ?>
            </button>
            <div id="debug" class="collapse">
                <textarea style="width:90%; height:200px;" onclick="this.select();return false;" readonly><?php
                    rplg_debug(FBRP_VERSION, fbrp_options(), 'widget_fbrp_widget');
                ?></textarea>
            </div>
            <div style="max-width:700px">Feel free to contact support team by support@richplugins.com for any issues but please don't forget to provide debug information that you can get by click on 'Debug Information' button.</div>
        </div>
        <div role="tabpanel" class="tab-pane" id="shortcode">
            <h4><?php echo fbrp_i('Shortcode Builder'); ?></h4>
            <?php
            class fbrp_widget {
                function get_field_id($id) {
                    return $id;
                }
                function get_field_name($name) {
                    return $name;
                }
                function render_id_options() {
                    $page_id               = '';
                    $page_name             = '';
                    $page_photo            = '';
                    $page_access_token     = '';
                    include(dirname(__FILE__) . '/fbrp-id-options.php');
                }
                function render_options() {
                    $dark_theme            = '';
                    $view_mode             = '';
                    $cache                 = '24';
                    $rating_snippet        = '';
                    $text_size             = '';
                    $min_filter            = '';
                    $min_letter            = '';
                    $pagination            = '';
                    $hide_photo            = '';
                    $hide_avatar           = '';
                    $disable_user_link     = '';
                    $slider_speed          = '';
                    $slider_count          = '';
                    $slider_hide_pagin     = '';
                    $open_link             = true;
                    $nofollow_link         = true;
                    $max_width             = '';
                    $max_height            = '';
                    $api_ratings_limit     = '';
                    $hide_float_badge      = '';
                    $lazy_load_img         = '';
                    include(dirname(__FILE__) . '/fbrp-options.php');
                }
            }
            $fbrp_widget = new fbrp_widget;
            ?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form form-horizontal">
                        <?php
                        $fbrp_widget->render_id_options();
                        ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form form-horizontal">
                        <?php
                        $fbrp_widget->render_options();
                        ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form form-horizontal">
                        <textarea class="rplg-shortcode" onclick="this.select();return false;" readonly></textarea>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                function shortcode() {
                    if (!document.querySelector('#shortcode .fbrev-page-id').value) {
                        return;
                    }
                    var args = '', els = document.querySelectorAll('#shortcode .form-control[name]');
                    for (var i = 0; i < els.length; i++) {
                        var el = els[i];
                        if (el.type == 'checkbox') {
                            if (el.checked) {
                                args += ' ' + el.getAttribute('name') + '=' + el.checked;
                            }
                        } else if (el.value) {
                            args += ' ' + el.getAttribute('name') + '=';
                            if (el.value.indexOf(' ') > -1) {
                                args += '"' + el.value + '"';
                            } else {
                                args += el.value;
                            }
                        }
                    }
                    var textarea = document.querySelector('.rplg-shortcode');
                    textarea.innerHTML = '[facebook-reviews-pro' + args + ']';
                }

                jQuery(document).ready(function($) {
                    $('a[data-toggle="tab"]').on('click', function(e)  {
                        var active = $(this).attr('href');
                        $('.tab-content ' + active).addClass('active').show().siblings().hide();
                        $(this).parent('li').addClass('active').siblings().removeClass('active');
                        e.preventDefault();
                    });
                    $('button[data-toggle="collapse"]').click(function () {
                        $target = $(this);
                        $collapse = $target.next();
                        $collapse.slideToggle(500);
                    });

                    var fbrev_init_async = function(attempts) {
                            if (!window.fbrev_init) {
                                if (attempts > 0) {
                                    setTimeout(function() { fbrev_init_async(attempts - 1); }, 300);
                                }
                                return;
                            }
                            fbrev_init({
                                widgetId: 'shortcode',
                                cb: function(el, businessId) {
                                    shortcode();
                                }
                            });
                        };

                    fbrev_init_async(10);

                    $('#shortcode input.form-control[type="text"]').keyup(function() {
                        shortcode();
                    });
                    $('#shortcode input.form-control[type="checkbox"],select.form-control').change(function() {
                        shortcode();
                    });
                });
            </script>
        </div>
    </div>
</div>
