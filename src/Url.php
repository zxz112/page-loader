<?php

namespace App;

class Url
{
    private string $url;
    private string $host;
    private string $urlWithoutScheme;
    private string $scheme;

    public function __construct(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('incorrect url');
        }
        /**
         * @var array<string> $parsedUrl
         */
        $parsedUrl = parse_url($url);
        $this->url = $url;
        $this->host = $parsedUrl['host'];
        $this->scheme = $parsedUrl['scheme'];
        $this->urlWithoutScheme = $parsedUrl['host'];
        if (array_key_exists('path', $parsedUrl)) {
            $this->urlWithoutScheme .= $parsedUrl['path'];
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUrlWithoutScheme(): string
    {
        return $this->urlWithoutScheme;
    }
    public function getScheme(): string
    {
        return $this->scheme;
    }
}
