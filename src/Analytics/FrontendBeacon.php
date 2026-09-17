<?php
/** Eligible-page beacon bootstrap without analytics reads. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
defined( 'ABSPATH' ) || exit;

final class FrontendBeacon {
	private string $profile;
	public function __construct( string $profile ) { $this->profile = $profile; }
	public function register(): void { add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) ); }
	public function enqueue(): void {
		if ( ! Eligibility::frontend( $this->profile ) ) { return; }
		$post_id = (int) get_queried_object_id();
		wp_enqueue_script( 'site-master-content-analytics', SITE_MASTER_URL . 'assets/js/content-analytics.min.js', array(), SITE_MASTER_VERSION, true );
		wp_localize_script( 'site-master-content-analytics', 'siteMasterAnalytics', array( 'endpoint' => rest_url( 'site-master/v1/analytics/collect' ), 'postId' => $post_id, 'token' => Token::create( $post_id, $this->profile ) ) );
	}
}
