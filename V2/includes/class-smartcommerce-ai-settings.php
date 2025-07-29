<?php

class SmartCommerce_AI_Settings {

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting(
            'smartcommerce_ai_chatbot_options',
            'smartcommerce_ai_chatbot_options',
            array($this, 'sanitize_options')
        );

        add_settings_section(
            'smartcommerce_ai_chatbot_general_section',
            'General Settings',
            null,
            'smartcommerce-ai-chatbot'
        );

        add_settings_field(
            'enable_chatbot',
            'Enable Chatbot',
            array($this, 'enable_chatbot_callback'),
            'smartcommerce-ai-chatbot',
            'smartcommerce_ai_chatbot_general_section'
        );

        add_settings_field(
            'restrict_to_registered_users',
            'Restrict to Registered Users',
            array($this, 'restrict_to_registered_users_callback'),
            'smartcommerce-ai-chatbot',
            'smartcommerce_ai_chatbot_general_section'
        );

        add_settings_section(
            'smartcommerce_ai_chatbot_appearance_section',
            'Appearance Settings',
            null,
            'smartcommerce-ai-chatbot'
        );

        add_settings_field(
            'chatbot_template',
            'Chatbot Template',
            array($this, 'chatbot_template_callback'),
            'smartcommerce-ai-chatbot',
            'smartcommerce_ai_chatbot_appearance_section'
        );
    }

    public function sanitize_options($options) {
        $options['enable_chatbot'] = isset($options['enable_chatbot']);
        $options['restrict_to_registered_users'] = isset($options['restrict_to_registered_users']);
        $options['chatbot_template'] = sanitize_text_field($options['chatbot_template']);
        return $options;
    }

    public function enable_chatbot_callback() {
        $options = get_option('smartcommerce_ai_chatbot_options');
        $checked = isset($options['enable_chatbot']) ? 'checked' : '';
        echo '<input type="checkbox" name="smartcommerce_ai_chatbot_options[enable_chatbot]" ' . $checked . ' />';
    }

    public function restrict_to_registered_users_callback() {
        $options = get_option('smartcommerce_ai_chatbot_options');
        $checked = isset($options['restrict_to_registered_users']) ? 'checked' : '';
        echo '<input type="checkbox" name="smartcommerce_ai_chatbot_options[restrict_to_registered_users]" ' . $checked . ' />';
    }

    public function chatbot_template_callback() {
        $options = get_option('smartcommerce_ai_chatbot_options');
        $template = isset($options['chatbot_template']) ? $options['chatbot_template'] : 'default';
        echo '<select name="smartcommerce_ai_chatbot_options[chatbot_template]">';
        echo '<option value="default" ' . selected($template, 'default', false) . '>Default</option>';
        echo '<option value="template1" ' . selected($template, 'template1', false) . '>Template 1</option>';
        echo '<option value="template2" ' . selected($template, 'template2', false) . '>Template 2</option>';
        echo '</select>';
    }
}

new SmartCommerce_AI_Settings();
