<?php

namespace App\TranslationMemory;

use Elasticsearch\ClientBuilder as EsClientBuilder;

class ClientBuilder
{
    const INDEX_NAME = 'tm';

    private $elasticSearchHost;

    public function __construct($elasticSearchHost)
    {
        $this->elasticSearchHost = $elasticSearchHost;
    }

    public function build()
    {
        return EsClientBuilder::create()
            ->setHosts(explode(',', $this->elasticSearchHost))
            ->build();
    }
}
