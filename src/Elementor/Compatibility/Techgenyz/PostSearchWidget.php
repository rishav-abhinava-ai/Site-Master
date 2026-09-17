<?php
/** Techgenyz accessible native search adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz;
defined('ABSPATH')||exit;
final class PostSearchWidget extends LegacyWidgetBase {
	public function get_name():string{return'tgm_post_search';}public function get_title():string{return esc_html__('TGM Post Search','site-master');}public function get_icon():string{return'eicon-search';}
	public function render_compatibility(?array$s=null,string$query=''):string{$s=$s??$this->settings();$placeholder=sanitize_text_field((string)($s['placeholder']??__('Search','site-master')));$label=sanitize_text_field((string)($s['aria_label']??__('Search','site-master')));$id=$this->compatibility_id('tgm-post-search-field');$query=''!==$query?$query:(function_exists('get_search_query')?get_search_query():'');$button=self::on($s,'show_submit_button',false)?'<button type="submit">'.esc_html((string)($s['submit_button_text']??__('Search','site-master'))).'</button>':'';return'<form class="tgm-post-search" role="search" method="get" action="'.esc_url(home_url('/')).'"><label class="screen-reader-text" for="'.esc_attr($id).'">'.esc_html($label).'</label><input id="'.esc_attr($id).'" type="search" name="s" value="'.esc_attr(sanitize_text_field($query)).'" placeholder="'.esc_attr($placeholder).'">'.$button.'</form>';}
	protected function render():void{echo$this->render_compatibility();}
}
