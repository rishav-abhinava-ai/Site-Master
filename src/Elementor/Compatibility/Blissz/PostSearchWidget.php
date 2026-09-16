<?php
/** Legacy Blissz native-search-form adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
defined( 'ABSPATH' ) || exit;
final class PostSearchWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbm_post_search'; }
	public function get_title(): string { return esc_html__( 'Blissz Post Search', 'site-master' ); }
	public function get_icon(): string { return 'eicon-search'; }
	public function render_compatibility( ?array $settings = null, string $query = '' ): string {
		$settings = $settings ?? $this->settings(); $placeholder = sanitize_text_field( (string) ( $settings['placeholder'] ?? __( 'Type and press Enter', 'site-master' ) ) ); $label = sanitize_text_field( (string) ( $settings['aria_label'] ?? __( 'Search', 'site-master' ) ) );
		$field_id = $this->compatibility_id( 'tbm-post-search-field' );
		$query = '' !== $query ? $query : ( function_exists( 'get_search_query' ) ? get_search_query() : '' );
		$button = self::on( $settings, 'show_submit_button', false ) ? '<button class="tbm-post-search-submit" type="submit">' . esc_html( (string) ( $settings['submit_button_text'] ?? __( 'Search', 'site-master' ) ) ) . '</button>' : '';
		return '<form class="tbm-post-search" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '"><label class="screen-reader-text" for="' . esc_attr( $field_id ) . '">' . esc_html( $label ) . '</label><input id="' . esc_attr( $field_id ) . '" type="search" name="s" value="' . esc_attr( sanitize_text_field( $query ) ) . '" placeholder="' . esc_attr( $placeholder ) . '" aria-label="' . esc_attr( $label ) . '">' . $button . '</form>';
	}
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
