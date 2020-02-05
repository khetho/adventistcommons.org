<?php

namespace App\TranslationMemory;

class Translator
{
    private $caller;

    public function __construct(Caller $caller)
    {
        $this->caller = $caller;
    }

    public function translate(string $source, string $translationLangCode, string $sourceLangCode = 'eng')
    {
        $response = $this->caller->searchTranslation($source, $translationLangCode, $sourceLangCode);

        return [
            'response' => $response,
        ];
    }
}
