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
            $('#show_comments').on('click', function () {
                if ($('#comments').hasClass('show')) {
                    $('#comments').collapse('hide');
                    $('#machine').collapse('show');

                    $('#show_comments').removeClass('active');
                } else {
                    $('#show_review').removeClass('active');
                    $('#show_comments').addClass('active');

                    $('#machine').collapse('hide');
                    $('#revisions').collapse('hide');
                    $('#comments').collapse('show');
                }
            });
            $('#show_review').on('click', function () {
                if ($('#revisions').hasClass('show')) {
                    $('#revisions').collapse('hide');
                    $('#machine').collapse('show');

                    $('show_review').removeClass('active');
                } else {
                    $('#show_comments').removeClass('active');
                    $('#show_review').addClass('active');

                    $('#machine').collapse('hide');
                    $('#comments').collapse('hide');
                    $('#revisions').collapse('show');
                }
            });
        }

        function initRoles() {
            $('.role > button').on('click', function () {
                $(this).addClass('active');
                $('.role > button').not($(this)).removeClass('active');

                if ($(this).hasClass('proof') || $(this).hasClass('rev')) {
                    $('.save-translation').text('Approve');
                } else {
                    $('.save-translation').text('Save');
                }
            });
        }

        $(document).ready(function () {
            editor_content_origin = $('div[data-role="origin"]');
            editor_content_translation = $('div[data-role="translation"]');

            initRoles();
            initPanels();

            // Translation system
            Translation(
                editor_content_origin,
                editor_content_translation
            );

            // Glossary
            Glossary.adventist(editor_content_origin);
            Glossary.wordApi(editor_content_origin);

            // Chat
            Chat.init($("#comment"));
        });
    }
);
