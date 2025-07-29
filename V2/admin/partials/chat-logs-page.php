<div class="wrap">
    <h1>Chat Logs</h1>
    <p>
        Here you can view and export the chat logs.
    </p>
    <form method="post" action="">
        <input type="hidden" name="export_chat_logs_csv" value="1" />
        <?php submit_button('Export as CSV'); ?>
    </form>
    <form method="post" action="">
        <input type="hidden" name="export_chat_logs_pdf" value="1" />
        <?php submit_button('Export as PDF'); ?>
    </form>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>User Message</th>
                <th>AI Message</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'smartcommerce_ai_chat_logs';
            $results = $wpdb->get_results("SELECT * FROM $table_name");
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->user_message) . '</td>';
                echo '<td>' . esc_html($row->ai_message) . '</td>';
                echo '<td>' . esc_html($row->time) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
