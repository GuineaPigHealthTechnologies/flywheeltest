<?php

global $SGRB_AUTOLOAD;
$SGRB_AUTOLOAD = array();

$SGRB_AUTOLOAD['menu_items'] = array(
	array(
		'id' => 'showAll',
		'page_title' => 'All Reviews',
		'menu_title' => 'Review Builder',
		'capability' => 'manage_options',
		'icon' => 'dashicons-testimonial',
		'controller' => 'Review',
		'action' => 'index',
		'submenu_items' => array(
			array(
				'id' => 'showAll',
				'page_title' => __('All Reviews', 'sgrb'),
				'menu_title' => __('All Reviews', 'sgrb'),
				'capability' => 'manage_options',
				'controller' => 'Review',
				'action' => 'index',
			),
			array(
				'id' => 'add',
				'page_title' => __('Add/Edit Review', 'sgrb'),
				'menu_title' => __('Add Review', 'sgrb'),
				'capability' => 'manage_options',
				'controller' => 'Review',
				'action' => 'edit',
			),
			array(
				'id' => 'allComms',
				'page_title' => __('All Comments', 'sgrb'),
				'menu_title' => __('All Comments', 'sgrb'),
				'capability' => 'manage_options',
				'controller' => 'Comment',
				'action' => 'index'
			),
			array(
				'id' => 'addComment',
				'page_title' => __('Add/Edit Comment', 'sgrb'),
				'menu_title' => __('Add Comment', 'sgrb'),
				'capability' => 'manage_options',
				'controller' => 'Comment',
				'action' => 'save',
			),
			array(
				'id' => 'allTemplates',
				'page_title' => __('All Templates', 'sgrb'),
				'menu_title' => __('All Templates', 'sgrb').'<i class="sgrb-required-asterisk"> PRO</i>',
				'capability' => 'manage_options',
				'controller' => 'TemplateDesign',
				'action' => 'index',
			),
			array(
				'id' => 'addTemplate',
				'page_title' => __('Add/Edit Template', 'sgrb'),
				'menu_title' => __('Add Template', 'sgrb').'<i class="sgrb-required-asterisk"> PRO</i>',
				'capability' => 'manage_options',
				'controller' => 'TemplateDesign',
				'action' => 'save',
			),
			array(
				'id' => 'addForm',
				'page_title' => __('Comment box', 'sgrb'),
				'menu_title' => __('Comment box', 'sgrb'),
				'capability' => 'manage_options',
				'controller' => 'CommentForm',
				'action' => 'edit',
			),
			array(
				'id' => 'sgSettings',
				'page_title' => 'Settings',
				'menu_title' => 'Settings',
				'capability' => 'manage_options',
				'controller' => 'Review',
				'action' => 'reviewSetting',
			),
			array(
				'id' => 'sgPlugins',
				'page_title' => __('More Plugins', 'sgrb'),
				'menu_title' => __('More Plugins', 'sgrb'),
				'capability' => 'manage_options',
				'controller' => 'Review',
				'action' => 'morePlugins',
			)
		),
	),
);

$SGRB_AUTOLOAD['network_admin_menu_items'] = array();

$SGRB_AUTOLOAD['shortcodes'] = array(
	array(
		'shortcode' => 'sgrb_review',
		'controller' => 'Review',
		'action' => 'sgrbShortcode',
	),
);

$SGRB_AUTOLOAD['front_ajax'] = array(
	array(
		'controller' =>'Review',
		'action' => 'ajaxLazyLoading',
	),
	array(
		'controller' =>'Review',
		'action' => 'ajaxUserRate'
	),
	array(
		'controller' =>'Review',
		'action' => 'ajaxReloadReviewFrontView'
	)
);

$SGRB_AUTOLOAD['admin_ajax'] = array(
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxSave',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxDelete',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxWooProductLoad',
	),
	array(
		'controller' =>'Review',
		'action' => 'ajaxReloadReviewFrontView'
	),
	array(
		'controller' => 'Comment',
		'action'	 => 'ajaxSave',
	),
	array(
		'controller' => 'Comment',
		'action'	 => 'ajaxDelete',
	),
	array(
		'controller' => 'Comment',
		'action'	 => 'ajaxApproveComment',
	),
	array(
		'controller' => 'Comment',
		'action'	 => 'ajaxSelectReview',
	),
	array(
		'controller' => 'Comment',
		'action'	 => 'ajaxSelectPosts',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxDeleteField',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxPostComment',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxSelectTemplate',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxPagination',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxUserRate',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxSaveFreeTables',
	),
	array(
		'controller' => 'TemplateDesign',
		'action'	 => 'ajaxSave',
	),
	array(
		'controller' => 'TemplateDesign',
		'action'	 => 'ajaxDeleteTemplate',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxCloseBanner',
	),
	array(
		'controller' => 'Review',
		'action'	 => 'ajaxCloneReview',
	)
);

$SGRB_AUTOLOAD['admin_post'] = array(
	array(
		'controller' => 'Review',
		'action'	 => 'delete',
	)
);

//use wp_ajax_library to include ajax for the frontend
$SGRB_AUTOLOAD['front_scripts'] = array();

//use wp_enqueue_media to enqueue media
$SGRB_AUTOLOAD['admin_scripts'] = array();

$SGRB_AUTOLOAD['front_styles'] = array();

$SGRB_AUTOLOAD['admin_styles'] = array();
