define(
    [
        'jquery',
        'mark.js/dist/jquery.mark.js',
        '../../config/adventist-glossary'
    ],function($, Mark, AdventistGlossary){

        function setup_words(word, resWord, resDef, e, parent)
        {
            let editor_content = $(parent);
            let instance = new Mark(editor_content);
            instance.markRegExp(new RegExp("\\b" + word + "\\b", 'gim'), {
                "element" : "span",
                "className": "",
                "each" : htmlElement => {
                    let elemClass = salted(word.replace(/'/g,""));
                    htmlElement.classList.add(elemClass);
                },
            });

            $('.'+document.elementFromPoint(e.pageX - window.pageXOffset,e.pageY - window.pageYOffset).classList)
                .popover({
                    placement:'top',
                    trigger:'manual',
                    html:true,
                    title:resWord,
                    content:resDef,
                })
                .popover('show').addClass('wordapi_popover');
        }

        function salted (word) {
            return  word+'_'+Math.random().toString(36).substring(7);
        }

        return {
            adventist: function (container) {
                container.mark(Object.keys(AdventistGlossary), {
                    "element": "span",
                    "accuracy": "complementary",
                    "className": "adventist_terms",
                    "each": htmlElement => {
                        $.each(AdventistGlossary, function (term, def) {
                            if (htmlElement.textContent === term) {
                                htmlElement.classList.add(term);
                            }
                        });
                    },
                });
                $.each(AdventistGlossary, function (term, def) {
                    container
                        .find('.' + term)
                        .popover({
                            placement: 'top',
                            trigger: 'hover',
                            html: true,
                            title: 'Glossary term: ' + term,
                            content: def,
                        })
                        .on('click', function () {
                            $(this).popover('hide');
                        });
                });
            },

            wordApi: function(container) {
                container.on('dblclick', container, function (e) {
                    let range = window.getSelection() || document.getSelection() || document.selection.createRange();
                    let word = $.trim(range.toString().replace(/[.,\/#!$%\^&\*;:{}=\-_`~()]/g,""));

                    if(word !== '' && !$(e.target).hasClass('wordapi_popover') ) {
                        var settings = {
                            "async": true,
                            "crossDomain": true,
                            "url": 'https://wordsapiv1.p.rapidapi.com/words/'+word+'/definitions',
                            "method": "GET",
                            "headers": {
                                "x-rapidapi-host": "wordsapiv1.p.rapidapi.com",
                                "x-rapidapi-key": "31a8e8bfddmshc18ebaedde291e5p1ed469jsna7ca535fa9b4"
                            }
                        };

                        $.ajax(settings)
                            .done(function (response) {
                                var resWord = response.word;
                                var resDef = 'Definition not found';

                                if (response.definitions.length) {
                                    resDef = response.definitions[0].definition;
                                }

                                setup_words(word, resWord, resDef, e, container);
                            })
                            .fail(function (response) {
                                setup_words(word, 'Word not found', 'Definition not found', e);
                            });
                    } else if ($(e.target).hasClass('wordapi_popover')) {
                        $(e.target).popover('show');
                    }
                })
                .on('mouseleave click', '.wordapi_popover', function(e){
                    $(this).popover('hide');
                });
            }
        };
    }
);