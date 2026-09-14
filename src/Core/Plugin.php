<?php
/**
 * Site Master plugin bootstrap.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Core;

use Abhinava\SiteMaster\Admin\StatusPage;
use Abhinava\SiteMaster\Profiles\ProfileResolver;

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

		if ( is_admin() ) {
			( new StatusPage( $profile ) )->register();
		}

		/**
		 * Fires after Site Master has resolved its active profile.
		 *
		 * No later-phase feature modules are enabled in the 0.0.2 scaffold.
		 */
		do_action( 'site_master/booted', $profile );
	}

	private function __clone() {}

	public function __wakeup(): void {
		throw new \LogicException( 'Site Master singleton cannot be unserialized.' );
	}
}
