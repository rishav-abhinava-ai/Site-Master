<?php
/** Visible article topics footer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Article;

defined( 'ABSPATH' ) || exit;

final class ArticleFooterRenderer {
	/** @param array<int,mixed> $topics */
	public function render( array $topics, bool $show_topics = true ): string {
		if ( ! $show_topics ) {
			return '';
		}

		$links = array();
		foreach ( $topics as $topic ) {
			if ( is_array( $topic ) && ! empty( $topic['url'] ) && ! empty( $topic['name'] ) ) {
				$links[] = '<a href="' . esc_url( (string) $topic['url'] ) . '">' . esc_html( (string) $topic['name'] ) . '</a>';
			}
		}
		if ( array() === $links ) {
			return '';
		}

		return '<footer class="sm-article__footer"><p class="sm-article__topics"><span class="sm-article__topics-label">' . esc_html__( 'Topics:', 'site-master' ) . '</span> ' . implode( ' ', $links ) . '</p></footer>';
	}
}
