<?php
/** Crawlable editorial pagination renderer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Listing;

defined( 'ABSPATH' ) || exit;

final class PaginationRenderer {
	public function render( int $current_page, int $total_pages, string $base = '' ): string {
		if ( $total_pages <= 1 ) {
			return '';
		}

		$links = paginate_links(
			array(
				'base'      => '' !== $base ? $base : str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
				'current'   => max( 1, $current_page ),
				'total'     => $total_pages,
				'type'      => 'array',
				'prev_text' => esc_html__( 'Previous', 'site-master' ),
				'next_text' => esc_html__( 'Next', 'site-master' ),
			)
		);

		if ( ! is_array( $links ) || array() === $links ) {
			return '';
		}

		return '<nav class="sm-pagination" aria-label="' . esc_attr__( 'Pagination', 'site-master' ) . '">' . implode( '', $links ) . '</nav>';
	}
}
