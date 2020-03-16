define(
    [
        'jquery',
        '../utils/backend-caller',
        'mark.js/dist/jquery.mark.js',
        '../utils/error-reporter',
    ],function(
        $,
        BackendCaller,
        Mark,
        ErrorReporter
    ) {
        let editor_content_origin = null;
        let editor_content_translation = null;
        let TranslationWorkingArea = null;
        const selected_class = 's_selected';

        function setCurrentSentenceIfOk(sentence_id) {
            TranslationWorkingArea.setWorkingTranslationIfOk(
                getTranslationFromSentenceId(sentence_id).html().trim(),
                sentence_id
            );
        }

        function getOriginalFromSentenceId(sentenceId) {
            return editor_content_origin.find('[data-sentence-id="' + sentenceId + '"]');
        }

        function getTranslationFromSentenceId(sentenceId) {
            return editor_content_translation.find('[data-sentence-id="' + sentenceId + '"]');
        }

        function getSentenceId(el) {
            return $(el).data('sentence-id');
        }

        return {
            getCurrentSentence: function () {
                const translation = editor_content_translation.find('.'+selected_class);
                if (!translation.length) {
                    ErrorReporter.report('Error: cannot find current translation');
                    return;
                }
                return translation;
            },

            setCurrentSentence: function(sentence_id) {
                editor_content_origin.find('.'+selected_class).removeClass(selected_class);
                editor_content_translation.find('.'+selected_class).removeClass(selected_class);
                getOriginalFromSentenceId(sentence_id).addClass(selected_class);
                getTranslationFromSentenceId(sentence_id).addClass(selected_class);
            },

            selectNextSentence: function () {
                let next = this.getCurrentSentence().next();
                if (!next.length) {
                    next = this.getCurrentSentence().closest('p').next().find('.js-sentence').first();
                }
                if (next.length) {
                    setCurrentSentenceIfOk(getSentenceId(next));
                }
            },

            selectPrevSentence: function () {
                let prev = this.getCurrentSentence().prev();
                if (!prev.length) {
                    prev = this.getCurrentSentence().closest('p').prev().find('.js-sentence').last();
                }
                if (prev.length) {
                    setCurrentSentenceIfOk(getSentenceId(prev));
                }
            },

            selectFirst: function () {
                const first = editor_content_origin.find('.js-sentence')[0];
                if (first) {
                    setCurrentSentenceIfOk(getSentenceId(first));
                }
            },

            markCurrentAs: function (targetState) {
                if (!['translated', 'approved', 'reviewed'].includes(targetState)) {
                    ErrorReporter.report('Unknown state : ' + targetState);
                }
                this.getCurrentSentence().removeData('sentence-state').attr("data-sentence-state", targetState);
            },

            init: function (translation_working_area) {
                TranslationWorkingArea = translation_working_area;
                editor_content_origin = $('div[data-role="origin"]');
                editor_content_translation = $('div[data-role="translation"]');

                editor_content_origin.on('click', '.js-sentence', function () {
                    setCurrentSentenceIfOk(getSentenceId(this));
                });
                editor_content_translation.on('click', '.js-sentence', function () {
                    setCurrentSentenceIfOk(getSentenceId(this));
                });
            }
        }
    }
);
