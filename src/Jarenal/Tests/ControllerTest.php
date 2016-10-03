<?php

use PHPUnit\Framework\TestCase;
use Jarenal\Controller;

class ControllerTest extends TestCase
{

    public function testOutputFileNotWritable()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(101);

        $input = dirname(__FILE__)."/Resources/success.csv";
        $output = dirname(__FILE__)."/Resources/not-writable.csv";
        chmod($output, 0444);
        $controller = new Controller($input, $output);
        $controller->execute();
    }

}