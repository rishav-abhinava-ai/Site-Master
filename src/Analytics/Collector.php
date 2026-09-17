<?php
/** Shared REST/beacon and legacy-adapter ingestion service. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Collector {
	private EventRepository $events;

	public function __construct( ?EventRepository $events = null ) { $this->events = $events ?? new EventRepository(); }

	/** @param array<string,mixed> $payload @param array<string,mixed> $server @return array{status:string,http:int} */
	public function collect( array $payload, array $server, string $profile, string $source = 'beacon', bool $require_token = true ): array {
		if ( ! Config::enabled( $profile ) || ! in_array( $profile, array( 'techgenyz', 'blissz' ), true ) ) { return array( 'status' => 'disabled', 'http' => 204 ); }
		if ( is_user_logged_in() && Config::get( $profile )['exclude_logged_in'] ) { return array( 'status' => 'staff', 'http' => 204 ); }
		$method = strtoupper( (string) ( $server['REQUEST_METHOD'] ?? 'POST' ) );
		$ua = (string) ( $server['HTTP_USER_AGENT'] ?? '' );
		if ( 'POST' !== $method || Privacy::is_bot( $ua ) || 'HEAD' === $method || ! empty( $server['HTTP_PURPOSE'] ) || ! empty( $server['HTTP_SEC_PURPOSE'] ) ) { return array( 'status' => 'rejected', 'http' => 400 ); }
		$post_id = absint( $payload['post_id'] ?? 0 );
		if ( ! Eligibility::post( $post_id ) ) { return array( 'status' => 'invalid_post', 'http' => 400 ); }
		if ( $require_token && ! Token::valid( (string) ( $payload['token'] ?? '' ), $post_id, $profile ) ) { return array( 'status' => 'invalid_token', 'http' => 403 ); }
		$origin = (string) ( $server['HTTP_ORIGIN'] ?? '' );
		if ( '' !== $origin ) {
			$origin_host = wp_parse_url( $origin, PHP_URL_HOST ); $home_host = wp_parse_url( home_url(), PHP_URL_HOST );
			if ( ! is_string( $origin_host ) || ! is_string( $home_host ) || strtolower( $origin_host ) !== strtolower( $home_host ) ) { return array( 'status' => 'origin', 'http' => 403 ); }
		}
		$now        = time();
		$report_day = wp_date( 'Y-m-d', $now, wp_timezone() );
		$hashes     = Privacy::hashes( $profile, $post_id, (string) ( $server['REMOTE_ADDR'] ?? '' ), $ua, $now, $report_day );
		$settings   = Config::get( $profile );
		$event = array(
			'profile' => $profile, 'post_id' => $post_id, 'occurred_at_utc' => gmdate( 'Y-m-d H:i:s', $now ), 'report_day' => $report_day,
			'reader_hash' => $hashes['reader'], 'dedupe_hash' => $hashes['dedupe'],
			'referrer_domain' => Privacy::referrer_domain( (string) ( $payload['referrer'] ?? '' ) ),
			'utm_source' => Privacy::bounded_text( $payload['utm_source'] ?? '', 100 ), 'utm_medium' => Privacy::bounded_text( $payload['utm_medium'] ?? '', 100 ),
			'utm_campaign' => Privacy::bounded_text( $payload['utm_campaign'] ?? '', 150 ), 'utm_term' => Privacy::bounded_text( $payload['utm_term'] ?? '', 100 ), 'utm_content' => Privacy::bounded_text( $payload['utm_content'] ?? '', 100 ),
			'device_class' => Privacy::device( $ua ), 'country_code' => Privacy::country( $settings, $server ), 'ingestion_source' => 'legacy' === $source ? 'legacy' : 'beacon',
		);
		$status = $this->events->insert( $event );
		return array( 'status' => $status, 'http' => in_array( $status, array( 'accepted', 'duplicate' ), true ) ? 204 : 503 );
	}
}
