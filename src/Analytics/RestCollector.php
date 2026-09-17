<?php
/** Public narrow analytics REST collector. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
defined( 'ABSPATH' ) || exit;

final class RestCollector {
	private string $profile;
	public function __construct( string $profile ) { $this->profile = $profile; }
	public function register(): void { add_action( 'rest_api_init', array( $this, 'routes' ) ); }
	public function routes(): void {
		register_rest_route( 'site-master/v1', '/analytics/collect', array( 'methods' => 'POST', 'callback' => array( $this, 'collect' ), 'permission_callback' => '__return_true' ) );
	}
	public function collect( \WP_REST_Request $request ) {
		if ( strlen( (string) $request->get_body() ) > 2048 ) { return new \WP_REST_Response( array( 'code' => 'payload_too_large' ), 413 ); }
		$input = $request->get_json_params();
		$input = is_array( $input ) ? array_intersect_key( $input, array_flip( array( 'post_id','token','referrer','utm_source','utm_medium','utm_campaign','utm_term','utm_content' ) ) ) : array();
		$result = ( new Collector() )->collect( $input, $_SERVER, $this->profile );
		return new \WP_REST_Response( 204 === $result['http'] ? null : array( 'code' => $result['status'] ), $result['http'] );
	}
}
