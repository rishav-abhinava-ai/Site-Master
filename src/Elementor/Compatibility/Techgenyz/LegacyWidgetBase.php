<?php
/** Base for Techgenyz publishing compatibility widgets. @package SiteMaster */
namespace Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz;
defined( 'ABSPATH' ) || exit;
abstract class LegacyWidgetBase extends \Elementor\Widget_Base {
	public function get_categories(): array { return array( 'techgenyz' ); }
	public function get_keywords(): array { return array( 'techgenyz', 'publishing', 'compatibility' ); }
	protected function register_controls(): void { $this->start_controls_section('site_master_legacy_compatibility',array('label'=>esc_html__('Legacy compatibility','site-master')));$r=LegacyControlRegistry::widget($this->get_name());foreach((array)$r['controls']as$id=>$d){$a=array('type'=>'PERSISTENCE_ONLY'===$d['status']?\Elementor\Controls_Manager::HIDDEN:(string)$d['type']);if(!empty($d['has_default'])){$a['default']=$d['default'];}if(!empty($d['has_options'])){$a['options']=$d['options'];}if(!empty($d['has_condition'])){$a['condition']=$d['condition'];}if(!empty($d['has_conditions'])){$a['conditions']=$d['conditions'];}if(!empty($d['repeater_subcontrols'])){$a['fields']=$d['repeater_subcontrols'];}if(isset($d['title_field'])){$a['title_field']=$d['title_field'];}if(!empty($d['selectors'])){$a['selectors']=array();foreach($d['selectors']as$s=>$v){$a['selectors']['{{WRAPPER}} '.$s]=$v;}}if(!empty($d['responsive'])){$this->add_responsive_control($id,$a);}else{$this->add_control($id,$a);}}foreach((array)$r['groups']as$id=>$d){$a=array('name'=>$id);if('SUPPORTED'===$d['status']&&!empty($d['selector'])){$a['selector']='{{WRAPPER}} '.$d['selector'];}$this->add_group_control((string)$d['type'],$a);}$this->end_controls_section(); }
	protected function settings(): array { $s=$this->get_settings_for_display();return is_array($s)?$s:array(); }
	protected static function on(array$s,string$k,bool$d=false):bool{return array_key_exists($k,$s)?'yes'===(string)$s[$k]:$d;}
	protected static function post_id():int{return absint(get_the_ID());}
	protected function compatibility_id(string$p):string{$id=method_exists($this,'get_id')?sanitize_html_class((string)$this->get_id()):'';return sanitize_html_class($p.(''!==$id?'-'.$id:''));}
}
