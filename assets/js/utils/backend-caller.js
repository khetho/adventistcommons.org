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

        const call = function(routeName, routeParams, action, parent, method, data){
            // @TODO : add a loader somewhere
            routeParams = routeParams || {};
            routeParams._locale = getLocale();
            $.ajax({
                type: method || 'GET',
                url: Router.generate(routeName, routeParams || {}),
                contentType: 'application/json',
                data: data ? JSON.stringify(data) : null,
                success: function (backendReturn) {
                    if (action) {
                        action(backendReturn);
                    }
                    if (parent && backendReturn.html) {
                        parent.html(backendReturn.html);
                    }
                    if (backendReturn.status !== 'success') {
                        ErrorReporter.report('A client error occurred during the action');
                    }
                },
                error: function () {
                    if (parent) {
                        parent.html('A server error occurred during the action');
                    }
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
            callContentRevisionHistory: function (sentenceId, parent) {
                call(
                    'app_content_revision_history',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    undefined,
                    parent
                );
            },
            callSentenceComments: function (sentenceId, parent) {
                call(
                    'app_comment_for_sentence',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    undefined,
                    parent
                );
            },
            callSentenceInfo: function (sentenceId, successAction) {
                call(
                    'app_sentence_info',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction
                )
            }
        };
    }
);