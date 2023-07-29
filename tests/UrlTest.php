<?php
namespace Test;

use App\Url;

final class UrlTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Url("");

        $this->expectException(\InvalidArgumentException::class);
        new Url("test.com");

        $this->expectException(\InvalidArgumentException::class);
        new Url("https://test");
    }

    public function testCorrectUrl()
    {
        $correctUrl = "https://test.com";
        $url = new Url($correctUrl);

        $this->assertEquals($correctUrl, $url->getUrl());
        $this->assertEquals("test.com", $url->getHost());
        $this->assertEquals("test.com", $url->getUrlWithoutScheme());
        $this->assertEquals("https", $url->getScheme());
    }
}