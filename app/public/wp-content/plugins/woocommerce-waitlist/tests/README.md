## Testing
**Before getting started setup a local install specifically for testing with the domain http://waitlisttest.local (your database will be overwritten each time tests are run so use this install just for testing)**

Steps required to run acceptance tests:
* Use terminal to navigate to the plugins directory (cd wp-content/plugins)
* Install WooCommerce (git clone git@github.com:woocommerce/woocommerce.git)
* Install WooCommerce Waitlist (git clone git@github.com:woocommerce/woocommerce-waitlist.git)
* Change to whichever branch/version you need to test (cd woocommerce-waitlist/ && git checkout my-new-branch)
* Change to the test directory and run composer update (cd tests/ && composer update)
* Google chromedriver is now running from your terminal (you should see `Starting ChromeDriver 2.38.552518 (183d19265345f54ce39cbb94cf81ba5f15905011) on port 9515. Only local connections are allowed.`
* SSH into your local install (right click in Local) and navigate to the waitlist tests directory (cd app/public/wp-content/plugins/woocommerce-waitlist/tests)
* Run the tests (vendor/bin/codecept run acceptance)
* If you see test names in pink things are looking good. Enjoy your coffee for ~15mins and come back to check the report 
* If any tests fail a screenshot will usually indicate what was wrong. These are stored in tests/tests/_ouput

Tests cover almost all functionality for front and backend waitlist features. Currently the following does not have tests:
* Mailto links
* Meta update scripts (found at the bottom of the settings page)
