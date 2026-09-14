<?php
/** Editorial Post Card rendering data. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\PostCard;

use Abhinava\SiteMaster\Content\AuthorData;
use Abhinava\SiteMaster\Content\PostData;
use Abhinava\SiteMaster\Support\DateFormatter;

defined( 'ABSPATH' ) || exit;

final class PostCardViewModel {
	/** @var array<string,mixed> */
	private array $data;

	/** @param array<string,mixed> $data Normalized data, useful for controlled render tests and adapters. */
	public function __construct( array $data ) {
		$this->data = self::normalize( $data );
	}

	public static function from_post( int $post_id, int $excerpt_length = 24 ): ?self {
		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post ) {
			return null;
		}

		$image_id = get_post_thumbnail_id( $post_id );
		$caption  = $image_id ? wp_get_attachment_caption( $image_id ) : '';
		$alt      = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';

		return new self(
			array(
				'id'             => $post_id,
				'permalink'      => get_permalink( $post_id ),
				'title'          => get_the_title( $post_id ),
				'image_id'       => $image_id,
				'image_alt'      => is_string( $alt ) && '' !== trim( $alt ) ? $alt : get_the_title( $post_id ),
				'image_caption'  => is_string( $caption ) ? $caption : '',
				'category'       => PostData::primary_category( $post_id ),
				'excerpt'        => PostData::excerpt( $post_id, $excerpt_length ),
				'author'         => AuthorData::get( (int) $post->post_author ),
				'published_date' => DateFormatter::published( $post_id ),
				'modified_date'  => DateFormatter::modified( $post_id ),
			)
		);
	}

	/** @return array<string,mixed> */
	public function data(): array {
		return $this->data;
	}

	/** @param array<string,mixed> $data @return array<string,mixed> */
	private static function normalize( array $data ): array {
		return array(
			'id'             => absint( $data['id'] ?? 0 ),
			'permalink'      => is_string( $data['permalink'] ?? null ) ? $data['permalink'] : '',
			'title'          => is_string( $data['title'] ?? null ) ? $data['title'] : '',
			'image_id'       => absint( $data['image_id'] ?? 0 ),
			'image_alt'      => is_string( $data['image_alt'] ?? null ) ? $data['image_alt'] : '',
			'image_caption'  => is_string( $data['image_caption'] ?? null ) ? $data['image_caption'] : '',
			'category'       => is_array( $data['category'] ?? null ) ? $data['category'] : null,
			'excerpt'        => is_string( $data['excerpt'] ?? null ) ? $data['excerpt'] : '',
			'author'         => is_array( $data['author'] ?? null ) ? $data['author'] : null,
			'published_date' => is_array( $data['published_date'] ?? null ) ? $data['published_date'] : null,
			'modified_date'  => is_array( $data['modified_date'] ?? null ) ? $data['modified_date'] : null,
		);
	}
}
