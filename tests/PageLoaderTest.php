<?php
namespace Test;

use App\PageLoader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class PageLoaderTest extends TestCase
{
    private \Psr\Http\Client\ClientInterface $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup('testDir');
    }

    public function testCorrectPageLoad()
    {
        $url = "https://test.com/courses";
        $directory = vfsStream::url('testDir') . "/test";

        $pageLoader = $this->buildPageLoader($url, __DIR__ . "/fixtures/page_with_image_default.html");
        $this->assertFalse($this->root->hasChild('test'));
        $pageLoader->loadPage($directory);
        $this->assertTrue($this->root->hasChild('test'));
        $this->assertTrue($this->root->hasChild('test/test-com-courses.html'));
    }

    public function testProcessAssets()
    {
        $fakeHtml = file_get_contents(__DIR__ . "/fixtures/page_with_image_default.html");

        file_put_contents(vfsStream::url('testDir') . DIRECTORY_SEPARATOR . "images.html", $fakeHtml);

        $url = "https://test.com/courses";

        $client = $this->createMock(\App\Client\ClientInterface::class);

        $client->expects($this->exactly(3))->method('save');

        $pageLoader =  new PageLoader($url, $client, $this->createMock(LoggerInterface::class));

        $pageLoader->processAssets(vfsStream::url('testDir') . DIRECTORY_SEPARATOR . "images.html", vfsStream::url('testDir'));

        $this->assertFileEquals(
            __DIR__ . "/fixtures/page_with_image_modified.html",
            vfsStream::url('testDir') . DIRECTORY_SEPARATOR . "images.html"
        );
    }

    public function testReplaceHtml()
    {
        $url = "https://test.com/courses";

        $assets = [
            "/assets/professions-php.png" => "test-com-courses_files/test-com-assets-professions-php.png",
            "/assets/professions-php2.png" => "test-com-courses_files/test-com-assets-professions-php2.png",
            "https://test.com/assets/professions/php3.png" => "test-com-courses_files/test-com-assets-professions-php3.png",
        ];

        $pageLoader = $this->buildPageLoader($url, __DIR__ . "/fixtures/page_with_image_default.html");

        $html = file_get_contents(__DIR__ . "/fixtures/page_with_image_default.html");

        $modifiedHtml = $pageLoader->replaceOriginalAssets($html, $assets);

        $this->assertStringEqualsFile(__DIR__ . "/fixtures/page_with_image_modified.html", $modifiedHtml);
    }

    private function buildPageLoader($url, $fixturePath)
    {
        $fakeHtml = file_get_contents($fixturePath);

        $client = $this->createMock(\App\Client\ClientInterface::class);
        $client->method('getContents')->willReturn((string)$fakeHtml);

        $logger = $this->createMock(LoggerInterface::class);

        return new PageLoader($url, $client, $logger);
    }
}
