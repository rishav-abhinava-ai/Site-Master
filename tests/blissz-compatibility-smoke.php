<?php
/** Development 0.0.8 Blissz source-fixture and compatibility tests. */
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
	foreach ( array( 'profile-blissz', 'profile-techgenyz', 'profile-unconfigured', 'runtime' ) as $case ) {
		passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' ' . escapeshellarg( $case ), $code );
		if ( 0 !== $code ) { exit( $code ); }
	}
	echo "Site Master Blissz compatibility tests passed.\n"; exit( 0 );
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

use Abhinava\SiteMaster\Elementor\ElementorIntegration; use Abhinava\SiteMaster\Elementor\Compatibility\Blissz\BlisszCompatibility; use Abhinava\SiteMaster\Elementor\Compatibility\Blissz\LegacyControlInventory; use Abhinava\SiteMaster\Elementor\Compatibility\Blissz\LegacyControlRegistry; use Abhinava\SiteMaster\Profiles\ProfileResolver; use Abhinava\SiteMaster\Content\AuthorData;
if('profile-blissz'===$mode){$GLOBALS['sm_option']='blissz';$integration=new ElementorIntegration(ProfileResolver::resolve());$integration->register();sm_expect(isset($GLOBALS['sm_actions']['elementor/widgets/register']),'Blissz widget hook absent.');sm_expect(isset($GLOBALS['sm_actions']['elementor/elements/categories_registered']),'Blissz category hook absent.');echo"PASS profile-blissz\n";exit;}
if('profile-techgenyz'===$mode){$GLOBALS['sm_option']='techgenyz';(new ElementorIntegration(ProfileResolver::resolve()))->register();sm_expect(empty($GLOBALS['sm_actions']),'Blissz hooks leaked to Techgenyz.');echo"PASS profile-techgenyz\n";exit;}
if('profile-unconfigured'===$mode){(new ElementorIntegration(ProfileResolver::resolve()))->register();sm_expect(empty($GLOBALS['sm_actions']),'Blissz hooks leaked to unconfigured profile.');echo"PASS profile-unconfigured\n";exit;}

$GLOBALS['sm_posts'][1]=new WP_Post(1);$GLOBALS['sm_posts'][77]=new WP_Post(77,'attachment');$GLOBALS['sm_posts'][78]=new WP_Post(78,'attachment');$GLOBALS['sm_posts'][91]=new WP_Post(91,'attachment');$GLOBALS['sm_terms'][1]['category']=array(new WP_Term(10,'News'));$GLOBALS['sm_terms'][1]['post_tag']=array();$GLOBALS['sm_meta'][1]['rank_math_primary_category']=10;$GLOBALS['sm_meta'][91]['_wp_attachment_image_alt']='';
$compat=new BlisszCompatibility();$compat->register();$GLOBALS['sm_meta'][7]['tbm_profile_picture']=77;$GLOBALS['sm_meta'][7]['profile_picture']=78;sm_expect(77===AuthorData::attachment_id(7),'Blissz legacy avatar did not win coexistence.');unset($GLOBALS['sm_meta'][7]['tbm_profile_picture']);sm_expect(78===AuthorData::attachment_id(7),'Shared avatar fallback failed.');$GLOBALS['sm_meta'][7]['tbm_profile_picture']=77;
$GLOBALS['sm_caps']=array('edit_user');$_POST=array('site_master_profile_picture_nonce'=>'ok','site_master_profile_picture_id'=>'77');sm_expect((new AuthorData())->save_profile_field(7),'Blissz avatar write failed.');sm_expect(77===$GLOBALS['sm_meta'][7]['tbm_profile_picture']&&78===$GLOBALS['sm_meta'][7]['profile_picture'],'Blissz write synchronized or damaged the fallback key.');
$_POST['site_master_profile_picture_id']='0';sm_expect((new AuthorData())->save_profile_field(7),'Blissz avatar removal failed.');sm_expect(!isset($GLOBALS['sm_meta'][7]['tbm_profile_picture'])&&78===$GLOBALS['sm_meta'][7]['profile_picture'],'Blissz removal touched the fallback key.');$GLOBALS['sm_meta'][7]['tbm_profile_picture']=78;
class SM_Manager{public array $widgets=array();public function register($w){$this->widgets[$w->get_name()]=$w;}} $manager=new SM_Manager();BlisszCompatibility::register_widgets($manager);$ids=array('tbm_breadcrumb','tbm-date-widget','tbm-post-author-widget','tbm-post-content','tbm_post_search','tbz_loop_post_block','blissz_loop_category_block','tbz_loop_single_post_block');sm_expect($ids===array_keys($manager->widgets),'Legacy widget IDs/registration order changed.');
$fixture=json_decode((string)file_get_contents(__DIR__.'/fixtures/blissz-elementor-controls.json'),true,512,JSON_THROW_ON_ERROR);foreach($ids as$id){$widget=$manager->widgets[$id];$widget->initialize_controls();sm_expect($fixture['widgets'][$id]['control_ids']===LegacyControlInventory::controls($id),'Fixture/runtime controls differ for '.$id);sm_expect($fixture['widgets'][$id]['control_ids']===$widget->test_controls,'Adapter did not recognize all saved controls for '.$id);$registered=LegacyControlRegistry::widget($id);sm_expect(count($fixture['widgets'][$id]['group_control_names'])===count($widget->test_groups),'Group controls not registered for '.$id);foreach($registered['controls'] as$control_id=>$definition){$actual=$widget->test_definitions[$control_id];sm_expect((!empty($definition['responsive'])?'add_responsive_control':'add_control')===$actual['method'],'Control method mismatch: '.$id.'/'.$control_id);if(!empty($definition['selectors'])){sm_expect(!empty($actual['args']['selectors'])&&str_contains((string)array_key_first($actual['args']['selectors']),'{{WRAPPER}}'),'Selector translation missing: '.$id.'/'.$control_id);}}}
$counts=LegacyControlRegistry::counts();sm_expect(408===$counts['direct']&&65===$counts['group']&&$counts['responsive']>0&&$counts['style']>0,'Structured registry counts invalid.');
$breadcrumb=$manager->widgets['tbm_breadcrumb']->render_compatibility();sm_expect(str_contains($breadcrumb,'Breadcrumb')&&!preg_match('/itemscope|itemtype|itemprop/',$breadcrumb),'Breadcrumb adapter failed.');
$date=$manager->widgets['tbm-date-widget']->render_compatibility(array('absolute_format_preset'=>'iso_24h','date_time_view'=>'date_only'),1);sm_expect(str_contains($date,'2026-09-17')&&str_contains($date,'<time '),'Date adapter failed.');sm_expect(!isset($GLOBALS['sm_actions']['wp_ajax_tbm_get_local_post_date'])&&!isset($GLOBALS['sm_actions']['wp_ajax_nopriv_tbm_get_local_post_date']),'Legacy date AJAX restored.');
$author=$manager->widgets['tbm-post-author-widget']->render_compatibility(array('show_avatar'=>'yes','show_name'=>'yes'),1);sm_expect(str_contains($author,'avatar-78.jpg')&&str_contains($author,'rel="author"')&&!str_contains($author,'Person'),'Author adapter failed.');
$content=$manager->widgets['tbm-post-content']->render_compatibility(1);sm_expect(1===$GLOBALS['sm_content_filters']&&str_contains($content,'Body [shortcode]'),'Content filter count/preservation failed.');
$search=$manager->widgets['tbm_post_search']->render_compatibility(array('placeholder'=>'<b>Find</b>','aria_label'=>'Search " site','show_submit_button'=>'yes','submit_button_text'=>'Go'),'<script>x</script>');sm_expect(str_contains($search,'method="get"')&&str_contains($search,'name="s"')&&!str_contains($search,'<script>')&&str_contains($search,'&quot;'),'Search form sanitization failed.');
$search_two=(new \Abhinava\SiteMaster\Elementor\Compatibility\Blissz\PostSearchWidget())->render_compatibility(array('aria_label'=>'Second'), '');preg_match('/id="([^"]+)"/',$search,$search_id_one);preg_match('/id="([^"]+)"/',$search_two,$search_id_two);sm_expect(($search_id_one[1]??'')!==($search_id_two[1]??'')&&str_contains($search,'for="'.($search_id_one[1]??'').'"')&&str_contains($search_two,'for="'.($search_id_two[1]??'').'"'),'Search IDs/labels are not instance-unique.');
$card=$manager->widgets['tbz_loop_post_block']->render_compatibility(array('show_image'=>'yes','show_category'=>'yes','show_title'=>'yes','title_tag'=>'h1','show_excerpt'=>'yes','show_author'=>'yes','show_date'=>'yes'),1);sm_expect(str_contains($card,'<article class="sm-post-card tbz-lpb">')&&str_contains($card,'<h3')&&str_contains($card,'aria-label="Example Story"'),'Loop Post shared semantics failed.');
$card_controls=$manager->widgets['tbz_loop_post_block']->render_compatibility(array('show_image'=>'no','show_category'=>'no','show_title'=>'no','show_excerpt'=>'no','show_author'=>'no','show_date'=>'no','show_reading_time'=>'yes','show_button'=>'yes','button_text'=>'Read now'),1);sm_expect(!str_contains($card_controls,'sm-post-card__title')&&str_contains($card_controls,'sm-post-card__reading-time')&&str_contains($card_controls,'sm-post-card__button'),'Loop Post functional controls failed.');
$term=new WP_Term(10,'Lifestyle');$GLOBALS['sm_term_meta'][10]['tbz_lpb_cat_image_id']=91;$category=$manager->widgets['blissz_loop_category_block']->render_compatibility(array('show_image'=>'yes','image_link'=>'category','show_badge'=>'yes','link_category'=>'yes'),$term);sm_expect(str_contains($category,'class="blcb"')&&str_contains($category,'aria-label="Lifestyle"'),'Loop Category adapter failed.');
$single=$manager->widgets['tbz_loop_single_post_block']->render_compatibility(array('enable_schema'=>'yes','show_image'=>'yes','show_category'=>'yes','show_excerpt'=>'yes','show_author'=>'yes','show_date'=>'yes'),1);sm_expect(1===substr_count($single,'<h1')&&!str_contains($single,'<article')&&!preg_match('/itemscope|itemtype|itemprop|BlogPosting|Person|application\/ld\+json/',$single),'Single Post fragment normalization failed.');
$composition='<article class="elementor-parent">'.$single.$content.'</article>';sm_expect(1===substr_count($composition,'<article')&&1===substr_count($composition,'<h1'),'Native parent article composition failed.');
foreach(array($breadcrumb,$date,$author,$content,$search,$card,$category,$single)as$output){sm_expect(!preg_match('/itemscope|itemtype|itemprop|BlogPosting|Person|application\/ld\+json/',$output),'Prohibited schema output.');}
echo"PASS runtime\n";
}
