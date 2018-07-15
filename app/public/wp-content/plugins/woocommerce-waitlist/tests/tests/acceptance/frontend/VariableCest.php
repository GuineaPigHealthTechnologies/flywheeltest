<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Frontend_Variable_Product_Page_Cest {

	// Generic
	public function WaitlistElementsDisplayForOutOfStockProducts( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		$I->dontSeeElement( 'a.woocommerce_waitlist' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->seeElement( 'a.woocommerce_waitlist' );
		$I->seeElement( 'button.single_add_to_cart_button.disabled' );
	}

	// Logged In
	public function LoggedInUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function LoggedInUserCanLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been removed from the waitlist for this product' );
	}

	// Logged Out
	public function NewLoggedOutUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com', 'variable' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function ExistingLoggedOutUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com', 'variable' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function EnteringInvalidEmailShowsError( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'newtestuser.com', 'variable' );
		$I->seeElement( '.wcwl_email.wcwl_error_highlight' );
	}

	// Opt in logged in
	public function OptinElementsShowForLoggedInUser( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registered_user_opt-in' ) );
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_new_user_opt-in' ) );
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'By ticking this box you agree to receive waitlist communications by email' );
		$I->see( 'Join Waitlist' );
	}

	public function OptinOptionsAllowLoggedInUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->checkOption( '#wcwl_optin' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function OptinOptionsAllowLoggedInUserToLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been removed from the waitlist for this product' );
	}

	public function OptinOptionsShowErrorWhenLeftUnchecked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->click( 'a.woocommerce_waitlist' );
		$I->seeElement( '.wcwl_error_highlight' );
	}

	//Opt in logged out
	public function OptinElementsShowForLoggedOutUser( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->seeElement( 'input[name=wcwl_email]' );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'By ticking this box you agree to an account being created using the given email address and to receive waitlist communications by email' );
		$I->see( 'Join Waitlist' );
	}

	public function OptinOptionsAllowLoggedOutUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->checkOption( '#wcwl_optin' );
		$I->fillField( 'input[name=wcwl_email]', 'new@testuser.com' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been added to the waitlist for this product' );
	}

	public function OptinOptionsShowErrorsWhenRequired( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		TestHelperFunctions::CheckOptinErrors( $I );
	}

	// In Stock
	public function UserCanAddInStockProductToCart( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'variable' );
		$I->selectOption( 'select#pa_color', 'Green' );
		$I->wait( 1 );
		$I->click( 'button.single_add_to_cart_button' );
		$I->see( 'has been added to your cart.' );
		$I->dontSee( 'You have been added to the waitlist for this product' );
		$I->dontSee( 'You have been removed from the waitlist for this product' );
	}

	//Registration needed
	public function LoggedOutUserCantJoinWaitlistIfRegistrationRequired( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registration_needed' ) );
		TestHelperFunctions::VisitPage( $I, false, 'variable' );
		$I->selectOption( 'select#pa_color', 'Blue' );
		$I->wait( 1 );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You must register to use the waitlist feature. Please login or create an account' );
	}

	// Reset Options
	public function ResetWaitlistOptions( AcceptanceTester $I ) {
		TestHelperFunctions::ResetOptions( $I );
	}
}
