<?php

use PHPUnit\Framework\TestCase;
use Jarenal\Parser;
use Jarenal\ProductPicker;

class ProductPickerTest extends TestCase
{

    public function testSortingWithoutGroups()
    {

            $input = dirname(__FILE__)."/Resources/input-without-groups.csv";
            $output = dirname(__FILE__)."/Resources/output-without-groups.csv";

            $parser = new Parser($input);
            $picker = new ProductPicker($parser->getData());

            $expected = file_get_contents($output);
            $received = $picker->getData();

            $this->assertEquals($expected, $received);

    }

    public function testSortingWithGroups()
    {

            $input = dirname(__FILE__)."/Resources/input-with-groups.csv";
            $output = dirname(__FILE__)."/Resources/output-with-groups.csv";

            $parser = new Parser($input);
            $picker = new ProductPicker($parser->getData());

            $expected = file_get_contents($output);
            $received = $picker->getData();

            $this->assertEquals($expected, $received);

    }

    public function testSortingWithRepeatedProducts()
    {

            $input = dirname(__FILE__)."/Resources/input-with-repeated-products.csv";
            $output = dirname(__FILE__)."/Resources/output-with-repeated-products.csv";

            $parser = new Parser($input);
            $picker = new ProductPicker($parser->getData());

            $expected = file_get_contents($output);
            $received = $picker->getData();

            $this->assertEquals($expected, $received);

    }

}