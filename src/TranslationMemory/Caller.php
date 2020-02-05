<?php

namespace App\TranslationMemory;

class Caller
{
    private $client;

    public function __construct(ClientBuilder $clientBuilder)
    {
        $this->client = $clientBuilder->build();
    }

    public function searchTranslation(string $source, $translationLangCode, $sourceLangCode = 'eng')
    {
        $query = [
            'index' => ClientBuilder::INDEX_NAME,
            'body' => [
                'query'=> [
                    'bool' => [
                        'should' => [
                            'match' => [
                                'source.text' => $source,
                            ],
                        ],
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'term' => [
                                            'source.lang'      => $sourceLangCode,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'translation.lang' => $translationLangCode,
                                        ],
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->client->search($query);
    }
}
