<?php

namespace Jarenal;

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