<?php

namespace Jarenal;

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