<?php
/** Shared feed featured-image compatibility. @package SiteMaster */

namespace Abhinava\SiteMaster\Content;

defined( 'ABSPATH' ) || exit;

final class RssFeaturedImage {
	public function register(): void {
		add_filter( 'the_excerpt_rss', array( $this, 'prepend' ) );
		add_filter( 'the_content_feed', array( $this, 'prepend' ) );
	}

	public function prepend( string $content ): string {
		if ( ! is_feed() ) {
			return $content;
		}

		$post_id = get_the_ID();
		$image_id = $post_id ? get_post_thumbnail_id( $post_id ) : 0;
		if ( ! $image_id ) {
			return $content;
		}

		$image_url = wp_get_attachment_image_url( $image_id, 'full' );
		if ( str_contains( $content, 'wp-image-' . $image_id ) || ( $image_url && str_contains( $content, $image_url ) ) ) {
			return $content;
		}

		$image = \Abhinava\SiteMaster\Media\ImageRenderer::attachment( $image_id, 'full', array( 'context' => 'rss', 'alt' => '' ) );
		return '' !== $image ? '<p class="sm-feed-featured-image">' . $image . '</p>' . $content : $content;
	}
}
