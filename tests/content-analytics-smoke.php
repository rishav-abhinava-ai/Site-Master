<?php
/** Development 0.1.0 Content Analytics source/privacy regression suite. */
declare(strict_types=1);
define('ABSPATH',__DIR__.'/');
function sm_expect(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function wp_salt($scheme='auth'):string{return'test-secret-'.$scheme;}
function sanitize_text_field($value):string{return trim(strip_tags((string)$value));}
function wp_parse_url($url,$component=-1){return parse_url((string)$url,$component);}
function absint($value):int{return abs((int)$value);}
require_once dirname(__DIR__).'/src/Analytics/Config.php';
require_once dirname(__DIR__).'/src/Analytics/Privacy.php';
require_once dirname(__DIR__).'/src/Analytics/BaselineImporter.php';
require_once dirname(__DIR__).'/src/Analytics/Reporting.php';
require_once dirname(__DIR__).'/src/Analytics/Aggregator.php';
use Abhinava\SiteMaster\Analytics\Aggregator;
use Abhinava\SiteMaster\Analytics\BaselineImporter;
use Abhinava\SiteMaster\Analytics\Config;
use Abhinava\SiteMaster\Analytics\Privacy;
use Abhinava\SiteMaster\Analytics\Reporting;

sm_expect(true===Config::defaults('techgenyz')['enabled'],'Techgenyz default must be enabled.');
sm_expect(false===Config::defaults('blissz')['enabled'],'Blissz default must be disabled.');
sm_expect(false===Config::defaults('unconfigured')['enabled'],'Unconfigured default must be disabled.');
sm_expect('post_views_count'===BaselineImporter::LEGACY_KEY,'Verified legacy baseline key changed.');
foreach(array(array('12',12),array(0,0),array(-8,0),array('bad',0),array(null,0))as$case){sm_expect($case[1]===BaselineImporter::normalize_count($case[0]),'Baseline normalization failed.');}
$before_midnight=strtotime('2026-09-17 23:59 UTC');
$after_midnight=strtotime('2026-09-18 00:01 UTC');
$same_day_a=Privacy::hashes('techgenyz',7,'203.0.113.4','Browser/1',$before_midnight,'2026-09-18');
$same_day_b=Privacy::hashes('techgenyz',7,'203.0.113.4','Browser/1',$after_midnight,'2026-09-18');
sm_expect($same_day_a['reader']===$same_day_b['reader'],'UTC midnight rotated a reader inside one report day.');
$kolkata_before=Privacy::hashes('techgenyz',7,'203.0.113.4','Browser/1',strtotime('2026-09-17 18:29 UTC'),'2026-09-17');
$kolkata_after=Privacy::hashes('techgenyz',7,'203.0.113.4','Browser/1',strtotime('2026-09-17 18:31 UTC'),'2026-09-18');
sm_expect($kolkata_before['reader']!==$kolkata_after['reader'],'Reader did not rotate at the report-day boundary.');
$stable=Privacy::hashes('techgenyz',7,'203.0.113.4','Browser/1',strtotime('2026-09-18 12:00 UTC'),'2026-09-18');
$different_ip=Privacy::hashes('techgenyz',7,'203.0.113.5','Browser/1',strtotime('2026-09-18 12:00 UTC'),'2026-09-18');
$different_ua=Privacy::hashes('techgenyz',7,'203.0.113.4','Other/2',strtotime('2026-09-18 12:00 UTC'),'2026-09-18');
sm_expect($same_day_a['reader']===$stable['reader'],'Same identity/report day did not remain stable.');
sm_expect($stable['reader']!==$different_ip['reader']&&$stable['reader']!==$different_ua['reader'],'Different reader identity shared a hash.');
sm_expect(16===strlen($stable['reader'])&&16===strlen($stable['dedupe']),'Hashes must remain binary 128-bit values.');
$dedupe_timestamp=strtotime('2026-09-17 23:59 UTC');
$dedupe_actual=Privacy::hashes('techgenyz',7,'203.0.113.4','Browser/1',$dedupe_timestamp,'2026-09-18')['dedupe'];
$legacy_day=gmdate('Y-m-d',$dedupe_timestamp);
$legacy_key=hash_hmac('sha256','site-master-readers|'.$legacy_day,wp_salt('auth'),true);
$legacy_identity='203.0.113.4|browser/1';
$legacy_bucket=(string)floor($dedupe_timestamp/Config::DEDUPE_WINDOW_SECONDS);
$dedupe_expected=substr(hash_hmac('sha256','techgenyz|7|'.$legacy_identity.'|'.$legacy_bucket,$legacy_key,true),0,16);
sm_expect($dedupe_expected===$dedupe_actual,'Dedupe derivation changed during reader-day correction.');
$referrers=array(
	'www.google.com'=>'google.com',
	'google.com'=>'google.com',
	'https://www.google.com/search?q=test'=>'google.com',
	'news.google.com'=>'news.google.com',
	'https://www.reddit.com/r/example'=>'reddit.com',
	''=>'',
	'not a valid host/?q=secret'=>'',
);
foreach($referrers as$input=>$expected){sm_expect($expected===Privacy::referrer_domain($input),'Unexpected referrer normalization for: '.$input);}
sm_expect('google.com'===Aggregator::referrer_dimension(Privacy::referrer_domain('www.google.com')),'Google referrer was converted to direct acquisition traffic.');
sm_expect('direct'===Aggregator::referrer_dimension(Privacy::referrer_domain('')),'Empty referrer was not classified as direct traffic.');
sm_expect('mobile'===Privacy::device('Mozilla iPhone Mobile')&&Privacy::is_bot('Googlebot/2.1'),'Device/bot classifier failed.');
sm_expect(150===strlen(Privacy::bounded_text(str_repeat('x',300),150)),'UTM bound failed.');
sm_expect(null===Reporting::percent_change(2,0)&&0.0===Reporting::percent_change(0,0)&&100.0===Reporting::percent_change(20,10),'Comparison zero handling failed.');
sm_expect(Reporting::trend_score(80,5,100)>Reporting::trend_score(0,80,10000),'Trending incorrectly favors stale lifetime popularity.');

$root=dirname(__DIR__);$php='';foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/src/Analytics'))as$file){if($file->isFile()&&'php'===$file->getExtension())$php.=file_get_contents($file->getPathname());}
$js=(string)file_get_contents($root.'/assets/js/content-analytics.min.js');
sm_expect(!preg_match('/update_post_meta\s*\(/i',$php),'Per-view postmeta mutation found.');
sm_expect(!preg_match('/wp_remote_(?:get|post|request)\s*\(/i',$php),'Blocking remote request found.');
sm_expect(!preg_match('/document\.cookie|localStorage|sessionStorage|getContext\s*\(/i',$js),'Persistent/fingerprinting browser API found.');
foreach(array('raw_ip','full_user_agent','cookie_value','email','account_id')as$forbidden){sm_expect(false===stripos($php,$forbidden),'Forbidden persisted field found: '.$forbidden);}
sm_expect(false===stripos($php,'application/ld+json')&&!preg_match('/itemscope|itemtype|itemprop/',$php),'Analytics emitted schema markers.');
sm_expect(str_contains($php,'LIMIT %d')&&str_contains($php,'LIMIT 5000'),'Bounded worker/report limits missing.');
sm_expect(str_contains($php,'START TRANSACTION')&&str_contains($php,'GET_LOCK')&&str_contains($php,'last_aggregated_event_id'),'Transactional cursor/lock missing.');
sm_expect(str_contains($php,'wp_ajax_nopriv_tgm_count_post_view')&&str_contains($php,'wp_ajax_tgm_count_post_view'),'Legacy AJAX registration missing.');
sm_expect(str_contains($php,"array_intersect_key" )&&str_contains($php,'2048'),'Collector allowlist/size gate missing.');
$collector=(string)file_get_contents($root.'/src/Analytics/Collector.php');
$aggregator=(string)file_get_contents($root.'/src/Analytics/Aggregator.php');
sm_expect(1===substr_count($collector,"wp_date( 'Y-m-d', \$now, wp_timezone() )"),'Collector must calculate report_day exactly once.');
sm_expect(str_contains($collector,'Privacy::hashes( $profile, $post_id, (string) ( $server[\'REMOTE_ADDR\'] ?? \'\' ), $ua, $now, $report_day )')&&str_contains($collector,"'report_day' => \$report_day"),'Collector did not reuse one report_day for hashing and storage.');
sm_expect(str_contains($aggregator,'COUNT(DISTINCT e.reader_hash)')&&str_contains($aggregator,'e.report_day=d.report_day'),'Aggregation no longer finalizes distinct readers by stored report_day.');
sm_expect(str_contains($aggregator,"gmdate( 'Y-m-d H:00:00'"),'UTC hourly bucketing changed unexpectedly.');
echo "Site Master Content Analytics tests passed.\n";
