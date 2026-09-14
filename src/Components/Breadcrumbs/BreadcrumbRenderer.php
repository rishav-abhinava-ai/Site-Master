<?php
/** Visible Rank Math breadcrumb integration. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Breadcrumbs;

use Abhinava\SiteMaster\SEO\RankMathBridge;

defined( 'ABSPATH' ) || exit;

final class BreadcrumbRenderer {
	private RankMathBridge $bridge;

	public function __construct( ?RankMathBridge $bridge = null ) {
		$this->bridge = $bridge ?? new RankMathBridge();
	}

	public function render(): string {
		$output = $this->bridge->breadcrumb_output();
		if ( '' === $output ) {
			return '';
		}

		$allowed = array(
			'nav'  => array( 'aria-label' => true, 'class' => true ),
			'p'    => array( 'class' => true ),
			'span' => array( 'class' => true, 'aria-current' => true ),
			'a'    => array( 'href' => true, 'class' => true, 'rel' => true ),
		);
		$output = wp_kses( $output, $allowed );
		if ( preg_match( '/<nav\b/i', $output ) ) {
			return $output;
		}

		return '<nav class="sm-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'site-master' ) . '">' . $output . '</nav>';
	}
}
