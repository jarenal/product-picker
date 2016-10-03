#!/usr/bin/php
<?php

/********************************/
/* Controller class */
/********************************/
{controller}

/********************************/
/* Parser class */
/********************************/
{parser}

/********************************/
/* ProductPicker class */
/********************************/
{product_picker}

/********************************/
/* CLI script */
/********************************/
$message = <<<EOD

Usage:

	product-picker.php [options]

This script will find the optimal route through the warehouse for to pick the products.

Optional arguments:

  -h, --help          Show this help message.
  -i, --input         Input file path (CSV format).
  -o, --output        Output file path (CSV format).

Examples:

	Using short-name options:

        $ ./product-picker.php -i input.csv -o output.csv

    Using long-name options:

        $ ./product-picker.php --input=input.csv --output=output.csv


EOD;

if(count($argv)==1 || (isset($argv[1]) && in_array($argv[1], array("-h", "--help"))))
	die($message);

$parameters = array("input"=>"", "output"=>"");

foreach ($argv as $key => $argument) {

	// Parsing input
	if($argument == "-i") {
		if(isset($argv[$key+1]))
			$parameters["input"] = $argv[$key+1];
	} elseif(strpos($argument, "--input") !== false) {
		$parts = explode("=", $argument);
		$parameters["input"] = $parts[1];
	}

	// Parsing output
	if($argument == "-o")
	{
		if(isset($argv[$key+1]))
			$parameters["output"] = $argv[$key+1];
	} elseif(strpos($argument, "--output") !== false) {
		$parts = explode("=", $argument);
		$parameters["output"] = $parts[1];
	}

}

try {
	$controller = new Controller($parameters["input"], $parameters['output']);

	$controller->execute();

	echo "Execution was successful!\n";

} catch (\Exception $ex) {
	die($ex->getMessage()."\n");
}
