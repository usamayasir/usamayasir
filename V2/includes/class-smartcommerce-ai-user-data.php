<?php

class SmartCommerce_AI_User_Data {

    public function __construct() {
        add_action('init', array($this, 'create_table'));
        add_action('admin_init', array($this, 'export_user_data'));
    }

    public function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartcommerce_ai_user_data';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email tinytext NOT NULL,
            phone tinytext NOT NULL,
            query text NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function collect_user_data($name, $email, $phone, $query) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartcommerce_ai_user_data';

        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'query' => $query,
            )
        );
    }

    public function export_user_data() {
        if (isset($_POST['export_user_data'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'smartcommerce_ai_user_data';
            $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

            if ($results) {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=user-data.csv');
                $output = fopen('php://output', 'w');
                fputcsv($output, array_keys($results[0]));
                foreach ($results as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
                exit;
            }
        }
    }
}

new SmartCommerce_AI_User_Data();
