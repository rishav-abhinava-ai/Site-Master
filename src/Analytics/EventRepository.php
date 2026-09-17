<?php
/** Append-only analytics event persistence. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class EventRepository {
	/** @param array<string,mixed> $event */
	public function insert( array $event ): string {
		if ( ! Schema::ready() ) { return 'unavailable'; }
		global $wpdb; $table = Tables::names()['events'];
		$result = $wpdb->insert(
			$table,
			array(
				'profile'          => $event['profile'], 'post_id' => $event['post_id'], 'occurred_at_utc' => $event['occurred_at_utc'], 'report_day' => $event['report_day'],
				'reader_hash'      => $event['reader_hash'], 'dedupe_hash' => $event['dedupe_hash'], 'referrer_domain' => $event['referrer_domain'],
				'utm_source'       => $event['utm_source'], 'utm_medium' => $event['utm_medium'], 'utm_campaign' => $event['utm_campaign'], 'utm_term' => $event['utm_term'], 'utm_content' => $event['utm_content'],
				'device_class'     => $event['device_class'], 'country_code' => $event['country_code'], 'ingestion_source' => $event['ingestion_source'],
			),
			array( '%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s' )
		);
		if ( false !== $result ) { return 'accepted'; }
		return false !== stripos( (string) $wpdb->last_error, 'duplicate' ) ? 'duplicate' : 'failed';
	}
}
