<div class="form-group">
    <div class="col-sm-12">
        <button class="fbrev-connect btn btn-small btn-block"><?php echo fbrp_i('Connect to Facebook'); ?></button>
    </div>
</div>

<div class="fbrev-pages"></div>

<?php global $wp_version; if (version_compare($wp_version, '3.5', '>=')) { wp_enqueue_media(); ?>
<div class="form-group">
    <div class="col-sm-12">
        <?php
        if ($page_photo) {
            $page_photo = $page_photo;
        } elseif ($page_id) {
            $page_photo = 'https://graph.facebook.com/' . $page_id . '/picture';
        } else {
            $page_photo = '';
        }
        ?>
        <img id="<?php echo $this->get_field_id('page_photo_img'); ?>" src="<?php echo $page_photo; ?>" style="display:<?php if ($page_photo) { ?>inline-block<?php } else { ?>none<?php } ?>;width:32px;height:32px;border-radius:50%;" class="fbrev-page-photo-img">
        <a id="<?php echo $this->get_field_id('page_photo_btn'); ?>" href="#" class="fbrev-page-photo-btn"><?php echo fbrp_i('Change page photo'); ?></a>
        <input type="hidden" id="<?php echo $this->get_field_id('page_photo'); ?>" name="<?php echo $this->get_field_name('page_photo'); ?>" value="<?php echo $page_photo; ?>" class="form-control fbrev-page-photo" tabindex="2"/>
    </div>
</div>
<?php } ?>

<div class="form-group">
    <div class="col-sm-12">
        <input type="text" id="<?php echo $this->get_field_id('page_name'); ?>" name="<?php echo $this->get_field_name('page_name'); ?>" value="<?php echo $page_name; ?>" class="form-control fbrev-page-name" placeholder="<?php echo fbrp_i('Page Name'); ?>" readonly />
    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <input type="text" id="<?php echo $this->get_field_id('page_id'); ?>" name="<?php echo $this->get_field_name('page_id'); ?>" value="<?php echo $page_id; ?>" class="form-control fbrev-page-id" placeholder="<?php echo fbrp_i('Page ID'); ?>" readonly />
    </div>
</div>

<input type="hidden" id="<?php echo $this->get_field_id('page_access_token'); ?>" name="<?php echo $this->get_field_name('page_access_token'); ?>" value="<?php echo $page_access_token; ?>" class="form-control fbrev-page-token" placeholder="<?php echo fbrp_i('Access token'); ?>" readonly />