<?php
/** Declarative module boundaries. @package SiteMaster */

namespace Abhinava\SiteMaster\Core;

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
			$status[ $module ] = array(
				'supported' => $supported,
				'available' => $supported,
				'enabled'   => false,
				'state'     => $supported ? 'reserved' : 'unavailable',
			);
		}

		return $status;
	}
}
