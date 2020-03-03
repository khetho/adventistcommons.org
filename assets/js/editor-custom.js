define(
    [
        'jquery',
        './glossary',
        './chat',
        './translation',
    ],function($, Glossary, Chat, Translation) {

        let editor_content_origin = null;
        let editor_content_translation = null;

        function initPanels() {
            /**
             * TODO: Refactor
             */
            $('.js-show-comments').on('click', function () {
                if ($('.js-comments').hasClass('show')) {
                    $('.js-comments').collapse('hide');
                    $('.js-machine').collapse('show');

                    $('.js-show-comments').removeClass('active');
                } else {
                    $('.js-show-review').removeClass('active');
                    $('.js-show-comments').addClass('active');

                    $('.js-machine').collapse('hide');
                    $('.js-revisions').collapse('hide');
                    $('.js-comments').collapse('show');
                }
            });
            $('.js-show-review').on('click', function () {
                if ($('.js-revisions').hasClass('show')) {
                    $('.js-revisions').collapse('hide');
                    $('.js-machine').collapse('show');

                    $('.js-show-review').removeClass('active');
                } else {
                    $('.js-show-comments').removeClass('active');
                    $('.js-show-review').addClass('active');

                    $('.js-machine').collapse('hide');
                    $('.js-comments').collapse('hide');
                    $('.js-revisions').collapse('show');
                }
            });
        }

        $(document).ready(function () {
            editor_content_origin = $('div[data-role="origin"]');

            initPanels();

            // Translation system
            Translation.init();

            // Glossary
            Glossary.adventist(editor_content_origin);
            Glossary.wordApi(editor_content_origin);

            // Chat
            Chat.init($("js-comment"));
        });
    }
);
