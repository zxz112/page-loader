<?php
namespace Test;

use App\HtmlParser;

final class HtmlParserTest extends \PHPUnit\Framework\TestCase
{
    private HtmlParser $htmlParser;

    public function testParsingAssets() :void
    {
        $this->configureHtmlParser("/fixtures/test.html");

        $expected = [
            "/assets/professions/php.png",
            "/assets/professions/php.svg",
            "/assets/professions/php.jpg",
            "https://test.com/assets/professions/php.png",
            "https://js.stripe.com/v3/",
            "https://test.com/packs/js/runtime.js",
            "https://cdn.test.com/assets/menu.css",
            "/assets/application.css",
        ];

        $assets = $this->htmlParser->getAssets();
        $this->assertEqualsCanonicalizing($expected, $assets);
    }

    public function testEmptyAssets() :void
    {
        $this->configureHtmlParser("/fixtures/empty.html");
        $assets = $this->htmlParser->getAssets();
        $this->assertEquals([], $assets);
    }

    private function configureHtmlParser($path)
    {
        $html = file_get_contents(__DIR__ . $path);
        $this->htmlParser = new \App\HtmlParser();
        $this->htmlParser->load($html);
    }
}
