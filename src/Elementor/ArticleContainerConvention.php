<?php
/** Elementor Theme Builder article-parent convention helper. @package SiteMaster */

namespace Abhinava\SiteMaster\Elementor;

defined( 'ABSPATH' ) || exit;

final class ArticleContainerConvention {
	public const REQUIRED_TAG = 'article';

	public static function is_valid_tag( string $tag ): bool {
		return self::REQUIRED_TAG === strtolower( trim( $tag ) );
	}

	public static function guidance(): string {
		return __( 'Place Site Master article components inside one Elementor Container whose HTML Tag is article.', 'site-master' );
	}
}
