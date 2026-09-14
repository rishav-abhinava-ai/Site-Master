<?php
/** Normal WordPress article-content processing boundary. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Article;

defined( 'ABSPATH' ) || exit;

final class ArticleBodyRenderer {
	public function render( string $content ): string {
		if ( '' === trim( $content ) ) {
			return '';
		}

		$processed = apply_filters( 'the_content', $content );
		if ( ! is_string( $processed ) || '' === trim( $processed ) ) {
			return '';
		}

		return '<div class="sm-article__body">' . $processed . '</div>';
	}
}
