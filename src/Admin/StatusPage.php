<?php
/** Site Master setup and status screen. @package SiteMaster */

namespace Abhinava\SiteMaster\Admin;

use Abhinava\SiteMaster\Core\DependencyStatus;
use Abhinava\SiteMaster\Core\ModuleRegistry;
use Abhinava\SiteMaster\Profiles\ProfileInterface;
use Abhinava\SiteMaster\Profiles\ProfileResolver;

defined( 'ABSPATH' ) || exit;

final class StatusPage {
	private ProfileInterface $profile;

	public function __construct( ProfileInterface $profile ) {
		$this->profile = $profile;
	}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_post_site_master_save_profile', array( $this, 'save_profile' ) );
	}

	public function add_menu(): void {
		add_menu_page( __( 'Site Master', 'site-master' ), __( 'Site Master', 'site-master' ), 'manage_options', 'site-master', array( $this, 'render' ), 'dashicons-admin-site-alt3' );
	}

	public function save_profile(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to configure Site Master.', 'site-master' ) );
		}

		check_admin_referer( 'site_master_save_profile' );
		$value = isset( $_POST['site_master_profile'] ) ? sanitize_key( wp_unslash( $_POST['site_master_profile'] ) ) : '';
		$result = self::persist_profile( $value );
		wp_safe_redirect( add_query_arg( $result, '1', admin_url( 'admin.php?page=site-master' ) ) );
		exit;
	}

	/**
	 * Persist a validated profile unless deployment configuration locks it.
	 *
	 * @return string Redirect notice key.
	 */
	public static function persist_profile( string $value ): string {
		if ( defined( 'SITE_MASTER_PROFILE' ) ) {
			return 'profile_locked';
		}

		$value = sanitize_key( $value );
		if ( ! in_array( $value, ProfileResolver::supported(), true ) ) {
			$value = '';
		}

		update_option( ProfileResolver::OPTION, $value, false );
		return 'updated';
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$locked       = defined( 'SITE_MASTER_PROFILE' );
		$dependencies = DependencyStatus::all();
		$modules      = ModuleRegistry::status( $this->profile );
		$dependency_labels = array(
			'elementor'     => 'Elementor',
			'elementor_pro' => 'Elementor Pro',
			'rank_math'     => 'Rank Math',
		);
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Site Master Status', 'site-master' ); ?></h1>
			<?php if ( isset( $_GET['profile_locked'] ) ) : ?><div class="notice notice-warning"><p><?php echo esc_html__( 'Profile changes are locked by SITE_MASTER_PROFILE.', 'site-master' ); ?></p></div><?php endif; ?>
			<table class="widefat striped"><tbody>
				<tr><th><?php echo esc_html__( 'Plugin', 'site-master' ); ?></th><td><?php echo esc_html__( 'Site Master', 'site-master' ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Development Version', 'site-master' ); ?></th><td><?php echo esc_html( SITE_MASTER_DEVELOPMENT_VERSION ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Runtime / Plugin Version', 'site-master' ); ?></th><td><?php echo esc_html( SITE_MASTER_VERSION ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Channel', 'site-master' ); ?></th><td><?php echo esc_html( SITE_MASTER_CHANNEL ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Active Profile', 'site-master' ); ?></th><td><?php echo esc_html( $this->profile->key() ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'Profile Source', 'site-master' ); ?></th><td><?php echo esc_html( ProfileResolver::source() ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'WordPress Version', 'site-master' ); ?></th><td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td></tr>
				<tr><th><?php echo esc_html__( 'PHP Version', 'site-master' ); ?></th><td><?php echo esc_html( PHP_VERSION ); ?></td></tr>
			</tbody></table>
			<h2><?php echo esc_html__( 'Site profile', 'site-master' ); ?></h2>
			<?php if ( $locked ) : ?>
				<p><?php echo esc_html__( 'The profile is locked by SITE_MASTER_PROFILE.', 'site-master' ); ?></p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="site_master_save_profile">
					<?php wp_nonce_field( 'site_master_save_profile' ); ?>
					<select name="site_master_profile">
						<option value=""><?php echo esc_html__( 'Unconfigured', 'site-master' ); ?></option>
						<option value="techgenyz" <?php selected( $this->profile->key(), 'techgenyz' ); ?>><?php echo esc_html__( 'Techgenyz', 'site-master' ); ?></option>
						<option value="blissz" <?php selected( $this->profile->key(), 'blissz' ); ?>><?php echo esc_html__( 'The Blissz', 'site-master' ); ?></option>
					</select>
					<?php submit_button( __( 'Save profile', 'site-master' ) ); ?>
				</form>
			<?php endif; ?>
			<h2><?php echo esc_html__( 'Optional dependencies', 'site-master' ); ?></h2>
			<ul><?php foreach ( $dependencies as $name => $active ) : ?><li><?php echo esc_html( $dependency_labels[ $name ] . ': ' . ( $active ? 'available' : 'unavailable' ) ); ?></li><?php endforeach; ?></ul>
			<h2><?php echo esc_html__( 'Reserved modules', 'site-master' ); ?></h2>
			<ul><?php foreach ( $modules as $name => $status ) : ?><li><?php echo esc_html( $name . ': ' . $status['state'] ); ?></li><?php endforeach; ?></ul>
		</div>
		<?php
	}
}
