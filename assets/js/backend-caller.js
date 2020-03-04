define(
    [
        'jquery',
        './router',
    ],function($, Router){

        const getLocale = function () {
            return $('html').attr('lang');
        };
        
        const getContextVar = function(name){
            return $('body').attr('data-' + name);
        }

        const call = function(routeName, routeParams, method, data, successAction){
            // @TODO : add a loader somewhere
            routeParams = routeParams || {};
            routeParams._locale = getLocale();
            $.ajax({
                type: method || 'GET',
                url: Router.generate(routeName, routeParams || {}),
                contentType: 'application/json',
                data: JSON.stringify(data || {}),
                success: function (results) {
                    if (results.status === 'created' || results.status === 'no-action') {
                        if (successAction) {
                            successAction();
                        }
                    } else {
                        alert('An error occurred during the action');
                    }
                },
                error: function (results) {
                    alert('An error occurred during the action');
                },
            });
        }

        return {
            callContentRevisionPut: function (sentenceId, content, successAction) {
                call(
                    'app_content_revision_put',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    'PUT',
                    {
                        "content":content,
                    },
                    successAction
                );
            }
        };
    }
);