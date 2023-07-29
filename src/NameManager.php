<?php

namespace App;

use App\NameFormatter\Formatter;

final class NameManager
{
    public function __construct(private readonly Url $url, private readonly Formatter $formatter)
    {}

    private function format(string $string): string
    {
        return $this->formatter->format($string);
    }

    public function getPageName(): string
    {
        return $this->format($this->url->getUrlWithoutScheme()) . ".html";
    }

    public function getAssetName(string $asset): string
    {
        /**
         * @var array<string> $info
         */
        $info = pathinfo($asset);
        $extension = explode("?", $info["extension"])[0];

        $assetWithoutExtension = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'];

        return $this->format($this->url->getHost() . $assetWithoutExtension) . "." . $extension;
    }

    public function getFilesDirectoryName(): string
    {
        return $this->format($this->url->getUrlWithoutScheme()) . "_files";
    }
}
