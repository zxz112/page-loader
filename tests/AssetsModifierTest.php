<?php
namespace Test;

use App\AssetsModifier;

final class AssetsModifierTest extends \PHPUnit\Framework\TestCase
{
    private $assetsModifier;
    public function setUp(): void
    {
        $url = new \App\Url("https://test.com/courses");
        $this->assetsModifier = new AssetsModifier($url);
        parent::setUp();
    }

    public function testRelativePath()
    {
        $path = "/assets/professions/php.png";
        $expected = "/assets/professions/php.png";

        $result = $this->assetsModifier->modify($path);

        $this->assertEquals($expected, $result);
    }

    public function testAbsolutePath()
    {
        $path = "https://test.com/assets/professions/php.png";
        $expected = "/assets/professions/php.png";

        $result = $this->assetsModifier->modify($path);

        $this->assertEquals($expected, $result);
    }

    public function testAnotherServerPath()
    {
        $path = "https://test.io/assets/professions/php.png";

        $result = $this->assetsModifier->modify($path);

        $this->assertFalse($result);
    }

    public function testEmptyPath()
    {
        $path = "";

        $result = $this->assetsModifier->modify($path);

        $this->assertFalse($result);
    }
}
