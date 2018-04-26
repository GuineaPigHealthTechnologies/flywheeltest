<div class="wpstg-tabs-wrapper">
    <a href="#" class="wpstg-tab-header active" data-id="#wpstg-scanning-db" style="display:block;">
        <span class="wpstg-tab-triangle">&#9658;</span>
        <?php echo __("DB Tables", "wpstg")?>
    </a>

    <div class="wpstg-tab-section" id="wpstg-scanning-db">
        <?php do_action("wpstg_scanning_db")?>
        <h4 style="margin:0">
            <?php echo __(
                "Select tables to push",
                "wpstg"
            )?>
        </h4>
            <a href="#" class="wpstg-button-unselect">
                Un-check All
            </a>
        <?php
        if (isset($options->tables)){
        foreach ($options->tables as $table):
            $attributes = in_array($table->name, $options->excludedTables) ? '' : "checked";
            $attributes .= in_array($table->name, $options->clonedTables) ? " disabled" : '';
            ?>
            <div class="wpstg-db-table">
                <label>
                    <input class="wpstg-db-table-checkboxes" type="checkbox" name="<?php echo $table->name?>" <?php echo $attributes?>>
                    <?php echo $table->name?>
                </label>
                <span class="wpstg-size-info">
				<?php echo $scan->formatSize($table->size)?>
			</span>
            </div>
        <?php endforeach; 
            }
        ?>
        <div>
            <a href="#" class="wpstg-button-unselect">
                Un-check All
            </a>
        </div>
    </div>

    <a href="#" class="wpstg-tab-header" data-id="#wpstg-scanning-files">
        <span class="wpstg-tab-triangle">&#9658;</span>
        <?php echo __("Select Files", "wpstg")?>
    </a>

    <div class="wpstg-tab-section" id="wpstg-scanning-files">
        <h4 style="margin:10px 0 10px 0">
            <?php echo __("Select plugins, themes & uploads folder to push to live site.", "wpstg")?>
        </h4>

        <?php echo $scan->directoryListing()?>

<!--        <h4 style="margin:10px 0 10px 0">
            <?php //echo __("Extra directories to copy", "wpstg")?>
        </h4>-->

<!--        <textarea id="wpstg_extraDirectories" name="wpstg_extraDirectories" style="width:100%;height:250px;"></textarea>
        <p>
            <span>
                <?php
                echo __(
                    "Enter one folder path per line.<br>".
                    "Folders must start with absolute path: " . $options->root,
                    "wpstg"
                )
                ?>
            </span>
        </p>-->

        <p>
            <span>
                <?php
                if (isset($options->clone)){
                  echo __("Plugin files will be pushed to: ", "wpstg") . $options->root . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' ;
                  echo '<br>';
                  echo __("Theme files will be pushed to: ", "wpstg") . $options->root . 'wp-content' . DIRECTORY_SEPARATOR . 'themes' ;
                  echo '<br>';
                  echo __("Media files will be pushed to: ", "wpstg") . $options->root . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' ;
                }
                ?>
            </span>
        </p>
    </div>

<!--    <a href="#" class="wpstg-tab-header" data-id="#wpstg-advanced-settings">
        <span class="wpstg-tab-triangle">&#9658;</span>
        <?php //echo __("Login Options", "wpstg")?>
    </a>-->

<!--    <div class="wpstg-tab-section" id="wpstg-advanced-settings">
        Coming Soon...
    </div>-->

</div>

<button type="button" class="wpstg-prev-step-link wpstg-link-btn button-primary">
    <?php _e("Back", "wpstg")?>
</button>

<button type="button" id="wpstg-push-changes" class="wpstg-next-step-link-pro wpstg-link-btn button-primary" data-action="wpstg_push_changes" data-clone="<?php echo $options->current; ?>">
    <?php
   echo __('Push to Live Site', 'wpstg');
    ?>
</button>

    <?php echo __("<strong>Note:</strong> If you push all database tables you may have to login again.", "wpstg")?>

<!--<span class="wpstg-loader" style="display:none;"></span>//-->
<div id="wpstg-error-wrapper">
    <div id="wpstg-error-details"></div>
</div>

<div id="wpstg-log-details" style="display: none;"></div>