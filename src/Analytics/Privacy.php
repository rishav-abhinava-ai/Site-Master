<?php
/** Privacy-preserving request normalization and hashing. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Privacy {
	public static function is_bot( string $user_agent ): bool {
		return '' === trim( $user_agent ) || (bool) preg_match( '/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|headless|lighthouse|monitor|uptime|validator|preview/i', substr( $user_agent, 0, 512 ) );
	}

	public static function device( string $user_agent ): string {
		$ua = strtolower( substr( $user_agent, 0, 512 ) );
		if ( '' === $ua ) { return 'unknown'; }
		if ( preg_match( '/ipad|tablet|kindle|silk/', $ua ) ) { return 'tablet'; }
		if ( preg_match( '/mobile|iphone|ipod|android/', $ua ) ) { return 'mobile'; }
		return 'desktop';
	}

	public static function referrer_domain( string $value ): string {
		$value = trim( $value );
		if ( '' === $value || preg_match( '/[\x00-\x20\x7f]/', $value ) ) {
			return '';
		}

		if ( preg_match( '#^([a-z][a-z0-9+.-]*)://#i', $value, $matches ) ) {
			if ( ! in_array( strtolower( $matches[1] ), array( 'http', 'https' ), true ) ) {
				return '';
			}
			$host = wp_parse_url( $value, PHP_URL_HOST );
			if ( ! is_string( $host ) ) {
				return '';
			}
		} else {
			if ( preg_match( '#[/\\?\#@]#', $value ) ) {
				return '';
			}
			$host = $value;
		}

		$host      = strtolower( rtrim( $host, '.' ) );
		$host      = preg_replace( '/^www\./i', '', $host ) ?? '';
		$sanitized = preg_replace( '/[^a-z0-9.:-]/', '', $host ) ?? '';
		if ( '' === $sanitized || $sanitized !== $host ) {
			return '';
		}

		$is_ip     = false !== filter_var( $sanitized, FILTER_VALIDATE_IP );
		$is_domain = false !== filter_var( $sanitized, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME );
		return $is_ip || $is_domain ? substr( $sanitized, 0, 191 ) : '';
	}

	public static function bounded_text( $value, int $length ): string {
		return substr( sanitize_text_field( is_scalar( $value ) ? (string) $value : '' ), 0, $length );
	}

	public static function country( array $settings, array $server ): string {
		$header = (string) ( $settings['trusted_country_header'] ?? '' );
		if ( '' === $header ) { return 'ZZ'; }
		$key = 'HTTP_' . str_replace( '-', '_', $header );
		$value = strtoupper( (string) ( $server[ $key ] ?? '' ) );
		return preg_match( '/^[A-Z]{2}$/', $value ) ? $value : 'ZZ';
	}

	/** @return array{reader:string,dedupe:string} Binary 128-bit hashes. */
	public static function hashes( string $profile, int $post_id, string $ip, string $user_agent, int $timestamp, string $report_day ): array {
		$reader_key = hash_hmac( 'sha256', 'site-master-readers|' . $report_day, wp_salt( 'auth' ), true );
		$dedupe_day = gmdate( 'Y-m-d', $timestamp );
		$dedupe_key = hash_hmac( 'sha256', 'site-master-readers|' . $dedupe_day, wp_salt( 'auth' ), true );
		$identity  = substr( trim( $ip ), 0, 64 ) . '|' . strtolower( substr( trim( $user_agent ), 0, 256 ) );
		$reader    = substr( hash_hmac( 'sha256', $identity, $reader_key, true ), 0, 16 );
		$bucket    = (string) floor( $timestamp / Config::DEDUPE_WINDOW_SECONDS );
		$dedupe    = substr( hash_hmac( 'sha256', $profile . '|' . $post_id . '|' . $identity . '|' . $bucket, $dedupe_key, true ), 0, 16 );
		return array( 'reader' => $reader, 'dedupe' => $dedupe );
	}
}
