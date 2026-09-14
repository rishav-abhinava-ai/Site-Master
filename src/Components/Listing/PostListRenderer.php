<?php
/** Shared editorial Post Card collection renderer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Listing;

use Abhinava\SiteMaster\Components\PostCard\PostCardRenderer;
use Abhinava\SiteMaster\Components\PostCard\PostCardViewModel;

defined( 'ABSPATH' ) || exit;

final class PostListRenderer {
	private PostCardRenderer $cards;
	private PaginationRenderer $pagination;
	private static int $heading_sequence = 0;

	public function __construct( ?PostCardRenderer $cards = null, ?PaginationRenderer $pagination = null ) {
		$this->cards      = $cards ?? new PostCardRenderer();
		$this->pagination = $pagination ?? new PaginationRenderer();
	}

	/** @param PostCardViewModel[] $items @param array<string,mixed> $options */
	public function render( array $items, array $options = array() ): string {
		$card_options = is_array( $options['card'] ?? null ) ? $options['card'] : array();
		$context      = sanitize_key( (string) ( $options['context'] ?? 'homepage' ) );
		$card_options['context'] = $context;
		$cards = array();
		foreach ( $items as $item ) {
			if ( $item instanceof PostCardViewModel ) {
				$html = $this->cards->render( $item, $card_options );
				if ( '' !== $html ) {
					$cards[] = $html;
				}
			}
		}

		if ( array() === $cards ) {
			return '';
		}

		$content = implode( '', $cards );
		$heading = is_string( $options['heading'] ?? null ) ? trim( $options['heading'] ) : '';
		if ( '' === $heading ) {
			return '<div class="sm-post-list">' . $content . '</div>';
		}

		$level = strtolower( (string) ( $options['heading_level'] ?? 'h2' ) );
		if ( ! in_array( $level, array( 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
			$level = 'h2';
		}
		$id = sanitize_html_class( (string) ( $options['heading_id'] ?? '' ) );
		if ( '' === $id ) {
			++self::$heading_sequence;
			$id = 'sm-post-list-heading-' . self::$heading_sequence;
		}

		return '<section class="sm-post-list" aria-labelledby="' . esc_attr( $id ) . '"><' . $level . ' id="' . esc_attr( $id ) . '" class="sm-post-list__heading">' . esc_html( $heading ) . '</' . $level . '>' . $content . '</section>';
	}

	/** @param array<string,mixed> $options */
	public function render_query( \WP_Query $query, array $options = array() ): string {
		$items = array();
		foreach ( (array) $query->posts as $post ) {
			$post_id = $post instanceof \WP_Post ? (int) $post->ID : absint( $post );
			$model = $post_id > 0 ? PostCardViewModel::from_post( $post_id, max( 1, min( 100, absint( $options['excerpt_length'] ?? 24 ) ) ) ) : null;
			if ( $model ) {
				$items[] = $model;
			}
		}

		$output = $this->render( $items, $options );
		if ( '' !== $output && ! empty( $options['paginated'] ) ) {
			$output .= $this->pagination->render( max( 1, absint( $query->get( 'paged' ) ) ), max( 1, (int) $query->max_num_pages ), (string) ( $options['pagination_base'] ?? '' ) );
		}

		return $output;
	}
}
