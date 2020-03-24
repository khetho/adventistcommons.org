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

        function doActionIfTranslationIsOk(action) {
            if (translator_dashboard.is(":visible") && current_translation !== translation_area.val()) {
                const modal = $('#changedTranslationModal');
                modal.modal();
                modal.find('[data-action="saveContinue"]').unbind('click').click(function(){
                    save(function() {
                        action();
                        modal.modal('hide');
                    });
                });
                modal.find('[data-action="undoContinue"]').unbind('click').click(function(){
                    action();
                    modal.modal('hide');
                });
            } else {
                action();
            }
        }

        /**
         * Check if current content has change from saved, propose to save if not
         * @param content
         * @param sentence_id
         */
        function setWorkingTranslationIfOk(content, sentence_id) {
            doActionIfTranslationIsOk(function() {
                setWorkingTranslation(content, sentence_id);
            });
        }

        /**
         * Go to next page if the current working translation is ok
         */
        function goNextSectionIfOk() {
            doActionIfTranslationIsOk(function() {
                document.getElementById("js-link-next").click();
            });
        }

        /**
         * Set the current translation content and may change also the sentence to translate
         * @param content
         * @param sentence_id
         */
        function setWorkingTranslation(content, sentence_id ) {
            if (sentence_id) {
                if (sentence_id !== current_sentence_id) {
                    BackendCaller.callSentenceInfo(
                        sentence_id,
                        function (backendResponse) {
                            $('.js-revisions-count').html(backendResponse.data.revisions_count);
                            $('.js-comments-count').html(backendResponse.data.comments_count);
                        }
                    );
                }
                current_sentence_id = sentence_id;
                current_translation = cleanUpHtml(content);
                TranslationSentences.setCurrentSentence(sentence_id);
                initPanels();
                translator_dashboard.show();
                translation_area.focus();
            }
            translation_area.val(cleanUpHtml(content));
        }

        /**
         * Remove html taqs, and content inside <del» tags too
         * @param html
         * @returns {string | string}
         */
        function cleanUpHtml(html)
        {
            const tmpDiv = document.createElement("DIV");
            // Remove <DEL> tags and their contents, when content is coming from history
            tmpDiv.innerHTML = html.replace(/<del([\S\s]*?)>([\S\s]*?)<\/del>/ig, "");
            // remove all tags
            return tmpDiv.textContent.trim() || tmpDiv.innerText.trim() || "";
        }

        /**
         * Save the translation !
         * @param successAction
         */
        function save(successAction) {
            let role = 'translator';
            if (roles.length) {
                role = roles.find('.active').data('role');
                if (typeof role === 'undefined') {
                    ErrorReporter.report('You must choose a role');
                    return;
                }
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
                            TranslationSentences.markCurrentAs('translated');
                            current_translation = translation_area.val();
                            translation.html(current_translation);
                            TranslationSentences.selectNextSentence();
                            if (successAction) {
                                successAction();
                            }
                        }
                    );
                    break;
                case 'proofreader':
                    BackendCaller.callContentRevisionApprove(
                        sentenceId,
                        function() {
                            TranslationSentences.markCurrentAs('approved');
                            TranslationSentences.selectNextSentence();
                        }
                    );
                    break;
                case 'reviewer':
                    BackendCaller.callContentRevisionReview(
                        sentenceId,
                        function() {
                            TranslationSentences.markCurrentAs('reviewed');
                            TranslationSentences.selectNextSentence();
                        }
                    );
                    break;

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
                return setWorkingTranslationIfOk(content, sentence_id);
            },

            goNextSectionIfOk: function() {
                goNextSectionIfOk();
            },

            init: function () {
                translator_dashboard = $('.js-translator-dashboard');
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
                TranslationSentences.selectFirst();

                translation_area.keydown(function(event) {
                    if (event.keyCode === 13 /* ENTER */) {
                        save();
                        return false;
                    }
                });

                $('body').keydown(function(event) {
                    if (event.altKey && event.keyCode === 38 /* UP ARROW */) {
                        TranslationSentences.selectPrevSentence();
                    } else if (event.altKey && event.keyCode === 40 /* DOWN ARROW */) {
                        TranslationSentences.selectNextSentence();
                    }
                });

                translation_area.keypress(function(e) {
                    const c = String.fromCharCode(e.which);
                    if (c.toUpperCase() === c && c.toLowerCase() !== c && !e.shiftKey) {
                        $('#message').show();
                    } else {
                        $('#message').hide();
                    }
                });

                // Move content
                translator_dashboard.on('click', '.js-transfer-button', function (e) {
                    setWorkingTranslationIfOk($(e.target).closest('.js-transfer-group').find('.js-transfer-subject').html());
                });
            }
        }
    }
);
