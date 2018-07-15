<?php
require_once dirname( dirname( __FILE__ ) ) . '/TestHelperFunctions.php';

class Admin_Product_Listing_Screen_Cest {

	public function WaitlistColumnIsVisible( AcceptanceTester $I ) {
		TestHelperFunctions::VisitProductListingPage( $I );
		$I->see( 'Waitlist' );
		$I->seeElement( '#woocommerce_waitlist_count' );
	}

	public function ProductsCanBeSortedByWaitlistCounts( AcceptanceTester $I ) {
		TestHelperFunctions::VisitProductListingPage( $I );
		$I->click( '#woocommerce_waitlist_count a' );
		$I->click( '#woocommerce_waitlist_count a' );
		$I->seeElement( '#the-list tr:first-child' );
		$first_row_count  = $I->grabTextFrom( '#the-list tr:first-child td.column-woocommerce_waitlist_count' );
		$second_row_count = $I->grabTextFrom( '#the-list tr:nth-child(2) td.column-woocommerce_waitlist_count' );
		\PHPUnit_Framework_Assert::assertGreaterThanOrEqual( $second_row_count, $first_row_count );
		$third_row_count  = $I->grabTextFrom( '#the-list tr:nth-child(3) td.column-woocommerce_waitlist_count' );
		\PHPUnit_Framework_Assert::assertGreaterThanOrEqual( $third_row_count, $second_row_count );
	}
}