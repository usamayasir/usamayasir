<?php

class SmartCommerce_AI_Chatbot {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_chatbot_ui'));
        add_shortcode('smartcommerce_ai_chatbot', array($this, 'chatbot_shortcode'));
        add_action('wp_ajax_smartcommerce_ai_chatbot', array($this, 'handle_chatbot_request'));
        add_action('wp_ajax_nopriv_smartcommerce_ai_chatbot', array($this, 'handle_chatbot_request'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('smartcommerce-ai-style', SMARTCOMMERCE_AI_PLUGIN_URL . 'assets/style.css', array(), SMARTCOMMERCE_AI_VERSION);
        wp_enqueue_script('smartcommerce-ai-chat', SMARTCOMMERCE_AI_PLUGIN_URL . 'assets/chat.js', array('jquery'), SMARTCOMMERCE_AI_VERSION, true);
        wp_localize_script(
            'smartcommerce-ai-chat',
            'smartcommerce_ai_chatbot_params',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('smartcommerce_ai_chatbot_nonce'),
            )
        );
    }

    public function add_chatbot_ui() {
        $options = get_option('smartcommerce_ai_chatbot_options');
        if (!empty($options['enable_chatbot'])) {
            if (!empty($options['restrict_to_registered_users']) && !is_user_logged_in()) {
                return;
            }
            $template = !empty($options['chatbot_template']) ? $options['chatbot_template'] : 'default';
            require_once SMARTCOMMERCE_AI_PLUGIN_DIR . 'templates/chatbot-ui-' . $template . '.php';
        }
    }

    public function chatbot_shortcode() {
        ob_start();
        $this->add_chatbot_ui();
        return ob_get_clean();
    }

    public function handle_chatbot_request() {
        check_ajax_referer('smartcommerce_ai_chatbot_nonce', 'nonce');

        $message = sanitize_text_field($_POST['message']);

        // User data collection
        if (preg_match('/my name is (.*)/i', $message, $matches)) {
            $name = $matches[1];
            // Save name to user meta or session
        }
        if (preg_match('/my email is (.*)/i', $message, $matches)) {
            $email = $matches[1];
            // Save email to user meta or session
        }
        if (preg_match('/my phone is (.*)/i', $message, $matches)) {
            $phone = $matches[1];
            // Save phone to user meta or session
        }

        $response = $this->call_openai_api($message);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        } else {
            $ai_message = $response['choices'][0]['message']['content'];
            SmartCommerce_AI_Chat_Logger::log_interaction($message, $ai_message);
            wp_send_json_success(array('message' => $ai_message));
        }
    }

    public function call_openai_api($message) {
        $options = get_option('smartcommerce_ai_options');
        $api_key = isset($options['api_key']) ? $options['api_key'] : '';
        $model   = isset($options['default_model']) ? $options['default_model'] : 'gpt-3.5-turbo';
        $max_tokens = isset($options['max_tokens']) ? $options['max_tokens'] : 1024;

        if (empty($api_key)) {
            return new WP_Error('api_key_missing', __('OpenAI API key is not set.', 'smartcommerce-ai'));
        }

        // Pre-trained scripts
        $business_name = isset($options['business_name']) ? $options['business_name'] : '';
        $contact_details = isset($options['contact_details']) ? $options['contact_details'] : '';
        $business_model = isset($options['business_model']) ? $options['business_model'] : '';

        $system_message = 'You are a helpful eCommerce assistant for ' . $business_name . '. ' . $business_model . ' You can be contacted at ' . $contact_details . '.';

        // Auto-training from website database
        // Fetch products and services from the database and add them to the system message.

        $messages = array(
            array(
                'role'    => 'system',
                'content' => $system_message,
            ),
            array(
                'role'    => 'user',
                'content' => $message,
            ),
        );

        $response = wp_remote_post(
            'https://api.openai.com/v1/chat/completions',
            array(
                'headers' => array(
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body'    => json_encode(
                    array(
                        'model'    => $model,
                        'messages' => $messages,
                        'max_tokens' => $max_tokens,
                    )
                ),
                'timeout' => 60,
            )
        );

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return new WP_Error('openai_error', $data['error']['message']);
        }

        return $data;
    }
}

new SmartCommerce_AI_Chatbot();
