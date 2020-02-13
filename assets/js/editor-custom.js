define(
    [
        'jquery',
        'mark.js/dist/jquery.mark.js',
    ],function($, Mark){

        function setup_words(word, resWord, resDef, e)
        {
            let editor_content = $('div[data-role="origin"], div[data-role="translation"]');
            let instance = new Mark(editor_content);
            instance.markRegExp(new RegExp("\\b" + word + "\\b", 'gim'), {
                "element" : "span",
                "className": "",
                "each" : htmlElement => {
                    let elemClass = salted(word.replace(/'/g,""));
                    htmlElement.classList.add(elemClass);
                },
            });

            console.log('PX:'+e.clientX, 'PY:'+e.clientY);

            $('.'+document.elementFromPoint(e.pageX - window.pageXOffset,e.pageY - window.pageYOffset).classList)
                .popover({
                    placement:'top',
                    trigger:'manual',
                    html:true,
                    title:resWord,
                    content:resDef,
                })
                .popover('show').addClass('wordapi_popover');
        }

        function salted (word) {
            return  word+'_'+Math.random().toString(36).substring(7);
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function textToSentence(text) {
            let pattern = /(.+?([A-Z].)[\.|\?](?:['")\\\s]?)+?\s?)/igm, match;
            let text_buffer = [];
            while( ( match = pattern.exec( text )) != null ) {
                if( match.index === pattern.lastIndex ) {
                    pattern.lastIndex++;
                }
                text_buffer.push(match[0].trim());
            }
            return text_buffer;
        }

        function mirrorMarkSearch(needle, id) {
            let instance = new Mark($('div[data-role="translation"]'));
            instance.markRegExp(new RegExp(needle, 'gm'), {
                "element" : "span",
                "className": "sentence",
                "each" : htmlElement => {
                    //console.log('target: ' + htmlElement.textContent);
                    htmlElement.dataset.concord = id;
                },
            });
        }

        $( document ).ready(function() {
            let editor_content = $('div[data-role="origin"], div[data-role="translation"]');
            let translation_save = $('.save-translation');
            /**
             * Sentence handling
             */
            let instance = new Mark(editor_content);
            instance.markRegExp(new RegExp(/(.+?([A-Z].)[\.|\?](?:['")\\\s]?)+?\s?)/, 'gim'), {
                "element" : "span",
                "className": "sentence",
                "each" : htmlElement => {
                    let id = salted('s_');
                    //console.log('donor: ' + htmlElement.textContent);
                    htmlElement.dataset.concord = id;
                    mirrorMarkSearch(escapeRegExp(htmlElement.textContent), id);
                },
            });

            // Translation caret
            editor_content.on('click', '.sentence', function (e) {
                $(this).addClass('s_selected');
                $(e.delegateTarget).find($('.s_selected').not($(this))).removeClass('s_selected');

                let parent =  $(e.delegateTarget).parent().siblings();
                parent.find('.s_selected').removeClass('s_selected');
                parent.find('[data-concord="' + $(this).data('concord') + '"]')
                    .addClass('s_selected');
            });

            // Translator save
            translation_save.on('click', function () {
                let role = $('#roles').find('.active').data('role');
                let translation = $('div[data-role="translation"]').find('.s_selected');

                if ( !translation.length ){
                    console.log('Error: Nothing to save');
                    return;
                }
                if ( typeof role === 'undefined' ){
                    alert('You need choice a role');
                    return;
                }

                switch (role) {
                    case 'translator':
                        // generate ajax coll to backend here, after success:
                        translation.removeData('sentence-state').attr("data-sentence-state", 'translated' );
                        break;
                    case 'proofreader':
                        // generate ajax coll to backend here, after success:
                        translation.removeData('sentence-state').attr("data-sentence-state", 'approved' );
                        break;
                    case 'reviewer':
                        // generate ajax coll to backend here, after success:
                        translation.removeData('sentence-state').attr("data-sentence-state", 'reviewed' );
                }
            });

            // Move content
            let translation_tools = $('.chat-item, #translation-memory, #machine-translation');
            translation_tools.on('click', '.transfer', function (e) {
                console.log($(e.delegateTarget).find('.subject').text());
                $('#translation_area').val(
                    $(e.delegateTarget).find('.subject').text().trim()
                );
            });

            $('.role > button').on('click', function () {
                $(this).addClass('active');
                $('.role > button').not($(this)).removeClass('active');

                if ($(this).hasClass('proof') || $(this).hasClass('rev')){
                    $('.save-translation').text('Approve');
                } else {
                    $('.save-translation').text('Save');
                }
            });

            /**
             * Chat form submitting
             */
            // Ajax response mock
            function ajax_response(response) {
                return function (params) {
                    params.success(response);
                };
            }
            $.ajax = ajax_response('{"saved":true, "author":"Artur"}');

            $("#comment").keypress(function (e) {
                if(e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    let chat_body = $('#comments .chat-module-body');
                    let textarea = $(this).closest("form").find('#comment');
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
                                let new_msg = $('#comments .chat-item').last().clone();
                                new_msg.find('.chat-item-body').text(message);
                                new_msg.find('.chat-item-author').text(json['author']);
                                new_msg.find('.chat-item-time').text(
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

            /**
             * Glossary example
             * @type {string}
             */
            let glossary = {
                "morning":"Yoruba: owurọ",
                "raising":"Yoruba: igbega",
                "salesman":"Yoruba: olutaja"
            };

            $('div[data-role="origin"]').mark(Object.keys(glossary), {
                "element": "span",
                "accuracy": "complementary",
                "className": "adventist_terms",
                "each" : htmlElement => {
                    $.each(glossary, function(term, def) {
                        if (htmlElement.textContent === term){
                            htmlElement.classList.add(term);
                        }
                    });
                },
            });
            $.each(glossary, function(term, def) {
                $('div[data-role="origin"]')
                    .find('.'+term)
                    .popover({
                        placement:'top',
                        trigger:'hover',
                        html:true,
                        title:'Glossary term: '+term,
                        content:def,
                    })
                    .on('click', function () {
                        $(this).popover('hide');
                    });
            });

            /**
             * WordAPL
             */
            editor_content.on('dblclick', editor_content, function (e) {
                let range = window.getSelection() || document.getSelection() || document.selection.createRange();
                let word = $.trim(range.toString().replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,""));

                if(word !== '' && !$(e.target).hasClass('wordapi_popover') ) {
                    var settings = {
                        "async": true,
                        "crossDomain": true,
                        "url": 'https://wordsapiv1.p.rapidapi.com/words/'+word+'/definitions',
                        "method": "GET",
                        "headers": {
                            "x-rapidapi-host": "wordsapiv1.p.rapidapi.com",
                            "x-rapidapi-key": "31a8e8bfddmshc18ebaedde291e5p1ed469jsna7ca535fa9b4"
                        }
                    };

                    $.ajax(settings)
                        .done(function (response) {
                            var resWord = response.word;
                            var resDef = 'Definition not found';

                            if (response.definitions.length) {
                                resDef = response.definitions[0].definition;
                            }

                            setup_words(word, resWord, resDef, e);
                        })
                        .fail(function (response) {
                            setup_words(word, 'Word not found', 'Definition not found', e);
                        });
                    //console.log($(e.target));
                } else if ($(e.target).hasClass('wordapi_popover')) {
                    $(e.target).popover('show');
                }
            })
                .on('mouseleave click', '.wordapi_popover', function(e){
                    $(this).popover('hide');
                });

            /**
             * TODO: Refactor
             */
            $('#show_comments').on('click', function () {
                if ($('#comments').hasClass('show')){
                    $('#comments').collapse('hide');
                    $('#machine').collapse('show');

                    $('#show_comments').removeClass('active');
                } else {
                    $('#show_review').removeClass('active');
                    $('#show_comments').addClass('active');

                    $('#machine').collapse('hide');
                    $('#revisions').collapse('hide');
                    $('#comments').collapse('show');
                }
            });
            $('#show_review').on('click', function () {
                if ($('#revisions').hasClass('show')){
                    $('#revisions').collapse('hide');
                    $('#machine').collapse('show');

                    $('show_review').removeClass('active');
                } else {
                    $('#show_comments').removeClass('active');
                    $('#show_review').addClass('active');

                    $('#machine').collapse('hide');
                    $('#comments').collapse('hide');
                    $('#revisions').collapse('show');
                }
            });
        });

    });