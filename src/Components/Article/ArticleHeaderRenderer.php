<?php
/** Editorial article header and compact metadata renderer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Article;

use Abhinava\SiteMaster\Support\DateFormatter;

defined( 'ABSPATH' ) || exit;

final class ArticleHeaderRenderer {
	/** @param array<string,mixed> $data @param array<string,mixed> $options */
	public function render( array $data, array $options ): string {
		if ( '' === trim( (string) $data['headline'] ) ) {
			return '';
		}

		$parts = array();
		if ( $options['show_category'] && is_array( $data['category'] ) && ! empty( $data['category']['url'] ) && ! empty( $data['category']['name'] ) ) {
			$parts[] = '<a class="sm-article__category" href="' . esc_url( (string) $data['category']['url'] ) . '">' . esc_html( (string) $data['category']['name'] ) . '</a>';
		}
		$parts[] = '<h1 class="sm-article__title">' . esc_html( (string) $data['headline'] ) . '</h1>';
		if ( $options['show_summary'] && '' !== trim( (string) $data['summary'] ) ) {
			$parts[] = '<p class="sm-article__summary">' . esc_html( (string) $data['summary'] ) . '</p>';
		}

		$meta = array();
		if ( $options['show_author'] && is_array( $data['author'] ) && ! empty( $data['author']['url'] ) && ! empty( $data['author']['display_name'] ) ) {
			$author = '';
			if ( $options['show_avatar'] && ! empty( $data['author']['id'] ) ) {
				$avatar = get_avatar( (int) $data['author']['id'], 48, '', (string) $data['author']['display_name'], array( 'class' => array( 'sm-article__avatar' ) ) );
				$author = is_string( $avatar ) ? $avatar : '';
			}
			$meta[] = '<a class="sm-article__author" href="' . esc_url( (string) $data['author']['url'] ) . '" rel="author">' . $author . esc_html( (string) $data['author']['display_name'] ) . '</a>';
		}
		if ( $options['show_published'] && is_array( $data['published_date'] ) ) {
			$time = DateFormatter::render( $data['published_date'], array( 'class' => 'sm-article__published' ) );
			if ( '' !== $time ) {
				$meta[] = '<span class="sm-article__date"><span class="sm-article__date-label">' . esc_html__( 'Published', 'site-master' ) . '</span> ' . $time . '</span>';
			}
		}
		if ( $options['show_modified'] && is_array( $data['modified_date'] ) ) {
			$time = DateFormatter::render( $data['modified_date'], array( 'class' => 'sm-article__modified' ) );
			if ( '' !== $time ) {
				$meta[] = '<span class="sm-article__date"><span class="sm-article__date-label">' . esc_html__( 'Updated', 'site-master' ) . '</span> ' . $time . '</span>';
			}
		}
		if ( array() !== $meta ) {
			$parts[] = '<div class="sm-article__meta">' . implode( ' ', $meta ) . '</div>';
		}

		return '<header class="sm-article__header">' . implode( '', $parts ) . '</header>';
	}
}
