<?php
/** Legacy Blissz post-content adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Components\Article\ArticleBodyRenderer;
defined( 'ABSPATH' ) || exit;
final class PostContentWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbm-post-content'; }
	public function get_title(): string { return esc_html__( 'Blissz Post Content', 'site-master' ); }
	public function get_icon(): string { return 'eicon-post-content'; }
	public function render_compatibility( int $post_id = 0 ): string {
		$post_id = $post_id > 0 ? $post_id : self::post_id(); $post = $post_id > 0 ? get_post( $post_id ) : null; if ( ! $post instanceof \WP_Post ) { return ''; }
		$body = ( new ArticleBodyRenderer() )->render( (string) $post->post_content );
		return '' !== $body ? '<div class="tbm-post-content-wrapper">' . $body . '</div>' : '';
	}
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
