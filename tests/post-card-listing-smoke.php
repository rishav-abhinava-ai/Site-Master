<?php
/** Development 0.0.4 Post Card/listing render and query tests. */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['sm_posts'] = array(); $GLOBALS['sm_terms'] = array(); $GLOBALS['sm_meta'] = array(); $GLOBALS['sm_image'] = true;

class WP_Post { public int $ID; public int $post_author; public string $post_type = 'post'; public string $post_excerpt = ''; public string $post_content = ''; public function __construct( int $id, int $author = 7 ) { $this->ID = $id; $this->post_author = $author; } }
class WP_Term { public int $term_id; public string $name; public function __construct( int $id, string $name ) { $this->term_id = $id; $this->name = $name; } }
class WP_User { public int $ID; public string $display_name; public function __construct( int $id, string $name ) { $this->ID = $id; $this->display_name = $name; } }
class WP_Error {}
class WP_Query {
	public array $posts = array(); public int $max_num_pages = 1; private array $args;
	public function __construct( array $args = array() ) { $this->args = $args; $this->posts = array_values( $GLOBALS['sm_posts'] ); $this->max_num_pages = ! empty( $args['no_found_rows'] ) ? 1 : 3; }
	public function get( string $key ) { return $this->args[ $key ] ?? null; }
}

function sm_expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function absint( $value ): int { return abs( (int) $value ); }
function sanitize_key( $value ): string { return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) ?? '' ); }
function sanitize_html_class( $value ): string { return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ) ?? ''; }
function sanitize_text_field( $value ): string { return trim( strip_tags( (string) $value ) ); }
function esc_url( $value ): string { return str_starts_with( (string) $value, 'javascript:' ) ? '' : htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ): string { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ): string { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $value, $domain = null ): string { return (string) $value; }
function esc_attr__( $value, $domain = null ): string { return (string) $value; }
function is_wp_error( $value ): bool { return $value instanceof WP_Error; }
function get_post( $id ) { return $GLOBALS['sm_posts'][ (int) $id ] ?? null; }
function get_permalink( $id ): string { return 'https://example.test/story/' . (int) $id . '/'; }
function get_the_title( $id ): string { return 2 === (int) $id ? '<Dangerous & title>' : 'Safe title'; }
function get_post_thumbnail_id( $id ): int { return $GLOBALS['sm_image'] ? 90 + (int) $id : 0; }
function wp_get_attachment_caption( $id ): string { return 91 === (int) $id ? 'Photo <credit>' : ''; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['sm_meta'][ (int) $id ][ $key ] ?? ''; }
function get_the_terms( $id, $taxonomy ) { return $GLOBALS['sm_terms'][ (int) $id ] ?? array(); }
function get_term_link( $term ): string { return 'https://example.test/category/' . (int) $term->term_id . '/'; }
function strip_shortcodes( $text ): string { return preg_replace( '/\[[^\]]+\]/', '', (string) $text ) ?? ''; }
function wp_strip_all_tags( $text, $remove_breaks = false ): string { return trim( strip_tags( (string) $text ) ); }
function wp_trim_words( $text, $length, $more = null ): string { $words = preg_split( '/\s+/', trim( (string) $text ) ) ?: array(); return implode( ' ', array_slice( $words, 0, (int) $length ) ) . ( count( $words ) > $length ? '…' : '' ); }
function get_userdata( $id ) { return 7 === (int) $id ? new WP_User( 7, 'Author <script>' ) : false; }
function get_author_posts_url( $id ): string { return 'https://example.test/author/' . (int) $id . '/'; }
function get_user_meta( $id, $key, $single = false ) { return 'profile_picture' === $key ? 77 : ''; }
function get_post_mime_type( $id ): string { return 'image/jpeg'; }
function wp_get_attachment_image_src( $id, $size ) { return array( 'author.jpg', 32, 32, true ); }
function wp_get_attachment_image( $id, $size, $icon = false, $attributes = array() ): string { if ( ! $GLOBALS['sm_image'] ) { return ''; } return '<img src="image.jpg" width="640" height="360" srcset="image.jpg 640w" sizes="' . esc_attr( $attributes['sizes'] ?? '' ) . '" loading="' . esc_attr( $attributes['loading'] ?? '' ) . '" alt="' . esc_attr( $attributes['alt'] ?? '' ) . '">'; }
function get_avatar( $id, $size, $default = '', $alt = '', $args = array() ): string { return '<img class="sm-post-card__avatar" src="avatar.jpg" width="32" height="32" alt="' . esc_attr( $alt ) . '">'; }
function get_option( $key, $default = false ) { return 'date_format' === $key ? 'F j, Y' : ( 'time_format' === $key ? 'g:i A' : $default ); }
function get_post_datetime( $id, $field = 'date', $source = 'local' ) { return new DateTimeImmutable( 'modified' === $field ? '2026-09-15 10:00:00 +05:30' : '2026-09-14 10:00:00 +05:30' ); }
function wp_timezone(): DateTimeZone { return new DateTimeZone( 'Asia/Kolkata' ); }
function wp_date( $format, $timestamp, $timezone = null ): string { return ( new DateTimeImmutable( '@' . $timestamp ) )->setTimezone( $timezone ?: wp_timezone() )->format( $format ); }
function get_pagenum_link( $page ): string { return 'https://example.test/page/' . (int) $page . '/'; }
function paginate_links( $args ) { return array( '<a class="prev page-numbers" href="/page/1/">Previous</a>', '<a class="page-numbers" href="/page/2/">2</a>' ); }

require_once dirname( __DIR__ ) . '/src/Content/AuthorData.php'; require_once dirname( __DIR__ ) . '/src/Content/PostData.php'; require_once dirname( __DIR__ ) . '/src/Support/DateFormatter.php'; require_once dirname( __DIR__ ) . '/src/Media/ImageRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/PostCard/PostCardViewModel.php'; require_once dirname( __DIR__ ) . '/src/Components/PostCard/PostCardRenderer.php'; require_once dirname( __DIR__ ) . '/src/Content/EditorialQuery.php'; require_once dirname( __DIR__ ) . '/src/Components/Listing/PaginationRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Listing/PostListRenderer.php';

use Abhinava\SiteMaster\Components\Listing\PostListRenderer; use Abhinava\SiteMaster\Components\PostCard\PostCardRenderer; use Abhinava\SiteMaster\Components\PostCard\PostCardViewModel; use Abhinava\SiteMaster\Content\EditorialQuery; use Abhinava\SiteMaster\Content\PostData;

$post = new WP_Post( 1 ); $post->post_excerpt = 'Manual excerpt with <b>markup</b> and [code]shortcode[/code].'; $post->post_content = 'Generated content should not be used.'; $GLOBALS['sm_posts'][1] = $post;
$GLOBALS['sm_terms'][1] = array( new WP_Term( 10, 'Fallback'), new WP_Term( 20, 'Primary <bad>' ) ); $GLOBALS['sm_meta'][1]['rank_math_primary_category'] = 20;
$model = PostCardViewModel::from_post( 1, 5 ); sm_expect( $model instanceof PostCardViewModel, 'View model was not built.' ); sm_expect( 20 === $model->data()['category']['id'], 'Rank Math primary category was not selected.' ); sm_expect( str_contains( $model->data()['excerpt'], 'Manual excerpt' ) && ! str_contains( $model->data()['excerpt'], 'Generated' ), 'Manual excerpt priority failed.' );

$renderer = new PostCardRenderer(); $home = $renderer->render( $model, array( 'context' => 'homepage', 'show_excerpt' => true, 'show_avatar' => true ) );
sm_expect( str_starts_with( $home, '<article class="sm-post-card">' ), 'Card root is not article.' ); sm_expect( str_contains( $home, '<h3 class="sm-post-card__title"><a href=' ), 'Homepage heading is not H3 with a link.' ); sm_expect( str_contains( $home, 'rel="author"' ) && str_contains( $home, '<time ' ), 'Author/date semantics missing.' ); sm_expect( str_contains( $home, 'width="640"') && str_contains( $home, 'srcset=') && ! str_contains( $home, '<figure'), 'Normal responsive image semantics failed.' ); sm_expect( ! preg_match( '/itemscope|itemtype|itemprop|application\/ld\+json/i', $home ), 'Schema or microdata emitted.' );
sm_expect( str_contains( $home, '<a class="sm-post-card__media" href="https://example.test/story/1/" aria-label="Safe title"><img' ), 'Empty-alt linked media lacks its headline fallback label.' );
sm_expect( str_contains( $home, 'alt=""' ), 'Empty attachment alt was replaced instead of preserving media policy.' );

$meaningful_alt = new PostCardViewModel( array( 'permalink' => 'https://example.test/described/', 'title' => 'Described story', 'image_id' => 92, 'image_alt' => 'Phone displayed on a desk' ) );
$meaningful_html = $renderer->render( $meaningful_alt );
sm_expect( str_contains( $meaningful_html, 'alt="Phone displayed on a desk"' ), 'Meaningful attachment alt was changed.' );
sm_expect( ! preg_match( '/class="sm-post-card__media"[^>]*aria-label=/i', $meaningful_html ), 'Meaningful-alt media received a duplicate fallback label.' );

$hostile_media = new PostCardViewModel( array( 'permalink' => 'https://example.test/hostile/', 'title' => 'Story " <script>bad</script>', 'image_id' => 93, 'image_alt' => '' ) );
$hostile_media_html = $renderer->render( $hostile_media );
sm_expect( str_contains( $hostile_media_html, 'aria-label="Story &quot; &lt;script&gt;bad&lt;/script&gt;"' ), 'Media-link fallback label was not attribute escaped.' );
sm_expect( ! str_contains( $hostile_media_html, 'aria-label="Story " <script>' ), 'Hostile headline broke the media anchor attribute.' );

$markup_only_alt = new PostCardViewModel( array( 'permalink' => 'https://example.test/markup-alt/', 'title' => 'Markup alt story', 'image_id' => 94, 'image_alt' => '<span></span>' ) );
$markup_only_alt_html = $renderer->render( $markup_only_alt );
sm_expect( str_contains( $markup_only_alt_html, 'alt=""' ) && str_contains( $markup_only_alt_html, 'aria-label="Markup alt story"' ), 'Alt sanitization and link fallback became inconsistent.' );

$archive = $renderer->render( $model, array( 'context' => 'archive', 'heading_level' => 'h1', 'show_caption' => true, 'show_excerpt' => false, 'show_author' => false, 'date_type' => 'modified', 'classes' => array( 'layout-grid', 'bad" onclick="x' ) ) );
sm_expect( str_contains( $archive, '<h2 class="sm-post-card__title">' ) && ! str_contains( $archive, '<h1'), 'Unsafe heading was not rejected.' ); sm_expect( str_contains( $archive, '<figure') && str_contains( $archive, '<figcaption>Photo &lt;credit&gt;</figcaption>'), 'Caption figure behavior failed.' ); sm_expect( ! str_contains( $archive, 'rel="author"') && ! str_contains( $archive, 'sm-post-card__excerpt'), 'Disabled elements rendered.' ); sm_expect( ! str_contains( $archive, 'onclick=') && ! str_contains( $archive, '<bad>'), 'Untrusted output was not escaped.' );
sm_expect( preg_match( '/<figure[^>]*>\s*<a class="sm-post-card__media"[^>]*aria-label="Safe title"[^>]*>\s*<img[^>]*alt=""/i', $archive ) === 1, 'Caption figure did not preserve the empty-alt link fallback.' );
sm_expect( strpos( $archive, '</figure>' ) < strpos( $archive, 'sm-post-card__category' ), 'Category was placed inside the figure.' ); sm_expect( str_contains( $archive, 'September 15, 2026' ), 'Explicit modified date was not rendered.' );

$hostile = new PostCardViewModel( array( 'permalink' => 'https://example.test/unsafe/', 'title' => '<script>alert(1)</script>', 'excerpt' => '<img src=x onerror=alert(1)>', 'category' => array( 'name' => '<b>Category</b>', 'url' => 'https://example.test/category/' ), 'author' => array( 'id' => 7, 'display_name' => '<i>Author</i>', 'url' => 'https://example.test/author/' ) ) );
$escaped = $renderer->render( $hostile, array( 'show_excerpt' => true, 'show_date' => false ) );
sm_expect( ! str_contains( $escaped, '<script>') && ! str_contains( $escaped, '<img src=x') && ! str_contains( $escaped, '<b>') && ! str_contains( $escaped, '<i>'), 'Hostile visible values were not escaped.' );

$GLOBALS['sm_image'] = false; $no_image = $renderer->render( $model ); sm_expect( ! str_contains( $no_image, 'sm-post-card__media') && ! str_contains( $no_image, '<figure') && ! str_contains( $no_image, 'aria-label='), 'Missing image emitted media markup or an empty label.' ); $GLOBALS['sm_image'] = true;
$GLOBALS['sm_meta'][1]['rank_math_primary_category'] = 999; $GLOBALS['sm_meta'][1]['_yoast_wpseo_primary_category'] = 10; sm_expect( 10 === PostData::primary_category( 1 )['id'], 'Yoast category fallback failed.' ); $GLOBALS['sm_meta'][1] = array(); sm_expect( 10 === PostData::primary_category( 1 )['id'], 'WordPress category fallback failed.' ); $GLOBALS['sm_terms'][1] = array(); sm_expect( null === PostData::primary_category( 1 ), 'No-category state failed.' );
$generated = new WP_Post( 2 ); $generated->post_content = 'One two three four five six [gallery]'; $GLOBALS['sm_posts'][2] = $generated; sm_expect( 'One two three…' === PostData::excerpt( 2, 3 ), 'Generated excerpt failed.' );

$query_service = new EditorialQuery(); $fixed = $query_service->arguments( array( 'posts_per_page' => 999, 'post_type' => 'evil', 'orderby' => 'meta_value', 'taxonomy' => 'evil', 'term_ids' => array( 1 ), 'include' => array( '2', -3, 0 ) ) ); sm_expect( true === $fixed['no_found_rows'] && 100 === $fixed['posts_per_page'], 'Fixed-query bounds/no_found_rows failed.' ); sm_expect( 'post' === $fixed['post_type'] && 'date' === $fixed['orderby'] && ! isset( $fixed['tax_query'] ), 'Query allow-list failed.' ); sm_expect( array( 2, 3 ) === $fixed['post__in'], 'Query ID normalization failed.' );
$paged = $query_service->arguments( array( 'paginated' => true, 'paged' => 2, 'taxonomy' => 'category', 'term_ids' => array( 10 ) ) ); sm_expect( false === $paged['no_found_rows'] && 2 === $paged['paged'] && isset( $paged['tax_query'] ), 'Paginated query behavior failed.' );

$list = new PostListRenderer(); $before = $GLOBALS['post'] ?? null; $plain = $list->render( array( $model, $model ), array( 'context' => 'archive' ) ); sm_expect( 2 === substr_count( $plain, '<article ') && str_starts_with( $plain, '<div class="sm-post-list">' ) && ! str_contains( $plain, '<li'), 'Plain listing structure failed.' ); $section = $list->render( array( $model ), array( 'heading' => 'Latest <Stories>', 'context' => 'homepage' ) ); sm_expect( str_starts_with( $section, '<section ') && str_contains( $section, 'aria-labelledby=') && str_contains( $section, 'Latest &lt;Stories&gt;'), 'Meaningful section semantics failed.' ); sm_expect( '' === $list->render( array() ), 'Empty listing emitted a wrapper.' );
$query = $query_service->query( array( 'paginated' => true, 'paged' => 2 ) ); $query_output = $list->render_query( $query, array( 'paginated' => true, 'context' => 'archive' ) ); sm_expect( str_contains( $query_output, '<nav class="sm-pagination" aria-label="Pagination">') && str_contains( $query_output, 'href='), 'Crawlable pagination failed.' ); sm_expect( $before === ( $GLOBALS['post'] ?? null ), 'Global post state leaked.' );

echo "Site Master Post Card/listing tests passed.\n";
