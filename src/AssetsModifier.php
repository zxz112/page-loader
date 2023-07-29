<?php
namespace App;
final class AssetsModifier
{
    public function __construct(private readonly Url $url)
    {}

    public function modify(string $path): string|false
    {
        if (empty($path)) {
            return false;
        }
        /**
         * @var array<string> $parsedUrl
         */
        $parsedUrl = parse_url($path);

        if (!isset($parsedUrl['host'])) {
            return $path;
        }

        if ($this->url->getHost() == $parsedUrl['host']) {
            return $parsedUrl['path'];
        }

        return false;
    }
}