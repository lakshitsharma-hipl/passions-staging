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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'hipldemo1_passionsdev' );

/** Database username */
// define( 'DB_USER', 'root' );
define( 'DB_USER', 'hipldemo1_passionsdev' );

/** Database password */
// define( 'DB_PASSWORD', '' );
define( 'DB_PASSWORD', 'WASbLzm-c6;9' );

/** Database hostname */
// define( 'DB_HOST', 'localhost' );
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY',         'ZdsPtC|r.@JVQ=ja-pJ~_m=D@@vQ><NaTjtr7fi|Xc;T3?M%sGByOD*:(/pxU5=a');
define('SECURE_AUTH_KEY',  '1{q 9(`F6oA1]H~>2Q9-]Z9l5NnpqGv0WIJSl64& /cV-lAsmc+TPo&6gIsXP[-8');
define('LOGGED_IN_KEY',    ':sJ(UMI:Rx<LV&V%#2<![;n ~m]mS|UVoew09Ly@Qdd>5,;EkrkE@1=%UJ%>Z:SZ');
define('NONCE_KEY',        'o;s|Pk|?nNMSaA^>!{D|]-R;[%rVyGL?k0gB3O:X/GXyJNG]2K;|_A&-L+.N/Svi');
define('AUTH_SALT',        '.;/;mE@(?N.oV^*9H!Q0.02(mZ};SJ0|1oo/lLfHrT4<p$/kxK[+W>YzV(ttwFk0');
define('SECURE_AUTH_SALT', 'OXnx|fhZhJY-Flt_4&(/b]+2Pz|c9j9n*~&ID@)4ct5QvBUIrhZ&ei`@b[(|EMy:');
define('LOGGED_IN_SALT',   '/2I,[F.BCHXX<#RK^mz8I8IUu)ym:-V*+:$:Wend y-mA+:UB *#O<m7}ItD+FWv');
define('NONCE_SALT',       '4#8p<Q:XkkA]$9+N_%xM6&|}i|)x|d-2Pl%&7%Uqq3{t=mD~]8&ulBD2~h6IFnh(');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'zi9RMjXIf_';


/* Add any custom values between this line and the "stop editing" line. */

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
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', true );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}


define( 'DISALLOW_FILE_EDIT', true );
define( 'CONCATENATE_SCRIPTS', false );
define( 'DISABLE_WP_CRON', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}


/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
