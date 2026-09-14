<?php
/**
 * Rank Math integration boundary.
 *
 * IMPORTANT:
 * Site Master must not emit JSON-LD, canonical, robots, Open Graph,
 * Twitter metadata, or XML sitemaps. Rank Math remains authoritative.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\SEO;

defined( 'ABSPATH' ) || exit;

final class RankMathBridge {

	public function register(): void {
		// Reserved central boundary. No global Rank Math behavior is overridden.
	}

	public static function available(): bool {
		return defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' ) || function_exists( 'rank_math_the_breadcrumbs' );
	}

	public function breadcrumb_output(): string {
		if ( ! self::available() || ! function_exists( 'rank_math_the_breadcrumbs' ) ) {
			return '';
		}

		ob_start();
		rank_math_the_breadcrumbs();
		$output = ob_get_clean();
		return is_string( $output ) ? trim( $output ) : '';
	}
}
