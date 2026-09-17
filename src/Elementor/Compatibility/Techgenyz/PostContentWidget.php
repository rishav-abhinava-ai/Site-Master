<?php
/** Techgenyz one-pass post content adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz;use Abhinava\SiteMaster\Components\Article\ArticleBodyRenderer;
defined('ABSPATH')||exit;
final class PostContentWidget extends LegacyWidgetBase {
	public function get_name():string{return'tgm-post-content';}public function get_title():string{return esc_html__('TGM Post Content','site-master');}public function get_icon():string{return'eicon-post-content';}
	public function render_compatibility(int$post_id=0):string{$post_id=$post_id?:self::post_id();$post=$post_id?get_post($post_id):null;if(!$post instanceof \WP_Post)return'';$body=(new ArticleBodyRenderer())->render((string)$post->post_content);return''!==$body?'<div class="tgm-post-content"><div class="tgm-post-content__body">'.$body.'</div></div>':'';}
	protected function render():void{echo$this->render_compatibility();}
}
