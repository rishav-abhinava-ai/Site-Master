<?php
/** Legacy Blissz date adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Support\DateFormatter;
defined( 'ABSPATH' ) || exit;
final class DateWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbm-date-widget'; }
	public function get_title(): string { return esc_html__( 'TBM Date', 'site-master' ); }
	public function get_icon(): string { return 'eicon-calendar'; }
	public function render_compatibility( ?array $settings = null, int $post_id = 0 ): string {
		$settings = $settings ?? $this->settings(); $post_id = $post_id > 0 ? $post_id : self::post_id(); if ( $post_id <= 0 ) { return ''; }
		$formats = array( 'india_apac' => array( 'M j, Y', 'g:i A T' ), 'us_12h' => array( 'm/d/Y', 'g:i A T' ), 'uk_24h' => array( 'j M Y', 'H:i T' ), 'eu_24h' => array( 'd/m/Y', 'H:i T' ), 'iso_24h' => array( 'Y-m-d', 'H:i T' ), 'weekday' => array( 'D, j M Y', 'H:i T' ) );
		$preset = sanitize_key( (string) ( $settings['absolute_format_preset'] ?? 'india_apac' ) ); $parts = $formats[ $preset ] ?? $formats['india_apac']; $view = (string) ( $settings['date_time_view'] ?? 'date_time' );
		$format = 'date_only' === $view ? $parts[0] : ( 'time_only' === $view ? $parts[1] : $parts[0] . ', ' . $parts[1] );
		return '<div class="elementor-post-date-wrapper">' . DateFormatter::render( DateFormatter::published( $post_id, $format ), array( 'class' => 'tbm-date' ) ) . '</div>';
	}
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
