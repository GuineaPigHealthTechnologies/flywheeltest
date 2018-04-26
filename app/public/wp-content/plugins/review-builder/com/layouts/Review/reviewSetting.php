<?php
	$saveTables = get_option('SGRB_SAVE_TABLES');
	$checked = '';
	if ($saveTables) {
		$checked = ' checked';
	}
?>

<div class="sgrb-settings-wrapper">
	<h2>
		<?php _e('General settings', 'sgrb'); ?>
		<input type="button" class="sgrb-save-tables button-primary" value="<?php _e('Save changes', 'sgrb');?>">
	</h2>
	<form class="sgrb-js-settings-form">
		<div class="sg-row">
			<div class="sg-col-12">
				<p class="sgrb-review-setting-notice"><?php _e('Successfully saved', 'sgrb');?>.</p>
			</div>
		</div>
		<div class="sg-row">
			<div class="sg-col-12">
				<p><span><?php _e('Save review data', 'sgrb');?>: </span>
				<input type="checkbox" name="saveFreeTables" value="1"<?=$checked?>></p>
			</div>
		</div>
		<div class="sg-row sgrb-setting-row">
			<div class="sg-col-12">
				<span class="sgrb-review-setting-contact"><?php _e('Got something to say? Need help?', 'sgrb');?></span>
			</div>
			<div class="sg-col-12">
				<span class="sgrb-review-setting-contact"><?php _e('Contact Us', 'sgrb');?> <a href="mailto:wp-review@sygnoos.com" rel="nofollow">wp-review@sygnoos.com</a></span>
			</div>
		</div>
	</form>
</div>
