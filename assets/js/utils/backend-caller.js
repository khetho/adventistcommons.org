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
        
        const callByRouteName = function(routeName, routeParams, action, parent, method, data){
            routeParams = routeParams || {};
            routeParams._locale = getLocale();
            return call(
                Router.generate(routeName, routeParams || {}), action, parent, method, data
            )
            
        };

        const call = function(url, action, parent, method, data){
            // @TODO : add a loader somewhere
            $.ajax({
                type: method || 'GET',
                url: url,
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
                callByRouteName(
                    'app_content_revision_put',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction,
                    undefined,
                    'PUT',
                    {
                        "content":content,
                    },
                );
            },
            callContentRevisionApprove: function (sentenceId, successAction) {
                callByRouteName(
                    'app_content_revision_approve',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction,
                    undefined,
                    'POST',
                );
            },
            callContentRevisionReview: function (sentenceId, successAction) {
                callByRouteName(
                    'app_content_revision_review',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction,
                    undefined,
                    'POST',
                );
            },
            callContentRevisionHistory: function (sentenceId, parent) {
                callByRouteName(
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
                callByRouteName(
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
                callByRouteName(
                    'app_sentence_info',
                    {
                        'slug': getContextVar('slug'),
                        'languageCode': getContextVar('languageCode'),
                        'sentenceId': sentenceId,
                    },
                    successAction
                )
            },
            callWords: function (successAction) {
                $.ajax({
                    type: 'GET',
                    url: '/config/glossary.json',
                    contentType: 'application/json',
                    success: function (backendReturn) {
                        if (successAction) {
                            successAction(backendReturn);
                        }
                    },
                })
            },
        };
    }
);