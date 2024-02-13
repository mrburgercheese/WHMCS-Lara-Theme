<?php

namespace Lara\Widgets\GoogleAnalytics;

use Lara\Utils\Common as Common;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @package    Google Analytics by Lara - Pro
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @link       https://www.whmcsadmintheme.com
 * @copyright  Copyright (c) WHMCSAdminTheme 2017
 */

require_once("lrga_earnings_sales.php");

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

class lrga_whmcs_plugin extends lrga_earnings_sales{
	private  $whmcsStartDate;
	private  $whmcsEndDate;
	private  $periodArray       = array();
	private  $graphSettings     = array();
	private  $graphDataGroups   = array();
	private  $processedProducts = array();
	private  $rawSales;
	private  $rawEarnings;
	private  $graphType;
	private  $graphMode;	
	private  $productIds = array();
	
	
	
	public function __construct($startDate, $endDate){
		$this->whmcsStartDate = new \DateTime($startDate);
		$this->whmcsEndDate   = new \DateTime($endDate);
		$this->whmcsEndDate   = $this->whmcsEndDate->modify( '+1 day' ); 
		$this->salesLabel     = "All Orders";
		$this->earningsLabel  = "Income";
		parent::__construct($startDate, $endDate);
	}

	protected function getStoreCurrency(){
		try{
			$results = Capsule::table("tblcurrencies")->where('default', 1)
			                                          ->first();

			if ((!empty($results->prefix)) && (!empty($results->suffix))){
				$this->storeCurrencyPrefix  = $results->prefix;
			}elseif (!empty($results->suffix)){
				$this->storeCurrencySuffix  = $results->suffix;
			}else{
				$this->storeCurrencyPrefix  = $results->prefix;
			}
			
		} catch (\Exception $e) {
			Common\ErrorHandler::FatalError("Cannot get default currency. Returned error: ". $e->getMessage());
		}		
	}
	
	private function getGraphSettings(){
		$currentUser = $_SESSION['adminid'];
		try{
			$results = Capsule::table("mod_laraUserSettings")->where('admin_id','=',$currentUser)
															 ->where('name','=',"lrgraphsettings")
															 ->first();
		} catch (\Exception $e) {
			Common\ErrorHandler::FatalError("Cannot get graph settings. Returned error: ". $e->getMessage());
		}
		
		if (!empty($results->value)){
			$this->graphSettings = json_decode($results->value, true);
		}else{//return default settings
			$this->graphSettings = array( "settings"  => array("type" => "types", "mode" => "all", "showempty" => "off", "showtotal" => "on"),
										  "types"     => array("lrdomain"        => array("on","#F8B195"),
															   "hostingaccount"  => array("on","#F67280"),
															   "reselleraccount" => array("on","#C06C84"),	
															   "server" 		 => array("on","#6C5B7B"),
															   "other" 		 	 => array("on","#355C7D"))													   
															   );
		}

		$this->graphType = $this->graphSettings["settings"]["type"];
		$this->graphMode = $this->graphSettings["settings"]["mode"];		
	}
	
	public function setGraphOptions($options){
		$currentUser = $_SESSION['adminid'];
		$finalOptionsArray = array();
		
		if (!empty($options["settings"]) && is_array($options["settings"])){
			$finalOptionsArray["settings"] = $options["settings"];
		}
		
		if (!empty($options["colors"]) && is_array($options["colors"])){
			foreach ($options["colors"] as $group => $colors){
				foreach ($colors as $productID => $color){
					$status = "off";
					if (!empty($options[$group]) && is_array($options[$group]) && (in_array($productID, $options[$group])) ){
						$status = "on";
					}	
					$finalOptionsArray[$group][$productID] = array($status, $color);
				}
			}
		}
		
		$finalOptionsJson = json_encode($finalOptionsArray, true);
		
		try{
			$exists = Capsule::table("mod_laraUserSettings")->where('admin_id','=',$currentUser)
															->where('name','=',"lrgraphsettings")
															->first();
			if (!empty($exists)){
				Capsule::table("mod_laraUserSettings")->where('admin_id','=',$currentUser)
													  ->where('name','=',"lrgraphsettings")
													  ->update(array('value' => $finalOptionsJson));
			}else{
				Capsule::table("mod_laraUserSettings")->insert(array('admin_id'=> $currentUser, 'name' => "lrgraphsettings", 'value' => $finalOptionsJson));
			}
		} catch (\Exception $e) {
			Common\ErrorHandler::FatalError("cannot save ".$name." returned error: ". $e->getMessage());
		}		
	}	
	
	private function getGraphDataGroups(){
		$this->getGraphSettings();

		try{
			$results = Capsule::table("tblproducts as p")->leftJoin('tblproductgroups as g', 'p.gid', '=', 'g.id')
														 ->select(['p.id as product_id', 'p.name as product_name', 'p.type as product_type', 'p.gid as product_group_id', 'g.name as product_group_name'])
														 ->get();
																   
			$products = json_decode(json_encode($results), true);	
			$products['lrdomain'] = array("product_id"  	   => "lrdomain",
										  "product_name"	   => "Domain Names",
										  "product_type"	   => "lrdomain",
										  "product_group_id"   => "lrdomain",
										  "product_group_name" => "Domain Names");
										  
			$products['other'] =  array("product_id"  	     => "other",
										"product_name"	     => "Other Products & Addons",
										"product_type"	     => "other",
										"product_group_id"   => "other",
										"product_group_name" => "Other Products & Addons");										  
			

		} catch (\Exception $e) {
			Common\ErrorHandler::FatalError("Cannot get default currency. Returned error: ". $e->getMessage());
		}
		
		foreach ($products as $product){
			$this->processedProducts[$product['product_id']] =  array("id"    => $product['product_id'],
																	  "name"  => $product['product_name'],
																	  "type"  => $product['product_type'],
																	  "gid"   => $product['product_group_id'],
																	  "gname" => $product['product_group_name']);

			$this->graphDataGroups['products'][$product['product_group_id']][] = array("id"    => $product['product_id'],
																					   "name"  => $product['product_name'],
																					   "gname" => $product['product_group_name'],
																					   "state" => $this->graphSettings["products"][$product['product_id']][0],
																					   "color" => $this->getColor($this->graphSettings["products"][$product['product_id']][1]));
			if (empty($this->graphDataGroups['groups'][$product['product_group_id']])){
				$this->graphDataGroups['groups'][$product['product_group_id']] = array("id"    =>$product['product_group_id'],
																					   "name"  => $product['product_group_name'],
																					   "state" => $this->graphSettings["groups"][$product['product_group_id']][0],
																					   "color" => $this->getColor($this->graphSettings["groups"][$product['product_group_id']][1]));
			}
		}
		
		$this->graphDataGroups['types'] = array("lrdomain"        => array( "id"    => "lrdomain",
																			"name"  => "Domain Names",
																			"state" => $this->graphSettings["types"]["lrdomain"][0],
																			"color" => $this->getColor($this->graphSettings["types"]["lrdomain"][1])),
												"hostingaccount"  => array( "id"    => "hostingaccount",
																			"name"  => "Shared Hosting",
																			"state" => $this->graphSettings["types"]["hostingaccount"][0],
																			"color" => $this->getColor($this->graphSettings["types"]["hostingaccount"][1])),
												"reselleraccount" => array( "id"    => "reselleraccount",
																			"name"  => "Reseller Accounts",
																			"state" => $this->graphSettings["types"]["reselleraccount"][0],
																			"color" => $this->getColor($this->graphSettings["types"]["reselleraccount"][1])),
												"server"          => array( "id"    => "server",
																			"name"  => "VPS/Servers",
																			"state" => $this->graphSettings["types"]["server"][0],
																			"color" => $this->getColor($this->graphSettings["types"]["server"][1])),
												"other"           => array( "id"    => "other",
																			"name"  => "Other Products & Addons",
																			"state" => $this->graphSettings["types"]["other"][0],
																			"color" => $this->getColor($this->graphSettings["types"]["other"][1])));	
	}
	
	private function getSeriesName($graphType, $productID){
		$productSeries = "";
		if ($graphType == "products"){$productSeries = $this->processedProducts[$productID]["id"];}
		elseif ($graphType == "groups"){$productSeries = $this->processedProducts[$productID]["gid"];}
		elseif ($graphType == "types"){$productSeries =  $this->processedProducts[$productID]["type"];} 
		return $productSeries;
	}
	
	private function getColor($color){
		$colors = array("#D32F2F", "#C2185B", "#7B1FA2", "#512DA8", "#303F9F", "#1976D2", "#0288D1", "#0097A7", "#00796B", "#388E3C", "#689F38", "#F57C00", "#E64A19", "#5D4037");
		if (empty($color)){
			$color = $colors[array_rand($colors, 1)];
			//$color =  '#'.str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
		}
		return $color;
	}	
	
	private function createGraphDataArray($graphDataGroups){
		foreach ($graphDataGroups as $type){
			$id = $this->graphSettings["settings"]["type"]."_".$type["id"];
			if ($type['state'] == "on"){
				$this->productIds[] = $type["id"];
				$this->rawSales["series_".$type["id"]]     =  array("id" =>$id, "label" => $type["name"], "color" => $type["color"], "data"  => $this->periodArray);
				$this->rawEarnings["series_".$type["id"]]  =  array("id" =>$id, "label" => $type["name"], "color" => $type["color"], "data"  => $this->periodArray);
			}
		}		
	}
	
	private function createAllGraphDataArrays(){
		if ($this->graphSettings["settings"]["type"] == "types"){
			$this->createGraphDataArray($this->graphDataGroups['types']);
		}elseif ($this->graphSettings["settings"]["type"] == "groups"){
			$this->createGraphDataArray($this->graphDataGroups['groups']);
		}elseif ($this->graphSettings["settings"]["type"] == "products"){
			foreach ($this->graphDataGroups['products'] as $group){//check repeating or empty array
				$this->createGraphDataArray($group);
			}
		}
	}
	
	protected function getRawData(){
		$this->periodArray = $this->generateEmptyPeriodArray();
		$this->getGraphDataGroups();
		$this->createAllGraphDataArrays();
		if ($this->graphMode == "new"){$this->salesLabel = "New Orders";}
		if ($this->graphMode == "renew"){$this->salesLabel = "Renewal Orders";}
		
		if (!empty($this->productIds)){
			try{
				$rawTransactions = Capsule::table("tblaccounts")->whereBetween('date', [$this->whmcsStartDate, $this->whmcsEndDate])
																->where(function ($query) {
																			$query->where('invoiceid', '>', 0)
																				  ->where('refundid', '=', 0)
																			;})
																->get();
				foreach ($rawTransactions as $rawTransaction){
					$Invoice = Capsule::table("tblinvoices")->where('id', '=', $rawTransaction->invoiceid)->first();

					if ($Invoice->status == "Paid"){
						$Order = Capsule::table("tblorders")->where('invoiceid', '=', $rawTransaction->invoiceid)->first();
						
						if (empty($Order->id)   &&  $this->graphMode == "new" ){ continue; }
						if (!empty($Order->id)  &&  $this->graphMode == "renew" ){ continue; }

						$InvoiceItems = Capsule::table("tblinvoiceitems")->where('invoiceid', '=', $rawTransaction->invoiceid)->get();
						foreach ($InvoiceItems as $InvoiceItem){
							if ((int)$InvoiceItem->amount > 0){
								if ($InvoiceItem->type == "Hosting"){
									$Item = Capsule::table("tblhosting")->where('id', '=', $InvoiceItem->relid)->first();
									$itemPrice   = $Item->amount;
									$ItemID  = $Item->packageid;
								}elseif (in_array($InvoiceItem->type, array('DomainRegister','DomainTransfer','Domain'))){	
									$Item = Capsule::table("tbldomains")->where('id', '=', $InvoiceItem->relid)->first();
									$itemPrice   = $Item->recurringamount;
									$ItemID  = "lrdomain";
								}else{
									$ItemID = "other";
									$itemPrice   = $InvoiceItem->amount;
								}
				
								$itemTypeID  = $this->getSeriesName($this->graphType, $ItemID);
								
								if (in_array($itemTypeID, $this->productIds)){
									$cDate = new \DateTime($rawTransaction->date);
									$cDate = $cDate->format('Y-m-d');
									
									$this->rawSales["series_".$itemTypeID]['data'][$cDate]++;
									$this->rawEarnings["series_".$itemTypeID]['data'][$cDate] = $this->rawEarnings["series_".$itemTypeID]['data'][$cDate] + $itemPrice;
								
								}
							}
						}
					}		
				}
				
			} catch (\Exception $e) {
				Common\ErrorHandler::FatalError("Cannot get invoices data. Returned error: ". $e->getMessage());
			}
		}
		$this->rawData['sales'] = $this->rawSales;
		$this->rawData['earnings'] = $this->rawEarnings;
		$this->rawData['settings'] = $this->graphSettings["settings"];
		$this->rawData['options']  = $this->graphDataGroups;
	}
}
?>