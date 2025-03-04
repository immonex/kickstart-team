<?php
/**
 * TGMPA handling
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

require_once __DIR__ . '/includes/third_party/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', __NAMESPACE__ . '\register_required_plugins' );

if ( ! is_callable( __NAMESPACE__ . '\register_required_plugins' ) ) {
	/**
	 * Register required plugins.
	 */
	function register_required_plugins() {
		$plugins = array(
			array(
				'name'        => 'immonex Kickstart',
				'slug'        => 'immonex-kickstart',
				'is_callable' => array( '\immonex\Kickstart\Kickstart', 'init_plugin' ),
				'required'    => true,
				'version'     => '1.9.46',
			),
		);

		$config = array(
			'id'           => 'immonex-kickstart-team',
			'default_path' => '',
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => 'plugins.php',
			'capability'   => 'manage_options',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => '',
		);

		tgmpa( $plugins, $config );
	} // register_required_plugins
}
