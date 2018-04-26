<div class="wrap">
	<div class="sgrb-container">
	<h2>
		<?php _e('Comments', 'sgrb'); ?>
		<a href="<?php echo @esc_attr($createNewUrl);?>" class="btn page-title-action add-new-h2"><?php _e('Add new', 'sgrb'); ?></a>

		<?php if (!SGRB_PRO_VERSION) : ?>
			<a target="_blank" href="<?php echo SGRB_PRO_URL ;?>" style="float:right;"><img src="<?php echo $sgrb->app_url.'assets/page/img/long-ribbon.png'; ?>" width="500px"></a>
		<?php endif;?>
	</h2>

	<?php echo @esc_attr($comment); ?>
	</div>
</div>
