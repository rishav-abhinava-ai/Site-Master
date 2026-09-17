<?php
/** Narrow editorial tracking eligibility. @package SiteMaster */

namespace Abhinava\SiteMaster\Analytics;

defined( 'ABSPATH' ) || exit;

final class Eligibility {
	public static function post( int $post_id ): bool {
		$post = get_post( $post_id );
		return $post instanceof \WP_Post && 'post' === $post->post_type && 'publish' === $post->post_status && '' === (string) $post->post_password;
	}

	public static function frontend( string $profile ): bool {
		if ( ! Config::enabled( $profile ) || is_admin() || wp_doing_ajax() || wp_is_json_request() || wp_doing_cron() || is_feed() || is_preview() || ! is_singular( 'post' ) ) { return false; }
		if ( is_user_logged_in() && Config::get( $profile )['exclude_logged_in'] ) { return false; }
		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['preview'] ) ) { return false; }
		return self::post( (int) get_queried_object_id() );
	}
}
