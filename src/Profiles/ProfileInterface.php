<?php
/**
 * Site profile contract.
 *
 * @package SiteMaster
 */

namespace Abhinava\SiteMaster\Profiles;

defined( 'ABSPATH' ) || exit;

interface ProfileInterface {

	public function key(): string;

	/**
	 * @return array<string,bool>
	 */
	public function modules(): array;
}
