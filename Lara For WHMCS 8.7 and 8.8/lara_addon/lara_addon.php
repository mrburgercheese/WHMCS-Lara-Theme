<?php
/**
 * Lara Theme Settings
 *
 * Setting module for the Lara admin template.
 * Please refer to the full documentation @ http://www.whmcsadmintheme.com for more details.
 *
 * @package    WHMCS
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @copyright  Copyright (c) WHMCSAdminTheme 2016
 * @link       http://www.whmcsadmintheme.com
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

define("currentLaraVersion", "8.7.0");

function lara_addon_config() {
     $configarray = array(
    "name" => "Lara Theme Settings",
    "description" => "Setting module for the Lara admin template. <br><b>Important:</b> Make sure to permit access to all admin groups.",
    "version" => currentLaraVersion,
    "author" => "Amr M. Ibrahim",
    "language" => "english",
    "fields" => array());
    return $configarray;
}

function lara_addon_activate() {

    $query = "CREATE TABLE `mod_laraSettings` (`id` int(10) NOT NULL AUTO_INCREMENT, `name` TEXT NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`))";
    $result = full_query($query);
	
	full_query("INSERT INTO `mod_laraSettings` (`name`, `value`) VALUES ('version', '".currentLaraVersion."')");
	
    $query = "CREATE TABLE `mod_laraUserSettings` (`admin_id` INT( 10 ) NOT NULL ,`name` TEXT NOT NULL, `value` TEXT NOT NULL )";
    $result = full_query($query);

    $query = "CREATE TABLE `mod_lrgawidget` (`id` int(10) NOT NULL AUTO_INCREMENT, `name` TEXT NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`))";
    $result = full_query($query);	
	
    $query = "CREATE TABLE `mod_lrchat` (`id` int(10) NOT NULL AUTO_INCREMENT, `fromid` int(10) NOT NULL , `toid` int(10) NOT NULL, `message` TEXT NOT NULL, `chatid` VARCHAR(15) NOT NULL, `seen` TINYINT(1) DEFAULT 0, `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_general_ci";
    $result = full_query($query);	
}

function lara_addon_deactivate() {
	
    $query = "DROP TABLE `mod_laraSettings`";
    $result = full_query($query);	

    $query = "DROP TABLE `mod_laraUserSettings`";
    $result = full_query($query);
	
    $query = "DROP TABLE `mod_lrgawidget`";
    $result = full_query($query);

    $query = "DROP TABLE `mod_lrchat`";
    $result = full_query($query);	

}

function lara_addon_upgrade($vars) {
	$version = $vars['version'];
   
	full_query("UPDATE `mod_laraSettings` SET `value` = '".currentLaraVersion."' WHERE `name` = 'version' ");

	
	if ($version < 3.0) {
		$query = "CREATE TABLE `mod_lrgawidget` (`id` int(10) NOT NULL AUTO_INCREMENT, `name` TEXT NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`))";
		$result = full_query($query);
	}
	
	if ($version < 3.5) {
		$query = "CREATE TABLE `mod_lrchat` (`id` int(10) NOT NULL AUTO_INCREMENT, `fromid` int(10) NOT NULL , `toid` int(10) NOT NULL, `message` TEXT NOT NULL, `chatid` VARCHAR(15) NOT NULL, `seen` TINYINT(1) DEFAULT 0, `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`))";
		$result = full_query($query);	
	}

	full_query("ALTER TABLE `mod_lrchat` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
	full_query("ALTER TABLE `mod_lrchat` MODIFY `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci");	

}

function lara_internal_permissions(){
	$parray = array();
	$data = get_query_vals("tbladmins", "tbladminroles.widgets,tbladmins.roleid,tbladmins.disabled", array("tbladmins.id" => $_SESSION['adminid']), "", "", "", "tbladminroles ON tbladminroles.id=tbladmins.roleid");
	if (!empty($data) || $data['disabled'] != "0"){
		$adminPermissions = localAPI("getadmindetails", array());
		if ($adminPermissions['result'] === "success"){
			$parray["roleID"] = $data['roleid'];
			$parray["widgets"] = explode(',', $data['widgets']);
			$parray["permissions"] = explode(',', $adminPermissions['allowedpermissions']); 
		}
	}
	return $parray;
}

function lara_addon_output($vars) {
	if (isset($_SESSION['adminid'])){
		$sysPermissions = lara_internal_permissions();
		require_once ("lrgawidget/options.class.php");
		
		if ((isset($_POST['action'])) || (isset($_POST['lrchat']))){
			$options = new Lara\Utils\Common\options();
			$laraPermissions = $options->getOptions($sysPermissions['roleID']);
		}
		
		if (isset($_POST['action'])){
			$lrperm = $laraPermissions['cuser'];
			$lrdata = $_POST;
			require ("lrgawidget/lrgawidget.handler.php");
		}elseif (isset($_POST['lrchat'])){
			$lrperm = $laraPermissions['cuser'];
			$lrdata = $_POST;
			require ("lrchat/lrchat.handler.php");			
		}elseif ($_POST['mode'] == "update"){
			unset($_POST['mode']);
			foreach ($_POST as $key => $value){
				$table = "mod_laraUserSettings";
				$fields = "admin_id,name,value";
				$where = array("admin_id"=>$_SESSION['adminid'],"name"=>$key);
				$result = select_query($table,$fields,$where);
				$data = mysql_fetch_array($result);
				$id = $data['admin_id'];
					if ($id){
						$update = array("value"=>$value);
						update_query($table,$update,$where);
					}else{
						$values = array("admin_id"=>$_SESSION['adminid'],"name"=>$key,"value"=>$value);
						insert_query($table,$values);
					}
			}
			$data = array('results'=>'done');
			header('Content-Type: application/json');
			echo json_encode($data);
			exit;
		}else{ //Module page ourput
		
			if (in_array("Configure Admin Roles", $sysPermissions["permissions"])){
				if($_POST['lrRequest']){
					$options = new Lara\Utils\Common\options();

					switch ($_POST['lrRequest']) {
						case "setPermissions":
							$results = $options->setPermissions($_POST['lrperms']);
							break;
						case "setSettings":
							$results = $options->setSettings($_POST['settings']);
							break;
						default:
							$results = array('errors'=>array("Incorrect Request"));
					}					
					header('Content-Type: application/json');
					echo json_encode($results);
					exit;					
				}
				
				echo '<div class="callout callout-success">
					<h4><i class="fa fa-lightbulb-o"></i> Tip</h4>
					To apply any custom CSS classes or Javascript/jQuery, check the files in the "custom" folder. Edit the default files and add your modifications, then rename the modified file name to either custom.css or custom.js. 
				</div>';
				
				require ("lrgawidget/options_page.php");
				$options_page = new Lara\Utils\Common\options_page();
				$options_page->settings_page();		
				
			}else{
				echo '<div class="callout callout-warning">
						<ul class="fa-ul">
							<li><i class="fa-li fa fa-warning"></i>To edit the global theme settings, You need to be a super admin and your admin group needs "<b>Configure Admin Roles</b>" Permission.</li>
						</ul>
					</div>';
			}
		}
	}
}
?>