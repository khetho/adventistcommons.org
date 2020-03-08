define(
    [
        'jquery',
    ],function($) {
        
        show = function() {
            $('.js-show-review').removeClass('active');
            $('.js-show-comments').addClass('active');

            $('.js-machine').collapse('hide');
            $('.js-revisions').collapse('hide');
            $('.js-comments').collapse('show');            
        }
        
        hide = function() {
            $('.js-comments').collapse('hide');
            $('.js-machine').collapse('show');

            $('.js-show-comments').removeClass('active');
        }
        
        return {
            init: function () {
                $('.js-show-comments').on('click', function () {
                    if ($('.js-comments').hasClass('show')) {
                        hide();
                    } else {
                        show();
                    }
                });
            },
            
            hide: function() {
                hide();
            },
        }
    }
);
