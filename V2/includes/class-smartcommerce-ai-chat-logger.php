<?php

class SmartCommerce_AI_Chat_Logger {

    public function __construct() {
        add_action('init', array($this, 'create_table'));
        add_action('admin_init', array($this, 'export_chat_logs'));
    }

    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_message text NOT NULL,
            ai_message text NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function log_interaction($user_message, $ai_message) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';

        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'user_message' => $user_message,
                'ai_message' => $ai_message,
            )
        );
    }

    public function export_chat_logs() {
        if (isset($_POST['export_chat_logs_csv'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';
            $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

            if ($results) {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=chat-logs.csv');
                $output = fopen('php://output', 'w');
                fputcsv($output, array_keys($results[0]));
                foreach ($results as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit;
            }
        }

        if (isset($_POST['export_chat_logs_pdf'])) {
            // PDF export functionality will be added here.
        }
    }
}

new SmartCommerce_AI_Chat_Logger();
