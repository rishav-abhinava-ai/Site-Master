<?php
/** Declarative module boundaries. @package SiteMaster */

namespace Abhinava\SiteMaster\Core;

use Abhinava\SiteMaster\Analytics\Config;
use Abhinava\SiteMaster\Profiles\ProfileInterface;

defined( 'ABSPATH' ) || exit;

final class ModuleRegistry {
	/** @return array<string,array<string,mixed>> */
	public static function status( ProfileInterface $profile ): array {
		$declared        = array( 'analytics', 'products', 'companies', 'comparisons', 'deals', 'notifications', 'api' );
		$profile_modules = $profile->modules();
		$status          = array();

		foreach ( $declared as $module ) {
			$supported = true === ( $profile_modules[ $module ] ?? false );
			$analytics_loaded = class_exists( Config::class );
			$enabled = 'analytics' === $module && $supported && $analytics_loaded && Config::enabled( $profile->key() );
			$status[ $module ] = array(
				'supported' => $supported,
				'available' => $supported,
				'enabled'   => $enabled,
				'state'     => $enabled ? 'enabled' : ( $supported ? ( 'analytics' === $module && $analytics_loaded ? 'disabled' : 'reserved' ) : 'unavailable' ),
			);
		}

		return $status;
	}
}
