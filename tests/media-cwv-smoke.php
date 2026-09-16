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
function get_intermediate_image_sizes(): array { return array( 'thumbnail', 'medium', 'medium_large', 'large', 'custom-card' ); }
function wp_get_attachment_image( $id, $size, $icon = false, $attributes = array() ): string {
 $GLOBALS['media_size'] = $size; $GLOBALS['media_attributes'] = $attributes;
 if ( ! $GLOBALS['sm_image'] ) { return ''; }
 $attributes = array_merge( array( 'src' => 'image.jpg', 'width' => is_array($size) ? $size[0] : 800, 'height' => is_array($size) ? $size[1] : 450, 'srcset' => 'small.jpg 400w, image.jpg 800w', 'sizes' => '(max-width: 800px) 100vw, 800px', 'alt' => 'Attachment alt' ), $attributes );
 $html = '<img'; foreach ( $attributes as $key => $value ) { if ( false !== $value ) { $html .= ' ' . $key . '="' . esc_attr($value) . '"'; } } return $html . '>';
}
function get_avatar( $id, $size, $default = '', $alt = '', $args = array() ): string { return '<img class="sm-author-profile__avatar" src="avatar.jpg" width="160" height="160" alt="' . esc_attr( $alt ) . '">'; }
function get_option( $key, $default = false ) { return 'date_format' === $key ? 'F j, Y' : ( 'time_format' === $key ? 'g:i A' : $default ); }
function get_post_datetime( $id, $field = 'date', $source = 'local' ) { return new DateTimeImmutable( '2026-09-14 10:00:00 +05:30' ); }
function wp_timezone(): DateTimeZone { return new DateTimeZone( 'Asia/Kolkata' ); }
function wp_date( $format, $timestamp, $timezone = null ): string { return ( new DateTimeImmutable( '@' . $timestamp ) )->setTimezone( $timezone ?: wp_timezone() )->format( $format ); }
function apply_filters( $hook, $value ) { sm_expect( 'the_content' === $hook, 'Unexpected filter.' ); ++$GLOBALS['sm_content_filters']; return '<div class="blocks">' . $value . '</div>'; }
function get_pagenum_link( $page ): string { return '/page/' . (int) $page . '/'; }
function paginate_links( $args ) { return array( '<a class="page-numbers" href="/page/1/">1</a>', '<a class="page-numbers" href="/page/2/">2</a>' ); }

spl_autoload_register( static function( $class ) { $prefix = 'Abhinava\\SiteMaster\\'; if ( str_starts_with($class, $prefix) ) { require_once dirname(__DIR__) . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php'; } } );

use Abhinava\SiteMaster\Media\ImageRenderer;
use Abhinava\SiteMaster\Components\PostCard\PostCardRenderer;
use Abhinava\SiteMaster\Components\PostCard\PostCardViewModel;
use Abhinava\SiteMaster\Components\Article\SingleArticleRenderer;
use Abhinava\SiteMaster\Components\Article\SingleArticleViewModel;
use Abhinava\SiteMaster\Components\Page\StaticPageRenderer;
use Abhinava\SiteMaster\Components\Page\StaticPageViewModel;
use Abhinava\SiteMaster\Content\AuthorData;
use Abhinava\SiteMaster\Content\RssFeaturedImage;

function media_image( string $html ): DOMElement {
 $dom = new DOMDocument(); @$dom->loadHTML($html); $images = $dom->getElementsByTagName('img');
 sm_expect(1 === $images->length, 'Ordinary responsive media must have exactly one image.'); return $images->item(0);
}
foreach (array('card','hero','static-page-featured','avatar','rss','content','generic') as $context) {
 $img = media_image(ImageRenderer::attachment(91, 'custom-card', array('context'=>$context)));
 sm_expect($img->getAttribute('width') === '800' && $img->getAttribute('height') === '450', 'Known dimensions lost.');
 sm_expect(str_contains($img->getAttribute('srcset'), '400w') && $img->hasAttribute('sizes'), 'Core responsive data lost.');
 sm_expect($img->getAttribute('alt') === 'Attachment alt', 'Attachment alt lost.');
 sm_expect($img->getAttribute('fetchpriority') !== 'high', 'Default explicit high priority.');
}
$img = media_image(ImageRenderer::attachment(91, 'large', array('sizes'=>'(max-width: 600px) 100vw, 40vw','alt'=>'')));
sm_expect($img->getAttribute('sizes') === '(max-width: 600px) 100vw, 40vw' && $img->getAttribute('alt') === '', 'Explicit sizes/decorative alt lost.');
sm_expect(ImageRenderer::size('custom-card') === 'custom-card' && ImageRenderer::size(array(32,32)) === array(32,32), 'Valid image sizes rejected.');
foreach (array('bad/path',array(0,32),array(9000,32),array('32',32),array(1),new stdClass()) as $size) { sm_expect(ImageRenderer::size($size) === 'medium_large', 'Invalid size accepted.'); }
$img = media_image(ImageRenderer::attachment(91, 'large', array('context'=>'card','loading'=>'lazy','fetchpriority'=>'high')));
sm_expect($img->getAttribute('loading') === 'eager' && $img->getAttribute('fetchpriority') === 'high', 'Lazy/high conflict not normalized.');
foreach (array('avatar','rss') as $context) { $img=media_image(ImageRenderer::attachment(91,array(32,32),array('context'=>$context,'fetchpriority'=>'high'))); sm_expect(!$img->hasAttribute('fetchpriority'), 'Avatar/feed has priority.'); }
$img=media_image(ImageRenderer::attachment(91,'large',array('context'=>'card','loading'=>array(),'fetchpriority'=>'evil','class'=>'safe" onclick="evil','sizes'=>'100vw" onerror="evil','onerror'=>'evil','src'=>'evil','width'=>1,'itemprop'=>'image')));
sm_expect(!$img->hasAttribute('onerror') && !$img->hasAttribute('onclick') && !$img->hasAttribute('itemprop') && $img->getAttribute('src')==='image.jpg' && $img->getAttribute('width')==='800', 'Unsafe attributes escaped boundary.');
sm_expect($img->getAttribute('loading')==='lazy' && $img->getAttribute('fetchpriority')!=='high', 'Invalid intents not normalized.');
$card = new PostCardViewModel(array('title'=>'Story','permalink'=>'/story/','image_id'=>91,'image_alt'=>'Editorial alt','image_caption'=>'Caption <unsafe>'));
$cards = new PostCardRenderer(); $normal=$cards->render($card); media_image($normal);
sm_expect(str_contains($normal,'<article') && str_contains($normal,'<a class="sm-post-card__media"') && !str_contains($normal,'<figure') && str_contains($normal,'<h3'), 'Card semantics changed.');
sm_expect(!preg_match('/class="sm-post-card__media"[^>]*aria-label=/i',$normal),'Meaningful-alt media received a duplicate fallback label.');
$empty_alt_card = new PostCardViewModel(array('title'=>'Example Story','permalink'=>'/example/','image_id'=>91,'image_alt'=>'','image_caption'=>'Real caption'));
$empty_alt_html=$cards->render($empty_alt_card); $empty_alt_img=media_image($empty_alt_html);
sm_expect($empty_alt_img->getAttribute('alt')==='' && str_contains($empty_alt_html,'aria-label="Example Story"'),'Empty-alt media accessibility fallback failed.');
$empty_alt_figure=$cards->render($empty_alt_card,array('show_caption'=>true));
sm_expect(str_contains($empty_alt_figure,'<figure') && str_contains($empty_alt_figure,'aria-label="Example Story"'),'Caption figure lost media-link fallback.');
$listing=$normal.$normal.$normal; sm_expect(substr_count($listing,'<img')===3 && !str_contains($listing,'fetchpriority="high"'), 'Listing duplicated images or high priorities.');
$caption=$cards->render($card,array('show_caption'=>true)); sm_expect(str_contains($caption,'<figure') && str_contains($caption,'Caption &lt;unsafe&gt;'), 'Card caption escaping lost.');
$explicit=$cards->render($card,array('image_priority'=>'high','image_loading'=>'lazy')); sm_expect(media_image($explicit)->getAttribute('loading')==='eager', 'Card intent forwarding failed.');
$article = new SingleArticleViewModel(array('headline'=>'Story','content'=>'Body','image_id'=>91,'image_alt'=>'Hero'));
$articles = new SingleArticleRenderer(); $hero=$articles->render($article); sm_expect(media_image($hero)->getAttribute('loading')==='eager' && !str_contains($hero,'fetchpriority="high"'), 'Ordinary hero policy failed.');
sm_expect(str_contains($hero,'<figure class="sm-article__hero"><img') && !str_contains($hero,'<figcaption>') && substr_count($hero,'<article')===1 && substr_count($hero,'<h1')===1, 'Hero/article semantics changed.');
$high=$articles->render($article,array('image_priority'=>'high','image_loading'=>'lazy')); sm_expect(media_image($high)->getAttribute('fetchpriority')==='high' && media_image($high)->getAttribute('loading')==='eager', 'Explicit hero priority failed.');
$page=new StaticPageViewModel(array('title'=>'About','image_id'=>91)); $pages=new StaticPageRenderer(); $featured=$pages->render($page,array('show_image'=>true,'image_loading'=>'lazy','image_sizes'=>'50vw'));
sm_expect(media_image($featured)->getAttribute('loading')==='lazy' && media_image($featured)->getAttribute('sizes')==='50vw' && !str_contains($featured,'<article'), 'Static page media forwarding failed.');
$GLOBALS['sm_image']=false; sm_expect(!str_contains($pages->render($page,array('show_image'=>true)),'<figure'), 'Empty media wrapper.'); $GLOBALS['sm_image']=true;
$authors=new AuthorData(); $avatar=$authors->filter_avatar('fallback',7,32,'','',array('fetchpriority'=>'high')); sm_expect($GLOBALS['media_size']===array(32,32) && !media_image($avatar)->hasAttribute('fetchpriority') && AuthorData::META_KEY==='profile_picture', 'Avatar sizing/compatibility failed.');
sm_expect($authors->filter_avatar('fallback',0,32,'','',array())==='fallback', 'Avatar fallback lost.');
function is_feed(): bool { return $GLOBALS['media_feed'] ?? false; }
function get_the_ID(): int { return 20; }
function wp_get_attachment_image_url($id,$size): string { return 'image.jpg'; }
$feeds=new RssFeaturedImage(); sm_expect($feeds->prepend('Body')==='Body','Non-feed mutated.'); $GLOBALS['media_feed']=true;
$feed=$feeds->prepend('Body'); sm_expect($feeds->prepend($feed)===$feed && !media_image($feed)->hasAttribute('fetchpriority') && !media_image($feed)->hasAttribute('loading'),'Feed duplicate/hints regression.');
$missing=new WP_Post(20); $GLOBALS['sm_posts'][20]=$missing; $GLOBALS['sm_meta'][91]['_wp_attachment_image_alt']='';
sm_expect(PostCardViewModel::from_post(20)->data()['image_alt']==='' && SingleArticleViewModel::from_post(20)->data()['image_alt']==='', 'Missing alt fabricated from title.');
echo "Site Master media/CWV static tests passed.\n";
