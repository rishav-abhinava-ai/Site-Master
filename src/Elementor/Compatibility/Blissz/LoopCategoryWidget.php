<?php
/** Legacy Blissz Loop Category entity adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Media\ImageRenderer;
defined( 'ABSPATH' ) || exit;
final class LoopCategoryWidget extends LegacyWidgetBase {
	public const META_BG_COLOR = 'tbz_lpb_cat_bg_color';
	public const META_TEXT_COLOR = 'tbz_lpb_cat_text_color';
	public const META_IMAGE_ID = 'tbz_lpb_cat_image_id';
	public const META_ICON_ID = 'tbz_lpb_cat_icon_id';
	public function get_name(): string { return 'blissz_loop_category_block'; }
	public function get_title(): string { return esc_html__( 'Blissz Loop Category Block', 'site-master' ); }
	public function get_icon(): string { return 'eicon-tags'; }
	public function render_compatibility( ?array $settings = null, $term = null ): string {
		$settings = $settings ?? $this->settings(); $term = $term instanceof \WP_Term ? $term : get_queried_object(); if ( ! $term instanceof \WP_Term || 'category' !== $term->taxonomy ) { return ''; }
		$url = get_term_link( $term ); if ( is_wp_error( $url ) ) { return ''; } $image_id = absint( get_term_meta( $term->term_id, self::META_IMAGE_ID, true ) ); $parts = array();
		if ( self::on( $settings, 'show_image', true ) && $image_id > 0 ) { $image = ImageRenderer::attachment( $image_id, 'large', array( 'context' => 'card', 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 768px) 100vw, 33vw' ) ); if ( '' !== $image ) { $parts[] = 'none' === ( $settings['image_link'] ?? 'category' ) ? $image : '<a class="blcb__media" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $term->name ) . '">' . $image . '</a>'; } }
		if ( self::on( $settings, 'show_badge', true ) ) { $name = esc_html( $term->name ); $parts[] = self::on( $settings, 'link_category', true ) ? '<a class="blcb__badge" href="' . esc_url( $url ) . '">' . $name . '</a>' : '<span class="blcb__badge">' . $name . '</span>'; }
		return array() !== $parts ? '<div class="blcb">' . implode( '', $parts ) . '</div>' : '';
	}
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
