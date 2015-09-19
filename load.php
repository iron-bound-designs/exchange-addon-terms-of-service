<?php
/*
Plugin Name: iThemes Exchange - Terms of Service add-on
Plugin URI: https://ironbounddesigns.com
Description: Have your customers agree to your Terms of Service when purchasing your products.
Version: 1.0
Author: Iron Bound Designs
Author URI: https://ironbounddesigns.com
License: GPLv2
Text Domain: ibd-exchange-addon-tos
Domain Path: /lang
*/

if ( version_compare( phpversion(), '5.3', '<' ) ) {

	/**
	 * Displays a message saying this plugin requires php 5.3 or higher.
	 *
	 * @since 1.0
	 */
	function itetos_outdated_php() {

		?>

		<div class="notice notice-error">
			<p><?php _e( "iThemes Exchange Terms of Service requires PHP version 5.3 or higher.", 'ibd-exchange-addon-tos' ); ?></p>
		</div>

		<?php
	}

	add_action( 'admin_notices', 'itetos_outdated_php' );

	/**
	 * Deactivate the plugin.
	 *
	 * @since 1.0
	 */
	function itetos_deactivate_self() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	add_action( 'admin_init', 'itetos_deactivate_self' );

	return;
} else {
	require_once plugin_dir_path( __FILE__ ) . 'exchange-addon-terms-of-service.php';
}