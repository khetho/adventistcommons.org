define(
    [
        'jquery',
    ],function($, Mark) {

        let editor_content_origin = null;
        let editor_content_translation = null;
        let translation_area = null;
        let roles = null;

        function setCurrentTranslation(translation) {
            setWorkingTranslation($(translation).html().trim());
            editor_content_origin.find('.s_selected').removeClass('s_selected');
            editor_content_translation.find('.s_selected').removeClass('s_selected');
            $(translation).addClass('s_selected');
            getOriginalFromTranslation(translation).addClass('s_selected');
        }

        function save() {
            let role = roles.find('.active').data('role');
            let translation = editor_content_translation.find('.s_selected');

            if (!translation.length) {
                console.log('Error: Nothing to save');
                return;
            }
            if (typeof role === 'undefined') {
                alert('You need choice a role');
                return;
            }

            switch (role) {
                case 'translator':
                    // generate ajax coll to backend here, after success:
                    translation.removeData('sentence-state').attr("data-sentence-state", 'translated');
                    translation.html(translation_area.val());
                    break;
                case 'proofreader':
                    // generate ajax coll to backend here, after success:
                    translation.removeData('sentence-state').attr("data-sentence-state", 'approved');
                    break;
                case 'reviewer':
                    // generate ajax coll to backend here, after success:
                    translation.removeData('sentence-state').attr("data-sentence-state", 'reviewed');
            }
        }

        function getTranslationFromOriginal(sentence) {
            return editor_content_translation.find('[data-concord="' + $(sentence).data('concord') + '"]');
        }

        function getOriginalFromTranslation(translation) {
            return editor_content_origin.find('[data-concord="' + $(translation).data('concord') + '"]');
        }

        function stripHtml(html)
        {
            const tmp = document.createElement("DIV");
            tmp.innerHTML = html;
            return tmp.textContent.trim() || tmp.innerText.trim() || "";
        }

        function setWorkingTranslation(content) {
            // TODO : check if unchanged
            translation_area.val(stripHtml(content));

            return true;
        }

        return function (a_editor_content_origin, a_editor_content_translation) {
            translation_area = $('#translation_area');
            roles = $('#roles');
            editor_content_origin = a_editor_content_origin;
            editor_content_translation = a_editor_content_translation;

            editor_content_origin.on('click', '.sentence', function (e) {
                setCurrentTranslation(getTranslationFromOriginal(this));
            });
            editor_content_translation.on('click', '.sentence', function (e) {
                setCurrentTranslation(this);
            });

            // Translator save
            $('.save-translation').on('click', function () {
                save();
            });

            // Move content
            let translation_tools = $('.chat-item, #translation-memory, #machine-translation');
            translation_tools.on('click', '.transfer', function (e) {
                setWorkingTranslation($(e.delegateTarget).find('.subject').text());
            });
        }
    }
);
