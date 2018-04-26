<?php

class SGRB
{
	private $autoload;
	private $menuActions = array();
	private $ajaxCallbacks = array();
	private $postCallbacks = array();
	private $shortcodes = array();
	private $slugs = array();
	public $prefix = '';
	public $app_path = '';
	public $app_url = '';

	public function __construct()
	{
		$this->prefix = strtolower(__CLASS__).'_';
		$var = strtoupper($this->prefix).'AUTOLOAD';
		global $$var;
		$this->autoload = $$var;
	}

	public function __call($name, $args)
	{
		$param1 = null;
		$param2 = null;

		if (strpos($name, 'wp_ajax_')===0) {
			$action = $this->ajaxCallbacks[$name];
		}
		else if (strpos($name, 'wp_shortcode_')===0) {;
			$action = $this->shortcodes[$name];
			$param1 = $args[0];
			$param2 = $args[1];
		}
		else {
			$action = $this->menuActions[$name];
		}

		return $this->dispatchAction($action, $param1, $param2);
	}

	public function run()
	{
		$this->registerSetupController();

		add_action('plugins_loaded', array($this, 'sgrbSetVersion'));

		if (count($this->autoload['menu_items'])) {
			add_action('admin_menu', array($this, 'loadMenu'));
		}

		if (count($this->autoload['network_admin_menu_items'])) {
			add_action('network_admin_menu', array($this, 'loadNetworkAdminMenu'));
		}

		$this->registerAjaxCallbacks();
		$this->registerShortcodes();
		$this->registerPostCallbacks();

		add_action('admin_enqueue_scripts', array($this, 'includeAdminScriptsAndStyles'));
		add_action('wp_enqueue_scripts', array($this, 'includeFrontScriptsAndStyles'));
		add_action('media_buttons', array($this, 'sgrb_media_button'));
		add_action('wp_head', array($this, 'sgrbCreateAjaxUrl'));
		add_filter('the_content', array($this, 'showPostReview'));
		// start wooCommerce
		add_action('wp_head', array($this, 'woo_review_disable'));
		add_filter('woocommerce_product_tabs', array($this, 'woo_sgrb_product_tab'));
		// end wooCommerce
		add_action('add_meta_boxes_post',array($this, 'meta_post_box'));
		add_action('save_post', array($this, 'sgrbSelectPostReview'));
		add_action("widgets_init", array($this, 'sgrbWidgetInit'));
		add_action('admin_notices', array($this, 'sgrbBanner'));
	}
	public function sgrbBanner() {
		if (SGRB_PRO_VERSION) {
			return;
		}
		if ($this->getReviewTotalRate() < 4.6) {
			delete_option(SG_REVIEW_BANNER);
			return;
		}
		if (get_option(SG_REVIEW_BANNER) && !get_option(SG_NO_BANNER)) {
			$this->includeScript('page/scripts/helpers/sgReviewHelper');
			$this->includeScript('page/scripts/helpers/sgTemplateHelper');
			$this->includeScript('page/scripts/helpers/sgCommentHelper');
			$this->includeScript('page/scripts/helpers/sgRateSkin');
			$this->includeScript('page/scripts/helpers/sgMainHelper');
			$this->includeScript('page/scripts/helpers/sgWizardSettings');
			$this->includeScript('core/scripts/main');
			$this->includeScript('page/scripts/sgReview');
			$this->includeScript('page/scripts/sgComment');
			$this->includeScript('page/scripts/sgTemplate');
			$this->includeScript('page/scripts/sgForm');
			$this->includeScript('core/scripts/sgrbRequestHandler');
			require_once($this->app_path.'com/layouts/Review/banner.php');
		}
	}

	public function woo_review_disable()
	{
		$this->includeController('Review');
		$html = '';
		$reviewId = 0;
		$selectedCategory = array();
		$reviewsArray = array();

		$currentPost = get_post();
		if (!is_object($currentPost)) {
			return;
		}
		$currentPostId = $currentPost->ID;
		$currentPostType = $currentPost->post_type;

		if (($currentPostType == 'product') && !is_page() && SGRB_PRO_VERSION == 1) {
			$byProductReviews = SGRB_Page_ReviewModel::finder()->findAll('product_id = %d', $currentPostId);
			if ($byProductReviews) {
				foreach ($byProductReviews as $byProductReview) {
					if ($byProductReview) {
						$reviewId = $byProductReview->getReview_id();
					}
				}
			}
			else {
				$currentProductCategories = get_the_terms($currentPostId, 'product_cat');
				if ($currentProductCategories && !is_wp_error($currentProductCategories)) {
					$lastCategoryIndex = max(array_keys($currentProductCategories));
					$currentCat = $currentProductCategories[$lastCategoryIndex];
					$currentCatId = $currentCat->term_id;
					if ($currentCatId) {
						$byProductCategoryReviews = SGRB_Page_ReviewModel::finder()->find('category_id = %d', $currentCatId);
						if ($byProductCategoryReviews) {
							$reviewId = $byProductCategoryReviews->getReview_id();
						}
					}
				}
			}
			if ($reviewId) {
				$review = SGRB_ReviewModel::finder()->findByPk($reviewId);
				if ($review) {
					$options = $review->getOptions();
					$options = json_decode($options, true);
					$isDisabled = $options['disable-woo-comments'];
					if ($isDisabled) {
						add_filter('woocommerce_product_tabs', array($this, 'woo_remove_product_tabs'));
					}
				}
			}
		}
	}

	public function woo_remove_product_tabs($tabs) {
		unset($tabs['reviews']);
		return $tabs;
	}

	public function woo_sgrb_product_tab($tabs) {
		// Adds the new tab
		$currentPost = get_post();
		if (!is_object($currentPost)) {
			return false;
		}

		$currentPostId = $currentPost->ID;
		$byProductCategoryReviews = '';
		$byProductReviews = SGRB_Page_ReviewModel::finder()->findAll('product_id = %d', $currentPostId);
		$currentProductCategories = get_the_terms($currentPostId, 'product_cat');//product_type
		if ($currentProductCategories && !is_wp_error($currentProductCategories)) {
			$lastCategoryIndex = max(array_keys($currentProductCategories));
			$currentCat = $currentProductCategories[$lastCategoryIndex];
			$currentCatId = $currentCat->term_id;

			if ($currentCatId) {
				$byProductCategoryReviews = SGRB_Page_ReviewModel::finder()->find('category_id = %d', $currentCatId);
			}
		}

		if (!empty($byProductReviews) || $byProductCategoryReviews) {
			$tabs['sgrb_tab'] = array(
				'title' 	=> __( 'Add reviews', 'woocommerce' ),
				'priority' 	=> 50,
				'callback' 	=> array($this, 'woo_sgrb_product_tab_content')
			);
		}

		return $tabs;

	}
	public function woo_sgrb_product_tab_content() {
		// The new tab content
		$this->includeController('Review');
		$html = '';
		$reviewId = 0;
		$selectedCategory = array();
		$reviewsArray = array();

		$currentPost = get_post();
		if (!is_object($currentPost)) {
			return;
		}
		$currentPostId = $currentPost->ID;

		$currentPostType = $currentPost->post_type;
		$allTerms = get_terms(array('get' => 'all'));
		foreach ($allTerms as $term) {
			if (get_term_meta($term->term_id)) {
				if ($term->term_id == $currentPostId) {
					$termsArray['id'][] = $term->term_id;
					$termsArray['name'][] = $term->name;
				}
			}
		}

		if (($currentPostType == 'product') && !is_page() && SGRB_PRO_VERSION == 1) {
			$byProductReviews = SGRB_Page_ReviewModel::finder()->findAll('product_id = %d', $currentPostId);

			if (!empty($byProductReviews)) {
				foreach ($byProductReviews as $byProductReview) {
					if ($byProductReview) {
						$reviewId = $byProductReview->getReview_id();
					}
				}
				if ($reviewId) {
					$review = SGRB_ReviewModel::finder()->findByPk($reviewId);
					if ($review) {
						$rev = new SGRB_ReviewController();
						$html = $rev->createWooReviewHtml($review);
					}
				}
			}
			else {
				$currentProductCategories = get_the_terms($currentPostId, 'product_cat');
				if ($currentProductCategories && !is_wp_error($currentProductCategories)) {
					$lastCategoryIndex = max(array_keys($currentProductCategories));
					$currentCat = $currentProductCategories[$lastCategoryIndex];
					$currentCatId = $currentCat->term_id;
					if ($currentCatId) {
						$byProductCategoryReviews = SGRB_Page_ReviewModel::finder()->find('category_id = %d', $currentCatId);
						$reviewId = $byProductCategoryReviews->getReview_id();
						if ($reviewId) {
							$review = SGRB_ReviewModel::finder()->findByPk($reviewId);
							if ($review) {
								$rev = new SGRB_ReviewController();
								$html = $rev->createWooReviewHtml($review);
							}
						}
					}
				}
			}
		}
		echo $html;
	}

	public function sgrbSetVersion()
	{
		$this->includeModel('TemplateDesign');
		$this->includeModel('Comment_Rating');
		$this->includeModel('CommentForm');
		$this->sgrbLoadTextdomain();

		$sgrbVersion = (int)get_option('SGRB_VERSION');
		$sgrbCommentBoxTheme = get_option('sgrb-comment-box-theme');
		$commentBox = '{"comment-box-theme":"1","comment-box-rate-show":"true","comment-box-rate-alignment":"right","comment-box-title-show":"true","comment-box-title-alignment":"left","comment-box-avatar":null,"comment-box-text":"true","comment-box-avatar-and-text-alignment":"left","comment-box-date-show":"true","comment-box-comment-by-show":"true","comment-box-date-alignment":"right"}';
		if (!$sgrbCommentBoxTheme) {
			update_option('sgrb-comment-box-theme', $commentBox);
		}
		if (!SGRB_PRO_VERSION) {
			if ($sgrbVersion && $sgrbVersion >= '1.1.3' && $sgrbVersion <= '2.0.3') {
				SGRB_CommentModel::changeColumnType();
			}
			if ($sgrbVersion && $sgrbVersion <= '1.1.2') {
				SGRB_TemplateDesignModel::create();
				SGRB_Comment_RatingModel::alterTable();
				SGRB_Rate_LogModel::alterTable();
			}
			if ($sgrbVersion && $sgrbVersion < '2.0.7') {
				SGRB_TemplateDesignModel::updateColumns();
				SGRB_ReviewModel::alterReviewTypeColumn();
			}
		}

		if($sgrbVersion < SGRB_VERSION) update_option('SGRB_VERSION', SGRB_VERSION);
	}

	public function getReviewTotalRate()
	{
		$total = 0;
		$reviews = SGRB_ReviewModel::finder()->findAll();
		foreach ($reviews as $review) {
			$rateCount = 0;
			$reviewId = $review->getId();
			$allComments = SGRB_CommentModel::finder()->findAll('review_id = %d', $reviewId);
			$currentReviewCategories = SGRB_CategoryModel::finder()->findAll('review_id = %d', $reviewId);
			foreach ($currentReviewCategories as $category) {
				$rates = SGRB_Comment_RatingModel::finder()->findAll('category_id = %d', $category->getId());
				foreach ($rates as $rate) {
					$total += $rate->getRate();
					$rateCount++;
				}
			}
			if ($total && $rateCount) {
				$total = $total/$rateCount;
			}
			if ($total >= 4.6) {
				if (count($allComments) >= 3) {
					update_option(SG_REVIEW_BANNER, SG_REVIEW_BANNER);
				}
				else {
					delete_option(SG_REVIEW_BANNER);
				}
				return $total;
			}
		}
		return $total;
	}

	public function sgrbLoadTextdomain()
	{
		load_plugin_textdomain( 'sgrb', false, dirname(plugin_basename(__FILE__)).'/languages/');
	}

	public function sgrbWidgetInit()
	{
		$this->includeLib('SgrbWidget');
		register_widget("SgrbWidget");
	}

	public function sgrb_media_button()
	{
		$currentPost = get_post();
		if ($currentPost && is_object($currentPost)) {
			$currentPostId = $currentPost->ID;
			$currentPostType = $currentPost->post_type;
			if ($currentPostType == 'product') {
				return;
			}
		}
		$buttonTitle = __('Insert review', 'sgrb');
		$output = '';
		$buttonIcon = '<i class="dashicons dashicons-testimonial" style="padding: 3px 2px 0px 0px"></i>';
		$output = '<a href="javascript:void(0);" onclick="jQuery(\'#sgrb-thickbox\').dialog({ width: 450, modal: true ,resizable: false,beforeClose : function(event, ui) { jQuery(\'.sgrb-not-selected-notice-message\').css(\'display\', \'none\') } });" class="button" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $buttonIcon.$buttonTitle.'</a>';
		echo $output;
		add_action('admin_footer',array($this,'mediaButtonThickboxs'));
	}

	public function meta_post_box()
	{
		add_meta_box('my-review', 'Select review', array($this,'reviewMetabox'), 'post', 'normal', 'default');
	}

	public function reviewMetabox()
	{
		global $post;
		$postReviewType = 'post_review';
		$selectedPostReview = 0;
		$reviewsArray = array();
		$currentPost = get_post();
		if (!is_object($currentPost)) {
			return false;
		}
		$postMetaValue = get_post_meta($currentPost->ID);
		if (empty($postMetaValue)) {
			return false;
		}
		if (!@empty($postMetaValue['post_review'])) {
			$selectedPostReview = $postMetaValue['post_review'][0];
			if ($selectedPostReview) {
				$selected = ' selected';
			}
			else {
				$selected = '';
			}
		}
		$postReviews = SGRB_TemplateModel::finder()->findAll('name = %s', $postReviewType);
		$html = '<select><option>Select review</option></select>';
		if (!empty($postReviews)) {
			foreach ($postReviews as $postReview) {
				$reviews = SGRB_ReviewModel::finder()->find('template_id = %d', $postReview->getId());
				$reviewsArray[] = $reviews;
			}
			$html = '<select name="post_review">';

			$html .= '<option value="">Not selected</option>';

			$currentPostCategory = get_the_category(get_post()->ID);
			//$selected = '';
			foreach ($reviewsArray as $review) {
				if ($review) {
					$options = $review->getOptions();
					$options = json_decode($options, true);
					$categoryId = $options['post-category'];
					if ($currentPostCategory) {
						if ($categoryId == $currentPostCategory[0]->term_id) {
							if ($selectedPostReview == $review->getId()) {
								$selected = ' selected';
							}
							else {
								$selected = '';
							}
							$html .= '<option value="'.$review->getId().'"'.$selected.'>'.$review->getTitle().'</option>';
						}
					}
				}
			}
			$html .= '</select>';
		}
		echo $html;
	}

	public function sgrbSelectPostReview($post_id)
	{
		if (empty($_POST['post_review'])) {
			delete_post_meta($post_id, 'post_review');
		}
		else {
			update_post_meta($post_id, 'post_review' , $_POST['post_review']);
		}
	}

	function showPostReview($content)
	{
		global $post;

		$allPostReviews = array();
		$this->includeController('Review');
		if (is_singular('post') && !is_page() && SGRB_PRO_VERSION == 1) {
			$html = '';
			$currentPost = get_post();
			if (!is_object($currentPost)) {
				return false;
			}
			$postMetaValue = get_post_meta($currentPost->ID);
			if (empty($postMetaValue)) {
				return false;
			}

			$allPostReviews = SGRB_ReviewModel::finder()->findAll('type = %d', array(SGRB_REVIEW_TYPE_POST));
			if (empty($allPostReviews)) {
				return;
			}

			foreach ($allPostReviews as $singlePostReview) {
				if (!$singlePostReview) {
					continue;
				}
				$options = $singlePostReview->getOptions();
				$options = json_decode($options, true);
				$categoryId = (int)$options['post-category'];
				$disableComments = $options['disable-wp-comments'];

				$category = get_the_category($currentPost->ID);
				if ($categoryId == $category[0]->cat_ID) {
					$rev = new SGRB_ReviewController();
					$html = $rev->createPostReviewHtml($singlePostReview);
				}
			}
			if ($html) {
				if ($disableComments) {
					add_filter('comments_open', array($this, 'disableComments'));
				}
				else {
					remove_filter('comments_open', array($this, 'disableComments'));
				}
				$content = $content.'<div>'.$html;
				return '<div>'.$content.'<div>';
			}
		}
		return $content;
	}

	public function disableComments()
	{
		return false;
	}

	public function sgrbCreateAjaxUrl()
	{
		?>
			<script type="text/javascript">
			var sgrb_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			</script>
		<?php
	}

	public function mediaButtonThickboxs()
	{
		$this->includeStyle('page/styles/general/jquery-ui-dialog');
		$this->includeStyle('page/styles/review/save');
		$this->includeScript('core/scripts/jquery-ui-dialog');

		?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#sgrb-insert').on('click', function () {
					if (jQuery('#sgrb-type-buttons-id').val() == 1) {/*insert review*/
						var id = jQuery('#sgrb-buttons-id-review').val();
					}
					else if (jQuery('#sgrb-type-buttons-id').val() == 2) {/*insert widget*/
						var id = jQuery('#sgrb-buttons-id-widget').val();
					}
					else {
						var id = jQuery('#sgrb-buttons-id-review').val();
					}
					if ('' === id) {
						jQuery('.sgrb-not-selected-notice-message').css('display', 'inline');
						jQuery('.sgrb-insert-button').css('margin-top', 0);
						return;
					}
					else {
						jQuery('.sgrb-not-selected-notice-message').css('display', 'none');
						jQuery('.sgrb-insert-button').css('margin-top', '20px');
					}
					selectionText = '';
					if (typeof(tinyMCE.editors.content) != "undefined") {
						selectionText = (tinyMCE.activeEditor.selection.getContent()) ? tinyMCE.activeEditor.selection.getContent() : '';
					}
					if (jQuery('#sgrb-type-buttons-id').val() == 1) {/*insert review*/
						window.send_to_editor('[sgrb_review id=' + id + ']');
					}
					else if (jQuery('#sgrb-type-buttons-id').val() == 2) {/*insert widget*/
						var linkUrl ='';
						if (jQuery('.sgrb-widget-link').val() != '') {
							linkUrl = ' link='+jQuery('.sgrb-widget-link').val();
						}
						window.send_to_editor('[sgrb_widget id=' + id + linkUrl + ']');
					}
					else {
						window.send_to_editor('[sgrb_review id=' + id + ']');
					}
					jQuery('#sgrb-thickbox').dialog('close');
				});
			});
		</script>
		<div id="sgrb-thickbox" title="<?php _e('Insert review', 'sgrb');?>" style="height:0px;width:350px;display:none">
			<div class="wrap">
				<p class="insert-title"><?php _e('Insert the shortcode for showing a Review', 'sgrb');?>.</p>
				<?php if (SGRB_PRO_VERSION) :?>
				<div class="sgrb-widget-review-insert-type">
					<select id="sgrb-type-buttons-id">
						<option value="1"><?php _e('Insert Review', 'sgrb');?></option>
						<option value="2"><?php _e('Insert Widget of Reivew', 'sgrb');?></option>
					</select>
				</div>
				<?php endif ;?>
				<div class="sgrb-insert-review">
					<select id="sgrb-buttons-id-review">
						<option value=""><?php _e('Please select...', 'sgrb');?></option>
						<?php
							global $wpdb;
							$proposedTypes = array();
							$orderBy = 'id DESC';
							$allTables = SGRB_ReviewModel::finder()->findAll();
							arsort($allTables);
							foreach ($allTables as $table) : ?>
								<option value="<?php echo esc_attr($table->getId());?>"><?php echo esc_html($table->getTitle());?></option>;
							<?php endforeach; ?>
					</select>
				</div>
				<?php if (SGRB_PRO_VERSION) :?>
				<div class="sgrb-insert-widget" style="display: none;">
					<select id="sgrb-buttons-id-widget">
						<option value=""><?php _e('Please select...', 'sgrb');?></option>
						<?php
							global $wpdb;
							$proposedTypes = array();
							$orderBy = 'id DESC';
							$allTables = SGRB_ReviewModel::finder()->findAll();
							arsort($allTables);
							foreach ($allTables as $table) : ?>
								<option value="<?php echo esc_attr($table->getId());?>"><?php echo esc_html($table->getTitle());?></option>;
							<?php endforeach; ?>
					</select>
					<input type="text" class="sgrb-widget-link" style="width:100%" placeholder="<?php _e('you review page url...', 'sgrb');?>">
				</div>
				<?php endif ;?>
				<p class="sgrb-not-selected-notice-message" style="display:none"><?php _e('Notice : select your review', 'sgrb');?></p>
				<p class="submit sgrb-insert-button">
					<input type="button" id="sgrb-insert" class="button-primary dashicons-share" value="<?php _e('Insert', 'sgrb');?>"/>
					<a class="button-secondary" onclick="jQuery('#sgrb-thickbox').dialog('close')"><?php _e('Cancel', 'sgrb');?></a>
				</p>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('#sgrb-type-buttons-id').change(function(){
				if (jQuery(this).val() == 1) { /*insert review*/
					jQuery('.sgrb-insert-widget').hide();
					jQuery('.sgrb-insert-review').show();
				}
				else if (jQuery(this).val() == 2) { /*insert widget*/
					jQuery('.sgrb-insert-widget').show();
					jQuery('.sgrb-insert-review').hide();
				}
			});
		</script>
	<?php
	}

	public function includeController($controller)
	{
		require_once($this->app_path.'com/controllers/'.$controller.'.php');
	}

	public function includeView($view)
	{
		require_once($this->app_path.'com/views/'.$view.'.php');
	}

	public function includeModel($model)
	{
		require_once($this->app_path.'com/models/'.$model.'.php');
	}

	public function includeLib($lib)
	{
		require_once($this->app_path.'com/lib/'.$lib.'.php');
	}

	public function includeCore($core)
	{
		require_once($this->app_path.'com/core/'.$core.'.php');
	}

	public function asset($component)
	{
		return $this->app_url.'assets/'.$component;
	}

	public function tablename($tbl, $blogId = 1)
	{
		global $wpdb;

		if ($blogId && $blogId > 1) {
			return $wpdb->prefix.$blogId.$this->prefix.$tbl;
		}
		else {
			return $wpdb->prefix.$this->prefix.$tbl;
		}
	}

	public function layoutPath($layout)
	{
		return $this->app_path.'com/layouts/'.$layout.'.php';
	}

	public function adminUrl($action, $extra='')
	{
		$url = admin_url().'admin.php?page='.$this->actionToSlug($action);
		if ($extra) $url .= '&'.$extra;
		return $url;
	}

	public function adminPostUrl($action, $extra='')
	{
		$url = admin_url().'admin-post.php?action='.$action;
		if ($extra) $url .= '&'.$extra;
		return $url;
	}

	public function url($component)
	{
		return $this->app_url.$component;
	}

	public function redirect($component)
	{
		header('Location: '.$this->url($component));
		exit;
	}

	public function registerSetupController()
	{
		$this->includeController('Setup');
		$controllerName = $this->prefix.'SetupController';

		register_activation_hook($this->app_path.'app.php', array($controllerName, 'activate'));
		register_deactivation_hook($this->app_path.'app.php', array($controllerName, 'deactivate'));
		register_uninstall_hook($this->app_path.'app.php', array($controllerName, 'uninstall'));

		add_action('wpmu_new_blog', array($controllerName, 'createBlog'));
	}

	private function registerAjaxCallbacks()
	{
		if (count($this->autoload['front_ajax'])) {
			foreach ($this->autoload['front_ajax'] as $callback) {
				$id = 'wp_ajax_nopriv_'.$this->prefix.$callback['controller'].'_'.$callback['action'];
				$this->ajaxCallbacks[$id] = array($callback['controller'], $callback['action']);
				add_action($id, array($this, $id));

				$id = 'wp_ajax_'.$this->prefix.$callback['controller'].'_'.$callback['action'];
				$this->ajaxCallbacks[$id] = array($callback['controller'], $callback['action']);
				add_action($id, array($this, $id));
			}
		}

		if (count($this->autoload['admin_ajax'])) {
			foreach ($this->autoload['admin_ajax'] as $callback) {
				$id = 'wp_ajax_'.$this->prefix.$callback['controller'].'_'.$callback['action'];
				$this->ajaxCallbacks[$id] = array($callback['controller'], $callback['action']);
				add_action($id, array($this, $id));
			}
		}
	}

	private function registerPostCallbacks()
	{
		if (count($this->autoload['admin_post'])) {
			foreach ($this->autoload['admin_post'] as $callback) {
				$id = 'admin_post_'.$this->prefix.$callback['controller'].'/'.$callback['action'];
				$this->postCallbacks[$id] = array($callback['controller'], $callback['action']);
				add_action($id, array($this, $id));
			}
		}
	}

	private function registerShortcodes()
	{
		foreach ($this->autoload['shortcodes'] as $shortcode) {
			$id = 'wp_shortcode_'.$shortcode['shortcode'];
			$this->shortcodes[$id] = array($shortcode['controller'], $shortcode['action']);
			add_shortcode($shortcode['shortcode'], array($this, $id));
		}
	}

	public function setLocale($locale)
	{
		return 'en';
	}

	private function prepareForLocalization()
	{

	}

	public function includeScript($script)
	{
		if ($script=='wp_enqueue_media') {
			wp_enqueue_media();
			return;
		}

		if ($script=='wp_ajax_library') {
			add_action('wp_head', array($this, 'addAjaxLibrary'));
			return;
		}

		if(is_admin()) {
			wp_enqueue_script($this->prefix.$script, $this->asset($script.'.js'), array('jquery', 'wp-color-picker'), SGRB_VERSION, true);
			wp_enqueue_media();
		}
		wp_enqueue_script($this->prefix.$script, $this->asset($script.'.js'), array('jquery'),SGRB_VERSION);
	}

	public function includeStyle($style)
	{
		wp_enqueue_style($this->prefix.$style, $this->asset($style.'.css'), array('wp-color-picker'), SGRB_VERSION);
	}

	public function includeAdminScriptsAndStyles($hook)
	{
		if (count($this->autoload['admin_scripts'])) {
			foreach ($this->autoload['admin_scripts'] as $script) {
				$this->includeScript($script);
			}
		}

		if (count($this->autoload['admin_styles'])) {
			foreach ($this->autoload['admin_styles'] as $style) {
				$this->includeStyle($style);
			}
		}

		// need to add this style to header (template css issue)
		wp_enqueue_style('sgrb_review/page/styles/review/save',  $this->app_url.'assets/page/styles/review/save.css', array(), 'all');
	}

	public function includeFrontScriptsAndStyles($hook)
	{
		if (count($this->autoload['front_scripts'])) {
			foreach ($this->autoload['front_scripts'] as $script) {
				$this->includeScript($script);
			}
		}

		if (count($this->autoload['front_styles'])) {
			foreach ($this->autoload['front_styles'] as $style) {
				$this->includeStyle($style);
			}
		}
		// need to add this style to header (template css issue)
		wp_enqueue_style('sgrb_page/review/styles/save',  $this->app_url.'assets/page/styles/review/save.css', array(), 'all');
	}

	public function addAjaxLibrary() {
		$html = "<script type=\"text/javascript\">\n";
		$html .= 'var ajaxurl = "'.admin_url('admin-ajax.php' ).'";'."\n";
		$html .= "</script>\n";

		echo $html;
	}

	public function actionToSlug($action)
	{
		if (isset($this->slugs[$action])) {
			return $this->slugs[$action];
		}

		return '';
	}

	private function dispatchAction($action, $param1, $param2)
	{
		$this->includeController(ucfirst($action[0]));

		$controllerName = strtoupper($this->prefix).ucfirst($action[0]).'Controller';

		$controller = new $controllerName();
		$controller->setController($action[0]);
		$controller->setAction($action[1]);
		return $controller->dispatch($param1, $param2);
	}

	public function loadMenu()
	{
		$this->loadMenuItems('menu_items');
	}

	public function loadNetworkAdminMenu()
	{
		$this->loadMenuItems('network_admin_menu_items');
	}

	public function loadMenuItems($key)
	{
		$autoload = $this->autoload;
		foreach ($autoload[$key] as $menu_item) {
			$menu_item_slug = $this->prefix.$menu_item['id'];
			$menu_item_func = array($this, $menu_item['id']);
			$this->menuActions[$menu_item['id']] = array($menu_item['controller'], $menu_item['action']);
			$this->slugs[$menu_item['controller'].'/'.$menu_item['action']] = $menu_item_slug;
			add_menu_page(
				$menu_item['page_title'],
				$menu_item['menu_title'],
				$menu_item['capability'],
				$menu_item_slug,
				$menu_item_func,
				$menu_item['icon']
			);
			if (count($menu_item['submenu_items'])) {
				foreach ($menu_item['submenu_items'] as $submenu_item) {
					$submenu_item_slug = $this->prefix.$submenu_item['id'];
					$submenu_item_func = array($this, $submenu_item['id']);
					$this->menuActions[$submenu_item['id']] = array($submenu_item['controller'], $submenu_item['action']);
					$this->slugs[$submenu_item['controller'].'/'.$submenu_item['action']] = $submenu_item_slug;
					add_submenu_page(
						$menu_item_slug,
						$submenu_item['page_title'],
						$submenu_item['menu_title'],
						$submenu_item['capability'],
						$submenu_item_slug,
						$submenu_item_func
					);
				}
			}
		}
	}
}
