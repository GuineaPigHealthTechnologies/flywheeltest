<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @package   WC-Product-Reviews-Pro/Functions
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Global functions for review contributions
 *
 * @since 1.0.0
 */

/**
 * Main function for returning contributions, uses the WC_Product_Reviews_Pro_Contribution_Factory class.
 *
 * @since 1.0.0
 * @param null|\WP_Comment|int $the_contribution comment object or comment ID of the contribution.
 * @param array $args (default: array()) Contains all arguments to be used to get this contribution.
 * @return \WC_Contribution
 */
function wc_product_reviews_pro_get_contribution( $the_contribution = null, $args = array() ) {
	return wc_product_reviews_pro()->get_contribution_factory_instance()->get_contribution( $the_contribution, $args );
}


/**
 * Get number of comments, optionally filtered by type
 *
 * @param array $comments
 * @param string|null $type
 * @return int The number of comments
 */
function wc_product_reviews_pro_get_comment_count( $comments, $type = null ) {

	if ( ! $type ) {
		return count( $comments );
	}

	$count = 0;

	foreach ( $comments as $comment ) {

		if ( $type === $comment->comment_type ) {
		  $count++;
		}
	}

	return $count;
}


/**
 * Get number of comments, filtered by type(s).
 *
 * @since 1.0.0
 *
 * @param int $post_id the post (product) ID
 * @param string|array $types contribution type(s)
 * @return int contribution count
 */
function wc_product_reviews_pro_get_comments_number( $post_id = 0, $types = array() ) {
	global $wpdb;

	$select     = $wpdb->prepare( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1", $post_id );
	$where_type = ! empty( $types ) ? $wpdb->prepare( " AND comment_type IN(" . implode( ', ', array_fill( 0, count( (array) $types ), '%s' ) ) . ")", (array) $types ) : '';
	$count      = $wpdb->get_var( $select . $where_type );

	return (int) $count;
}


/**
 * Get top level contribution (comment) in a comments thread
 *
 * @since 1.3.0
 * @param \WC_Contribution $contribution The comment to look for topmost ancestor
 * @return \WC_Contribution The top level contribution in a comment thread
 */
function wc_product_reviews_pro_get_top_level_contribution( $contribution ) {

	if ( ! isset( $contribution->comment ) ) {
		return $contribution;
	}

	$comment = $contribution->comment;

	while ( $comment->comment_parent > 0 ) {
		$comment = get_comment( $comment->comment_parent );
	}

	return wc_product_reviews_pro_get_contribution( $comment );
}


/**
 * Helper function to trim the contribution content in widgets
 * as wp_trim_words would strip all HTML tags, which we don't want
 *
 * @since 1.6.4
 * @param string $content The content HTML
 * @param int $word_count The number of words to use in the excerpt
 * @return string Trimmed content
 */
function wc_product_reviews_pro_trim_contribution( $content, $word_count ) {

	// ensure word count is always a positive integer
	$word_count = absint( $word_count );

	if ( str_word_count( $content ) > $word_count ) {

		$words   = str_word_count( $content, 2 );
		$pos     = array_keys( $words );
		$content = substr( $content, 0, $pos[ $word_count ] ) . '&hellip;';
	}

	return $content;
}


/**
 * Returns the available contributions types.
 *
 * @since 1.10.0
 *
 * @return string[]
 */
function wc_product_reviews_pro_get_contribution_types() {
	return wc_product_reviews_pro()->get_contribution_factory_instance()->get_contribution_types();
}


/**
 * Returns enabled contribution types.
 *
 * @since 1.6.0
 *
 * @return string[] array of contribution types
 */
function wc_product_reviews_pro_get_enabled_contribution_types() {
	return wc_product_reviews_pro()->get_contribution_factory_instance()->get_enabled_contribution_types();
}


/**
 * Check if notification emails for new replies on contributions are enabled
 *
 * @since 1.3.0
 * @return bool
 */
function wc_product_reviews_pro_comment_notification_enabled() {

	$setting = get_option( 'woocommerce_wc_product_reviews_pro_new_comment_email_settings' );

	if ( isset( $setting['enabled'] ) ) {
		return 'no' !== $setting['enabled'];
	}

	return true;
}


/**
 * Get users subscribing to contribution replies
 *
 * @since 1.3.0
 * @param int|\WC_Contribution $contribution
 * @return array A list of user ids
 */
function wc_product_reviews_pro_get_comment_notification_subscribers( $contribution ) {

	if ( is_int( $contribution ) ) {
		$contribution = wc_product_reviews_pro_get_contribution( $contribution );
	}

	if ( isset( $contribution->id ) ) {

		$contribution = wc_product_reviews_pro_get_top_level_contribution( $contribution );
		$subscribers = get_comment_meta( $contribution->id, 'wc_product_reviews_pro_notify_users', true );

		if ( is_array( $subscribers ) ) {
			return $subscribers;
		}

	}

	return array();
}


/**
 * Subscribe a user to new replies on contribution
 *
 * @since 1.3.0
 * @param string $action Either 'subscribe' or 'unsubscribe'
 * @param int $user_id The user id to manage
 * @param \WC_Contribution $contribution Contribution to subscribe to
 * @param string $type The guest or registered user
 * @return null|\WC_Contribution
 */
function wc_product_reviews_pro_add_comment_notification_subscriber( $action, $user_id, $contribution, $type = 'user' ) {

	if ( ! in_array( $action, array( 'subscribe', 'unsubscribe' ), true ) ) {
		return null;
	}

	if ( isset( $contribution->id ) && $user_id ) {

		if ( $contribution->comment->comment_parent > 0 ) {
			$contribution = wc_product_reviews_pro_get_top_level_contribution( $contribution );
		}

		// checking if subscriber is registered user or a guest
		if ( 'user' === $type ) {

			$id	   = (int) $user_id;
			$saved = get_comment_meta( $contribution->id, 'wc_product_reviews_pro_notify_users', true );
			$users = array( $id );

			if ( ! empty( $saved ) && is_array( $saved ) ) {
				$users = array_unique( array_merge( $saved, $users ) );
			}

			if ( 'unsubscribe' === $action ) {
				$users = array_diff( $users, array( $id ) );
			}

			update_comment_meta( $contribution->id, 'wc_product_reviews_pro_notify_users', $users );

		} elseif ( 'guest' === $type ) {

			$id	   = $user_id;
			$saved = get_comment_meta( $contribution->id, 'wc_product_reviews_pro_notify_emails', true );
			$users = array( $id );

			if ( ! empty( $saved ) && is_array( $saved ) ) {
				$users = array_unique( array_merge( $saved, $users ) );
			}

			if ( 'unsubscribe' === $action ) {
				$users = array_diff( $users, array( $id ) );
			}

			update_comment_meta( $contribution->id, 'wc_product_reviews_pro_notify_emails', $users );

		}

		return $contribution;
	}

	return null;
}


/**
 * Get an unsubscribe link to contribution new comments email notifications
 *
 * @since 1.3.0
 * @param \WP_User $user
 * @param \WC_Contribution $contribution
 * @param \WC_Product $product
 * @return string URL with variables
 */
function wc_product_reviews_pro_get_comment_notification_unsubscribe_link( $user, $contribution, $product ) {

	$type = '';

	// checking if user_id is numeric or an email address
	if ( is_numeric( $user->ID ) ) {
		$type = 'user';
	} elseif ( is_email( $user->ID ) ) {
		$type = 'guest';
	}

	return add_query_arg(
		array(
			'wc_prp_comments_notifications' => 'unsubscribe',
			'user'                          => $user->ID,
			'contribution'                  => $contribution->id,
			'type'                          => $type,
		),
		$product->get_permalink()
	);
}


/**
 * Get a contribution type class instance
 *
 * @since 1.0.0
 * @param string $type Contribution type
 * @return \WC_Product_Reviews_Pro_Contribution_Type Object
 */
function wc_product_reviews_pro_get_contribution_type( $type ) {
	return new WC_Product_Reviews_Pro_Contribution_Type( $type );
}


/**
 * Get product review count
 *
 * @since 1.0.0
 * @param  int $product_id Product ID
 * @return int Number of reviews for this product
 */
function wc_product_reviews_pro_get_review_count( $product_id ) {

	if ( false === ( $count = get_transient( 'wc_product_reviews_pro_review_count_' . $product_id ) ) ) {

		global $wpdb;

		$count = $wpdb->get_var( $wpdb->prepare( "
		  SELECT COUNT(comment_ID) FROM $wpdb->comments
		  WHERE comment_post_ID = %d
		  AND comment_approved = '1'
		  AND comment_type = 'review'
		", $product_id ) );

		set_transient( 'wc_product_reviews_pro_review_count_' . $product_id, $count, YEAR_IN_SECONDS );
	}

	return $count;
}


/**
 * Get product's highest rating count
 *
 * @since 1.0.0
 * @param  int $product_id Product ID
 * @return int Highest rating for this product
 */
function wc_product_reviews_pro_get_highest_rating( $product_id ) {

	if ( false === ( $rating = get_transient( 'wc_product_reviews_pro_highest_rating_' . $product_id ) ) ) {

		global $wpdb;

		$rating = $wpdb->get_var( $wpdb->prepare("
		  SELECT MAX(meta_value) FROM $wpdb->commentmeta
		  LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		  WHERE comment_post_ID = %d
		  AND comment_approved = '1'
		  AND comment_type = 'review'
		  AND meta_key = 'rating'
		  AND meta_value > 0
		", $product_id ) );

		set_transient( 'wc_product_reviews_pro_highest_rating_' . $product_id, $rating, YEAR_IN_SECONDS );
	}

	return $rating;
}


/**
 * Get product's lowest rating count
 *
 * @since 1.0.0
 * @param  int $product_id Product ID
 * @return int Lowest rating for this product
 */
function wc_product_reviews_pro_get_lowest_rating( $product_id ) {

	if ( false === ( $rating = get_transient( 'wc_product_reviews_pro_lowest_rating_' . $product_id ) ) ) {

		global $wpdb;

		$rating = $wpdb->get_var( $wpdb->prepare("
		  SELECT MIN(meta_value) FROM $wpdb->commentmeta
		  LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
		  WHERE comment_post_ID = %d
		  AND comment_approved = '1'
		  AND comment_type = 'review'
		  AND meta_key = 'rating'
		  AND meta_value > 0
		", $product_id ) );

		set_transient( 'wc_product_reviews_pro_lowest_rating_' . $product_id, $rating, YEAR_IN_SECONDS );
	}

	return $rating;
}


/**
 * Get the currently applied comment filters
 *
 * @since 1.0.0
 * @return array|null
 */
function wc_product_reviews_pro_get_current_comment_filters() {

	$comments_filter = isset( $_REQUEST['comments_filter'] ) ? $_REQUEST['comments_filter'] : null;
	$filters = array();

	if ( $comments_filter ) {
		parse_str( $comments_filter, $filters );
	}

	return array_filter( $filters );
}


/**
 * Get the form field value from session
 *
 * @since 1.0.0
 * @param string $key
 * @return mixed|null
 */
function wc_product_reviews_pro_get_form_field_value( $key ) {
	return isset( $_POST[ $key ] ) ? $_POST[ $key ] : null;
}

/**
 * Check if review update confirmation emails on contributions are enabled.
 *
 * @since 1.8.0
 *
 * @return bool
 */
function wc_product_reviews_pro_review_update_confirmation_enabled() {

	$setting = get_option( 'woocommerce_wc_product_reviews_pro_review_update_confirmation_email_settings' );
	$enabled = true;

	if ( isset( $setting['enabled'] ) && 'no' === $setting['enabled'] ) {
		$enabled = false;
	}

	return $enabled;
}

/**
 * Get user's review count on particular product.
 *
 * @since 1.8.0
 *
 * @param int $user_id
 * @param int $product_id
 *
 * @return int
 */
function wc_product_reviews_pro_get_user_review_count( $user_id, $product_id ) {
	global $wpdb;

	// guest user check
	if ( ! $user_id ) {
		return 0;
	}

	$review_count = $wpdb->get_var( $wpdb->prepare("
		  SELECT COUNT(comment_ID) FROM $wpdb->comments
		  WHERE comment_post_ID = %d
		  AND user_id = %d
		  AND comment_type = 'review'
          AND comment_approved != 'trash'
		", $product_id, $user_id ) );

	return (int) $review_count;
}


/**
 * Update review.
 *
 * @since 1.8.0
 *
 * @param array $review_data
 * @return bool
 */
function wc_product_reviews_pro_update_review_data( $review_data ) {

	$is_updated = false;

	// checking if review_content is now empty when updating the review
	if ( ! empty( $review_data['review_content'] ) ) {

		// Updating comment (review)
		$commentarr = array(
			'comment_ID'         => $review_data['comment_id'],
			'comment_content'    => $review_data['review_content'],
			'comment_author_url' => ( ! empty( $review_data['attachment_url'] ) ) ? $review_data['attachment_url'] : '',
			'comment_approved'   => ( true === wc_product_reviews_pro_check_review_moderation() ) ? 0 : 1,
		);

		// update user_id only if user is registered
		if ( ! empty( $review_data['user_id'] ) && is_numeric( $review_data['user_id'] ) ) {
			$commentarr['user_id'] = $review_data['user_id'];
		}

		$review_update = wp_update_comment( $commentarr );

		if ( $review_update ) {

			// checking if comment has any attachment url file
			if ( ! empty( $review_data['attachment_url'] ) ) {

				wc_product_reviews_pro_delete_old_contribution_attachment( $review_data['comment_id'] );

				update_comment_meta( $review_data['comment_id'], 'attachment_url', $review_data['attachment_url'] );
				update_comment_meta( $review_data['comment_id'], 'attachment_type', $review_data['attachment_type'] );
				delete_comment_meta( $review_data['comment_id'], 'attachment_id' );

			} elseif ( ! empty( $review_data['files'] ) ) {

				$attachment_id = wc_product_reviews_pro_upload_review_attachment( $review_data['files'] );

				// checking if media is uploaded successfully
				if ( is_wp_error( $attachment_id ) ) {
					return false;
				} else {

					wc_product_reviews_pro_delete_old_contribution_attachment( $review_data['comment_id'] );

					update_comment_meta( $review_data['comment_id'], 'attachment_id', $attachment_id );
					update_comment_meta( $review_data['comment_id'], 'attachment_type', $review_data['attachment_type'] );
					delete_comment_meta( $review_data['comment_id'], 'attachment_url' );

				}

			} elseif ( ! empty( $review_data['attachment_id'] ) ) {

				wc_product_reviews_pro_delete_old_contribution_attachment( $review_data['comment_id'] );

				update_comment_meta( $review_data['comment_id'], 'attachment_id', $review_data['attachment_id'] );
				update_comment_meta( $review_data['comment_id'], 'attachment_type', $review_data['attachment_type'] );
				delete_comment_meta( $review_data['comment_id'], 'attachment_url' );

			} elseif ( '0' !== $review_data['delete_attachment'] ) {
				delete_comment_meta( $review_data['comment_id'], 'attachment_url' );
			} else {

				wc_product_reviews_pro_delete_old_contribution_attachment( $review_data['comment_id'] );

				delete_comment_meta( $review_data['comment_id'], 'attachment_url' );
				delete_comment_meta( $review_data['comment_id'], 'attachment_id' );
				delete_comment_meta( $review_data['comment_id'], 'attachment_type' );

			}

			// update comment meta
			update_comment_meta( $review_data['comment_id'], 'title', $review_data['review_title'] );
			update_comment_meta( $review_data['comment_id'], 'rating', $review_data['rating'] );
			delete_comment_meta( $review_data['comment_id'], 'new_review_data' );

			$is_updated = true;
		}
	}

	return $is_updated;
}


/**
 * Delete old comment attachments.
 *
 * @since 1.8.0
 * @param int $comment_id
 */
function wc_product_reviews_pro_delete_old_contribution_attachment( $comment_id ) {

	$old_attachment_id = get_comment_meta( $comment_id, 'attachment_id', true );

	if ( ! empty( $old_attachment_id ) ) {
		wp_delete_attachment( $old_attachment_id );
	}
}


/**
 * Upload file object.
 *
 * @since 1.8.0
 * @param array $file_obj
 * @return int|object
 */
function wc_product_reviews_pro_upload_review_attachment( $file_obj ) {

	// these files need to be included as dependencies when on the front end
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	return media_handle_sideload( $file_obj, 0 );
}


/**
 * Get review update confirmation link.
 *
 * @since 1.8.0
 *
 * @param \WP_User $user
 * @param \WC_Contribution $contribution
 * @param \WC_Product $product
 * @return string URL with variables
 */
function wc_product_reviews_pro_get_review_update_confirmation_link( $user, $contribution, $product ) {

	$nonce = wp_create_nonce( 'wc_prp_update_review_' . $contribution->id );

	return add_query_arg(
		array(
			'wc_prp_update_review' => 'update',
			'user'                 => $user->ID,
			'contribution'         => $contribution->id,
			'_wpnonce'             => $nonce,
		),
		$product->get_permalink()
	);
}


/**
 * Get new comment data and displaying it in the mail.
 *
 * @since 1.8.0
 *
 * @param $contribution_id
 * @return mixed
 */
function wc_product_reviews_pro_get_review_update_data( $contribution_id ) {
	return get_comment_meta( $contribution_id, 'new_review_data', true );
}


/**
 * Checking if moderation is enabled for review.
 *
 * @since 1.8.0
 *
 * @return bool
 */
function wc_product_reviews_pro_check_review_moderation() {

	$moderation = false;

	// checking if current user does not have moderate_comments capability
	if ( ! current_user_can( 'moderate_comments' ) ) {
		if ( 'yes' === get_option('wc_product_reviews_pro_contribution_moderation') ) {
			$moderation = true;
		} elseif ( '1' === get_option('comment_moderation') ) {
			$moderation = true;
		}
	}

	return $moderation;
}


/**
 * Checking if contribution form is enabled when trying to update the review.
 *
 * @since 1.8.0
 *
 * @param string $type Contribution type
 * @param \WC_Product $product product object
 * @return bool
 */
function wc_product_reviews_pro_is_contribution_form_enabled( $type, $product ) {

	$form_visible = true;

	// disable the form if review updates are disabled and the user has already left one
	if ( 'review' === $type
	     && wc_product_reviews_pro_get_user_review_count( get_current_user_id(), $product->get_id() )
	     && ! wc_product_reviews_pro_review_update_confirmation_enabled() ) {

		$form_visible = false;
	}

	// default enable the form if user is not logged in
	if ( ! is_user_logged_in() ) {
		$form_visible = true;
	}

	return $form_visible;
}
