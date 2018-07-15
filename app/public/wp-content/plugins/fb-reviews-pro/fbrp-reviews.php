<?php
if ($lazy_load_img && strpos($view_mode, 'badge') !== 0) {
    wp_register_script('blazy_js', plugins_url('/static/js/blazy.min.js', __FILE__));
    wp_enqueue_script('blazy_js', plugins_url('/static/js/blazy.min.js', __FILE__));
}

wp_register_script('rplg_js', plugins_url('/static/js/rplg.js', __FILE__));
wp_enqueue_script('rplg_js', plugins_url('/static/js/rplg.js', __FILE__));

if ($view_mode == 'slider') {
    wp_register_style('swiper_css', plugins_url('/static/css/swiper.min.css', __FILE__));
    wp_enqueue_style('swiper_css', plugins_url('/static/css/swiper.min.css', __FILE__));
    wp_register_script('swiper_js', plugins_url('/static/js/swiper.min.js', __FILE__));
    wp_enqueue_script('swiper_js', plugins_url('/static/js/swiper.min.js', __FILE__));
}

include_once(dirname(__FILE__) . '/fbrp-reviews-helper.php');

$rating = 0;
$count = count($reviews);
$count_real = 0;
if ($count > 0) {
    foreach ($reviews as $review) {
        $rating = $rating + $review->rating;
        if ($review->rating >= $min_filter && (!$min_letter || (isset($review->review_text) && strlen($review->review_text) > $min_letter))) {
            $count_real++;
        }
    }
    $rating = round($rating / $count, 1);
    $rating = number_format((float)$rating, 1, '.', '');
}

$slider_count = $slider_count > 0 ? $slider_count : ($count_real > 2 ? 3 : $count_real);
?>

<?php if ($view_mode == 'slider') { ?>

<div class="fbrev-slider rplg-slider">
    <div class="rplgsw-container">
        <div class="rplgsw-wrapper">
        <?php
        foreach ($reviews as $review) {
            if ($review->rating >= $min_filter && (!$min_letter || (isset($review->review_text) && strlen($review->review_text) > $min_letter))) {
        ?>
            <div class="rplgsw-slide">

                <div class="fbrev-review">
                    <div class="wp-facebook-feedback">
                        <div class="wp-facebook-content2">
                            <span class="wp-facebook-stars"><?php echo fbrp_stars($review->rating); ?></span>
                            <?php if (isset($review->review_text)) { ?>
                            <span class="wp-facebook-text"><?php echo fbrp_trim_text($review->review_text, $text_size); ?></span>
                            <?php } ?>
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="30" height="30" viewBox="0 0 100 100">
                                <g transform="translate(23,85) scale(0.05,-0.05)">
                                    <path fill="#fff" d="M959 1524v-264h-157q-86 0 -116 -36t-30 -108v-189h293l-39 -296h-254v-759h-306v759h-255v296h255v218q0 186 104 288.5t277 102.5q147 0 228 -12z"></path>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="wp-facebook-user">
                        <?php if (!$hide_avatar) {
                            fbrp_image('https://graph.facebook.com/' . $review->reviewer->id . '/picture', $review->reviewer->name, $lazy_load_img);
                        } ?>
                        <div class="wp-facebook-info">
                            <?php
                            if (!$disable_user_link) {
                                $profile_url = 'https://facebook.com/' . $page_id . '/reviews';
                                fbrp_anchor($profile_url, 'wp-facebook-name', $review->reviewer->name, $open_link, $nofollow_link);
                            } else {
                                ?><div class="wp-facebook-name"><?php echo $review->reviewer->name; ?></div><?php
                            }
                            ?>
                            <div class="wp-facebook-time" data-time="<?php echo $review->created_time; ?>"><?php echo $review->created_time; ?></div>
                        </div>
                    </div>
                </div>

            </div>
            <?php
                }
            }
            ?>
        </div>
        <?php if (!$slider_hide_pagin) { ?>
        <div class="rplgsw-pagination"></div>
        <?php } ?>
    </div>
    <div class="rplg-slider-prev"><span>&lsaquo;</span></div>
    <div class="rplg-slider-next"><span>&rsaquo;</span></div>
    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" onload="(function(el) { var t = setInterval(function () {if (window.rplg_init_slider_theme){rplg_init_slider_theme(el, {speed: <?php echo ($slider_speed > 0 ? $slider_speed : 4) * 1000; ?>, count: <?php echo $slider_count; ?>, pagin: <?php echo !$slider_hide_pagin || true; ?>});clearInterval(t);}}, 200); })(this.parentNode);" style="display:none">
</div>

<?php } else { ?>

<div class="wp-fbrev wpac" <?php if ($rating_snippet) { ?>itemscope="" itemtype="http://schema.org/LocalBusiness"<?php } ?> style="<?php if (isset($max_width) && strlen($max_width) > 0) { ?>max-width:<?php echo $max_width;?>!important;<?php } ?><?php if (isset($max_height) && strlen($max_height) > 0) { ?>max-height:<?php echo $max_height;?>!important;overflow-y:auto!important;<?php } ?>">

    <?php if ($view_mode == 'list') { ?>

    <div class="wp-facebook-list<?php if ($dark_theme) { ?> wp-dark<?php } ?>">
        <div class="wp-facebook-place">
            <?php fbrp_page($page_id, $page_name, $rating, $rating_snippet, $count, $page_photo, $hide_photo, $open_link, $nofollow_link, $lazy_load_img); ?>
        </div>
        <div class="wp-facebook-content-inner">
            <?php fbrp_page_reviews($page_id, $reviews, $hide_avatar, $text_size, $min_filter, $min_letter, $pagination, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img); ?>
        </div>
    </div>

    <?php } elseif ($view_mode == 'grid') { ?>

    <style>
    .wp-fbrev .wp-facebook-grid {
        display: -webkit-flex!important;
        display: -ms-flexbox!important;
        display: flex!important;
        -webkit-flex-flow: row wrap!important;
        -ms-flex-flow: row wrap!important;
        flex-flow: row wrap!important;
        margin: 0 auto!important;
        -webkit-align-items: stretch!important;
        -ms-flex-align: stretch!important;
        align-items: stretch!important;
    }
    @media (min-width: 840px) {
        .wp-fbrev .wp-facebook-grid {
            padding: 8px!important;
        }
    }
    .wp-fbrev .wp-facebook-col {
        box-sizing: border-box!important;
    }
    @media (min-width: 840px) {
        .wp-fbrev .wp-facebook-col-4 {
            margin: 8px!important;
            width: calc(33.3333333333% - 16px)!important;
        }
    }
    @media (max-width: 839px) and (min-width: 480px) {
        .wp-fbrev .wp-facebook-col-4 {
            margin: 8px!important;
            width: calc(50% - 16px)!important;
        }
    }
    @media (max-width: 479px) {
        .wp-fbrev .wp-facebook-col-4 {
            margin: 8px!important;
            width: calc(100% - 16px)!important;
        }
    }
    .wp-fbrev .wp-facebook-col-6 {
        margin: 8px!important;
        width: calc(50% - 16px)!important;
    }
    </style>
    <div class="wp-facebook-grid<?php if ($dark_theme) { ?> wp-dark<?php } ?>">
        <?php
        switch ($count_real) {
            case 1:
                $col = 12;
                break;
            case 2:
                $col = 6;
                break;
            default:
               $col = 4;
        }
        foreach ($reviews as $review) {
            $col_class = 'wp-facebook-col-' . $col;
            if ($review->rating >= $min_filter && (!$min_letter || (isset($review->review_text) && strlen($review->review_text) > $min_letter))) { ?>
            <div class="wp-facebook-col <?php echo $col_class; ?>">
                <?php fbrp_page_review($page_id, $review, $hide_avatar, $text_size, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img); ?>
            </div><?php
            }
        } ?>
    </div>

    <?php } else { ?>

    <div class="rplg-badge">
        <div class="wp-facebook-badge<?php if ($view_mode != 'badge_inner') { ?> wp-facebook-<?php echo $view_mode; ?>-fixed<?php } ?><?php if ($hide_float_badge) { ?> wp-facebook-badge-hide<?php } ?>">
            <div class="wp-facebook-border"></div>
            <div class="wp-facebook-badge-btn">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="30" height="30" viewBox="0 0 100 100">
                    <g transform="translate(23,85) scale(0.05,-0.05)">
                        <path fill="#fff" d="M959 1524v-264h-157q-86 0 -116 -36t-30 -108v-189h293l-39 -296h-254v-759h-306v759h-255v296h255v218q0 186 104 288.5t277 102.5q147 0 228 -12z"></path>
                    </g>
                </svg>
                <?php if ($rating_snippet) { ?>
                <meta itemprop="name" content="<?php echo $page_name; ?>"/>
                <meta itemprop="image" content="https://graph.facebook.com/<?php echo $page_id; ?>/picture"/>
                <div class="wp-facebook-badge-score" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                    <div><?php echo fbrp_i('Facebook Rating'); ?></div>
                    <span class="wp-facebook-rating" itemprop="ratingValue"><?php echo $rating; ?></span>
                    <span class="wp-facebook-stars"><?php fbrp_stars($rating); ?></span>
                    <meta itemprop="ratingCount" content="<?php echo $count; ?>"/>
                    <meta itemprop="bestRating" content="5"/>
                </div>
                <?php } else { ?>
                <div class="wp-facebook-badge-score">
                    <div><?php echo fbrp_i('Facebook Rating'); ?></div>
                    <span class="wp-facebook-rating"><?php echo $rating; ?></span>
                    <span class="wp-facebook-stars"><?php fbrp_stars($rating); ?></span>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="wp-facebook-form <?php if ($view_mode == 'badge_left') { ?>wp-facebook-form-left<?php } ?>" style="display:none">
            <div class="wp-facebook-head">
                <div class="wp-facebook-head-inner">
                    <?php fbrp_page($page_id, $page_name, $rating, false, 0, $page_photo, $hide_photo, $open_link, $nofollow_link, $lazy_load_img, false); ?>
                </div>
                <button class="wp-facebook-close" type="button" onclick="this.parentNode.parentNode.style.display='none'">×</button>
            </div>
            <div class="wp-facebook-body"></div>
            <div class="wp-facebook-content">
                <div class="wp-facebook-content-inner">
                    <?php fbrp_page_reviews($page_id, $reviews, $hide_avatar, $text_size, $min_filter, $min_letter, $pagination, $disable_user_link, $open_link, $nofollow_link, $lazy_load_img); ?>
                </div>
            </div>
            <div class="wp-facebook-footer">
                <div class="wp-facebook-powered">powered by <span>Facebook</span></div>
            </div>
        </div>
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" onload="(function(el) { var t = setInterval(function () {if (window.rplg_badge_init){rplg_badge_init(el, 'facebook', 'wp-fbrev');clearInterval(t);}}, 100); })(this.parentNode);" style="display:none">
    </div>

    <?php } ?>
</div>

<?php } ?>