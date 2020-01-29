<?php

namespace App\Nlp;

use \GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class Client
{
    private $host;
    private $client;
    private $jsonDecode;

    public function __construct($nlpHost, DecoderInterface $jsonDecode)
    {
        $this->host = $nlpHost;
        $this->jsonDecode = $jsonDecode;
    }

    private function getClient(): GuzzleClient
    {
        if (!$this->client) {
            $this->client = new GuzzleClient([
                'base_uri' => $this->host,
            ]);
        }

        return $this->client;
    }

    public function splitParagraphIntoSentences(string $paragraph): array
    {
        $request = new Request('GET', '/snt', ['Content-Type' => 'application/json'], \GuzzleHttp\json_encode(['text' => $paragraph]));
        $response = $this->getClient()->send($request);
        $sntData = \GuzzleHttp\json_decode((string) $response->getBody(), true);
        if ($sntData['history'][0][1] !== 'success') {
            throw new ApiErrorException();
        }

        return $sntData['result'];
    }
}
