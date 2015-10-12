<?php
/**
 * Main plugin file.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace ITETOS;

/**
 * Class Plugin
 * @package ITETOS
 */
class Plugin {

	/**
	 * Plugin Version
	 */
	const VERSION = '1.0.1';

	/**
	 * Translation SLUG
	 */
	const SLUG = 'ibd-exchange-addon-tos';

	/**
	 * Exchange add-on slug.
	 */
	const ADD_ON = 'terms-of-service';

	/**
	 * @var string
	 */
	static $dir;

	/**
	 * @var string
	 */
	static $url;

	/**
	 * Constructor.
	 */
	public function __construct() {
		self::$dir = plugin_dir_path( __FILE__ );
		self::$url = plugin_dir_url( __FILE__ );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts_and_styles' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ), 5 );

		self::autoload();
	}

	/**
	 * Run the upgrade routine if necessary.
	 */
	public static function upgrade() {
		$current_version = get_option( 'itetos_version', '1.0' );

		if ( $current_version != self::VERSION ) {

			/**
			 * Runs when the version upgrades.
			 *
			 * @param $current_version
			 * @param $new_version
			 */
			do_action( 'itetos_upgrade', self::VERSION, $current_version );

			update_option( 'itetos_version', self::VERSION );
		}
	}

	/**
	 * The activation hook.
	 */
	public function activate() {
		do_action( 'itetos_activate' );
	}

	/**
	 * The deactivation hook.
	 */
	public function deactivate() {

	}

	/**
	 * Register admin scripts.
	 *
	 * @since 1.0
	 */
	public function scripts_and_styles() {

		wp_register_script( 'itetos-checkout', self::$url . 'assets/js/checkout.js', array( 'jquery' ), self::VERSION );
		wp_register_script( 'itetos-sw', self::$url . 'assets/js/super-widget.js', array( 'jquery' ), self::VERSION );

		wp_register_style( 'itetos-checkout', self::$url . 'assets/css/checkout.css', array(), self::VERSION );
		wp_register_style( 'itetos-sw', self::$url . 'assets/css/super-widget.css', array(), self::VERSION );
	}

	/**
	 * Autoloader.
	 *
	 * @since 1.0
	 */
	public static function autoload() {

		require_once( self::$dir . 'autoloader.php' );

		$autoloader = new Psr4AutoloaderClass();
		$autoloader->addNamespace( 'ITETOS', self::$dir . 'lib' );

		$autoloader->register();
	}
}

new Plugin();

/**
 * This registers our add-on
 *
 * @since 1.0
 */
function register_addon() {
	$options = array(
		'name'              => __( 'Terms of Service', Plugin::SLUG ),
		'description'       => __( 'Have your customers agree to your Terms of Service when purchasing your products.', Plugin::SLUG ),
		'author'            => 'Iron Bound Designs',
		'author_url'        => 'http://www.ironbounddesigns.com',
		'file'              => dirname( __FILE__ ) . '/init.php',
		'icon'              => Plugin::$url . '/assets/img/icon-50.png',
		'category'          => 'product-feature',
		'settings-callback' => array( 'ITETOS\Settings', 'display' ),
		'basename'          => plugin_basename( __FILE__ ),
		'labels'            => array(
			'singular_name' => __( 'Terms of Service', Plugin::SLUG ),
		)
	);
	it_exchange_register_addon( 'terms-of-service', $options );
}

add_action( 'it_exchange_register_addons', __NAMESPACE__ . '\\register_addon' );

/**
 * Loads the translation data for WordPress
 *
 * @since 1.0
 */
function set_textdomain() {
	load_plugin_textdomain( Plugin::SLUG, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\set_textdomain' );