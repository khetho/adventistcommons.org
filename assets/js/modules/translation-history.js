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

        function showHistory()
        {
            const newSentenceId = TranslationWorkingArea.getCurrentSentenceId();
            if (newSentenceId === sentenceId) {
                return;
            }
            sentenceId = newSentenceId;
            BackendCaller.callContentRevisionHistory(
                sentenceId,
                function() {
                    alert('ok');
                }
            );
        }

        return {
            init: function (transaction_working_area) {
                TranslationWorkingArea = transaction_working_area;

                $('.js-show-review').on('click', function () {
                    const revisions = $('.js-revisions');
                    const machine = $('.js-machine');
                    if (revisions.hasClass('show')) {
                        revisions.collapse('hide');
                        machine.collapse('show');

                        $('.js-show-review').removeClass('active');
                    } else {
                        showHistory();
                        $('.js-show-comments').removeClass('active');
                        $('.js-show-review').addClass('active');

                        machine.collapse('hide');
                        $('.js-comments').collapse('hide');
                        revisions.collapse('show');
                    }
                });
            }
        }
    }
);
