<?php
/** Semantic site-sidebar boundary. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Layout;

defined( 'ABSPATH' ) || exit;

final class SidebarRenderer {
	public function render( string $content, string $label = '' ): string {
		if ( '' === trim( $content ) || preg_match( '/<main\b/i', $content ) ) {
			return '';
		}

		$label = sanitize_text_field( $label );
		if ( '' === $label ) {
			$label = __( 'Sidebar', 'site-master' );
		}
		return '<aside class="sm-sidebar" aria-label="' . esc_attr( $label ) . '">' . $content . '</aside>';
	}
}
