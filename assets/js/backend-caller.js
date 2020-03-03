define(
    [
        'jquery',
        './router',
    ],function($, Router){

        const getLocale = function () {
            $('html').attr('lang');
        };
        
        const getContextVar = function(name){
            $('html').attr('data-' + name);           
         }

        return {
            callContentRevisionPut: function (sentenceId, content) {
                $.ajax({
                    type: "POST",
                    url: Router.generate('app_content_revision_put', {
                        'locale': getLocale(),
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    }),
                    data: content,
                    success: function (results) {
                        alert('ok');
                    }
                });
            }
        };
    }
);