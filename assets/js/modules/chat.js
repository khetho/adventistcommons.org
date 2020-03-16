define(
    [
        'jquery',
    ],function($){
        return {
            init: function (parent) {
                /**
                 * Chat form submitting
                 */
                parent.keypress(function (e) {
                    if(e.which === 13 && !e.shiftKey) {
                        e.preventDefault();
                        let chat_body = parent.find('.js-chat-module-body');
                        let textarea = $(this).closest("form").find('textarea');
                        let message = textarea.val().trim();
                        if (message === ''){
                            return;
                        }
                        $.ajax({
                            type: "POST",
                            url: "file.php",
                            data: message,
                            success: function (results) {
                                let json = $.parseJSON(results);
                                if (json['saved']) {
                                    let new_msg = parent.find('.js-chat-item').last().clone();
                                    new_msg.find('.js-chat-item-body').text(message);
                                    new_msg.find('.js-chat-item-author').text(json['author']);
                                    new_msg.find('.js-chat-item-time').text(
                                        new Date().toLocaleTimeString('en-GB',
                                            {
                                                hour: "numeric",
                                                minute: "numeric"
                                            })
                                    );

                                    chat_body.children('.chat-item').each(function() {
                                        outerHeight += $(this).outerHeight();
                                    });

                                    chat_body
                                        .append(new_msg)
                                        .animate({ scrollTop: outerHeight }, "slow");
                                    textarea.val('');
                                }
                            }
                        });
                    }
                });
            }
        };
    }
);