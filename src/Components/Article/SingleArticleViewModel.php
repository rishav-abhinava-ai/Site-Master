<?php
/** Normalized single editorial article data. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Article;

use Abhinava\SiteMaster\Content\AuthorData;
use Abhinava\SiteMaster\Content\PostData;
use Abhinava\SiteMaster\Support\DateFormatter;

defined( 'ABSPATH' ) || exit;

final class SingleArticleViewModel {
	/** @var array<string,mixed> */
	private array $data;

	/** @param array<string,mixed> $data Normalized transfer data for renderers/adapters. */
	public function __construct( array $data ) {
		$this->data = self::normalize( $data );
	}

	public static function from_post( int $post_id ): ?self {
		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post ) {
			return null;
		}

		$image_id = get_post_thumbnail_id( $post_id );
		$alt      = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
		$caption  = $image_id ? wp_get_attachment_caption( $image_id ) : '';
		$topics   = self::topics( $post_id );

		return new self(
			array(
				'id'             => $post_id,
				'post_type'      => (string) $post->post_type,
				'permalink'      => get_permalink( $post_id ),
				'headline'       => get_the_title( $post_id ),
				'summary'        => (string) $post->post_excerpt,
				'category'       => PostData::primary_category( $post_id ),
				'author'         => AuthorData::get( (int) $post->post_author ),
				'published_date' => DateFormatter::published( $post_id ),
				'modified_date'  => DateFormatter::meaningfully_modified( $post_id ) ? DateFormatter::modified( $post_id ) : null,
				'image_id'       => $image_id,
				'image_alt'      => is_string( $alt ) && '' !== trim( $alt ) ? $alt : get_the_title( $post_id ),
				'image_caption'  => is_string( $caption ) ? $caption : '',
				'content'        => (string) $post->post_content,
				'topics'         => $topics,
			)
		);
	}

	/** @return array<string,mixed> */
	public function data(): array {
		return $this->data;
	}

	/** @return array<int,array{id:int,name:string,url:string}> */
	private static function topics( int $post_id ): array {
		$terms = get_the_terms( $post_id, 'post_tag' );
		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return array();
		}

		$topics = array();
		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term ) {
				continue;
			}
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) ) {
				$topics[] = array( 'id' => (int) $term->term_id, 'name' => (string) $term->name, 'url' => (string) $url );
			}
		}
		return $topics;
	}

	/** @param array<string,mixed> $data @return array<string,mixed> */
	private static function normalize( array $data ): array {
		return array(
			'id'             => absint( $data['id'] ?? 0 ),
			'post_type'      => sanitize_key( (string) ( $data['post_type'] ?? 'post' ) ),
			'permalink'      => is_string( $data['permalink'] ?? null ) ? $data['permalink'] : '',
			'headline'       => is_string( $data['headline'] ?? null ) ? $data['headline'] : '',
			'summary'        => is_string( $data['summary'] ?? null ) ? $data['summary'] : '',
			'category'       => is_array( $data['category'] ?? null ) ? $data['category'] : null,
			'author'         => is_array( $data['author'] ?? null ) ? $data['author'] : null,
			'published_date' => is_array( $data['published_date'] ?? null ) ? $data['published_date'] : null,
			'modified_date'  => is_array( $data['modified_date'] ?? null ) ? $data['modified_date'] : null,
			'image_id'       => absint( $data['image_id'] ?? 0 ),
			'image_alt'      => is_string( $data['image_alt'] ?? null ) ? $data['image_alt'] : '',
			'image_caption'  => is_string( $data['image_caption'] ?? null ) ? $data['image_caption'] : '',
			'content'        => is_string( $data['content'] ?? null ) ? $data['content'] : '',
			'topics'         => is_array( $data['topics'] ?? null ) ? $data['topics'] : array(),
		);
	}
}
