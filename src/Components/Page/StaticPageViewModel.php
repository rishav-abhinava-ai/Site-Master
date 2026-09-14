<?php
/** Normalized WordPress static-page data. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Page;

defined( 'ABSPATH' ) || exit;

final class StaticPageViewModel {
	/** @var array<string,mixed> */
	private array $data;

	/** @param array<string,mixed> $data */
	public function __construct( array $data ) {
		$this->data = array(
			'id'            => absint( $data['id'] ?? 0 ),
			'permalink'     => is_string( $data['permalink'] ?? null ) ? $data['permalink'] : '',
			'title'         => is_string( $data['title'] ?? null ) ? $data['title'] : '',
			'content'       => is_string( $data['content'] ?? null ) ? $data['content'] : '',
			'image_id'      => absint( $data['image_id'] ?? 0 ),
			'image_alt'     => is_string( $data['image_alt'] ?? null ) ? $data['image_alt'] : '',
			'image_caption' => is_string( $data['image_caption'] ?? null ) ? $data['image_caption'] : '',
			'parent_id'     => absint( $data['parent_id'] ?? 0 ),
		);
	}

	public static function from_page( int $page_id ): ?self {
		$page = get_post( $page_id );
		if ( ! $page instanceof \WP_Post || 'page' !== $page->post_type ) {
			return null;
		}

		$image_id = get_post_thumbnail_id( $page_id );
		$alt      = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
		return new self(
			array(
				'id'            => $page_id,
				'permalink'     => get_permalink( $page_id ),
				'title'         => get_the_title( $page_id ),
				'content'       => (string) $page->post_content,
				'image_id'      => $image_id,
				'image_alt'     => is_string( $alt ) ? $alt : '',
				'image_caption' => $image_id ? (string) wp_get_attachment_caption( $image_id ) : '',
				'parent_id'     => (int) $page->post_parent,
			)
		);
	}

	/** @return array<string,mixed> */
	public function data(): array { return $this->data; }
}
