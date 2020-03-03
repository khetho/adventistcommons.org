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

        function setCurrentTranslation(translation) {
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
                
        function save() {
            const role = roles.find('.active').data('role');
            const translation = editor_content_translation.find('.s_selected');
            const sentenceId = translation.attr('data-sentence-id'); 

            if (!translation.length) {
                console.log('Error: Nothing to save');
                return;
            }
            if (typeof role === 'undefined') {
                alert('You need choice a role');
                return;
            }

            switch (role) {
                case 'translator':
                    // generate ajax coll to backend here, after success:
                    BackendCaller.callContentRevisionPut(sentenceId, );
                    translation.removeData('sentence-state').attr("data-sentence-state", 'translated');
                    current_translation = translation_area.val();
                    translation.html(current_translation);
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
            $('.js-role > button').on('click', function () {
                $(this).addClass('active');
                $('.js-role > button').not($(this)).removeClass('active');

                if ($(this).hasClass('js-proof') || $(this).hasClass('js-rev')) {
                    $('.js-save-translation').text('Approve');
                    translation_area.attr('readonly','readonly');
                } else {
                    $('.js-save-translation').text('Save');
                    translation_area.removeAttr('readonly');
                }
            });
        }

        return {
            init: function () {
                translation_area = $('.js-translation-area');
                roles = $('.js-roles');
                editor_content_origin = $('div[data-role="origin"]');
                editor_content_translation = $('div[data-role="translation"]');

                translator_dashboard = $('.js-translator-dashboard');
                translator_dashboard.hide();
                translation_area.val('');

                editor_content_origin.on('click', '.js-sentence', function (e) {
                    setCurrentTranslation(getTranslationFromOriginal(this));
                });
                editor_content_translation.on('click', '.js-sentence', function (e) {
                    setCurrentTranslation(this);
                });
            
                initRoles();

                // Translator save
                $('.js-save-translation').on('click', function () {
                    save();
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
