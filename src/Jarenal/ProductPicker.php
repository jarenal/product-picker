<?php

namespace Jarenal;

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