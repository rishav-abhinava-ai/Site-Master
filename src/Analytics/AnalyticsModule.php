<?php
/** Shared profile-aware Content Analytics runtime. @package SiteMaster */
namespace Abhinava\SiteMaster\Analytics;
use Abhinava\SiteMaster\Profiles\ProfileInterface;
defined('ABSPATH')||exit;
final class AnalyticsModule{
	public const CRON='site_master_analytics_worker';private string $profile;
	public function __construct(ProfileInterface $profile){$this->profile=$profile->key();}
	public function register():void{(new RestCollector($this->profile))->register();(new FrontendBeacon($this->profile))->register();(new LegacyAdapter($this->profile))->register();add_filter('cron_schedules',array($this,'schedule'));add_action('init',array($this,'ensure_scheduled'));add_action(self::CRON,array($this,'work'));if(is_admin()){add_action('admin_init',array($this,'maybe_upgrade'));(new AnalyticsAdmin($this->profile))->register();}}
	public function schedule(array $s):array{$s['site_master_five_minutes']=array('interval'=>300,'display'=>__('Every five minutes','site-master'));return$s;}
	public function ensure_scheduled():void{if(!wp_next_scheduled(self::CRON))wp_schedule_event(time()+300,'site_master_five_minutes',self::CRON);}
	public function maybe_upgrade():void{if(Schema::VERSION!==(string)get_option(Schema::OPTION,''))Schema::install();}
	public function work():void{if(!Config::enabled($this->profile))return;(new BaselineImporter())->run($this->profile);$a=new Aggregator();$a->run($this->profile);$a->finalize_and_prune($this->profile);}
	public static function activate():void{Schema::install();add_filter('cron_schedules',static function(array $s):array{$s['site_master_five_minutes']=array('interval'=>300,'display'=>'Every five minutes');return$s;});if(!wp_next_scheduled(self::CRON))wp_schedule_event(time()+300,'site_master_five_minutes',self::CRON);}
	public static function deactivate():void{$timestamp=wp_next_scheduled(self::CRON);while($timestamp){wp_unschedule_event($timestamp,self::CRON);$timestamp=wp_next_scheduled(self::CRON);}}
}
