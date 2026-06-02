<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'local');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', 'root');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'v&$F_ana/cP1a$SZlR fa7sjoOw-V-b^P=`k2l*6]=>b>0O|nxgb#fYO?=&KQ9iG' );
define( 'SECURE_AUTH_KEY',  'wofdnj?9Hg^U)/=-[fqzJd?J[.OW(8igpt+r2vLhJPVY-LkE%m[v^6y:#X~v<~]k' );
define( 'LOGGED_IN_KEY',    '*d(32=i)!2^5Pd 7hZbki6X~&3>AwO=H]8N|sqmJ#1RCwx|A&>.Of#^zO^uV3bx,' );
define( 'NONCE_KEY',        '!H$[6&:zPIO)zm&^a`g(bU~1B.U|+zs_y>OiTBu`l)T:R?W-<IswG=)iir>{-@Vx' );
define( 'AUTH_SALT',        '^!nF|m: axJQERl@m9&*CMjStDaKk{J<#1nk?!jR#]Wo$Ve X{!I[36&3- ]C$M=' );
define( 'SECURE_AUTH_SALT', '6kRU}fk.]4dzWnlV~{_~1`O? mkyha$N)|@[[NC4uq5y,jeo3gYByf^1K|29YkJ}' );
define( 'LOGGED_IN_SALT',   '9dV+>kXY/PXshi(xw;0JjrY|}iis9Im!/q$[ $o<(aJGb{asCDGih_ pBUo9P~Kv' );
define( 'NONCE_SALT',       'E<`?+..7[//QrG@p&bN &,4dU=oCf]V,Vu-PV.bHKX>,+S*GHH<PAFgmTiczVBki' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp735_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', false );

/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_AUTO_UPDATE_CORE', false );
define( 'WP_POST_REVISIONS', 30 );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
