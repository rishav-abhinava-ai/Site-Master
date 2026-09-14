<?php
/**
 * Shared post data/view-model boundary.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Content;

defined( 'ABSPATH' ) || exit;

final class PostData {
	/**
	 * Resolve the compatible primary category without changing stored metadata.
	 *
	 * @return array{id:int,name:string,url:string}|null
	 */
	public static function primary_category( int $post_id ): ?array {
		$terms = get_the_terms( $post_id, 'category' );
		if ( is_wp_error( $terms ) || ! is_array( $terms ) || array() === $terms ) {
			return null;
		}

		$by_id = array();
		foreach ( $terms as $term ) {
			if ( $term instanceof \WP_Term ) {
				$by_id[ (int) $term->term_id ] = $term;
			}
		}

		foreach ( array( 'rank_math_primary_category', '_yoast_wpseo_primary_category' ) as $key ) {
			$primary_id = absint( get_post_meta( $post_id, $key, true ) );
			if ( $primary_id > 0 && isset( $by_id[ $primary_id ] ) ) {
				return self::term_data( $by_id[ $primary_id ] );
			}
		}

		$term = reset( $by_id );
		return $term instanceof \WP_Term ? self::term_data( $term ) : null;
	}

	public static function excerpt( int $post_id, int $length = 24 ): string {
		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		$length = max( 1, min( 100, $length ) );
		$text   = '' !== trim( (string) $post->post_excerpt ) ? (string) $post->post_excerpt : (string) $post->post_content;
		$text   = strip_shortcodes( $text );
		$text   = wp_strip_all_tags( $text, true );
		return trim( wp_trim_words( $text, $length, '&hellip;' ) );
	}

	/** @return array{id:int,name:string,url:string}|null */
	private static function term_data( \WP_Term $term ): ?array {
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) {
			return null;
		}

		return array(
			'id'   => (int) $term->term_id,
			'name' => (string) $term->name,
			'url'  => (string) $url,
		);
	}
}
