<div class="wrap">
	<form class="sgrb-js-form">
		<div class="sgrb-top-bar">
			<h1 class="sgrb-add-edit-title">
				<?php _e('Comment box settings', 'sgrb'); ?>
				<?php if (SGRB_PRO_VERSION) :?>
				<span class="sgrb-spinner-save-button-wrapper">
					<i class="sgrb-loading-spinner"><img src='<?php echo $sgrb->app_url.'assets/page/img/spinner-2x.gif';?>'></i>
					<a href="javascript:void(0)"
						class="sgrb-comment-form-js-update button-primary sgrb-pull-right"><?php _e('Save changes', 'sgrb'); ?></a>
				</span>
				<?php endif ;?>
			</h1>
			<input type="hidden" name="sgrbSaveUrl" value="<?php echo esc_attr(@$sgrbSaveUrl); ?>">

		</div>

		<?php if (!SGRB_PRO_VERSION) :?>
		<div class="sgrb-pro-options-wrapper">
			<div class="sgrb-coming-soon">
				<a class="sgrb-pull-right" target="_blank" href="<?php echo SGRB_PRO_URL ;?>"><img src="<?php echo $sgrb->app_url.'assets/page/img/long-ribbon.png'; ?>" width="400px"></a>
			</div>
		<?php endif;?>
		<div class="sgrb-form-options-main-wrapper"<?php echo (!SGRB_PRO_VERSION) ? ' style="opacity: 0.8;"': '';?>>
			<div class="sg-row">
				<div class="sg-col-12">
					<div class="sg-box sgrb-form-general-box">
						<div class="sg-box-title">
							<?php _e('Comment box settings', 'sgrb');?>
						</div>
						<div class="sg-box-content">
							<div class="sg-row sgrb-comment-box-theme-row">
								<div class="sg-col-1">
									<div class="sgrb-comment-box-theme-radio">
										<label class="sgrb-comment-box-theme-label" for="sgrb-comment-box-theme-1"><?php _e('Default:', 'sgrb');?></label>
									</div>
								</div>
								<div class="sg-col-1">
									<div class="sgrb-comment-box-theme-radio">
										<div class="sgrb-radio-wrapper">
											<input value="1" id="sgrb-comment-box-theme-1" name="comment-box-theme" class="sgrb-comment-box-type sgrb-default-comment-box-type" checked="" autocomplete="off" type="checkbox">
											<label for="sgrb-comment-box-theme-1"></label>
										</div>
									</div>
								</div>
								<div class="sg-col-10 sgrb-form-textareas-wrapper">
									<div class="sgrb-comment-box-main-wrapper">

										<div class="sgrb-rate-setting">
											<div class="sg-col-5">
												<div class="sgrb-checkbox-wrapper">
													<input value="true" id="sgrb-comment-box-rate-show" name="comment-box-rate-show"<?php echo (@$options['comment-box-rate-show']) ? ' checked': '' ;?> type="checkbox">
													<label class="sgrb-checkbox-label" for="sgrb-comment-box-rate-show"></label>
												</div>
												<label for="sgrb-comment-box-rate-show"> - <?php _e('show rate', 'sgrb');?></label>
											</div>
											<div class="sgrb-rate-box-disable-js<?php echo (!@$options['comment-box-rate-show']) ? ' sgrb-disabled': '' ;?>">
												<div class="sg-col-3">
													<span class="sgrb-align-title"><?php _e('Alignment', 'sgrb');?> : </span>
													<div class="sgrb-radio-wrapper">
														<input value="left" id="sgrb-comment-box-rate-alignment-left" class="comment-box-rate-alignment" name="comment-box-rate-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-rate-alignment'] == 'left') ? ' checked' : '';?>>
														<label for="sgrb-comment-box-rate-alignment-left"></label>
													</div>
													<label for="sgrb-comment-box-rate-alignment-left"> - <?php _e('left', 'sgrb');?></label>
												</div>
												<div class="sg-col-2">
													<div class="sgrb-radio-wrapper">
														<input value="center" id="sgrb-comment-box-rate-alignment-center" class="comment-box-rate-alignment" name="comment-box-rate-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-rate-alignment'] == 'center') ? ' checked' : '';?>>
														<label for="sgrb-comment-box-rate-alignment-center"></label>
													</div>
													<label for="sgrb-comment-box-rate-alignment-center"> - <?php _e('center', 'sgrb');?></label>
												</div>
												<div class="sg-col-2">
													<div class="sgrb-radio-wrapper">
														<input value="right" id="sgrb-comment-box-rate-alignment-right" class="comment-box-rate-alignment" name="comment-box-rate-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-rate-alignment'] == 'right') ? ' checked' : '';?>>
														<label for="sgrb-comment-box-rate-alignment-right"></label>
													</div>
													<label for="sgrb-comment-box-rate-alignment-right"> - <?php _e('right', 'sgrb');?></label>
												</div>
											</div>
										</div>

										<div class="sgrb-each-rate-box sgrb-comment-box-stars-hide-show-js">
											<div class="sgrb-col-3">
												<div class="sgrb-comment-box-rate<?php echo (!@$options['comment-box-rate-show']) ? ' sgrb-disabled': '' ;?>" style="text-align:<?php echo (@$options['comment-box-rate-alignment']) ? @$options['comment-box-rate-alignment'] : 'right';?>">
													<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="35px">
													<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="35px">
													<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="35px">
													<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="35px">
													<img src="<?php echo $sgrb->app_url.'assets/page/img/star.png'?>" width="35px">
												</div>
											</div>
										</div>

										<div class="sgrb-rate-setting">
											<div class="sg-col-5">
												<div class="sgrb-checkbox-wrapper">
													<input value="true" id="sgrb-comment-box-title-show" name="comment-box-title-show" class="sgrb-email-hide-show-js" type="checkbox"<?php echo (@$options['comment-box-title-show']) ? ' checked': '' ;?>>
													<label class="sgrb-checkbox-label" for="sgrb-comment-box-title-show"></label>
												</div>
												<label for="sgrb-comment-box-title-show"> - <?php _e('show title', 'sgrb');?></label>
											</div>
											<div class="sgrb-title-box-disable-js<?php echo (!@$options['comment-box-title-show']) ? ' sgrb-disabled': '' ;?>">
												<div class="sg-col-3">
													<span class="sgrb-align-title"><?php _e('Alignment', 'sgrb');?> : </span>
													<div class="sgrb-radio-wrapper">
														<input value="left" id="sgrb-comment-box-title-alignment-left" class="comment-box-title-alignment" name="comment-box-title-alignment"<?php echo (@$options['comment-box-title-alignment'] == 'left') ? ' checked': '' ;?> autocomplete="off" type="checkbox">
														<label for="sgrb-comment-box-title-alignment-left"></label>
													</div>
													<label for="sgrb-comment-box-title-alignment-left"> - <?php _e('left', 'sgrb');?></label>
												</div>
												<div class="sg-col-2">
													<div class="sgrb-radio-wrapper">
														<input value="center" id="sgrb-comment-box-title-alignment-center" class="comment-box-title-alignment" name="comment-box-title-alignment"<?php echo (@$options['comment-box-title-alignment'] == 'center') ? ' checked': '' ;?> autocomplete="off" type="checkbox">
														<label for="sgrb-comment-box-title-alignment-center"></label>
													</div>
													<label for="sgrb-comment-box-title-alignment-center"> - <?php _e('center', 'sgrb');?></label>
												</div>
												<div class="sg-col-2">
													<div class="sgrb-radio-wrapper">
														<input value="right" id="sgrb-comment-box-title-alignment-right" class="comment-box-title-alignment" name="comment-box-title-alignment" <?php echo (@$options['comment-box-title-alignment'] == 'right') ? ' checked': '' ;?> autocomplete="off" type="checkbox">
														<label for="sgrb-comment-box-title-alignment-right"></label>
													</div>
													<label for="sgrb-comment-box-title-alignment-right"> - <?php _e('right', 'sgrb');?></label>
												</div>
											</div>
										</div>

										<div class="sgrb-comment-title-box sgrb-comment-box-title-hide-show-js">
											<div class="sgrb-col-3">
												<div class="sgrb-comment-box-title" style="text-align:<?php echo (@$options['comment-box-title-alignment']) ? @$options['comment-box-title-alignment'] : 'left';?>">
													<span class="<?php echo (!@$options['comment-box-title-show']) ? ' sgrb-disabled': '' ;?>"<?php echo (!SGRB_PRO_VERSION) ? ' style="opacity: 0.6;"': '';?>>
														<?php _e('TITLE', 'sgrb');?>
													</span>
												</div>
											</div>
										</div>
										<div class="sgrb-comment-avatar-box">
											<div class="sgrb-rate-setting">
												<div class="sg-col-5">
													<div class="sgrb-checkbox-wrapper">
														<input value="true" id="sgrb-comment-box-avatar-show" name="comment-box-avatar" type="checkbox"<?php echo (@$options['comment-box-avatar']) ? ' checked' : '' ;?>>
														<label class="sgrb-comment-box-avatar-show" for="sgrb-comment-box-image-show"></label>
													</div>
													<label for="sgrb-comment-box-avatar-show"> - <?php _e('show image', 'sgrb');?></label>
													<div class="sgrb-checkbox-wrapper">
														<input value="true" id="sgrb-comment-box-text-show" name="comment-box-text" type="checkbox"<?php echo (@$options['comment-box-text']) ? ' checked' : '' ;?>>
														<label class="sgrb-comment-box-text-show" for="sgrb-comment-box-text-show"></label>
													</div>
													<label for="sgrb-comment-box-text-show"> - <?php _e('show text', 'sgrb');?></label>
												</div>
												<div class="sgrb-avatar-text-disable-js<?php echo (!@$options['comment-box-avatar'] || !@$options['comment-box-text']) ? ' sgrb-disabled' : '';?>">
													<div class="sg-col-3">
														<span class="sgrb-align-title"><?php _e('Alignment', 'sgrb');?> : </span>
														<div class="sgrb-radio-wrapper">
															<input value="1" id="sgrb-comment-box-avatar-and-text-alignment-default" class="comment-box-avatar-and-text-alignment" name="comment-box-avatar-and-text-alignment" autocomplete="off" type="checkbox"<?php echo (!@$options['comment-box-avatar-and-text-alignment'] || @$options['comment-box-avatar-and-text-alignment'] == 1) ? ' checked' : '' ;?>>
															<label for="sgrb-comment-box-avatar-and-text-alignment-default"></label>
														</div>
														<label for="sgrb-comment-box-avatar-and-text-alignment-default"> - <?php _e('default', 'sgrb');?></label>
													</div>
													<div class="sg-col-2">
														<div class="sgrb-radio-wrapper">
															<input value="2" id="sgrb-comment-box-avatar-and-text-alignment-reverse" class="comment-box-avatar-and-text-alignment" name="comment-box-avatar-and-text-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-avatar-and-text-alignment'] == 2) ? ' checked' : '' ;?>>
															<label for="sgrb-comment-box-avatar-and-text-alignment-reverse"></label>
														</div>
														<label for="sgrb-comment-box-avatar-and-text-alignment-reverse"> - <?php _e('reverse', 'sgrb');?></label>
													</div>
												</div>
											</div>
											<div class="sgrb-user-avatar-wrapper sgrb-avatar-disable-js<?php echo (!@$options['comment-box-avatar']) ? ' sgrb-disabled' : '';?>" style="<?php echo (!SGRB_PRO_VERSION) ? 'opacity: 0.6;': '';?><?php echo (@$options['comment-box-avatar-and-text-alignment'] == 1) ? 'float: left;' : 'float: right;';?>">
												<img src="<?php echo $sgrb->app_url.'assets/page/img/avatar.png' ;?>" width="100px" height="100px">
											</div>
											<div class="sgrb-comment-text-wrapper sgrb-comment-text-disable-js<?php echo (!@$options['comment-box-text']) ? ' sgrb-disabled' : '';?>" style="<?php echo (!SGRB_PRO_VERSION) ? 'opacity: 0.6;': '';?><?php echo (@$options['comment-box-avatar-and-text-alignment'] != 1) ? 'float: right;' : 'float: left;';?>">
												<p><?php _e("comment text here ...
													Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
													when an unknown printer took a galley of type and scrambled it to make a type specimen book.
													It has survived not only five centuries.", 'sgrb');?>
												</p>
											</div>
										</div>

										<div class="sgrb-rate-setting">
											<div class="sg-col-5">
												<div class="sgrb-checkbox-wrapper">
													<input value="true" id="sgrb-comment-box-date-show" name="comment-box-date-show" type="checkbox"<?php echo (@$options['comment-box-date-show']) ? ' checked' : '';?>>
													<label class="sgrb-checkbox-label" for="sgrb-comment-box-date-show"></label>
												</div>
												<label for="sgrb-comment-box-date-show"> - <?php _e('show date', 'sgrb');?></label>
												<div class="sgrb-checkbox-wrapper">
													<input value="true" id="sgrb-comment-box-comment-by-show" name="comment-box-comment-by-show"<?php echo (@$options['comment-box-comment-by-show']) ? ' checked' : '';?> type="checkbox">
													<label class="sgrb-checkbox-label" for="sgrb-comment-box-comment-by-show"></label>
												</div>
												<label for="sgrb-comment-box-comment-by-show"> - <?php _e('show comment by', 'sgrb');?></label>
											</div>
											<div class="sgrb-date-comment-by-disable-js<?php echo (!@$options['comment-box-comment-by-show'] && !@$options['comment-box-date-show']) ? ' sgrb-disabled' : '';?>">
												<div class="sg-col-3">
													<span class="sgrb-align-title"><?php _e('Alignment', 'sgrb');?> : </span>
													<div class="sgrb-radio-wrapper">
														<input value="left" id="sgrb-comment-box-date-left" class="comment-box-date-alignment" name="comment-box-date-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-date-alignment'] == 'left') ? ' checked' : '';?>>
														<label for="sgrb-comment-box-date-left"></label>
													</div>
													<label for="sgrb-comment-box-date-left"> - <?php _e('left', 'sgrb');?></label>
												</div>
												<div class="sg-col-2">
													<div class="sgrb-radio-wrapper">
														<input value="center" id="sgrb-comment-box-date-center" class="comment-box-date-alignment" name="comment-box-date-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-date-alignment'] == 'center') ? ' checked' : '';?>>
														<label for="sgrb-comment-box-date-center"></label>
													</div>
													<label for="sgrb-comment-box-date-center"> - <?php _e('center', 'sgrb');?></label>
												</div>
												<div class="sg-col-2">
													<div class="sgrb-radio-wrapper">
														<input value="right" id="sgrb-comment-box-date-right" class="comment-box-date-alignment" name="comment-box-date-alignment" autocomplete="off" type="checkbox"<?php echo (@$options['comment-box-date-alignment'] == 'right') ? ' checked' : '';?>>
														<label for="sgrb-comment-box-date-right"></label>
													</div>
													<label for="sgrb-comment-box-date-right"> - <?php _e('right', 'sgrb');?></label>
												</div>
											</div>
										</div>

										<div class="sgrb-comment-title-box"<?php echo (!SGRB_PRO_VERSION) ? ' style="opacity: 0.6;"': '';?>>
											<div class="sgrb-col-3">
												<div class="sgrb-comment-box-title" style="text-align:<?php echo (@$options['comment-box-date-alignment']) ? @$options['comment-box-date-alignment'] : 'right';?>">
													<span class="sgrb-date-hide-show-js<?php echo (!@$options['comment-box-date-show']) ? ' sgrb-disabled' : '';?>">d/m/year hh:mm, </span>
													<span class="sgrb-comment-by-hide-show-js<?php echo (!@$options['comment-box-comment-by-show']) ? ' sgrb-disabled' : '';?>"><?php _e('comment by', 'sgrb');?> ...</span>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php if (!SGRB_PRO_VERSION) :?>
			</div>
		<?php endif;?>

	</form>
</div>
