<?php
/** Structured, source-derived Elementor compatibility-control registry. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;

defined( 'ABSPATH' ) || exit;

final class LegacyControlRegistry {
	/** @var array<string,mixed>|null */
	private static ?array $registry = null;

	/** @return array<string,mixed> */
	public static function widget( string $widget_id ): array {
		$all = self::all();
		return is_array( $all['widgets'][ $widget_id ] ?? null ) ? $all['widgets'][ $widget_id ] : array( 'controls' => array(), 'groups' => array() );
	}

	/** @return array<string,mixed> */
	public static function all(): array {
		if ( null !== self::$registry ) {
			return self::$registry;
		}
		$path = __DIR__ . '/legacy-control-map.json';
		$json = is_readable( $path ) ? file_get_contents( $path ) : false;
		$data = is_string( $json ) ? json_decode( $json, true ) : null;
		self::$registry = is_array( $data ) ? $data : array( 'widgets' => array() );
		return self::$registry;
	}

	/** @return array<string,int> */
	public static function counts(): array {
		$counts = array( 'direct' => 0, 'responsive' => 0, 'group' => 0, 'style' => 0, 'functional' => 0, 'inactive' => 0, 'persistence_only' => 0 );
		foreach ( (array) ( self::all()['widgets'] ?? array() ) as $widget ) {
			foreach ( (array) ( $widget['controls'] ?? array() ) as $control ) {
				++$counts['direct'];
				$counts['responsive'] += ! empty( $control['responsive'] ) ? 1 : 0;
				$counts['style'] += ! empty( $control['selectors'] ) ? 1 : 0;
				$counts['functional'] += empty( $control['selectors'] ) && 'PERSISTENCE_ONLY' !== ( $control['status'] ?? '' ) ? 1 : 0;
				$counts['inactive'] += 'INTENTIONALLY_INACTIVE' === ( $control['status'] ?? '' ) ? 1 : 0;
				$counts['persistence_only'] += 'PERSISTENCE_ONLY' === ( $control['status'] ?? '' ) ? 1 : 0;
			}
			$counts['group'] += count( (array) ( $widget['groups'] ?? array() ) );
		}
		return $counts;
	}
}
