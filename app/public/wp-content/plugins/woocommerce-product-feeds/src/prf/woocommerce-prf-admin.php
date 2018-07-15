<?php

class WoocommercePrfAdmin {

	/**
	 * Constructor.
	 *
	 * Registers some always used actions (Such as registering endpoints). Also checks to see
	 * if this is a feed request, and if so registers the hooks needed to generate the feed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_filter( 'comment_edit_redirect', array( $this, 'save_comment_meta' ), 1, 2 );
	}

	/**
	 * Show a metabox on the comment edit pages.
	 */
	public function add_meta_boxes() {
		if ( 'comment' == get_current_screen()->id && isset( $_GET['c'] ) ) {
			if ( ! $this->is_review_comment( $_GET['c'] ) ) {
				return;
			}
			add_meta_box( 'wc-prf-rating', __( 'Exclude from Product Review feed', 'woocommerce_gpf' ), array( $this, 'render_meta_box' ), 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Render the metabox on the comment edit pages.
	 */
	public function render_meta_box( $comment ) {
		$current = get_comment_meta( $comment->comment_ID, '_wc_prf_no_feed', true );
		?>
		<input type="checkbox" name="_wc_prf_no_feed" <?php checked( $current, 1 ); ?> id="_wc_prf_no_feed">
		<label for="_wc_prf_no_feed"><?php _e( 'Exclude from Product Review feed.', 'woocommerce_gpf' ); ?></label>
		<?php
	}

	/**
	 * Save the metabox info on the comment edit pages.
	 */
	public function save_comment_meta( $location, $comment_id ) {
		$value = isset( $_POST['_wc_prf_no_feed'] ) ? ( $_POST['_wc_prf_no_feed'] == 'on' ) : 0;
		if ( $value ) {
			update_comment_meta( $comment_id, '_wc_prf_no_feed', $value );
		} else {
			delete_comment_meta( $comment_id, '_wc_prf_no_feed' );
		}
		return $location;
	}

	private function is_review_comment( $comment_id ) {
		$meta = get_comment_meta( $comment_id, 'rating', true );
		return is_numeric( $meta );
	}
}
