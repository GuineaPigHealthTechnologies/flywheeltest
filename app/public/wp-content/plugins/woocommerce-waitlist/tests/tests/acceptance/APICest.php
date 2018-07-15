<?php
require_once 'TestHelperFunctions.php';

class API_Cest {

	public function ClearEmails( AcceptanceTester $I ) {
		TestHelperFunctions::ClearEmails( $I );
	}

	public function TriggerInstockNotification( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'joey@pie.co.de' ) );
		TestHelperFunctions::UpdateProductStockLevel( $I, 5 );
	}

	// Have to set a name on the iFrame here to be able to switch to it, else we can't recognise elements within
	public function CheckEmailIsAsExpected( AcceptanceTester $I ) {
		$I->amOnUrl( TestHelperFunctions::$mailcatcher_url );
		$I->see( 'A product you are waiting for is back in stock' );
		$I->click( 'div.msglist-message.row.ng-scope' );
		$I->executeJS( "$('iframe').attr('name','emailFrame');" );
		$I->switchToIframe( 'emailFrame' );
		TestHelperFunctions::SeeExpectedOutput( $I, array( 'Polo is now back in stock at waitlist-test', 'Hi There,', 'Polo is now back in stock at waitlist-test. You have been sent this email because your email address was registered on a waitlist for this product.', 'If you would like to purchase Polo please visit the following link: http://waitlisttest.local/product/polo/' ) );
	}

	public function EnableAnalyticsData( AcceptanceTester $I ) {
		TestHelperFunctions::ClearEmails( $I );
		$I->amOnUrl( 'http://waitlisttest.local/wp-admin/admin.php?page=wc-settings&tab=email&section=pie_wcwl_waitlist_mailout' );
		$I->loginAsAdmin();
		$I->amOnUrl( 'http://waitlisttest.local/wp-admin/admin.php?page=wc-settings&tab=email&section=pie_wcwl_waitlist_mailout' );
		$I->checkOption( '#woocommerce_woocommerce_waitlist_mailout_waitlist_add_analytics' );
		$I->click( 'Save changes' );
	}

	public function TriggerNewInstockNotification( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::UpdateProductStockLevel( $I, 0 );
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'joey@pie.co.de' ) );
		TestHelperFunctions::UpdateProductStockLevel( $I, 5 );
	}

	public function AnalyticsIsAddedToEmailsAsExpected( AcceptanceTester $I ) {
		$I->amOnUrl( TestHelperFunctions::$mailcatcher_url );
		$I->see( 'A product you are waiting for is back in stock' );
		$I->click( 'div.msglist-message.row.ng-scope' );
		$I->executeJS( "$('iframe').attr('name','emailFrame');" );
		$I->switchToIframe( 'emailFrame' );
		$I->see( 'http://waitlisttest.local/product/polo/?utm_source=waitlist&utm_medium=email&utm_campaign=21&' );
	}

	public function UserRemovedFromWaitlistWhenProductBackInStock( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		$I->dontSee( 'joey@pie.co.de' );
	}

	public function UserAddedToArchiveWhenProductBackInStock( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		$I->see( 'joey@pie.co.de' );
	}

	public function WhenArchivingOptionDisabledUserNotAddedToArchive( AcceptanceTester $I ) {
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'no' ), array( 'option_name' => 'woocommerce_waitlist_archive_on' ) );
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::UpdateProductStockLevel( $I, 0 );
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'test@pie.co.de' ) );
		TestHelperFunctions::UpdateProductStockLevel( $I, 5 );
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		$I->dontSee( 'test@pie.co.de' );
		TestHelperFunctions::UpdateProductStockLevel( $I, 0 );
		$I->updateInDatabase( 'wp_options', array( 'option_value' => 'yes' ), array( 'option_name' => 'woocommerce_waitlist_archive_on' ) );
	}

	public function RemoveUsersFromWaitlistAndArchive( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		TestHelperFunctions::ProcessAction( $I, 'archive', 'wcwl_remove_archive', 'input[name=wcwl_select_all]' );
	}
}