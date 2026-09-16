<?php
/** Low-DOM editorial Post Card renderer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\PostCard;

use Abhinava\SiteMaster\Media\ImageRenderer;
use Abhinava\SiteMaster\Support\DateFormatter;

defined( 'ABSPATH' ) || exit;

final class PostCardRenderer {
	/** @param array<string,mixed> $options */
	public function render( PostCardViewModel $view_model, array $options = array() ): string {
		$data    = $view_model->data();
		$options = $this->options( $options );
		if ( '' === $data['permalink'] || '' === $data['title'] ) {
			return '';
		}

		$parts = array();
		if ( $options['show_image'] && $data['image_id'] > 0 ) {
			$image_alt = sanitize_text_field( (string) $data['image_alt'] );
			$image = ImageRenderer::attachment(
				$data['image_id'],
				$options['image_size'],
				array(
					'alt'     => $image_alt,
					'context' => 'card',
					'loading' => $options['image_loading'],
					'fetchpriority' => $options['image_priority'],
					'sizes'   => $options['image_sizes'],
				)
			);
			if ( '' !== $image ) {
				$media_label = '' === trim( $image_alt )
					? ' aria-label="' . esc_attr( (string) $data['title'] ) . '"'
					: '';
				$media = '<a class="sm-post-card__media" href="' . esc_url( $data['permalink'] ) . '"' . $media_label . '>' . $image . '</a>';
				if ( $options['show_caption'] && '' !== trim( $data['image_caption'] ) ) {
					$media = '<figure class="sm-post-card__figure">' . $media . '<figcaption>' . esc_html( $data['image_caption'] ) . '</figcaption></figure>';
				}
				$parts[] = $media;
			}
		}

		if ( $options['show_category'] && is_array( $data['category'] ) && ! empty( $data['category']['url'] ) && ! empty( $data['category']['name'] ) ) {
			$parts[] = '<a class="sm-post-card__category" href="' . esc_url( (string) $data['category']['url'] ) . '">' . esc_html( (string) $data['category']['name'] ) . '</a>';
		}

		$tag     = $options['heading_level'];
		$parts[] = '<' . $tag . ' class="sm-post-card__title"><a href="' . esc_url( $data['permalink'] ) . '">' . esc_html( $data['title'] ) . '</a></' . $tag . '>';

		if ( $options['show_excerpt'] && '' !== trim( $data['excerpt'] ) ) {
			$parts[] = '<p class="sm-post-card__excerpt">' . esc_html( $data['excerpt'] ) . '</p>';
		}

		$meta = array();
		if ( $options['show_author'] && is_array( $data['author'] ) && ! empty( $data['author']['url'] ) && ! empty( $data['author']['display_name'] ) ) {
			$author = '';
			if ( $options['show_avatar'] && ! empty( $data['author']['id'] ) ) {
				$avatar = get_avatar( (int) $data['author']['id'], 32, '', (string) $data['author']['display_name'], array( 'class' => array( 'sm-post-card__avatar' ) ) );
				$author = is_string( $avatar ) ? $avatar : '';
			}
			$author .= esc_html( (string) $data['author']['display_name'] );
			$meta[] = '<a class="sm-post-card__author" href="' . esc_url( (string) $data['author']['url'] ) . '" rel="author">' . $author . '</a>';
		}

		$date = 'modified' === $options['date_type'] ? $data['modified_date'] : $data['published_date'];
		if ( $options['show_date'] && is_array( $date ) ) {
			$rendered_date = DateFormatter::render( $date, array( 'class' => 'sm-post-card__date' ) );
			if ( '' !== $rendered_date ) {
				$meta[] = $rendered_date;
			}
		}
		if ( array() !== $meta ) {
			$parts[] = '<p class="sm-post-card__meta">' . implode( '', $meta ) . '</p>';
		}

		$classes = array_merge( array( 'sm-post-card' ), $options['classes'] );
		return '<article class="' . esc_attr( implode( ' ', $classes ) ) . '">' . implode( '', $parts ) . '</article>';
	}

	public static function heading_for_context( string $context ): string {
		return in_array( $context, array( 'archive', 'category', 'tag', 'search' ), true ) ? 'h2' : 'h3';
	}

	/** @param array<string,mixed> $options @return array<string,mixed> */
	private function options( array $options ): array {
		$context = sanitize_key( (string) ( $options['context'] ?? 'homepage' ) );
		$heading = strtolower( (string) ( $options['heading_level'] ?? self::heading_for_context( $context ) ) );
		if ( ! in_array( $heading, array( 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
			$heading = self::heading_for_context( $context );
		}

		$classes = array();
		foreach ( (array) ( $options['classes'] ?? array() ) as $class ) {
			$class = sanitize_html_class( (string) $class );
			if ( '' !== $class && 'sm-post-card' !== $class ) {
				$classes[] = $class;
			}
		}

		return array(
			'show_image'    => ! array_key_exists( 'show_image', $options ) || (bool) $options['show_image'],
			'show_caption'  => ! empty( $options['show_caption'] ),
			'show_category' => ! array_key_exists( 'show_category', $options ) || (bool) $options['show_category'],
			'show_excerpt'  => ! empty( $options['show_excerpt'] ),
			'show_author'   => ! array_key_exists( 'show_author', $options ) || (bool) $options['show_author'],
			'show_date'     => ! array_key_exists( 'show_date', $options ) || (bool) $options['show_date'],
			'show_avatar'   => ! empty( $options['show_avatar'] ),
			'date_type'     => 'modified' === ( $options['date_type'] ?? '' ) ? 'modified' : 'published',
			'heading_level' => $heading,
			'image_size'    => ImageRenderer::size( $options['image_size'] ?? 'medium_large' ),
			'image_loading' => $options['image_loading'] ?? 'lazy',
			'image_priority' => $options['image_priority'] ?? 'auto',
			'image_sizes'   => is_string( $options['image_sizes'] ?? null ) ? sanitize_text_field( $options['image_sizes'] ) : '(max-width: 768px) 100vw, 33vw',
			'classes'       => array_values( array_unique( $classes ) ),
		);
	}
}
