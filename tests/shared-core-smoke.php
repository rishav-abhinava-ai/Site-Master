<?php
/** Development 0.0.3 shared-core smoke and security tests. */

declare(strict_types=1);

$mode = $argv[1] ?? 'suite';
if ( 'suite' === $mode ) {
	$modes = array( 'rankmath-off', 'breadcrumbs-fragment', 'breadcrumbs-nav', 'svg-off', 'svg-on', 'author', 'dates', 'rss' );
	foreach ( $modes as $case ) {
		passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' ' . escapeshellarg( $case ), $code );
		if ( 0 !== $code ) {
			exit( $code );
		}
	}
	echo "Site Master shared-core tests passed.\n";
	exit( 0 );
}

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['sm_caps'] = array();
$GLOBALS['sm_nonce_valid'] = false;
$GLOBALS['sm_meta'] = array();
$GLOBALS['sm_feed'] = false;
$GLOBALS['sm_content_has_image'] = false;

class WP_User { public int $ID; public string $display_name = 'Editor'; public function __construct( int $id ) { $this->ID = $id; } }
class WP_Post { public string $post_type = 'attachment'; public int $post_author = 1; }
class WP_Comment { public int $user_id = 0; }
function sm_expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function esc_html__( $text, $domain = null ): string { return (string) $text; }
function esc_attr__( $text, $domain = null ): string { return (string) $text; }
function esc_attr( $text ): string { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ): string { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function sanitize_key( $text ): string { return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $text ) ?? '' ); }
function sanitize_text_field( $text ): string { return trim( strip_tags( (string) $text ) ); }
function sanitize_html_class( $text ): string { return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $text ) ?? ''; }
function wp_unslash( $value ) { return $value; }
function absint( $value ): int { return abs( (int) $value ); }
function current_user_can( $capability, ...$args ): bool { return in_array( $capability, $GLOBALS['sm_caps'], true ); }
function wp_verify_nonce( $nonce, $action ): bool { return $GLOBALS['sm_nonce_valid']; }
function get_user_meta( $user_id, $key, $single = false ) { return $GLOBALS['sm_meta'][ $user_id ][ $key ] ?? ''; }
function update_user_meta( $user_id, $key, $value ): bool { $GLOBALS['sm_meta'][ $user_id ][ $key ] = $value; return true; }
function delete_user_meta( $user_id, $key ): bool { unset( $GLOBALS['sm_meta'][ $user_id ][ $key ] ); return true; }
function get_post( $id ) { return 10 === (int) $id ? new WP_Post() : null; }
function get_post_mime_type( $id ) { return 10 === (int) $id ? 'image/jpeg' : false; }
function get_userdata( $id ) { return $id > 0 ? new WP_User( $id ) : false; }
function get_author_posts_url( $id ): string { return 'https://example.test/author/' . $id; }
function wp_get_attachment_image_src( $id, $size ) { return 10 === (int) $id ? array( 'image.jpg', 100, 100, true ) : false; }
function wp_get_attachment_image( $id, $size, $icon = false, $attr = array() ): string { return 10 === (int) $id ? '<img src="image.jpg" width="100" height="100" srcset="image.jpg 100w" alt="">' : ''; }
function wp_get_attachment_image_url( $id, $size ) { return 10 === (int) $id ? 'image.jpg' : false; }
function is_email( $value ): bool { return false !== filter_var( $value, FILTER_VALIDATE_EMAIL ); }
function get_user_by( $field, $value ) { return false; }
function get_option( $key, $default = false ) { return 'date_format' === $key ? 'F j, Y' : ( 'time_format' === $key ? 'g:i A' : $default ); }
function get_post_datetime( $id, $field = 'date', $source = 'local' ) { $time = 'modified' === $field ? '2026-09-14 16:30:00' : '2026-09-14 15:30:00'; return new DateTimeImmutable( $time, new DateTimeZone( 'Asia/Kolkata' ) ); }
function wp_timezone(): DateTimeZone { return new DateTimeZone( 'Asia/Kolkata' ); }
function wp_date( $format, $timestamp, $timezone = null ): string { return ( new DateTimeImmutable( '@' . $timestamp ) )->setTimezone( $timezone ?: wp_timezone() )->format( $format ); }
function wp_kses( $html, $allowed ): string { return preg_replace( '/\s(?:itemscope|itemtype|itemprop)=(?:"[^"]*"|\'[^\']*\')/i', '', strip_tags( $html, '<nav><p><span><a>' ) ) ?? ''; }
function is_feed(): bool { return $GLOBALS['sm_feed']; }
function get_the_ID(): int { return 1; }
function get_post_thumbnail_id( $id ): int { return 10; }

if ( str_starts_with( $mode, 'breadcrumbs-' ) ) {
	define( 'RANK_MATH_VERSION', 'test' );
	$GLOBALS['sm_breadcrumb_markup'] = 'breadcrumbs-nav' === $mode ? '<nav aria-label="Breadcrumb"><a href="/">Home</a></nav>' : '<p><a href="/">Home</a></p>';
	eval( 'function rank_math_the_breadcrumbs(){ echo $GLOBALS["sm_breadcrumb_markup"]; }' );
}

if ( 'svg-on' === $mode ) {
	eval( 'namespace enshrined\\svgSanitize; class Sanitizer { public function removeRemoteReferences($remove){} public function sanitize($data){ if(!preg_match("/<svg\\b[^>]*>.*<\\/svg>/s",$data) || preg_match("/<script|onload\\s*=|javascript:|<foreignObject|<(?:image|use)[^>]+(?:href|src)=.[ ]*https?:\\/\\//i",$data)){return false;} return $data;} }' );
}

require_once dirname( __DIR__ ) . '/src/SEO/RankMathBridge.php';
require_once dirname( __DIR__ ) . '/src/Components/Breadcrumbs/BreadcrumbRenderer.php';
require_once dirname( __DIR__ ) . '/src/Security/SvgSanitizer.php';
require_once dirname( __DIR__ ) . '/src/Content/AuthorData.php';
require_once dirname( __DIR__ ) . '/src/Support/DateFormatter.php';
require_once dirname( __DIR__ ) . '/src/Content/RssFeaturedImage.php';

use Abhinava\SiteMaster\Components\Breadcrumbs\BreadcrumbRenderer;
use Abhinava\SiteMaster\Content\AuthorData;
use Abhinava\SiteMaster\Content\RssFeaturedImage;
use Abhinava\SiteMaster\Security\SvgSanitizer;
use Abhinava\SiteMaster\SEO\RankMathBridge;
use Abhinava\SiteMaster\Support\DateFormatter;

if ( 'rankmath-off' === $mode ) {
	sm_expect( ! RankMathBridge::available(), 'Rank Math false-positive.' );
	sm_expect( '' === ( new BreadcrumbRenderer() )->render(), 'Unavailable breadcrumbs emitted output.' );
} elseif ( str_starts_with( $mode, 'breadcrumbs-' ) ) {
	$output = ( new BreadcrumbRenderer() )->render();
	sm_expect( 1 === preg_match_all( '/<nav\b/i', $output ), 'Breadcrumb nav count is not one.' );
	sm_expect( false === stripos( $output, 'itemtype' ), 'Breadcrumb schema attribute emitted.' );
} elseif ( 'svg-off' === $mode ) {
	$GLOBALS['sm_caps'] = array( 'manage_options', 'upload_files' );
	$service = new SvgSanitizer();
	sm_expect( ! isset( $service->allowed_mimes( array() )['svg'] ), 'SVG enabled without sanitizer.' );
	$file = $service->sanitize_upload( array( 'name' => 'safe.svg' ) );
	sm_expect( isset( $file['error'] ), 'Unavailable sanitizer did not fail closed.' );
} elseif ( 'svg-on' === $mode ) {
	$GLOBALS['sm_caps'] = array( 'manage_options', 'upload_files' );
	$service = new SvgSanitizer();
	$payloads = array(
		'valid' => '<svg xmlns="http://www.w3.org/2000/svg"><circle cx="5" cy="5" r="5"/></svg>',
		'malformed' => '<svg><circle>',
		'script' => '<svg><script>alert(1)</script></svg>',
		'event' => '<svg onload="alert(1)"></svg>',
		'javascript' => '<svg><a href="javascript:alert(1)">x</a></svg>',
		'external' => '<svg><image href="https://evil.test/x"/></svg>',
		'embedded' => '<svg><foreignObject>bad</foreignObject></svg>',
	);
	foreach ( $payloads as $name => $payload ) {
		$path = tempnam( sys_get_temp_dir(), 'sm-svg-' );
		file_put_contents( $path, $payload );
		$result = $service->sanitize_upload( array( 'name' => $name . '.svg', 'tmp_name' => $path, 'size' => strlen( $payload ) ) );
		sm_expect( ( 'valid' === $name ) === ! isset( $result['error'] ), "Unexpected SVG result: $name" );
		unlink( $path );
	}
} elseif ( 'author' === $mode ) {
	$service = new AuthorData();
	$GLOBALS['sm_meta'][1]['profile_picture'] = 10;
	sm_expect( 10 === AuthorData::attachment_id( 1 ), 'Valid profile picture not read.' );
	$GLOBALS['sm_meta'][1]['profile_picture'] = 99;
	sm_expect( 0 === AuthorData::attachment_id( 1 ), 'Invalid attachment did not fall back.' );
	sm_expect( '<img class="default">' === $service->filter_avatar( '<img class="default">', 1, 96, '', '', array() ), 'Default avatar fallback changed.' );
	$_POST = array( 'site_master_profile_picture_nonce' => 'x', 'site_master_profile_picture_id' => '10' );
	sm_expect( ! $service->save_profile_field( 1 ), 'Unauthorized update accepted.' );
	$GLOBALS['sm_caps'] = array( 'edit_user' );
	sm_expect( ! $service->save_profile_field( 1 ), 'Bad nonce accepted.' );
	$GLOBALS['sm_nonce_valid'] = true;
	sm_expect( $service->save_profile_field( 1 ) && 10 === $GLOBALS['sm_meta'][1]['profile_picture'], 'Authorized update failed.' );
	$_POST['site_master_profile_picture_id'] = '';
	sm_expect( $service->save_profile_field( 1 ) && ! isset( $GLOBALS['sm_meta'][1]['profile_picture'] ), 'Safe removal failed.' );
} elseif ( 'dates' === $mode ) {
	$published = DateFormatter::published( 1 );
	$modified = DateFormatter::modified( 1 );
	sm_expect( str_ends_with( $published['datetime'], '+05:30' ), 'ISO timezone incorrect.' );
	sm_expect( 'September 14, 2026 3:30 PM' === $published['text'], 'Visible date incorrect.' );
	sm_expect( DateFormatter::meaningfully_modified( 1 ), 'Modified date distinction failed.' );
	sm_expect( str_starts_with( DateFormatter::render( $modified ), '<time datetime=' ), 'Semantic time output failed.' );
} elseif ( 'rss' === $mode ) {
	$service = new RssFeaturedImage();
	sm_expect( 'Body' === $service->prepend( 'Body' ), 'Non-feed content changed.' );
	$GLOBALS['sm_feed'] = true;
	$with_image = $service->prepend( 'Body' );
	sm_expect( str_contains( $with_image, '<img ' ), 'Feed image missing.' );
	sm_expect( $with_image === $service->prepend( $with_image ), 'Feed image duplicated.' );
}

echo "PASS $mode\n";
