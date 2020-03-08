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
            $('.js-show-comments').removeClass('active');
            $('.js-show-review').addClass('active');

            machine.collapse('hide');
            $('.js-comments').collapse('hide');
            revisions.collapse('show');
        }
        
        function hide() {
            revisions.collapse('hide');
            machine.collapse('show');

            $('.js-show-review').removeClass('active');            
        }

        return {
            init: function (transaction_working_area) {
                revisions = $('.js-revisions');
                machine = $('.js-machine');

                TranslationWorkingArea = transaction_working_area;

                $('.js-show-review').on('click', function () {
                    if (revisions.hasClass('show')) {
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
