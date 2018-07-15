<?php

class Frontend_WC_Shortcode_Cest {

	public function WaitlistElementsShowOnPageUsingWoocommerceShortcode( AcceptanceTester $I ) {
		$I->amOnPage( '/product-shortcode/' );
		$I->see( 'Join Waitlist' );
	}

	public function UserCanJoinWaitlistOnPageUsingWoocommerceShortcode( AcceptanceTester $I ) {
		$I->loginAsAdmin();
		$I->amOnPage( '/product-shortcode/' );
		$I->click( 'a.woocommerce_waitlist' );
		$I->see( 'You have been added to the waitlist for this product' );
		$I->canSeeInCurrentUrl( '/product-shortcode/' );
	}
}