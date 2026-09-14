<?php
/** Shared one-main page composition boundary. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Layout;

use Abhinava\SiteMaster\Components\Accessibility\SkipLinkRenderer;

defined( 'ABSPATH' ) || exit;

final class PageShellRenderer {
	private SkipLinkRenderer $skip_link;
	private SidebarRenderer $sidebar;

	public function __construct( ?SkipLinkRenderer $skip_link = null, ?SidebarRenderer $sidebar = null ) {
		$this->skip_link = $skip_link ?? new SkipLinkRenderer();
		$this->sidebar   = $sidebar ?? new SidebarRenderer();
	}

	/** @param array<string,mixed> $options */
	public function render( string $primary, array $options = array() ): string {
		if ( '' === trim( $primary ) || preg_match( '/<main\b/i', $primary ) || preg_match( '/\bid\s*=\s*(["\'])main\1/i', $primary ) ) {
			return '';
		}

		$breadcrumb = is_string( $options['breadcrumb'] ?? null ) ? trim( $options['breadcrumb'] ) : '';
		if ( preg_match( '/<main\b/i', $breadcrumb ) ) {
			$breadcrumb = '';
		}

		$sidebar = $this->sidebar->render(
			is_string( $options['sidebar'] ?? null ) ? $options['sidebar'] : '',
			is_string( $options['sidebar_label'] ?? null ) ? $options['sidebar_label'] : ''
		);
		if ( '' !== $sidebar ) {
			$primary = '<div class="sm-layout"><div class="sm-content-column">' . $primary . '</div>' . $sidebar . '</div>';
		}

		$output = '';
		if ( ! empty( $options['show_skip_link'] ) ) {
			$output .= $this->skip_link->render( 'main', is_string( $options['skip_link_label'] ?? null ) ? $options['skip_link_label'] : '' );
		}

		return $output . '<main id="main">' . $breadcrumb . $primary . '</main>';
	}
}
