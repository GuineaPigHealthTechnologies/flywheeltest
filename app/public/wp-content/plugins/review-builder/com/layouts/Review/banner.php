<style type="text/css">
	.sgrb-banner-wrapper {
		color: #ffffff !important;
		background-color: #00abc7 !important;
		border-left: 4px solid #ffffff !important;
		display: inline-block !important;
		margin: 0 !important;
		padding: 0 0 0 16px !important;
		width: 97% !important;
	}
	.sgrb-banner-wrapper i,
	.sgrb-banner-wrapper h1 {
		padding: 0 !important;
		font-size: 26px !important;
		font-weight: 18px !important;
		line-height: 18px !important;
	}
	.sgrb-banner-wrapper h1 {
		margin: 17px 0 !important;
	}
	#sgrb-banner .notice-dismiss:before {
		color: #0073AA !important;
	}
	#sgrb-banner .notice-dismiss:hover:before {
		color: #ffffff !important;
	}
	.sgrb-banner-image {
		padding-top: 17px !important;
		padding-left: 17px !important;
		width: 24% !important;
		float: left !important;
	}
	.sgrb-banner-text {
		width: 54% !important;
		float: right !important;
	}
	.sgrb-banner-text h1 {
		color: #efeded !important;
	}
	.sgrb-banner-review-link {
		text-decoration: none;
		color: #ffffff !important;
	}
	.sgrb-banner-review-link:hover {
		text-decoration: underline;
	}
	.sgrb-banner-hide-link {
		color: #0073AA !important;
		font-size: 13px !important;
		text-decoration: none;
		padding-right: 9px;
	}
	.sgrb-banner-hide-link:hover {
		color: #ffffff !important;
	}

</style>
<?php
	global $sgrb;
?>
<div id="sgrb-banner" class="sgrb-banner-wrapper updated notice notice-success is-dismissible below-h2">
	<div style="width:18%;float:left;">
		<h1 style="color:#fff;padding:17px 0 !important;">REVIEW BUILDER</h1>
	</div>
	<div class="sgrb-banner-image">
		<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="50px">
		<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="50px">
		<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="50px">
		<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="50px">
		<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="50px">
	</div>
	<div class="sgrb-banner-text">
		<h1>
			<b style="color: #ffffff">Review Builder</b> <?php _e('helped you earn 5 stars for your reviews','sgrb');?>
		</h1>
		<h1>
			<?php _e('We\'ll be very thankful if you','sgrb');?> <a target="_blank" href="https://wordpress.org/support/plugin/review-builder/reviews/" class="sgrb-banner-review-link">leave one good one for us!</a>
		</h1>
		<i style="float: right;margin-top: -14px;"><a href="#" onclick="SGMainHelper.prototype.ajaxCloseBanner()" class="sgrb-banner-hide-link"><?php _e('Don\'t show again','sgrb');?></a></i>
	</div>
</div>
