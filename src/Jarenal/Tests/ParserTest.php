<?php

use PHPUnit\Framework\TestCase;
use Jarenal\Parser;

class ParserTest extends TestCase
{

    public function testFileDoesNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(100);

        $parser = new Parser("abc.txt");
        $parser->getData();
    }

    public function testNotReadable()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(101);

        $filename = dirname(__FILE__)."/Resources/not-readable.txt";
        chmod($filename, 0222);
        $parser = new Parser($filename);
        $parser->getData();
    }

    public function testWrongExtension()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(102);

        $filename = dirname(__FILE__)."/Resources/wrong-extension.txt";
        $parser = new Parser($filename);
        $parser->getData();
    }

    public function testIncorrectColumnNumber()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(103);

        $filename = dirname(__FILE__)."/Resources/incorrect-column-number.csv";
        $parser = new Parser($filename);
        $parser->getData();
    }

    public function testProductCodeIsInteger()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(104);
        $this->expectExceptionMessage("The product code should be an integer in the line 3.");

        $filename = dirname(__FILE__)."/Resources/product-code-not-integer.csv";
        $parser = new Parser($filename);
        $parser->getData();
    }

    public function testQuantityIsInteger()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(105);
        $this->expectExceptionMessage("The quantity should be an integer in the line number 4.");

        $filename = dirname(__FILE__)."/Resources/quantity-not-integer.csv";
        $parser = new Parser($filename);
        $parser->getData();
    }

    public function testPickLocationFormat()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(106);
        $this->expectExceptionMessage("The pick location format is wrong in the line number 5.");

        $filename = dirname(__FILE__)."/Resources/pick-location-wrong-format.csv";
        $parser = new Parser($filename);
        $parser->getData();
    }

    public function testSuccess()
    {
        $filename = dirname(__FILE__)."/Resources/success.csv";
        $parser = new Parser($filename);
        $expected = array();
        $expected[] = array(15248,10,"AB 10");
        $expected[] = array(25636,1,"C 8");
        $expected[] = array(26982,1,"AF 7");

        $this->assertEquals($expected, $parser->getData());
    }

}