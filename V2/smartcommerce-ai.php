<?php
/**
 * Plugin Name:       SmartCommerce AI Toolkit V2
 * Plugin URI:        https://example.com/
 * Description:       All-in-one AI-powered toolkit for WordPress eCommerce brands. Includes AI chatbot, product description generator, email writer, SEO assistant, and customer chat history logging.
 * Version:           2.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smartcommerce-ai
 * Domain Path:       /languages
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SMARTCOMMERCE_AI_VERSION', '2.0.0' );
define( 'SMARTCOMMERCE_AI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMARTCOMMERCE_AI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the autoloader.
if ( file_exists( SMARTCOMMERCE_AI_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'vendor/autoload.php';
}

// Activation and deactivation hooks.
register_activation_hook( __FILE__, array( 'SmartCommerce_AI_V2', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SmartCommerce_AI_V2', 'deactivate' ) );

/**
 * The core plugin class.
 */
final class SmartCommerce_AI_V2 {

	/**
	 * The single instance of the class.
	 *
	 * @var SmartCommerce_AI_V2
	 */
	protected static $_instance = null;

	/**
	 * Main SmartCommerce_AI_V2 Instance.
	 *
	 * Ensures only one instance of SmartCommerce_AI_V2 is loaded or can be loaded.
	 *
	 * @static
	 * @return SmartCommerce_AI_V2 - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * SmartCommerce_AI_V2 Constructor.
	 */
	public function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'includes/class-smartcommerce-ai-chatbot.php';
		require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'includes/class-smartcommerce-ai-subscriptions.php';
		require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'includes/class-smartcommerce-ai-user-data.php';
		require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'includes/class-smartcommerce-ai-chat-logger.php';
		require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'includes/class-smartcommerce-ai-settings.php';

		if ( is_admin() ) {
			require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'admin/class-smartcommerce-ai-admin.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
	}

	/**
	 * On plugins_loaded.
	 */
	public function on_plugins_loaded() {
		// Initialization logic here.
	}

	/**
	 * Activation.
	 */
	public static function activate() {
		// Create chat log table.
		SmartCommerce_AI_Chat_Logger::create_table();
	}

	/**
	 * Deactivation.
	 */
	public static function deactivate() {
		// Deactivation logic here.
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function smartcommerce_ai_v2() {
	return SmartCommerce_AI_V2::instance();
}

// Get SmartCommerce AI V2 Running.
smartcommerce_ai_v2();
