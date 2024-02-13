<?php

namespace Lara\Utils\Common;

use Lara\Utils\Common as Common;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @package    Google Analytics by Lara - Pro
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @link       https://www.whmcsadmintheme.com
 * @copyright  Copyright (c) WHMCSAdminTheme 2016
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

class options_page {
	private $lrOptions;

	function __construct() {
		$this->lrOptions = new options();
	}

	function  settings_page() {
		global $CONFIG;
		$WHMCSVersion = $CONFIG['Version'];

		$results = $this->lrOptions->getOptions();
		$laraRoles = $results["roles"];
		$laraSettings = $results["settings"];
		$laraStockPermissions = $this->lrOptions->getStockPermissions();
		$rolesOutput = $permissions = "";
		$isActive ="active";
		
		foreach ($laraRoles as $role){
			$activePermissions = $role["permissions"];
			$rolesOutput .= '<li class="'.$isActive.'"><a href="#role_'.$role['id'].'" data-toggle="pill"><i class="fa fa-users fa-fw"></i>'.$role['label'].'</a></li>';
			$permissions .= '<div class="tab-pane '.$isActive.'" id="role_'.$role['id'].'">';
			$isActive = "";
			foreach ($laraStockPermissions as $group){
				$isGroupActive = "";
				if (in_array($group['id'], $activePermissions)){$isGroupActive = "checked";}				
				
				$permissions .= '<div class="box box-primary"><div class="box-header with-border"><h3 class="box-title"><i class="fa '.$group['icon'].'"></i> '.$group['name'].'</h3>
									<span class="pull-right">
										<input type="checkbox" class="laraperm-bootstrap-switch" '.$isGroupActive.' name="lrperms[lrrole_'.$role['id'].'][]" value="'.$group['id'].'" data-label-text="Enabled" data-handle-width="50px"  data-size="mini" />
									</span>
								</div><div class="box-body lroptions-checkbox-grid" id="'.$group['id'].'_role_'.$role['id'].'">';
				foreach ($group['permissions'] as $permission){
					$isChecked = "";
					if (in_array($permission['name'], $activePermissions)){$isChecked = "checked";}
						$permissions .= '<div><label><input name="lrperms[lrrole_'.$role['id'].'][]"  value="'.$permission['name'].'" type="checkbox" '.$isChecked.' > '.$permission['label'].'</label></div>';
					}
				$permissions .= '</div></div>';
			}
			$permissions .= '</div>';
		}
		require ("templates/options.tpl.php");
	}
}
?>