<?php
/** Development 0.0.5 complete single-article semantic tests. */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['sm_posts'] = array(); $GLOBALS['sm_meta'] = array(); $GLOBALS['sm_terms'] = array(); $GLOBALS['sm_filter_count'] = 0; $GLOBALS['sm_has_image'] = true; $GLOBALS['sm_close_dates'] = false;

class WP_Post { public int $ID; public int $post_author = 7; public string $post_type = 'post'; public string $post_excerpt = ''; public string $post_content = ''; public function __construct( int $id, string $type = 'post' ) { $this->ID = $id; $this->post_type = $type; } }
class WP_Term { public int $term_id; public string $name; public function __construct( int $id, string $name ) { $this->term_id = $id; $this->name = $name; } }
class WP_User { public int $ID; public string $display_name; public function __construct( int $id, string $name ) { $this->ID = $id; $this->display_name = $name; } }
class WP_Error {}

function sm_expect( bool $condition, string $message ): void { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function absint( $value ): int { return abs( (int) $value ); }
function sanitize_key( $value ): string { return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) ?? '' ); }
function sanitize_html_class( $value ): string { return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value ) ?? ''; }
function sanitize_text_field( $value ): string { return trim( strip_tags( (string) $value ) ); }
function esc_url( $value ): string { return str_starts_with( (string) $value, 'javascript:' ) ? '' : htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ): string { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ): string { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $value, $domain = null ): string { return (string) $value; }
function __( $value, $domain = null ): string { return (string) $value; }
function is_wp_error( $value ): bool { return $value instanceof WP_Error; }
function get_post( $id ) { if ( 77 === (int) $id ) { return new WP_Post( 77, 'attachment' ); } return $GLOBALS['sm_posts'][ (int) $id ] ?? null; }
function get_post_mime_type( $id ): string { return 'image/jpeg'; }
function get_permalink( $id ): string { return 'https://example.test/story/' . (int) $id . '/'; }
function get_the_title( $id ): string { return 1 === (int) $id ? 'Headline <unsafe>' : 'Second headline'; }
function get_post_thumbnail_id( $id ): int { return $GLOBALS['sm_has_image'] ? 91 : 0; }
function wp_get_attachment_caption( $id ): string { return 'Caption <credit>'; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['sm_meta'][ (int) $id ][ $key ] ?? ''; }
function get_the_terms( $id, $taxonomy ) { return $GLOBALS['sm_terms'][ (int) $id ][ $taxonomy ] ?? array(); }
function get_term_link( $term ): string { return 'https://example.test/term/' . (int) $term->term_id . '/'; }
function get_userdata( $id ) { return 7 === (int) $id ? new WP_User( 7, 'Author <unsafe>' ) : false; }
function get_author_posts_url( $id ): string { return 'https://example.test/author/' . (int) $id . '/'; }
function get_user_meta( $id, $key, $single = false ) { return 'profile_picture' === $key ? 77 : ''; }
function wp_get_attachment_image_src( $id, $size ) { return array( 'author.jpg', 48, 48, true ); }
function wp_get_attachment_image( $id, $size, $icon = false, $attributes = array() ): string { if ( ! $GLOBALS['sm_has_image'] ) { return ''; } return '<img src="hero.jpg" width="1200" height="675" srcset="hero.jpg 1200w" sizes="' . esc_attr( $attributes['sizes'] ?? '' ) . '" loading="' . esc_attr( $attributes['loading'] ?? '' ) . '" alt="' . esc_attr( $attributes['alt'] ?? '' ) . '">'; }
function get_avatar( $id, $size, $default = '', $alt = '', $args = array() ): string { return '<img class="sm-article__avatar" src="avatar.jpg" width="48" height="48" alt="' . esc_attr( $alt ) . '">'; }
function get_option( $key, $default = false ) { return 'date_format' === $key ? 'F j, Y' : ( 'time_format' === $key ? 'g:i A' : $default ); }
function get_post_datetime( $id, $field = 'date', $source = 'local' ) { if ( 'modified' === $field ) { $time = $GLOBALS['sm_close_dates'] ? '2026-09-14 10:00:30 +05:30' : '2026-09-14 11:00:00 +05:30'; } else { $time = '2026-09-14 10:00:00 +05:30'; } return new DateTimeImmutable( $time ); }
function wp_timezone(): DateTimeZone { return new DateTimeZone( 'Asia/Kolkata' ); }
function wp_date( $format, $timestamp, $timezone = null ): string { return ( new DateTimeImmutable( '@' . $timestamp ) )->setTimezone( $timezone ?: wp_timezone() )->format( $format ); }
function apply_filters( $hook, $value ) { sm_expect( 'the_content' === $hook, 'Unexpected content filter.' ); ++$GLOBALS['sm_filter_count']; return '<p class="filtered">' . $value . '</p>'; }

require_once dirname( __DIR__ ) . '/src/Content/AuthorData.php'; require_once dirname( __DIR__ ) . '/src/Content/PostData.php'; require_once dirname( __DIR__ ) . '/src/Support/DateFormatter.php'; require_once dirname( __DIR__ ) . '/src/Media/ImageRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/SingleArticleViewModel.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleHeaderRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleHeroRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleBodyRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleFooterRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/SingleArticleRenderer.php'; require_once dirname( __DIR__ ) . '/src/Elementor/ArticleContainerConvention.php';

use Abhinava\SiteMaster\Components\Article\SingleArticleRenderer; use Abhinava\SiteMaster\Components\Article\SingleArticleViewModel; use Abhinava\SiteMaster\Elementor\ArticleContainerConvention;

$post = new WP_Post( 1 ); $post->post_excerpt = 'Visible summary <script>'; $post->post_content = '<!-- wp:paragraph -->Body [approved-shortcode]<!-- /wp:paragraph -->'; $GLOBALS['sm_posts'][1] = $post;
$GLOBALS['sm_meta'][1]['rank_math_primary_category'] = 10; $GLOBALS['sm_meta'][91]['_wp_attachment_image_alt'] = 'Hero <alt>';
$GLOBALS['sm_terms'][1]['category'] = array( new WP_Term( 10, 'News <section>' ) ); $GLOBALS['sm_terms'][1]['post_tag'] = array( new WP_Term( 20, 'Topic <one>' ), new WP_Term( 30, 'Topic Two' ) );

$model = SingleArticleViewModel::from_post( 1 ); sm_expect( $model instanceof SingleArticleViewModel, 'Article view model failed.' ); sm_expect( 77 === $model->data()['author']['attachment_id'], 'AuthorData profile_picture was not reused.' ); sm_expect( 10 === $model->data()['category']['id'], 'Shared category resolution was not reused.' ); sm_expect( 2 === count( $model->data()['topics'] ), 'Topics were not normalized.' );

$renderer = new SingleArticleRenderer(); $html = $renderer->render( $model, array( 'show_avatar' => true, 'classes' => array( 'layout-wide', 'bad" onclick="x' ) ) );
sm_expect( 1 === substr_count( $html, '<article ' ) && 1 === substr_count( $html, '</article>' ), 'Primary article count is not one.' ); sm_expect( 1 === substr_count( $html, '<h1 ') && 1 === substr_count( $html, '</h1>' ), 'Article H1 count is not one.' );
$open = strpos( $html, '<article ' ); $header = strpos( $html, '<header ' ); $body = strpos( $html, '<div class="sm-article__body">' ); $footer = strpos( $html, '<footer ' ); $close = strrpos( $html, '</article>' );
sm_expect( false !== $open && $open < $header && $header < $body && $body < $footer && $footer < $close && str_ends_with( $html, '</article>' ), 'Header/body/footer are not inside one article.' );
sm_expect( 1 === $GLOBALS['sm_filter_count'] && str_contains( $html, '<p class="filtered"><!-- wp:paragraph -->Body [approved-shortcode]'), 'the_content processing was not exactly once/preserved.' );
sm_expect( str_contains( $html, 'rel="author"') && str_contains( $html, 'class="sm-article__published"') && str_contains( $html, 'class="sm-article__modified"'), 'Author or distinct date output missing.' ); sm_expect( str_contains( $html, '+05:30') && str_contains( $html, 'Published') && str_contains( $html, 'Updated'), 'Exact ISO/labeled dates missing.' );
sm_expect( str_contains( $html, '<figure class="sm-article__hero"><img') && str_contains( $html, '<figcaption>Caption &lt;credit&gt;</figcaption>'), 'Hero figure/caption missing.' ); sm_expect( ! str_contains( substr( $html, strpos( $html, '<figure'), strpos( $html, '</figure>') - strpos( $html, '<figure') ), '<a '), 'Hero was automatically linked.' ); sm_expect( str_contains( $html, 'width="1200"') && str_contains( $html, 'srcset=') && str_contains( $html, 'loading="eager"'), 'Responsive hero/loading context failed.' );
sm_expect( str_contains( $html, 'sm-article__topics') && str_contains( $html, 'https://example.test/term/20/') && str_contains( $html, 'https://example.test/term/30/'), 'Topic links missing.' );
sm_expect( ! preg_match( '/itemscope|itemtype|itemprop|application\/ld\+json|<script>|onclick=/i', $html ), 'Unsafe/schema output detected.' ); sm_expect( str_contains( $html, 'Headline &lt;unsafe&gt;') && str_contains( $html, 'Visible summary &lt;script&gt;') && str_contains( $html, 'Author &lt;unsafe&gt;') && str_contains( $html, 'News &lt;section&gt;'), 'Generated values were not escaped.' );

$GLOBALS['sm_filter_count'] = 0; $GLOBALS['sm_has_image'] = false; $minimal = $renderer->render( $model, array( 'show_category' => false, 'show_summary' => false, 'show_author' => false, 'show_published' => false, 'show_modified' => false, 'show_topics' => false ) );
sm_expect( ! str_contains( $minimal, 'sm-article__hero') && ! str_contains( $minimal, 'sm-article__meta') && ! str_contains( $minimal, 'sm-article__footer') && ! str_contains( $minimal, 'sm-article__summary'), 'Disabled/empty wrappers rendered.' ); sm_expect( 1 === $GLOBALS['sm_filter_count'], 'Minimal article content was not processed once.' );

$GLOBALS['sm_close_dates'] = true; $close_model = SingleArticleViewModel::from_post( 1 ); sm_expect( null === $close_model->data()['modified_date'], 'Effectively identical modified time was exposed.' );
$no_body = new SingleArticleViewModel( array( 'headline' => 'Only headline' ) ); $no_body_html = $renderer->render( $no_body ); sm_expect( ! str_contains( $no_body_html, 'sm-article__body') && ! str_contains( $no_body_html, 'sm-article__footer'), 'Empty body/footer wrappers rendered.' );
$no_headline = new SingleArticleViewModel( array( 'content' => 'Body' ) ); sm_expect( '' === $renderer->render( $no_headline ), 'Article rendered without required headline.' );
sm_expect( ArticleContainerConvention::is_valid_tag( 'article' ) && ArticleContainerConvention::is_valid_tag( ' ARTICLE ' ) && ! ArticleContainerConvention::is_valid_tag( 'div' ), 'Elementor article-container convention failed.' );

$source_root = dirname( __DIR__ ) . '/src'; $forbidden_classes = array(); $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source_root ) ); foreach ( $iterator as $file ) { if ( $file->isFile() && 'php' === strtolower( $file->getExtension() ) ) { $source = file_get_contents( $file->getPathname() ); if ( preg_match( '/class\s+Article(?:Start|End)\b/i', (string) $source ) ) { $forbidden_classes[] = $file->getPathname(); } } } sm_expect( array() === $forbidden_classes, 'Article Start/End runtime class detected.' );

echo "Site Master single-article tests passed.\n";
