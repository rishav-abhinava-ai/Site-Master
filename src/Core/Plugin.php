<?php
/**
 * Site Master plugin bootstrap.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Core;

use Abhinava\SiteMaster\Admin\StatusPage;
use Abhinava\SiteMaster\Analytics\AnalyticsModule;
use Abhinava\SiteMaster\Content\AuthorData;
use Abhinava\SiteMaster\Content\RssFeaturedImage;
use Abhinava\SiteMaster\Elementor\ElementorIntegration;
use Abhinava\SiteMaster\Elementor\Compatibility\Blissz\BlisszCompatibility;
use Abhinava\SiteMaster\Profiles\ProfileResolver;
use Abhinava\SiteMaster\Security\SvgSanitizer;
use Abhinava\SiteMaster\SEO\RankMathBridge;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	private static ?Plugin $instance = null;

	private bool $booted = false;

	private function __construct() {}

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		load_plugin_textdomain(
			'site-master',
			false,
			dirname( SITE_MASTER_BASENAME ) . '/languages'
		);

		$profile = ProfileResolver::resolve();
		if ( ProfileResolver::BLISSZ === $profile->key() ) {
			( new BlisszCompatibility() )->register();
		}
		( new SvgSanitizer() )->register();
		( new AuthorData() )->register();
		( new RankMathBridge() )->register();
		( new RssFeaturedImage() )->register();
		( new ElementorIntegration( $profile ) )->register();
		( new AnalyticsModule( $profile ) )->register();

		if ( is_admin() ) {
			( new StatusPage( $profile ) )->register();
		}

		/**
		 * Fires after Site Master has resolved its active profile.
		 *
		 * Blissz compatibility is profile-gated in Development 0.0.8.
		 */
		do_action( 'site_master/booted', $profile );
	}

	private function __clone() {}

	public function __wakeup(): void {
		throw new \LogicException( 'Site Master singleton cannot be unserialized.' );
	}
}
