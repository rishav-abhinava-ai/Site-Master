<?php
/**
 * Elementor integration boundary.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Elementor;

defined( 'ABSPATH' ) || exit;

final class ElementorIntegration {

	public function register(): void {
		/*
		 * The shared Post Card/listing architecture does not require Elementor.
		 * The same is true for the complete PHP article renderer. Theme Builder
		 * should use one native Container with HTML Tag = article; the convention
		 * helper documents/validates that tag without rewriting Elementor output.
		 * Widget registration and legacy-ID adapters remain deferred.
		 */
	}
}
