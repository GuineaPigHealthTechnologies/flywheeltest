<div class="sgrb-require-options-fields">
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Success post text:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="success-comment-text" value="<?php echo (@$sgrbDataArray['success-comment-text']) ? esc_attr(@$sgrbDataArray['success-comment-text']) : 'Thank You for Your Comment!';?>" type="text">
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Total rating:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="total-rating-text" value="<?php echo (@$sgrbDataArray['total-rating-text']) ? esc_attr(@$sgrbDataArray['total-rating-text']) : 'Total rating';?>" type="text">
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Add review:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="add-review-text" value="<?php echo (@$sgrbDataArray['add-review-text']) ? esc_attr(@$sgrbDataArray['add-review-text']) : 'Add your own review';?>" type="text">
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Edit review:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="edit-review-text" value="<?php echo (@$sgrbDataArray['edit-review-text']) ? esc_attr(@$sgrbDataArray['edit-review-text']) : 'Edit your own review';?>" type="text">
		</div>
	</div>
<?php if (@$sgrbDataArray['review-type'] != SGRB_REVIEW_TYPE_SOCIAL) :?>
	<div class="sgrb-hide-options-for-social-review-js">
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Name:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="name-text" value="<?php echo (@$sgrbDataArray['name-text']) ? esc_attr(@$sgrbDataArray['name-text']) : 'Your name';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Name placeholder:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="name-placeholder-text" value="<?php echo (@$sgrbDataArray['name-placeholder-text']) ? esc_attr(@$sgrbDataArray['name-placeholder-text']) : 'name';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Email:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="email-text" value="<?php echo (@$sgrbDataArray['email-text']) ? esc_attr(@$sgrbDataArray['email-text']) : 'Email';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Email placeholder:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="email-placeholder-text" value="<?php echo (@$sgrbDataArray['email-placeholder-text']) ? esc_attr(@$sgrbDataArray['email-placeholder-text']) : 'email';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Title:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="title-text" value="<?php echo (@$sgrbDataArray['title-text']) ? esc_attr(@$sgrbDataArray['title-text']) : 'Title';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Title placeholder:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="title-placeholder-text" value="<?php echo (@$sgrbDataArray['title-placeholder-text']) ? esc_attr(@$sgrbDataArray['title-placeholder-text']) : 'title';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Comment:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="comment-text" value="<?php echo (@$sgrbDataArray['comment-text']) ? esc_attr(@$sgrbDataArray['comment-text']) : 'Comment';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Comment placeholder:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="comment-placeholder-text" value="<?php echo (@$sgrbDataArray['comment-placeholder-text']) ? esc_attr(@$sgrbDataArray['comment-placeholder-text']) : 'your comment here';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Load more button:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="load-more-text" value="<?php echo (@$sgrbDataArray['load-more-text']) ? esc_attr(@$sgrbDataArray['load-more-text']) : 'Load more';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('No more comments:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="no-more-text" value="<?php echo (@$sgrbDataArray['no-more-text']) ? esc_attr(@$sgrbDataArray['no-more-text']) : 'no more comments';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Show all:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="show-all-text" value="<?php echo (@$sgrbDataArray['show-all-text']) ? esc_attr(@$sgrbDataArray['show-all-text']) : 'show all';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Hide:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="hide-text" value="<?php echo (@$sgrbDataArray['hide-text']) ? esc_attr(@$sgrbDataArray['hide-text']) : 'hide';?>" type="text">
			</div>
		</div>
	</div>
<?php endif;?>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Post button:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="post-button-text" value="<?php echo (@$sgrbDataArray['post-button-text']) ? esc_attr(@$sgrbDataArray['post-button-text']) : 'Post Comment';?>" type="text">
		</div>
	</div>
	<?php if (SGRB_PRO_VERSION) :?>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Captcha regenerate:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="captcha-text" value="<?php echo (@$sgrbDataArray['captcha-text']) ? esc_attr(@$sgrbDataArray['captcha-text']) : 'Change image';?>" type="text">
			</div>
		</div>
<?php if (@$sgrbDataArray['review-type'] != SGRB_REVIEW_TYPE_SOCIAL) :?>
	<div class="sgrb-hide-options-for-social-review-js">
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Not logged in users:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="logged-in-text" value="<?php echo (@$sgrbDataArray['logged-in-text']) ? esc_attr(@$sgrbDataArray['logged-in-text']) : 'Sorry, to leave a review you need to log in';?>" type="text">
			</div>
		</div>
	</div>
<?php endif;?>
	<?php endif;?>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('No rated categories:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="no-category-text" value="<?php echo (@$sgrbDataArray['no-category-text']) ? esc_attr(@$sgrbDataArray['no-category-text']) : 'Please, rate all suggested categories';?>" type="text">
		</div>
	</div>
<?php if (@$sgrbDataArray['review-type'] != SGRB_REVIEW_TYPE_SOCIAL) :?>
	<div class="sgrb-hide-options-for-social-review-js">
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Empty name:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="no-name-text" value="<?php echo (@$sgrbDataArray['no-name-text']) ? esc_attr(@$sgrbDataArray['no-name-text']) : 'Name is required';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Empty email:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="no-email-text" value="<?php echo (@$sgrbDataArray['no-email-text']) ? esc_attr(@$sgrbDataArray['no-email-text']) : 'Invalid email address';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Empty title:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="no-title-text" value="<?php echo (@$sgrbDataArray['no-title-text']) ? esc_attr(@$sgrbDataArray['no-title-text']) : 'Title is required';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Empty comment:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="no-comment-text" value="<?php echo (@$sgrbDataArray['no-comment-text']) ? esc_attr(@$sgrbDataArray['no-comment-text']) : 'Comment text is required';?>" type="text">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 text-right">
				<?php _e('Comment by:', 'sgrb');?>
			</div>
			<div class="col-md-8">
				<input class="sgrb-comments-count-to-show" name="comment-by-text" value="<?php echo (@$sgrbDataArray['comment-by-text']) ? esc_attr(@$sgrbDataArray['comment-by-text']) : 'comment by';?>" type="text">
			</div>
		</div>
	</div>
<?php endif;?>
	<?php if (SGRB_PRO_VERSION) :?>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Incorrect captcha:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="no-captcha-text" value="<?php echo (@$sgrbDataArray['no-captcha-text']) ? esc_attr(@$sgrbDataArray['no-captcha-text']) : 'Invalid captcha text';?>" type="text">
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Widget link add review:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="widget-link-add-text" value="<?php echo (@$sgrbDataArray['widget-link-add-text']) ? esc_attr(@$sgrbDataArray['widget-link-add-text']) : 'leave a review';?>" type="text">
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 text-right">
			<?php _e('Widget link edit review:', 'sgrb');?>
		</div>
		<div class="col-md-8">
			<input class="sgrb-comments-count-to-show" name="widget-link-edit-text" value="<?php echo (@$sgrbDataArray['widget-link-edit-text']) ? esc_attr(@$sgrbDataArray['widget-link-edit-text']) : 'edit review';?>" type="text">
		</div>
	</div>
	<?php endif;?>

</div>
