<?php
/** Techgenyz tgm_count_post_view compatibility adapter. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
defined( 'ABSPATH' ) || exit;
final class LegacyAdapter {
	private string $profile;
	public function __construct( string $profile ) { $this->profile=$profile; }
	public function register(): void { if('techgenyz'!==$this->profile)return; add_action('wp_ajax_nopriv_tgm_count_post_view',array($this,'handle'));add_action('wp_ajax_tgm_count_post_view',array($this,'handle')); }
	public function handle(): void {
		check_ajax_referer('tgm_count_post_view','nonce');
		$post_id=isset($_POST['post_id'])?absint($_POST['post_id']):0;
		if(is_user_logged_in()){wp_send_json_success(array('counted'=>false,'views'=>0));}
		if(!Eligibility::post($post_id)){wp_send_json_error(array('message'=>'Invalid post.'),400);}
		$result=(new Collector())->collect(array('post_id'=>$post_id),$_SERVER,$this->profile,'legacy',false);
		$lifetime=(new Reporting($this->profile))->lifetime($post_id)['lifetime'];
		wp_send_json_success(array('counted'=>'accepted'===$result['status'],'views'=>$lifetime+('accepted'===$result['status']?1:0)));
	}
}
