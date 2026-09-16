<?php
/** Shared, normalized WordPress attachment-image policy. @package SiteMaster */
namespace Abhinava\SiteMaster\Media;
defined( 'ABSPATH' ) || exit;
final class ImageRenderer {
	/** Context supplies defaults; callers supply viewport knowledge explicitly. */
	public static function attachment( int $attachment_id, $size = 'medium_large', array $options = array() ): string {
		if ( $attachment_id <= 0 ) { return ''; }
		$context = self::choice( $options['context'] ?? 'generic', array( 'card', 'hero', 'content', 'avatar', 'static-page-featured', 'rss', 'generic' ), 'generic' );
		$default_loading = 'card' === $context ? 'lazy' : ( 'hero' === $context ? 'eager' : 'auto' );
		$loading = self::choice( $options['loading'] ?? $default_loading, array( 'auto', 'eager', 'lazy' ), $default_loading );
		$priority = self::choice( $options['fetchpriority'] ?? 'auto', array( 'auto', 'high', 'low' ), 'auto' );
		if ( in_array( $context, array( 'avatar', 'rss' ), true ) ) { $priority = 'auto'; }
		elseif ( 'high' === $priority ) { $loading = 'eager'; }
		$attributes = array();
		if ( 'auto' !== $loading ) { $attributes['loading'] = $loading; }
		if ( 'auto' !== $priority ) { $attributes['fetchpriority'] = $priority; }
		// Suppress core high-priority inference for small avatars and feed media.
		if ( in_array( $context, array( 'avatar', 'rss' ), true ) ) { $attributes['fetchpriority'] = false; }
		if ( 'rss' === $context ) { $attributes['loading'] = false; }
		foreach ( array( 'alt', 'sizes' ) as $name ) {
			if ( isset( $options[ $name ] ) && is_string( $options[ $name ] ) ) { $attributes[ $name ] = sanitize_text_field( $options[ $name ] ); }
		}
		if ( isset( $options['class'] ) && is_string( $options['class'] ) ) {
			$classes = preg_split( '/\s+/', trim( $options['class'] ) ) ?: array();
			$attributes['class'] = implode( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
		}
		$html = wp_get_attachment_image( $attachment_id, self::size( $size ), false, $attributes );
		return is_string( $html ) ? $html : '';
	}
	/** Registered size names or bounded positive integer dimension pairs. */
	public static function size( $size ) {
		if ( is_array( $size ) && array_keys( $size ) === array( 0, 1 ) && is_int( $size[0] ) && is_int( $size[1] ) && $size[0] > 0 && $size[1] > 0 && $size[0] <= 8192 && $size[1] <= 8192 ) { return $size; }
		$names = function_exists( 'get_intermediate_image_sizes' ) ? get_intermediate_image_sizes() : array( 'thumbnail', 'medium', 'medium_large', 'large' );
		$names[] = 'full';
		return is_string( $size ) && in_array( $size, $names, true ) ? $size : 'medium_large';
	}
	public static function hero( int $attachment_id, $size = 'full', array $options = array() ): string {
		return self::attachment( $attachment_id, $size, array_merge( array( 'context' => 'hero' ), $options ) );
	}
	private static function choice( $value, array $allowed, string $fallback ): string {
		return is_string( $value ) && in_array( $value, $allowed, true ) ? $value : $fallback;
	}
}
