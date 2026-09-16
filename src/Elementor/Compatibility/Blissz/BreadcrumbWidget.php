<?php
/** Legacy Blissz breadcrumb adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Components\Breadcrumbs\BreadcrumbRenderer;
defined( 'ABSPATH' ) || exit;
final class BreadcrumbWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbm_breadcrumb'; }
	public function get_title(): string { return esc_html__( 'Blissz Breadcrumb', 'site-master' ); }
	public function get_icon(): string { return 'eicon-navigation-horizontal'; }
	public function render_compatibility(): string { return ( new BreadcrumbRenderer() )->render(); }
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
