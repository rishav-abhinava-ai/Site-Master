<?php
/** Legacy Blissz author adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Content\AuthorData;
defined( 'ABSPATH' ) || exit;
final class AuthorWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbm-post-author-widget'; }
	public function get_title(): string { return esc_html__( 'TBM Post Author', 'site-master' ); }
	public function get_icon(): string { return 'eicon-person'; }
	public function render_compatibility( ?array $settings = null, int $post_id = 0 ): string {
		$settings = $settings ?? $this->settings(); $post_id = $post_id > 0 ? $post_id : self::post_id();
		$author_id = absint( $settings['author_id_override'] ?? 0 ); if ( $author_id <= 0 && $post_id > 0 ) { $author_id = (int) get_post_field( 'post_author', $post_id ); }
		$data = $author_id > 0 ? AuthorData::get( $author_id, array( 160, 160 ) ) : null; if ( ! is_array( $data ) ) { return ''; }
		$parts = array(); if ( self::on( $settings, 'show_avatar', true ) ) { $avatar = get_avatar( $author_id, 160, '', '', array( 'class' => array( 'tbm-post-author__avatar' ) ) ); if ( is_string( $avatar ) ) { $parts[] = $avatar; } }
		if ( self::on( $settings, 'show_name', true ) ) { $prefix = sanitize_text_field( (string) ( $settings['name_prefix'] ?? '' ) ); $name = ( '' !== $prefix ? esc_html( $prefix ) . ' ' : '' ) . esc_html( (string) $data['display_name'] ); $parts[] = '<a class="tbm-post-author__name" href="' . esc_url( (string) $data['url'] ) . '" rel="author">' . $name . '</a>'; }
		if ( self::on( $settings, 'show_bio', false ) && '' !== trim( (string) $data['bio'] ) ) { $parts[] = '<div class="tbm-post-author__bio">' . wp_kses_post( wpautop( (string) $data['bio'] ) ) . '</div>'; }
		if ( self::on( $settings, 'show_post_count', false ) ) { $parts[] = '<span class="tbm-post-author__count">' . esc_html( (string) count_user_posts( $author_id, 'post', true ) ) . '</span>'; }
		return '<div class="tbm-post-author">' . implode( '', $parts ) . '</div>';
	}
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
