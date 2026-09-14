<?php
/**
 * Activation/deactivation lifecycle.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Core;

defined( 'ABSPATH' ) || exit;

final class Lifecycle {

	public static function activate(): void {
		/*
		 * Foundation rule:
		 * Do not overwrite existing stored Site Master or legacy plugin data.
		 * Destructive migrations are forbidden here.
		 */
	}

	public static function deactivate(): void {
		/*
		 * Deactivation is intentionally non-destructive.
		 */
	}
}
