<?php
/**
 * The Blissz profile.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Profiles;

defined( 'ABSPATH' ) || exit;

final class BlisszProfile implements ProfileInterface {

	public function key(): string {
		return 'blissz';
	}

	public function modules(): array {
		return array(
			'analytics'     => true,
			'products'      => false,
			'companies'     => false,
			'comparisons'   => false,
			'deals'         => false,
			'notifications' => false,
			'api'           => false,
		);
	}
}
