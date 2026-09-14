<?php
/**
 * Shared SVG sanitizer boundary.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Security;

defined( 'ABSPATH' ) || exit;

final class SvgSanitizer {
	private const MAX_UPLOAD_BYTES     = 2097152;
	private const MAX_EXPANDED_BYTES   = 5242880;
	private const SANITIZER_CLASS      = '\\enshrined\\svgSanitize\\Sanitizer';

	public function register(): void {
		add_filter( 'upload_mimes', array( $this, 'allowed_mimes' ) );
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_upload' ) );
		add_filter( 'wp_handle_sideload_prefilter', array( $this, 'sanitize_upload' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'validate_filetype' ), 10, 4 );
	}

	public static function available(): bool {
		return class_exists( self::SANITIZER_CLASS );
	}

	public static function permitted(): bool {
		return current_user_can( 'upload_files' ) && ( current_user_can( 'manage_options' ) || current_user_can( 'manage_categories' ) );
	}

	public function allowed_mimes( array $mimes ): array {
		if ( self::available() && self::permitted() ) {
			$mimes['svg']  = 'image/svg+xml';
			$mimes['svgz'] = 'image/svg+xml';
		}

		return $mimes;
	}

	/** @param array<string,mixed> $file @return array<string,mixed> */
	public function sanitize_upload( array $file ): array {
		$extension = $this->extension( $file['name'] ?? '' );
		if ( '' === $extension ) {
			return $file;
		}

		if ( ! self::permitted() || ! self::available() ) {
			$file['error'] = esc_html__( 'Secure SVG upload is unavailable.', 'site-master' );
			return $file;
		}

		$path = is_string( $file['tmp_name'] ?? null ) ? $file['tmp_name'] : '';
		$size = isset( $file['size'] ) ? (int) $file['size'] : 0;
		if ( '' === $path || ! is_file( $path ) || ! is_readable( $path ) ) {
			$file['error'] = esc_html__( 'The SVG upload could not be inspected.', 'site-master' );
			return $file;
		}

		if ( $size <= 0 ) {
			$detected = filesize( $path );
			$size     = false === $detected ? 0 : (int) $detected;
		}
		if ( $size <= 0 || $size > self::MAX_UPLOAD_BYTES ) {
			$file['error'] = esc_html__( 'The SVG file is empty or too large.', 'site-master' );
			return $file;
		}

		$data = file_get_contents( $path );
		if ( false === $data ) {
			$file['error'] = esc_html__( 'The SVG file could not be read.', 'site-master' );
			return $file;
		}

		if ( 'svgz' === $extension ) {
			$data = function_exists( 'gzdecode' ) ? gzdecode( $data, self::MAX_EXPANDED_BYTES + 1 ) : false;
		}
		if ( false === $data || strlen( $data ) > self::MAX_EXPANDED_BYTES ) {
			$file['error'] = esc_html__( 'The SVG content is invalid or too large.', 'site-master' );
			return $file;
		}

		$class     = self::SANITIZER_CLASS;
		$sanitizer = new $class();
		$sanitizer->removeRemoteReferences( true );
		$clean = $sanitizer->sanitize( $data );
		if ( false === $clean || '' === trim( (string) $clean ) ) {
			$file['error'] = esc_html__( 'The SVG contains invalid or unsafe content.', 'site-master' );
			return $file;
		}

		if ( 'svgz' === $extension ) {
			$clean = function_exists( 'gzencode' ) ? gzencode( (string) $clean, 9 ) : false;
		}
		if ( false === $clean || false === file_put_contents( $path, $clean, LOCK_EX ) ) {
			$file['error'] = esc_html__( 'The sanitized SVG could not be saved.', 'site-master' );
			return $file;
		}

		$file['size'] = strlen( $clean );
		return $file;
	}

	/** @param array<string,mixed> $data @param array<string,string> $mimes @return array<string,mixed> */
	public function validate_filetype( array $data, string $file, string $filename, array $mimes ): array {
		$extension = $this->extension( $filename );
		if ( '' === $extension ) {
			return $data;
		}

		if ( ! self::available() || ! self::permitted() ) {
			return array( 'ext' => false, 'type' => false, 'proper_filename' => false );
		}

		return array( 'ext' => $extension, 'type' => 'image/svg+xml', 'proper_filename' => false );
	}

	private function extension( $filename ): string {
		if ( ! is_string( $filename ) ) {
			return '';
		}

		$extension = strtolower( (string) pathinfo( $filename, PATHINFO_EXTENSION ) );
		return in_array( $extension, array( 'svg', 'svgz' ), true ) ? $extension : '';
	}
}
