<?php
/** Public author-archive profile data. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Author;

use Abhinava\SiteMaster\Content\AuthorData;

defined( 'ABSPATH' ) || exit;

final class AuthorPageViewModel {
	/** @var array<string,mixed> */
	private array $data;

	/** @param array<string,mixed> $data */
	public function __construct( array $data ) {
		$this->data = array(
			'id'            => absint( $data['id'] ?? 0 ),
			'display_name'  => is_string( $data['display_name'] ?? null ) ? $data['display_name'] : '',
			'url'           => is_string( $data['url'] ?? null ) ? $data['url'] : '',
			'bio'           => is_string( $data['bio'] ?? null ) ? $data['bio'] : '',
			'attachment_id' => absint( $data['attachment_id'] ?? 0 ),
		);
	}

	public static function from_user( int $user_id ): ?self {
		$data = AuthorData::get( $user_id, 'medium' );
		return is_array( $data ) ? new self( $data ) : null;
	}

	/** @return array<string,mixed> */
	public function data(): array { return $this->data; }
}
