<?php
/** Shared non-article static-page renderer. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Page;

use Abhinava\SiteMaster\Components\Breadcrumbs\BreadcrumbRenderer;
use Abhinava\SiteMaster\Components\Layout\PageShellRenderer;
use Abhinava\SiteMaster\Media\ImageRenderer;

defined( 'ABSPATH' ) || exit;

final class StaticPageRenderer {
	private PageShellRenderer $shell;
	private BreadcrumbRenderer $breadcrumbs;

	public function __construct( ?PageShellRenderer $shell = null, ?BreadcrumbRenderer $breadcrumbs = null ) {
		$this->shell       = $shell ?? new PageShellRenderer();
		$this->breadcrumbs = $breadcrumbs ?? new BreadcrumbRenderer();
	}

	/** @param array<string,mixed> $options */
	public function render( StaticPageViewModel $view_model, array $options = array() ): string {
		$data = $view_model->data();
		if ( '' === trim( (string) $data['title'] ) ) {
			return '';
		}

		$content = '<header class="sm-page-header"><h1>' . esc_html( (string) $data['title'] ) . '</h1></header>';
		if ( ! empty( $options['show_image'] ) && (int) $data['image_id'] > 0 ) {
			$image_size = sanitize_key( (string) ( $options['image_size'] ?? 'full' ) );
			$image = ImageRenderer::hero( (int) $data['image_id'], '' !== $image_size ? $image_size : 'full', array( 'alt' => (string) $data['image_alt'], 'sizes' => '100vw' ) );
			if ( '' !== $image ) {
				$caption = ! empty( $options['show_caption'] ) && '' !== trim( (string) $data['image_caption'] ) ? '<figcaption>' . esc_html( (string) $data['image_caption'] ) . '</figcaption>' : '';
				$content .= '<figure class="sm-page-hero">' . $image . $caption . '</figure>';
			}
		}

		if ( '' !== trim( (string) $data['content'] ) ) {
			$processed = apply_filters( 'the_content', (string) $data['content'] );
			if ( is_string( $processed ) && '' !== trim( $processed ) ) {
				$content .= '<div class="sm-page-content">' . $processed . '</div>';
			}
		}

		return $this->shell->render(
			$content,
			array(
				'breadcrumb'      => ! array_key_exists( 'show_breadcrumbs', $options ) || (bool) $options['show_breadcrumbs'] ? $this->breadcrumbs->render() : '',
				'sidebar'         => is_string( $options['sidebar'] ?? null ) ? $options['sidebar'] : '',
				'sidebar_label'   => is_string( $options['sidebar_label'] ?? null ) ? $options['sidebar_label'] : '',
				'show_skip_link'  => ! empty( $options['show_skip_link'] ),
				'skip_link_label' => is_string( $options['skip_link_label'] ?? null ) ? $options['skip_link_label'] : '',
			)
		);
	}
}
