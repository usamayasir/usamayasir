jQuery(document).ready(function($) {
    $('#smartcommerce-ai-chatbot-submit').on('click', function() {
        var message = $('#smartcommerce-ai-chatbot-input').val();
        if (message) {
            $('#smartcommerce-ai-chatbot-messages').append('<div class="user-message">' + message + '</div>');
            $('#smartcommerce-ai-chatbot-input').val('');
            $.ajax({
                url: smartcommerce_ai_chatbot_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'smartcommerce_ai_chatbot',
                    nonce: smartcommerce_ai_chatbot_params.nonce,
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        $('#smartcommerce-ai-chatbot-messages').append('<div class="ai-message">' + response.data.message + '</div>');
                    } else {
                        $('#smartcommerce-ai-chatbot-messages').append('<div class="error-message">' + response.data.message + '</div>');
                    }
                }
            });
        }
    });
});
