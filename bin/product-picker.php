#!/usr/bin/php
<?php

/********************************/
/* Controller class */
/********************************/
class Controller {

	private $input_file;
	private $output_file;

	public function __construct($input_file, $output_file){
		$this->input_file = $input_file;
		$this->output_file = $output_file;
	}

	public function execute(){

		try {

			// Reading input file
			$parser = new Parser($this->input_file);

			// Calculating the best way for to pick the products.
			$picker = new ProductPicker($parser->getData());

			// Generating output file
			$handle = @fopen($this->output_file, "w+");

			if($handle===false)
				throw new \Exception("Error trying to open file '{$this->output_file}'.", 101);

			fwrite($handle, $picker->getData());
			fclose($handle);

		} catch (\Exception $ex) {
			throw $ex;
		}

	}
}

/********************************/
/* Parser class */
/********************************/
class Parser {
	private $filename;

	public function __construct($filename){
		$this->filename = $filename;
	}

	public function getData(){
		try
		{
			if(!file_exists($this->filename))
				throw new \Exception("The file '{$this->filename}' doesn't exist.", 100);

			$data = array();

			$handle = @fopen($this->filename, "r");

			if($handle===false)
				throw new \Exception("Error trying to open file '{$this->filename}'.", 101);

			$extension = pathinfo($this->filename, PATHINFO_EXTENSION);

			if($extension !== "csv")
				throw new \Exception("Wrong file format. Use CSV file format instead.", 102);

			$i=0;

		    while (($row = fgetcsv($handle, 0, ",")) !== false) {

		    	// Removing title row
		    	if(!$i++)
	    			continue;

	    		$row = array_map("trim", $row);

				if(count($row)!==3)
					throw new \Exception("The CSV file must have 3 columns.", 103);

				if(!is_numeric($row[0]))
					throw new \Exception("The product code should be an integer in the line $i.", 104);

				if(!is_numeric($row[1]))
					throw new \Exception("The quantity should be an integer in the line number $i.", 105);

				if(!preg_match("/^([A-Z]|A[A-Z]) ([1-9]0?)$/", $row[2]))
					throw new \Exception("The pick location format is wrong in the line number $i.", 106);

		        $data[] = $row;


		    }

		    fclose($handle);

			return $data;

		} catch (\Exception $ex) {
			throw $ex;
		}
	}
}

/********************************/
/* ProductPicker class */
/********************************/
class ProductPicker {

	private $input_data;
	private $alphabet;
	private $bays_names_mapping=array();

	public function __construct($input_data){
		$this->input_data = $input_data;

		$this->alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','K','R','S','T','U','V','W','X','Y','Z');

		$prefix_list = array('','A');

		foreach ($prefix_list as $prefix)
		{
			for($c=0;$c<26;$c++)
			{
				$this->bays_names_mapping[] = $prefix.$this->alphabet[$c];
			}
		}

	}

	public function getData(){

		$tmp_data = $this->sortProducts($this->input_data);

		$data = "product_code,quantity,pick_location\n";

		foreach ($tmp_data as $key => $row) {
			$data .= implode(",", $row)."\n";
		}

		return substr($data, 0, -1);
	}

	private function sortProducts($input_data){
		$tmp_data = $this->expandColumns($input_data);
		$tmp_data = $this->groupByProduct($tmp_data);
		$tmp_data = $this->sortByBay($tmp_data);
		$tmp_data = $this->sortByShelf($tmp_data);
		$tmp_data = $this->joinColumns($tmp_data);
		return $tmp_data;
	}

	private function expandColumns($input_data){

		$tmp_data = array();

		foreach ($input_data as $key => $row) {
			$extra = explode(" ", $row[2]);
			$tmp_data[] = array($row[0],$row[1],$this->bayNameToKey($extra[0]),$extra[1]);
		}

		return $tmp_data;

	}

	private function joinColumns($input_data){

		$tmp_data = array();

		foreach ($input_data as $key => $row) {
			$tmp_data[] = array($row[0],$row[1],$this->keyToBayName($row[2])." ".$row[3]);
		}

		return $tmp_data;
	}

	private function groupByProduct($input_data){
		$tmp_data = array();

		foreach ($input_data as $key => $row) {

			$exist = $this->existProduct($row[0], $tmp_data);
			if($exist !== false)
				$tmp_data[$exist][1] += $row[1];
			else
				$tmp_data[] = $row;
		}

		return $tmp_data;
	}

	private function existProduct($product, $data){

		foreach ($data as $key => $row) {
			if($row[0]==$product)
				return $key;
		}

		return false;
	}

	private function sortByBay($input_data){

		$tmp_data = array();

		while($row = current($input_data))
		{
			$lower = array("key"=>key($input_data), "row"=>$row);

			while($row2 = next($input_data))
			{
				if($row2[2]<$lower["row"][2])
					$lower = array("key"=>key($input_data), "row"=>$row2);
			}

			$tmp_data[] = $lower["row"];

			unset($input_data[$lower["key"]]);
			reset($input_data);

		}

		return $tmp_data;
	}

	private function sortByShelf($input_data){

		$tmp_data = array();

		$groups = array();

		$g=-1;

		foreach ($input_data as $key => $row)
		{
			if(isset($previous) && $previous)
			{
				if($previous[2]==$row[2])
				{
					$groups[$g][] = $row;
					$previous = $row;
					continue;
				}
			}

			$previous = $row;
			$groups[++$g][] = $previous;
			continue;

		}

		while($group = current($groups))
		{
			while($row = current($group))
			{
				$lower = array("key"=>key($group), "row"=>current($group));

				while($row2 = next($group))
				{
					if($row2[3]<$lower["row"][3])
						$lower = array("key"=>key($group), "row"=>$row2);
				}

				$tmp_data[] = $lower["row"];

				unset($group[$lower["key"]]);

				reset($group);

			}

			next($groups);
		}


		return $tmp_data;
	}

	private function bayNameToKey($bay_name){
		$mapping = array_flip($this->bays_names_mapping);

		return $mapping[$bay_name];
	}

	private function keyToBayName($key){
		return $this->bays_names_mapping[$key];
	}
}

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
