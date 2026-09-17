<?php
/** Cache-safe non-reader collector token. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Token {
	public static function create( int $post_id, string $profile ): string {
		return hash_hmac( 'sha256', 'collect|' . $profile . '|' . $post_id, wp_salt( 'nonce' ) );
	}

	public static function valid( string $token, int $post_id, string $profile ): bool {
		return 64 === strlen( $token ) && hash_equals( self::create( $post_id, $profile ), $token );
	}
}
