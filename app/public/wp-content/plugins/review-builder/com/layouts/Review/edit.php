<?php
	global $sgrb;
	$sgrb->includeStyle('page/styles/general/jquery-ui-dialog');
	$sgrb->includeScript('core/scripts/jquery-ui-dialog');
	$allTags = get_tags();
	$allTerms = get_categories();
?>
<style type="text/css">
	.sgrb-banner-wrapper {
		width: 99% !important;
	}
	.sgrb-banner-text {
		width: 57% !important;
	}
</style>
<div class="wrap">
	<div class="sgrb-container">
		<div class="sgrb-review-wrapper">
			<div class="row">
			<form role="form" class="sgrb-js-form">
				<section>
					<nav class="navbar-default stuckMenu isStuck" role="navigation" style="">
						<div class="container-fluida">
							<div class="navbar-header">
								<a class="navbar-brand" href="#home" style="font-family: 'Lato', Helvetica, Arial, sans-serif;pointer-events: none;"><?php echo (@$sgrbRev->getId() != 0) ? _e('Edit Review', 'sgrb') : _e('Add New Review', 'sgrb');?></a>
							</div>
							<div class="navbar-header">
								<div style="width:366px;overflow:hidden">
									<input class="sgrb-text-input sgrb-title-input sgrb-reviewupdate-js" value="<?php echo esc_attr(@$sgrbDataArray['title']); ?>" autofocus="" name="sgrb-title" placeholder="<?php _e('Enter title here', 'sgrb');?>" autocomplete="off" type="text">
									<?php if (!@$sgrbRev->getId()) :?>
										<div class="tooltip fade right in">
											<div class="tooltip-arrow"></div>
											<div class="tooltip-inner"> <?php _e('type review title', 'sgrb');?> </div>
										</div>
									<?php endif;?>
								</div>
							</div>
							<div class="collapse navbar-collapse navbar-right navbar-ex1-collapse">
								<ul class="nav navbar-nav list-inline" style="margin-bottom: 10px;">
									<li class="menuItem" style="margin: 0;"><button type="button" class="btn btn-default prev-step sgrb-previous-step disabled" style="margin-top: 15px;"><?php _e('Previous', 'sgrb');?></button></li>
									<li class="menuItem" style="margin: 0;"><button type="button" class="btn btn-default next-step sgrb-skip-step next-skip disabled" style="margin-top: 15px;"><?php _e('Skip', 'sgrb');?></button></li>
									<li class="menuItem" style="margin: 0;">
										<?php if ($sgrbRev->getId()) :?>
											<button onclick="SGReview.prototype.save();" type="button" class="sgrb-review-save-button btn btn-primary btn-info-full" style="margin-top: 15px;"><?php _e('Save', 'sgrb');?></button>
										<?php else :?>
											<button type="button" class="sgrb-review-save-button btn btn-primary btn-info-full next-step sgrb-next-step disabled" style="margin-top: 15px;"><?php _e('Continue', 'sgrb');?></button>
										<?php endif ;?>
									</li>
								</ul>
							</div>
						</div>
					</nav>
					<div class="wizard">
						<div class="wizard-inner">
							<div class="connecting-line"></div>
							<ul class="nav nav-tabs" role="tablist">

								<li id="sgrb-wizard-step-1" role="presentation" class="active">
									<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="<?php _e('Select Review Type', 'sgrb');?>">
										<span class="round-tab" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/typeSample.png'; ?>);background-repeat: no-repeat;background-size: 66px auto;">
											<i class=""></i>
										</span>
									</a>
								</li>

								<li id="sgrb-wizard-step-2" role="presentation" class="disabled">
									<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="<?php _e('Select Template', 'sgrb');?>">
										<span class="round-tab">
											<i class="glyphicon glyphicon-picture"></i>
										</span>
									</a>
								</li>
								<li id="sgrb-wizard-step-3" role="presentation" class="disabled">
									<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="<?php _e('Options', 'sgrb');?>">
										<span class="round-tab">
											<i class="glyphicon glyphicon-wrench"></i>
										</span>
									</a>
								</li>

								<li id="sgrb-wizard-step-4" role="presentation" class="disabled">
									<a href="#step4" data-toggle="tab" aria-controls="step4" role="tab" title="<?php _e('Rate options', 'sgrb');?>">
										<span class="round-tab">
											<i class="glyphicon glyphicon-cog"></i>
										</span>
									</a>
								</li>
								<li id="sgrb-wizard-step-5" role="presentation" class="disabled">
									<a href="#step5" data-toggle="tab" aria-controls="step5" role="tab" title="<?php _e('Localization', 'sgrb');?>">
										<span class="round-tab">
											<i class="glyphicon glyphicon-globe"></i>
										</span>
									</a>
								</li>
								<li id="sgrb-wizard-step-6" role="presentation" class="disabled">
									<a href="#step6" data-toggle="tab" aria-controls="step6" role="tab" title="Google SEO">
										<span class="round-tab" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/googleIcon.png'; ?>);background-repeat: no-repeat;background-size: 66px auto;">
											<i class=""></i>
										</span>
										<span class="round-tab sgrb-loading-spinner" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/wizard-save-spinner.gif';?>);background-repeat: no-repeat;background-size: 66px auto;display: none;">
											<i class=""></i>
										</span>
									</a>
								</li>
							</ul>
						</div>

							<input type="hidden" class="sgrb-wizard-current-page" value="1" autocomplete="off">
							<input name="rate-type-notice" class="sgrb-rate-type-notice" type="hidden" value="<?php echo esc_attr(@$sgrbDataArray['rate-type']) ;?>" autocomplete="off">
							<input type="hidden" name="review-type" class="sgrb-review-type" value="<?php echo (@$sgrbDataArray['review-type']) ? @$sgrbDataArray['review-type'] : '';?><?php echo (!@$sgrbDataArray['review-type'] && @$sgrbRev->getId() != 0) ? 2 : '';?>" autocomplete="off">
							<input type="hidden" class="sgrb-id" name="sgrb-id" value="<?php echo esc_attr(@$_GET['id']); ?>">
							<input type="hidden" name="sgrb-template" value="<?php echo isset($sgrbDataArray['template']) ? esc_attr($sgrbDataArray['template']) : 'full_width'; ?>">
							<div class="tab-content" style="border-top:4px solid rgb(84, 102, 120);border-bottom:none;border-left:1px solid rgb(84, 102, 120);border-right:1px solid rgb(84, 102, 120);background-color: rgba(250, 250, 250, 0.43);min-height: 500px;">
								<div class="tab-pane active" role="tabpanel" id="step1" style="overflow: hidden;">
									<div class="container-fluid">
										<div class="row" style="padding-left: 28px;">
											<div class="col-sm-4 block wow bounceIn animated animated" style="visibility: visible;margin:50px 0">
												<div class="row">
													<div class="col-md-4 box-icon rotate sgrb-review-type-selector<?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_SIMPLE) ? ' sgrb-selected-review-type' : '';?>" data-review-type="simple_review">
														<span class="dashicons dashicons-format-chat fa-4x" style="display: inline"></span>
													</div>
													<div class="col-md-6 box-ct">
														<h3> <?php _e('Simple review', 'sgrb');?> </h3>
														<p> <?php _e('Single feature to rate, text/image as a template', 'sgrb');?>. </p>
													</div>
												</div>
											</div>
											<div class="col-sm-4 block wow bounceIn animated animated" style="visibility: visible;margin:50px 0">
												<div class="row">
													<div class="col-md-4 box-icon rotate sgrb-review-type-selector<?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_PRODUCT || (!@$sgrbDataArray['review-type'] && @$sgrbRev->getId() != 0)) ? ' sgrb-selected-review-type' : '';?>" data-review-type="product_review">
														<span class="dashicons dashicons-cart fa-4x" style="display: inline"></span>
													</div>
													<div class="col-md-6 box-ct">
														<h3> <?php _e('Product review', 'sgrb');?> </h3>
														<p> <?php _e('Unlimited features to rate with a lot of templates', 'sgrb');?>. </p>
													</div>
												</div>
											</div>
											<div class="col-sm-4 block wow bounceIn animated animated" style="visibility: visible;margin:50px 0;">
												<?php if (!SGRB_PRO_VERSION) :?>
												<div style="position:relative;">
													<div class="sgrb-coming-soon">
														<a target="_blank" href="<?php echo SGRB_PRO_URL ;?>"><img src="<?php echo $sgrb->app_url.'assets/page/img/pro-ribbon.png'; ?>" width="100px"></a>
													</div>
												<?php endif;?>
													<div class="row">
														<div class="col-md-4 box-icon rotate sgrb-review-type-selector<?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_SOCIAL) ? ' sgrb-selected-review-type' : '';?>" data-review-type="social_review">
															<span class="dashicons dashicons-share fa-4x" style="display: inline"></span>
														</div>
														<div class="col-md-6 box-ct">
															<h3> <?php _e('Social comments', 'sgrb');?> </h3>
															<p> <?php _e('Replace our comments form with the traditional Facebook comments', 'sgrb');?>. </p>
														</div>
													</div>
												<?php echo (!SGRB_PRO_VERSION) ? '</div>' : ''?>
											</div>
										</div>
										<div class="row" style="padding-left: 28px;">
											<div class="col-sm-1"></div>
											<div class="col-sm-5  block wow bounceIn animated animated" style="visibility: visible;margin:20px 0">
												<?php if (!SGRB_PRO_VERSION) :?>
												<div style="position:relative;">
													<div class="sgrb-coming-soon">
														<a target="_blank" href="<?php echo SGRB_PRO_URL ;?>"><img src="<?php echo $sgrb->app_url.'assets/page/img/pro-ribbon.png'; ?>" width="100px"></a>
													</div>
												<?php endif;?>
													<div class="row">
														<div class="col-md-4 box-icon rotate sgrb-review-type-selector<?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_POST) ? ' sgrb-selected-review-type' : '';?>" data-review-type="post_review">
															<span class="dashicons dashicons-admin-post fa-4x" style="display: inline"></span>
														</div>
														<div class="col-md-7 box-ct">
															<h3> <?php _e('Post review', 'sgrb');?> </h3>
															<p> <?php _e('Unlimited features to rate Your post', 'sgrb');?>. </p>
														</div>
													</div>
												<?php echo (!SGRB_PRO_VERSION) ? '</div>' : ''?>
											</div>
											<div class="col-sm-5  block wow bounceIn animated animated" style="visibility: visible;margin:20px 0">
												<?php if (!SGRB_PRO_VERSION) :?>
												<div style="position:relative;">
													<div class="sgrb-coming-soon">
														<a target="_blank" href="<?php echo SGRB_PRO_URL ;?>"><img src="<?php echo $sgrb->app_url.'assets/page/img/pro-ribbon.png'; ?>" width="100px"></a>
													</div>
												<?php endif;?>
													<div class="row">
														<div class="col-md-4 box-icon rotate sgrb-review-type-selector<?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_WOO) ? ' sgrb-selected-review-type' : '';?>" data-review-type="woo_review">
															<img src="<?php echo $sgrb->app_url.'assets/page/img/woo.png';?>" width="110px" height="110px">
														</div>
														<div class="col-md-7 box-ct">
															<h3> <?php _e('WooCommerce review', 'sgrb');?> </h3>
															<p> <?php _e('WooCommerce review lets You get reviews with our plugin', 'sgrb');?>. </p>
														</div>
													</div>
												<?php echo (!SGRB_PRO_VERSION) ? '</div>' : ''?>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane" role="tabpanel" id="step2" style="overflow-x: hidden;">
									<?php require_once('templateSelectSection.php');?>
								</div>
								<div class="tab-pane" role="tabpanel" id="step3" style="margin: 10px 0;overflow-x: hidden;">
									<?php require_once('generalOptions.php');?>
								</div>
								<div class="tab-pane" role="tabpanel" id="step4" style="overflow-x: hidden;">
									<?php require_once('rateOptions.php');?>
								</div>
								<div class="tab-pane" role="tabpanel" id="step5" style="margin: 10px 40px;overflow-x: hidden;">
									<?php require_once('localization.php');?>
								</div>
								<div class="tab-pane" role="tabpanel" id="step6" style="margin: 10px 40px;overflow: hidden;"">
									<?php require_once('googleSearchPreviewOptions.php');?>
								</div>
								<input type="hidden" class="sgrbSaveUrl" value="<?php echo esc_attr($sgrbSaveUrl);?>">
								<div class="clearfix"></div>
							</div>
					</div>
					<nav class="navbar-default stuckMenu isStuck" role="navigation" style="">
						<div class="container-fluida">
							<div class="collapse navbar-collapse navbar-right navbar-ex1-collapse">
								<ul class="nav navbar-nav list-inline" style="margin-bottom: 10px;">
									<li class="menuItem" style="margin: 0;"><button type="button" class="btn btn-default prev-step sgrb-previous-step disabled" style="margin-top: 15px;"><?php _e('Previous', 'sgrb');?></button></li>
									<li class="menuItem" style="margin: 0;"><button type="button" class="btn btn-default next-step sgrb-skip-step next-skip disabled" style="margin-top: 15px;"><?php _e('Skip', 'sgrb');?></button></li>
									<li class="menuItem" style="margin: 0;">
										<?php if ($sgrbRev->getId()) :?>
											<button onclick="SGReview.prototype.save();" type="button" class="sgrb-review-save-button btn btn-primary btn-info-full" style="margin-top: 15px;"><?php _e('Save', 'sgrb');?></button>
										<?php else :?>
											<button type="button" class="sgrb-review-save-button btn btn-primary btn-info-full next-step sgrb-next-step disabled" style="margin-top: 15px;"><?php _e('Continue', 'sgrb');?></button>
										<?php endif ;?>
									</li>
								</ul>
							</div>
						</div>
					</nav>
				</section>
				</form>
			</div>
		</div>
</div>
</div>
<input type="hidden" class="sgrb-is-pro" value="<?php echo SGRB_PRO_VERSION;?>">
<div id="sgrb-template" title="<?php _e('Select template', 'sgrb');?>">
	<?php
	foreach($allTemplates as $template):
		$isChecked = ($template->getName() == @$sgrbDataArray['template']) ? ' checked' : '';
		$proHtml = '<div class="ribbon-wrapper" style="position:relative;display:block;"><div class="sgrb-ribbon"><div><a target="_blank" href="'.SGRB_PRO_URL.'" type="button" class="btn btn-danger">PRO</a></div></div></div>';
		if($template->getSgrb_pro_version()==1) $proHtml='';
	?>
	<?php if ($template->getName() != 'simple_review') :?>
		<label class="sgrb-template-label" style="max-height:150px;margin-bottom:5px;margin-bottom: 21px;">
			<?php if($template):?>
			<input type="radio" class="sgrb-radio" name="sgrb-template-radio" value="<?php echo $template->getName()?>"<?php echo esc_attr($isChecked);?>>
			<?php endif?>
			<?php echo $proHtml; ?>
			<?php if (!$template->getImg_url()):?>
				<div class="sgrb-custom-template-hilghlighting" style="position:absolute;color:#3F3F3F;margin-left:10px;z-index:9"><b><?=$template->getName()?></b></div>
				<img width="200px" src="<?php echo $sgrb->app_url.'assets/page/img/custom_template.jpeg'; ?>" style="max-height: 157px;">
			<?php else:?>
				<img class="sgrb-default-template-js" width="200px" src="<?php echo $template->getImg_url(); ?>" style="max-height: 157px;">
			<?php endif;?>
		</label>
	<?php endif; ?>
	<?php endforeach; ?>
</div>
