<?php

/**
 * @package    WHMCS
 * @author     Amr M. Ibrahim <mailamr@gmail.com>
 * @copyright  Copyright (c) WHMCSAdminTheme 2016
 * @link       http://www.whmcsadmintheme.com
 */

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

?>

<style type="text/css">

.laraoption h3 { font-family: "Source Sans Pro", sans-serif; }
.laraoption a { color: #3c8dbc; }
.laraoption a:hover, .laraoption a:active { outline: none; text-decoration: none; color: #72afd2; }
.laraoption .form-control { border-radius: 0px; box-shadow: none; border-color: #d2d6de; }
.laraoption .form-control:not(select) { -webkit-appearance: none; }
.laraoption .box { position: relative; border-radius: 3px; background: #fff; border-top: 3px solid #d2d6de; margin-bottom: 20px; width: 100%; box-shadow: rgba(0, 0, 0, 0.0980392) 0px 1px 1px; }
.laraoption .box.box-primary { border-top-color: #3c8dbc; }
.laraoption .box .nav-stacked > li { border-bottom: 1px solid #f4f4f4; margin: 0px; }
.laraoption .box .nav-stacked > li:last-of-type { border-bottom: none; }
.laraoption .box-header::before,.laraoption .box-body::before,.laraoption .box-header::after,.laraoption .box-body::after { content: " "; display: table; }
.laraoption .box-header::after,.laraoption .box-body::after { clear: both; }
.laraoption .box-header { color: #444; display: block; padding: 10px; position: relative; }
.laraoption .box-header.with-border { border-bottom: 1px solid #f4f4f4; }
.laraoption .box-header .box-title { display: inline-block; font-size: 18px; margin: 0px; line-height: 1; }
.laraoption .box-header > .box-tools { position: absolute; right: 10px; top: 5px; }
.laraoption .box-body { border-radius: 0px 0px 3px 3px; padding: 10px; }
.laraoption .btn { border-radius: 3px; box-shadow: none; border: 1px solid transparent; }
.laraoption .btn:active { box-shadow: rgba(0, 0, 0, 0.121569) 0px 3px 5px inset; }
.laraoption .btn-primary { background-color: #3c8dbc; border-color: #367fa9; }
.laraoption .btn-primary:hover,.laraoption .btn-primary:active { background-color: #367fa9; }
.laraoption .alert { border-radius: 3px; }
.laraoption .alert-danger { border-color: #d73925; }
.laraoption .nav > li > a:hover,.laraoption .nav > li > a:active { color: #444; background: #f7f7f7; }
.laraoption .nav-pills > li > a { border-radius: 0px; border-top: 3px solid transparent; color: #444; }
.laraoption .nav-pills > li > a > .fa { margin-right: 5px; }
.laraoption .nav-pills > li.active > a, .laraoption .nav-pills > li.active > a:hover { border-top-color: #3c8dbc; }
.laraoption .nav-pills > li.active > a { font-weight: 600; }
.laraoption .nav-stacked > li > a { border-radius: 0px; border-top: 0px; border-left: 3px solid transparent; color: #444; }
.laraoption .nav-stacked > li.active > a,.laraoption .nav-stacked > li.active > a:hover { background: transparent; color: #444; border-top: 0px; border-left-color: #3c8dbc; }
.laraoption .nav-tabs-custom { margin-bottom: 20px; background: #fff; box-shadow: rgba(0, 0, 0, 0.0980392) 0px 1px 1px; border-radius: 3px; }
.laraoption .nav-tabs-custom > .nav-tabs { margin: 0px; border-bottom-color: #f4f4f4; border-top-right-radius: 3px; border-top-left-radius: 3px; }
.laraoption .nav-tabs-custom > .nav-tabs > li { border-top: 3px solid transparent; margin-bottom: -2px; margin-right: 5px; }
.laraoption .nav-tabs-custom > .nav-tabs > li > a { color: #444; border-radius: 0px; }
.laraoption .nav-tabs-custom > .nav-tabs > li > a,.laraoption .nav-tabs-custom > .nav-tabs > li > a:hover { background: transparent; margin: 0px; }
.laraoption .nav-tabs-custom > .nav-tabs > li > a:hover { color: #999; }
.laraoption .nav-tabs-custom > .nav-tabs > li:not(.active) > a:hover,.laraoption .nav-tabs-custom > .nav-tabs > li:not(.active) > a:active { border-color: transparent; }
.laraoption .nav-tabs-custom > .nav-tabs > li.active { border-top-color: #3c8dbc; }
.laraoption .nav-tabs-custom > .nav-tabs > li.active > a,.laraoption .nav-tabs-custom > .nav-tabs > li.active:hover > a { background-color: #fff; color: #444; }
.laraoption .nav-tabs-custom > .nav-tabs > li.active > a { border-top-color: transparent; border-left-color: #f4f4f4; border-right-color: #f4f4f4; }
.laraoption .nav-tabs-custom > .nav-tabs > li:first-of-type { margin-left: 0px; }
.laraoption .nav-tabs-custom > .nav-tabs > li:first-of-type.active > a { border-left-color: transparent; }
.laraoption .nav-tabs-custom > .tab-content { background: #fff; padding: 10px; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px; }
.laraoption .table > tbody > tr > td { border-top: 1px solid #f4f4f4; }
.laraoption .alert-danger { color: #fff !important; }
.laraoption .alert-danger { background-color: #dd4b39 !important; }
.laraoption .tab-content > .tab-pane { padding: 1px; border: none; }



h4 { font-family: "Source Sans Pro", sans-serif; }
.callout { border-radius: 3px; margin: 0px 0px 20px; padding: 15px 30px 15px 15px; border-left: 5px solid #eee; }
.callout h4 { margin-top: 0px; font-weight: 600; }
.callout.callout-success { border-color: #00733e; }
.callout.callout-success { color: #fff !important; }
.callout.callout-success { background-color: #00a65a !important; }

.laraoption .lroptions-checkbox-grid div { display: block; float: left; width: 30%; }
.laraoption .lroptions-checkbox-grid label { font-weight: normal; }
.laraoption .lroptions-checkbox-grid input { margin-right: 5px; }
.laraoption .nav-stacked > li.active > a,.laraoption .nav-stacked > li.active > a:hover { color: #fff; background-color: #337ab7; }
.laraoption .table-lara-custom td:first-child { width: 30%; text-align: right; }
.laraoption .table-lara-custom td:last-child { width: 70%; }

</style>


<script type="text/javascript">
(function($) {
	
var debug = true;

	function laraShowError(errors){
		$("#laraCore_error").html("<ul class='fa-ul'></ul>");
		if (typeof errors === 'object'){
			$.each( errors, function( index, error ) {
				$("#laraCore_error .fa-ul").append("<li><i class='fa-li fa fa-warning'></i>"+error+"</li>");
			});
		}else{
			$("#laraCore_error .fa-ul").append("<li><i class='fa-li fa fa-warning'></i>Error Returned : "+errors+"</li>");
		}
		$("#laraCore_error").show();
	}
	
	function laraCoreAjax(request){
		if (debug){console.log(request)};
		$("#laraCore_error").html("").hide();
		$("#laraCore_results").html("");
		$("#laraCore_loading").html('<i class="fa fa-spinner fa-pulse"></i>');

		return $.ajax({
			method: "POST",
			url: "addonmodules.php?module=lara_addon",
			data: request,
			dataType: 'json'
		})
		.done(function (data, textStatus, jqXHR) {
			if (debug){console.log(data)};
			if (data.status == "done"){
				$("#laraCore_results").html("Saved");
				$("#laraCore_results").removeClass("bg-red").addClass("bg-green");
			}else{
				$("#laraCore_results").html("Failed");
				$("#laraCore_results").removeClass("bg-green").addClass("bg-red");
				laraShowError(data.errors);
			}		
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			$("#laraCore_results").html("Failed");
			$("#laraCore_results").removeClass("bg-green").addClass("bg-red");
			laraShowError(errorThrown);
		})		
		.always(function (dataOrjqXHR, textStatus, jqXHRorErrorThrown) {
			$("#laraCore_loading").html("");
		});
	}	
	
	function lrTogglePermisionsGroup(switchID, state){
		var roleID  = $(switchID).parents(".tab-pane").attr("id");
		var groupID = switchID.value+"_"+roleID;
		if(state) { 
		   $("#"+roleID+" #"+groupID+" input").prop("disabled", false);
		   $("#"+roleID+" #"+groupID+" input").prop("checked", true);
		}else{
			$("#"+roleID+" #"+groupID+" input").prop("checked", false);
			$("#"+roleID+" #"+groupID+" input").prop("disabled", true);
			$(switchID).prop("disabled", false);
		}		
	}
	
$(document).ready(function(){
	try {	
		$('.laraperm-bootstrap-switch').on('init.bootstrapSwitch', function(event) {
			if (!this.checked){ 
				lrTogglePermisionsGroup(this,this.checked);
			}
			
		});

		$(".laraperm-bootstrap-switch").bootstrapSwitch();
		
		$('.laraperm-bootstrap-switch').on('switchChange.bootstrapSwitch', function(event, state) {
			lrTogglePermisionsGroup(this,state);
		});	
	} catch(err) {
		console.log(err.message);
	}
	
	$("#logo_lg_txt").on('keyup', function () {
		$("body").removeClass("sidebar-collapse");
		if ($(this).val().trim()){
			$('.main-header .logo .logo-lg').html($(this).val());
		}else{
			$('.main-header .logo .logo-lg').html("WHMCS");
		}
	});

	$("#logo_mini_txt").on('keyup', function () {
		$("body").addClass("sidebar-collapse");
		if ($(this).val().trim()){
			$('.main-header .logo .logo-mini').html($(this).val());
		}else{
			$('.main-header .logo .logo-mini').html("WHM");
		}
	});	
	
	$("#logo_lg_img").on("change keyup paste", function(){
		$("body").removeClass("sidebar-collapse");
		$('.main-header .logo .logo-lg').html('<img src="'+$(this).val()+'">');
	})	
	
	$("#logo_mini_img").on("change keyup paste", function(){
		$("body").addClass("sidebar-collapse");
		$('.main-header .logo .logo-mini').html('<img src="'+$(this).val()+'">');
	})	
	
	$("#logo_mini_txt, #logo_mini_img" ).focusout(function() {
		$("body").removeClass("sidebar-collapse");
	});	

	$('form[id^="laraCore"]').submit(function(e) {
        e.preventDefault();
		laraCoreAjax($(this).serializeArray());
	});	
	
});
			
})(jQuery);			
</script>
<div class="laraoption">

<div class="box box-primary" id="laraCore">
	<div class="box-header with-border">
		<h3 class="box-title"><i class="fa fa-cogs"></i> Lara Core Settings</h3>
		<div class="box-tools pull-right">
			<span id="laraCore_loading"></span> <span class="label" id="laraCore_results"></span>
		</div>
	</div>
	<div class="box-body">
		<div class="nav-tabs-custom" id="laraCore_main">
			<ul class="nav nav-tabs">
				<li class="active">
					<a data-toggle="tab" href="#laraCore_settings_tab"><i class="fa fa-cogs fa-fw"></i> <span class="hidden-xs hidden-sm">Settings</span></a>
				</li>
				<li>
					<a data-toggle="tab" href="#laraCore_permissions_tab"><i class="fa fa-lock fa-fw"></i> <span class="hidden-xs hidden-sm">Permissions</span></a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="alert alert-danger" id="laraCore_error" style="display: none;"></div>
				<div class="tab-pane active" id="laraCore_settings_tab">
					<div class="row" style="margin:auto;">
						<form action="addonmodules.php?module=lara_addon" class="form-horizontal" id="laraCore-setSettings" method="post" name="laraCore-setSettings" role="form">
							<input name="lrRequest" type="hidden" value="setSettings">
							<table class="table table-striped table-lara-custom">
								<tbody>
									<tr>
										<td>Lara Version :</td>
										<td><?php echo $laraSettings['version']; ?></td>
									</tr>
									<tr>
										<td>WHMCS Version :</td>
										<td><?php echo $WHMCSVersion; ?></td>
									</tr>
									<tr>
										<td>PHP Version :</td>
										<td><?php echo phpversion(); ?></td>
									</tr>
									<tr>
										<td>Text Logo :</td>
										<td><input class="form-control" id="logo_lg_txt" maxlength="14" name="settings[logo_lg_txt]" placeholder="WHMCS" type="text" value="<?php echo $laraSettings['logo_lg_txt']; ?>"></td>
									</tr>
									<tr>
										<td>Mini Text Logo :</td>
										<td><input class="form-control" id="logo_mini_txt" maxlength="3" name="settings[logo_mini_txt]" placeholder="WHM" type="text" value="<?php echo $laraSettings['logo_mini_txt']; ?>"></td>
									</tr>
									<tr>
										<td>Image Logo URL:</td>
										<td><input class="form-control" id="logo_lg_img" name="settings[logo_lg_img]" placeholder="https://domain.com/logo.png" type="text" value="<?php echo $laraSettings['logo_lg_img']; ?>"></td>
									</tr>
									<tr>
										<td>Mini Image Logo URL:</td>
										<td><input class="form-control" id="logo_mini_img" name="settings[logo_mini_img]" placeholder="https://domain.com/logo-mini.png" type="text" value="<?php echo $laraSettings['logo_mini_img']; ?>"></td>
									</tr>									
								</tbody>
							</table>
							<div>
								<button class="btn btn-primary pull-right" type="submit">Save</button>
							</div>
						</form>
					</div>
				</div>
				<div class="tab-pane" id="laraCore_permissions_tab">
					<div class="row">
						<div class="col-md-3">
							<ul class="nav nav-pills nav-stacked">
								<?php echo $rolesOutput; ?>
							</ul>
						</div>
						<div class="col-md-9">
							<form action="addonmodules.php?module=lara_addon" id="laraCore-setPermissions" method="post" name="laraCore-setPermissions" role="form">
								<input name="lrRequest" type="hidden" value="setPermissions">
								<div class="tab-content">
									<?php echo $permissions; ?>
								</div>
								<div>
									<button class="btn btn-primary pull-right" type="submit">Save</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>