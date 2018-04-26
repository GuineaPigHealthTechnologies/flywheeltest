<div id="wpstg-clonepage-wrapper">

    <?php require_once($this->path . "views/_includes/header.php") ?>

    <?php
    do_action("wpstg_notifications");

    // Multi site
    if (is_multisite()) {
        require_once($this->path . "views/clone/multi-site/index.php");
    }
    else if (false !== get_option('wpstg_is_staging_site') && 'false' !== get_option('wpstg_is_staging_site')) {
        // Staging site
        require_once($this->path . "views/clone/stagingsite/index.php");
    } else {
        // Single site
        require_once($this->path . "views/clone/single-site/index.php");
    }

    // Footer
    require_once($this->path . "views/clone/includes/footer.php");
    ?>
</div>