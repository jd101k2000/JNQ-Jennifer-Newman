<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'jnq12341234');

/** MySQL database username */
define('DB_USER', 'jnq12341234');

/** MySQL database password */
define('DB_PASSWORD', 'Crunchy31!22');

/** MySQL hostname */
define('DB_HOST', 'jnq12341234.db.7567312.hostedresource.com');

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
define('AUTH_KEY',         '/R~rwlk*<W!C-^0tCbpct(r$D}{:?yvl=R4C/-WI_epz?H{xfi|^+4n[-;iMJbQI');
define('SECURE_AUTH_KEY',  'WUILYct~9]cf>??nkR-_kyQZ}kt0T;JeAX$DEFBtwR`v=[Vz(|-EWqp-xqZNT{,X');
define('LOGGED_IN_KEY',    'jHHt/FOa{tRseF0l)TP=,Z`e{^?fB7q+@|gkqI y5+Zqi0>)-w[Q(f)$2@3_D@-t');
define('NONCE_KEY',        'dOgTi#2mef:|<@:=S!+A(]05Bjd|w-mFOoe>h?D7%lf)qF;&dg P(A-/Z-H:b#Mr');
define('AUTH_SALT',        'aC~[9*N+pZNV,;p3t(]Eejt2Oor*o!jf(TU5:qd-NLy;[q@walWkQ/ge}-}Zd@Qr');
define('SECURE_AUTH_SALT', 'L6Dx1v{~fp~.x#s]o>WoWfY<AS54d>tnFC9)S](yu.{`x29UE#%![|&8OAL>CbVl');
define('LOGGED_IN_SALT',   'a1sv.c{}gu|4-|nbp5WX.h/CHrqN?P?-,dME[135_NM;>es553-K=gCz|K#1MTMy');
define('NONCE_SALT',       'ZwY]]yKlj/~7%]I(x]it3|LZIZ-D/,Y/}z6=7*t]=P|^F]F~Ddw2#@+8-Y|gG),8');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
