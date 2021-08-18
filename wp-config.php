<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
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
define( 'DB_NAME', 'ullapopken' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'maya' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'W|sG1n.+O$SB#Az-KOIbNPvHDE0AH%;sH[PmVOt!4fa])4uM6Ns5ZC~2%MokJsR ' );
define( 'SECURE_AUTH_KEY',  'mu;4KQjUIv4Xs4f:eST7eXUMej8NN]VuQtjedA^Jo1[;]O%AH&Be=9+5,[9%~FrB' );
define( 'LOGGED_IN_KEY',    '#GuxiRq8D<v/Je_:)jfmf0WGbOA R&Dw}AVPNIPzV/ko//H_U0->:q?yu1BN?D@Z' );
define( 'NONCE_KEY',        '7TKC$!nsBHFsHm)A]nMe/?`tzq~ iKIadSiS4=J F%2em+qG->xq6{kw=/}`}{*]' );
define( 'AUTH_SALT',        'k-IXN0|,V_as80f3Lh=_M8SU9[luFnfTX!:M.gopx]UMM:*  e{Z$a,UdA*9blnd' );
define( 'SECURE_AUTH_SALT', '|;J9Gn>O{ph2e)DB$28GvSx$||O+g_%+H``y=vLM(0Bns?juG]]-CUQwvb(BL|QA' );
define( 'LOGGED_IN_SALT',   'e#}}=2JMcMva*X9m?FP+<q-zR-F>n,a/4a0j`uPb7wlSODs-y8lY8b^]<ZK4~aSD' );
define( 'NONCE_SALT',       'gb pi{Wa*H&4L;;^|jj>/BI66/tU&vVKO@x?K^|S?h:?pt/2YOUJ)sli+EF,M1Zp' );

/**#@-*/

/**
 * WordPress database table prefix.
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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
