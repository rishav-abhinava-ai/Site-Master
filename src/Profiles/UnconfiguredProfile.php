<?php
/** Safe profile until explicitly configured. @package SiteMaster */

namespace Abhinava\SiteMaster\Profiles;

defined( 'ABSPATH' ) || exit;

final class UnconfiguredProfile implements ProfileInterface {
	public function key(): string {
		return 'unconfigured';
	}

	public function modules(): array {
		return array();
	}
}
