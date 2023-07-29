<?php
namespace Test;

use App\NameFormatter\Formatter;
use App\NameManager;
use App\Url;
use PHPUnit\Framework\TestCase;

class NameManagerTest extends TestCase
{
    private readonly NameManager $nameManager;
    public function setUp(): void
    {
        parent::setUp();
        $url = $this->createMock(Url::class);
        $url->method("getUrlWithoutScheme")->willReturn("test.com");
        $url->method("getHost")->willReturn("test.com");

        $formatter = $this->createMock(Formatter::class);
        $formatter->method("format")->willReturnArgument(0);

        $this->nameManager = new NameManager(
            $url,
            $formatter
        );
    }

    public function testPageName()
    {
        $expected = "test.com.html";
        $this->assertEquals($expected, $this->nameManager->getPageName());
    }

    public function testAssetName()
    {
        $expected = "test.com/upload/images.png";

        $asset = "/upload/images.png";
        $this->assertEquals($expected, $this->nameManager->getAssetName($asset));

        $asset = "/upload/images.png?query=test";
        $this->assertEquals($expected, $this->nameManager->getAssetName($asset));
    }

    public function testFilesDirectoryName()
    {
        $this->assertEquals("test.com_files",  $this->nameManager->getFilesDirectoryName());
    }
}
