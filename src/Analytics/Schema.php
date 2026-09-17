<?php
/** Versioned non-destructive analytics schema. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Schema {
	public const VERSION = '1';
	public const OPTION = 'site_master_analytics_schema_version';

	public static function install(): bool {
		global $wpdb;
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		$t = Tables::names(); $c = $wpdb->get_charset_collate();
		$sql = array(
			"CREATE TABLE {$t['events']} (
				id bigint(20) unsigned NOT NULL auto_increment,
				profile varchar(16) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				occurred_at_utc datetime NOT NULL,
				report_day date NOT NULL,
				reader_hash binary(16) NOT NULL,
				dedupe_hash binary(16) NOT NULL,
				referrer_domain varchar(191) NOT NULL default '',
				utm_source varchar(100) NOT NULL default '',
				utm_medium varchar(100) NOT NULL default '',
				utm_campaign varchar(150) NOT NULL default '',
				utm_term varchar(100) NOT NULL default '',
				utm_content varchar(100) NOT NULL default '',
				device_class varchar(16) NOT NULL default 'unknown',
				country_code char(2) NOT NULL default 'ZZ',
				ingestion_source varchar(16) NOT NULL default 'beacon',
				PRIMARY KEY  (id),
				UNIQUE KEY dedupe (profile,dedupe_hash),
				KEY profile_time (profile,occurred_at_utc),
				KEY profile_post_time (profile,post_id,occurred_at_utc),
				KEY profile_day_reader (profile,report_day,reader_hash)
			) $c;",
			"CREATE TABLE {$t['hourly']} (
				profile varchar(16) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				bucket_utc datetime NOT NULL,
				views bigint(20) unsigned NOT NULL default 0,
				PRIMARY KEY  (profile,post_id,bucket_utc),
				KEY profile_bucket (profile,bucket_utc)
			) $c;",
			"CREATE TABLE {$t['daily']} (
				profile varchar(16) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				report_day date NOT NULL,
				views bigint(20) unsigned NOT NULL default 0,
				estimated_readers bigint(20) unsigned NOT NULL default 0,
				readers_finalized tinyint(1) NOT NULL default 0,
				PRIMARY KEY  (profile,post_id,report_day),
				KEY profile_day (profile,report_day)
			) $c;",
			"CREATE TABLE {$t['dimensions']} (
				profile varchar(16) NOT NULL,
				report_day date NOT NULL,
				dimension_type varchar(24) NOT NULL,
				dimension_value varchar(191) NOT NULL,
				views bigint(20) unsigned NOT NULL default 0,
				PRIMARY KEY  (profile,report_day,dimension_type,dimension_value),
				KEY profile_type_day (profile,dimension_type,report_day)
			) $c;",
			"CREATE TABLE {$t['totals']} (
				profile varchar(16) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				views bigint(20) unsigned NOT NULL default 0,
				updated_at_utc datetime NOT NULL,
				PRIMARY KEY  (profile,post_id),
				KEY profile_views (profile,views)
			) $c;",
			"CREATE TABLE {$t['baseline']} (
				profile varchar(16) NOT NULL,
				post_id bigint(20) unsigned NOT NULL,
				baseline_count bigint(20) unsigned NOT NULL default 0,
				source_key varchar(64) NOT NULL,
				captured_at_utc datetime NOT NULL,
				cutover_at_utc datetime NOT NULL,
				PRIMARY KEY  (profile,post_id),
				KEY profile_count (profile,baseline_count)
			) $c;",
			"CREATE TABLE {$t['state']} (
				profile varchar(16) NOT NULL,
				state_key varchar(64) NOT NULL,
				state_value longtext NOT NULL,
				updated_at_utc datetime NOT NULL,
				PRIMARY KEY  (profile,state_key)
			) $c;",
		);
		foreach ( $sql as $statement ) { dbDelta( $statement ); }
		update_option( self::OPTION, self::VERSION, false );
		return self::ready();
	}

	public static function ready(): bool {
		global $wpdb; $t = Tables::names();
		if ( self::VERSION !== (string) get_option( self::OPTION, '' ) ) { return false; }
		foreach ( $t as $table ) {
			$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
			if ( $found !== $table ) { return false; }
		}
		return true;
	}
}
