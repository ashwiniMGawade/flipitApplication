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
define('DB_NAME', 'kortingsco_blog');

/** MySQL database username */
define('DB_USER', 'kortingsco_usr');

/** MySQL database password */
define('DB_PASSWORD', '6DGR5JMm');

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
define('AUTH_KEY',         'I34$:>d/1J+)N5-: K{wY=Rn|@z:%+$9ek),zUdD>1un0K^0o:|= p.~cYHsun+H');
define('SECURE_AUTH_KEY',  'ZhV-Xp5?lk}b,Y+@j9Vv7YuDyZM xk}NJ$*3S).jSuL58Z8i~*ZucD-7!<NaR{-p');
define('LOGGED_IN_KEY',    '74c]&|p9<HOvm<~s<[2c*H>g<]h16`?Br-~e9``$^@Uj%[/XR+sFcBQuZel|SH89');
define('NONCE_KEY',        'NWEaQ3$+=]nL! hFCGwYfrVq.h 1W9|vr|%)+6|p:2c8a)tTXZ| s[*7TM|ao{@{');
define('AUTH_SALT',        '$FkcP3_M+/FB2o_m>s%JrebZx}yY5Rg}Fd|c|tQ!)xv*yiMPfzz4LBZLEZ$-a+ln');
define('SECURE_AUTH_SALT', '%GN6.44l(wgrU;Y>J<s/w}U^=2|Wb;*wS^sO#Cb-41L2c3lW3eK eqa5LM[qG 5F');
define('LOGGED_IN_SALT',   'i*|-t+:|bHnO>9oC0;%9}_~crGjy:`xt`6ejh>@t0w)~s,Ooz3VUF!$:h:-t/wn-');
define('NONCE_SALT',       '*DUCNTz5&*2`veVz`w_GU~@xh`?++WhIyH#|H9wibSqNd6$eD%kO*dzuqF]-Ij)w');

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
define('WPLANG', 'nl_NL');

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
