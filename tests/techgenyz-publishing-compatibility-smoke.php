<?php
/** Development 0.0.9 Techgenyz publishing compatibility tests. */
declare(strict_types=1);

namespace Elementor {
	class Controls_Manager { public const HIDDEN = 'hidden'; }
	class Widget_Base {
		protected array $test_settings = array(); public array $test_controls = array(); public array $test_definitions = array(); public array $test_groups = array(); private static int $sequence = 0; private string $test_id;
		public function __construct( array $data = array(), $args = null ) { $this->test_id = 'instance-' . ++self::$sequence; }
		protected function start_controls_section( $id, $args = array() ): void {}
		protected function add_control( $id, $args = array() ): void { $this->test_controls[] = $id; $this->test_definitions[$id]=array('method'=>'add_control','args'=>$args); }
		protected function add_responsive_control( $id, $args = array() ): void { $this->test_controls[] = $id; $this->test_definitions[$id]=array('method'=>'add_responsive_control','args'=>$args); }
		protected function add_group_control( $type, $args = array() ): void { $this->test_groups[$args['name']] = array('type'=>$type,'args'=>$args); }
		protected function end_controls_section(): void {}
		public function get_id(): string { return $this->test_id; }
		public function get_settings_for_display(): array { return $this->test_settings; }
		public function set_test_settings( array $settings ): void { $this->test_settings = $settings; }
		public function initialize_controls(): void { $this->register_controls(); }
	}
}

namespace {
$mode = $argv[1] ?? 'suite';
if ( 'suite' === $mode ) {
	foreach ( array( 'profile-techgenyz', 'profile-blissz', 'profile-unconfigured', 'runtime' ) as $case ) {
		passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' ' . escapeshellarg( $case ), $code );
		if ( 0 !== $code ) { exit( $code ); }
	}
	echo "Site Master Techgenyz publishing compatibility tests passed.\n"; exit( 0 );
}

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['sm_actions'] = array(); $GLOBALS['sm_filters'] = array(); $GLOBALS['sm_option'] = ''; $GLOBALS['sm_meta'] = array(); $GLOBALS['sm_posts'] = array(); $GLOBALS['sm_terms'] = array(); $GLOBALS['sm_term_meta'] = array(); $GLOBALS['sm_content_filters'] = 0; $GLOBALS['sm_caps'] = array(); $GLOBALS['sm_nonce'] = true;
class WP_Post { public int $ID; public int $post_author = 7; public string $post_type = 'post'; public string $post_excerpt = 'Summary'; public string $post_content = 'Body [shortcode]'; public function __construct(int $id,string $type='post'){ $this->ID=$id;$this->post_type=$type; } }
class WP_User { public int $ID; public string $display_name='Blissz Author'; public function __construct(int $id){$this->ID=$id;} }
class WP_Term { public int $term_id; public string $name; public string $taxonomy; public function __construct(int $id,string $name,string $taxonomy='category'){$this->term_id=$id;$this->name=$name;$this->taxonomy=$taxonomy;} }
class WP_Comment { public int $user_id=0; }
class WP_Error {}
function sm_expect(bool $condition,string $message):void{if(!$condition){throw new RuntimeException($message);}}
function add_action($hook,$callback,$priority=10,$args=1):void{$GLOBALS['sm_actions'][$hook][]=$callback;}
function add_filter($hook,$callback,$priority=10,$args=1):void{$GLOBALS['sm_filters'][$hook][]=$callback;}
function has_filter($hook,$callback=false){return !empty($GLOBALS['sm_filters'][$hook]);}
function apply_filters($hook,$value,...$args){foreach($GLOBALS['sm_filters'][$hook]??array() as $callback){$value=$callback($value,...$args);}return $value;}
function sanitize_key($v):string{return strtolower(preg_replace('/[^a-z0-9_-]/','',(string)$v)??'');}
function sanitize_text_field($v):string{return trim(strip_tags((string)$v));}
function sanitize_html_class($v):string{return preg_replace('/[^A-Za-z0-9_-]/','',(string)$v)??'';}
function absint($v):int{return abs((int)$v);} function esc_attr($v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');} function esc_html($v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');} function esc_url($v):string{return str_starts_with((string)$v,'javascript:')?'':htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function esc_html__($v,$d=null):string{return(string)$v;} function __($v,$d=null):string{return(string)$v;} function wp_unslash($v){return$v;} function is_wp_error($v):bool{return$v instanceof WP_Error;}
function _n($single,$plural,$number,$domain=null):string{return 1===(int)$number?$single:$plural;}
function get_option($key,$default=false){return 'site_master_profile'===$key?$GLOBALS['sm_option']:('date_format'===$key?'F j, Y':('time_format'===$key?'g:i A':$default));}
function get_user_meta($id,$key,$single=false){return$GLOBALS['sm_meta'][(int)$id][$key]??'';} function update_user_meta($id,$key,$value):bool{$GLOBALS['sm_meta'][(int)$id][$key]=$value;return true;} function delete_user_meta($id,$key):bool{unset($GLOBALS['sm_meta'][(int)$id][$key]);return true;}
function get_post($id){return$GLOBALS['sm_posts'][(int)$id]??null;} function get_post_mime_type($id){return isset($GLOBALS['sm_posts'][(int)$id])&&'attachment'===$GLOBALS['sm_posts'][(int)$id]->post_type?'image/jpeg':false;} function get_the_ID():int{return 1;} function get_post_field($field,$id){$p=get_post($id);return$p&&'post_author'===$field?$p->post_author:'';}
function get_userdata($id){return 7===(int)$id?new WP_User(7):false;} function get_user_by($field,$value){return'id'===$field?get_userdata((int)$value):false;} function get_author_posts_url($id):string{return'/author/'.(int)$id.'/';} function is_email($v):bool{return false;} function count_user_posts($id,$type='post',$public=true):int{return 3;}
function current_user_can($cap,...$args):bool{return in_array($cap,$GLOBALS['sm_caps'],true);} function wp_verify_nonce($nonce,$action):bool{return$GLOBALS['sm_nonce'];} function wp_nonce_field($action,$name):void{}
function get_permalink($id):string{return'/story/'.(int)$id.'/';} function get_the_title($id):string{return'Example Story';} function get_post_thumbnail_id($id):int{return 91;} function wp_get_attachment_caption($id):string{return'Caption';}
function get_post_meta($id,$key,$single=false){return$GLOBALS['sm_meta'][(int)$id][$key]??'';} function get_the_terms($id,$taxonomy){return$GLOBALS['sm_terms'][(int)$id][$taxonomy]??array();} function get_term_link($term){return'/category/'.(int)$term->term_id.'/';} function get_queried_object(){return$GLOBALS['sm_queried']??null;} function get_term_meta($id,$key,$single=false){return$GLOBALS['sm_term_meta'][(int)$id][$key]??'';}
function strip_shortcodes($v):string{return preg_replace('/\[[^\]]+\]/','',(string)$v)??'';} function wp_strip_all_tags($v,$breaks=false):string{return trim(strip_tags((string)$v));} function wp_trim_words($v,$n,$more='…'):string{return trim((string)$v);}
function wp_get_attachment_image_src($id,$size){return array('image.jpg',160,160,true);} function get_intermediate_image_sizes():array{return array('thumbnail','medium','medium_large','large');}
function wp_get_attachment_image($id,$size,$icon=false,$attrs=array()):string{if(!isset($GLOBALS['sm_posts'][(int)$id]))return'';$attrs=array_merge(array('src'=>'image.jpg','width'=>'800','height'=>'450','srcset'=>'image.jpg 800w','alt'=>''),$attrs);$s='<img';foreach($attrs as$k=>$v){if(false!==$v)$s.=' '.$k.'="'.esc_attr($v).'"';}return$s.'>';}
function get_avatar($id,$size,$default='',$alt='',$args=array()):string{$attachment=\Abhinava\SiteMaster\Content\AuthorData::attachment_id((int)$id);return$attachment?'<img class="avatar" src="avatar-'.$attachment.'.jpg" width="'.$size.'" height="'.$size.'" alt="'.esc_attr($alt).'">':'<img class="gravatar" alt="">';}
function get_post_datetime($id,$field='date',$source='local'){return new DateTimeImmutable('2026-09-17 10:00:00 +05:30');} function wp_timezone():DateTimeZone{return new DateTimeZone('Asia/Kolkata');} function wp_date($format,$timestamp,$timezone=null):string{return(new DateTimeImmutable('@'.$timestamp))->setTimezone($timezone?:wp_timezone())->format($format);}
function apply_shortcodes($v){return$v;} function wp_kses_post($v):string{return strip_tags((string)$v,'<p><a><strong><em>');} function wpautop($v):string{return'<p>'.(string)$v.'</p>';}
function home_url($path='/'):string{return'https://example.test'.$path;} function get_search_query():string{return'';}
function rank_math_the_breadcrumbs():void{echo'<nav aria-label="Breadcrumb"><a href="/">Home</a></nav>';}
function wp_kses($html,$allowed):string{return strip_tags((string)$html,'<nav><p><span><a>');}
function did_action($hook):int{return 0;} function load_plugin_textdomain(...$args):void{} function dirname_stub($v){return$v;}
function apply_content($value){return$value;}
// the_content is special so adapters can prove exactly-once processing.
$GLOBALS['sm_filters']['the_content'][]=static function($value){++$GLOBALS['sm_content_filters'];return'<p class="filtered">'.$value.'</p>';};

spl_autoload_register(static function($class){$prefix='Abhinava\\SiteMaster\\';if(str_starts_with($class,$prefix)){require_once dirname(__DIR__).'/src/'.str_replace('\\','/',substr($class,strlen($prefix))).'.php';}});

use Abhinava\SiteMaster\Elementor\ElementorIntegration;
use Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz\TechgenyzCompatibility;
use Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz\LegacyControlRegistry;
use Abhinava\SiteMaster\Profiles\ProfileResolver;
use Abhinava\SiteMaster\Content\AuthorData;
if('profile-techgenyz'===$mode){$GLOBALS['sm_option']='techgenyz';(new ElementorIntegration(ProfileResolver::resolve()))->register();$callbacks=$GLOBALS['sm_actions']['elementor/widgets/register']??array();sm_expect($callbacks&&str_contains((string)$callbacks[0][0],'TechgenyzCompatibility'),'Techgenyz adapter hook absent.');echo"PASS profile-techgenyz\n";exit;}
if('profile-blissz'===$mode){$GLOBALS['sm_option']='blissz';(new ElementorIntegration(ProfileResolver::resolve()))->register();$callbacks=$GLOBALS['sm_actions']['elementor/widgets/register']??array();sm_expect($callbacks&&!str_contains((string)$callbacks[0][0],'TechgenyzCompatibility'),'Techgenyz hooks leaked to Blissz.');echo"PASS profile-blissz\n";exit;}
if('profile-unconfigured'===$mode){(new ElementorIntegration(ProfileResolver::resolve()))->register();sm_expect(empty($GLOBALS['sm_actions']),'Compatibility hooks leaked to unconfigured profile.');echo"PASS profile-unconfigured\n";exit;}
$GLOBALS['sm_posts'][1]=new WP_Post(1);$GLOBALS['sm_posts'][78]=new WP_Post(78,'attachment');$GLOBALS['sm_posts'][91]=new WP_Post(91,'attachment');$GLOBALS['sm_terms'][1]['category']=array(new WP_Term(10,'News'),new WP_Term(11,'Tech'));$GLOBALS['sm_terms'][1]['post_tag']=array();$GLOBALS['sm_meta'][7]['profile_picture']=78;$GLOBALS['sm_meta'][7]['tbm_profile_picture']=91;sm_expect(78===AuthorData::attachment_id(7),'Techgenyz must use profile_picture without Blissz precedence.');
$GLOBALS['sm_caps']=array('edit_user');$_POST=array('site_master_profile_picture_nonce'=>'ok','site_master_profile_picture_id'=>'78');sm_expect((new AuthorData())->save_profile_field(7),'Techgenyz avatar write failed.');sm_expect(91===$GLOBALS['sm_meta'][7]['tbm_profile_picture'],'Techgenyz write touched Blissz key.');$_POST['site_master_profile_picture_id']='0';sm_expect((new AuthorData())->save_profile_field(7)&&!isset($GLOBALS['sm_meta'][7]['profile_picture'])&&91===$GLOBALS['sm_meta'][7]['tbm_profile_picture'],'Techgenyz removal crossed profile keys.');$GLOBALS['sm_meta'][7]['profile_picture']=78;
class SM_TGM_Manager{public array $widgets=array();public function register($w){$this->widgets[$w->get_name()]=$w;}}$manager=new SM_TGM_Manager();TechgenyzCompatibility::register_widgets($manager);$ids=array('post_terms','tgm-date-widget','tgm-post-author-widget','tgm-post-content','tgm_post_search');sm_expect($ids===array_keys($manager->widgets),'Techgenyz widget IDs changed.');
$fixture=json_decode((string)file_get_contents(__DIR__.'/fixtures/techgenyz-elementor-controls.json'),true,512,JSON_THROW_ON_ERROR);foreach($ids as$id){$w=$manager->widgets[$id];$w->initialize_controls();$r=LegacyControlRegistry::widget($id);sm_expect($fixture['widgets'][$id]['control_ids']===array_keys($r['controls'])&&$fixture['widgets'][$id]['control_ids']===$w->test_controls,'Control inventory mismatch '.$id);sm_expect(count($fixture['widgets'][$id]['group_control_names'])===count($w->test_groups),'Group inventory mismatch '.$id);foreach($r['controls']as$cid=>$d){$actual=$w->test_definitions[$cid];sm_expect((!empty($d['responsive'])?'add_responsive_control':'add_control')===$actual['method'],'Registration type mismatch '.$id.'/'.$cid);if($d['selectors'])sm_expect(str_contains((string)array_key_first($actual['args']['selectors']),'{{WRAPPER}}'),'Selector translation missing '.$id.'/'.$cid);if($d['has_default'])sm_expect(array_key_exists('default',$actual['args'])&&$d['default']===$actual['args']['default'],'Default mismatch '.$id.'/'.$cid);if($d['has_options'])sm_expect($d['options']===$actual['args']['options'],'Options mismatch '.$id.'/'.$cid);if($d['has_condition'])sm_expect($d['condition']===$actual['args']['condition'],'Condition mismatch '.$id.'/'.$cid);if($d['has_conditions'])sm_expect($d['conditions']===$actual['args']['conditions'],'Conditions mismatch '.$id.'/'.$cid);if(in_array($d['status'],array('INTENTIONALLY_INACTIVE','PERSISTENCE_ONLY'),true))sm_expect(empty($d['selectors'])&&empty($actual['args']['selectors']),'Inactive selector registered '.$id.'/'.$cid);}}
$counts=LegacyControlRegistry::counts();sm_expect(178===$counts['direct']&&93===$counts['responsive']&&65===$counts['group'],'Registry counts changed.');
$content_registry=LegacyControlRegistry::widget('tgm-post-content');foreach(array('related_post_style_alignment','related_post_style_background_color','related_post_style_border_radius','related_post_style_margin','related_post_style_padding','related_post_style_row_gap','related_post_style_column_gap')as$cid){sm_expect('INTENTIONALLY_INACTIVE'===$content_registry['controls'][$cid]['status']&&empty($content_registry['controls'][$cid]['selectors']),'Related style leaked: '.$cid);}foreach(array('related_post_style_border','related_post_style_box_shadow')as$gid){sm_expect('INTENTIONALLY_INACTIVE'===$content_registry['groups'][$gid]['status']&&empty($manager->widgets['tgm-post-content']->test_groups[$gid]['args']['selector']),'Related group selector leaked: '.$gid);}
$search_registry=LegacyControlRegistry::widget('tgm_post_search');foreach(array('icon_color','icon_background_color','icon_size','icon_padding','icon_border_radius')as$cid){sm_expect('PERSISTENCE_ONLY'===$search_registry['controls'][$cid]['status']&&empty($search_registry['controls'][$cid]['selectors'])&&empty($manager->widgets['tgm_post_search']->test_definitions[$cid]['args']['selectors']),'Search icon style leaked: '.$cid);}
foreach(array('related_post_alternate_templates','advertisements')as$cid){$definition=$content_registry['controls'][$cid];$registered=$manager->widgets['tgm-post-content']->test_definitions[$cid]['args'];$row=$fixture['inactive_repeater_rows'][$cid][0];sm_expect('repeater'===$definition['type']&&array_keys($definition['repeater_subcontrols'])===array_keys($registered['fields'])&&array_keys($row)===array_values(array_filter(array_keys($definition['repeater_subcontrols']),static fn($key)=>array_key_exists('default',$definition['repeater_subcontrols'][$key]))),'Inactive repeater structure changed: '.$cid);}
$date_definition=LegacyControlRegistry::widget('tgm-date-widget')['controls']['absolute_format_preset'];sm_expect('india_apac'===$date_definition['default']&&isset($date_definition['options']['iso_24h']),'Select defaults/options missing.');$author_condition=LegacyControlRegistry::widget('tgm-post-author-widget')['controls']['name_prefix']['condition'];sm_expect(array('show_name'=>'yes')===$author_condition,'Source condition missing.');
$terms=$manager->widgets['post_terms']->render_compatibility(array('taxonomy'=>'category','exclude_terms'=>array(11),'link_terms'=>'yes','separator'=>' | '),1);sm_expect(str_contains($terms,'News')&&!str_contains($terms,'Tech')&&str_contains($terms,'term-item'),'Terms adapter failed.');
$date=$manager->widgets['tgm-date-widget']->render_compatibility(array('absolute_format_preset'=>'iso_24h','date_time_view'=>'date_only'),1);sm_expect(str_contains($date,'2026-09-17')&&!isset($GLOBALS['sm_actions']['wp_ajax_tgm_get_local_post_date']),'Date adapter/AJAX boundary failed.');
$author=$manager->widgets['tgm-post-author-widget']->render_compatibility(array('show_avatar'=>'yes','show_name'=>'yes'),1);sm_expect(str_contains($author,'avatar-78.jpg')&&str_contains($author,'rel="author"')&&!str_contains($author,'Person'),'Author adapter failed.');
$content=$manager->widgets['tgm-post-content']->render_compatibility(1);sm_expect(1===$GLOBALS['sm_content_filters']&&str_contains($content,'Body [shortcode]'),'Content one-pass behavior failed.');
$search=$manager->widgets['tgm_post_search']->render_compatibility(array('placeholder'=>'<b>Find</b>','aria_label'=>'Search " site'),'<script>x</script>');$search2=(new \Abhinava\SiteMaster\Elementor\Compatibility\Techgenyz\PostSearchWidget())->render_compatibility(array('aria_label'=>'Second'),'');preg_match('/id="([^"]+)"/',$search,$a);preg_match('/id="([^"]+)"/',$search2,$b);sm_expect(($a[1]??'')!==($b[1]??'')&&str_contains($search,'for="'.($a[1]??'').'"')&&!str_contains($search,'<script>'),'Search accessibility/sanitization failed.');
foreach(array($terms,$date,$author,$content,$search)as$out)sm_expect(!preg_match('/itemscope|itemtype|itemprop|BlogPosting|NewsArticle|Person|application\/ld\+json/',$out),'Prohibited schema output.');
echo"PASS runtime\n";
}
