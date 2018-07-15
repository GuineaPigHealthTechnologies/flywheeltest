<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Frontend_Grouped_Product_Cest {
	// Logged In
	public function LoggedInUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->checkOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function LoggedInUserCanLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->seeCheckboxIsChecked( '#wcwl_checked_14' );
		$I->uncheckOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function ClearEmails( AcceptanceTester $I ) {
		TestHelperFunctions::ClearEmails( $I );
	}

	// Admin Email Sent Out
	public function AdminEmailSentWhenOptionEnabled( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_notify_admin' ) );
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->checkOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->uncheckOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		TestHelperFunctions::CheckEmails( $I, 1, array( 'A user has just joined a waitlist!' ) );
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'no' ), array( 'option_name' => 'woocommerce_waitlist_notify_admin' ) );
	}

	// Logged Out
	public function LoggedOutNewUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com', 'grouped' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function LoggedOutExistingUserCanJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com', 'grouped' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function InvalidEmailSubmissionShowsError( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'newtestuser.com', 'grouped' );
		$I->seeElement( '.wcwl_email.wcwl_error_highlight' );
	}

	// Opt in logged in
	public function OptinElementsShowForLoggedInUser( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registered_user_opt-in' ) );
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_new_user_opt-in' ) );
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'By ticking this box you agree to receive waitlist communications by email' );
		$I->see( 'Join Waitlist' );
	}

	public function OptinOptionsAllowLoggedInUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->checkOption( '#wcwl_optin' );
		$I->checkOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function OptinOptionsAllowLoggedInUserToLeaveWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->checkOption( '#wcwl_optin' );
		$I->seeCheckboxIsChecked( '#wcwl_checked_14' );
		$I->uncheckOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function OptinOptionsShowErrorsWhenUnchecked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'grouped' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->seeElement( '.wcwl_error_highlight' );
	}

	//Opt in logged out
	public function OptinElementsShowForLoggedOutUser( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		$I->seeElement( 'input[name=wcwl_email]' );
		$I->seeElement( '#wcwl_optin' );
		$I->see( 'By ticking this box you agree to an account being created using the given email address and to receive waitlist communications by email' );
		$I->see( 'Join Waitlist' );
	}

	public function OptinOptionsAllowLoggedOutUserToJoinWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		$I->checkOption( '#wcwl_optin' );
		TestHelperFunctions::AddUserToWaitlist( $I, 'new@testuser.com', 'grouped' );
		$I->see( 'You have updated the selected waitlist/s' );
	}

	public function OptinOptionsShowErrorsWhenRequiredForLoggedOutUsers( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		$I->checkOption( '#wcwl_checked_14' );
		TestHelperFunctions::CheckOptinErrors( $I );
	}
	
	// In Stock
	public function UserCanAddInStockProductToCart( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		$I->click( 'button.single_add_to_cart_button' );
		$I->see( 'Please choose the quantity of items you wish to add to your cart' );
		$I->dontSee( 'You have updated the selected waitlist/s' );
		$I->fillField( 'input.input-text.qty.text', 1 );
		$I->click( 'button.single_add_to_cart_button' );
		$I->see( 'has been added to your cart.' );
		$I->dontSee( 'You have updated the selected waitlist/s' );
	}

	//Registration needed
	public function LoggedOutUserCantJoinWaitlistIfRegistrationIsRequired( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_registration_needed' ) );
		TestHelperFunctions::VisitPage( $I, false, 'grouped' );
		$I->checkOption( '#wcwl_checked_14' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You must register to use the waitlist feature. Please login or create an account' );
	}

	// Reset Options
	public function ResetWaitlistOptions( AcceptanceTester $I ) {
		TestHelperFunctions::ResetOptions( $I );
	}
}
