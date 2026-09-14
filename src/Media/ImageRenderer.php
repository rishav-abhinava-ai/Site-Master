<?php
/**
 * Shared image-rendering service placeholder.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Media;

defined( 'ABSPATH' ) || exit;

final class ImageRenderer {
	/**
	 * Render an attachment through WordPress so dimensions and responsive
	 * source attributes remain intact.
	 *
	 * Advanced hero/LCP policy remains reserved for Development 0.0.7.
	 *
	 * @param int          $attachment_id Attachment ID.
	 * @param string|array $size          Registered image size or dimensions.
	 * @param array        $attributes    Approved image attributes.
	 */
	public static function attachment( int $attachment_id, $size = 'medium_large', array $attributes = array() ): string {
		if ( $attachment_id <= 0 ) {
			return '';
		}

		$attributes = array_merge(
			array(
				'loading'  => 'lazy',
				'decoding' => 'async',
			),
			$attributes
		);

		$html = wp_get_attachment_image( $attachment_id, $size, false, $attributes );
		return is_string( $html ) ? $html : '';
	}

	/** Render a likely primary article image without blanket lazy/high-priority policy. */
	public static function hero( int $attachment_id, $size = 'full', array $attributes = array() ): string {
		return self::attachment(
			$attachment_id,
			$size,
			array_merge(
				array(
					'loading'  => 'eager',
					'decoding' => 'async',
				),
				$attributes
			)
		);
	}
}
