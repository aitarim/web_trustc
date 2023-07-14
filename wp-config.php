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
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'web_trust' );

/** Database username */
define( 'DB_USER', 'grupo_trust' );

/** Database password */
define( 'DB_PASSWORD', '123qweasdzxc' );

/** Database hostname */
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
define( 'AUTH_KEY',         '_qDT_Ny/H|ro0O4P^>ep6qN.cR_&CdAZ%W(#D_Yk~$.V)bQYm|LHb*OU9?DO>u9Y' );
define( 'SECURE_AUTH_KEY',  '1%BT#.m[9OSVC./1BVjYJ+q&!?rL#[SdM``;Y0](S(ycw}/^F?s,@M3`k?K`b)U,' );
define( 'LOGGED_IN_KEY',    'zdVljz~chaw7(U/gCpvr=|HV?S mNn*(Gr>i$dJrker7n2(},M,@jG$ivpz~QGf=' );
define( 'NONCE_KEY',        'F/%APQz;;G=N5o8u/x64g;`tY0LVxa}Fd#U1|;J_U7J:mud5hw(io(*}b!`/vROo' );
define( 'AUTH_SALT',        '/lxGH^UEJoFB,_A[/x0IOt@UGOne6&j`L]|oV|#$$Y_y<Mq 7I7A_4]~E4NfvYul' );
define( 'SECURE_AUTH_SALT', 'K=]2$20Zz(g.y|#7}vH^).-[z?-D><+]7fDgB*BU1VfPJpSNS )DsC4HzG.2o0,J' );
define( 'LOGGED_IN_SALT',   'Qf]5]tOT dv_mRM7^bT04O-jQa^G]^rh,21Xv!OW!JD+[&5{JypR#hxU$29Jj^vn' );
define( 'NONCE_SALT',       '0(J S~@IDS|lp4[M}e1zQ3duZ-e.6Cb`@vk+fFm6aY[Y4~~d]pgoDO%{xhyy[9C=' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'tc_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
