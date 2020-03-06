define(
    [
        'jquery',
        '../utils/backend-caller',
        'mark.js/dist/jquery.mark.js',
        './translation-working-area',
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
                const translation = editor_content_translation.find('.s_selected');
                if (!translation.length) {
                    ErrorReporter.report('Error: cannot find current translation');
                    return;
                }
                return translation;
            },

            setCurrentSentence: function(sentence_id) {
                editor_content_origin.find('.s_selected').removeClass('s_selected');
                editor_content_translation.find('.s_selected').removeClass('s_selected');
                getOriginalFromSentenceId(sentence_id).addClass('s_selected');
                getTranslationFromSentenceId(sentence_id).addClass('s_selected');
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

            init: function (translationWorkingArea) {
                TranslationWorkingArea = translationWorkingArea;
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
