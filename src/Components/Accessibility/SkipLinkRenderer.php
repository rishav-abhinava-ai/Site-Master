<?php
/** Explicit, theme-compatible skip-link helper. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Accessibility;

defined( 'ABSPATH' ) || exit;

final class SkipLinkRenderer {
	public function render( string $target_id = 'main', string $label = '' ): string {
		$target_id = sanitize_html_class( $target_id );
		if ( '' === $target_id ) {
			return '';
		}

		$label = '' !== trim( $label ) ? $label : __( 'Skip to content', 'site-master' );
		return '<a class="sm-skip-link" href="#' . esc_attr( $target_id ) . '">' . esc_html( $label ) . '</a>';
	}
}
