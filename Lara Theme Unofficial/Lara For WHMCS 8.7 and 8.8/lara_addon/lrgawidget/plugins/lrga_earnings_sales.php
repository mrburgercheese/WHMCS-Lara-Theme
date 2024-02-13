<?php

namespace Lara\Widgets\GoogleAnalytics;

/**
 * @package    Google Analytics by Lara - Pro
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @link       https://www.whmcsadmintheme.com
 * @copyright  Copyright (c) WHMCSAdminTheme 2017
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

class lrga_earnings_sales{
	
	protected $storeObject;
	protected $rawData;
	protected $storeCurrencyPrefix;
	protected $storeCurrencySuffix;
	protected $salesLabel;
	protected $earningsLabel;	
	private $startDate;
	private $endDate;	
	private $sales;
	private $earnings;

	
	
	public function __construct($startDate, $endDate){
		$this->rawData = array();
		$this->startDate = $startDate;
		$this->endDate   = $endDate;
		$this->storeCurrencyPrefix = "";
		$this->storeCurrencySuffix = "";
		$this->getStoreCurrency();
	}

	protected function getStoreCurrency(){

	}
	
	protected function getSales(){
		if ((!is_array($this->rawData['sales'])) || (empty($this->rawData['sales']))) {
			$this->rawData['sales'] = $this->generateEmptyPeriodArray();
		}
		$this->sales = $this->prepareGraphData($this->rawData['sales']);

	}
	
	protected function getEarnings(){
		if ((!is_array($this->rawData['earnings'])) || (empty($this->rawData['earnings']))) {
			$this->rawData['earnings'] =  $this->generateEmptyPeriodArray();
		}		
		$this->earnings = $this->prepareGraphData($this->rawData['earnings']);
	}

	private function getMaxAxisValue($arr){
		$maxAxisValue =  max($arr);
		return $maxAxisValue;
	}
	
	private function getTotals($arr){
		$totals = array_sum($arr);
		return $totals;
	}
	
	private function prepareGraphData($arr){
		$preparedArray = array();
		foreach ($arr as $series){
			if (is_array($series) && !empty($series)){
				$finalArray = array();
				$finalArrayValues = array();

				foreach ($series["data"] as $id => $value){ 
					$finalArray[] = array($id, $value);
					$finalArrayValues[] = $value;
				}
				
				@array_walk($finalArray, array($this, 'convertDate'));
				$maxAxisValue = $this->getMaxAxisValue($finalArrayValues);
				$total = $this->getTotals($finalArrayValues);
				$preparedArray['series'][] = array("data"  			=> $finalArray,
												   "id"  		    => $series["id"],	
												   "label"  		=> $series["label"],
												   "color"  		=> $series["color"],
												   "total"			=> $total);												
				$preparedArray['config']['maxAxisValue'] = $preparedArray['config']['maxAxisValue'] + $maxAxisValue;
				$preparedArray['config']['Total'] = $preparedArray['config']['Total'] + $total;
			}
		}
			
		return $preparedArray;
	}

	private function convertDate(&$item){
		$item[0] = strtotime($item[0]." UTC") * 1000;
	}
	
	protected function generateEmptyPeriodArray(){
		$start = new \DateTime($this->startDate);
		$end   = new \DateTime($this->endDate);
		$end   = $end->modify( '+1 day' ); 
		$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
		$periods = iterator_to_array($period);
		foreach($periods as $date) { 
				$array[$date->format('Y-m-d')] = 0; 
		}
		return $array;
	}
	
	
	
	public function getGraphData(){
		$this->getRawData();
		$this->getSales();
		$this->getEarnings();
		
		$sales = array("config" => array("label" => $this->salesLabel,
						"lrbefore"=>"",
						"lrafter"=>"",
						"lrformat"=>"",
						"maxv"=> $this->sales['config']['maxAxisValue'],
						"total"=>$this->sales['config']['Total']),
						"series"  => $this->sales["series"]);

		$earnings = array("config" => array("label" => $this->earningsLabel,
						  "lrbefore"=>$this->storeCurrencyPrefix,
						  "lrafter"=>$this->storeCurrencySuffix,
						  "lrformat"=>"",
						  "maxv"=> $this->earnings['config']['maxAxisValue'],
						  "total"=>$this->earnings['config']['Total']),
						  "series"  =>  $this->earnings["series"]);
						  
		return array($sales,$earnings,$this->rawData['settings'],$this->rawData['options']);
	}
}
?>