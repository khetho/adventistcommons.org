<?php

namespace App\TranslationMemory;

class Indexer
{
    private $client;

    public function __construct(ClientBuilder $clientBuilder)
    {
        $this->client = $clientBuilder->build();
    }

    public function index(string $sourceLanguageCode, string $source, string $translationLangCode, string $translation)
    {
        $this->client->index(self::formatObject($sourceLanguageCode, $source, $translationLangCode, $translation));
    }

    private static function formatObject(string $sourceLangCode, string $source, string $translationLangCode, string $translation)
    {
        return [
            'index' => ClientBuilder::INDEX_NAME,
            'id' => sprintf('%s:%s:%s', md5($source), $sourceLangCode, $translationLangCode),
            'body' => [
                'source' => [
                    'lang'  => $sourceLangCode,
                    'text' => $source,
                ],
                'translation' => [
                    'lang'  => $translationLangCode,
                    'text' => $translation,
                ],
            ],
        ];
    }
}
