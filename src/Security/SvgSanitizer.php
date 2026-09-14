<?php
/**
 * Shared SVG sanitizer boundary.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Security;

defined( 'ABSPATH' ) || exit;

final class SvgSanitizer {
	/*
	 * The proven Blissz sanitizer is scheduled for migration in a later phase.
	 * Do not enable unsafe SVG upload behavior before that migration.
	 */
}
