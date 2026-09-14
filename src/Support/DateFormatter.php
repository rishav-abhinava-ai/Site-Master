<?php
/**
 * Shared date-formatting boundary.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Support;

defined( 'ABSPATH' ) || exit;

final class DateFormatter {
	/** @return array{timestamp:int,datetime:string,text:string}|null */
	public static function published( int $post_id, string $format = '' ): ?array {
		return self::for_post( $post_id, 'date', $format );
	}

	/** @return array{timestamp:int,datetime:string,text:string}|null */
	public static function modified( int $post_id, string $format = '' ): ?array {
		return self::for_post( $post_id, 'modified', $format );
	}

	/** @return array{timestamp:int,datetime:string,text:string}|null */
	private static function for_post( int $post_id, string $field, string $format ): ?array {
		$date = get_post_datetime( $post_id, $field, 'local' );
		if ( ! $date instanceof \DateTimeInterface ) {
			return null;
		}

		if ( '' === $format ) {
			$format = trim( (string) get_option( 'date_format', 'F j, Y' ) . ' ' . (string) get_option( 'time_format', 'g:i a' ) );
		}

		return array(
			'timestamp' => $date->getTimestamp(),
			'datetime'  => $date->format( DATE_W3C ),
			'text'      => wp_date( $format, $date->getTimestamp(), wp_timezone() ),
		);
	}

	/** @param array{datetime:string,text:string}|null $value */
	public static function render( ?array $value, array $attributes = array() ): string {
		if ( null === $value ) {
			return '';
		}

		$attributes = array_merge( array( 'datetime' => $value['datetime'] ), $attributes );
		$parts      = array();
		foreach ( $attributes as $name => $attribute ) {
			$name = sanitize_key( (string) $name );
			if ( '' !== $name && is_scalar( $attribute ) ) {
				$parts[] = $name . '="' . esc_attr( (string) $attribute ) . '"';
			}
		}

		return '<time ' . implode( ' ', $parts ) . '>' . esc_html( $value['text'] ) . '</time>';
	}

	public static function meaningfully_modified( int $post_id ): bool {
		$published = self::published( $post_id );
		$modified  = self::modified( $post_id );
		return null !== $published && null !== $modified && $modified['timestamp'] > $published['timestamp'];
	}
}
