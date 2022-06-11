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
define( 'DB_NAME', 'docdoc' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'r7vT|C-pgW?t9u@5rs@+NZQs0=UESHgJa[Y#lOYQ=zZBpPWWWBcfMbHaVHM!zj,d' );
define( 'SECURE_AUTH_KEY',  'C*[%7n3LZo Pc%@vMAxS9l4]hX1_Ly<?G$ c0P(@L8_.sI3(UwiKtw*;efx}-FI*' );
define( 'LOGGED_IN_KEY',    '`1YCPB^A&;>(dZAmb`@g$2/^[x^5:IO XfU DnvBg5Aa3=P#yHH]g7)`/zD&$krx' );
define( 'NONCE_KEY',        'w(xZZCz|UX:5xGZ1G|m;A#yrq])pCWnkKB(;rku+46!s~25r5Sdog6aY{O&H!YF.' );
define( 'AUTH_SALT',        '#^UX9#J|F VI+PvB!,J#,v|CqXXG?$&C1OW6Zvf>.:2V/a6n .AQI%R<n|3ylL5A' );
define( 'SECURE_AUTH_SALT', 'S&W./EHtz[{AHq4 eLEssI-Cts/gny$7;E=Cjj<JKE7W66YZVTpC/_3my:CA}j5f' );
define( 'LOGGED_IN_SALT',   'coU)fXd&SU[ $@yxztakzy.& 5Md5+iDX[3[wPlPEQq7PffCCIRG_6/3?F8$8Tf.' );
define( 'NONCE_SALT',       '(&lK#=C<eo!.dy=>HpA1:)C&28S[g+gx(6)mae)$:gt+aXJoPQABlYunF]2B|h/3' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
