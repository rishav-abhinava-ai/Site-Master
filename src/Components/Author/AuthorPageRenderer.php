<?php
/** Shared author-profile/archive page composition. @package SiteMaster */

namespace Abhinava\SiteMaster\Components\Author;

use Abhinava\SiteMaster\Components\Breadcrumbs\BreadcrumbRenderer;
use Abhinava\SiteMaster\Components\Layout\PageShellRenderer;
use Abhinava\SiteMaster\Components\Listing\PostListRenderer;
use Abhinava\SiteMaster\Content\EditorialQuery;

defined( 'ABSPATH' ) || exit;

final class AuthorPageRenderer {
	private EditorialQuery $queries;
	private PostListRenderer $posts;
	private PageShellRenderer $shell;
	private BreadcrumbRenderer $breadcrumbs;

	public function __construct( ?EditorialQuery $queries = null, ?PostListRenderer $posts = null, ?PageShellRenderer $shell = null, ?BreadcrumbRenderer $breadcrumbs = null ) {
		$this->queries     = $queries ?? new EditorialQuery();
		$this->posts       = $posts ?? new PostListRenderer();
		$this->shell       = $shell ?? new PageShellRenderer();
		$this->breadcrumbs = $breadcrumbs ?? new BreadcrumbRenderer();
	}

	/** @param array<string,mixed> $options */
	public function render( AuthorPageViewModel $view_model, array $options = array() ): string {
		$data = $view_model->data();
		if ( (int) $data['id'] <= 0 || '' === trim( (string) $data['display_name'] ) ) {
			return '';
		}

		$heading_id = 'sm-author-heading-' . (int) $data['id'];
		$profile    = '';
		if ( ! array_key_exists( 'show_avatar', $options ) || (bool) $options['show_avatar'] ) {
			$avatar = get_avatar( (int) $data['id'], 160, '', '', array( 'class' => array( 'sm-author-profile__avatar' ) ) );
			$profile = is_string( $avatar ) ? $avatar : '';
		}
		$profile .= '<h1 id="' . esc_attr( $heading_id ) . '">' . esc_html( (string) $data['display_name'] ) . '</h1>';
		if ( ! array_key_exists( 'show_bio', $options ) || (bool) $options['show_bio'] ) {
			$bio = trim( (string) $data['bio'] );
			if ( '' !== $bio ) {
				$profile .= '<div class="sm-author-profile__bio">' . wp_kses_post( wpautop( $bio ) ) . '</div>';
			}
		}
		$profile = '<section class="sm-author-profile" aria-labelledby="' . esc_attr( $heading_id ) . '">' . $profile . '</section>';

		$paginated = ! array_key_exists( 'paginated', $options ) || (bool) $options['paginated'];
		$query = $this->queries->query(
			array(
				'author'         => (int) $data['id'],
				'posts_per_page' => absint( $options['posts_per_page'] ?? 10 ),
				'paginated'      => $paginated,
				'paged'          => absint( $options['paged'] ?? 1 ),
			)
		);
		$posts_heading    = sprintf( __( 'Articles by %s', 'site-master' ), (string) $data['display_name'] );
		$posts_heading_id = 'sm-author-posts-heading-' . (int) $data['id'];
		$post_output = $this->posts->render_query(
			$query,
			array(
				'heading'         => $posts_heading,
				'heading_id'      => $posts_heading_id,
				'heading_level'   => 'h2',
				'classes'         => array( 'sm-author-posts' ),
				'context'         => 'homepage',
				'card'            => array( 'heading_level' => 'h3' ),
				'paginated'       => $paginated,
				'excerpt_length'  => max( 1, min( 100, absint( $options['excerpt_length'] ?? 24 ) ) ),
				'pagination_base' => is_string( $options['pagination_base'] ?? null ) ? $options['pagination_base'] : '',
			)
		);
		if ( '' === $post_output ) {
			$empty = is_string( $options['empty_message'] ?? null ) && '' !== trim( $options['empty_message'] ) ? $options['empty_message'] : __( 'No published articles were found.', 'site-master' );
			$post_output = '<section class="sm-author-posts" aria-labelledby="' . esc_attr( $posts_heading_id ) . '"><h2 id="' . esc_attr( $posts_heading_id ) . '">' . esc_html( $posts_heading ) . '</h2><p class="sm-author-posts__empty">' . esc_html( $empty ) . '</p></section>';
		}

		return $this->shell->render(
			$profile . $post_output,
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
