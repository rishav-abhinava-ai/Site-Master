<?php
/**
 * Techgenyz profile.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Profiles;

defined( 'ABSPATH' ) || exit;

final class TechgenyzProfile implements ProfileInterface {

	public function key(): string {
		return 'techgenyz';
	}

	public function modules(): array {
		/*
		 * Module enabling is intentionally deferred to later development phases.
		 * These keys document the intended Techgenyz-specific boundaries.
		 */
		return array(
			'analytics'     => true,
			'products'      => true,
			'companies'     => true,
			'comparisons'   => true,
			'deals'         => true,
			'notifications' => true,
			'api'           => true,
		);
	}
}
