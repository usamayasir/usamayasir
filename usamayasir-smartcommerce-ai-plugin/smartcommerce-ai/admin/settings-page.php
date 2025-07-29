<?php
/**
 * Admin settings page.
 *
 * @package SmartCommerce_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class SmartCommerce_AI_Settings_Page {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		add_options_page(
			'SmartCommerce AI Settings',
			'SmartCommerce AI',
			'manage_options',
			'smartcommerce-ai-settings',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		$this->options = get_option( 'smartcommerce_ai_options' );
		?>
		<div class="wrap">
			<h1>SmartCommerce AI Settings</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'smartcommerce_ai_option_group' );
				do_settings_sections( 'smartcommerce-ai-setting-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'smartcommerce_ai_option_group',
			'smartcommerce_ai_options',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'setting_section_id',
			'API Settings',
			array( $this, 'print_section_info' ),
			'smartcommerce-ai-setting-admin'
		);

		add_settings_field(
			'api_key',
			'OpenAI API Key',
			array( $this, 'api_key_callback' ),
			'smartcommerce-ai-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'default_model',
			'Default Model',
			array( $this, 'default_model_callback' ),
			'smartcommerce-ai-setting-admin',
			'setting_section_id'
		);

		add_settings_field(
			'max_tokens',
			'Max Tokens',
			array( $this, 'max_tokens_callback' ),
			'smartcommerce-ai-setting-admin',
			'setting_section_id'
		);

		add_settings_section(
			'module_section_id',
			'Enable/Disable Modules',
			array( $this, 'print_module_section_info' ),
			'smartcommerce-ai-setting-admin'
		);

		add_settings_field(
			'enable_chatbot',
			'Enable Chatbot',
			array( $this, 'enable_chatbot_callback' ),
			'smartcommerce-ai-setting-admin',
			'module_section_id'
		);

		add_settings_field(
			'enable_product_generator',
			'Enable Product Generator',
			array( $this, 'enable_product_generator_callback' ),
			'smartcommerce-ai-setting-admin',
			'module_section_id'
		);

		add_settings_field(
			'enable_email_writer',
			'Enable Email Writer',
			array( $this, 'enable_email_writer_callback' ),
			'smartcommerce-ai-setting-admin',
			'module_section_id'
		);

		add_settings_field(
			'enable_seo_assistant',
			'Enable SEO Assistant',
			array( $this, 'enable_seo_assistant_callback' ),
			'smartcommerce-ai-setting-admin',
			'module_section_id'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = sanitize_text_field( $input['api_key'] );
		}

		if ( isset( $input['default_model'] ) ) {
			$new_input['default_model'] = sanitize_text_field( $input['default_model'] );
		}

		if ( isset( $input['max_tokens'] ) ) {
			$new_input['max_tokens'] = absint( $input['max_tokens'] );
		}

		$new_input['enable_chatbot'] = isset( $input['enable_chatbot'] ) ? 1 : 0;
		$new_input['enable_product_generator'] = isset( $input['enable_product_generator'] ) ? 1 : 0;
		$new_input['enable_email_writer'] = isset( $input['enable_email_writer'] ) ? 1 : 0;
		$new_input['enable_seo_assistant'] = isset( $input['enable_seo_assistant'] ) ? 1 : 0;

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		print 'Enter your OpenAI API settings below:';
	}

	public function print_module_section_info() {
		print 'Enable or disable specific AI modules:';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="api_key" name="smartcommerce_ai_options[api_key]" value="%s" />',
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function default_model_callback() {
		?>
		<select id="default_model" name="smartcommerce_ai_options[default_model]">
			<option value="gpt-3.5-turbo" <?php selected( $this->options['default_model'], 'gpt-3.5-turbo' ); ?>>GPT-3.5 Turbo</option>
			<option value="gpt-4" <?php selected( $this->options['default_model'], 'gpt-4' ); ?>>GPT-4</option>
		</select>
		<?php
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function max_tokens_callback() {
		printf(
			'<input type="number" id="max_tokens" name="smartcommerce_ai_options[max_tokens]" value="%s" />',
			isset( $this->options['max_tokens'] ) ? esc_attr( $this->options['max_tokens'] ) : '1024'
		);
	}

	public function enable_chatbot_callback() {
		printf(
			'<input type="checkbox" id="enable_chatbot" name="smartcommerce_ai_options[enable_chatbot]" value="1" %s />',
			checked( 1, $this->options['enable_chatbot'], false )
		);
	}

	public function enable_product_generator_callback() {
		printf(
			'<input type="checkbox" id="enable_product_generator" name="smartcommerce_ai_options[enable_product_generator]" value="1" %s />',
			checked( 1, $this->options['enable_product_generator'], false )
		);
	}

	public function enable_email_writer_callback() {
		printf(
			'<input type="checkbox" id="enable_email_writer" name="smartcommerce_ai_options[enable_email_writer]" value="1" %s />',
			checked( 1, $this->options['enable_email_writer'], false )
		);
	}

	public function enable_seo_assistant_callback() {
		printf(
			'<input type="checkbox" id="enable_seo_assistant" name="smartcommerce_ai_options[enable_seo_assistant]" value="1" %s />',
			checked( 1, $this->options['enable_seo_assistant'], false )
		);
	}
}

if ( is_admin() ) {
	$smartcommerce_ai_settings_page = new SmartCommerce_AI_Settings_Page();
}
