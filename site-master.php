<?php
/**
 * Plugin Name: Site Master
 * Plugin URI:  https://techgenyz.com/
 * Description: Shared publishing, Elementor, media, product, company, and performance infrastructure for Techgenyz and The Blissz.
 * Version:     0.0.6
 * Author:      Techgenyz
 * Text Domain: site-master
 * Domain Path: /languages
 *
 * @package SiteMaster
 */

defined( 'ABSPATH' ) || exit;

/**
 * Development source-of-truth version.
 *
 * This is the authoritative Development Version.
 * Production releases use an independent production-version track.
 */
define( 'SITE_MASTER_DEVELOPMENT_VERSION', '0.0.6' );

/**
 * WordPress/release metadata for the current development source.
 *
 * During production packaging this value and the plugin header Version are
 * changed in the temporary production staging copy only.
 */
define( 'SITE_MASTER_VERSION', '0.0.6' );

define( 'SITE_MASTER_CHANNEL', 'development' );
define( 'SITE_MASTER_SLUG', 'site-master' );
define( 'SITE_MASTER_FILE', __FILE__ );
define( 'SITE_MASTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'SITE_MASTER_URL', plugin_dir_url( __FILE__ ) );
define( 'SITE_MASTER_BASENAME', plugin_basename( __FILE__ ) );

$site_master_vendor_autoload = SITE_MASTER_PATH . 'vendor/autoload.php';
if ( is_readable( $site_master_vendor_autoload ) ) {
	require_once $site_master_vendor_autoload;
}

/**
 * Minimal PSR-4-style first-party autoloader.
 *
 * Composer may replace/augment this later. The plugin must remain able to
 * bootstrap before Composer dependencies are introduced.
 */
spl_autoload_register(
	static function ( $class ) {
		$prefix = 'Abhinava\\SiteMaster\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative = substr( $class, strlen( $prefix ) );
		$relative = str_replace( '\\', DIRECTORY_SEPARATOR, $relative );
		$file     = SITE_MASTER_PATH . 'src/' . $relative . '.php';

		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
);

register_activation_hook(
	__FILE__,
	static function () {
		\Abhinava\SiteMaster\Core\Lifecycle::activate();
	}
);

register_deactivation_hook(
	__FILE__,
	static function () {
		\Abhinava\SiteMaster\Core\Lifecycle::deactivate();
	}
);

add_action(
	'plugins_loaded',
	static function () {
		\Abhinava\SiteMaster\Core\Plugin::instance()->boot();
	}
);
