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

define( 'DB_NAME', 'blsoftware_havefan' );



/** MySQL database username */

define( 'DB_USER', 'blsoftware_havefan' );



/** MySQL database password */

define( 'DB_PASSWORD', '[_dyYS8yuQ&e?LlF&z' );



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

define( 'AUTH_KEY',         '|Z&7X#]zv*/] EN{@FRID}?e{3XYU%u[{5bv=MiIrL.E4k8MB/KDmr@zXex><g{T' );

define( 'SECURE_AUTH_KEY',  '{9gWk)s9~wE`r?/Y6uyPkA,1Gaw+N)/jx8o6,Kcu+)K=_fd3AGp`w<m>49lDrHpv' );

define( 'LOGGED_IN_KEY',    's$MCtVw/)-XU}ICPt+|>:#$Pr0LIC%cGk=ihNLN[YX(:u[3hoD2T:CC@J:h]_JVa' );

define( 'NONCE_KEY',        'IH*mw-lhJI<oOOm.7{~w+DU9&^;5.Y=baA7Jd&q4gxqD^9tdosfy:m`w@fdgu >v' );

define( 'AUTH_SALT',        '!DWwE&O>oR|?QH+Q?PR*r*mXs:9yH5f>s8L,:R#]#02@[`^4.@1Cd[NOMR@8H>h,' );

define( 'SECURE_AUTH_SALT', '/4U>C9{h. ?p<FyZv13O/Ff0~yXDY|7Lgvwcsx]g<F#uWv)|%s||[8/XRN/LiVX>' );

define( 'LOGGED_IN_SALT',   '}XfDF7_bfA`n<XFd+WC5*Ha7wuP^Bo:y$[}.NJx M}`%5`nO/bEu*<OMu@,6<6M=' );

define( 'NONCE_SALT',       'C~f[]-09B}.W%g4cMeIf~5cPd<lyQKJvDDCy-uAFdm]`K)^f9~jUzoY28(TP2[/q' );



/**#@-*/



/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'hf_';



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

