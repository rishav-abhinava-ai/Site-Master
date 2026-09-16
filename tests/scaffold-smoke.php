<?php
/** Development 0.0.2 scaffold smoke tests. */

declare(strict_types=1);

$mode = $argv[1] ?? 'suite';

if ( 'suite' === $mode ) {
	$modes = array( 'autoload', 'constant-techgenyz', 'constant-blissz', 'constant-invalid', 'saved-techgenyz', 'saved-blissz', 'saved-invalid', 'unconfigured', 'dependencies', 'dependencies-present', 'registry-techgenyz', 'registry-blissz', 'registry-unconfigured', 'profile-lock' );
	foreach ( $modes as $case ) {
		passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' ' . escapeshellarg( $case ), $code );
		if ( 0 !== $code ) {
			exit( $code );
		}
	}
	echo "Site Master scaffold smoke tests passed.\n";
	exit( 0 );
}

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['sm_test_option'] = '';

function sanitize_key( $value ): string {
	return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) ?? '' );
}
function get_option( $name, $default = false ) {
	return 'site_master_profile' === $name ? $GLOBALS['sm_test_option'] : $default;
}
function update_option( $name, $value, $autoload = null ): bool {
	if ( 'site_master_profile' === $name ) {
		$GLOBALS['sm_test_option'] = $value;
	}
	return true;
}
function did_action( $name ): int {
	return 0;
}
function plugin_dir_path( $file ): string {
	return dirname( (string) $file ) . DIRECTORY_SEPARATOR;
}
function plugin_dir_url( $file ): string {
	return 'https://example.test/wp-content/plugins/site-master/';
}
function plugin_basename( $file ): string {
	return 'site-master/site-master.php';
}
function register_activation_hook( $file, $callback ): void {}
function register_deactivation_hook( $file, $callback ): void {}
function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {}
function sm_expect( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

if ( 'autoload' === $mode ) {
	require_once dirname( __DIR__ ) . '/site-master.php';
	sm_expect( class_exists( 'Abhinava\\SiteMaster\\Core\\DependencyStatus' ), 'First-party autoload failed.' );
	sm_expect( '0.0.7' === SITE_MASTER_DEVELOPMENT_VERSION, 'Development version mismatch.' );
	echo "PASS $mode\n";
	exit( 0 );
}

require_once dirname( __DIR__ ) . '/src/Profiles/ProfileInterface.php';
require_once dirname( __DIR__ ) . '/src/Profiles/TechgenyzProfile.php';
require_once dirname( __DIR__ ) . '/src/Profiles/BlisszProfile.php';
require_once dirname( __DIR__ ) . '/src/Profiles/UnconfiguredProfile.php';
require_once dirname( __DIR__ ) . '/src/Profiles/ProfileResolver.php';
require_once dirname( __DIR__ ) . '/src/Core/DependencyStatus.php';
require_once dirname( __DIR__ ) . '/src/Core/ModuleRegistry.php';
require_once dirname( __DIR__ ) . '/src/Admin/StatusPage.php';

use Abhinava\SiteMaster\Admin\StatusPage;
use Abhinava\SiteMaster\Core\DependencyStatus;
use Abhinava\SiteMaster\Core\ModuleRegistry;
use Abhinava\SiteMaster\Profiles\ProfileResolver;

if ( str_starts_with( $mode, 'constant-' ) ) {
	define( 'SITE_MASTER_PROFILE', substr( $mode, strlen( 'constant-' ) ) );
	$GLOBALS['sm_test_option'] = 'blissz';
} elseif ( str_starts_with( $mode, 'saved-' ) ) {
	$GLOBALS['sm_test_option'] = substr( $mode, strlen( 'saved-' ) );
} elseif ( str_starts_with( $mode, 'registry-' ) && 'registry-unconfigured' !== $mode ) {
	$GLOBALS['sm_test_option'] = substr( $mode, strlen( 'registry-' ) );
} elseif ( 'profile-lock' === $mode ) {
	define( 'SITE_MASTER_PROFILE', 'techgenyz' );
	$GLOBALS['sm_test_option'] = 'blissz';
}

if ( 'dependencies-present' === $mode ) {
	define( 'ELEMENTOR_VERSION', 'test' );
	define( 'ELEMENTOR_PRO_VERSION', 'test' );
	define( 'RANK_MATH_VERSION', 'test' );
	$actual = DependencyStatus::all();
	sm_expect( array( 'elementor' => true, 'elementor_pro' => true, 'rank_math' => true ) === $actual, 'Optional dependency presence detection failed.' );
	echo "PASS $mode\n";
	exit( 0 );
}

if ( 'dependencies' === $mode ) {
	$actual = DependencyStatus::all();
	sm_expect( array( 'elementor' => false, 'elementor_pro' => false, 'rank_math' => false ) === $actual, 'Optional dependency detection failed.' );
	echo "PASS $mode\n";
	exit( 0 );
}

$profile = ProfileResolver::resolve();
if ( str_starts_with( $mode, 'registry-' ) ) {
	$status = ModuleRegistry::status( $profile );
	$techgenyz_modules = array( 'analytics', 'products', 'companies', 'comparisons', 'deals', 'notifications', 'api' );
	foreach ( $status as $name => $module ) {
		$expected_support = 'registry-techgenyz' === $mode || ( 'registry-blissz' === $mode && 'analytics' === $name );
		sm_expect( $expected_support === $module['supported'], "Incorrect support for $name in $mode." );
		sm_expect( $expected_support === $module['available'], "Incorrect availability for $name in $mode." );
		sm_expect( false === $module['enabled'], "$name unexpectedly enabled in $mode." );
		sm_expect( ( $expected_support ? 'reserved' : 'unavailable' ) === $module['state'], "Incorrect state for $name in $mode." );
	}
	sm_expect( $techgenyz_modules === array_keys( $status ), 'Global module registry changed unexpectedly.' );
	echo "PASS $mode\n";
	exit( 0 );
}

if ( 'profile-lock' === $mode ) {
	$result = StatusPage::persist_profile( 'blissz' );
	sm_expect( 'profile_locked' === $result, 'Deployment lock was not reported.' );
	sm_expect( 'blissz' === $GLOBALS['sm_test_option'], 'Deployment lock mutated the saved option.' );
	sm_expect( 'techgenyz' === ProfileResolver::resolve()->key(), 'Runtime profile ignored deployment constant.' );
	echo "PASS $mode\n";
	exit( 0 );
}

$expected = str_ends_with( $mode, 'techgenyz' ) ? 'techgenyz' : ( str_ends_with( $mode, 'blissz' ) ? 'blissz' : 'unconfigured' );
sm_expect( $expected === $profile->key(), 'Profile resolution failed for ' . $mode );
$expected_source = str_starts_with( $mode, 'constant-' ) && 'constant-invalid' !== $mode ? 'deployment constant' : ( str_starts_with( $mode, 'saved-' ) && 'saved-invalid' !== $mode ? 'saved option' : 'unconfigured' );
sm_expect( $expected_source === ProfileResolver::source(), 'Profile source failed for ' . $mode );
echo "PASS $mode\n";
