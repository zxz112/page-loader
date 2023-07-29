<?php

namespace App;

use DiDom\Document;

final class HtmlParser
{
    private Document $document;

    public function __construct(string $html = null)
    {
        $this->document = new Document($html);
    }

    public function load(string $html, bool $isFile = false): void
    {
        $this->document->load($html, $isFile);
    }

    private function getImageUrls(): array
    {
        $imageUrls = [];
        $imageNodes = $this->document->find('[src$=png]');
        foreach ($imageNodes as $imageNode) {
            if ($src = $imageNode->getAttribute('src')) {
                $imageUrls[] = $src;
            }
        }

        $imageNodes = $this->document->find('[src$=jpg]');
        foreach ($imageNodes as $imageNode) {
            if ($src = $imageNode->getAttribute('src')) {
                $imageUrls[] = $src;
            }
        }

        $imageNodes = $this->document->find('[src$=svg]');
        foreach ($imageNodes as $imageNode) {
            if ($src = $imageNode->getAttribute('src')) {
                $imageUrls[] = $src;
            }
        }

        return $imageUrls;
    }

    private function getScriptUrls(): array
    {
        $scriptUrls = [];
        $scriptNodes = $this->document->find('script');
        foreach ($scriptNodes as $scriptNode) {
            if ($src = $scriptNode->getAttribute('src')) {
                $scriptUrls[] = $src;
            }
        }

        return $scriptUrls;
    }

    private function getLinkUrls(): array
    {
        $linkUrls = [];
        $linkNodes = $this->document->find('link');
        foreach ($linkNodes as $linkNode) {
            if ($linkNode->getAttribute('rel') == 'stylesheet'
                && $src = $linkNode->getAttribute('href')
            ) {
                $linkUrls[] = $src;
            }
        }

        return $linkUrls;
    }

    public function getAssets(): array
    {
        $images = $this->getImageUrls();
        $scripts = $this->getScriptUrls();
        $links = $this->getLinkUrls();

        return array_merge($images, $scripts, $links);
    }
}
