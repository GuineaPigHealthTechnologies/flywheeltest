<div class="container-fluid">
	<div class="sgrb-pro-options-wrapper">
		<div class="sgrb-coming-soon">
			<a class="sgrb-pull-right" target="_blank" href="<?php echo SGRB_PRO_URL ;?>"><img src="<?php echo $sgrb->app_url.'assets/page/img/long-ribbon.png'; ?>" width="400px"></a>
		</div>
		<div class="sgrb-require-options-fields sgrb-pro-options-opacity sgrb-pro-options-opacity">
			<div class="row">
				<div class="col-md-4 text-right">
					<span class="sgrb-comments-count-options"><?php _e('Comments to show', 'sgrb');?>:</span>
				</div>
				<div class="col-md-7">
					<input class="sgrb-comments-count-to-show" value="10" type="text">
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 text-right">
					<span class="sgrb-comments-count-options"><?php _e('Comments to load', 'sgrb');?>:</span>
				</div>
				<div class="col-md-7">
					<input class="sgrb-comments-count-to-load" value="3" type="text">
				</div>
			</div>
		</div>
		<div class="sgrb-require-options-fields sgrb-pro-options-opacity">
			<div class="row">
				<div class="col-md-4 text-right">
					<label for="sgrb-email-checkbox">
						<?php _e('Notify for new comments to this email', 'sgrb');?>:
						<input class="sgrb-admin-email " type="hidden" value="<?php echo get_option('admin_email') ;?>">
					</label>
				</div>
				<div class="col-md-1">
					<div class="sgrb-checkbox-wrapper">
						<input type="checkbox" value="true" id="sgrb-email-checkbox" class="sgrb-email-hide-show-js">
						<label class="sgrb-checkbox-label" for="sgrb-email-checkbox"></label>
					</div>
				</div>
				<div class="col-md-6">
					<input class="sgrb-email-notification" type="email" value="<?php echo get_option('admin_email');?>">
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 text-right">
					<label for="sgrb-required-login-checkbox">
						<?php _e('Require login for new comments', 'sgrb');?>:
					</label>
				</div>
				<div class="col-md-7">
					<div class="sgrb-checkbox-wrapper">
						<input type="checkbox" value="true" id="sgrb-required-login-checkbox">
						<label class="sgrb-checkbox-label" for="sgrb-required-login-checkbox"></label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 text-right">
					<label for="sgrb-hide-comment-form">
						<?php _e('Hide comment form for all users', 'sgrb');?>:
					</label>
				</div>
				<div class="col-md-7">
					<div class="sgrb-checkbox-wrapper">
						<input type="checkbox" value="true" id="sgrb-hide-comment-form">
						<label class="sgrb-checkbox-label" for="sgrb-hide-comment-form"></label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="sgrb-require-options-fields">
		<div class="row">
			<div class="col-md-4 text-right">
				<label for="sgrb-required-title-checkbox"><?php _e('Title required', 'sgrb');?>:</label>
			</div>
			<div class="col-md-7">
				<div class="sgrb-checkbox-wrapper">
					<input id="sgrb-required-title-checkbox" class="sgrb-email-notification-checkbox" value="true" type="checkbox" name="required-title-checkbox"<?php echo (@$sgrbDataArray['required-title-checkbox']) ? ' checked' : '';?><?php echo (@$sgrbRevId != 0) ? '' : ' checked';?>>
					<label class="sgrb-checkbox-label" for="sgrb-required-title-checkbox"></label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-right">
				<label for="sgrb-required-email-checkbox"><?php _e('Email required', 'sgrb');?>:</label>
			</div>
			<div class="col-md-7">
				<div class="sgrb-checkbox-wrapper">
					<input id="sgrb-required-email-checkbox" class="sgrb-email-notification-checkbox" value="true" type="checkbox" name="required-email-checkbox"<?php echo (@$sgrbDataArray['required-email-checkbox']) ? ' checked' : '';?><?php echo (@$sgrbRevId != 0) ? '' : ' checked';?>>
					<label class="sgrb-checkbox-label" for="sgrb-required-email-checkbox"></label>
				</div>
			</div>
		</div>
	</div>
	<div class="sgrb-require-options-fields">
		<div class="row">
			<div class="col-md-4 text-right">
				<label for="sgrb-auto-approve-checkbox"><?php _e('Auto approve comments', 'sgrb');?>:</label>
			</div>
			<div class="col-md-7">
				<div class="sgrb-checkbox-wrapper">
					<input id="sgrb-auto-approve-checkbox" class="sgrb-auto-approve-checkbox" value="true" type="checkbox" name="auto-approve-checkbox"<?php echo (@$sgrbDataArray['auto-approve-checkbox']) ? ' checked' : '';?>>
					<label class="sgrb-checkbox-label" for="sgrb-auto-approve-checkbox"></label>
				</div>
			</div>
		</div>
	</div>
	<div class="sgrb-require-options-fields">
		<div class="row">
			<div class="col-md-4 text-right">
				<span><?php _e('Detect user by PC or IP', 'sgrb');?>:</span>
			</div>
			<div class="col-md-7">
				<div class="sgrb-ip-pc-checkbox-wrapper">
					<input type="checkbox" value="true" id="sgrb-user-detect-by" name="user-detect-by"<?php echo (@$sgrbDataArray['user-detect-by']) ? ' checked' : '';?>>
					<label class="sgrb-checkbox-label" for="sgrb-user-detect-by"></label>
				</div>
			</div>
		</div>
	</div>

	<div class="sgrb-simple-review-categories-wrapper">
		<div class="row">
			<div class="col-md-4 text-right">
				<?php _e('Feature to rate', 'sgrb');?>:
			</div>
			<div class="col-md-7">
				<?php if (@$fields == '') :?>
                    <?php if ($simpleField == '') :?>
					<div class="sgrb-one-field">
						<input class="sgrb-fieldId" name="simpleFieldId" type="hidden" value="0" autocomplete="off">
						<input name="simple-field-name" type="text" value="" placeholder="<?php _e('e.g. quality/speed/price of review item', 'sgrb');?>" class="sgrb-field-name sgrb-field-name-single" autocomplete="off">
						<i class="sgrb-required-asterisk" style="float:right">required</i>
						<input type="hidden" class="fake-sgrb-id" name="simple-fake-id[]" value="66">
					</div>
				<?php else :?>
					<div class="sgrb-one-field">
						<?php foreach (@$simpleField as $field) : ?>
							<input class="sgrb-fieldId" name="simpleFieldId" type="hidden" value="<?php echo esc_attr(@$field->getId());?>" autocomplete="off">
							<input name="simple-field-name" readonly type="text" value="<?php echo esc_attr(@$field->getName());?>" placeholder="<?php _e('e.g. quality/speed/price of review item', 'sgrb');?>" class="sgrb-field-name sgrb-field-name-single" autocomplete="off">
							<input type="hidden" class="fake-sgrb-id" name="simple-fake-id[]" value="66">
						<?php break;?>
						<?php endforeach;?>
					</div>

					<?php endif;?>
				<?php endif;?>
			</div>
		</div>
	</div>
	<div class="sgrb-main-review-categories-wrapper">
		<div class="row sgrb-categories-title">
			<div class="col-md-4 text-right">
				<b><?php _e('Features to rate', 'sgrb');?> </b>:
			</div>
			<?php if (@$simpleField == '') :?>
				<?php if (empty($fields)) :?>
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-5">
							<i class="sgrb-category-empty-warning"> <?php echo (@$sgrbRevId != 0) ? '' : _e('At least one feature is required', 'sgrb');?></i>
						</div>
					</div>
					<div class="sgrb-field-container">
						<div class="row">
							<div class="col-md-12"><div class="sgrb-one-field" id="clone_1"><input class="sgrb-fieldId" name="fieldId[]" type="hidden" value="0" autocomplete="off"><input name="field-name[]" type="text" value="" placeholder="<?php _e('e.g. quality/speed/price of review item', 'sgrb');?>" class="sgrb-field-name" autocomplete="off"><input type="hidden" class="fake-sgrb-id" name="fake-id[]" value="66" autocomplete="off"><a class="btn btn-info sgrb-add-field" type="button" onclick="SGReview.prototype.clone()">+</a></div></div></div>
					</div>
				</div>
				<?php else :?>
					<?php foreach (@$fields as $field) : ?>
					<div class="col-md-7 col-md-offset-4">
						<div class="row">
							<input class="sgrb-fieldId" name="fieldId[]" type="hidden" value="<?php echo esc_attr(@$field->getId());?>" autocomplete="off">
							<div class="col-md-5">
								<i class="sgrb-category-empty-warning"> <?php echo (@$sgrbRevId != 0) ? '' : _e('At least one feature is required', 'sgrb');?></i>
							</div>
						</div>
						<div class="sgrb-field-container">
							<div class="row">
								<div class="col-md-12">
									<div class="sgrb-one-field" id="clone_1">
										<input<?php echo (@$sgrbRevId != 0) ? ' readonly' : '';?> name="field-name[]" type="text" value="<?php echo esc_attr(@$field->getName());?>"placeholder="<?php _e('e.g. quality/speed/price of review item', 'sgrb');?>" class="sgrb-border sgrb-field-name" autocomplete="off">
										<input type="hidden" class="fake-sgrb-id" name="fake-id[]" value="66" autocomplete="off">
										<a class="btn btn-embossed btn-primary sgrb-add-field sgrb-noteditable-field-name disabled" type="button"><img src="<?php echo $sgrb->app_url.'assets/page/img/checkmark.png'; ?>" width="24px"></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach;?>
				<?php endif;?>
			<?php endif;?>
		</div>
	</div>
</div>
