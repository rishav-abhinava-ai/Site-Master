<?php
/** Blissz compatibility activation and registration. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
defined( 'ABSPATH' ) || exit;
final class BlisszCompatibility {
	public const LEGACY_AVATAR_KEY = 'tbm_profile_picture';
	public function register(): void {
		add_filter( 'site_master/author_image_meta_keys', array( $this, 'avatar_read_keys' ), 10, 2 );
		add_filter( 'site_master/author_image_write_meta_key', array( $this, 'avatar_write_key' ), 10, 2 );
	}
	/** Legacy Blissz key is authoritative; current shared key is a non-destructive fallback. */
	public function avatar_read_keys( array $keys, int $user_id ): array {
		return array( self::LEGACY_AVATAR_KEY, 'profile_picture' );
	}
	public function avatar_write_key( string $key, int $user_id ): string {
		return self::LEGACY_AVATAR_KEY;
	}
	public static function register_category( $manager ): void {
		if ( is_object( $manager ) && method_exists( $manager, 'add_category' ) ) {
			$manager->add_category( 'blissz', array( 'title' => esc_html__( 'Blissz', 'site-master' ), 'icon' => 'fa fa-plug' ) );
		}
	}
	public static function register_widgets( $manager ): void {
		if ( ! class_exists( '\\Elementor\\Widget_Base' ) || ! is_object( $manager ) || ! method_exists( $manager, 'register' ) ) { return; }
		$classes = array( BreadcrumbWidget::class, DateWidget::class, AuthorWidget::class, PostContentWidget::class, PostSearchWidget::class, LoopPostWidget::class, LoopCategoryWidget::class, SinglePostWidget::class );
		foreach ( $classes as $class ) { $manager->register( new $class() ); }
	}
}
