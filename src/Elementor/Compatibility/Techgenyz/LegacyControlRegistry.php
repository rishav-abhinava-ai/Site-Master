<?php
/** Source-derived Techgenyz publishing control registry. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz;
defined( 'ABSPATH' ) || exit;
final class LegacyControlRegistry {
	private static ?array $registry = null;
	public static function all(): array { if ( null === self::$registry ) { $json = file_get_contents( __DIR__ . '/legacy-control-map.json' ); $data = is_string( $json ) ? json_decode( $json, true ) : null; self::$registry = is_array( $data ) ? $data : array( 'widgets' => array() ); } return self::$registry; }
	public static function widget( string $id ): array { $all = self::all(); return is_array( $all['widgets'][ $id ] ?? null ) ? $all['widgets'][ $id ] : array( 'controls' => array(), 'groups' => array() ); }
	public static function counts(): array { $out=array('direct'=>0,'responsive'=>0,'group'=>0,'style'=>0,'functional'=>0,'inactive'=>0,'persistence_only'=>0); foreach((array)(self::all()['widgets']??array())as$w){foreach((array)$w['controls']as$c){++$out['direct'];$out['responsive']+=!empty($c['responsive']);$out['style']+=!empty($c['selectors']);$out['functional']+=empty($c['selectors'])&&'PERSISTENCE_ONLY'!==$c['status'];$out['inactive']+='INTENTIONALLY_INACTIVE'===$c['status'];$out['persistence_only']+='PERSISTENCE_ONLY'===$c['status'];}$out['group']+=count((array)$w['groups']);}return$out; }
}
