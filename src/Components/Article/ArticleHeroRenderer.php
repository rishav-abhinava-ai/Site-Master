<?php
/** Single-article hero renderer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Article;

use Abhinava\SiteMaster\Media\ImageRenderer;

defined( 'ABSPATH' ) || exit;

final class ArticleHeroRenderer {
	/** @param array<string,mixed> $data @param array<string,mixed> $options */
	public function render( array $data, array $options ): string {
		if ( ! $options['show_image'] || (int) $data['image_id'] <= 0 ) {
			return '';
		}

		$image = ImageRenderer::hero(
			(int) $data['image_id'],
			$options['image_size'],
			array(
				'alt'   => (string) $data['image_alt'],
				'sizes' => $options['image_sizes'],
				'loading' => $options['image_loading'] ?? 'eager',
				'fetchpriority' => $options['image_priority'] ?? 'auto',
			)
		);
		if ( '' === $image ) {
			return '';
		}

		$caption = '';
		if ( $options['show_caption'] && '' !== trim( (string) $data['image_caption'] ) ) {
			$caption = '<figcaption>' . esc_html( (string) $data['image_caption'] ) . '</figcaption>';
		}

		return '<figure class="sm-article__hero">' . $image . $caption . '</figure>';
	}
}
