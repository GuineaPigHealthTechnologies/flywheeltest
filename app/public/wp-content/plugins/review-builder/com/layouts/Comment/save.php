<div class="wrap">
	<form class="sgrb-js-form">
		<div class="sgrb-top-bar">
			<h1 class="sgrb-add-edit-title">
				<?php echo (@$sgrbCommentId != 0) ? _e('Edit Comment', 'sgrb') : _e('Add New Comment', 'sgrb');?>
				<?php if (@$sgrbCommentId != 0) :?>
					<a href="<?php echo @esc_attr($createNewUrl);?>" class="page-title-action add-new-h2"><?php _e('Add new', 'sgrb'); ?></a>
				<?php endif ;?>
				<span class="sgrb-spinner-save-button-wrapper">
					<i class="sgrb-loading-spinner"><img src='<?php echo $sgrb->app_url.'assets/page/img/spinner-2x.gif';?>'></i>
					<a href="javascript:void(0)"
						class="sgrb-comment-js-update button-primary sgrb-pull-right"><?php _e('Save changes', 'sgrb'); ?></a>
				</span>
			</h1>
			<input class="sgrb-text-input sgrb-title-input" value="<?php echo esc_attr(@$sgrbDataArray['title']); ?>"
			type="text" autofocus name="title" placeholder="<?php _e('Enter title here', 'sgrb'); ?>">
			<input type="hidden" name="sgrb-id" value="<?php echo esc_attr(@$_GET['id']); ?>">
			<input type="hidden" name="sgrb-com-id" value="<?php echo esc_attr(@$_GET['id']); ?>">
			<input type="hidden" name="sgrbSaveUrl" value="<?php echo esc_attr(@$sgrbSaveUrl); ?>">
			<input type="hidden" name="sgrbAppUrl" value="<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>">
			<input type="hidden" name="sgrbProVersion" value="<?php echo SGRB_PRO_VERSION; ?>">
		</div>

		<div class="sgrb-options-main-wrapper">
		<div class="sg-row">
			<div class="sg-col-6">
				<div class="sg-box">
				<div class="sg-box-title">
					<?php _e('Comment options', 'sgrb');?>
				</div>
				<div class="sg-box-content">
					<div class="sgrb-options-wrapper-inner">
						<div class="sgrb-wrapping-options">
							<div class="sg-row">
								<div class="sgrb-options-row">
								<?php if (@$sgrbCommentId != 0) :?>
									<!-- if edit-->
									<div class="sg-col-3">
										<span class="sgrb-options-rows-span"><?php _e('Select review', 'sgrb');?>: </span>
									</div>
									<div class="sg-col-9">
										<input class="sgrb-review" type="hidden" name="review" value="<?php echo esc_attr(@$sgrbDataArray['review_id']);?>">
										<div class="sgrb-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
											<select class="sgrb-options-rows-input sgrb-select-review" disabled>
												<option value="<?php echo esc_attr($sgrbDataArray['review_id']);?>"><?php echo esc_html(@$sgrbDataArray['review-title']);?></option>
											</select>
										</div>
									</div>
								<?php else :?>
									<!-- if add-->
									<div class="sg-col-3">
										<span class="sgrb-options-rows-span"><?php _e('Select review', 'sgrb');?>: </span>
									</div>
									<div class="sg-col-9">
										<input class="sgrb-review" type="hidden" name="review" value="<?php echo esc_attr(@$sgrbDataArray['review_id']);?>">
										<div class="sgrb-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
											<select class="sgrb-options-rows-input sgrb-select-review">
												<option value="" selected><?php _e('Select review', 'sgrb');?></option>
											<?php foreach (@$allReviews as $review) :?>
												<option value="<?php echo esc_attr($review->getId());?>">
													<?php echo esc_attr(@$review->getTitle());?>
												</option>
											<?php endforeach;?>
											</select>
										</div>
									</div>
								<?php endif;?>
								</div>
							</div>
							<?php if (SGRB_PRO_VERSION) :?>
							<?php if (@$sgrbDataArray['post-category-id']) :?>
								<!-- if edit-->
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Select post category', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<input class="sgrb-review" type="hidden" name="post-category" value="<?php echo esc_attr(@$sgrbDataArray['post-category-id']);?>">
											<div class="sgrb-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
												<select class="sgrb-options-rows-input sgrb-select-post-category" name="post-category" disabled>
													<option value="<?php echo @$sgrbDataArray['post-category-id'];?>"><?php echo @$sgrbDataArray['post-category-title'];?></option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Select post', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<input class="sgrb-review" type="hidden" name="post" value="<?php echo esc_attr(@$sgrbDataArray['post-id']);?>">
											<div class="sgrb-selectbox-wrapper" style="background-image: url();">
												<select class="sgrb-options-rows-input sgrb-select-post" name="post" disabled>
													<option value="<?php echo @$sgrbDataArray['post-id'];?>"><?php echo @$sgrbDataArray['post-title'];?></option>
												</select>
											</div>
										</div>
									</div>
								</div>
							<?php else :?>
								<!-- if add-->
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Select post category', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<div class="sgrb-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
												<select class="sgrb-options-rows-input sgrb-select-post-category" name="post-category" disabled>
													<option value="<?php echo @$sgrbDataArray['post-category-id'];?>"><?php _e('Select post category', 'sgrb');?></option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Select post', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<div class="sgrb-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
												<select class="sgrb-options-rows-input sgrb-select-post" name="post" disabled>
													<option value="<?php echo @$sgrbDataArray['post-id'];?>"><?php _e('Select post', 'sgrb');?></option>
												</select>
											</div>
										</div>
									</div>
								</div>
							<?php endif ;?>
							<?php endif;?>

								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Name', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<input class="sgrb-options-rows-input sgrb-name" name="name" value="<?php echo esc_attr(@$sgrbDataArray['name']); ?>" type="text" placeholder="<?php _e('Name', 'sgrb');?>">
										</div>
									</div>
								</div>

								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Email', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<input class="sgrb-options-rows-input email" name="email" value="<?php echo esc_attr(@$sgrbDataArray['email']); ?>" type="email" placeholder="<?php _e('Email', 'sgrb');?>">
										</div>
									</div>
								</div>
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span class="sgrb-options-rows-span"><?php _e('Comment', 'sgrb');?>: </span>
										</div>

									</div>
								</div>
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-12">
											<textarea name="comment" class="sgrb-comment-textarea" placeholder="<?php _e('Type your text here', 'sgrb');?>"><?php echo esc_attr(@$sgrbDataArray['comment']); ?></textarea>
										</div>
									</div>
								</div>
								<div class="sg-row">
									<div class="sgrb-options-row">
										<div class="sg-col-3">
											<span style="vertical-align:sub;"><?php _e('Approved', 'sgrb');?>: </span>
										</div>
										<div class="sg-col-9">
											<div class="sgrb-approve-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
												<select name="isApproved" class="sgrb-approve-selectbox">
													<option value="0"<?php echo (@$sgrbDataArray['isApproved'] == 0) ? ' selected' : '';?>><?php _e('No', 'sgrb');?></option>
													<option value="1"<?php echo (@$sgrbDataArray['isApproved'] == 1 || @$sgrbCommentId == 0) ? ' selected' : '';?>><?php _e('Yes', 'sgrb');?></option>
												</select>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>

				</div>

			</div>

			<div class="sg-col-6">
				<div class="sg-box">
					<div class="sgrb-options-wrapper-inner">
					<div class="sg-box-title">
						<?php _e('Rate options', 'sgrb');?>
					</div>
					<div class="sg-box-content">
						<div class="sgrb-wrapping-options">
							<div class="sgrb-options-rows sgrb-ajax-load-categories">
							<i><img class="sgrb-comment-loader" src="<?php echo $sgrb->app_url;?>assets/page/img/spinner-2x.gif"></i>
								<?php foreach (@$sgrbDataArray['category'] as $category) :?>

									<?php foreach (@$sgrbDataArray['ratings'] as $rating) :?>
										<?php if ($category->getId() == $rating->getCategory_id()) :?>
											<div class="sgrb-category-row-wrapper">
												<span><?php _e('Category', 'sgrb');?>: </span>
												<div class="sgrb-category-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
													<select name="categories[]" class="sgrb-category">
														<option value="<?php echo esc_attr($category->getId());?>">
															<?php echo esc_attr($category->getName());?>
														</option>
													</select>
												</div>
												<?php if ($sgrbDataArray['ratingType'] == 'star') :?>
													<?php $count = 5;?>
												<?php elseif ($sgrbDataArray['ratingType'] == 'percent') :?>
													<?php $count = 100;?>
												<?php elseif ($sgrbDataArray['ratingType'] == 'point') :?>
													<?php $count = 10;?>
												<?php endif;?>
												<span><?php _e('Rate', 'sgrb');?> (1-<?php echo $count;?>) : </span>
												<div class="sgrb-rate-selectbox-wrapper" style="background-image: url(<?php echo $sgrb->app_url.'assets/page/img/arr.png';?>);">
													<select class="sgrb-each-rate-skin" name="rates[]">
														<?php for ($i=1;$i<=$count;$i++) :?>
															<option value="<?php echo $i;?>"<?php echo ($rating->getRate() == $i) ?  ' selected' : '';?>><?php echo $i;?></option>
														<?php endfor;?>
													</select>
												</div>
												<code class="sgrb-rate-type-code"><?php echo esc_attr(@$sgrbDataArray['ratingType']); ?></code>
											</div>
										<?php endif;?>
									<?php endforeach;?>

								<?php endforeach;?>

							</div>

						</div>
					</div>

					</div>
				</div>
			</div>



		</div>
		</div>

	</form>
</div>
