<div class="wrap">
    <h1>Chatbot Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('smartcommerce_ai_chatbot_options');
        do_settings_sections('smartcommerce-ai-chatbot');
        submit_button();
        ?>
    </form>
</div>
