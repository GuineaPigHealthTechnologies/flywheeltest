<div class="container" style="overflow:hidden">
<div class="sgrb-template-box"<?php echo (@$sgrbDataArray['review-type'] != SGRB_REVIEW_TYPE_PRODUCT && @$sgrbDataArray['review-type']) ? ' style="display: none;"' : '';?>>
	<strong><?php _e('Change template: ', 'sgrb'); ?></strong><span id="sgrb-template-name"><?php echo isset($sgrbDataArray['template']) ? esc_attr($sgrbDataArray['template']) : 'full_width'; ?></span>
	<input  class="sgrb-template-selector button-small button" type="button" value="<?php _e('Select template', 'sgrb')?>"/>
</div>
<div class="sgrb-main-template-wrapper"<?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_PRODUCT || (!@$sgrbDataArray['review-type'] && @$sgrbRev->getId())) ? '' : ' style="display: none;"';?>><?php echo (@$sgrbDataArray['template'] != 'post_review') ? @$res : '';?></div>

<!-- Simple review template -->
<div class="sgrb-simple-template-wrapper"<?php echo (!@$sgrbDataArray['review-type'] && @$sgrbRev->getId()) ? ' style="display: none;"' : '';?><?php echo (@$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_SIMPLE || @$sgrbDataArray['review-type'] == SGRB_REVIEW_TYPE_SOCIAL) ? '' : ' style="display: none;"';?>>
		<div class="row">
			<div class="col-md-12 text-center">
				<h2 class="section-heading"><?php _e('Select the subject of your review', 'sgrb');?></h2>
			</div>
		</div>
		<div class="row" style="margin-bottom: 10px;">
			<div class="col-md-4 text-right">
				<label for="sgrb-sample-template-type-empty">
					<?php _e('Empty (default)', 'sgrb');?>:
				</label>
			</div>
			<div class="col-md-1">
				<div class="sgrb-radio-wrapper">
					<input type="checkbox" id="sgrb-sample-template-type-empty" name="sgrb-sample-template-type-empty" class="form-control sgrb-sample-template-type sgrb-sample-template-default-value" value="empty"<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'empty' || @$sgrbRevId == 0) ? ' checked' : '';?> autocomplete="off">
					<label for="sgrb-sample-template-type-empty"></label>
				</div>
			</div>
			<div class="col-md-4">
				<span style="color:#6e6e6e;"> - review without any title, only comment form to show</span>
			</div>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			<div class="col-md-4 text-right">
				<label for="sgrb-sample-template-type-text">
					<?php _e('Text/shortcode as a template', 'sgrb');?>:
				</label>
			</div>
			<div class="col-md-1">
				<div class="sgrb-radio-wrapper">
					<input type="checkbox" id="sgrb-sample-template-type-text" name="sgrb-sample-template-type-text" class="form-control sgrb-sample-template-type" value="text"<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'text') ? ' checked' : '';?> autocomplete="off">
					<label for="sgrb-sample-template-type-text"></label>
				</div>
			</div>
			<div class="col-md-4">
				<input type="text" name="sgrb-sample-template-text" class="form-control sgrb-sample-template-text" style="margin-bottom: 0;" value="<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'text') ? @$sgrbDataArray['simple-review-template'] : '';?>">
			</div>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			<div class="col-md-4 text-right">
				<label for="sgrb-sample-template-type-image">
					<?php _e('Image as a template', 'sgrb');?>:
				</label>
			</div>
			<div class="col-md-1">
				<div class="sgrb-radio-wrapper">
					<input type="checkbox" id="sgrb-sample-template-type-image" name="sgrb-sample-template-type-image" class="form-control sgrb-sample-template-type" value="image"<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'image') ? ' checked' : '';?> autocomplete="off">
					<label for="sgrb-sample-template-type-image"></label>
				</div>
			</div>
			<div class="col-md-4">
				<div class="sg-tempo-image" style="<?php echo (@$sgrbDataArray['simple-review-template-type'] != 'image') ? 'border: 2px dashed #ccc;' : '';?>border-radius: 10px;height: 250px;margin-top:10px;background-image: url(<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'image') ? @$sgrbDataArray['simple-review-template'] : '';?>);background-repeat: no-repeat;background-position: center;">
					<div class="sgrb-image-review" style="margin:0;<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'image') ? 'background-image:url('.@$sgrbDataArray['simple-review-template'].');background-color:#f7f7f7;margin: 0 auto;': '';?>">
						<div class="sgrb-icon-wrapper" style="left: 34% !important;top: 35% !important;">
							<div class="sgrb-image-review-plus"><span class="sgrb-upload-btn" name="upload-btn-simple"><i><img class="sgrb-plus-icon" src="<?php echo $sgrb->app_url.'assets/page/img/add.png';?>"></i></span>
								<input type="hidden" class="sgrb-img-num" data-auto-id="">
								<input type="hidden" class="sgrb-images" id="sgrb_image_url_simple" name="simple_image_url" value="<?php echo (@$sgrbDataArray['simple-review-template-type'] == 'image') ? @$sgrbDataArray['simple-review-template'] : '';?>">
							</div>
							<div class="sgrb-image-review-minus">
								<span class="sgrb-remove-img-btn" name="remove-btn-simple">
									<i>
										<img class="sgrb-minus-icon" src="<?php echo $sgrb->app_url.'assets/page/img/remove_image.png';?>">
									</i>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- Social review template (star rating skins) -->

<!-- Post review template -->
<?php if (SGRB_PRO_VERSION) :?>
	<div class="sgrb-post-template-wrapper"<?php echo (@$sgrbDataArray['template'] == 'post_review') ? '' : ' style="display:none;"';?>>
		<div class="container-fluida">
			<div class="row">
				<div class="col-md-6 text-right">
					<span><?php _e('Select post category to show current review:', 'sgrb');?></span>
				</div>
				<div class="col-md-6">
					<div class="sgrb-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
						<select class="sgrb-post-category" name="post-category">
							<?php foreach ($allTerms as $postCategory) :?>
								<option value="<?php echo $postCategory->term_id;?>"<?php echo (@$sgrbDataArray['post-category'] == $postCategory->term_id) ? ' selected': '';?>><?php echo $postCategory->name?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 text-right">
					<label for="sgrb-disable-wp-comments"><?php _e('Disable Wordpress default comments', 'sgrb');?>:</label>
				</div>
				<div class="col-md-1">
						<div class="sgrb-checkbox-wrapper">
							<input id="sgrb-disable-wp-comments" value="true" type="checkbox" name="disableWPcomments"<?php echo (@$sgrbDataArray['disable-wp-comments']) ? ' checked' : '';?>>
							<label class="sgrb-checkbox-label" for="sgrb-disable-wp-comments"></label>
						</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 text-right">
					<p class="sgrb-type-warning"><?php _e('This review will only be shown in posts with selected category', 'sgrb');?></p>
				</div>
			</div>
		</div>
	</div>
	<!-- end-->

	<!-- WooCommerce review template -->
	<div class="sgrb-woo-template-wrapper"<?php echo (@$sgrbDataArray['template'] == 'woo_review') ? '' : ' style="display:none;"';?>>
		<div class="container-fluida">
			<div class="row sgrb-disable-woo-comments-wrapper">
				<div class="col-md-1">
					<div class="sgrb-checkbox-wrapper">
						<input id="sgrb-disable-woo-comments" value="true" type="checkbox" name="disable-woo-comments"<?php echo (@$sgrbDataArray['disable-woo-comments']) ? ' checked' : '';?>>
						<label for="sgrb-disable-woo-comments"></label>
					</div>
				</div>
				<div class="col-md-5">
					<label for="sgrb-disable-woo-comments">
						 <?php _e('Disable WooCommerce products default reviews and comments', 'sgrb');?>
					</label>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<p class="sgrb-type-warning"><?php _e('Note: if the product has more than one review, priority will be given to the review attached directly to the product.', 'sgrb');?></p>
				</div>
			</div>
			<div class="row" style="margin: 10px 0;">
				<div class="col-md-5 text-right">
					<label for="wooReviewShowType">
						<?php _e('Show review on products with selected categories', 'sgrb');?>:
					</label>
				</div>
				<div class="col-md-1">
					<div class="sgrb-radio-wrapper">
						<input id="wooReviewShowType" type="checkbox" value="showByCategory" name="wooReviewShowType"<?php echo (@$sgrbDataArray['wooReviewShowType'] == 'showByCategory' || !@$_GET['id']) ? ' checked' : '' ;?>>
						<label for="wooReviewShowType"></label>
					</div>
				</div>
				<div class="col-md-6">
					<div class="container sgrb-woo-categories-select-all"<?php echo (@$sgrbDataArray['wooReviewShowType'] != 'showByCategory') ? ' style="display:none;"' : '' ;?>>
						<div class="row">
							<div class="col-md-4">
								<div class="sgrb-checkbox-wrapper">
									<input type="checkbox" class="sgrb-select-all-categories" id="sgrb-select-all-categories-checkbox">
									<label for="sgrb-select-all-categories-checkbox"></label>
								</div>
							</div>
						</div>
					</div>
					<div class="container sgrb-woo-category-wrapper"<?php echo (@$sgrbDataArray['wooReviewShowType'] != 'showByCategory') ? ' style="display:none;"' : '' ;?>>
						<?php if (empty($termsArray)):?>
							<div class="sgrb-each-category-wrapper">
								<span class="sgrb-woo-product-category-name"><?php _e('No product categories found', 'sgrb');?></span>
							</div>
						<?php else :?>
							<?php for ($i=0;$i<count(@$termsArray['id']);$i++) :?>
								<?php $categoryClass = 'sgrb-selected-categories';?>
								<div class="sgrb-each-category-wrapper">
									<div class="row">
										<div class="col-md-1">
										<?php for ($j=0;$j<count(@$sgrbDataArray['woo-category']);$j++) :?>
											<?php $checked = '';?>
											<?php $disabled = '';?>

											<?php $categoryClass = 'sgrb-selected-categories';?>
											<?php if (@$termsArray['id'][$i] == @$sgrbDataArray['woo-category'][$j]) :?>
												<?php $checked = ' checked';?>
												<?php break;?>
											<?php endif;?>
										<?php endfor;?>
										<?php for ($k=0;$k<count(@$matchesCategories['id']);$k++) :?>
											<?php $matchReview = '';?>
											<?php $disabled = '';?>
											<?php if (@$matchesCategories['id'][$k] == @$termsArray['id'][$i]) :?>
												<?php $checked = '';?>
												<?php $disabled = ' disabled';?>
												<?php $categoryClass = '';?>
												<?php $matchReview = ' - <i class="sgrb-is-used">used in </i> '.@$matchesCategories['review'][$k].'<i class="sgrb-is-used"> review</i>';?>
												<?php break;?>
											<?php endif;?>
										<?php endfor;?>
											<div class="sgrb-checkbox-wrapper">
												<input class="sgrb-woo-category <?=$categoryClass?>" id="sgrb-woo-category-<?=@$termsArray['id'][$i];?>" type="checkbox" value="<?php echo @$termsArray['id'][$i];?>" <?php echo @$checked.' '.@$disabled;?>>
												<label for="sgrb-woo-category-<?=$termsArray['id'][$i];?>"></label>
											</div>
										</div>
										<div class="col-md-10">
											<label for="sgrb-woo-category-<?=$termsArray['id'][$i];?>">
												<span class="sgrb-woo-product-category-name"><?php echo @$termsArray['name'][$i]?><?php echo @$matchReview?></span>
											</label>
										</div>
									</div>
								</div>
							<?php endfor;?>
						<?php endif;?>
					</div>
				</div>
			</div>
			<div class="row" style="margin: 10px 0;">
				<div class="col-md-5 text-right">
					<label for="sgrbWooReviewShowTypeProduct"><?php _e('Show review on selected products', 'sgrb');?></label>
				</div>
				<div class="col-md-1">
					<div class="sgrb-radio-wrapper">
						<input id="sgrbWooReviewShowTypeProduct" type="checkbox" value="showByProduct" name="wooReviewShowType"<?php echo (@$sgrbDataArray['wooReviewShowType'] == 'showByProduct') ? ' checked' : '' ;?>>
						<label for="sgrbWooReviewShowTypeProduct"></label>
					</div>
				</div>
				<div class="col-md-6">
					<div class="container sgrb-woo-products-select-all"<?php echo (@$sgrbDataArray['wooReviewShowType'] != 'showByProduct') ? ' style="display:none;"' : '' ;?>>
						<div class="row">
							<div class="col-md-4">
								<div class="sgrb-select-checkbox">
									<div class="sgrb-checkbox-wrapper">
										<input type="checkbox" class="sgrb-select-all-products" id="sgrb-select-all-products-checkbox">
										<label for="sgrb-select-all-products-checkbox"></label>
									</div>
								</div>
							</div>
							<div class="col-md-8">
								<div class="row" style="padding-top:7px">
									<div class="col-md-9 text-right">
										<?php _e('Products to load:', 'sgrb');?>
									</div>
									<div class="col-md-3">
										<input name="productsToLoad" maxlength="3" value="500" type="text" style="width: 100%;">
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="container sgrb-woo-products-wrapper"<?php echo (@$sgrbDataArray['wooReviewShowType'] != 'showByProduct') ? ' style="display:none;"' : '' ;?>>
						<div class="sgrb-load-more-woo">
							<input class="button-small button sgrb-products-selector" value="<?php _e('Loading...', 'sgrb');?>" type="button">
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" class="sgrb-all-products-categories" name="all-products-categories" value='<?php echo @$sgrbDataArray['woo-products'];?>'>
			<input type="hidden" class="sgrb-all-products-count" name="allProductsCount" value="<?php echo @$allProductsCount;?>">
		</div>
	</div>
	<!-- end-->
<?php endif ;?>
</div>
