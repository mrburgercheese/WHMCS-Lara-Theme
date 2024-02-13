<?php
/**
 * Lara Theme Settings Hook
 *
 * Setting module for the Lara admin template.
 * Please refer to the full documentation @ http://www.whmcsadmintheme.com for more details.
 *
 * @package    WHMCS
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @copyright  Copyright (c) WHMCSAdminTheme 2016
 * @link       http://www.whmcsadmintheme.com
 */
use WHMCS\Billing\Invoice;

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

function lara_chat_admins(){
	$allAdmins = array();
	$query = "SELECT id, username, firstname, lastname, email, disabled FROM tbladmins";
	$result = full_query($query);
	while ($data = mysql_fetch_array($result)) {
		if ((int)($data['disabled']) == 0 ){
			$allAdmins[(int)($data['id'])]['id'] = (int)($data['id']);
			$allAdmins[(int)($data['id'])]['username'] = $data['username'];
			$allAdmins[(int)($data['id'])]['name'] = ucwords($data['firstname'])." ".ucwords($data['lastname']);
			$allAdmins[(int)($data['id'])]['uimg'] = md5(strtolower($data['email']));
			$allAdmins[(int)($data['id'])]['status'] = 0;
			if ((int)($data['id']) == $_SESSION['adminid']){
				$allAdmins[(int)($data['id'])]['cuser'] = 1;
			}else{
				$allAdmins[(int)($data['id'])]['cuser'] = 0;
			}
		}
	}
	return $allAdmins;
}

function lara_get_orders_stats(){
	$order_stats = array();
	$result = full_query("SELECT status, COUNT(*) AS count FROM tblorders GROUP BY status");
	while($data = mysql_fetch_array($result)){
		$order_stats[strtolower($data["status"])] = $data["count"];
	}
	return $order_stats;
}

function lara_count_tickets(){
	
	$result = select_query("tbladmins", "supportdepts", array("id" => $_SESSION['adminid']));
	$data = mysql_fetch_array($result);
	$admin_supportdepts = $data['supportdepts'];
	
	$allactive = $awaitingreply = 0;
	$flaggedtickets = 0;
	$ticketsCalculatedData = array();
	$ticketcounts = array();
	$admin_supportdepts_qry = array();
	$admin_supportdepts = explode(",", $admin_supportdepts);
	foreach ($admin_supportdepts as $deptid) {
		if (trim($deptid)) {
			$admin_supportdepts_qry[] = (int)$deptid;
			continue;
		}
	}
	
	if (count($admin_supportdepts_qry) < 1) {
		$admin_supportdepts_qry[] = 0;
	}
	
    $query = "SELECT tblticketstatuses.title,(SELECT COUNT(tbltickets.id) FROM tbltickets WHERE did IN (" . db_build_in_array($admin_supportdepts_qry) . ") AND tbltickets.status=tblticketstatuses.title AND tbltickets.merged_ticket_id = '0'),showactive,showawaiting FROM tblticketstatuses ORDER BY sortorder ASC";
	$result = full_query($query);
	while ($data = mysql_fetch_array($result)) {
		$ticketcounts[] = array("title" => $data[0], "count" => $data[1]);
		if ($data['showactive']) {
			$allactive += $data[1];
		}
		
		if ($data['showawaiting']) {
			$awaitingreply += $data[1];
		}
	}
	
	$result = select_query("tbltickets", "COUNT(*)", "status!='Closed' AND merged_ticket_id = '0' AND flag='" . (int)$_SESSION['adminid'] . "' AND did IN (" . db_build_in_array($admin_supportdepts_qry) . ")");
	$data = mysql_fetch_array($result);
	$flaggedtickets = $data[0];
	$flaggedticketschecked = true;
	
	$ticketsCalculatedData["ticketsallactive"]= $allactive;
	$ticketsCalculatedData["ticketsawaitingreply"]= $awaitingreply;
	$ticketsCalculatedData["ticketsflagged"]=  $flaggedtickets;
	$ticketsCalculatedData["ticketcounts"]=  $ticketcounts;
	$ticketsCalculatedData["ticketstatuses"]=  $ticketcounts;
	$ticketsCalculatedData['created_on'] = time();
	

	return $ticketsCalculatedData;
}

function lara_sort_homeWidgets($vars, $widgets_options){
	$sorted_widgets = array();
	$widgets_sizes  = array();
	if ((!empty($vars['widgets'])) && (is_array($vars['widgets'])) ){
		$widgets_copy = $vars['widgets'];
		$widgets_order = array();
		foreach ($widgets_options as $key => $value){
			$widgets_order[] = $value['id'];
			$widgets_sizes[$value['name']] = $value['size'];
		}		
		foreach ($widgets_order as $wID) {
			if (!empty($widgets_copy[$wID])){
				$sorted_widgets['key_'.$wID] = $widgets_copy[$wID];
				unset($widgets_copy[$wID]);
			}
		}
		foreach ($widgets_copy as $wID => $wObj) {
			$sorted_widgets['key_'.$wID] = $widgets_copy[$wID];
		}
	}
	$results = 	array("sorted_widgets" => $sorted_widgets,
                      "widgets_sizes" => $widgets_sizes); 
	return $results; 
}

function lara_custom_file_exists($cFile){
	global $customadminpath;
	$exists = false;
	
	$admin_folder = "admin";
	if (isset($customadminpath) && !empty($customadminpath)){
		$admin_folder = $customadminpath;
	}
	
	if (defined("ROOTDIR")){
		$custom_file_path = ROOTDIR . DIRECTORY_SEPARATOR . $admin_folder . DIRECTORY_SEPARATOR . "templates"  . DIRECTORY_SEPARATOR . "lara" . DIRECTORY_SEPARATOR . "custom"  . DIRECTORY_SEPARATOR . $cFile;
		if (!empty($custom_file_path) && file_exists($custom_file_path)){
			$exists = true;
		}		
	}	
	return $exists;
}

function lara_settings_update_SmartyVars($vars){
		$return = array();
		if ($vars['template'] == "lara"){
			require_once ("lrgawidget/options.class.php");
			$options = new Lara\Utils\Common\options();
			$roleID = $options->getUserRoleID($_SESSION['adminid']);
			$return['lara_options'] = $options->getOptions($roleID);
		
			## User Settings
			$cAdminID = $_SESSION['adminid'];
			$table = "mod_laraUserSettings";
			$fields = "admin_id,name,value";
			$where = array("admin_id"=>$cAdminID);
			$result = select_query($table,$fields,$where);
			while ($data = mysql_fetch_array($result)) {
				$ckey = $data['name'];
				$vkey = $data['value'];
				$return['lara_'.$ckey]= $vkey;
			}
			$adminEmail = get_query_vals("tbladmins", "email", array("id" => $_SESSION['adminid']));
			if (!empty($adminEmail['email'])){
				$return['lara_adminemail']= $adminEmail['email'];
				$return['lara_adminemail_md5']= md5(strtolower($adminEmail['email']));
			}
			
			## Home widgets Order
			$user_widgets_db_options = json_decode(html_entity_decode($return['lara_widgets_order']),true);
			$user_widgets_options = lara_sort_homeWidgets($vars, $user_widgets_db_options);
			$return['lara_options']['cuser']['sorted_widgets'] = $user_widgets_options['sorted_widgets'];
			$return['lara_options']['cuser']['widgets_sizes'] = $user_widgets_options['widgets_sizes'];
			
			## Tickets Count
			global $disable_admin_ticket_page_counts;
			$ticketsCalculatedData = array();
			session_start();
			
			if ((isset($_SESSION['adminid'])) && (in_array("List Support Tickets", $vars['admin_perms'])) && (!$disable_admin_ticket_page_counts) ) {
				if (($vars['sidebar'] == "support") && ($vars['pageicon'] == "tickets")){
					/*
					$_SESSION['cachedTicketsData']['ticketsallactive'] = $vars["ticketsallactive"];
					$_SESSION['cachedTicketsData']['ticketsawaitingreply'] = $vars["ticketsawaitingreply"];
					$_SESSION['cachedTicketsData']['ticketsflagged'] = $vars["ticketsflagged"];
					$_SESSION['cachedTicketsData']['ticketcounts'] = $vars["ticketcounts"];
					$_SESSION['cachedTicketsData']['ticketstatuses'] = $vars["ticketstatuses"];
					$_SESSION['cachedTicketsData']['created_on'] = time();
					*/
					/* Start Temp Fix */
					$ticketsCalculatedData = lara_count_tickets();
					$_SESSION['cachedTicketsData'] = $ticketsCalculatedData;
					$return = array_merge($return, $ticketsCalculatedData);
					/* End Temp Fix */
				}else{
					if ((isset($_SESSION['cachedTicketsData'])) &&
					    (($_SESSION['cachedTicketsData']['created_on'] + 300) >=  time()) ) {
						$ticketsCalculatedData = $_SESSION['cachedTicketsData'];
					}else{
						$ticketsCalculatedData = lara_count_tickets();
						$_SESSION['cachedTicketsData'] = $ticketsCalculatedData;
					}
					$return = array_merge($return, $ticketsCalculatedData);
				}
			}
			
			## Chat Admins
			if (in_array("lrchatwidget", $return['lara_options']['cuser']['permissions'])){
				$laraChatAdmins = array();
				if ((isset($_SESSION['adminid']))){
					if (isset($_SESSION['laraChatAdmins'])) {
						$laraChatAdmins = $_SESSION['laraChatAdmins'];
					}else{
						$laraChatAdmins = lara_chat_admins();
						$_SESSION['laraChatAdmins'] = $laraChatAdmins;
					}
				}
				
				$onlineAdmins = explode(",", $vars['adminsonline']);
				foreach ($laraChatAdmins as $lrAdminId => $lrAdminDetails){
					if (in_array($lrAdminDetails['username'], $onlineAdmins)){
						$laraChatAdmins[$lrAdminId]['status'] = 1;
					}
				}

				$return = array_merge($return, array("laraChatAdmins" =>$laraChatAdmins));
		    }
			
			## Custom scripts
			$return['lara_custom_files']['css'] = lara_custom_file_exists("custom.css");
			$return['lara_custom_files']['js']  = lara_custom_file_exists("custom.js");
			
			# Stats
			$return['sidebarstats']['orders'] = lara_get_orders_stats();
            $return['sidebarstats']["invoices"]["unpaid"]  = Invoice::unpaid()->count("id");
            $return['sidebarstats']["invoices"]["overdue"] = Invoice::overdue()->count("id");			
			 
		}
		return $return;
}

add_hook("AdminAreaPage",1,"lara_settings_update_SmartyVars");

?>