<?php
/** Techgenyz author adapter using profile_picture through AuthorData. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz;use Abhinava\SiteMaster\Content\AuthorData;
defined('ABSPATH')||exit;
final class AuthorWidget extends LegacyWidgetBase {
	public function get_name():string{return'tgm-post-author-widget';}public function get_title():string{return esc_html__('TGM Post Author','site-master');}public function get_icon():string{return'eicon-person';}
	public function render_compatibility(?array$s=null,int$post_id=0):string{$s=$s??$this->settings();$post_id=$post_id?:self::post_id();$id=absint($s['author_id_override']??0);if(!$id&&$post_id)$id=(int)get_post_field('post_author',$post_id);$d=$id?AuthorData::get($id,array(160,160)):null;if(!$d)return'';$p=array();if(self::on($s,'show_avatar',true)){$a=get_avatar($id,160,'','',array('class'=>array('tgm-post-author-avatar')));if(is_string($a))$p[]=$a;}if(self::on($s,'show_name',true)){$prefix=sanitize_text_field((string)($s['name_prefix']??''));$name=(''!==$prefix?esc_html($prefix).' ':'').esc_html((string)$d['display_name']);$p[]='<a class="tgm-post-author-name" href="'.esc_url((string)$d['url']).'" rel="author">'.$name.'</a>';}if(self::on($s,'show_bio',false)&&''!==trim((string)$d['bio']))$p[]='<div class="tgm-post-author-bio">'.wp_kses_post(wpautop((string)$d['bio'])).'</div>';if(self::on($s,'show_post_count',false))$p[]='<span class="tgm-post-author-post-count">'.esc_html((string)count_user_posts($id,'post',true)).'</span>';return'<div class="tgm-post-author-widget">'.implode('',$p).'</div>';}
	protected function render():void{echo$this->render_compatibility();}
}
