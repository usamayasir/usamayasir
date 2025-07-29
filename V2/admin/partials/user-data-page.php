<div class="wrap">
    <h1>User Data</h1>
    <p>
        Here you can view and export the user data collected by the chatbot.
    </p>
    <form method="post" action="">
        <input type="hidden" name="export_user_data" value="1" />
        <?php submit_button('Export as CSV'); ?>
    </form>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Query</th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'smartcommerce_ai_user_data';
            $results = $wpdb->get_results("SELECT * FROM $table_name");
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->name) . '</td>';
                echo '<td>' . esc_html($row->email) . '</td>';
                echo '<td>' . esc_html($row->phone) . '</td>';
                echo '<td>' . esc_html($row->query) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
