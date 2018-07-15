<?php

function yrp_page($business, $rating, $business_img, $rating_snippet, $hide_photo, $open_link, $nofollow_link, $lazy_load_img, $show_review_count=true) {
    if (!$hide_photo) { ?>
    <div class="wp-yelp-left">
        <?php yrp_image($business_img, $business->name, $lazy_load_img); ?>
    </div>
    <?php } ?>
    <div class="wp-yelp-right">
        <?php if ($rating_snippet) { ?>
        <div class="wp-yelp-name">
            <?php echo yrp_anchor($business->url, '', '<span itemprop="name">' . $business->name . '</span>', $open_link, $nofollow_link); ?>
            <meta itemprop="image" content="<?php echo $business_img; ?>"/>
        </div>
        <div itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
            <span class="wp-yelp-rating" itemprop="ratingValue"><?php echo $rating; ?></span>
            <span class="wp-yelp-stars"><?php yrp_stars($rating); ?></span>
            <meta itemprop="ratingCount" content="<?php echo $business->review_count; ?>"/>
            <meta itemprop="bestRating" content="5"/>
        </div>
        <?php } else { ?>
        <div class="wp-yelp-name">
            <?php echo yrp_anchor($business->url, '', '<span>' . $business->name . '</span>', $open_link, $nofollow_link); ?>
        </div>
        <div>
            <span class="wp-yelp-rating"><?php echo $rating; ?></span>
            <span class="wp-yelp-stars"><?php yrp_stars($rating); ?></span>
        </div>
        <?php } ?>

        <?php if ($show_review_count) { ?>
        <div class="wp-yelp-powered">Based on <?php echo $business->review_count; ?> Reviews</div>
        <div class="wp-yelp-logo">
            <?php echo yrp_anchor($business->url, '', '<img src="' . YRP_PLUGIN_URL . '/static/img/yelp-logo.png" alt="Yelp logo">', $open_link, $nofollow_link); ?>
        </div>
        <?php } ?>
    </div>
    <?php
}

function yrp_page_reviews($reviews, $pagination, $business_id, $min_filter, $hide_avatar, $text_size, $open_link, $nofollow_link, $lazy_load_img) {
    ?>
    <div class="wp-yelp-reviews">
    <?php
    $hr = false;
    if (count($reviews) > 0) {
        $i = 0;
        foreach ($reviews as $review) {
            if ($pagination > 0 && $pagination <= $i++) {
                $hr = true;
            }
            yrp_page_review($review, $hide_avatar, $text_size, $open_link, $nofollow_link, $lazy_load_img, $hr);
        }
    }
    ?>
    </div>

    <?php if ($pagination > 0 && $hr) { ?>
    <a class="wp-yelp-url" href="#" onclick="return rplg_next_reviews.call(this, 'yelp', <?php echo $pagination; ?>);"><?php echo yrp_i('Next Reviews'); ?></a>
    <?php }
}

function yrp_page_review($review, $hide_avatar, $text_size, $open_link, $nofollow_link, $lazy_load_img, $hide_review=false) {
    ?>
    <div class="wp-yelp-review <?php if ($hide_review) { ?>wp-yelp-hide<?php } ?>">
        <?php if (!$hide_avatar) { ?>
        <div class="wp-yelp-left">
            <?php yrp_image($review->author_img, $review->author_name, $lazy_load_img); ?>
        </div>
        <?php } ?>
        <div class="wp-yelp-right">
            <?php yrp_anchor($review->url, 'wp-yelp-name', $review->author_name, $open_link, $nofollow_link); ?>
            <div class="wp-yelp-time" data-time="<?php echo $review->time; ?>"><?php echo $review->time; ?></div>
            <div class="wp-yelp-feedback">
                <span class="wp-yelp-stars"><?php echo yrp_stars($review->rating); ?></span>
                <?php if (isset($review->text)) { ?>
                <span class="wp-yelp-text"><?php echo yrp_trim_text($review->text, $text_size); ?></span>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

function yrp_stars($rating) {
    ?><span class="wp-stars"><svg class="yrw-rating yrw-rating-<?php echo $rating * 10; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 865 145" width="865" height="145"><defs><linearGradient id="yrw-rating-gradient-0" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#CCCCCC"/><stop offset="100%" stop-color="#CCCCCC"/></linearGradient><linearGradient id="yrw-rating-gradient-1" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#F2BD79"/><stop offset="100%" stop-color="#F2BD79"/></linearGradient><linearGradient id="yrw-rating-gradient-2" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#FEC011"/><stop offset="100%" stop-color="#FEC011"/></linearGradient><linearGradient id="yrw-rating-gradient-3" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#FF9242"/><stop offset="100%" stop-color="#FF9242"/></linearGradient><linearGradient id="yrw-rating-gradient-4" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#F15C4F"/><stop offset="100%" stop-color="#F15C4F"/></linearGradient><linearGradient id="yrw-rating-gradient-5" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#D32323"/><stop offset="100%" stop-color="#D32323"/></linearGradient></defs><path class="yrw-stars-1f" d="M110.6 0h-76.9c-18.6 0-33.7 15.1-33.7 33.7v76.9c0 18.6 15.1 33.7 33.7 33.7h76.9c18.6 0 33.7-15.1 33.7-33.7v-76.9c0-18.6-15.1-33.7-33.7-33.7z"/><path class="yrw-stars-0h" d="M33.3,0.3C14.7,0.3-0.4,15.4-0.4,34V111c0,18.6,15.1,33.7,33.7,33.7h38.3V0.3H33.3z"/><path class="yrw-stars-2f" d="M290.6 0h-76.9c-18.6 0-33.7 15.1-33.7 33.7v76.9c0 18.6 15.1 33.7 33.7 33.7h76.9c18.6 0 33.7-15.1 33.7-33.7v-76.9c0-18.6-15.1-33.7-33.7-33.7z"/><path class="yrw-stars-1h" d="M214,0.3c-18.6,0-33.7,15.1-33.7,33.7v77c0,18.6,15.1,33.7,33.7,33.7h38.3V0.3H214z"/><path class="yrw-stars-3f" d="M470.4 0h-76.9c-18.6 0-33.7 15.1-33.7 33.7v76.9c0 18.6 15.1 33.7 33.7 33.7h76.9c18.6 0 33.7-15.1 33.7-33.7v-76.9c.1-18.6-15.1-33.7-33.7-33.7z"/><path class="yrw-stars-2h" d="M393.9,0.6c-18.6,0-33.7,15.1-33.7,33.7v77c0,18.6,15.1,33.7,33.7,33.7h38.3V0.6H393.9z"/><path class="yrw-stars-4f" d="M650.6 0h-76.9c-18.6 0-33.7 15.1-33.7 33.7v76.9c0 18.6 15.1 33.7 33.7 33.7h76.9c18.6 0 33.7-15.1 33.7-33.7v-76.9c0-18.6-15.1-33.7-33.7-33.7z"/><path class="yrw-stars-3h" d="M573.9 0c-18.6 0-33.7 15.1-33.7 33.7v77c0 18.6 15.1 33.7 33.7 33.7h38.3v-144.4h-38.3z"/><path class="yrw-stars-5f" d="M830.6 0h-76.9c-18.6 0-33.7 15.1-33.7 33.7v76.9c0 18.6 15.1 33.7 33.7 33.7h76.9c18.6 0 33.7-15.1 33.7-33.7v-76.9c0-18.6-15.1-33.7-33.7-33.7z"/><path class="yrw-stars-4h" d="M753.8 0c-18.6 0-33.7 15.1-33.7 33.7v77c0 18.6 15.1 33.7 33.7 33.7h38.3v-144.4h-38.3z"/><path class="yrw-stars" fill="#FFF" stroke="#FFF" stroke-width="2" stroke-linejoin="round" d="M72 19.3l13.6 35.4 37.9 2-29.5 23.9 9.8 36.6-31.8-20.6-31.8 20.6 9.8-36.6-29.5-23.9 37.9-2zm180.2 0l13.6 35.4 37.8 2-29.4 23.9 9.8 36.6-31.8-20.6-31.9 20.6 9.8-36.6-29.4-23.9 37.8-2zm179.8 0l13.6 35.4 37.9 2-29.5 23.9 9.8 36.6-31.8-20.6-31.8 20.6 9.8-36.6-29.5-23.9 37.9-2zm180.2 0l13.6 35.4 37.8 2-29.4 23.9 9.8 36.6-31.8-20.6-31.9 20.6 9.8-36.6-29.4-23.9 37.8-2zm180 0l13.6 35.4 37.8 2-29.4 23.9 9.8 36.6-31.8-20.6-31.9 20.6 9.8-36.6-29.4-23.9 37.8-2z"/></svg></span><?php
}

function yrp_rstrpos($haystack, $needle, $offset) {
    $size = strlen ($haystack);
    $pos = strpos (strrev($haystack), $needle, $size - $offset);

    if ($pos === false)
        return false;

    return $size - $pos;
}

function yrp_trim_text($text, $size) {
    if ($size > 0 && strlen($text) > $size) {
        $visible_text = $text;
        $invisible_text = '';
        $idx = yrp_rstrpos($text, ' ', $size);
        if ($idx < 1) {
            $idx = $size;
        }
        if ($idx > 0) {
            $visible_text = substr($text, 0, $idx);
            $invisible_text = substr($text, $idx, strlen($text));
        }
        echo $visible_text;
        if (strlen($invisible_text) > 0) {
            ?><span class="wp-more"><?php echo $invisible_text; ?></span><span class="wp-more-toggle" onclick="this.previousSibling.className='';this.textContent='';"><?php echo yrp_i('read more'); ?></span><?php
        }
    } else {
        echo $text;
    }
}

function yrp_anchor($url, $class, $text, $open_link, $nofollow_link) {
    ?><a href="<?php echo $url; ?>" class="<?php echo $class; ?>" <?php if ($open_link) { ?>target="_blank"<?php } ?> <?php if ($nofollow_link) { ?>rel="nofollow"<?php } ?>><?php echo $text; ?></a><?php
}

function yrp_image($src, $alt, $lazy) {
    ?><img <?php if ($lazy) { ?>class="rplg-blazy" data-<?php } ?>src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" onerror="if(this.src!='<?php echo YRP_AVATAR; ?>')this.src='<?php echo YRP_AVATAR; ?>';"><?php
}
?>