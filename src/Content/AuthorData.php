<?php
/**
 * Shared author data/view-model boundary.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Content;

defined( 'ABSPATH' ) || exit;

final class AuthorData {
	public const META_KEY = 'profile_picture';

	public function register(): void {
		add_action( 'show_user_profile', array( $this, 'render_profile_field' ) );
		add_action( 'edit_user_profile', array( $this, 'render_profile_field' ) );
		add_action( 'personal_options_update', array( $this, 'save_profile_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_field' ) );
		add_filter( 'get_avatar', array( $this, 'filter_avatar' ), 10, 6 );
	}

	/** @return array<string,mixed>|null */
	public static function get( int $user_id, $size = 'thumbnail' ): ?array {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return null;
		}

		$attachment_id = self::attachment_id( $user_id );
		return array(
			'id'            => $user_id,
			'display_name'  => (string) $user->display_name,
			'url'           => get_author_posts_url( $user_id ),
			'bio'           => (string) get_user_meta( $user_id, 'description', true ),
			'attachment_id' => $attachment_id,
			'image'         => $attachment_id ? wp_get_attachment_image_src( $attachment_id, $size ) : false,
		);
	}

	public static function attachment_id( int $user_id ): int {
		$keys = function_exists( 'apply_filters' ) && function_exists( 'has_filter' ) && has_filter( 'site_master/author_image_meta_keys' )
			? apply_filters( 'site_master/author_image_meta_keys', array( self::META_KEY ), $user_id )
			: array( self::META_KEY );
		foreach ( self::normalize_meta_keys( $keys ) as $key ) {
			$value = get_user_meta( $user_id, $key, true );
			$id    = is_numeric( $value ) ? absint( $value ) : 0;
			if ( self::valid_image_attachment( $id ) ) {
				return $id;
			}
		}
		return 0;
	}

	/** @return string[] */
	private static function normalize_meta_keys( $keys ): array {
		$normalized = array();
		foreach ( is_array( $keys ) ? $keys : array( $keys ) as $key ) {
			$key = sanitize_key( (string) $key );
			if ( '' !== $key && ! in_array( $key, $normalized, true ) ) {
				$normalized[] = $key;
			}
		}
		return array() !== $normalized ? $normalized : array( self::META_KEY );
	}

	private static function write_meta_key( int $user_id ): string {
		$key = function_exists( 'apply_filters' ) && function_exists( 'has_filter' ) && has_filter( 'site_master/author_image_write_meta_key' )
			? apply_filters( 'site_master/author_image_write_meta_key', self::META_KEY, $user_id )
			: self::META_KEY;
		$key = sanitize_key( is_string( $key ) ? $key : '' );
		return '' !== $key ? $key : self::META_KEY;
	}

	public static function valid_image_attachment( int $attachment_id ): bool {
		if ( $attachment_id <= 0 ) {
			return false;
		}

		$post = get_post( $attachment_id );
		$mime = get_post_mime_type( $attachment_id );
		return $post && 'attachment' === $post->post_type && is_string( $mime ) && str_starts_with( $mime, 'image/' );
	}

	public function render_profile_field( $user ): void {
		if ( ! isset( $user->ID ) || ! current_user_can( 'edit_user', (int) $user->ID ) ) {
			return;
		}
		?>
		<h2><?php echo esc_html__( 'Site Master author image', 'site-master' ); ?></h2>
		<table class="form-table" role="presentation"><tr>
			<th><label for="site_master_profile_picture_id"><?php echo esc_html__( 'Media attachment ID', 'site-master' ); ?></label></th>
			<td>
				<?php wp_nonce_field( 'site_master_save_profile_picture', 'site_master_profile_picture_nonce' ); ?>
				<input type="number" min="0" name="site_master_profile_picture_id" id="site_master_profile_picture_id" value="<?php echo esc_attr( (string) self::attachment_id( (int) $user->ID ) ); ?>">
				<p class="description"><?php echo esc_html__( 'Use an existing image attachment ID, or clear the field to remove only the author-image relationship.', 'site-master' ); ?></p>
			</td>
		</tr></table>
		<?php
	}

	public function save_profile_field( int $user_id ): bool {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		if ( ! isset( $_POST['site_master_profile_picture_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['site_master_profile_picture_nonce'] ) ), 'site_master_save_profile_picture' ) ) {
			return false;
		}

		$attachment_id = isset( $_POST['site_master_profile_picture_id'] ) ? absint( wp_unslash( $_POST['site_master_profile_picture_id'] ) ) : 0;
		$meta_key      = self::write_meta_key( $user_id );
		if ( 0 === $attachment_id ) {
			delete_user_meta( $user_id, $meta_key );
			return true;
		}
		if ( ! self::valid_image_attachment( $attachment_id ) ) {
			return false;
		}

		update_user_meta( $user_id, $meta_key, $attachment_id );
		return true;
	}

	public function filter_avatar( string $avatar, $id_or_email, $size, string $default, string $alt, array $args ): string {
		$user_id = $this->resolve_user_id( $id_or_email );
		$id      = $user_id ? self::attachment_id( $user_id ) : 0;
		if ( ! $id ) {
			return $avatar;
		}

		$classes = array_merge( array( 'avatar', 'avatar-' . absint( $size ), 'photo' ), (array) ( $args['class'] ?? array() ) );
		$html = \Abhinava\SiteMaster\Media\ImageRenderer::attachment(
			$id,
			array( absint( $size ), absint( $size ) ),
			array(
				'context' => 'avatar',
				'class'    => implode( ' ', array_map( 'sanitize_html_class', array_unique( $classes ) ) ),
				'alt'      => $alt,
				'loading'  => $args['loading'] ?? 'auto',
			)
		);

		return is_string( $html ) && '' !== $html ? $html : $avatar;
	}

	private function resolve_user_id( $subject ): int {
		if ( is_numeric( $subject ) ) {
			return absint( $subject );
		}
		if ( $subject instanceof \WP_User ) {
			return (int) $subject->ID;
		}
		if ( $subject instanceof \WP_Post ) {
			return (int) $subject->post_author;
		}
		if ( $subject instanceof \WP_Comment && $subject->user_id ) {
			return (int) $subject->user_id;
		}
		if ( is_object( $subject ) && isset( $subject->user_id ) ) {
			return absint( $subject->user_id );
		}
		if ( is_string( $subject ) && is_email( $subject ) ) {
			$user = get_user_by( 'email', $subject );
			return $user ? (int) $user->ID : 0;
		}
		return 0;
	}
}
