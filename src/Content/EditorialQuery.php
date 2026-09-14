<?php
/** Safe shared editorial listing query boundary. @package SiteMaster */

namespace Abhinava\SiteMaster\Content;

defined( 'ABSPATH' ) || exit;

final class EditorialQuery {
	/** @var string[] */
	private array $allowed_post_types;

	/** @param string[] $allowed_post_types Explicit editorial post-type allow-list. */
	public function __construct( array $allowed_post_types = array( 'post' ) ) {
		$this->allowed_post_types = array_values( array_filter( array_map( 'sanitize_key', $allowed_post_types ) ) );
		if ( array() === $this->allowed_post_types ) {
			$this->allowed_post_types = array( 'post' );
		}
	}

	/** @param array<string,mixed> $options */
	public function query( array $options = array() ): \WP_Query {
		return new \WP_Query( $this->arguments( $options ) );
	}

	/** @param array<string,mixed> $options @return array<string,mixed> */
	public function arguments( array $options = array() ): array {
		$paginated = ! empty( $options['paginated'] );
		$post_type = sanitize_key( (string) ( $options['post_type'] ?? 'post' ) );
		if ( ! in_array( $post_type, $this->allowed_post_types, true ) ) {
			$post_type = $this->allowed_post_types[0];
		}

		$order   = strtoupper( (string) ( $options['order'] ?? 'DESC' ) );
		$order   = in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'DESC';
		$orderby = sanitize_key( (string) ( $options['orderby'] ?? 'date' ) );
		$allowed_orderby = array( 'date', 'modified', 'title', 'menu_order', 'id', 'rand', 'comment_count' );
		$orderby = in_array( $orderby, $allowed_orderby, true ) ? $orderby : 'date';

		$arguments = array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, min( 100, absint( $options['posts_per_page'] ?? 10 ) ) ),
			'paged'               => $paginated ? max( 1, absint( $options['paged'] ?? 1 ) ) : 1,
			'orderby'             => $orderby,
			'order'               => $order,
			'ignore_sticky_posts' => ! array_key_exists( 'ignore_sticky_posts', $options ) || (bool) $options['ignore_sticky_posts'],
			'no_found_rows'       => ! $paginated,
		);

		$include = self::ids( $options['include'] ?? array() );
		$exclude = self::ids( $options['exclude'] ?? array() );
		if ( array() !== $include ) {
			$arguments['post__in'] = $include;
		}
		if ( array() !== $exclude ) {
			$arguments['post__not_in'] = $exclude;
		}

		$author = absint( $options['author'] ?? 0 );
		if ( $author > 0 ) {
			$arguments['author'] = $author;
		}

		$category_ids = self::ids( $options['category_ids'] ?? array() );
		if ( array() !== $category_ids ) {
			$arguments['category__in'] = $category_ids;
		}

		$taxonomy = sanitize_key( (string) ( $options['taxonomy'] ?? '' ) );
		$term_ids = self::ids( $options['term_ids'] ?? array() );
		if ( in_array( $taxonomy, array( 'category', 'post_tag' ), true ) && array() !== $term_ids ) {
			$arguments['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term_ids,
				),
			);
		}

		return $arguments;
	}

	/** @return int[] */
	private static function ids( $values ): array {
		$ids = array_map( 'absint', is_array( $values ) ? $values : array( $values ) );
		return array_values( array_unique( array_filter( $ids ) ) );
	}
}
