<?php
/** Durable non-autoloaded analytics state. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
defined( 'ABSPATH' ) || exit;

final class StateRepository {
	public function get( string $profile, string $key, string $default = '' ): string {
		global $wpdb;
		$table = Tables::names()['state'];
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT state_value FROM {$table} WHERE profile=%s AND state_key=%s", $profile, $key ) );
		return null === $value ? $default : (string) $value;
	}

	public function set( string $profile, string $key, string $value ): bool {
		global $wpdb;
		$table = Tables::names()['state'];
		$sql = $wpdb->prepare(
			"INSERT INTO {$table} (profile,state_key,state_value,updated_at_utc) VALUES (%s,%s,%s,%s) ON DUPLICATE KEY UPDATE state_value=VALUES(state_value),updated_at_utc=VALUES(updated_at_utc)",
			$profile, $key, $value, gmdate( 'Y-m-d H:i:s' )
		);
		return false !== $wpdb->query( $sql );
	}
}
