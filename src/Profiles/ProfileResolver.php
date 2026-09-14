<?php
/**
 * Site profile resolver.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Profiles;

defined( 'ABSPATH' ) || exit;

final class ProfileResolver {

	public const TECHGENYZ = 'techgenyz';
	public const BLISSZ    = 'blissz';
	public const OPTION    = 'site_master_profile';

	public static function key(): string {
		if ( defined( 'SITE_MASTER_PROFILE' ) ) {
			$key = sanitize_key( (string) SITE_MASTER_PROFILE );
		} else {
			$saved = get_option( self::OPTION, '' );
			$key   = sanitize_key( is_string( $saved ) ? $saved : '' );
		}

		return in_array( $key, self::supported(), true ) ? $key : '';
	}

	public static function source(): string {
		if ( defined( 'SITE_MASTER_PROFILE' ) ) {
			return '' !== self::key() ? 'deployment constant' : 'unconfigured';
		}

		return '' !== self::key() ? 'saved option' : 'unconfigured';
	}

	/** @return string[] */
	public static function supported(): array {
		return array( self::TECHGENYZ, self::BLISSZ );
	}

	public static function resolve(): ProfileInterface {
		$key = self::key();

		if ( self::BLISSZ === $key ) {
			return new BlisszProfile();
		}

		if ( self::TECHGENYZ === $key ) {
			return new TechgenyzProfile();
		}

		return new UnconfiguredProfile();
	}
}
