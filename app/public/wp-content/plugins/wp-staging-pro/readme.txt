=== WP Staging - DB & File Duplicator & Migration  === 

Author URL: https://wordpress.org/plugins/wp-staging
Plugin URL: https://wordpress.org/plugins/wp-staging
Contributors: ReneHermi, WP-Staging
Donate link: https://wordpress.org/plugins/wp-staging
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: staging, duplication, cloning, clone, migration, sandbox, test site, testing, backup, post, admin, administration, duplicate posts
Requires at least: 3.6+
Tested up to: 4.9
Stable tag: 2.3.8

A duplicator plugin! Clone, duplicate and migrate live sites to independent staging and development sites that are available only to administrators.

== Description == 

<strong>This cloning and staging plugin is well tested but still work in progress! <br>
If you find a bug please open a ticket in the [support request](https://wordpress.org/support/plugin/wp-staging/ "support forum"). Any issues will be fixed asap!
</strong>
<br /><br />
<strong>Note: </strong> This plugin is not able to push back your changes to the live site at the moment! 
Please let us know your most requested feature and use our quick poll. It only takes one minute of your time: [Start the poll](https://docs.google.com/forms/d/e/1FAIpQLScZ-dO5WffV3xObn16LwG05tr1HrADD_8L4wbTxPHqoPssVcg/viewform?c=0&w=1&usp=mail_form_link "wp staging poll")
<br /> <br />


<blockquote>
<h4> WP Staging for WordPress Migration </h4>
This duplicator plugin allows you to create an staging or development environment in seconds* <br /> <br />
It creates a file clone of your website into a subfolder of your current WordPress installation with an entire copy of your database. 
This sounds pretty simple and yes it is! All the hard time consumptive database and file copy stuff including url replacements is done in the background.
 <br /> <br />
I created this plugin because all other solutions are way too complex, overloaded with dozens of options or having server requirements which are not available on most shared hosting solutions.
All these reasons prevent user from testing new plugins and updates first before installing them on their live website, so its time to release a plugin which has the potential to be merged into everyone´s wordpress workflow.
 <br /> <br />
<p><small><em>* Time of creation depends on size of your database and file size</em></small></p>
</blockquote>

WP Staging helps you to prevent your website from being broken or unavailable because of installing untested plugin updates! 

[youtube https://www.youtube.com/watch?v=Ye3fC6cdB3A]

= Main Features =

* <strong>Easy: </strong> Staging migration applicable for everyone. No configuration needed!
* <strong>Fast: </strong> Migration process lasts only a few seconds or minutes, depending on the site's size and server I/O power
* <strong>Safe: </strong> Access to staging site is granted for administrators only.
<br /><br />
<strong>More safe:</strong> 
<br>
* Admin bar reflects that you are working on a staging site
* Extensive logging if duplication or  migration process should fail.
* New: Compatible to All In One WP Security & Firewall

= What does not work or is not tested when running wordpress migration? =

* Wordpress migration of wordpress multisites (not tested)
* WordPress duplicating process on windows server (not tested but will probably work) 
Edit: Duplication on windows server seems to be working well: [Read more](https://wordpress.org/support/topic/wont-copy-files?replies=5 "Read more") 


<strong>Change your workflow of updating themes and plugins data:</strong>

1. Use WP Staging for migration of a production website to a clone site for staging purposes
2. Customize theme, configuration and plugins or install new plugins
3. Test everything on your staging site first
4. Everything running as expected? You are on the save side for migration of all these modifications to your production site!


<h3> Why should i use a staging website? </h3>

Plugin updates and theme customizations should be tested on a staging platform first. Its recommended to have the staging platform on the same server where the production website is located.
When you run a plugin update or plan to install a new one, it is a necessary task to check first the modifications on a clone of your production website.
This makes sure that any modifications is  working on your website without throwing unexpected errors or preventing your site from loading. (Better known as the wordpress blank page error)

Testing a plugin update before installing it in live environment isn´t done very often by most user because existing staging solutions are too complex and need a lot of time to create a 
up-to-date copy of your website.

Some people are also afraid of installing plugins updates because they follow the rule "never touch a running system" with having in mind that untested updates are increasing the risk of breaking their site.
I totally understand this and i am guilty as well here, but unfortunately this leads to one of the main reasons why WordPress installations are often outdated, not updated at all and unsecure due to this non-update behavior.

<strong> I think its time to change this, so i created "WP Staging" for WordPress migration of staging sites</strong>

<h3> Can´t i just use my local wordpress development copy for testing like xampp / lampp? </h3>

Nope! If your local hardware and software environment is not a 100% exact clone of your production server there is NO guarantee that every aspect 
of your local copy is working on your live website exactely as you would expect it. 
There are some obvious things like differences in the config of php and the server you are running but even such non obvious settings like the amount of ram or the 
the cpu performance can lead to unexpected results on your production website. 
There are dozens of other possible cause of failure which can not be handled well when you are testing your changes on a local staging platform.

This is were WP Staging steps in... Site cloning and staging site creation simplified!

<h3>I just want to migrate the database from one installation to another</h3>
If you want to migrate your local database to a already existing production site you can use a tool like WP Migrate DB.
WP Staging is only for creating a staging site with latest data from your production site. So it goes the opposite way of WP Migrate DB.
Both tools are excellent cooperating eachother.

<h3>What are the benefits compared to a plugin like Duplicator?</h3>
At first, i love the [Duplicator plugin](https://wordpress.org/plugins/duplicator/ "Duplicator plugin"). Duplicator is a great tool for migrating from development site to production one or from production site to development one. 
The downside is that Duplicator needs adjustments, manually interventions and prerequirements for this. Duplicator also needs some skills to be able to create a development / staging site, where WP Staging does not need more than a click from you.
However, Duplicator is best placed to be a tool for first-time creation of your production site. This is something where it is very handy and powerful.

So, if you have created a local or webhosted development site and you need to migrate this site the first time to your production domain than you are doing nothing wrong with using
the Duplicator plugin! If you need all you latest production data like posts, updated plugins, theme data and styles in a testing environment than i recommend to use WP Staging instead!

= I need you feedback =
This plugin has been done in hundreds of hours to work on even the smallest shared webhosting package but i am limited in testing this only on a handful of different server so i need your help:
Please open a [support request](https://wordpress.org/support/plugin/wp-staging/ "support request") and describe your problem exactely. In wp-content/wp-staging/logs you find extended logfiles. Have a look at them and let me know the error-thrown lines.


= Important =

Permalinks are disabled on the staging site because the staging site is cloned into a subfolder and permalinks are not working on all systems
without doing changes to the .htaccess (Apache server) or nginx.conf (Nginx Server). 
[Read here](https://wp-staging.com/docs/activate-permalinks-staging-site/ "activate permalinks on staging site") how to activate permalinks on the staging site.

 
= How to install and setup? =
Install it via the admin dashboard and to 'Plugins', click 'Add New' and search the plugins for 'Staging'. Install the plugin with 'Install Now'.
After installation goto the settings page 'Staging' and do your adjustments there.


== Frequently Asked Questions ==

* I can not login to the staging site
If you are using a security plugin like All In One WP Security & Firewall you need to install latest version of WP Staging. 
Go to WP Staging > Settings and add the slug to the custom login page which you set up in All In One WP Security & Firewall plugin.



== Official Site ==
https://wp-staging.com

== Installation ==
1. Download the file "wp-staging-pro.zip":
2. Upload and install it via the WordPress plugin backend wp-admin > plugins > add new > uploads
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Start Plugins->Staging

== Screenshots ==

1. Step 1. Create new WordPress staging site
2. Step 2. Scanning your website for files and database tables
3. Step 3. Wordpress Staging site creation in progress
4. Finish!

== Changelog ==

= 2.3.8 =
* New: Replace even hardcoded links and server path by using search & replace through all staging site database tables
* New: New and improved progress bar with elapsed time
* Fix: Cancel cloning does not clean up unused tables and leads to duplicate tables
* Tweak: Better error messages
* Tweak: Open staging site in same window from login request


= 2.3.7 =
* New: Option to set Custom Login Link if there is one
* New: Set meta noindex for staging site to make it non indexable for search engines
* New: Better multiple folder selection. Allows to unselect a parent folder without collapsing all child folders
* New: Sorted list of folders to copy
* New: Add more sys info elements under tools section
* Fix: Can not login to staging site if plugin All In One WP Security & Firewall is used
* Fix: Staging site not reachable because permalinks are not disabled under certain conditions
* Fix: Change default login link to wp-admin
* Fix: Unneccessary duplicates of wpstg tables in db

= 2.3.6 =
* Fix: Better finishing message
* Fix: Old staging site is not listed and pushing is not working properly when plugin is updated from wp staging version 1.6 and lower
* Fix: Undefined property: stdClass::$totalFileSize

= 2.3.5 =
* Fix: Minnor code issues
* Fix: Skip directory listings for symlinks


= 2.3.4 =
* Fix: License menu not shown if db option wpstg_is_staging_site === 'false'
* Fix: Missing files from the staging folder /wp-content/uploads after migration

= 2.3.3 =
* Fix: Ignored selection "none selected tables" instead all of them are copied

= 2.3.2 =
* Fix: Get notice that operation is not allowed on live site
* Fix: Updating process failed with unknown error

= 2.3.1 =
* Fix: mod_security option is preventing cloning execution 

= 2.3.0 =
* New: Remove setting "wordpress in subdir" and detects it automatically
* Fix: If WordPress is in subdir installed, siteurl & home is wrong after pushing process
* Fix: Missing table if table name contains two occurance of the table prefix e.g. wp_affiliate_wp_affiliates

= 2.2.9 =
* New: Search & Replace absolute path
* Fix: High memory consumption leads to timeouts

= 2.2.8 =
* New: Keep permalink structure of live site when migrating from staging site
* Tweak: Increase performance of pushing process by factor 3
* Fix: Hide db table and folder selection when pushing process starts
* Fix: Set permalink to default after updating clone
* Fix: Prevent user session expiration after pushing from live site
* Fix: Undefined property userRoles
* Tweak: Revert to previous loader.gif
* Tweak: Skip files collecting for excluded folders
* Tweak: Do not show license screen on staging site
* Tweak: Do not show lists of staging sites on staging sites

= 2.2.7 =
* New: Allow migrating of database
* Fix: Skipping files leads to interrupted pushing process

= 2.2.6 =
* Tweak: Return more human readable error notices
* Fix: Cloning process stops due to file permission issue
* Fix: Exclude WP Super Cache from copying process because of bug in WP Super Cache, see https://github.com/Automattic/wp-super-cache/issues/505
* New: Tested up to WP 4.9.2


= 2.2.5 =
* New: Allow pushing media folder wp-content/uploads
* Tweak: Throw error if there is not enough disk space for creating a cloning site

= 2.2.4 =
* New: increased speed for cloning process by factor 5, using new method of file agregation 
* New: Skip files larger than 8MB
* Fix: Can not create clone If there is no php memory limit (-1)


= 2.2.3 =
* Fix: Additional checks to ensure that the root path is never deleted

= 2.2.2 =
* Fix: False error: can not push "live prefix table is same as staging table prefix"
* Fix: "Fatal error - The clone does not exist in database. This should not happen."
* Fix: Not all files are pushed from staging to live site
* New: Tested up to WordPress 4.9

= 2.2.1 =
* Fix: Link to the staging site is missing a slash if WordPress is installed in subdir
* Fix: Missing files in clone site if copy file limit is higher than 1


= 2.2.0 =
* Fix: Cloning a new site results in [undefined] 
* Fix: Show all tables from the tables selection dialogue and use default selection for correct tables
* Fix: If clone has been created with an older wp staging version its not possible to use the update function
* Fix: File Copy Limit settings default value is 1 now, which prevents some cases where not all files were copied over
* Fix: Missing files in clone site if copy file limit is higher than 1
* Tweak: Remove table wpstg_rmpermalinks_executed when plugin is uninstalled

= 2.1.9 =
* New: Allow selection of db tables with wpstg_ prefix in the table selection dialog
* Fix: Deleting process throws timout issues and php notices
* Fix: Link to staging site is undefined when staging name contains space characters
* Fix: If file copy limit is lower than 500, not all files are copied in all cases
* Fix: Increase file copy performance
* Fix: Cloning update function created new staging tables everytime it is run

= 2.1.8 =
* New: Allow deactivation of Optimizer plugin
* Fix. Update clone not working

= 2.1.7 =
* Fix: Sanitize Clone Names and Keys to fix "clone not found" issue
* Fix: New file clone limit of 10 files per batch to fix godaddy ajax 404 issues

= 2.1.6 =
* Fix: Remove LOCK_EX parameter in file_put_contents(). LOCK_EX is not working on several systems which results in cloning process timeouts
* Fix: Limit maximum execution time to 30 seconds
* New: New setting to specify the maximum amount of files copied within one ajax call
* Fix: Error 500 when debug mode is activated


= 2.1.5 =
* Fix: Remove test string from staging site
* Fix: Huge Performance improvement in copying process by removing duplicate file entries in the cache file. This also prevents weird timeout issues on some hosted websites

= 2.1.4 =
* Testing: resetMemory() not allowed
* New: Add link to tutorial how to push changes to live site

= 2.1.3 =
* Fix: Can not login to staging site

= 2.1.2 =
* New: Exclude unneccessary files from cloning process: .tmp, .log, .htaccess, .git, .gitignore, desktop.ini, .DS_Store, .svn
* New: More details for debugging in Tools->System Info
* Fix: Check if tables in staging site exists before attempting to modify them
* Fix: WordPress in sub directories were not opening
* Fix: Nonce check not working if nonce life time is filtered by another plugin WP Bug: https://core.trac.wordpress.org/ticket/41617#comment:1
* Fix: Access to staging site not working, if WP_SITEURL and WP_HOME is defined in wp-config.php 
* Tweak: Exclude wp-content/cache folder from copying process

= 2.1.1 =
* Fix: Fatal error causing blank screen

= 2.1.0 =
* Fix: Frontpage not available without login

= 2.0.9 =
* New: Select which user role is able to access the staging site
* Fix: After update from wpstg 1.6.x to 2.x previous settings were not imported resulting in cancellation of cloning process

= 2.0.8 =
* Fix: css file are not loaded on wpstg admin screen 

= 2.0.7 =
* Fix: Fatal error when some db tables are excluded
* Fix: Automatic update function not working

= 2.0.6 =
* Fix: Cancel Cloning button not working
* Fix: Limit max execution time to a maximum of 30sec to prevent high memory consumption and script timeouts

= 2.0.5 =
* Tweak: Improve deletion method
* Fix: Activation hook not running

= 2.0.4 =
* New: Installation of must use plugin to exclude other plugins from execution while wpstg is running and cloning website

= 2.0.3 =
* New: Complete rewrite of the code base
* New: Batch processing allows to clone even huge sites without any timeouts
* New: Preparation for WP QUADS PRO with ability to copy file changes back to live site

= 1.1.6 =
* New: Add download link to WP Staging Beta Version 2.0.1

= 1.1.5 =
* Fix: Admin notice is throwing a false positive write permission error
* New: Move log folder to wp-content/uploads/wp-staging/logs
* New: Tested up to WP 4.7.3

= 1.1.4 =
* Fix: Fatal error Unsupported operand types

= 1.1.3 =
* New: Tested up to wp 4.7.2
* Fix: Arrows in drop down for folder selection are distorted
* Tweak: Show working log as default to make debugging easier

= 1.1.2 = 
* Fix: Settings are not deleted when plugin is removed
* Fix: Staging site is available for non administrators

= 1.1.1 =
* Fix: Change rating url

= 1.1.0 =
* New: Tested up to WP 4.6
* New: Create a poll and ask what feature is most required

= 1.0.9 =
* Fix: Undefined WPSTG() warning
* Fix: Change compatibility version to wp 4.5.3

= 1.0.8 =
* Tested up to WP 4.5.2

= 1.0.7 =
* Fix: Activation hook is not fired and staging site is not working properly
* Performance: Increase default query copy limit to 1000

= 1.0.6 =
* Fix: Uninstalling plugin throwing error
* Fix: Error permission admin notice although permission issues are correct


=  1.0.5 =
* New: Tested up to WP 4.5
* Fix: Download system log not working
* Fix: Click on Optimizer "Select all | none | invert" links leads to jumping
* Tweak: Make clear that unselecting a checkbox will exlude table or file from copy process
* Tweak: Remove unnecessary text
* Tweak: Change beta notice in dashboard. WP Staging is stable
* Tweak: Change twitter handle to @wpstg

= 1.0.3 =
* Fix: Missing const MASHFS_VERSION
* Fix: Remove error "table XY has been created, BUT inserting rows failed."
* Fix: Not tested up to 4.4.2 message shown although it's tested up to WP 4.4.2
* New: Disable either free or pro version and does not allow to have both version enabled at the same time

= 1.0.2 =
* Tweak: Change setting description of uninstall option
* Tweak: Lower tags in readme.txt

= 1.0.1 =
* New: Orange colored admin bar on staging site for better visualization and comparision between production live site and staging site
* Tweak: Remove contact link on multisite notification

= 1.0.0 =
* Fix: Do not follow symlinks during file copy process
* Fix: css error
* Fix: Show "not-compatible" notice only when blog version is higher than plugin tested version.
* Fix: undefined var $size
* Fix: Check if $path is null before writing to remaining_files.json
* Fix: $db_helper undefined message
* Fix: Skip non utf8 encoded files during copying process

Complete changelog: [https://wp-staging.com/changelog.txt](https://wp-staging.com/changelog.txt)

== Upgrade Notice ==

= 2.0.4 =
2.0.4 * New: Install of must use plugin to exclude other plugins from execution while cloning