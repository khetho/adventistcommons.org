define(
    [
        'jquery',
        '../utils/backend-caller',
        './translation-history',
        '../utils/error-reporter',
        './translation-sentences',
        './translation-chat',
    ],function(
        $,
        BackendCaller,
        TranslationHistory,
        ErrorReporter,
        TranslationSentences,
        TranslationChat,
    ) {
        let translation_area = null;
        let roles = null;
        let translator_dashboard = null;
        let current_translation = '';
        let current_sentence_id = null;

        function setWorkingTranslation(content, sentence_id) {
            if (sentence_id) {
                current_sentence_id = sentence_id;
                TranslationSentences.setCurrentSentence(sentence_id);
            }
            current_translation = stripHtml(content);
            BackendCaller.callSentenceInfo(
                sentence_id,
                function (backendResponse) {
                    $('.js-revisions-count').html(backendResponse.data.revisions_count);
                    $('.js-comments-count').html(backendResponse.data.comments_count);
                }
            );
            translation_area.val(current_translation);
            translator_dashboard.show();
            initPanels();
        }

        function stripHtml(html)
        {
            const tmp = document.createElement("DIV");
            tmp.innerHTML = html;

            return tmp.textContent.trim() || tmp.innerText.trim() || "";
        }

        function save(successAction) {
            const role = roles.find('.active').data('role');
            if (typeof role === 'undefined') {
                ErrorReporter.report('You must choose a role');
                return;
            }

            const translation = TranslationSentences.getCurrentSentence();
            if (!translation.length) {
                return;
            }
            const sentenceId = translation.attr('data-sentence-id');

            switch (role) {
                case 'translator':
                    BackendCaller.callContentRevisionPut(
                        sentenceId,
                        translation_area.val(),
                        function() {
                            translation.removeData('sentence-state').attr("data-sentence-state", 'translated');
                            current_translation = translation_area.val();
                            translation.html(current_translation);
                            if (successAction) {
                                successAction();
                            }
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

        function hidePanels() {
            $('.js-machine').collapse('hide');
            hideOptionnalPanels();
        }

        function hideOptionnalPanels() {
            $('.js-comments-panel').collapse('hide');
            $('.js-revisions-panel').collapse('hide');

            $('.js-show-comments').removeClass('active');
            $('.js-show-review').removeClass('active');
        }

        function initPanels() {
            hideOptionnalPanels();
            $('.js-machine').collapse('show');
        }

        return {
            hidePanels: function() {
                hidePanels();
            },

            initPanels: function() {
                initPanels();
            },

            getCurrentSentenceId: function() {
                return current_sentence_id;
            },

            setWorkingTranslationIfOk: function(content, sentence_id) {
                if (translator_dashboard.is(":visible") && current_translation !== translation_area.val()) {
                    const modal = $('#changedTranslationModal');
                    modal.modal();
                    modal.find('[data-action="saveContinue"]').unbind('click').click(function(){
                        save(function() {
                            setWorkingTranslation(content, sentence_id);
                            modal.modal('hide');
                        });
                    });
                    modal.find('[data-action="undoContinue"]').unbind('click').click(function(){
                        setWorkingTranslation(content, sentence_id);
                        modal.modal('hide');
                    });
                } else {
                    setWorkingTranslation(content, sentence_id);
                }
            },

            init: function () {
                translator_dashboard = $('.js-translator-dashboard');
                translator_dashboard.hide();
                translation_area = $('.js-translation-area');
                translation_area.val('');

                roles = $('.js-roles');
                initRoles();

                TranslationSentences.init(this);
                TranslationChat.init(this);
                TranslationHistory.init(this);

                // Translator save
                $('.js-save-translation').on('click', function () {
                    save();
                });

                $('.js-next-translation').on('click', function () {
                    TranslationSentences.selectNextSentence();
                });

                $('.js-prev-translation').on('click', function () {
                    TranslationSentences.selectPrevSentence();
                });

                // Move content
                let translation_tools = $('.js-chat-item, #translation-memory, #machine-translation');
                translation_tools.on('click', '.js-transfer', function (e) {
                    this.setWorkingTranslationIfOk($(e.delegateTarget).find('.js-subject').text());
                });
            }
        }
    }
);
