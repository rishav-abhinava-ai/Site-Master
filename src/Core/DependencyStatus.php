<?php
/** Optional dependency detection. @package SiteMaster */

namespace Abhinava\SiteMaster\Core;

defined( 'ABSPATH' ) || exit;

final class DependencyStatus {
	/** @return array<string,bool> */
	public static function all(): array {
		return array(
			'elementor'     => did_action( 'elementor/loaded' ) > 0 || defined( 'ELEMENTOR_VERSION' ),
			'elementor_pro' => defined( 'ELEMENTOR_PRO_VERSION' ),
			'rank_math'     => defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' ),
		);
	}
}
