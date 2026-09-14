<?php
/** Development 0.0.6 page, author, sidebar and accessibility tests. */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' ); define( 'RANK_MATH_VERSION', 'test' );
$GLOBALS['sm_posts'] = array(); $GLOBALS['sm_terms'] = array(); $GLOBALS['sm_meta'] = array(); $GLOBALS['sm_query_posts'] = array(); $GLOBALS['sm_last_query'] = array(); $GLOBALS['sm_content_filters'] = 0; $GLOBALS['sm_image'] = true;

class WP_Post { public int $ID; public int $post_author = 7; public int $post_parent = 0; public string $post_type; public string $post_excerpt = ''; public string $post_content = ''; public function __construct( int $id, string $type = 'post' ) { $this->ID = $id; $this->post_type = $type; } }
class WP_Term { public int $term_id; public string $name; public function __construct( int $id, string $name ) { $this->term_id = $id; $this->name = $name; } }
class WP_User { public int $ID; public string $display_name; public function __construct( int $id, string $name ) { $this->ID = $id; $this->display_name = $name; } }
class WP_Error {}
class WP_Query { public array $posts; public int $max_num_pages; private array $args; public function __construct( array $args = array() ) { $this->args = $args; $GLOBALS['sm_last_query'] = $args; $this->posts = $GLOBALS['sm_query_posts']; $this->max_num_pages = empty( $args['no_found_rows'] ) ? 3 : 1; } public function get( string $key ) { return $this->args[ $key ] ?? null; } }

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
function __( $value, $domain = null ): string { return (string) $value; }
function is_wp_error( $value ): bool { return $value instanceof WP_Error; }
function wp_kses( $html, $allowed ): string { return preg_replace( '/\s(?:itemscope|itemtype|itemprop)=(?:"[^"]*"|\'[^\']*\')/i', '', strip_tags( (string) $html, '<nav><p><span><a>' ) ) ?? ''; }
function wp_kses_post( $html ): string { return strip_tags( (string) $html, '<p><a><strong><em>' ); }
function wpautop( $text ): string { return '<p>' . (string) $text . '</p>'; }
function rank_math_the_breadcrumbs(): void { echo '<nav aria-label="Breadcrumb"><a href="/">Home</a></nav>'; }
function get_post( $id ) { if ( 77 === (int) $id ) { return new WP_Post( 77, 'attachment' ); } return $GLOBALS['sm_posts'][ (int) $id ] ?? null; }
function get_post_mime_type( $id ): string { return 'image/jpeg'; }
function get_permalink( $id ): string { return 'https://example.test/content/' . (int) $id . '/'; }
function get_the_title( $id ): string { return 10 === (int) $id ? 'About <Site>' : 'Post headline'; }
function get_post_thumbnail_id( $id ): int { return $GLOBALS['sm_image'] ? 91 : 0; }
function wp_get_attachment_caption( $id ): string { return 'Image caption'; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['sm_meta'][ (int) $id ][ $key ] ?? ''; }
function get_the_terms( $id, $taxonomy ) { return $GLOBALS['sm_terms'][ (int) $id ][ $taxonomy ] ?? array(); }
function get_term_link( $term ): string { return 'https://example.test/term/' . (int) $term->term_id . '/'; }
function strip_shortcodes( $text ): string { return preg_replace( '/\[[^\]]+\]/', '', (string) $text ) ?? ''; }
function wp_strip_all_tags( $text, $remove_breaks = false ): string { return trim( strip_tags( (string) $text ) ); }
function wp_trim_words( $text, $length, $more = null ): string { return trim( (string) $text ); }
function get_userdata( $id ) { return 7 === (int) $id ? new WP_User( 7, 'Jane <Editor>' ) : false; }
function get_author_posts_url( $id ): string { return 'https://example.test/author/' . (int) $id . '/'; }
function get_user_meta( $id, $key, $single = false ) { if ( 'profile_picture' === $key ) { return 77; } if ( 'description' === $key ) { return 'Public <strong>biography</strong> <script>bad</script>'; } return ''; }
function wp_get_attachment_image_src( $id, $size ) { return array( 'avatar.jpg', 160, 160, true ); }
function wp_get_attachment_image( $id, $size, $icon = false, $attributes = array() ): string { if ( ! $GLOBALS['sm_image'] ) { return ''; } return '<img src="image.jpg" width="800" height="450" srcset="image.jpg 800w" loading="' . esc_attr( $attributes['loading'] ?? '' ) . '" alt="' . esc_attr( $attributes['alt'] ?? '' ) . '">'; }
function get_avatar( $id, $size, $default = '', $alt = '', $args = array() ): string { return '<img class="sm-author-profile__avatar" src="avatar.jpg" width="160" height="160" alt="' . esc_attr( $alt ) . '">'; }
function get_option( $key, $default = false ) { return 'date_format' === $key ? 'F j, Y' : ( 'time_format' === $key ? 'g:i A' : $default ); }
function get_post_datetime( $id, $field = 'date', $source = 'local' ) { return new DateTimeImmutable( '2026-09-14 10:00:00 +05:30' ); }
function wp_timezone(): DateTimeZone { return new DateTimeZone( 'Asia/Kolkata' ); }
function wp_date( $format, $timestamp, $timezone = null ): string { return ( new DateTimeImmutable( '@' . $timestamp ) )->setTimezone( $timezone ?: wp_timezone() )->format( $format ); }
function apply_filters( $hook, $value ) { sm_expect( 'the_content' === $hook, 'Unexpected filter.' ); ++$GLOBALS['sm_content_filters']; return '<div class="blocks">' . $value . '</div>'; }
function get_pagenum_link( $page ): string { return '/page/' . (int) $page . '/'; }
function paginate_links( $args ) { return array( '<a class="page-numbers" href="/page/1/">1</a>', '<a class="page-numbers" href="/page/2/">2</a>' ); }

require_once dirname( __DIR__ ) . '/src/SEO/RankMathBridge.php'; require_once dirname( __DIR__ ) . '/src/Components/Breadcrumbs/BreadcrumbRenderer.php'; require_once dirname( __DIR__ ) . '/src/Content/AuthorData.php'; require_once dirname( __DIR__ ) . '/src/Content/PostData.php'; require_once dirname( __DIR__ ) . '/src/Content/EditorialQuery.php'; require_once dirname( __DIR__ ) . '/src/Support/DateFormatter.php'; require_once dirname( __DIR__ ) . '/src/Media/ImageRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/PostCard/PostCardViewModel.php'; require_once dirname( __DIR__ ) . '/src/Components/PostCard/PostCardRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Listing/PaginationRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Listing/PostListRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Accessibility/SkipLinkRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Layout/SidebarRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Layout/PageShellRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Page/StaticPageViewModel.php'; require_once dirname( __DIR__ ) . '/src/Components/Page/StaticPageRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Author/AuthorPageViewModel.php'; require_once dirname( __DIR__ ) . '/src/Components/Author/AuthorPageRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/SingleArticleViewModel.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleHeaderRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleHeroRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleBodyRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/ArticleFooterRenderer.php'; require_once dirname( __DIR__ ) . '/src/Components/Article/SingleArticleRenderer.php';

use Abhinava\SiteMaster\Components\Article\SingleArticleRenderer; use Abhinava\SiteMaster\Components\Article\SingleArticleViewModel; use Abhinava\SiteMaster\Components\Author\AuthorPageRenderer; use Abhinava\SiteMaster\Components\Author\AuthorPageViewModel; use Abhinava\SiteMaster\Components\Layout\PageShellRenderer; use Abhinava\SiteMaster\Components\Layout\SidebarRenderer; use Abhinava\SiteMaster\Components\Page\StaticPageRenderer; use Abhinava\SiteMaster\Components\Page\StaticPageViewModel;

$page = new WP_Post( 10, 'page' ); $page->post_content = '<!-- wp:paragraph -->Page body [form]<!-- /wp:paragraph -->'; $page->post_parent = 2; $GLOBALS['sm_posts'][10] = $page; $GLOBALS['sm_meta'][91]['_wp_attachment_image_alt'] = 'Page illustration';
$page_model = StaticPageViewModel::from_page( 10 ); sm_expect( $page_model instanceof StaticPageViewModel && 2 === $page_model->data()['parent_id'], 'Static page model failed.' );
$page_html = ( new StaticPageRenderer() )->render( $page_model, array( 'show_skip_link' => true, 'show_image' => true, 'show_caption' => true ) );
sm_expect( 1 === substr_count( $page_html, '<main id="main">') && 1 === substr_count( $page_html, '<h1>'), 'Static page main/H1 count failed.' ); sm_expect( ! str_contains( $page_html, '<article'), 'Static page incorrectly uses article.' ); sm_expect( 1 === $GLOBALS['sm_content_filters'] && str_contains( $page_html, '<div class="blocks"><!-- wp:paragraph -->Page body [form]'), 'Page content processing failed.' ); sm_expect( strpos( $page_html, 'aria-label="Breadcrumb"') < strpos( $page_html, 'sm-page-header'), 'Breadcrumb is not before page content.' ); sm_expect( str_starts_with( $page_html, '<a class="sm-skip-link" href="#main">') && str_contains( $page_html, '<figure class="sm-page-hero">'), 'Skip-link/optional image failed.' );
$minimal_page = ( new StaticPageRenderer() )->render( new StaticPageViewModel( array( 'title' => 'Terms' ) ), array( 'show_breadcrumbs' => false ) ); sm_expect( ! str_contains( $minimal_page, 'sm-page-content') && ! str_contains( $minimal_page, 'sm-page-hero') && ! str_contains( $minimal_page, '<nav'), 'Static empty wrappers rendered.' );

$post = new WP_Post( 20 ); $post->post_excerpt = 'Card excerpt'; $GLOBALS['sm_posts'][20] = $post; $GLOBALS['sm_query_posts'] = array( $post ); $GLOBALS['sm_terms'][20]['category'] = array( new WP_Term( 5, 'News' ) );
$author_model = AuthorPageViewModel::from_user( 7 ); sm_expect( $author_model instanceof AuthorPageViewModel && 77 === $author_model->data()['attachment_id'], 'AuthorData was not reused.' );
$author_html = ( new AuthorPageRenderer() )->render( $author_model, array( 'paged' => 2, 'show_skip_link' => true ) );
sm_expect( 1 === substr_count( $author_html, '<main id="main">') && str_contains( $author_html, '<h1 id="sm-author-heading-7">Jane &lt;Editor&gt;</h1>'), 'Author main/H1 failed.' ); sm_expect( str_contains( $author_html, '<h2 id="sm-author-posts-heading-7"') && str_contains( $author_html, '<h3 class="sm-post-card__title">'), 'Author H1/H2/H3 hierarchy failed.' ); sm_expect( strpos( $author_html, '<h1 ') < strpos( $author_html, '<h2 ') && strpos( $author_html, '<h2 ') < strpos( $author_html, '<h3 '), 'Author heading order failed.' ); sm_expect( 7 === $GLOBALS['sm_last_query']['author'] && false === $GLOBALS['sm_last_query']['no_found_rows'] && 2 === $GLOBALS['sm_last_query']['paged'], 'Author query/pagination configuration failed.' ); sm_expect( str_contains( $author_html, '<nav class="sm-pagination"') && str_contains( $author_html, 'sm-author-posts'), 'Shared listing/pagination was not reused.' ); sm_expect( str_contains( $author_html, '<strong>biography</strong>') && ! str_contains( $author_html, '<script>'), 'Author bio sanitization failed.' ); sm_expect( str_contains( $author_html, 'alt=""'), 'Adjacent author avatar should be decorative.' );

$GLOBALS['sm_query_posts'] = array(); $empty_author = new AuthorPageViewModel( array( 'id' => 8, 'display_name' => 'Empty Author', 'url' => '/author/8/', 'bio' => '' ) ); $empty_html = ( new AuthorPageRenderer() )->render( $empty_author, array( 'show_avatar' => false, 'paginated' => false, 'show_breadcrumbs' => false ) ); sm_expect( ! str_contains( $empty_html, 'sm-author-profile__bio') && ! str_contains( $empty_html, 'sm-post-list') && str_contains( $empty_html, 'sm-author-posts__empty'), 'Empty biography/archive behavior failed.' ); sm_expect( true === $GLOBALS['sm_last_query']['no_found_rows'], 'Fixed author query should disable found rows.' );

$shell = new PageShellRenderer(); $article = ( new SingleArticleRenderer() )->render( new SingleArticleViewModel( array( 'headline' => 'Article title', 'content' => 'Article body' ) ) ); $layout = $shell->render( $article, array( 'sidebar' => '<p><a href="/more/">More</a></p>', 'sidebar_label' => 'Related resources' ) );
$article_close = strpos( $layout, '</article>'); $aside_open = strpos( $layout, '<aside '); sm_expect( false !== $article_close && false !== $aside_open && $article_close < $aside_open, 'Sidebar is not a sibling after article.' ); sm_expect( 1 === substr_count( $layout, '<main id="main">') && str_contains( $layout, 'aria-label="Related resources"'), 'Sidebar/main semantics failed.' ); sm_expect( '' === ( new SidebarRenderer() )->render( '' ) && '' === ( new SidebarRenderer() )->render( '<main>Nested</main>' ), 'Invalid sidebar was not omitted.' ); sm_expect( '' === $shell->render( '<main>Nested</main>' ) && '' === $shell->render( '<div id="main">Duplicate</div>' ), 'Nested/duplicate main target was accepted.' );

foreach ( array( $page_html, $author_html, $layout ) as $output ) { sm_expect( ! preg_match( '/itemscope|itemtype|itemprop|application\/ld\+json|javascript:|onclick=/i', $output ), 'Schema or unsafe navigation emitted.' ); }
echo "Site Master page/accessibility tests passed.\n";
