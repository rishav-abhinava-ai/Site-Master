<?php
/** Profile-aware Content Analytics settings. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Config {
	public const OPTION = 'site_master_analytics_settings';
	public const RAW_RETENTION_HOURS = 72;
	public const DEDUPE_WINDOW_SECONDS = 60;

	/** @return array<string,mixed> */
	public static function defaults( string $profile ): array {
		return array(
			'enabled'                => 'techgenyz' === $profile,
			'exclude_logged_in'      => true,
			'raw_retention_hours'    => self::RAW_RETENTION_HOURS,
			'trusted_country_header' => '',
		);
	}

	/** @return array<string,mixed> */
	public static function get( string $profile ): array {
		if ( ! in_array( $profile, array( 'techgenyz', 'blissz' ), true ) ) {
			return self::defaults( 'unconfigured' );
		}
		$all   = get_option( self::OPTION, array() );
		$saved = is_array( $all ) && is_array( $all[ $profile ] ?? null ) ? $all[ $profile ] : array();
		$value = array_merge( self::defaults( $profile ), $saved );
		$value['enabled']             = (bool) $value['enabled'];
		$value['exclude_logged_in']   = (bool) $value['exclude_logged_in'];
		$value['raw_retention_hours'] = max( 48, min( 168, absint( $value['raw_retention_hours'] ) ) );
		$value['trusted_country_header'] = preg_replace( '/[^A-Z0-9_-]/', '', strtoupper( (string) $value['trusted_country_header'] ) ) ?? '';
		return $value;
	}

	public static function enabled( string $profile ): bool {
		return in_array( $profile, array( 'techgenyz', 'blissz' ), true ) && true === self::get( $profile )['enabled'];
	}
}
