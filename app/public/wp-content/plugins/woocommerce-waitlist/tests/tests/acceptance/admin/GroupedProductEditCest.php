<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Admin_Grouped_Product_Screen_Cest {

	public function WaitlistTabDoesntAppearOnPage( AcceptanceTester $I ) {
		TestHelperFunctions::VisitWaitlistAdminTab( $I, 'grouped', '' );
		$I->dontSeeElement( '.wcwl_waitlist_menu_tab' );
	}
}