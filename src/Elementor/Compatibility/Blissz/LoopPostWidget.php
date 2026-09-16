<?php
/** Legacy Blissz Loop Post adapter using the shared Post Card. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Blissz;
use Abhinava\SiteMaster\Components\PostCard\PostCardRenderer;
use Abhinava\SiteMaster\Components\PostCard\PostCardViewModel;
use Abhinava\SiteMaster\Support\DateFormatter;
defined( 'ABSPATH' ) || exit;
final class LoopPostWidget extends LegacyWidgetBase {
	public function get_name(): string { return 'tbz_loop_post_block'; }
	public function get_title(): string { return esc_html__( 'Blissz Loop Post Block', 'site-master' ); }
	public function get_icon(): string { return 'eicon-loop-builder'; }
	public function render_compatibility( ?array $settings = null, int $post_id = 0 ): string {
		$settings = $settings ?? $this->settings(); $post_id = $post_id > 0 ? $post_id : self::post_id(); $length = max( 1, min( 100, absint( $settings['excerpt_length'] ?? 20 ) ) ); $model = $post_id > 0 ? PostCardViewModel::from_post( $post_id, $length ) : null; if ( ! $model ) { return ''; }
		$tag = strtolower( (string) ( $settings['title_tag'] ?? 'h3' ) ); if ( ! in_array( $tag, array( 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) { $tag = 'h3'; }
		$date_format = self::date_format( (string) ( $settings['date_format'] ?? 'M j, Y' ) ); $data = $model->data(); $data['published_date'] = DateFormatter::published( $post_id, $date_format ); $model = new PostCardViewModel( $data );
		$post = get_post( $post_id ); $words = $post instanceof \WP_Post ? str_word_count( wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) ) ) : 0; $minutes = max( 1, (int) ceil( $words / 200 ) );
		$html = ( new PostCardRenderer() )->render( $model, array( 'heading_level' => $tag, 'show_image' => self::on( $settings, 'show_image', true ), 'show_category' => self::on( $settings, 'show_category', true ), 'show_title' => self::on( $settings, 'show_title', true ), 'link_title' => self::on( $settings, 'link_title', true ), 'show_excerpt' => self::on( $settings, 'show_excerpt', true ), 'show_author' => self::on( $settings, 'show_author', true ), 'show_avatar' => self::on( $settings, 'show_author_image', false ), 'show_date' => self::on( $settings, 'show_date', true ), 'show_reading_time' => self::on( $settings, 'show_reading_time', true ), 'reading_minutes' => $minutes, 'show_button' => self::on( $settings, 'show_button', true ), 'button_text' => (string) ( $settings['button_text'] ?? '' ), 'classes' => array( 'tbz-lpb' ) ) );
		return $html;
	}
	private static function date_format( string $format ): string { return in_array( $format, array( 'M j, Y', 'F j, Y', 'm/d/Y', 'd/m/Y', 'Y-m-d' ), true ) ? $format : 'M j, Y'; }
	protected function render(): void { echo $this->render_compatibility(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ }
}
