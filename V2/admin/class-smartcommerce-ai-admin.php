<?php

class SmartCommerce_AI_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'SmartCommerce AI',
            'SmartCommerce AI',
            'manage_options',
            'smartcommerce-ai',
            array($this, 'main_page'),
            'dashicons-superhero',
            6
        );

        add_submenu_page(
            'smartcommerce-ai',
            'Chatbot Settings',
            'Chatbot Settings',
            'manage_options',
            'smartcommerce-ai-chatbot',
            array($this, 'chatbot_settings_page')
        );

        add_submenu_page(
            'smartcommerce-ai',
            'Subscription',
            'Subscription',
            'manage_options',
            'smartcommerce-ai-subscription',
            array($this, 'subscription_page')
        );

        add_submenu_page(
            'smartcommerce-ai',
            'User Data',
            'User Data',
            'manage_options',
            'smartcommerce-ai-user-data',
            array($this, 'user_data_page')
        );

        add_submenu_page(
            'smartcommerce-ai',
            'Chat Logs',
            'Chat Logs',
            'manage_options',
            'smartcommerce-ai-chat-logs',
            array($this, 'chat_logs_page')
        );
    }

    public function main_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/main-page.php';
    }

    public function chatbot_settings_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/chatbot-settings-page.php';
    }

    public function subscription_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/subscription-page.php';
    }

    public function user_data_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/user-data-page.php';
    }

    public function chat_logs_page() {
        require_once plugin_dir_path(__FILE__) . 'partials/chat-logs-page.php';
    }
}

new SmartCommerce_AI_Admin();
