<?php
/** Complete shared editorial article composition. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Article;

defined( 'ABSPATH' ) || exit;

final class SingleArticleRenderer {
	private ArticleHeaderRenderer $header;
	private ArticleHeroRenderer $hero;
	private ArticleBodyRenderer $body;
	private ArticleFooterRenderer $footer;

	public function __construct( ?ArticleHeaderRenderer $header = null, ?ArticleHeroRenderer $hero = null, ?ArticleBodyRenderer $body = null, ?ArticleFooterRenderer $footer = null ) {
		$this->header = $header ?? new ArticleHeaderRenderer();
		$this->hero   = $hero ?? new ArticleHeroRenderer();
		$this->body   = $body ?? new ArticleBodyRenderer();
		$this->footer = $footer ?? new ArticleFooterRenderer();
	}

	/** @param array<string,mixed> $options */
	public function render( SingleArticleViewModel $view_model, array $options = array() ): string {
		$data = $view_model->data();
		if ( '' === trim( (string) $data['headline'] ) ) {
			return '';
		}

		$options = $this->options( $options );
		$content = $this->header->render( $data, $options );
		$content .= $this->hero->render( $data, $options );
		$content .= $this->body->render( (string) $data['content'] );
		$content .= $this->footer->render( (array) $data['topics'], $options['show_topics'] );

		$classes = array_merge( array( 'sm-article' ), $options['classes'] );
		return '<article class="' . esc_attr( implode( ' ', $classes ) ) . '">' . $content . '</article>';
	}

	/** @param array<string,mixed> $options @return array<string,mixed> */
	private function options( array $options ): array {
		$classes = array();
		foreach ( (array) ( $options['classes'] ?? array() ) as $class ) {
			$class = sanitize_html_class( (string) $class );
			if ( '' !== $class && 'sm-article' !== $class ) {
				$classes[] = $class;
			}
		}

		return array(
			'show_category'  => ! array_key_exists( 'show_category', $options ) || (bool) $options['show_category'],
			'show_summary'   => ! array_key_exists( 'show_summary', $options ) || (bool) $options['show_summary'],
			'show_author'    => ! array_key_exists( 'show_author', $options ) || (bool) $options['show_author'],
			'show_avatar'    => ! empty( $options['show_avatar'] ),
			'show_published' => ! array_key_exists( 'show_published', $options ) || (bool) $options['show_published'],
			'show_modified'  => ! array_key_exists( 'show_modified', $options ) || (bool) $options['show_modified'],
			'show_image'     => ! array_key_exists( 'show_image', $options ) || (bool) $options['show_image'],
			'show_caption'   => ! array_key_exists( 'show_caption', $options ) || (bool) $options['show_caption'],
			'show_topics'    => ! array_key_exists( 'show_topics', $options ) || (bool) $options['show_topics'],
			'image_size'     => \Abhinava\SiteMaster\Media\ImageRenderer::size( $options['image_size'] ?? 'full' ),
			'image_loading' => $options['image_loading'] ?? 'eager',
			'image_priority' => $options['image_priority'] ?? 'auto',
			'image_sizes'    => is_string( $options['image_sizes'] ?? null ) ? sanitize_text_field( $options['image_sizes'] ) : '100vw',
			'classes'        => array_values( array_unique( $classes ) ),
		);
	}
}
