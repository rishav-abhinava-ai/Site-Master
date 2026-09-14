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
		/*
		 * Intentionally empty in development 0.0.2.
		 * Later phases may supply visible breadcrumb integration and data
		 * enrichment through documented Rank Math filters only.
		 */
	}
}
