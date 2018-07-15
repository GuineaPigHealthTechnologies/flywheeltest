<?php

class TestHelperFunctions {

	public static $mailcatcher_url = 'http://waitlisttest.local:4033/';

	/**
	 * Navigate tester to product listing screen
	 *
	 * @param AcceptanceTester $I
	 */
	public static function VisitProductListingPage( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/wp-admin/edit.php?post_type=product' );
	}

	/**
	 * Navigate tester to specified tab on the admin tab of the edit product screen
	 *
	 * @param AcceptanceTester $I
	 * @param string           $product_type
	 * @param string           $tab
	 */
	public static function VisitWaitlistAdminTab( AcceptanceTester $I, $product_type = 'simple', $tab = 'waitlist' ) {
		$I->loginAsAdmin();
		if ( 'variable' == $product_type ) {
			$I->amOnPage( '/wp-admin/post.php?post=10&action=edit' );
		} else if ( 'grouped' == $product_type ) {
			$I->amOnPage( '/wp-admin/post.php?post=32&action=edit' );
		} else {
			$I->amOnPage( '/wp-admin/post.php?post=21&action=edit' );
		}
		if ( $tab ) {
			$I->click( '.wcwl_waitlist_menu_tab a' );
			if ( 'variable' == $product_type ) {
				$I->click( '.wcwl_header_wrap:first-child' );
				$I->wait( 1 );
			}
			$I->click( 'li[data-tab=' . $tab . ']' );
		}
	}

	/**
	 * Add given users to the waitlist within the current edit product page
	 *
	 * @param AcceptanceTester $I
	 * @param array            $users
	 */
	public static function AddUsersToWaitlist( AcceptanceTester $I, array $users ) {
		$emails = implode( ',', $users );
		$I->click( 'button.wcwl_add' );
		$I->dontSeeElement( '.wcwl_actions' );
		$I->fillField( 'input.wcwl_email', $emails );
		$I->click( '.wcwl_email_add_user' );
		$I->wait( 1 );
	}

	/**
	 * Loop through array of output items and check each one is displayed
	 *
	 * @param AcceptanceTester $I
	 * @param array            $output
	 */
	public static function SeeExpectedOutput( AcceptanceTester $I, array $output ) {
		foreach ( $output as $item ) {
			$I->see( $item );
		}
	}

	/**
	 * Process given action on given tab using given selection
	 *
	 * @param AcceptanceTester $I
	 * @param string           $tab
	 * @param string           $action
	 * @param string           $select
	 */
	public static function ProcessAction( AcceptanceTester $I, $tab = 'waitlist', $action, $select ) {
		if ( $action ) {
			$I->selectOption( '.' . $tab . '.current .wcwl_action', $action );
		}
		if ( $select ) {
			$I->checkOption( '.' . $tab . '.current ' . $select );
		}
		$I->click( '.' . $tab . '.current .wcwl_actions button' );
		$I->wait( 1 );
	}

	/**
	 * Update current product's stock quantity
	 *
	 * @param AcceptanceTester $I
	 * @param int              $quantity
	 */
	public static function UpdateProductStockLevel( AcceptanceTester $I, $quantity ) {
		$I->click( '.inventory_tab a' );
		$I->checkOption( 'input[name=_manage_stock]' );
		$I->fillField( 'input[name=_stock]', $quantity );
		$I->scrollTo( '#submitdiv' );
		$I->click( 'input#publish' );
	}

	/**
	 * Navigate tester to frontend product page depending on options given
	 *
	 * @param AcceptanceTester $I
	 * @param bool             $logged_in
	 * @param string           $product_type
	 * @param string           $stock_status
	 */
	public static function VisitPage( AcceptanceTester $I, $logged_in = true, $product_type = 'simple', $stock_status = 'outofstock' ) {
		if ( $logged_in ) {
			$I->loginAsAdmin();
		}
		if ( 'simple' == $product_type ) {
			if ( 'outofstock' == $stock_status ) {
				$I->amOnPage( '/product/t-shirt-with-logo/' );
			} else {
				$I->amOnPage( '/product/beanie-with-logo/' );
			}
		}
		if ( 'variable' == $product_type ) {
			$I->amOnPage( '/product/hoodie/' );
		}
		if ( 'grouped' == $product_type ) {
			$I->amOnPage( '/product/logo-collection/' );
		}
		if ( 'shop' == $product_type ) {
			$I->amOnPage( '/shop/' );
		}
		if ( 'account' == $product_type ) {
			$I->amOnPage( '/my-account/woocommerce-waitlist/' );
		}
		if ( 'shortcode' == $product_type ) {
			$I->amOnPage( '/waitlist-shortcode/' );
		}
	}

	/**
	 * Reset waitlist options in database to default values
	 *
	 * @param AcceptanceTester $I
	 */
	public static function ResetOptions( AcceptanceTester $I ) {
		$options = array(
			'woocommerce_waitlist_show_on_shop'           => 'no',
			'woocommerce_waitlist_registered_user_opt-in' => 'no',
			'woocommerce_waitlist_new_user_opt-in'        => 'no',
			'woocommerce_waitlist_registration_needed'    => 'no',
			'woocommerce_waitlist_notify_admin'           => 'no'
		);
		foreach ( $options as $name => $value ) {
			$I->updateInDatabase( 'wp_options', array( 'option_value' => $value ), array( 'option_name' => $name ) );
		}
	}

	/**
	 * Run steps to check opt in errors work on current page
	 *
	 * @param AcceptanceTester $I
	 * @param string           $page
	 */
	public static function CheckOptinErrors( AcceptanceTester $I, $page = 'product' ) {
		$button = 'a.woocommerce_waitlist';
		if ( 'shop' == $page ) {
			$button = '#wcwl-product-22';
		}
		$I->click( $button );
		$I->seeNumberOfElements( '.wcwl_error_highlight', 2 );
		$I->checkOption( '#wcwl_optin' );
		$I->click( $button );
		$I->seeNumberOfElements( '.wcwl_error_highlight', 1 );
		$I->uncheckOption( '#wcwl_optin' );
		$I->fillField( 'input[name=wcwl_email]', 'new@testuser.com' );
		$I->click( $button );
		$I->seeNumberOfElements( '.wcwl_error_highlight', 1 );
	}

	/**
	 * Add user to waitlist on frontend
	 *
	 * @param AcceptanceTester $I
	 * @param                  $user
	 * @param string           $product_type
	 */
	public static function AddUserToWaitlist( AcceptanceTester $I, $user, $product_type = 'simple' ) {
		if ( 'variable' == $product_type ) {
			$I->selectOption( 'select#pa_color', 'Blue' );
			$I->wait( 1 );
		}
		if ( 'grouped' == $product_type ) {
			$I->checkOption( '.wcwl_checkbox' );
		}
		$I->fillField( 'input[name=wcwl_email]', $user );
		$I->click( 'a.woocommerce_waitlist' );
	}

	/**
	 * Check emails were sent out to customers (caught by mailhog)
	 *
	 * @param AcceptanceTester $I
	 * @param                  $quantity number of emails expected
	 * @param array            $subjects email subjects
	 */
	public static function CheckEmails( AcceptanceTester $I, $quantity, array $subjects ) {
		$I->amOnUrl( TestHelperFunctions::$mailcatcher_url );
		foreach ( $subjects as $subject ) {
			$I->see( $subject );
		}
		$I->seeNumberOfElements( '.msglist-message', $quantity );
		self::ClearEmails( $I, true );
	}

	/**
	 * Clear all caught emails
	 *
	 * @param AcceptanceTester $I
	 * @param bool             $in_mailcatcher
	 */
	public static function ClearEmails( AcceptanceTester $I, $in_mailcatcher = false ) {
		if ( ! $in_mailcatcher ) {
			$I->amOnUrl( TestHelperFunctions::$mailcatcher_url );
		}
		$I->click( 'Delete all messages' );
		$I->wait( 1 );
		$I->click( '.btn-danger' );
		$I->wait( 1 );
	}
}