<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'getguine_WPOHI');

/** MySQL database username */
define('DB_USER', 'getguine_WPOHI');

/** MySQL database password */
define('DB_PASSWORD', 'lsqZY27LYJNDzSa6C');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '966c7b81fe11fc381f7782db79b64e12d027a9c15863dae35d69772dd9689726');
define('SECURE_AUTH_KEY', 'ba980aa6594142cedbf5957a4ffb967fc01dba81092fd0f96ca304ce38c14bcb');
define('LOGGED_IN_KEY', '957cda1cb31a945d200f4cb4a726be306efb4ea81a9d03f59610081abf43a43f');
define('NONCE_KEY', '0d99a48f47c1981a3ef864cd8c364e26b01e1b3ffb72a66f20003fdbcf6c4484');
define('AUTH_SALT', '351ff1a9cc691f95fbec96d886766449287d2c966cf7ce15d79d1337448f3062');
define('SECURE_AUTH_SALT', 'c85fa5837df72a82940459a02105e376ed8d19d217de03bfba59d0e3987eaf4d');
define('LOGGED_IN_SALT', 'eeb1915354cfea50255022b890beab49a9dfc5fb6722df68a6b67fe998ef84fe');
define('NONCE_SALT', 'f23584f281ba9516c147a61485d22ad6a99ec0326f3e3bfbdb37376aeec70727');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = '_OHI_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'WP_CRON_LOCK_TIMEOUT', 120   ); 
define( 'AUTOSAVE_INTERVAL',    300   );
define( 'WP_POST_REVISIONS',    5     );
define( 'EMPTY_TRASH_DAYS',     7     );
define( 'WP_AUTO_UPDATE_CORE',  true  );


