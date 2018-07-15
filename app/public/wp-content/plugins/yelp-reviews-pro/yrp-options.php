<?php if (isset($title)) { ?>
<div class="form-group">
    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" placeholder="<?php echo yrp_i('Widget title'); ?>" />
</div>
<?php } ?>

<?php include(dirname(__FILE__) . '/yrp-id-options.php'); ?>

<h4 class="rplg-options-toggle"><?php echo yrp_i('Review Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('auto_load'); ?>" name="<?php echo $this->get_field_name('auto_load'); ?>" class="form-control" type="checkbox" value="true" <?php checked('true', $auto_load); ?> />
            <label for="<?php echo $this->get_field_id('auto_load'); ?>"><?php echo yrp_i('Try to get more than 3 reviews from Yelp'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('rating_snippet'); ?>" name="<?php echo $this->get_field_name('rating_snippet'); ?>" class="form-control" type="checkbox" value="true" <?php checked('true', $rating_snippet); ?> />
            <label for="<?php echo $this->get_field_id('rating_snippet'); ?>"><?php echo yrp_i('Enable Google Rich Snippet (schema.org)'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo yrp_i('Pagination'); ?>
            <select id="<?php echo $this->get_field_id('pagination'); ?>" name="<?php echo $this->get_field_name('pagination'); ?>" class="form-control">
                <option value="" <?php selected('', $pagination); ?>><?php echo yrp_i('Disabled'); ?></option>
                <option value="10" <?php selected('10', $pagination); ?>><?php echo yrp_i('10'); ?></option>
                <option value="5" <?php selected('5', $pagination); ?>><?php echo yrp_i('5'); ?></option>
                <option value="4" <?php selected('4', $pagination); ?>><?php echo yrp_i('4'); ?></option>
                <option value="3" <?php selected('3', $pagination); ?>><?php echo yrp_i('3'); ?></option>
                <option value="2" <?php selected('2', $pagination); ?>><?php echo yrp_i('2'); ?></option>
                <option value="1" <?php selected('1', $pagination); ?>><?php echo yrp_i('1'); ?></option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo yrp_i('Sorting'); ?>
            <select id="<?php echo $this->get_field_id('sort'); ?>" name="<?php echo $this->get_field_name('sort'); ?>" class="form-control">
                <option value="" <?php selected('', $sort); ?>><?php echo yrp_i('Default'); ?></option>
                <option value="1" <?php selected('1', $sort); ?>><?php echo yrp_i('Most recent'); ?></option>
                <option value="2" <?php selected('2', $sort); ?>><?php echo yrp_i('Most oldest'); ?></option>
                <option value="3" <?php selected('3', $sort); ?>><?php echo yrp_i('Highest score'); ?></option>
                <option value="4" <?php selected('4', $sort); ?>><?php echo yrp_i('Lowest score'); ?></option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo yrp_i('Minimum Review Rating'); ?>
            <select id="<?php echo $this->get_field_id('min_filter'); ?>" name="<?php echo $this->get_field_name('min_filter'); ?>" class="form-control">
                <option value="" <?php selected('', $min_filter); ?>><?php echo yrp_i('No filter'); ?></option>
                <option value="5" <?php selected('5', $min_filter); ?>><?php echo yrp_i('5 Stars'); ?></option>
                <option value="4" <?php selected('4', $min_filter); ?>><?php echo yrp_i('4 Stars'); ?></option>
                <option value="3" <?php selected('3', $min_filter); ?>><?php echo yrp_i('3 Stars'); ?></option>
                <option value="2" <?php selected('2', $min_filter); ?>><?php echo yrp_i('2 Stars'); ?></option>
                <option value="1" <?php selected('1', $min_filter); ?>><?php echo yrp_i('1 Star'); ?></option>
            </select>
        </div>
    </div>
</div>

<h4 class="rplg-options-toggle"><?php echo yrp_i('Display Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('hide_photo'); ?>" name="<?php echo $this->get_field_name('hide_photo'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $hide_photo); ?> />
            <label for="<?php echo $this->get_field_id('hide_photo'); ?>"><?php echo yrp_i('Hide business photo'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('hide_avatar'); ?>" name="<?php echo $this->get_field_name('hide_avatar'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $hide_avatar); ?> />
            <label for="<?php echo $this->get_field_id('hide_avatar'); ?>"><?php echo yrp_i('Hide user avatars'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('dark_theme'); ?>" name="<?php echo $this->get_field_name('dark_theme'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $dark_theme); ?> />
            <label for="<?php echo $this->get_field_id('dark_theme'); ?>"><?php echo yrp_i('Dark theme'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="<?php echo $this->get_field_id('text_size'); ?>"><?php echo yrp_i('Review limit before \'read more\' link'); ?></label>
            <input id="<?php echo $this->get_field_id('text_size'); ?>" name="<?php echo $this->get_field_name('text_size'); ?>" value="<?php echo $text_size; ?>" class="form-control" type="text" placeholder="for instance: 120" />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <?php echo yrp_i('Widget theme'); ?>
            <select id="<?php echo $this->get_field_id('view_mode'); ?>" name="<?php echo $this->get_field_name('view_mode'); ?>" class="form-control">
                <option value="list" <?php selected('list', $view_mode); ?>><?php echo yrp_i('Review List'); ?></option>
                <option value="grid" <?php selected('grid', $view_mode); ?>><?php echo yrp_i('Reviews Grid'); ?></option>
                <option value="badge" <?php selected('badge', $view_mode); ?>><?php echo yrp_i('Yelp Badge: right'); ?></option>
                <option value="badge_left" <?php selected('badge_left', $view_mode); ?>><?php echo yrp_i('Yelp Badge: left'); ?></option>
                <option value="badge_inner" <?php selected('badge_inner', $view_mode); ?>><?php echo yrp_i('Yelp Badge: embed'); ?></option>
            </select>
        </div>
    </div>
    <?php if (isset($max_width)) { ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="<?php echo $this->get_field_id('max_width'); ?>"><?php echo yrp_i('Maximum width'); ?></label>
            <input id="<?php echo $this->get_field_id('max_width'); ?>" name="<?php echo $this->get_field_name('max_width'); ?>" class="form-control" type="text" placeholder="for instance: 300px" />
        </div>
    </div>
    <?php } ?>
    <?php if (isset($max_height)) { ?>
    <div class="form-group">
        <div class="col-sm-12">
            <label><?php echo yrp_i('Maximum height'); ?></label>
            <input id="<?php echo $this->get_field_id('max_height'); ?>" name="<?php echo $this->get_field_name('max_height'); ?>" class="form-control" type="text" placeholder="for instance: 500px" />
        </div>
    </div>
    <?php } ?>
</div>

<h4 class="rplg-options-toggle"><?php echo yrp_i('Advance Options'); ?></h4>
<div class="rplg-options" style="display:none">
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('open_link'); ?>" name="<?php echo $this->get_field_name('open_link'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $open_link); ?> />
            <label for="<?php echo $this->get_field_id('open_link'); ?>"><?php echo yrp_i('Open links in new Window'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input id="<?php echo $this->get_field_id('nofollow_link'); ?>" name="<?php echo $this->get_field_name('nofollow_link'); ?>" class="form-control" type="checkbox" value="1" <?php checked('1', $nofollow_link); ?> />
            <label for="<?php echo $this->get_field_id('nofollow_link'); ?>"><?php echo yrp_i('Use no follow links'); ?></label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('hide_float_badge'); ?>" name="<?php echo $this->get_field_name('hide_float_badge'); ?>" type="checkbox" value="1" <?php checked('1', $hide_float_badge); ?> class="form-control" />
                <?php echo yrp_i('Hide float badge on mobile'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label>
                <input id="<?php echo $this->get_field_id('lazy_load_img'); ?>" name="<?php echo $this->get_field_name('lazy_load_img'); ?>" type="checkbox" value="1" <?php checked('1', $lazy_load_img); ?> class="form-control" />
                <?php echo yrp_i('Lazy load images to improve performance'); ?>
            </label>
        </div>
    </div>
</div>