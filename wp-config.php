<?php
define( 'WP_CACHE', true ) ;
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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'WP_HOME', 'https://jaynellebell.com' );
define( 'WP_SITEURL', 'https://jaynellebell.com' );

define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'wordpress' );

/** MySQL database password */
define('DB_PASSWORD', 'f3197e2019c55b6437eafd6af52bb07a126ed5218dabbf49');

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('SECURE_AUTH_KEY', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('LOGGED_IN_KEY', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('NONCE_KEY', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('AUTH_SALT', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('SECURE_AUTH_SALT', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('LOGGED_IN_SALT', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');
define('NONCE_SALT', '*EJj%12>?;fDUnKbm&T>[vvO&:A^tX4Bg[M1O^yuV?r.~Fon:D}pVY?YSy{m{CvL');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
