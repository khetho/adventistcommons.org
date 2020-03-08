define(
    [
        'jquery',
        './router',
        './error-reporter',
    ],function($, Router, ErrorReporter){

        const getLocale = function () {
            return $('html').attr('lang');
        };
        
        const getContextVar = function(name){
            return $('body').attr('data-' + name);
        };

        const call = function(routeName, routeParams, successAction, method, data){
            // @TODO : add a loader somewhere
            routeParams = routeParams || {};
            routeParams._locale = getLocale();
            $.ajax({
                type: method || 'GET',
                url: Router.generate(routeName, routeParams || {}),
                contentType: 'application/json',
                data: data ? JSON.stringify(data) : null,
                success: function (backendReturn) {
                    if (backendReturn.status === 'success') {
                        if (successAction) {
                            successAction();
                        }
                        console.log(backendReturn);
                        if (backendReturn.html && backendReturn.target) {
                            $(backendReturn.target).html(backendReturn.html);
                        }
                    } else {
                        ErrorReporter.report('A client error occurred during the action');
                    }
                },
                error: function () {
                    ErrorReporter.report('A server error occurred during the action');
                },
            });
        };

        return {
            callContentRevisionPut: function (sentenceId, content, successAction) {
                call(
                    'app_content_revision_put',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction,
                    'PUT',
                    {
                        "content":content,
                    },
                );
            },
            callContentRevisionHistory: function (sentenceId, successAction) {
                call(
                    'app_content_revision_history',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction
                );
            },
        };
    }
);