<?php

namespace App\Client;

use App\Url;
use App\Client\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class GuzzleClient implements ClientInterface
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client();
    }

    public function getContents(Url $url): string
    {
        try {
            $content = $this->client->get($url->getUrl())->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new ClientException($e->getMessage());
        }

        return $content;
    }

    public function save(string $url, string $to): void
    {
        try {
            $this->client->request('GET', $url, ['sink' => $to]);
        } catch (GuzzleException $e) {
            throw new \App\Client\ClientException($e->getMessage());
        }
    }
}
