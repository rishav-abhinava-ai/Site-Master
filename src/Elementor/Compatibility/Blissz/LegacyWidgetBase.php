<?php
/** Base for source-derived Blissz legacy widget adapters. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
defined( 'ABSPATH' ) || exit;
abstract class LegacyWidgetBase extends \Elementor\Widget_Base {
	public function get_categories(): array { return array( 'blissz' ); }
	public function get_keywords(): array { return array( 'blissz', 'legacy', 'compatibility' ); }
	protected function register_controls(): void {
		$this->start_controls_section( 'site_master_legacy_compatibility', array( 'label' => esc_html__( 'Legacy compatibility', 'site-master' ) ) );
		$registry = LegacyControlRegistry::widget( $this->get_name() );
		foreach ( (array) ( $registry['controls'] ?? array() ) as $control_id => $definition ) {
			$args = array( 'type' => (string) ( $definition['type'] ?? \Elementor\Controls_Manager::HIDDEN ) );
			if ( 'PERSISTENCE_ONLY' === ( $definition['status'] ?? '' ) ) {
				$args['type'] = \Elementor\Controls_Manager::HIDDEN;
			}
			if ( array_key_exists( 'default', $definition ) && null !== $definition['default'] ) { $args['default'] = $definition['default']; }
			if ( ! empty( $definition['options'] ) ) { $args['options'] = $definition['options']; }
			if ( ! empty( $definition['selectors'] ) ) {
				$args['selectors'] = array();
				foreach ( $definition['selectors'] as $selector => $declaration ) { $args['selectors']['{{WRAPPER}} ' . $selector] = $declaration; }
			}
			if ( ! empty( $definition['responsive'] ) ) { $this->add_responsive_control( $control_id, $args ); } else { $this->add_control( $control_id, $args ); }
		}
		foreach ( (array) ( $registry['groups'] ?? array() ) as $group_id => $definition ) {
			$args = array( 'name' => $group_id );
			if ( ! empty( $definition['selector'] ) ) { $args['selector'] = '{{WRAPPER}} ' . $definition['selector']; }
			$this->add_group_control( (string) ( $definition['type'] ?? 'unknown' ), $args );
		}
		$this->end_controls_section();
	}
	protected function compatibility_id( string $prefix ): string {
		$id = method_exists( $this, 'get_id' ) ? sanitize_html_class( (string) $this->get_id() ) : '';
		return sanitize_html_class( $prefix . ( '' !== $id ? '-' . $id : '' ) );
	}
	/** @return array<string,mixed> */
	protected function settings(): array {
		$settings = $this->get_settings_for_display();
		return array_merge( LegacyControlInventory::defaults( $this->get_name() ), is_array( $settings ) ? $settings : array() );
	}
	protected static function on( array $settings, string $key, bool $default = false ): bool {
		return array_key_exists( $key, $settings ) ? 'yes' === (string) $settings[ $key ] : $default;
	}
	protected static function post_id(): int { return absint( get_the_ID() ); }
}
