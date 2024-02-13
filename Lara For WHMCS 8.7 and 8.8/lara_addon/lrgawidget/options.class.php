<?php
namespace Lara\Utils\Common;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @package    Google Analytics by Lara - Pro
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @link       https://www.whmcsadmintheme.com
 * @copyright  Copyright (c) WHMCSAdminTheme 2016
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

class options{
	private $laraStockPermissions;
	private $results  = array();
	private $output   = array();
	private $errors   = array();	
	
	function __construct(){
		$this->laraStockPermissions =  array(array("id"    => "lrgawidget",
												   "name"  => "Google Analytics",
												   "icon"  => "fa-bar-chart",
												   "permissions" => array(array("name" => "lrgawidget_perm_admin",    "label" => "Administrator [Change Settings]"),
																		  array("name" => "lrgawidget_perm_sessions", "label" => "View Sessions"),
																		  array("name" => "lrgawidget_perm_earnings", "label" => "Orders & Income Graphs"),
																		  array("name" => "lrgawidget_perm_daterange","label" => "Change Date Range"),
																		  array("name" => "lrgawidget_perm_realtime", "label" => "Real Time"),
																		  array("name" => "lrgawidget_perm_countries","label" => "View Countries"),
																		  array("name" => "lrgawidget_perm_browsers", "label" => "View Browsers"),
																		  array("name" => "lrgawidget_perm_languages","label" => "View Languages"),
																		  array("name" => "lrgawidget_perm_os",       "label" => "View Operating Systems"),
																		  array("name" => "lrgawidget_perm_devices",  "label" => "View Devices"),
																		  array("name" => "lrgawidget_perm_screenres","label" => "View Screen Resolutions"),
																		  array("name" => "lrgawidget_perm_keywords", "label" => "View Keywords"),
																		  array("name" => "lrgawidget_perm_sources",  "label" => "View Sources"),
																		  array("name" => "lrgawidget_perm_pages",    "label" => "View Pages")
																		  )
													),
											array("id"    => "lrchatwidget",
												  "name"  => "Staff Chat",
												  "icon"  => "fa-comments",
												  "permissions" => array(array("name" => "lrgawidget_perm_chat",     "label" => "Enable Staff Chat"))
												   )
											);
	}
	
	private function parseCollection($recordsCollection){
		$results = array();
		try{
			if (!empty($recordsCollection)){
				foreach ($recordsCollection as $record){
					if (!empty($record)){
						$results[] = (array)$record;
					}
				}
			}
		} catch (\Exception $e) {
			$this->errors[]= "Cannot parse collection - Raw Error : ".$e->getMessage(); 
		}
		return $results;	
	}	
	
	private function getFromDB($table){
		try{
			$results = Capsule::table($table)->get();
		} catch (\Exception $e) {
			$this->errors[]= "Cannot get data from ".$table." - Raw Error : ".$e->getMessage(); 
		}
		
		$results = $this->parseCollection($results);
		return $results;
	}
	
	private function updateRecords($table, $records){
		foreach ($records as $name => $value){
			if (is_array($value)){$value = json_encode($value);}
			try{
				$exists = Capsule::table($table)->where('name', $name)->get();
				$exists = $this->parseCollection($exists);
				if (!empty($exists)){
					Capsule::table($table)->where('name', $name)->update(array('value' => $value));
				}else{
					Capsule::table($table)->insert(array('name' => $name, 'value' => $value));
				}
			} catch (\Exception $e) {
				$this->errors[]= "Cannot update record - Raw Error : ".$e->getMessage(); 
			}
		}
	}

	private function getSystemRoles(){
		$systemRoles = array();
		$results = $this->getFromDB("tbladminroles");
		foreach ($results as $role => $permissions) {
			$systemRoles[] = array("id"    => $permissions['id'],
			                       "label" => $permissions['name']);
		}
		return $systemRoles;
	}
	
	private function getLaraSettings(){
		$laraSettings = array();
		$results = $this->getFromDB("mod_laraSettings");
		foreach ($results as $result){
			if(preg_match('/^lrrole_/s', $result['name'])){
				$roleID = str_replace("lrrole_", "", $result['name']);
				$laraSettings['roles'][$roleID] = $result['value'];
			}else{
				$laraSettings['settings'][$result['name']] = $result['value'];
			}
		}
		return $laraSettings;
	}

    public function getOptions($roleID=""){
        $laraOptions = array(); 
        $systemRoles  = $this->getSystemRoles();
        $laraSettings = $this->getLaraSettings();
        foreach ($systemRoles as $id => $role) {
            $permissions = array();
            if(!empty($laraSettings['roles'][$role['id']])){
                $permissions = json_decode($laraSettings['roles'][$role['id']]);
            }
            $systemRoles[$id]["permissions"] = $permissions;
            if ((!empty($roleID)) &&  $roleID ==  $role['id'] ){
                $laraOptions['cuser'] = $systemRoles[$id];
            }                
        }
        $laraOptions['settings']       = $laraSettings['settings'];
        $laraOptions['roles']          = $systemRoles;

        $this->output = $laraOptions;
        return $this->processOutput();
    }
	
	public function getUserRoleID($userID){
		$results = $this->getFromDB("tbladmins");
		foreach ($results as $user) {
			if ($user['id'] == $userID){ return $user['roleid'];}
		}		
	}	
	
	public function getStockPermissions(){
		return $this->laraStockPermissions;
	}

	public function setPermissions($permissions){
		$options = $this->getOptions();
		$roles   = $options['roles'];
		foreach ($roles as $role){
			if (empty($permissions['lrrole_'.$role['id']])){
				$permissions['lrrole_'.$role['id']] =  array();
			}
		}
		$this->updateRecords("mod_laraSettings", $permissions);
		return $this->processOutput();
	}
	
	public function setSettings($settings){
		$this->updateRecords("mod_laraSettings", $settings);
		return $this->processOutput();
	}	
	
	private function processOutput(){
		if (empty($this->errors)){
			$this->output['status'] = "done";
		}else{
			$this->output['errors'] = $this->errors;
		}
		return $this->output; 
	}	
}

?>