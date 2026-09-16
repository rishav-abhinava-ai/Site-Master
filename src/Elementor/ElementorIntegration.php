<?php
/** Profile-aware Elementor integration boundary. @package SiteMaster */

namespace Abhinava\SiteMaster\Elementor;

use Abhinava\SiteMaster\Elementor\Compatibility\Blissz\BlisszCompatibility;
use Abhinava\SiteMaster\Profiles\ProfileInterface;

defined( 'ABSPATH' ) || exit;

final class ElementorIntegration {
	private ProfileInterface $profile;

	public function __construct( ProfileInterface $profile ) {
		$this->profile = $profile;
	}

	public function register(): void {
		if ( 'blissz' !== $this->profile->key() ) {
			return;
		}
		add_action( 'elementor/elements/categories_registered', array( BlisszCompatibility::class, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( BlisszCompatibility::class, 'register_widgets' ) );
	}
}
