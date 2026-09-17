<?php
/**
 * Activation/deactivation lifecycle.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Core;

use Abhinava\SiteMaster\Analytics\AnalyticsModule;

defined( 'ABSPATH' ) || exit;

final class Lifecycle {

	public static function activate(): void {
		/*
		 * Foundation rule:
		 * Do not overwrite existing stored Site Master or legacy plugin data.
		 * Destructive migrations are forbidden here.
		 */
		AnalyticsModule::activate();
	}

	public static function deactivate(): void {
		/*
		 * Deactivation is intentionally non-destructive.
		 */
		AnalyticsModule::deactivate();
	}
}
