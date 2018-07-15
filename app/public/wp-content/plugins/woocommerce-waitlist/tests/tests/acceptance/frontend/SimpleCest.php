<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Frontend_Simple_Product_Page_Cest {

	// Generic
	public function AddToCartButtonIsHiddenWhenProductOutOfStock( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		$I->dontSeeElement( 'a.single_add_to_cart_button' );
	}

	// Logged Out
	public function WaitlistElementsShowForLoggedOutUser( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		$I->see( 'Out of stock - Join the waitlist to be emailed when this product becomes available' );
		$I->seeElement( 'input[name=wcwl_email]' );
		$I->seeElement( 'a.woocommerce_waitlist' );
	}

	public function NewLoggedOutUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function ExistingLoggedOutUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function EnteringInvalidEmailShowsError( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'newtestuser.com' );
		$I->seeElement( '.wcwl_email.wcwl_error_highlight' );
	}

	// Logged In
	public function WaitlistElementsShowForLoggedInUser( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->see( 'Out of stock - Join the waitlist to be emailed when this product becomes available' );
		$I->seeElement( 'a.woocommerce_waitlist' );
	}

	public function LoggedInUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function LoggedInUserCanLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been removed from the waitlist for this product' );
	}

	// Opt in logged in
	public function OptinElementsShowForLoggedInUser( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registered_user_opt-in' ) );
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_new_user_opt-in' ) );
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'By ticking this box you agree to receive waitlist communications by email' );
		$I->see( 'Join Waitlist' );
	}

	public function OptinOptionsAllowLoggedInUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->checkOption( '#wcwl_optin' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function OptinOptionsAllowLoggedInUserToLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been removed from the waitlist for this product' );
	}

	public function OptinOptionsShowErrorWhenLeftUnchecked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->seeElement( '.wcwl_error_highlight' );
	}

	//Opt in logged out
	public function OptinElementsShowForLoggedOutUser( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		$I->seeElement( 'input[name=wcwl_email]' );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'By ticking this box you agree to an account being created using the given email address and to receive waitlist communications by email' );
		$I->see( 'Join Waitlist' );
	}

	public function OptinOptionsAllowLoggedOutUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		$I->checkOption( '#wcwl_optin' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function OptinOptionsShowErrorsWhenRequired( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		TestHelperFunctions::CheckOptinErrors( $I );
	}

	// In Stock
	public function UserCanAddInStockProductToCart( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'simple', 'instock' );
		$I->click( 'button[name=add-to-cart]' );
		$I->see( 'has been added to your cart.' );
		$I->dontSee( 'You have been added to the waitlist for this product' );
		$I->dontSee( 'You have been removed from the waitlist for this product' );
	}

	//Registration needed
	public function LoggedOutUserCantJoinWaitlistIfRegistrationRequired( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registration_needed' ) );
		TestHelperFunctions::VisitPage( $I, false, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You must register to use the waitlist feature. Please login or create an account' );
	}

	// Reset Options
	public function ResetWaitlistOptions( AcceptanceTester $I ) {
		TestHelperFunctions::ResetOptions( $I );
	}
}