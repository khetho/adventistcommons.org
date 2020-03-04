define(
    [
        'jquery',
        './backend-caller',
        'mark.js/dist/jquery.mark.js',
    ],function($, BackendCaller, Mark) {

        let editor_content_origin = null;
        let editor_content_translation = null;
        let translation_area = null;
        let roles = null;
        let translator_dashboard = null;
        let current_translation = '';

        function setCurrentSentence(translation) {
            setWorkingTranslationIfOk($(translation).html().trim(), translation);
        }

        function setWorkingTranslationIfOk(content, translation) {
            if (translator_dashboard.is(":visible") && current_translation != translation_area.val()) {
                const modal = $('#changedTranslationModal');
                modal.modal();
                modal.find('[data-action="saveContinue"]').unbind('click').click(function(e){
                    save();
                    setWorkingTranslation(content, translation);
                    modal.modal('hide');
                });
                modal.find('[data-action="undoContinue"]').unbind('click').click(function(e){
                    setWorkingTranslation(content, translation);
                    modal.modal('hide');
                });
            } else {
                setWorkingTranslation(content, translation);
            }
        }

        function setWorkingTranslation(content, translation) {
            if (translation) {
                highlightTranslation(translation);
            }
            current_translation = stripHtml(content);
            translation_area.val(current_translation);
            translator_dashboard.show();    
        }

        function highlightTranslation(translation) {
            editor_content_origin.find('.s_selected').removeClass('s_selected');
            editor_content_translation.find('.s_selected').removeClass('s_selected');
            $(translation).addClass('s_selected');
            getOriginalFromTranslation(translation).addClass('s_selected');
        }
        
        function getTranslationFromOriginal(sentence) {
            return editor_content_translation.find('[data-sentence-id="' + $(sentence).data('sentence-id') + '"]');
        }

        function getOriginalFromTranslation(translation) {
            return editor_content_origin.find('[data-sentence-id="' + $(translation).data('sentence-id') + '"]');
        }

        function stripHtml(html)
        {
            const tmp = document.createElement("DIV");
            tmp.innerHTML = html;

            return tmp.textContent.trim() || tmp.innerText.trim() || "";
        }

        function getCurrentSentence() {
            const translation = editor_content_translation.find('.s_selected');
            if (!translation.length) {
                exception('Error: cannot find current translation');
                return;
            }
            return translation;
        }
                
        function save() {
            const role = roles.find('.active').data('role');
            if (typeof role === 'undefined') {
                exception('You must choose a role');
                return;
            }

            const translation = getCurrentSentence();
            if (!translation.length) {
                return;
            }

            const sentenceId = translation.attr('data-sentence-id');

            switch (role) {
                case 'translator':
                    // generate ajax coll to backend here, after success:
                    BackendCaller.callContentRevisionPut(
                        sentenceId,
                        translation_area.val(),
                        function() {
                            translation.removeData('sentence-state').attr("data-sentence-state", 'translated');
                            current_translation = translation_area.val();
                            translation.html(current_translation);
                        }
                    );
                    break;
                case 'proofreader':
                    // generate ajax coll to backend here, after success:
                    translation.removeData('sentence-state').attr("data-sentence-state", 'approved');
                    break;
                case 'reviewer':
                    // generate ajax coll to backend here, after success:
                    translation.removeData('sentence-state').attr("data-sentence-state", 'reviewed');
            }
        }
        
        function initRoles() {
            $('[data-save-role]').hide();
            $('[data-save-role="trans"]').show();

            $('.js-role > button').on('click', function () {
                $('.js-role > button').removeClass('active');
                $(this).addClass('active');
                $('[data-save-role]').hide();

                if ($(this).hasClass('js-proof')) {
                    $('[data-save-role="proof"]').show();
                    translation_area.attr('readonly','readonly');
                } else if($(this).hasClass('js-rev')) {
                    $('[data-save-role="rev"]').show();
                    translation_area.attr('readonly','readonly');
                } else if($(this).hasClass('js-trans')) {
                    $('[data-save-role="trans"]').show();
                    translation_area.removeAttr('readonly');
                }
            });
        }

        function selectNextSentence() {
            let next = getCurrentSentence().next();
            if (!next.length) {
                next = getCurrentSentence().closest('p').next().find('.js-sentence').first();
            }
            if (!next.length) {
                return;
            }
            setCurrentSentence(next);
        }

        function selectPrevSentence() {
            let prev = getCurrentSentence().prev();
            if (!prev.length) {
                prev = getCurrentSentence().closest('p').prev().find('.js-sentence').last();
            }
            if (!prev.length) {
                return;
            }
            setCurrentSentence(prev);
        }

        function exception(message) {
            alert(message);
        }

        return {
            init: function () {
                translation_area = $('.js-translation-area');
                editor_content_origin = $('div[data-role="origin"]');
                editor_content_translation = $('div[data-role="translation"]');

                editor_content_origin.on('click', '.js-sentence', function (e) {
                    setCurrentSentence(getTranslationFromOriginal(this));
                });
                editor_content_translation.on('click', '.js-sentence', function (e) {
                    setCurrentSentence(this);
                });

                translator_dashboard = $('.js-translator-dashboard');
                translator_dashboard.hide();
                translation_area.val('');

                roles = $('.js-roles');
                initRoles();

                // Translator save
                $('.js-save-translation').on('click', function () {
                    save(this);
                });

                $('.js-next-translation').on('click', function () {
                    selectNextSentence();
                });

                $('.js-prev-translation').on('click', function () {
                    selectPrevSentence();
                });

                // Move content
                let translation_tools = $('.js-chat-item, #translation-memory, #machine-translation');
                translation_tools.on('click', '.js-transfer', function (e) {
                    setWorkingTranslationIfOk($(e.delegateTarget).find('.js-subject').text());
                });
            }
        }
    }
);
