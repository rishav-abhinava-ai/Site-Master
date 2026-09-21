<?php
/** Development 0.1.0 analytics schema recovery tests. */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['sm_schema_options'] = array();
$GLOBALS['sm_dbdelta_calls']  = 0;
$GLOBALS['sm_dbdelta_fail']   = '';

function sm_expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function get_option( $key, $default = false ) { return $GLOBALS['sm_schema_options'][ $key ] ?? $default; }
function update_option( $key, $value, $autoload = null ): bool { $changed = ! array_key_exists( $key, $GLOBALS['sm_schema_options'] ) || $GLOBALS['sm_schema_options'][ $key ] !== $value; $GLOBALS['sm_schema_options'][ $key ] = $value; return $changed; }
function dbDelta( $statement ): array {
	++$GLOBALS['sm_dbdelta_calls'];
	if ( preg_match( '/CREATE TABLE\s+([^\s(]+)/i', (string) $statement, $matches ) && $matches[1] !== $GLOBALS['sm_dbdelta_fail'] ) {
		$GLOBALS['wpdb']->tables[ $matches[1] ] = true;
		$GLOBALS['wpdb']->rows[ $matches[1] ] ??= array();
	}
	return array();
}

final class SM_Schema_Wpdb {
	public string $prefix = 'wp_test_';
	/** @var array<string,bool> */ public array $tables = array();
	/** @var array<string,array<int,mixed>> */ public array $rows = array();
	public function get_charset_collate(): string { return 'DEFAULT CHARACTER SET utf8mb4'; }
	public function esc_like( string $value ): string { return $value; }
	public function prepare( string $query, ...$args ): string {
		foreach ( $args as $arg ) { $query = preg_replace( '/%[sd]/', (string) $arg, $query, 1 ) ?? $query; }
		return $query;
	}
	public function get_var( string $query ) {
		if ( preg_match( '/SHOW TABLES LIKE\s+(.+)$/', $query, $matches ) ) {
			$table = trim( $matches[1], " '\"" );
			return isset( $this->tables[ $table ] ) ? $table : null;
		}
		return null;
	}
}

require_once dirname( __DIR__ ) . '/src/Analytics/Tables.php';
require_once dirname( __DIR__ ) . '/src/Analytics/Schema.php';
require_once dirname( __DIR__ ) . '/src/Profiles/ProfileInterface.php';
require_once dirname( __DIR__ ) . '/src/Analytics/AnalyticsModule.php';

use Abhinava\SiteMaster\Analytics\AnalyticsModule;
use Abhinava\SiteMaster\Analytics\Schema;
use Abhinava\SiteMaster\Analytics\Tables;
use Abhinava\SiteMaster\Profiles\ProfileInterface;

final class SM_Schema_Profile implements ProfileInterface {
	public function key(): string { return 'techgenyz'; }
	public function modules(): array { return array( 'analytics' => true ); }
}

$GLOBALS['wpdb'] = new SM_Schema_Wpdb();
$module = new AnalyticsModule( new SM_Schema_Profile() );
$names  = Tables::names();

// Case A: a healthy current schema does not invoke dbDelta again.
foreach ( $names as $table ) { $GLOBALS['wpdb']->tables[ $table ] = true; $GLOBALS['wpdb']->rows[ $table ] = array( 'preserved' ); }
$GLOBALS['sm_schema_options'][ Schema::OPTION ] = Schema::VERSION;
sm_expect( Schema::tables_ready() && Schema::ready(), 'Healthy schema was not ready.' );
$module->maybe_upgrade();
sm_expect( 0 === $GLOBALS['sm_dbdelta_calls'], 'Healthy schema unnecessarily ran dbDelta.' );

// Case B: a stale version is upgraded only after physical verification.
$GLOBALS['sm_schema_options'][ Schema::OPTION ] = 'old';
$module->maybe_upgrade();
sm_expect( 7 === $GLOBALS['sm_dbdelta_calls'], 'Stale schema did not run all seven definitions.' );
sm_expect( Schema::VERSION === get_option( Schema::OPTION ) && Schema::ready(), 'Stale schema did not become ready.' );

// Case C: current version plus one missing table repairs that table and preserves rows.
$missing = $names['dimensions']; unset( $GLOBALS['wpdb']->tables[ $missing ] );
$before_rows = $GLOBALS['wpdb']->rows;
$module->maybe_upgrade();
sm_expect( isset( $GLOBALS['wpdb']->tables[ $missing ] ) && Schema::ready(), 'Missing current-version table was not repaired.' );
foreach ( $names as $key => $table ) { if ( 'dimensions' !== $key ) { sm_expect( $before_rows[ $table ] === $GLOBALS['wpdb']->rows[ $table ], 'Existing table rows changed during repair.' ); } }

// Case D: failed physical repair must not mark a stale schema current.
unset( $GLOBALS['wpdb']->tables[ $missing ] );
$GLOBALS['sm_schema_options'][ Schema::OPTION ] = 'old';
$GLOBALS['sm_dbdelta_fail'] = $missing;
sm_expect( ! Schema::install(), 'Failed repair reported success.' );
sm_expect( 'old' === get_option( Schema::OPTION ) && ! Schema::ready(), 'Failed repair marked schema healthy.' );

// Case E: repeated healthy installs are non-destructive and idempotent.
$GLOBALS['sm_dbdelta_fail'] = '';
sm_expect( Schema::install() && Schema::install(), 'Repeat healthy installation failed.' );
foreach ( $names as $key => $table ) { if ( 'dimensions' !== $key ) { sm_expect( $before_rows[ $table ] === $GLOBALS['wpdb']->rows[ $table ], 'Repeat install reset existing rows.' ); } }

$schema_source = (string) file_get_contents( dirname( __DIR__ ) . '/src/Analytics/Schema.php' );
sm_expect( ! preg_match( '/\b(?:DROP|TRUNCATE)\s+TABLE\b/i', $schema_source ), 'Destructive table operation found.' );
echo "Site Master analytics schema recovery tests passed.\n";
