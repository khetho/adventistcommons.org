define(
    [
        'jquery',
        './../utils/backend-caller',
    ],function(
        $,
        BackendCaller,
    ) {
        let TranslationWorkingArea = null;
        let sentenceId = null;
        let content = null;
        let panel = null;
        let button = null;

        function retrieveContent()
        {
            const newSentenceId = TranslationWorkingArea.getCurrentSentenceId();
            if (newSentenceId === sentenceId) {
                return;
            }
            content.html(content.data('loading'));
            sentenceId = newSentenceId;
            BackendCaller.callContentRevisionHistory(
                sentenceId,
                content
            );
        }
        
        function show() {
            TranslationWorkingArea.hidePanels();
            retrieveContent();
            button.addClass('active');
            panel.collapse('show');
        }
        
        function hide() {
            TranslationWorkingArea.initPanels();
        }

        return {
            init: function (transaction_working_area) {
                TranslationWorkingArea = transaction_working_area;
                panel = $('.js-revisions-panel');
                button = $('.js-show-review');
                content = $('.js-revisions');

                button.on('click', function () {
                    if (button.hasClass('active')) {
                        hide();
                    } else {
                        show();
                    }
                });
            },
            
            hide: function () {
                hide();
            },
        }
    }
);
