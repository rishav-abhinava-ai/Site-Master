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
		 * Widget registration and legacy-ID adapters remain deferred to their
		 * authorized migration phases so saved templates are not disturbed.
		 */
	}
}
