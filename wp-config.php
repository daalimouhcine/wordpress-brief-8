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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress-brief-8' );

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
define( 'AUTH_KEY',         'UIPZv.[((?g}X@K%P[`>jF<VzctFh>fmn;s|- n%2cPGUs3{w^IOKbY<a3SwcN(W' );
define( 'SECURE_AUTH_KEY',  '%W[o|e)zG4oM`2.H*C3es`eoO0$cW%kcMi|(lJ`b1J%qGm,26+Z^3yW1O5W4_Tkd' );
define( 'LOGGED_IN_KEY',    'wIo+i*;{&|`P>Av(x{}L#Nmp<`vyDf5W-(RVGMhbAXqF#Kn_KHaRRP$a?9l9Zb@A' );
define( 'NONCE_KEY',        'EhP2a@/J~< aM1>z`=Y:S<ae[<FkluJ:&}|QW}`,^xh`8UCZrD?m[fb7bV]@8hQE' );
define( 'AUTH_SALT',        'athkYP9PBIpQ:L++w(|>/oPSj{)F3fc|7:,3%Uc%+nzck>>6d[<o3n$46A`rMhP}' );
define( 'SECURE_AUTH_SALT', ')y~6A3yl4ffF`ab:IhNoqTIOAM1#teYg2$d&e%!qY~.>6HS679Ko%+a7w0k`sIAi' );
define( 'LOGGED_IN_SALT',   '}Wl3`E9:yNJ.h).w1=K[!DEo<<Sn[/DzGuN-p^2*{LoS,urID;7VYP Ll%JiNaJ^' );
define( 'NONCE_SALT',       '7tuV@T?:%55`Go;yBPGJ). Nwo8]hV&e6]QFD9+zY{eYxRAw;y[O:SHJ2VZNK7j`' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
