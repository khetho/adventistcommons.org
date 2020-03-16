define(
    [
        'jquery',
        './modules/glossary',
        './modules/chat',
        './modules/translation-working-area',
    ],function(
        $,
        Glossary,
        Chat,
        Translation
    ) {
        $(document).ready(function () {

            // Translation system
            Translation.init();

            // Glossary
            const editor_content_origin = $('div[data-role="origin"]');
            Glossary.adventist(editor_content_origin);
            Glossary.wordApi(editor_content_origin);

            // Chat
            Chat.init($(".js-comment"));
        });
    }
);
