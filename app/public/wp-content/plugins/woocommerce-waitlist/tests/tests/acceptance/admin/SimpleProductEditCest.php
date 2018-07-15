<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Admin_Simple_Product_Screen_Cest {

	/* WAITLIST TAB */
	public function WaitlistTabAppearsOnPage( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', '' );
		$I->seeElement( '.wcwl_waitlist_menu_tab' );
	}

	public function ClickingWaitlistTabShowsWaitlistElements( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::SeeExpectedOutput( $I, array( 'Waitlist', 'Archive', 'Options', 'Add new user', 'There are no users on the waiting list for this product.' ) );
		$I->dontSeeElement( '.wcwl_actions' );
	}

	public function AddSingleUserToWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'joey@pie.co.de' ) );
		TestHelperFunctions::SeeExpectedOutput( $I, array( 'The waitlist has been updated', 'joey@pie.co.de', date( 'j M, y' ), 'Add new user' ) );
		$I->seeNumberOfElements( '.wcwl_user_row', 1 );
		$I->seeElement( '.wcwl_actions' );
		$I->dontSee( 'There are no users on the waiting list for this product.' );
	}

	public function AddMultipleUsersToWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'new1@pie.co.de', 'new2@pie.co.de', 'new3@pie.co.de') );
		TestHelperFunctions::SeeExpectedOutput( $I, array( 'The waitlist has been updated', 'new1@pie.co.de', 'new2@pie.co.de', 'new3@pie.co.de' ) );
		$I->seeNumberOfElements( '.wcwl_user_row', 4 );
	}

	public function AddedUsersPersistOnWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		$I->seeNumberOfElements( '.wcwl_user_row', 4 );
	}

	public function InvalidEmailIsNotAccepted( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'invalid email' ) );
		$I->see( 'One or more emails entered appear to be invalid' );
		$I->click( '.wcwl_dismiss' );
		$I->dontSeeElement( '.wcwl_notice' );
	}

	public function UseCloseButtonToHideAddEmailFields( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		$I->click( 'button.wcwl_add' );
		$I->click( '.wcwl_back' );
		$I->see( 'Add new user' );
		$I->seeElement( '.wcwl_actions' );
	}

	public function ClearEmails( AcceptanceTester $I ) {
		TestHelperFunctions::ClearEmails( $I );
	}

	public function SendInStockNotificationsToUsersOnWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'new1@pie.co.de', 'new2@pie.co.de', 'new3@pie.co.de') );
		TestHelperFunctions::ProcessAction( $I, 'waitlist', 'wcwl_email_instock', 'input[name=wcwl_select_all]' );
		$I->see( 'The selected users have been sent an in stock notification' );
	}

	public function CheckEmailsSentToCustomersFromWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::CheckEmails( $I, 4, array( 'A product you are waiting for is back in stock', 'joey@pie.co.de', 'new1@pie.co.de', 'new2@pie.co.de', 'new3@pie.co.de' ) );
	}

	public function NoUserSelectedErrorShowsWhenRequired( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::ProcessAction( $I, 'waitlist', 'wcwl_email_instock', '' );
		$I->see( 'No users selected' );
	}

	public function NoActionSelectedErrorShowsWhenRequired( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::ProcessAction( $I, 'waitlist', '', 'input[name=wcwl_select_all]' );
		$I->see( 'No action selected' );
	}

	public function RemoveSingleUserFromWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::ProcessAction( $I, 'waitlist', 'wcwl_remove_waitlist', '.wcwl_user_checkbox:first-child' );
		$I->see( 'The selected user has been removed from the waitlist' );
		$I->dontSee( 'joey@pie.co.de' );
	}

	public function RemoveMultipleUsersFromWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		TestHelperFunctions::ProcessAction( $I, 'waitlist', 'wcwl_remove_waitlist', 'input[name=wcwl_select_all]' );
		TestHelperFunctions::SeeExpectedOutput( $I, array( 'The selected users have been removed from the waitlist', 'There are no users on the waiting list for this product.' ) );
		$I->dontSeeElement( '.wcwl_waitlist_table' );
	}

	public function CheckRemovedUsersRemainGoneFromWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I );
		$I->dontSeeElement( '.wcwl_waitlist_table' );
	}
	/* WAITLIST TAB END */
	/* ARCHIVE TAB START */
	public function ArchiveTabShowsWhenClicked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		$I->seeElement( '.archive.current' );
	}

	public function AddSingleUserToWaitlistFromArchive( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		TestHelperFunctions::ProcessAction( $I, 'archive', 'wcwl_return_to_waitlist', '.wcwl_user_checkbox:first-child' );
		$I->see( 'The selected user has been added to the waitlist' );
		$I->click( 'li[data-tab=waitlist]' );
		$I->seeNumberOfElements( '.wcwl_user_row', 1 );
	}

	public function AddMultipleUsersToWaitlistFromArchive( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		TestHelperFunctions::ProcessAction( $I, 'archive', 'wcwl_return_to_waitlist', 'input[name=wcwl_select_all]' );
		$I->see( 'The selected users have been added to the waitlist' );
		$I->click( 'li[data-tab=waitlist]' );
		$I->seeNumberOfElements( '.wcwl_user_row', 4 );
	}

	public function PermanentlyDeleteSingleUserFromArchive( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		TestHelperFunctions::ProcessAction( $I, 'archive', 'wcwl_remove_archive', '.wcwl_user_checkbox:first-child' );
		$I->see( 'Selected users have been removed' );
		$I->seeNumberOfElements( '.archive.current .wcwl_user_row', 3 );
	}

	public function PermanentlyDeleteMultipleUsersFromArchive( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		TestHelperFunctions::ProcessAction( $I, 'archive', 'wcwl_remove_archive', 'input[name=wcwl_select_all]' );
		$I->see( 'Selected users have been removed' );
		$I->dontSeeElement( '.archive.current .wcwl_user_row' );
	}

	public function CheckRemovedUsersRemainGoneFromArchive( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'archive' );
		$I->dontSeeElement( '.archive.current .wcwl_user_row' );
	}

	/* ARCHIVE TAB END */
	/* OPTIONS TAB START */
	public function CanDisableWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'options' );
		$I->uncheckOption( 'input[name=enable_waitlist]' );
		$I->click( 'Update Options' );
		$I->wait( 1 );
		$I->see( 'Waitlist options have been updated for this product' );
		$I->click( '#wp-admin-bar-view a' );
		$I->dontSeeElement( 'a.woocommerce_waitlist' );
	}

	public function CanEnableWaitlist( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'options' );
		$I->checkOption( 'input[name=enable_waitlist]' );
		$I->click( 'Update Options' );
		$I->wait( 1 );
		$I->see( 'Waitlist options have been updated for this product' );
		$I->click( '#wp-admin-bar-view a' );
		$I->seeElement( 'a.woocommerce_waitlist' );
	}

	public function MinimumStockOptionOnlyEnabledWhenOverrideChecked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'options' );
		$I->seeElement( 'input[name=minimum_stock]:disabled' );
		$I->checkOption( 'input[name=enable_stock_trigger]' );
		$I->dontSeeElement( 'input[name=minimum_stock]:disabled' );
		$I->uncheckOption( 'input[name=enable_stock_trigger]' );
		$I->seeElement( 'input[name=minimum_stock]:disabled' );
	}

	public function MinimumStockOptionOverridesGlobalOption( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'options' );
		$global_option  = abs( intval( $I->grabOptionFromDatabase( 'woocommerce_waitlist_minimum_stock' ) ) );
		$updated_option = $global_option + 5;
		$I->checkOption( 'input[name=enable_stock_trigger]' );
		$I->fillField( 'input[name=minimum_stock]', $updated_option );
		$I->click( 'Update Options' );
		$I->wait( 1 );
		$I->click( 'li[data-tab=waitlist]' );
		TestHelperFunctions::AddUsersToWaitlist( $I, array( 'joey@pie.co.de' ) );
		TestHelperFunctions::UpdateProductStockLevel( $I, 2 );
		$I->click( '.wcwl_waitlist_menu_tab a' );
		$I->see( 'joey@pie.co.de' );
		$I->click( '.inventory_tab a' );
		TestHelperFunctions::UpdateProductStockLevel( $I, 6 );
		$I->click( '.wcwl_waitlist_menu_tab a' );
		$I->dontSee( 'joey@pie.co.de' );
		$I->see( 'There are no users on the waiting list for this product.' );
	}

	public function MinimumStockResetsToGlobalOptionWhenOverrideUnchecked( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'simple', 'options' );
		$I->checkOption( 'input[name=enable_stock_trigger]' );
		$I->fillField( 'input[name=minimum_stock]', 10 );
		$I->uncheckOption( 'input[name=enable_stock_trigger]' );
		$I->click( 'Update Options' );
		$I->seeInField( 'input[name=minimum_stock]', 1 );
	}
	/* OPTIONS TAB END */
}