<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Frontend_MyAccount_Shortcode_Cest {

	public function WaitlistTabShowsOnMyAccountPage( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'account' );
		$I->seeElement( '.woocommerce-MyAccount-navigation-link--woocommerce-waitlist' );
		$I->see( 'Your Waitlists' );
		$I->see( 'You have not yet joined the waitlist for any products.' );
		$I->see( 'Visit shop now!' );
		$I->see( 'Your email address is also stored on an archived waitlist for the following products:' );
	}

	public function WaitlistShortcodeDisplaysWaitlistElements( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'shortcode' );
		$I->see( 'Your Waitlists' );
		$I->see( 'You have not yet joined the waitlist for any products.' );
		$I->see( 'Visit shop now!' );
		$I->see( 'Your email address is also stored on an archived waitlist for the following products:' );
	}

	public function UserCanLeaveWaitlistsFromMyAccountWaitlistTab( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		TestHelperFunctions::VisitPage( $I, false, 'account' );
		$I->see( 'You are currently on the waitlist for the following products.' );
		$I->click( 'a.wcwl_remove_product' );
		$I->wait( 1 );
		$I->see( 'You have been removed from the waitlist for this product' );
		$I->seeInCurrentUrl( '/my-account/woocommerce-waitlist/' );
	}

	public function UserCanLeaveWaitlistsFromPageUsingWaitlistShortcode( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'simple' );
		$I->click( 'a.woocommerce_waitlist' );
		TestHelperFunctions::VisitPage( $I, false, 'shortcode' );
		$I->see( 'You are currently on the waitlist for the following products.' );
		$I->click( 'a.wcwl_remove_product' );
		$I->wait( 1 );
		$I->see( 'You have been removed from the waitlist for this product' );
		$I->seeInCurrentUrl( '/waitlist-shortcode/' );
	}

	public function UserCanRemoveThemselvesFromArchives( AcceptanceTester $I ) {
		TestHelperFunctions::VisitPage( $I, true, 'account' );
		$I->click( 'a#wcwl_remove_archives' );
		$I->wait( 1 );
		$I->see( 'You have been removed from all waitlist archives.' );
	}
}