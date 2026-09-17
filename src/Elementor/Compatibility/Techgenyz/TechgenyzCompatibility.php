<?php
/** Techgenyz editorial publishing compatibility registration. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz;
defined( 'ABSPATH' ) || exit;
final class TechgenyzCompatibility {
	public static function register_category($manager):void{if(is_object($manager)&&method_exists($manager,'add_category')){$manager->add_category('techgenyz',array('title'=>esc_html__('Techgenyz','site-master'),'icon'=>'fa fa-plug'));}}
	public static function register_widgets($manager):void{if(!class_exists('\\Elementor\\Widget_Base')||!is_object($manager)||!method_exists($manager,'register'))return;foreach(array(PostTermsWidget::class,DateWidget::class,AuthorWidget::class,PostContentWidget::class,PostSearchWidget::class)as$class){$manager->register(new$class());}}
}
