<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      2.0.0
 *
 * @package    SmartCommerce_AI
 * @subpackage SmartCommerce_AI/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SmartCommerce_AI
 * @subpackage SmartCommerce_AI/admin
 * @author     Your Name <email@example.com>
 */
class SmartCommerce_AI_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add the top-level admin menu.
	 *
	 * @since    2.0.0
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'SmartCommerce AI', 'smartcommerce-ai' ),
			__( 'SmartCommerce AI', 'smartcommerce-ai' ),
			'manage_options',
			'smartcommerce-ai',
			array( $this, 'display_settings_page' ),
			'dashicons-superhero',
			58
		);

		add_submenu_page(
			'smartcommerce-ai',
			__( 'Chatbot', 'smartcommerce-ai' ),
			__( 'Chatbot', 'smartcommerce-ai' ),
			'manage_options',
			'smartcommerce-ai-chatbot',
			array( $this, 'display_chatbot_page' )
		);

		add_submenu_page(
			'smartcommerce-ai',
			__( 'Product Generator', 'smartcommerce-ai' ),
			__( 'Product Generator', 'smartcommerce-ai' ),
			'manage_options',
			'smartcommerce-ai-product-generator',
			array( $this, 'display_product_generator_page' )
		);

		add_submenu_page(
			'smartcommerce-ai',
			__( 'Email Writer', 'smartcommerce-ai' ),
			__( 'Email Writer', 'smartcommerce-ai' ),
			'manage_options',
			'smartcommerce-ai-email-writer',
			array( $this, 'display_email_writer_page' )
		);

		add_submenu_page(
			'smartcommerce-ai',
			__( 'SEO Assistant', 'smartcommerce-ai' ),
			__( 'SEO Assistant', 'smartcommerce-ai' ),
			'manage_options',
			'smartcommerce-ai-seo-assistant',
			array( $this, 'display_seo_assistant_page' )
		);

		add_submenu_page(
			'smartcommerce-ai',
			__( 'Chat Logs', 'smartcommerce-ai' ),
			__( 'Chat Logs', 'smartcommerce-ai' ),
			'manage_options',
			'smartcommerce-ai-chat-logs',
			array( $this, 'display_chat_logs_page' )
		);
	}

	/**
	 * Display the settings page.
	 *
	 * @since    2.0.0
	 */
	public function display_settings_page() {
		require_once 'partials/settings-page.php';
	}

	/**
	 * Display the chatbot page.
	 *
	 * @since    2.0.0
	 */
	public function display_chatbot_page() {
		require_once 'partials/chatbot-page.php';
	}

	/**
	 * Display the product generator page.
	 *
	 * @since    2.0.0
	 */
	public function display_product_generator_page() {
		require_once 'partials/product-generator-page.php';
	}

	/**
	 * Display the email writer page.
	 *
	 * @since    2.0.0
	 */
	public function display_email_writer_page() {
		require_once 'partials/email-writer-page.php';
	}

	/**
	 * Display the SEO assistant page.
	 *
	 * @since    2.0.0
	 */
	public function display_seo_assistant_page() {
		require_once 'partials/seo-assistant-page.php';
	}

	/**
	 * Display the chat logs page.
	 *
	 * @since    2.0.0
	 */
	public function display_chat_logs_page() {
		require_once 'partials/chat-logs-page.php';
	}
}
