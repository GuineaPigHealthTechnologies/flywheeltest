<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Frontend_Shop_Page_Cest {

	// Generic
	public function WaitlistElementsDontShowWhenNotEnabled( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->dontSee( 'Join Waitlist' );
	}

	public function WaitlistElementsShowWhenEnabled( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_show_on_shop' ) );
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->see( 'Join Waitlist' );
	}

	//Logged Out
	public function LoggedOutExistingUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->fillField( 'input[name=wcwl_email]', 'new@testuser.com' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function LoggedOutNewUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->fillField( 'input[name=wcwl_email]', 'new1@pie.co.de' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function ErrorShowsWhenLoggedOutUserEntersInvalidEmail( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->fillField( 'input[name=wcwl_email]', 'newpie.co.de' );
		$I->click( '#wcwl-product-22' );
		$I->seeElement( '.wcwl_error_highlight' );
	}

	// Logged in
	public function LoggedInUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'shop' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function LoggedInUserCanLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'shop' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been removed from the waitlist for this product' );
	}

	// Opt in logged in
	public function OptinElementsShowForLoggedInUser( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registered_user_opt-in' ) );
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_new_user_opt-in' ) );
		TestHelperFunctions::VisitPage( $I, true, 'shop' );
		$I->see( 'Join Waitlist' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->see( 'By ticking this box you agree to receive waitlist communications by email' );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'Confirm' );
	}

	public function OptinOptionsAllowLoggedInUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->checkOption( '#wcwl_optin' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function OptinOptionsAllowLoggedInUserToLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'shop' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been removed from the waitlist for this product' );
	}

	public function OptinOptionsShowErrorsWhenUnchecked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->click( '#wcwl-product-22' );
		$I->seeElement( '.wcwl_error_highlight' );
	}

	//Opt in logged out
	public function OptinElementsShowForLoggedOutUsers( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->seeElement( 'input[name=wcwl_email]' );
		$I->see( 'By ticking this box you agree to an account being created using the given email address and to receive waitlist communications by email' );
		$I->see( 'Confirm' );
	}

	public function OptinOptionsAllowLoggedOutUsersToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		$I->checkOption( '#wcwl_optin' );
		$I->fillField( 'input[name=wcwl_email]', 'new@testuser.com' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function OptinOptionsShowErrorsWhenRequired( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( 'li.post-22 a.wcwl_toggle_email' );
		TestHelperFunctions::CheckOptinErrors( $I, 'shop' );
	}

	//In Stock
	public function UserCanAddToCartTest( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( '.post-31 a.add_to_cart_button' );
		$I->wait( 2 );
		$I->see( 'View Cart' );
	}

	//Registration needed
	public function LoggedOutUserCantJoinWaitlistIfRegistrationRequired( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registration_needed' ) );
		TestHelperFunctions::VisitPage( $I, false, 'shop' );
		$I->click( '#wcwl-product-22' );
		$I->see( 'You must register to use the waitlist feature. Please login or create an account' );
	}

	// Reset Options
	public function ResetWaitlistOptions( AcceptanceTester $I ) {
		TestHelperFunctions::ResetOptions( $I );
	}
}