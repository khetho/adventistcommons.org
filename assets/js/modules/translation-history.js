define(
    [
        'jquery',
        './../utils/backend-caller',
    ],function(
        $,
        BackendCaller,
    ) {
        let TransactionWorkingArea = null;
        let sentenceId = null;
        let revisions = null;
        let machine = null;
        let panel = null;

        function showHistory()
        {
            const newSentenceId = TranslationWorkingArea.getCurrentSentenceId();
            if (newSentenceId === sentenceId) {
                return;
            }
            sentenceId = newSentenceId;
            BackendCaller.callContentRevisionHistory(
                sentenceId
            );
        }
        
        function show() {
            showHistory();
            revisions.html(revisions.data('loading'));
            $('.js-show-comments').removeClass('active');
            $('.js-show-review').addClass('active');

            machine.collapse('hide');
            $('.js-comments').collapse('hide');
            panel.collapse('show');
        }
        
        function hide() {
            panel.collapse('hide');
            machine.collapse('show');

            $('.js-show-review').removeClass('active');            
        }

        return {
            init: function (transaction_working_area) {
                panel = $('.js-revisions-panel');
                machine = $('.js-machine');
                revisions = $('.js-revisions')

                TranslationWorkingArea = transaction_working_area;

                $('.js-show-review').on('click', function () {
                    if (panel.hasClass('show')) {
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
