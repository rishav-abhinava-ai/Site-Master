<?php
/** Transactional cursor-safe bounded analytics aggregation. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
defined( 'ABSPATH' ) || exit;

final class Aggregator {
	public const BATCH_SIZE = 500;
	private StateRepository $state;
	public function __construct() { $this->state = new StateRepository(); }

	/** @return array{processed:int,status:string} */
	public function run( string $profile ): array {
		if ( ! in_array( $profile, array( 'techgenyz','blissz' ), true ) || ! Schema::ready() ) { return array( 'processed' => 0, 'status' => 'unavailable' ); }
		global $wpdb; $t = Tables::names(); $lock = 'site_master_analytics_' . $profile;
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s,0)', $lock ) ) ) { return array( 'processed' => 0, 'status' => 'locked' ); }
		try {
			$wpdb->query( 'START TRANSACTION' );
			$cursor = absint( $this->state->get( $profile, 'last_aggregated_event_id', '0' ) );
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$t['events']} WHERE profile=%s AND id>%d ORDER BY id ASC LIMIT %d", $profile, $cursor, self::BATCH_SIZE ), ARRAY_A );
			$post_ids = array_values( array_unique( array_map( 'absint', wp_list_pluck( $rows, 'post_id' ) ) ) );
			if ( $post_ids ) { _prime_post_caches( $post_ids, true, true ); }
			foreach ( $rows as $row ) {
				if ( ! $this->aggregate_event( $row, $t ) ) { throw new \RuntimeException( 'Aggregate write failed.' ); }
				$cursor = (int) $row['id'];
			}
			if ( $rows && ! $this->state->set( $profile, 'last_aggregated_event_id', (string) $cursor ) ) { throw new \RuntimeException( 'Cursor write failed.' ); }
			$this->state->set( $profile, 'last_completed_run', gmdate( 'Y-m-d H:i:s' ) );
			$wpdb->query( 'COMMIT' );
			return array( 'processed' => count( $rows ), 'status' => 'complete' );
		} catch ( \Throwable $error ) {
			$wpdb->query( 'ROLLBACK' ); $this->state->set( $profile, 'last_error', substr( $error->getMessage(), 0, 500 ) );
			return array( 'processed' => 0, 'status' => 'failed' );
		} finally { $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) ); }
	}

	/** @param array<string,mixed> $row @param array<string,string> $t */
	private function aggregate_event( array $row, array $t ): bool {
		global $wpdb; $profile = (string) $row['profile']; $post_id = absint( $row['post_id'] ); $day = (string) $row['report_day']; $hour = gmdate( 'Y-m-d H:00:00', strtotime( (string) $row['occurred_at_utc'] . ' UTC' ) );
		$queries = array(
			$wpdb->prepare( "INSERT INTO {$t['hourly']} (profile,post_id,bucket_utc,views) VALUES (%s,%d,%s,1) ON DUPLICATE KEY UPDATE views=views+1", $profile,$post_id,$hour ),
			$wpdb->prepare( "INSERT INTO {$t['hourly']} (profile,post_id,bucket_utc,views) VALUES (%s,0,%s,1) ON DUPLICATE KEY UPDATE views=views+1", $profile,$hour ),
			$wpdb->prepare( "INSERT INTO {$t['daily']} (profile,post_id,report_day,views,estimated_readers,readers_finalized) VALUES (%s,%d,%s,1,0,0) ON DUPLICATE KEY UPDATE views=views+1", $profile,$post_id,$day ),
			$wpdb->prepare( "INSERT INTO {$t['daily']} (profile,post_id,report_day,views,estimated_readers,readers_finalized) VALUES (%s,0,%s,1,0,0) ON DUPLICATE KEY UPDATE views=views+1", $profile,$day ),
			$wpdb->prepare( "INSERT INTO {$t['totals']} (profile,post_id,views,updated_at_utc) VALUES (%s,%d,1,%s) ON DUPLICATE KEY UPDATE views=views+1,updated_at_utc=VALUES(updated_at_utc)", $profile,$post_id,gmdate('Y-m-d H:i:s') ),
		);
		foreach ( $this->dimensions( $row ) as $type => $values ) { foreach ( $values as $value ) { if ( '' !== $value ) { $queries[] = $wpdb->prepare( "INSERT INTO {$t['dimensions']} (profile,report_day,dimension_type,dimension_value,views) VALUES (%s,%s,%s,%s,1) ON DUPLICATE KEY UPDATE views=views+1", $profile,$day,$type,$value ); } } }
		foreach ( $queries as $query ) { if ( false === $wpdb->query( $query ) ) { return false; } }
		return true;
	}

	/** @param array<string,mixed> $row @return array<string,array<int,string>> */
	private function dimensions( array $row ): array {
		$post_id = absint( $row['post_id'] ); $post = get_post( $post_id );
		$out = array( 'referrer' => array( self::referrer_dimension( (string) $row['referrer_domain'] ) ), 'utm_source'=>array((string)$row['utm_source']), 'utm_medium'=>array((string)$row['utm_medium']), 'utm_campaign'=>array((string)$row['utm_campaign']), 'device'=>array((string)$row['device_class']), 'country'=>array((string)$row['country_code']) );
		if ( $post instanceof \WP_Post ) { $out['author'] = array( (string) $post->post_author ); $out['category'] = array_map( 'strval', wp_get_post_categories( $post_id ) ); $out['tag'] = array_map( 'strval', wp_get_post_tags( $post_id, array( 'fields'=>'ids' ) ) ); }
		return $out;
	}

	public static function referrer_dimension( string $domain ): string {
		return '' === $domain ? 'direct' : $domain;
	}

	public function finalize_and_prune( string $profile ): int {
		if ( ! Schema::ready() ) { return 0; }
		global $wpdb; $t=Tables::names(); $yesterday=wp_date('Y-m-d',time()-DAY_IN_SECONDS,wp_timezone());
		$wpdb->query( $wpdb->prepare( "UPDATE {$t['daily']} d SET estimated_readers=(SELECT COUNT(DISTINCT e.reader_hash) FROM {$t['events']} e WHERE e.profile=d.profile AND (d.post_id=0 OR e.post_id=d.post_id) AND e.report_day=d.report_day),readers_finalized=1 WHERE d.profile=%s AND d.report_day<=%s AND d.readers_finalized=0", $profile,$yesterday ) );
		$this->state->set( $profile, 'last_finalized_reporting_day', $yesterday );
		$retention = Config::get($profile)['raw_retention_hours']; $cutoff=gmdate('Y-m-d H:i:s',time()-HOUR_IN_SECONDS*$retention); $cursor=absint($this->state->get($profile,'last_aggregated_event_id','0'));
		return (int) $wpdb->query( $wpdb->prepare( "DELETE FROM {$t['events']} WHERE profile=%s AND id<=%d AND occurred_at_utc<%s AND report_day<=%s LIMIT 5000", $profile,$cursor,$cutoff,$yesterday ) );
	}
}
