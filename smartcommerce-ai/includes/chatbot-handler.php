<?php
/**
 * Chatbot handler.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Chatbot_Handler {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'add_chatbot_ui' ) );
		add_shortcode( 'smartcommerce_ai_chatbot', array( $this, 'chatbot_shortcode' ) );
		add_action( 'wp_ajax_smartcommerce_ai_chatbot', array( $this, 'handle_chatbot_request' ) );
		add_action( 'wp_ajax_nopriv_smartcommerce_ai_chatbot', array( $this, 'handle_chatbot_request' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'smartcommerce-ai-style', SMARTCOMMERCE_AI_PLUGIN_URL . 'assets/style.css', array(), SMARTCOMMERCE_AI_VERSION );
		wp_enqueue_script( 'smartcommerce-ai-chat', SMARTCOMMERCE_AI_PLUGIN_URL . 'assets/chat.js', array( 'jquery' ), SMARTCOMMERCE_AI_VERSION, true );
		wp_localize_script(
			'smartcommerce-ai-chat',
			'smartcommerce_ai_chatbot_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'smartcommerce_ai_chatbot_nonce' ),
			)
		);
	}

	public function add_chatbot_ui() {
		$options = get_option( 'smartcommerce_ai_options' );
		if ( ! empty( $options['enable_chatbot'] ) ) {
			require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'templates/chatbot-ui.php';
		}
	}

	public function chatbot_shortcode() {
		ob_start();
		$this->add_chatbot_ui();
		return ob_get_clean();
	}

	public function handle_chatbot_request() {
		check_ajax_referer( 'smartcommerce_ai_chatbot_nonce', 'nonce' );

		$message = sanitize_text_field( $_POST['message'] );

		$response = SmartCommerce_AI_Functions::call_openai_api(
			array(
				array(
					'role'    => 'system',
					'content' => 'You are a helpful eCommerce assistant.',
				),
				array(
					'role'    => 'user',
					'content' => $message,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		} else {
			$ai_message = $response['choices'][0]['message']['content'];
			SmartCommerce_AI_Chat_Logger::log_interaction( $message, $ai_message );
			wp_send_json_success( array( 'message' => $ai_message ) );
		}
	}
}

new SmartCommerce_AI_Chatbot_Handler();
