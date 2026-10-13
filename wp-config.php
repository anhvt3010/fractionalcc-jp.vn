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
define( 'DB_NAME', 'fractionalcc-jp' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '`a~V.+JRZq}Vo[!R8ngM_& 8aUTU+n*c`B6Gu%We7Ep7}jrd],( %#C-GpPhtBC5' );
define( 'SECURE_AUTH_KEY',  'Q;=MYg@7pkGX2nO{W0e }M1T`N`s{7RPL(f+}NHhd]>Jyw&7$uSF4#%/*lY7X$Ja' );
define( 'LOGGED_IN_KEY',    'J}~?={G|b;+eC|*i=pf#i=s]H4*qsZxwG287 PkN+h=NF/+H*reWqsj_s?UCd8So' );
define( 'NONCE_KEY',        '~$[;~hM${kq~Jq.lugx?j[g8+g#m9CONCC$6_?g[<aF)N0fEitNn$_@N>I;qKs<x' );
define( 'AUTH_SALT',        'g)-^#)#hAz?#z/6?o5$D22o>T h>u,x{65RzKd@<y`gglL7Em{K5hG~eIHM4,}_R' );
define( 'SECURE_AUTH_SALT', 'X{y+L1/wQCe:4u!UmDWhU^MC{y{ls^S(2c$$= i7([cLBVg3n;35Q-<y4@k(gb0!' );
define( 'LOGGED_IN_SALT',   '_Hu$>bkH/,v/@r{BkfxB><D3?U ^e!L1Tut_n-Zzk.g>ANj9(]RET^cizCaAx4u+' );
define( 'NONCE_SALT',       '(! v}|6N4YV6l}W@k*6>pKqByiBL6{%ihr^^adVKmjA:%+@8F>($)$aISM>@ELmF' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* Local XAMPP URL for this installation; shared database URLs are unchanged. */
define( 'WP_HOME', 'http://localhost/fractionalcc-jp.vn' );
define( 'WP_SITEURL', 'http://localhost/fractionalcc-jp.vn' );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
