jQuery(document).ready(function ($) {
    var chatbotWidget = $('#smartcommerce-ai-chatbot-widget');
    var chatbotFab = $('#smartcommerce-ai-chatbot-fab');
    var chatbotBody = $('#smartcommerce-ai-chatbot-body');
    var chatbotInput = $('#smartcommerce-ai-chatbot-input');

    function toggleChatbot() {
        chatbotWidget.toggle();
    }

    function sendMessage() {
        var message = chatbotInput.val();
        if (message.trim() === '') {
            return;
        }

        appendMessage(message, 'user');
        chatbotInput.val('');

        $.ajax({
            url: smartcommerce_ai_chatbot_params.ajax_url,
            type: 'POST',
            data: {
                action: 'smartcommerce_ai_chatbot',
                nonce: smartcommerce_ai_chatbot_params.nonce,
                message: message
            },
            success: function (response) {
                if (response.success) {
                    appendMessage(response.data.message, 'ai');
                } else {
                    appendMessage('Sorry, something went wrong. Please try again.', 'ai');
                }
            },
            error: function () {
                appendMessage('Sorry, something went wrong. Please try again.', 'ai');
            }
        });
    }

    function appendMessage(message, sender) {
        var messageHtml = '<div class="smartcommerce-ai-chatbot-message ' + sender + '"><p>' + message + '</p></div>';
        chatbotBody.append(messageHtml);
        chatbotBody.scrollTop(chatbotBody[0].scrollHeight);
    }

    chatbotFab.on('click', toggleChatbot);
    $('#smartcommerce-ai-chatbot-close').on('click', toggleChatbot);
    $('#smartcommerce-ai-chatbot-send').on('click', sendMessage);
    chatbotInput.on('keypress', function (e) {
        if (e.which === 13) {
            sendMessage();
        }
    });
});
