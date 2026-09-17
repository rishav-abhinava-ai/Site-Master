<?php
/** Immutable Techgenyz lifetime-view baseline importer. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
defined( 'ABSPATH' ) || exit;

final class BaselineImporter {
	public const LEGACY_KEY = 'post_views_count';
	public const BATCH_SIZE = 250;

	public static function normalize_count( $value ): int {
		return is_numeric( $value ) ? max( 0, (int) $value ) : 0;
	}

	/** @return array{imported:int,complete:bool} */
	public function run( string $profile ): array {
		if ( 'techgenyz' !== $profile || ! Schema::ready() ) { return array( 'imported' => 0, 'complete' => true ); }
		global $wpdb;
		$state = new StateRepository();
		if ( '1' === $state->get( $profile, 'baseline_complete' ) ) { return array( 'imported' => 0, 'complete' => true ); }
		$cursor = absint( $state->get( $profile, 'baseline_cursor', '0' ) );
		$cutover = $state->get( $profile, 'cutover_at_utc' );
		if ( '' === $cutover ) { $cutover = gmdate( 'Y-m-d H:i:s' ); $state->set( $profile, 'cutover_at_utc', $cutover ); }
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT p.ID post_id, MAX(CASE WHEN pm.meta_value REGEXP '^[0-9]+$' THEN CAST(pm.meta_value AS UNSIGNED) ELSE 0 END) baseline_count FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID AND pm.meta_key=%s WHERE p.ID>%d AND p.post_type='post' AND p.post_status='publish' GROUP BY p.ID ORDER BY p.ID ASC LIMIT %d",
			self::LEGACY_KEY, $cursor, self::BATCH_SIZE
		), ARRAY_A );
		$imported = 0; $table = Tables::names()['baseline'];
		foreach ( $rows as $row ) {
			$post_id = absint( $row['post_id'] ); $count = self::normalize_count( $row['baseline_count'] );
			$sql = $wpdb->prepare( "INSERT IGNORE INTO {$table} (profile,post_id,baseline_count,source_key,captured_at_utc,cutover_at_utc) VALUES (%s,%d,%d,%s,%s,%s)", $profile, $post_id, $count, self::LEGACY_KEY, gmdate( 'Y-m-d H:i:s' ), $cutover );
			if ( 1 === $wpdb->query( $sql ) ) { ++$imported; }
			$cursor = max( $cursor, $post_id );
		}
		$state->set( $profile, 'baseline_cursor', (string) $cursor );
		$complete = count( $rows ) < self::BATCH_SIZE;
		if ( $complete ) { $state->set( $profile, 'baseline_complete', '1' ); }
		return array( 'imported' => $imported, 'complete' => $complete );
	}
}
