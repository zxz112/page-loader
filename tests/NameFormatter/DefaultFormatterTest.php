<?php
namespace Test\NameFormatter;

use App\NameFormatter\DefaultFormatter;
use PHPUnit\Framework\TestCase;

class DefaultFormatterTest extends TestCase
{
    private readonly DefaultFormatter $formatter;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->formatter = new DefaultFormatter();
    }

    public function testFormat()
    {
        $expected = "images-upd-test";
        $formattedName = $this->formatter->format("images/upd/test");
        $this->assertEquals($expected, $formattedName);
    }

    public function testFormatWithSlash()
    {
        $expected = "images-upd-test";
        $formattedName = $this->formatter->format("images/upd/test/");
        $this->assertEquals($expected, $formattedName);
    }
}