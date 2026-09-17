<?php
/** Analytics table-name boundary. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Tables {
	/** @return array<string,string> */
	public static function names(): array {
		global $wpdb;
		return array(
			'events'     => $wpdb->prefix . 'sm_analytics_events',
			'hourly'     => $wpdb->prefix . 'sm_analytics_hourly',
			'daily'      => $wpdb->prefix . 'sm_analytics_daily',
			'dimensions' => $wpdb->prefix . 'sm_analytics_dimensions',
			'totals'     => $wpdb->prefix . 'sm_analytics_post_totals',
			'baseline'   => $wpdb->prefix . 'sm_analytics_legacy_baseline',
			'state'      => $wpdb->prefix . 'sm_analytics_state',
		);
	}
}
