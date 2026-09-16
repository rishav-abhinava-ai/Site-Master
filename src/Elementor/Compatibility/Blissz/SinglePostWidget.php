<?php
/** Legacy Blissz Single Post header/media adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Components\Article\ArticleHeaderRenderer;
use Abhinava\SiteMaster\Components\Article\ArticleHeroRenderer;
use Abhinava\SiteMaster\Components\Article\SingleArticleViewModel;
use Abhinava\SiteMaster\Support\DateFormatter;
defined( 'ABSPATH' ) || exit;
final class SinglePostWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbz_loop_single_post_block'; }
	public function get_title(): string { return esc_html__( 'Blissz Loop Single Post Block', 'site-master' ); }
	public function get_icon(): string { return 'eicon-single-post'; }
	public function render_compatibility( ?array $settings = null, int $post_id = 0 ): string {
		$settings = $settings ?? $this->settings(); $post_id = $post_id > 0 ? $post_id : self::post_id(); $model = $post_id > 0 ? SingleArticleViewModel::from_post( $post_id ) : null; if ( ! $model ) { return ''; } $data = $model->data();
		$format = in_array( (string) ( $settings['date_format'] ?? '' ), array( 'M j, Y', 'F j, Y', 'm/d/Y', 'd/m/Y', 'Y-m-d' ), true ) ? (string) $settings['date_format'] : 'M j, Y'; $data['published_date'] = DateFormatter::published( $post_id, $format );
		// Legacy title/schema switches are intentionally normalized: one visible H1, no schema.
		$options = array( 'show_category' => self::on( $settings, 'show_category', true ), 'show_summary' => self::on( $settings, 'show_excerpt', true ), 'show_author' => self::on( $settings, 'show_author', true ), 'show_avatar' => self::on( $settings, 'show_author_image', false ), 'show_published' => self::on( $settings, 'show_date', true ), 'show_modified' => false, 'show_image' => self::on( $settings, 'show_image', true ), 'show_caption' => true, 'image_size' => 'full', 'image_sizes' => '100vw', 'image_loading' => 'eager', 'image_priority' => 'auto' );
		$content = ( new ArticleHeaderRenderer() )->render( $data, $options ); $content .= ( new ArticleHeroRenderer() )->render( $data, $options );
		$post = get_post( $post_id ); $words = $post instanceof \WP_Post ? str_word_count( wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) ) ) : 0;
		if ( self::on( $settings, 'show_reading_time', true ) ) { $minutes = max( 1, (int) ceil( $words / 200 ) ); $content .= '<span class="sm-post-card__reading-time">' . esc_html( sprintf( _n( '%d min read', '%d min read', $minutes, 'site-master' ), $minutes ) ) . '</span>'; }
		$button_text = sanitize_text_field( (string) ( $settings['button_text'] ?? '' ) ); if ( self::on( $settings, 'show_button', false ) && '' !== $button_text ) { $content .= '<a class="sm-post-card__button" href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $button_text ) . '</a>'; }
		return '' !== $content ? '<div class="tbz-lspb">' . $content . '</div>' : '';
	}
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
