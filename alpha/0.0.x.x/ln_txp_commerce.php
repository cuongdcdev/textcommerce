<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Uncomment and edit this line to override:
# $plugin['name'] = 'abc_plugin';

$plugin['version'] = '0.0.6.7';
$plugin['author'] = 'Levi Nunnink';
$plugin['author_uri'] = 'http://culturezoo.com/nwa';
$plugin['description'] = 'An e-commerce extension for the TXP admin interface.';
$plugin['type'] = 1; // 0 for regular plugin; 1 if it includes admin-side code

if(isset($_GET['build'])){
@include_once($_SERVER['DOCUMENT_ROOT'].'/TextCommerce/plugins/subplugins/zem_tpl.php');
}
if (0) {
?>
# --- BEGIN PLUGIN HELP ---

h1. Visit "http://homeplatewp.com/TextPattern/":http://homeplatewp.com/TextPattern/ for help documentation.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

// ----------------------------------------------------
// Example public side tags


	

// ----------------------------------------------------
// Example admin side plugin
error_reporting(E_ERROR);
global $vars, $statuses;


$vars = array(
	'ID','Title','Title_html','Body','Body_html','Excerpt','textile_excerpt','Image',
	'textile_body', 'Keywords','Status','Posted','Section','Category1','Category2',
	'Annotate','AnnotateInvite','publish_now','reset_time','AuthorID','sPosted',
	'LastModID','sLastMod','override_form','from_view','year','month','day','hour',
	'minute','second','url_title','custom_1','custom_2','custom_3','custom_4','custom_5',
	'custom_6','custom_7','custom_8','custom_9','custom_10', 'new_vendor_name', 'vendor'
);

if(!function_exists("Textile")){
	require_once txpath.'/lib/classTextile.php';
}
require_once txpath.'/lib/txplib_wrapper.php';
$statuses = array(
		//1 => gTxt('draft'),
		2 => gTxt('hidden'),
		3 => gTxt('pending'),
		4 => strong(gTxt('live')),
		//5 => gTxt('sticky'),

);


	// Add a new tab to the Content area.
	// "test" is the name of the associated event; "testing" is the displayed title
	if (@txpinterface == 'admin') {
		//tabs
		$store_categories_event = 'store_categories';
		$categories_name = 'Categories';

		$new_product_event = 'product';
		$new_product_name = 'Product';

		$products_event = 'products';
		$products_name = 'Products';

		$customers_event = 'customers';
		$customers_name = 'Customers';

		$customer_event = 'customer';
		$customer_name = 'Customer';


		$orders_event = 'orders';
		$orders_name = 'Orders';

		$settings_event = 'settings';
		$settings_name = 'Settings';

		$dashboard_event = 'store';
		$dashboard_name = 'Dashboard';

		// Set the privilege levels for our new event
		add_privs($store_categories_event, '1,2');

		if(isset($_REQUEST['step'])){
			$step = $_REQUEST['step'];
		}else{
			$step = "";
		}

		if(isset($_REQUEST['event'])){
			$event = $_REQUEST['event'];
		}
		if($event == "product"){
			switch(strtolower($step)) {
				// 'zem_admin_test' will be called to handle the new event
				case "":  		register_callback("product_edit", $new_product_event); break;
				case "create":  register_callback("product_post", $new_product_event); break;
				case "edit":  	register_callback("product_edit", $new_product_event); break;
				case "save":  	register_callback("product_save", $new_product_event); break;
				case "product_import":  register_callback("product_import", $new_product_event); break;
				case "product_multi_edit": register_callback("product_multi_edit", $new_product_event); break;
			}
		}
		// Add a new tab under 'extensions' associated with our event
		//register_tab("store", $store_categories_event, $categories_name);
		//register_callback("store_categories", $store_categories_event);


		// Add a new tab under 'extensions' associated with our event
		register_tab("store", $new_product_event, $new_product_name);

		// 'zem_admin_test' will be called to handle the new event
		//register_callback("product_edit", $new_product_event);

		// Add a new tab under 'extensions' associated with our event
		register_tab("store", $products_event, $products_name);

		// 'zem_admin_test' will be called to handle the new event
		register_callback("products_list", $products_event);

		// Add a new tab under 'extensions' associated with our event
		register_tab("store", $customers_event, $customers_name);


		// Add a new tab under 'extensions' associated with our event
		register_tab("store", $orders_event, $orders_name);
		//register_callback("orders_list", $orders_event);

		if($event == "orders"){
			switch(strtolower($step)) {
				case "":  				register_callback("orders_list",   $orders_event); 	  	break;
				case "list":  			register_callback("orders_list",   $orders_event); 	  	break;
				case "edit_order":  	register_callback("orders_edit",   $orders_event); 		break;
				case "update_order":  	register_callback("orders_update", $orders_event); 		break;
				case "export_orders":  	register_callback("orders_export", $orders_event); 		break;
				case "delete_order":  	register_callback("orders_delete", $orders_event); 		break;
			}
		}

		if($event == "settings"){
			switch(strtolower($step)) {
				case "":  				register_callback("settings_edit",   $settings_event); 	  	break;
				case "edit":  			register_callback("settings_edit",   $settings_event); 	  	break;
				case "update":  		register_callback("settings_update", $settings_event); 		break;
			}
		}

		// 'zem_admin_test' will be called to handle the new event
		register_tab("store", $customers_event, $customers_name);
		register_callback("customers_switch", $customers_event);

		register_tab("store", $settings_event, $settings_name);

		// Add a new tab under 'extensions' associated with our event
		register_tab("store", $dashboard_event, $dashboard_name);

		// 'zem_admin_test' will be called to handle the new event
		register_callback("show_dashboard", $dashboard_event);
		
		//Special Multipule Categories Support
		/*if(is_callable("rss_admin_catlist")){
			register_callback('rss_admin_catlist_save', 'product', 'product_post');
			register_callback('rss_admin_catlist_save', 'product', 'product_save');
		}*/

	}

	function settings_update($event, $step){
		global $txp_user, $vars, $txpcfg, $prefs;

		extract(doSlash($_REQUEST));

		if($settings_update == "general"){
			if(!isset($inventory_management_on)){
				$inventory_management_on = 0;
			}
			if(!isset($send_low_inventory_email_notification)){
				$send_low_inventory_email_notification = 0;
			}

			$storeSettingExist = safe_count("store_settings", "1"); //do we even have any store settings?
			if($storeSettingExist > 0){
				$return = safe_update("store_settings",
						"inventory_management_on		= $inventory_management_on,
						 hide_inventory_when_depleted	= $hide_inventory_when_depleted,
						 depleted_inventory_message		= '$depleted_inventory_message',
						 send_low_inventory_email_notification = $send_low_inventory_email_notification,
						 store_address					= '$store_address',
						 store_city						= '$store_city',
						 store_state					= '$store_state',
						 store_zip						= '$store_zip',
						 store_country					= '$store_country',
						 owner_email					= '$owner_email',
						 unit_system					= '$unit_system',
						 store_currency					= '$store_currency'", "1");
			}else{
				$return = safe_insert("store_settings",
						"inventory_management_on		= $inventory_management_on,
						 hide_inventory_when_depleted	= $hide_inventory_when_depleted,
						 depleted_inventory_message		= '$depleted_inventory_message',
						 send_low_inventory_email_notification = $send_low_inventory_email_notification,
						 store_address					= '$store_address',
						 store_city						= '$store_city',
						 store_state					= '$store_state',
						 store_zip						= '$store_zip',
						 store_country					= '$store_country',
						 owner_email					= '$owner_email',
						 unit_system					= '$unit_system',
						 store_currency					= '$store_currency'");
			}
			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='General settings updated');
			}

		}else if($settings_update == "add_zone"){

			$country = safe_row("name", "countries", "id=$country_id");
			$country = $country['name'];
			if(!isset($shipping_rate_id)){
				$shipping_rate_id = 'NULL';
			}

			$return = safe_insert("shipping_zones",
						"name 		= '$country',
						 country_id = $country_id,
						 tax_rate	= $tax_rate,
						 shipping_rate_id=$shipping_rate_id");

			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping region added', "zones");
			}
		}else if($settings_update == "delete_zone"){

			$return = safe_delete("shipping_zones", "id=$id");
			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping region deleted', "zones");
			}

		}else if($settings_update == "update_zone"){

			$return = safe_update("shipping_zones",
									"tax_rate = $tax_rate",
									"id=$id");
			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping region updated', "zones");
			}

		}else if($settings_update == "update_zone"){

			$return = safe_update("shipping_zones",
									"tax_rate = $tax_rate",
									"id=$id");
			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping region updated', "zones");
			}

		}else if($settings_update == "update_rate"){

			$return = safe_update("shipping_rates",
									"title = '$title',
									 rate  =  $rate,
									 start_weight = $start_weight,
									 end_weight	  = $end_weight",
									 "id=$shipping_rate_id");
			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping rate updated', "rates");
			}

		}else if($settings_update == "add_rate"){
			$rate_id = safe_insert("shipping_rates",
									"title = '$title',
									 rate  =  $rate,
									 start_weight = $start_weight,
									 end_weight	  = $end_weight");
			if(!$rate_id){
				echo mysql_error();
				die();
			}
			$return = safe_insert("zones_rates",
								  "shipping_rate_id = $rate_id,
								   shipping_zone_id = $id");
			if(!$return){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping rate added', "rates");
			}

		}else if($settings_update == "delete_rate"){
			$result = safe_delete("shipping_rates", "id=$shipping_rate_id");

			if(!$result){
				echo mysql_error();
				die();
			}
			$result = safe_delete("zones_rates",
								  "shipping_rate_id = $shipping_rate_id AND
								   shipping_zone_id = $id");
			if(!$result){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Shipping rate deleted', "rates");
			}
	   }else if($settings_update == "notices"){

			$result = $return = safe_update("store_settings",
											"order_confirmation_form = '$order_confirmation_form',
											 new_order_notification_form = '$new_order_notification_form'",
											 "1");
			if(!$result){
				echo mysql_error();
			}else{
				settings_edit($event, $step, $message='Notices updated', "notices");
			}
		}
	}

	function settings_edit($event, $step, $message='', $show_panel=''){
		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		pagetop("Store Settings", $message);

		//JS INCLUDES
		//==================================
		//print_r($prefs);

		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/prototype.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/scriptaculous.js"></script>';

		$step = "update";

		//CSS FOR SETTINGS EDIT
		//==================================
		echo n.'<style type="text/css">'.

		 	 n.'td#article-main {'.
		 	 n.'	width: 500px;'.
		 	 n.'}'.



			 n.'.customerEdit legend{'.
			 n.'	font-size: 11px;'.
			 n.'	font-weight: bold;'.
			 n.'}'.

			 n.'.customerEdit label{'.
			 n.'	float: left;'.
			 n.'	width: 100px;'.
			 n.'	text-align: right;'.
			 n.'	padding: 8px 5px 5px 5px;'.
			 n.'}'.

			 n.'.customerEdit br{'.
			 n.'	clear: both;'.
			 n.'}'.

			 n.'.customerEdit p.desc{'.
			 n.'	padding: 0px; font-size: 10px; font-style:italic; padding-left: 110px; color:gray;'.
			 n.'}'.

			 n.'.customerEdit input, select{'.
			 n.'	margin: 5px 0 5px 0;'.
			 n.'	font-size: 11px;'.
			 n.'}'.
			 n.'#subNav {'.
			 n.' list-style:none;'.
			 n.' padding-left:0px; margin-left:0;'.
			 n.' background-color:#FFFFCC;'.
			 n.' border-bottom:1px solid #DDDDDD;'.
			 n.' border-left:1px solid #F1F1F1;'.
			 n.' border-right:1px solid #DDDDDD;'.
			 n.'}'.
			 n.'#subNav li {'.
			 n.' list-style:none;'.
			 n.' padding:5px 0 5px 0; margin-left:0;'.
			 n.'}'.
			 n.'#subNav li.selected {'.
			 n.' background-color:white;'.
			 n.'}'.

			 n.'#subNav li.selected a{'.
			 n.' color:gray;'.
			 n.'}'.

			 n.'#subNav li a{'.
			 n.' text-align:left;font-size: 10px; font-weight:bold; color:#996633; width:100%; padding:5px;'.
			 n.'}'.

			 n.'.rateList th{ background-color: #EEEEEE; text-align:left;}'.

			 n.'.rateList td, th{'.
			 n.' padding:3px;'.
			 n.'}'.

			 n.'.rateList {'.
			 n.' border-style:solid; border-width:0 1px 1px 1px; border-color:#DDDDDD; width:100%;'.
			 n.'}'.

			 n.'.zoneName{'.
			 n.' font-weight:normal; font-size: 110%; border-style:solid; border-width:0 0 1px 0; border-color:#DDDDDD; padding-bottom:5px; color:gray; margin-bottom:0;'.
			 n.'}'.


			 n.'</style>';

		//JS
		//==================================

		echo n.'<script type="text/javascript">'.
			  n.'<!--'.

			 n.'function showTab(tabID, fieldsetID){'.
			 n.'	$(tabID).className = "selected"'.
			 n.'	$(fieldsetID).style.display = "block"'.
			 n.'	tabs = $$("#subNav li");'.
			 n.'	for(i=0;i<tabs.length;i++){'.
			 n.'		tab = tabs[i]'.
			 n.'		if(tab.id != tabID){'.
			 n.'			tab.className = ""'.
			 n.'		}'.
			 n.'	}'.

			 n.'	fieldsets = $$(".settingsRegion");'.
			 n.'	for(i=0;i<fieldsets.length;i++){'.
			 n.'		fieldset = fieldsets[i]'.
			 n.'		if(fieldset.id != fieldsetID){'.
			 n.'			fieldset.style.display = "none"'.
			 n.'		}'.
			 n.'	}'.

			 n.'}'.

			 n.'function checkCountry(){'.
			 n.'	if($("store_country").value == "add_new"){'.
			 n.'		$("addCountry").style.display = "block";'.
			 n.'	} else {'.
			 n.'		$("addCountry").style.display = "none";'.
			 n.'	}'.
			 n.'}'.

			 n.'function deleteZone(id){'.
			 n.'	if(confirm("Are you sure?")){'.
			 n.'		$("zone_"+id+"_action").value = "delete_zone";'.
			 n.'		$("zone_"+id+"_form").submit()'.
			 n.'	}'.
			 n.'}'.

			 n.'function deleteRate(id){'.
			 n.'	if(confirm("Are you sure?")){'.
			 n.'		$("rates_"+id+"_action").value = "delete_rate";'.
			 n.'		$("rates_"+id+"_form").submit()'.
			 n.'	}'.
			 n.'}'.

			 n.'//-->'.
			 n.'</script>';


		echo	startTable('edit').

  		'<tr>'.n;

		//if ($view == 'text')
		//{

					//-- markup help --------------

		echo '<td id="article-col-1">'.
			 n.'<ul id="subNav">'.
			 n.'<li class="selected" id="generalSettingsTab" onclick="showTab(\'generalSettingsTab\', \'generalSettings\')"><a href="#" >General Settings</a></li>'.
			 n.'<li id="shippingZonesTab"><a href="#" onclick="showTab(\'shippingZonesTab\', \'shippingZones\')">Countries/Regions</a></li>'.
			 n.'<li id="shippingRatesTab"><a href="#" onclick="showTab(\'shippingRatesTab\', \'shippingRates\')">Shipping Rates</a></li>'.
			 n.'<li id="noticesTab"><a href="#" onclick="showTab(\'noticesTab\', \'notices\')">Notices</a></li>'.
			 n.'</ul></td>';


		echo '<td id="article-main">'.n;

		//General Settings
		//========================================
		echo "<div id='generalSettings' class='settingsRegion'>";

		$general_settings = safe_row("*", "store_settings", "1");

		extract($general_settings);

		echo n.n.'<form name="longform" method="post" action="index.php">';

		echo hInput('id', $id).
			eInput('settings').
			sInput($step).
			'<input type="hidden" name="settings_update" value="general" />'.

			n."<fieldset class='customerEdit'>".
			n."<legend>General Settings</legend>";

	 	echo  '<label for="owner_email">Event notify email</label>'.
			  n.'<input type="text" class="text" name="owner_email" id="owner_email" value="'.$owner_email.'"/>'.br.
			  n.'<p class="desc">Email will be used to notify of new orders, low inventory, etc.</p>'.

			  n.'<label for="store_address">Street Address</label>'.
			  n.'<input type="text" class="text" name="store_address" id="store_address" value="'.$store_address.'"/>'.br.

			  n.'<label for="store_city">City</label>'.
			  n.'<input type="text" class="text" name="store_city" id="store_city"  value="'.$store_city.'"/>'.br.

			  n.'<label for="store_state">State</label>'.
			  n.'<input type="text" class="text" name="store_state" id="store_state" value="'.$store_state.'"/>'.br.

			  n.'<label for="store_zip">Zip</label>'.
			  n.'<input type="text" class="text" name="store_zip" id="store_zip" value="'.$store_zip.'"/>'.br.

			  n.'<label for="store_country">Country</label>'.
			  n.build_list("store_country", "countries", "id", "name", $store_country,"1", false, "order by name asc").



			  n.'</fieldset>';

		if($unit_system == "metric"){
			 $metric_selected = 'selected="true"';

		}else{
			 $imperial_selected = 'selected="true"';
		}

		echo  "<fieldset class='customerEdit'>".
			  n."<legend>Standards &amp Formats</legend>".

			  n.'<label for="unit_system">Unit system</label>'.
			  n.'<select name="unit_system" id="unit_system"/>'.

			  n.'<option value="imperial" '.$imperial_selected.'>Imperial System (pound, inch)</option>'.
			  n.'<option value="metric" '.$metric_selected.'>Metric System (kilogram, centimeter)</option></select>'.

			  n.'<label for="store_currency">Store Currency</label>'.

			  n.build_list("store_currency", "currencies", "currency_code", "currency_name", $store_currency,"1").br;


		echo n.'</fieldset>';

		if($hide_inventory_when_depleted == 1){
			$inventory_hide_selected = "selected='true'";
		}else{
			$inventory_show_selected = "selected='true'";
			$display_message = "display:block;";
		}

		if(isset($inventory_management_on) && $inventory_management_on == 1){
			$inventory_management_on_checked="checked='checked'";
		}

		echo  "<fieldset class='customerEdit'>".
			  n."<legend>Inventory Settings</legend>".
			  n.'<p class="desc">These options only apply to products with inventory management turned on</p>'.
			  n.'<label for="hide_inventory_when_depleted">&nbsp;</label>'.
			  n.'<select name="hide_inventory_when_depleted" id="hide_inventory_when_depleted" onchange="if(this.value == \'0\'){$(\'inventory_message\').style.display = \'block\';}else{$(\'inventory_message\').style.display = \'none\'}"/>'.

			  n.'<option value="1" '.$inventory_hide_selected.'>Hide Inventory when depleted</option>'.
			  n.'<option value="0" '.$inventory_show_selected.'">Show message when inventory depleted</option></select>'.br.

			  n.'<div style="'.$display_message.'" id="inventory_message">'.
			  n.'<label for="depleted_inventory_message">Depleted Inventory Message</label>'.
			  n.'<textarea name="depleted_inventory_message" id="depleted_inventory_message">'.$depleted_inventory_message.'</textarea></div>'.br.

			  n.'<label for="inventory_management_on">&nbsp;</label>'.
			  n.'<input name="inventory_management_on" id="inventory_management_on" type="checkbox" value="1" style="float:left; margin-right:5px;" '.$inventory_management_on_checked.'/> <p style="padding-top:3px;">Send low inventory warning to store owner</p>'.br;


		echo n.'</fieldset>'.

		n.'<div class="submit">';
		//-- publish button --------------

		echo
		(has_privs('article.publish')) ?
		fInput('submit','publish',gTxt('update'),"publish", '', '', '', 4) :
		fInput('submit','publish',gTxt('update'),"publish", '', '', '', 4);

		echo n.'</div><!--/.submit--></form></div><!--/generalSettings-->';

		//Shipping Zones
		//========================================
		echo "<div id='shippingZones' style='display:none;' class='settingsRegion'><fieldset class='customerEdit'>".
			  n."<legend>Support a new country or region</legend>";

			  $zones = safe_rows("*", "shipping_zones", "1");

			  $countryCriteria = '1 ';
			  foreach($zones as $zone){
			  	$countryCriteria .= " AND id != ".$zone['country_id']. " ";
			  }

			  echo n.n.'<form name="longform" method="post" action="index.php">'.
			  eInput('settings').
			  sInput($step);

			  echo '<input type="hidden" name="settings_update" value="add_zone"/>';

			  echo '<label for="country_id">Country</label>'.
					 n.build_list("country_id", "countries", "id", "name", " ",$countryCriteria, true, "order by name asc").
					 n.'</select>'.br;

			  /*echo '<label for="shipping_rate_id">Shipping rate</label>'.
					 n.build_list("shipping_rate_id", "shipping_rates", "id", "title", " ", true, "1").
					 n.'</select>'.br;
			  echo n.'<p class="desc">You can add more shipping rates to your regions in the Shipping Rates section.</p>';*/
			  if(empty($tax_rate)){
					$tax_rate = "0.0";
			  }

			  echo '<label for="tax_rate">Tax Rate</label>'.
					  n.'<input type="text" name="tax_rate" id="tax_rate" value="'.$tax_rate.'" style="width: 20px">'.br;

			  echo '<label>&nbsp;</label>';
			  echo  fInput('submit','Add','Add',"smallerbox", '', '', '', 4);
			  echo '</form>';



			  echo n.'</fieldset>';
			  echo n."<h2>Your Regions</h2>";
			  echo '<table cellpadding="0" cellspacing="0" border="0" class="rateList">';
			  echo n.'<tr><th>Country</th><th colspan="2">Tax Rate</th></tr>';



			  foreach($zones as $zone){
					extract($zone);
			  		echo n.n.'<form name="longform" id="zone_'.$id.'_form" method="post" action="index.php">'.
							hInput('id', $id).
							eInput('settings').
							sInput($step);

					echo '<input type="hidden" name="settings_update" id="zone_'.$id.'_action" value="update_zone"/>';

					echo '<tr><td>'.$name.'</td>'.
					     n.'<td><input type="text" name="tax_rate" value="'.$tax_rate.'"/></td>'.
					     n.'<td style="text-align:right;"><input type="submit" value="update" class="smallerbox"/> or <a href="javascript:deleteZone(\''.$id.'\')">Delete</a></td>'.
					     n.'</tr></form>';


			  }
			  echo "</table>";

			  echo n.'</div><!--/shippingZones-->';

		//Shipping Rates
		//========================================
		echo "<div id='shippingRates' style='display:none;' class='settingsRegion'>".
			  n."<h2>Shipping Rates</h2>";
			  $zones = safe_rows("*", "shipping_zones", "1");
 			  foreach($zones as $zone){
					extract($zone);

					echo '<h4 class="zoneName">'.$name.' <small>[<a href="#" onclick="$(\'addNewShippingRate_'.$id.'\').style.display = \'block\'">Add new shipping rate</a>]</small></h4>';
					$sql = "select *, shipping_rates.id as shipping_rate_id from shipping_rates
										JOIN zones_rates
										ON zones_rates.shipping_rate_id = shipping_rates.id
										JOIN shipping_zones
										ON shipping_zones.id = zones_rates.shipping_zone_id
										WHERE zones_rates.shipping_zone_id = $id";

					$rates = safe_query($sql);

					echo '<table cellpadding="0" cellspacing="0" border="0" class="rateList">';
					if(mysql_num_rows($rates) > 0){
						echo n.'<tr><th style="width:150px;">Label</th><th style="width:100px;">Amount</th><th style="width:250px;">Weight Range</th></tr>';
					}
					$background = "#FFFFFF";
					while($rate = mysql_fetch_assoc($rates)){
							echo '<tr><td colspan="3" style="background-color: '.$background.'">';

							echo n.n.'<form name="longform" method="post" action="index.php" id="rates_'.$rate['shipping_rate_id'].'_form">'.
							hInput('id', $id).
							eInput('settings').
							sInput($step);

							echo '<input type="hidden" name="settings_update" id="rates_'.$rate['shipping_rate_id'].'_action" value="update_rate"/>';
							echo '<input type="hidden" name="shipping_rate_id" value="'.$rate['shipping_rate_id'].'"/>';

							echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td style="width:150px;"><input type="text" value="'.$rate['title'].'" name="title"/></td>'.
									 n.'<td valign="center" style="width:100px;padding:0;"><input type="text" value="'.number_format($rate['rate'], 2).'" name="rate" style="width:25px;"/> USD</td>'.
									 n.'<td valign="center" style="width:130px;padding:0;"><input type="text" name="start_weight" value="'.$rate['start_weight'].'" style="width:25px;"/> lbs - <input type="text" name="end_weight" value="'.$rate['end_weight'].'" style="width:25px;"/> lbs</td>'.
									 n.'<td style="text-align:right; width:120px;padding:0;"><input type="submit" value="update" class="smallerbox"/> or <a href="javascript:deleteRate(\''.$rate['shipping_rate_id'].'\')">delete</a></td></tr></table>';

							echo '</form>';
						  echo "</td></tr>";
						  if($background == "#F6F6F6"){
								$background = "#FFFFFF";
							}else{
								$background = "#F6F6F6";
							}
					}


					echo '</table>';
					echo br.n.n.'<form name="longform" method="post" action="index.php">'.
							hInput('id', $id).
							eInput('settings').
							sInput($step);

							echo '<input type="hidden" name="settings_update" value="add_rate"/>';

							echo '<table id="addNewShippingRate_'.$id.'" style="display: none; border-width:1px 1px 1px 1px;" cellpadding="0" cellspacing="0" border="0" class="rateList">';
							echo n.'<tr><th style="width:150px;">Label</th><th style="width:100px;">Amount</th><th style="width:250px;" colspan="2">Weight Range</th></tr>';
							echo '<tr style="background-color: '.$background.'"><td><input type="text" value="" name="title" style="width:100%;"/></td>'.
									 n.'<td valign="center"><input type="text" value="" name="rate" style="width:25px;"/> USD</td>'.
									 n.'<td valign="center"><input type="text" name="start_weight" value="0" style="width:25px;"/> lbs - <input type="text" name="end_weight" value="0" style="width:25px;"/> lbs</td>'.
									 n.'<td style="text-align:right;"><input type="submit" value="save" class="smallerbox"/> <small><a href="javascript:void(0)" onclick="$(\'addNewShippingRate_'.$id.'\').style.display = \'none\'">cancel</a></small></td></tr>';

							echo '</table></form>';

				}


			  echo n.'</div><!--/shippingRates-->';

		//Notices Management
		//========================================
		echo "<div id='notices' style='display:none;' class='settingsRegion'>".
			  n."<h2>Notices</h2>";

			  echo n.n.'<form name="longform" method="post" action="index.php">'.
							hInput('id', $id).
							eInput('settings').
							sInput($step);
			  echo '<input type="hidden" name="settings_update" value="notices" />';

			  echo n.'<fieldset class="customerEdit">'.
			  	   n.'<legend>Customer Notifications</legend>';




			  echo n.'<label for="order_confirmation_form" style="width:180px;">Order Confirmation Form</label>'.
			  	  n.build_list("order_confirmation_form", "txp_form", "name", "name", $order_confirmation_form,"type = 'article' and name != 'default'", true, "order by name asc");
			  echo '<p class="desc">TXP will send the customer a copy of this form when they complete a purchase</p>';
			  echo n.'</fieldset>';

			  echo n.'<fieldset class="customerEdit">'.
			  	   n.'<legend>Your Notifications</legend>';


			  echo n.'<label for="new_order_use_form" style="width:180px;">New Order Notification Form</label>'.
			  	  n.build_list("new_order_notification_form", "txp_form", "name", "name", $new_order_notification_form,"type = 'article' and name != 'default'", true, "order by name asc");

			  echo '<p class="desc">This is the email that TXP will send to you when a customer makes a new purchase</p>';
			  echo n.'</fieldset>';

			  echo fInput('submit','publish',gTxt('update'),"publish", '', '', '', 4);

			  echo n.'</form></div><!--/notices-->';


		if(isset($show_panel)){
			if($show_panel == "zones"){
				echo '<script type="text/javascript">'.
					 n.'<!--'.
					 n.'showTab("shippingZonesTab", "shippingZones");'.
					 n.'//-->'.
					 n.'</script>';

			}else if($show_panel == "rates"){
				echo '<script type="text/javascript">'.
					 n.'<!--'.
					 n.'showTab("shippingRatesTab", "shippingRates");'.
					 n.'//-->'.
					 n.'</script>';

			}else if($show_panel == "notices"){
				echo '<script type="text/javascript">'.
					 n.'<!--'.
					 n.'showTab("noticesTab", "notices");'.
					 n.'//-->'.
					 n.'</script>';
			}
		}


		echo '</td>';

		echo '</tr></table>';



	} //--- end settings

	function store_categories($event, $step){
		global $statuses, $comments_disabled_after, $step, $txp_user;

		pagetop("Categories", $message);

		//print_r($hits);
		echo "<h4 style='text-align:center;'>Coming soon</h4>";

	}

	function orders_export($event, $step){

		$type = $_GET['type'];

		$criteria = '';

		if($type == "pending" || $type == "approved" || $type=="shipped" || $type=="declined" || $type=="void"){
			$criteria .= " order_status = '$type' ";
		}else if($type == "lastweek"){
			$criteria .= " date_created >= ".date('Y-m-d', strtotime("7 days ago"));
		}else if($type == "lastmonth"){
			$criteria .= " date_created >= ".date('Y-m-d', strtotime("30 days ago"));
		}else{
			$criteria .= " id not null";
		}

		$sql = "
			SELECT id, date_created, subtotal, tax, shipping_handling, discount, total, transaction_id, tracking_number, last_updated, order_status, ship_date, ship_method, memo, note, payment_method, email, billing_firstname, billing_lastname, billing_company, billing_address1, billing_address2, billing_city, billing_state, billing_zip, billing_country, billing_fax, billing_phone, shipping_firstname, shipping_lastname, shipping_company, shipping_address1, shipping_address2, shipping_city, shipping_state, shipping_zip, shipping_country, shipping_fax, shipping_phone
			FROM orders
			JOIN txp_users AS users ON users.user_id = orders.user_id WHERE $criteria";

		$orders = safe_query($sql);


		header("Content-type: application/csv");
		header("Content-disposition:attachment;filename=order_export_".date("Y-m-d").".csv");

		echo "id, date_created, subtotal, tax, shipping_handling, discount, total, transaction_id, tracking_number, last_updated, order_status, ship_date, ship_method, memo, note, payment_method, email, billing_firstname, billing_lastname, billing_company, billing_address1, billing_address2, billing_city, billing_state, billing_zip, billing_country, billing_fax, billing_phone, shipping_firstname, shipping_lastname, shipping_company, shipping_address1, shipping_address2, shipping_city, shipping_state, shipping_zip, shipping_country, shipping_fax, shipping_phone, ITEMS \n";

		while($order = mysql_fetch_assoc($orders)){

			echo implode(",", $order);

			$sql = "
				SELECT *
				FROM textpattern AS txp
				JOIN orders_articles AS oa
				ON oa.article_id = txp.ID
				WHERE oa.order_id = ".$order['id'];

			$products = safe_query($sql);

			while($product = mysql_fetch_assoc($products)){
				echo ",". $product['Title'] . " " . $product['custom_1'];
			}
			echo "\n";
		}
		die();
	}

	function orders_update($event, $step){

		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));

		if(isset($ship_now)){
			$ship_date = 'current_timestamp()';
		}else{
			$ship_date = "'".date("Y-m-d", strtotime($ship_year.'-'.$ship_month.'-'.$ship_day))."'";
		}
		$rs = safe_update('orders', "
			tracking_number		 = '$tracking_number',
			order_status		 = '$order_status',
			ship_date 			 = $ship_date,
			ship_method			 = '$ship_method',
			note 				 = '$note'",
			"id = $id"
			);
		if ($rs)
		{
			orders_edit('', '', "Order updated");
		}else{
			orders_edit("There was an error trying to update the order: ".mysql_error());
		}

	}
	
	function orders_delete($event, $step){

		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));

		if(isset($id)){		
			$rs = safe_delete('orders', "id = $id");
		}
		if ($rs)
		{
			orders_list('', '', "Order Deleted");
		}else{
			orders_list("There was an error trying to update the order: ".mysql_error());
		}

	}
	
	function orders_edit($event, $step, $message=''){

		global $statuses, $comments_disabled_after, $step, $txp_user;

		pagetop("Order Edit", $message);

		extract(doSlash($_REQUEST));

		$sql = "
			SELECT *
			FROM orders
			JOIN txp_users AS users ON users.user_id = orders.user_id
			WHERE orders.id = $id";

		$order = safe_query($sql);

		$order = mysql_fetch_assoc($order);

		extract($order);

		$sql = "
			SELECT *
			FROM textpattern AS txp
			JOIN orders_articles AS oa
			ON oa.article_id = txp.ID
			WHERE oa.order_id = $id";

		$products = safe_query($sql);


		//echo "<h4 style='text-align:center;'>Coming soon</h4>";


		//CSS FOR CUSTOMER EDIT
		//==================================
		echo n.'<style type="text/css">'.
			  n.'.customerEdit legend{'.
			 n.'	font-size: 11px;'.
			 n.'	font-weight: bold;'.
			 n.'}'.

			 n.'.customerEdit label{'.
			 n.'	float: left;'.
			 n.'	width: 100px;'.
			 n.'	text-align: right;'.
			 n.'	padding: 8px 5px 5px 5px;'.
			 n.'}'.

			 n.'.customerEdit br{'.
			 n.'	clear: both;'.
			 n.'}'.

			 n.'.customerEdit p.desc{'.
			 n.'	padding: 0px; font-size: 10px; font-style:italic; padding-left: 110px; color:gray;'.
			 n.'}'.

			 n.'.customerEdit input, select{'.
			 n.'	margin: 5px 0 5px 0;'.
			 n.'	font-size: 11px;'.
			 n.'}'.
			 n.'.data {'.
			 n.' width:100%;'.
			 n.'}'.
			 n.'.data th {'.
			 n.' text-align:left; color:gray; font-size: 10px;'.
			 n.'}'.


			 n.'</style>';

		echo n.'<script type="text/javascript">'.
			  n.'<!--'.

			 n.'function deleteCustomer(){'.
			 n.'	if(confirm("Are you sure?")){'.
			 n.'		document.getElementById("customerDelete").submit(); '.
			 n.'	}'.
			 n.'}'.
			 n.'//-->'.
			 n.'</script>';

		$step = 'update_order';

		echo n.n.'<form name="longform" method="post" action="index.php">';

		echo hInput('id', $id).
			eInput('orders').
			sInput($step).
			'<input type="hidden" name="view" />'.

			startTable('edit').

  		'<tr>'.n;

		//if ($view == 'text')
		//{

					//-- markup help --------------



		echo '<td id="article-main">'.n;
		echo "<fieldset class='customerEdit'>".
			  n."<legend>Products</legend>";
			  echo n."<table class='data'><tr>";
			  echo n."<th>Product</th><th>Price</th><th>Quantity</th></tr>";
			  while($product = mysql_fetch_assoc($products)){
			  		extract($product);
			  		echo "<tr><td>$Title</td><td>$$custom_1</td><td>$quantity</td></tr>";
			  }
			  echo n."</tr></table>";
			  echo "<hr/>";
			  echo n."<table><tr><td><strong>Subtotal:</strong></td><td> $$subtotal</td></tr>".n;
			  echo n."<tr><td><strong>Tax:</strong></td><td> $$tax</td></tr>".n;

			  echo n."<tr><td><strong>Shipping &amp; Handling:</strong></td><td> $$shipping_handling</td></tr>".n;

			  if(!empty($discount)){
			  	echo n."<tr><td><strong>Discount:</strong></td><td> $$discount</td></tr>".n;
			  }

			  echo n."<tr><td><strong>Total:</strong> </td><td><strong>$$total</strong></td></tr></table>".n;

			  echo n.'</fieldset>';

		  echo "<fieldset class='customerEdit'>";
			  echo "<table><tr>";
			  if(isset($shipping_same_as_billing) && $shipping_same_as_billing == "1"){
			  	 echo '<td><strong>Ship to </strong>[<a href="?event=customers&step=edit_customer&user_id='.$user_id.'">edit</a>]'.br.$RealName.br.$billing_address1.br;
			  	 if(isset($billing_address2) && !empty($billing_address2)){
			  	 	echo $billing_address2.br;
			  	 }
			  	 echo $billing_city.', ';
			  	 echo $billing_state.' ';
			  	 echo $billing_zip.br;
			  	 echo $billing_country.'</td>';
			  }else{
			  	 echo '<td>'.$shipping_address1.br;
			  	 if(isset($shipping_address2)){
			  	 	echo $shipping_address2.br;
			  	 }
			  	 echo $shipping_city.br;
			  	 echo $shipping_state.br;
			  	 echo $shipping_zip.br;
			  	 echo $shipping_country.'</td>';
			  }

			  echo n."<td style='padding-left:10px;'><strong>Bill to </strong>[<a href=\"?event=customers&step=edit_customer&user_id=$user_id\">edit</a>]".br;
			  echo $RealName.br.$billing_address1.br;
			  if(isset($billing_address2) && !empty($billing_address2)){
				echo $billing_address2.br;
			  }
			  echo $billing_city.', ';
			  echo $billing_state.' ';
			  echo $billing_zip.br;
			  echo $billing_country.'</td>';

			  echo "</tr></table>".br;
			  echo n."<p style='padding-left:3px;'><strong>Payment info</strong>".br;
			  echo n.'Method: '.$payment_method.br.
			  	   n.'Transaction ID: '.$transaction_id.br.'</p>';
			  echo n.'</fieldset>';

		  echo "<fieldset class='customerEdit'>".
		  n."<legend>Order Update</legend>";

		  if(isset($ship_date) && !empty($ship_date)){
		  	 $ship_date = strtotime($ship_date);
		  	 $ship_year = date("Y", $ship_date);
		  	 $ship_month = date("m", $ship_date);
		  	 $ship_day = date("d", $ship_date);
		  }else{
		  	 $ship_year = date("Y");
		  	 $ship_month = date("m");
		  	 $ship_day = date("d");
		  }

		  if(isset($order_status)){

		  	if($order_status == "pending"){
		  		$pending_selected = "selected='true'";

		  	}else if($order_status == "approved"){
		  		$approved_selected = "selected='true'";

		  	}else if($order_status == "shipped"){
		  		$shipped_selected = "selected='true'";

		  	}else if($order_status == "declined"){
		  		$declined_selected = "selected='true'";

		  	}else if($order_status == "void"){
		  		$void_selected = "selected='true'";
		  	}
		  }

		  echo
		  n.'<label for="order_status">Status</label>'.
		  n.'<select id="order_status" name="order_status">'.
		  n.'	<option value="pending" '.$pending_selected.'>Pending</option>'.
		  n.'	<option value="approved" '.$approved_selected.'>Approved</option>'.
		  n.'	<option value="shipped" '.$shipped_selected.'>Shipped</option>'.
		  n.'	<option value="declined" '.$declined_selected.'>Declined</option>'.
		  n.'	<option value="void" '.$void_selected.'>Void</option>'.
		  n.'</select>'.br.
		  n.'<label for="ship_year">Ship Date</label>'.
		  n.listbox_year('ship_year', '2004', date('Y'), $ship_year) . ' ' . get_html_select_month("ship_month", $ship_month).
		  n.'<input type="text" name="ship_day" value="'.$ship_day.'" style="width: 20px;"/> &nbsp; '. br .
		  n.'<label for="ship_now">&nbsp;</label><div style="float:left; padding-top: 5px; padding-right:4px;">Shipped today</div> <input id="ship_now" type="checkbox" name="ship_now" value="'.date('Y-m-d').'" style="float:none;"/>'. br.
		  n.'<label for="ship_method">Ship method</label>'.
	 	  n.'<input type="text" name="ship_method" id="ship_method" value="'.$ship_method.'"/> &nbsp; '. br.
	 	  n.'<p class="desc">Example: USPS Priority, FedEx, etc.</p>'.
	 	  n.'<label for="tracking_number">Tracking number</label>'.
	 	  n.'<input type="text" name="tracking_number" id="tracking_number" value="'.$tracking_number.'"/> &nbsp; '. br.

		  n.'<label for="note">Note</label>'.
	 	  n.'<textarea name="note" id="note"/>'.$note.'</textarea>'. br . br;
		  echo n.'</fieldset>';

		echo '</td>';
		echo '<td id="article-col-2" style="padding-top: 13px;">'; //start article-col-2
		echo '<a href="?event=customers&step=edit_customer" class="navlink">Print packing slip</a>'.br.br;
		//-- publish button --------------
		echo
		(has_privs('article.publish')) ?
		fInput('submit','publish',gTxt('update'),"publish", '', '', '', 4) :
		fInput('submit','publish',gTxt('update'),"publish", '', '', '', 4);

		if($user_id){

			$orders = safe_rows("*", "orders", "user_id = $user_id AND id != $id ORDER BY last_updated DESC");
			if(count($orders) > 0){
				echo br.br."<fieldset>".
						n.'<legend>Other Orders by '. $RealName .'</legend>'.
						n.'<ul class="plain-list">';

						foreach($orders as $order){
							echo n.'<li><a href="?event=orders&step=edit_order&id='.$order['id'].'">ORDER #'.$order['id'].'</a> <em style="font-size:10px;">'.date('M j y', strtotime($order['date_created'])).'</em></li>';
						}
						n.'</ul>';
				echo '</fieldset>';
			}

		}
		echo '</td></tr></table></form>';

	}

	function orders_list($event, $step, $message=''){

		global $statuses, $comments_disabled_after, $step, $txp_user;

		pagetop("Orders", $message);

		extract(get_prefs());
		extract(doSlash($_REQUEST));
		extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method')));

		$sesutats = array_flip($statuses);

		$dir = ($dir == 'desc') ? 'desc' : 'asc';

		switch ($sort)
		{
			case 'id':
				$sort_sql = 'id '.$dir;
			break;

			case 'user_id':
				$sort_sql = 'user_id '.$dir;
			break;

			default:
				$dir = 'desc';
				$sort_sql = 'id '.$dir;
			break;
		}

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$criteria = "order_status = 'pending'";

		if(isset($show_approved)){
			$criteria .= " OR order_status = 'approved'";
			$show_approved_selected = "checked='true'";
		}

		if(isset($show_shipped)){
			$criteria .= " OR order_status = 'shipped'";
			$show_shipped_selected = "checked='true'";
		}
		if(isset($show_declined)){
			$criteria .= " OR order_status = 'declined'";
			$show_declined_selected = "checked='true'";
		}
		if(isset($show_void)){
			$criteria .= " OR order_status = 'void'";
			$show_void_selected = "checked='true'";
		}



		$total = safe_count('orders', "$criteria");



		$limit = max(@$article_list_pageby, 15);

		list($page, $offset, $numPages) = pager($total, $limit, $page);

		$rs = safe_rows_start('*', 'orders',
			"$criteria order by $sort_sql limit $offset, $limit"
		);

		$exportOptions = '<div style="display:none" id="exportOptions" class="list">'
							.br.'Export <select name="orderType" onchange="if(this.value!=\'\'){window.location=\'http://'.$siteurl.'/textpattern/index.php\'+this.value}">'.n.
							n.'<option value="" selected="true">Select export options</option>'.
							n.'<option value="?event=orders&step=export_orders&type=pending">Pending</option>'.
							n.'<option value="?event=orders&step=export_orders&type=approved">Approved</option>'.
							n.'<option value="?event=orders&step=export_orders&type=shipped">Shipped</option>'.
							n.'<option value="?event=orders&step=export_orders&type=declined">Declined</option>'.
							n.'<option value="?event=orders&step=export_orders&type=void">Void</option>'.
							n.'<option value="?event=orders&step=export_orders&type=lastweek">All orders in the last 7 days</option>'.
							n.'<option value="?event=orders&step=export_orders&type=lastmonth">All orders in the last 30 days</option>'.
							n.'<option value="?event=orders&step=export_orders&type=all">All orders ever!</option></select> [<a href="javascript:void(0)" onclick="document.getElementById(\'exportOptions\').style.display = \'none\';">cancel</a>]'.
						 	'</div>';
		if ($rs)
		{

			echo n.n.'<form name="longform" method="post" action="index.php" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">'.
			n.'<input type="hidden" name="event" value="orders"/>'.n.
			n.startTable('list','','','','700').

				n.tr(
					n.tda("Displaying $offset - $limit of $total orders ", ' colspan="2" style="border: none; padding-bottom: 15px;"').
					n.tda('<a href="javascript:void(0)" onclick="document.getElementById(\'exportOptions\').style.display=\'block\';" class="navlink">Export orders</a>'.$exportOptions, ' colspan="3" style="text-align: right; border: none; padding-bottom: 15px;"')
				).
				n.tr(
					n.tda('Show <input type="checkbox" value="pending" name="show_pending" checked="checked" disabled="true"/> Pending
						   <input type="checkbox" value="approved" name="show_approved" '.$show_approved_selected.'/> Approved
						   <input type="checkbox" value="shipped" name="show_shipped" '.$show_shipped_selected.'/> Shipped
						   <input type="checkbox" value="declined" name="show_declined" '.$show_declined_selected.'/> Declined
						   <input type="checkbox" value="void" name="show_void"'.$show_void_selected.' /> Void
						   <input type="submit" value="Refresh"/> ', ' colspan="4" style="border: none; padding-bottom: 15px;"')
				).
				n.tr(
					n.column_head('Order #', 'id', 'orders', true, $switch_dir, $crit, $search_method).
					column_head('Customer', 'user_id', 'orders', true, $switch_dir, $crit, $search_method).
					column_head('Order Date', 'date_created', 'orders', true, $switch_dir, $crit, $search_method).
					column_head('Total', 'total', 'orders', true, $switch_dir, $crit, $search_method).
					column_head('Status', 'order_status', 'orders', true, $switch_dir, $crit, $search_method)
				);

			include_once txpath.'/publish/taghandlers.php';

			while ($a = nextRow($rs))
			{
				extract($a);
				$edit_link = '<a href="?event=orders&step=edit_order&id='.$id.'">'.$id.'</a> <small>[<a href="?event=orders&step=edit_order&id='.$id.'">Edit</a> | <a href="?event=orders&step=delete_order&id='.$id.'" onclick="if(!confirm(\'Are you sure?\')){return false;}">Delete</a>]</small>';
				$user = safe_row("RealName, user_id", "txp_users", "user_id = '$user_id'");
				$user_link = '<a href="?event=customers&step=edit_customer&user_id='.$user_id.'">'.$user['RealName'].'</a>';
				echo n.n.tr(

					n.td($edit_link).

					td($user_link).

					td($date_created).

					td('$'.$total).

					td($order_status)
				);
			}

			echo n.endTable().
			n.'</form>'.

			n.nav_form('list', $page, $numPages, $sort, $dir, $crit, $search_method).

			n.pageby_form('list', $article_list_pageby);
		}

	}

	function show_dashboard($event, $step){

		global $statuses, $comments_disabled_after, $step, $txp_user, $prefs;

		pagetop("Store Dashboard", $message);

		//CSS FOR CUSTOMER EDIT
		//==================================
		echo n.'<style type="text/css">'.

			 n.'ul {'.
			 n.' width:300px; list-style:none; padding:0; margin:0;'.
			 n.'}'.

			 n.'#hits {'.
			 n.' width:300px; list-style:none; padding:0; margin:0;'.
			 n.'}'.

			 n.'#hits li{'.
			 n.' font-size: 10px; background-color:#FFFFCC; padding:3px; border-style:solid; border-color:#E4E4E8; border-width:0 1px 1px 0; text-align:right; margin-left:0px;'.
			 n.'}'.

			 n.'.referrers li{'.
			 n.' font-size: 11px; padding:3px; border-style:dotted; border-color:#E4E4E8; border-width:0 0 1px 0; margin-left:0px; text-align:right;'.
			 n.'}'.


			 n.'li span{'.
			 n.' float:left;'.
			 n.'}'.

			 n.'.box{'.
			 n.' border-style:solid; border-color:#CACAD2; border-width:1px; padding:5px;'.
			 n.'}'.

			 n.'.data {'.
			 n.' border-style:solid; border-color:#CACAD2; border-width:1px 0 0 0; width:400px;'.
			 n.'}'.

			 n.'.data th{'.
			 n.' padding:5px; background-color:#E8E8EC; font-size: 11px; font-weight:normal; text-align:left;'.
			 n.'}'.

			 n.'.data td{'.
			 n.' padding: 2px 5px 2px 5px; font-size:10px;'.
			 n.'}'.

			 n.'</style>';

		/* PERCENTAGE LOGIC
		================================*/
		$totalHits = 0;
		$allHits = array();
		$biggestHit = 0;

		for($i=0;$i<7;$i++){
			if($i==0){
				$hits = safe_count("txp_log", "time like '".date("Y-m-d",strtotime("today"))."%'");
				$totalHits = $totalHits + $hits;
				$allHits[$i] = $hits;
				if($hits > $biggestHit ){
					$biggestHit = $hits;
				}
			}else{
				$hits = safe_count("txp_log", "time like '".date("Y-m-d",strtotime($i." day ago"))."%'");
				$totalHits = $totalHits + $hits;
				$allHits[$i] = $hits;
				if($hits > $biggestHit ){
					$biggestHit = $hits;
				}
			}
		}
		/* END PERCENTAGE LOGIC
		================================*/


		echo startTable("edit");

		$plugin_info = safe_row("*", "txp_plugin", "name = 'ln_txp_commerce'");

		echo "<tr><td colspan='2'>"
			 ."<h2 style='font-size: 18px; padding-left:6px;'>".$prefs['sitename']." Dashboard <small style='font-size:11px;'> - <a href='".$plugin_info['author_uri']."'>ln_txp_commerce</a> version ".$plugin_info['version']."</small></h2>".
			 n."</td></tr>";

		$daysShown = false;
		echo '<tr><td class="column">'.
			tag("Visits in the last 7 days", 'h2');
			echo "<div class='box'>";
			echo '<ul id="hits">';
			for($i=0;$i<count($allHits);$i++){
				$hits = $allHits[$i];
				if($i==0){
					$date = date("D M j",strtotime("today"));
				}else{
					$date = date("D M j",strtotime($i." day ago"));
				}
				@$percent = round (($hits / $biggestHit) * 100);
				if($percent > 0){
					echo tag(tag("$date", "span")."<strong>$hits Hits</strong> ", "li", " style=\"width:$percent%\"");
					$daysShown = true;
				}
			}
			if($daysShown == false){
				echo tag("No hits for this week.", "li");
			}
			echo "</ul></div>";

			/* REFERRER LOGIC
			================================*/
			$allReferrers = array();
			$sql = "SELECT refer, count( refer ) AS count
					FROM txp_log
					WHERE time > '".date("Y-m-d H:i:s",strtotime("7 days ago"))."' and refer != ''
					GROUP BY refer ORDER BY count DESC";

			$referers = safe_query($sql);

			/* END REFERRER LOGIC
			================================*/

			echo br.tag("Top referrers in the last 7 days", 'h2');
			echo "<div class='box'>";
			echo '<ul class="referrers">';
			if(mysql_num_rows($referers) > 0){
				while($refer = mysql_fetch_assoc($referers)){
					extract($refer);
					$hits = safe_count("txp_log", "time > '".date("Y-m-d H:i:s",strtotime("7 days ago"))."' AND refer='".doSlash($refer)."'");
					if(strlen($refer)>40){
						$dot = "...";
					}else{
						$dot = "";
					}
					echo tag(tag("<a href='http://$refer'>".substr($refer,0, 40).$dot."</a> ", "span")." $count hits", "li");
				}
			}else{
				echo tag("No referrals for this week.", "li");
			}
			echo "</ul></div>";
		echo "</td><!--/end left column -->";

		/* ORDERS LOGIC
		=============================*/

		$orders = safe_rows("*", "orders, txp_users", "orders.date_created >= '".date("Y-m-d H:i:s",strtotime("7 days ago"))."' AND orders.user_id = txp_users.user_id order by orders.date_created desc");
		$order_total = safe_row("sum(total) as total", "orders", "orders.date_created >= '".date("Y-m-d H:i:s",strtotime("7 days ago"))."'");
		/* END ORDERS LOGIC
		================================*/

		echo '<td class="column" style="width:400px;">'.
		tag("This week's orders", 'h2').
		startTable("","","data").tr(
				hCell("Order number").
				hCell("Customer").
				hCell("Date").
				hCell("Total"));

		$background = "#FFFFFF";

		if(count($orders) > 0){
			foreach($orders as $order){
				extract($order);
				echo tr(
					tda($id . " [<a href='?event=orders&step=edit_order&id=".$id."'>edit</a>] ", ' style="background-color:'.$background.';"').
					tda('<a href="?event=customers&step=edit_customer&user_id='.$user_id.'">'.$billing_firstname." ".$billing_lastname.'</a>', ' style="background-color:'.$background.';"').
					tda(date("Y-m-d", strtotime($date_created)), ' style="background-color:'.$background.';"').
					tda("$".number_format($total,2), ' style="background-color:'.$background.';"')
				);
				if($background == "#F6F6F6"){
					$background = "#FFFFFF";
				}else{
					$background = "#F6F6F6";
				}


			}
		}else{
			echo "<td colspan='4'>No orders for this week.</td>";
		}
		echo tr(
			tda("<strong>Total: $".number_format($order_total['total'],2)."</strong>", ' colspan="4" style="background-color:#E8E8EC; text-align:right; border-style:solid; border-color:#CACAD2; border-width:1px 0 0 0;"')
		);

		echo endTable();


		/* POPULAR PAGES LOGIC
		=============================*/


		$sql = "SELECT page, count( refer ) AS count
				FROM txp_log
				WHERE time > '".date("Y-m-d H:i:s",strtotime("7 days ago"))."' and page != '/textpattern/'
				GROUP BY page ORDER BY count DESC";

		$pages = safe_query($sql);

		/* END POPULAR PAGES LOGIC
		================================*/
		
		echo br.tag("This week's popular pages", 'h2').
		startTable("","","data");

		$background = "#FFFFFF";

		if(mysql_num_rows($pages) > 0){
			while($pageData = mysql_fetch_assoc($pages)){
				extract($pageData);
				
				if(strlen($page)>40){
					$dot = "...";
					$page = substr($page,0, 55);
				}else{
					$dot = "";
				}
				
				echo tr(
					tda("<a href='$page'>$page$dot</a>", ' style="background-color:'.$background.';"').
					tda($count." hits", ' style="background-color:'.$background.';"')
				);
				if($background == "#F6F6F6"){
					$background = "#FFFFFF";
				}else{
					$background = "#F6F6F6";
				}
			}
		}else{
			echo "<td colspan='2'>No pages viewed this week.</td>";
		}
		echo endTable();
		echo "</td><!--/end right column --></tr>"
			 .endTable();


	}

	function customers_switch($event, $step){

		switch(strtolower($step)) {
			// 'zem_admin_test' will be called to handle the new event
			case "":  				customers_list($event, $step); 	break;
			case "list":			customers_list($event, $step); 	break;
			case "customers_change_pageby":	customers_list($event, $step); 	break;
			case "edit_customer":  	customer_edit($event, $step); 	break;
			case "delete_customer": customer_delete($event, $step); 	break;
			case "save_customer":  	customer_save($event, $step); 	break;
			case "update_customer": customer_update($event, $step); break;
			case "export_customer": customer_export($event, $step); break;

		}
	}

	function customer_save($event, $step){
		
		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));

		$RealName = $billing_firstname . " ". $billing_lastname;

		if(!isset($shipping_same_as_billing)){
			$shipping_same_as_billing = 0;
		}else{
			$shipping_same_as_billing = 1;
		}
	    
	    
		if(!function_exists("generate_password")){
			require_once txpath.'/include/txp_admin.php'; 
		}
		if(!function_exists("is_valid_email")){
			require_once txpath.'/lib/txplib_misc.php'; 
		}
	    
		if ($name && is_valid_email($email))
		{
			
			$password = doSlash(generate_password(6));
			
			$nonce = doSlash(md5(uniqid(rand(), true)));

			$rs = safe_insert('txp_users', "
				privs		 = 0,
				name		 = '$name',
				email		 = '$email',
				RealName = '$RealName',
				pass		 =	password(lower('$password')),
				nonce		 = '$nonce',
				billing_company = '$billing_company',
				billing_address1 = '$billing_address1',
				billing_address2 = '$billing_address2',
				billing_city = '$billing_city',
				billing_state = '$billing_state',
				billing_zip = '$billing_zip',
				billing_country = '$billing_country',
				billing_fax = '$billing_fax',
				billing_phone = '$billing_phone',
				shipping_same_as_billing = $shipping_same_as_billing,
				shipping_company = '$shipping_company',
				shipping_address1 = '$shipping_address1',
				shipping_address2 = '$shipping_address2',
				shipping_city = '$shipping_city',
				shipping_state = '$shipping_state',
				shipping_zip = '$shipping_zip',
				shipping_country = '$shipping_country',
				shipping_fax = '$shipping_fax',
				shipping_phone = '$shipping_phone',
				shipping_firstname = '$shipping_firstname',
				shipping_lastname = '$shipping_lastname',
				billing_firstname = '$billing_firstname',
				billing_lastname = '$billing_lastname'");

			if ($rs)
			{
				send_customer_password($RealName, $name, $email, $password);

				/*admin(
					gTxt('password_sent_to').sp.$email
				);*/

				customers_list('', '', gTxt('password_sent_to').sp.$email);
			}else{
				echo mysql_error();
			}
		}

		//admin("There was an error trying to add this customer");



	}
	function customer_delete($event, $step){

		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));

		$user_id = assert_int($user_id);

		safe_delete("txp_users", "user_id = $user_id");

		customers_list('', '', "customer deleted");

	}
	function customer_update($event, $step){

		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		extract(doSlash($_REQUEST));

		$RealName = $billing_firstname . " ". $billing_lastname;

		$user_id = assert_int($user_id);

		if(!isset($shipping_same_as_billing)){
			$shipping_same_as_billing = 0;
		}else{
			$shipping_same_as_billing = 1;
		}

		if(!function_exists("generate_password")){
			require_once txpath.'/include/txp_admin.php'; 
		}
		if(!function_exists("is_valid_email")){
			require_once txpath.'/lib/txplib_misc.php'; 
		}
		
		if ($name && is_valid_email($email))
		{
			
	    
			$password = doSlash(generate_password(6));
			$nonce = doSlash(md5(uniqid(rand(), true)));

			$rs = safe_update('txp_users', "
				privs		 = 0,
				name		 = '$name',
				email		 = '$email',
				RealName = '$RealName',
				billing_company = '$billing_company',
				billing_address1 = '$billing_address1',
				billing_address2 = '$billing_address2',
				billing_city = '$billing_city',
				billing_state = '$billing_state',
				billing_zip = '$billing_zip',
				billing_country = '$billing_country',
				billing_fax = '$billing_fax',
				billing_phone = '$billing_phone',
				shipping_same_as_billing = $shipping_same_as_billing,
				shipping_company = '$shipping_company',
				shipping_address1 = '$shipping_address1',
				shipping_address2 = '$shipping_address2',
				shipping_city = '$shipping_city',
				shipping_state = '$shipping_state',
				shipping_zip = '$shipping_zip',
				shipping_country = '$shipping_country',
				shipping_fax = '$shipping_fax',
				shipping_phone = '$shipping_phone',
				shipping_firstname = '$shipping_firstname',
				shipping_lastname = '$shipping_lastname',
				billing_firstname = '$billing_firstname',
				billing_lastname = '$billing_lastname'",
				"user_id = $user_id"
				);


			if ($rs)
			{
				customers_list('', '', "customer updated");
			}else{
				customers_list("There was an error trying to update customer");
			}
		}




	}

	function customer_edit($event, $step){
		global $statuses, $comments_disabled_after, $step, $txp_user;

		pagetop("Customer", $message);

		//CSS FOR CUSTOMER EDIT
		//==================================
		echo n.'<style type="text/css">'.
			  n.'.customerEdit legend{'.
			 n.'	font-size: 11px;'.
			 n.'	font-weight: bold;'.
			 n.'}'.

			 n.'.customerEdit label{'.
			 n.'	float: left;'.
			 n.'	width: 100px;'.
			 n.'	text-align: right;'.
			 n.'	padding: 5px;'.
			 n.'}'.

			 n.'.customerEdit br{'.
			 n.'	clear: both;'.
			 n.'}'.

			 n.'.customerEdit input{'.
			 n.'	margin: 5px 0 5px 0;'.
			 n.'	font-size: 11px;'.
			 n.'}'.


			 n.'</style>';

		echo n.'<script type="text/javascript">'.
			  n.'<!--'.

			 n.'function deleteCustomer(){'.
			 n.'	if(confirm("Are you sure?")){'.
			 n.'		document.getElementById("customerDelete").submit(); '.
			 n.'	}'.
			 n.'}'.
			 n.'//-->'.
			 n.'</script>';

		if(isset($_REQUEST['user_id'])){
			$user_id = $_REQUEST['user_id'];
			$customer = safe_row("*", "txp_users", "user_id = $user_id");
			extract($customer);
			$step = "update_customer";
		}else{
			$step = "save_customer";
		}

		//DELETE CUSTOMER FORM
		//====================================
		echo n.n.'<form name="product" method="post" action="index.php" enctype="multipart/form-data" id="customerDelete">';
			echo n."<input type='hidden' name='user_id' value='$user_id'/>".eInput('customers').sInput('delete_customer');
		echo n.n.'</form>';

		echo n.n.'<form name="product" method="post" action="index.php" enctype="multipart/form-data">';

		echo hInput('user_id', $user_id).
			eInput('customers').
			sInput($step).
			'<input type="hidden" name="view" />'.

			startTable('edit').

  		'<tr>'.n;

		//if ($view == 'text')
		//{

					//-- markup help --------------



		echo '<td id="article-main">'.n;
		echo "<fieldset class='customerEdit'>".
			  n."<legend>Customer Details</legend>".
			  n.'<label for="name">Username</label>'.
			  n.'<input id="name" name="name" value="'.$name.'"/>'.br.
			  n.'<label for="email">Email</label>'.
			  n.'<input id="email" name="email" value="'.$email.'"/>'.
			  n.'<label for="billing_firstname">First name</label>'.
			  n.'<input type="text" id="billing_firstname" name="billing_firstname" value="'.$billing_firstname.'"/>'.br.

			  n.'<label for="billing_lastname">Last name</label>'.
			  n.'<input type="text" id="billing_lastname" name="billing_lastname" value="'.$billing_lastname.'"/>'.br.
			  n.'</fieldset>';

		echo "<fieldset class='customerEdit'>".
			  n."<legend>Billing Information</legend>".

			  n.'<label for="billing_company">Company</label>'.
			  n.'<input type="text" id="billing_company" name="billing_company" value="'.$billing_company.'"/>'.br.

			  n.'<label for="billing_address1">Address 1</label>'.
			  n.'<input type="text" id="billing_address1" name="billing_address1" value="'.$billing_address1.'"/>'.br.

			  n.'<label for="billing_address2">Address 2</label>'.
			  n.'<input type="text" id="billing_address2" name="billing_address2" value="'.$billing_address2.'"/>'.br.

			  n.'<label for="billing_city">City</label>'.
			  n.'<input type="text" id="billing_city" name="billing_city" value="'.$billing_city.'"/>'.br.

			  n.'<label for="billing_state">State</label>'.
			  n.'<input type="text" id="billing_state" name="billing_state" value="'.$billing_state.'"/>'.br.

			  n.'<label for="billing_zip">Zip/Postal Code</label>'.
			  n.'<input type="text" id="billing_zip" name="billing_zip" value="'.$billing_zip.'"/>'.br.

			  n.'<label for="billing_country">Country</label>'.
			  n.'<input type="text" id="billing_country" name="billing_country" value="'.$billing_country.'"/>'.br.

			  n.'<label for="billing_fax">Fax Number</label>'.
			  n.'<input type="text" id="billing_fax" name="billing_fax" value="'.$billing_fax.'"/>'.br.


			  n.'<label for="billing_phone">Phone Number</label>'.
			  n.'<input type="text" id="billing_phone" name="billing_phone" value="'.$billing_phone.'"/>'.br.

			  n.'<label for="shipping_same">&nbsp;</label>';
			  if($shipping_same_as_billing == "1"){
			  	$checked = "checked='checked'";
			  }else{
			  	$checked ='';
			  }


			  echo n.'<input type="checkbox" id="shipping_same" name="shipping_same_as_billing" value="'.$shipping_same_as_billing.'" '.$checked.' onclick="toggleShipping()"/> Shipping info same as billing'.br.



			  n.'</fieldset>';

			  echo  '<script type="text/javascript">'.
			  		n.'<!--'.
			  		n.'	function toggleShipping(){'.
			  		n.' 	if(document.getElementById("shippingInfo").style.display == "none"){'.
			  		n.'				document.getElementById("shippingInfo").style.display = "block"; '.
			  		n.'		}else{'.
			  		n.'				document.getElementById("shippingInfo").style.display = "none"; '.
			  		n.'		}'.
			  		n.' }'.
			  		n.'//-->'.
			  		n.'</script>';

			if($shipping_same_as_billing == "1"){
				$display = "none";
			}else{
				$display = "block";
			}
			echo "<fieldset class='customerEdit' id='shippingInfo' style='display: $display'>".
			  n."<legend>Shipping Information</legend>".

			  n.'<label for="shipping_company">Company</label>'.
			  n.'<input type="text" id="shipping_company" name="shipping_company" value="'.$shipping_company.'"/>'.br.

			  n.'<label for="shipping_firstname">First name</label>'.
			  n.'<input type="text" id="shipping_firstname" name="shipping_firstname" value="'.$shipping_firstname.'"/>'.br.

			  n.'<label for="shipping_lastname">Last name</label>'.
			  n.'<input type="text" id="shipping_lastname" name="shipping_lastname" value="'.$shipping_lastname.'"/>'.br.


			  n.'<label for="shipping_address1">Address 1</label>'.
			  n.'<input type="text" id="shipping_address1" name="shipping_address1" value="'.$shipping_address1.'"/>'.br.

			  n.'<label for="shipping_address2">Address 2</label>'.
			  n.'<input type="text" id="shipping_address2" name="shipping_address2" value="'.$shipping_address2.'"/>'.br.

			  n.'<label for="shipping_city">City</label>'.
			  n.'<input type="text" id="shipping_city" name="shipping_city" value="'.$shipping_city.'"/>'.br.

			  n.'<label for="shipping_state">State</label>'.
			  n.'<input type="text" id="shipping_state" name="shipping_state" value="'.$shipping_state.'"/>'.br.

			  n.'<label for="shipping_zip">Zip/Postal Code</label>'.
			  n.'<input type="text" id="shipping_zip" name="shipping_zip" value="'.$shipping_zip.'"/>'.br.

			  n.'<label for="shipping_country">Country</label>'.
			  n.'<input type="text" id="shipping_country" name="shipping_country" value="'.$shipping_country.'"/>'.br.

			  n.'<label for="shipping_fax">Fax Number</label>'.
			  n.'<input type="text" id="shipping_fax" name="shipping_fax" value="'.$shipping_fax.'"/>'.br.


			  n.'<label for="shipping_phone">Phone Number</label>'.
			  n.'<input type="text" id="shipping_phone" name="shipping_phone" value="'.$shipping_phone.'"/>'.br.


			n.'</fieldset>';

		echo '</td>';
		echo '<td id="article-col-2" style="padding-top: 13px;">'; //start article-col-2
		echo '<a href="?event=customers&step=edit_customer" class="navlink">Add new customer</a>';
		if(isset($user_id)){
			echo n.br.br.'<a href="javascript:deleteCustomer()" style="color:#990000">Delete this customer</a>'.br.br;
		}else{
			echo br.br;
		}
		//-- publish button --------------
		echo
		(has_privs('article.publish')) ?
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4) :
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4);

		if($user_id){

			$orders = safe_rows("*", "orders", "user_id = $user_id ORDER BY last_updated DESC");
			if(count($orders) > 0){
				echo br.br."<fieldset>".
						n.'<legend>Order History</legend>'.
						n.'<ul class="plain-list">';

						foreach($orders as $order){
							echo n.'<li><a href="?event=orders&step=edit_order&id='.$order['id'].'">ORDER #'.$order['id'].'</a></li>';
						}
						n.'</ul>';
				echo '</fieldset>';
			}

		}
		echo '</td></tr></table></form>';

	} //--- end settings




	function customer_export($step='', $message=''){
		$customers = safe_rows("RealName,email,billing_company,billing_address1,billing_address2,billing_city,billing_state,billing_zip,billing_country,billing_fax,billing_phone,shipping_same_as_billing,shipping_company,shipping_address1,shipping_address2,shipping_city,shipping_state,shipping_zip,shipping_country,shipping_fax,shipping_phone,shipping_firstname,shipping_lastname,billing_firstname,billing_lastname", "txp_users", "privs = 0");

		header("Content-type: application/csv");
		header("Content-disposition:attachment;filename=customer_list_".date("Y-m-d").".csv");

		echo "RealName,email,billing_company,billing_address1,billing_address2,billing_city,billing_state,billing_zip,billing_country,billing_fax,billing_phone,shipping_same_as_billing,shipping_company,shipping_address1,shipping_address2,shipping_city,shipping_state,shipping_zip,shipping_country,shipping_fax,shipping_phone,shipping_firstname,shipping_lastname,billing_firstname,billing_lastname\n";
		foreach($customers as $customer){
			echo implode(",", $customer)."\n";
		}
		die();
	}

	function customers_list($event='', $step='', $message=''){

		global $statuses, $comments_disabled_after, $step, $txp_user;

		pagetop("Customers", $message);

		extract(get_prefs());

		extract(gpsa(array('page', 'sort', 'dir', 'crit', 'qty', 'search_method')));

		$sesutats = array_flip($statuses);

		$dir = ($dir == 'desc') ? 'desc' : 'asc';

		switch ($sort)
		{
			case 'RealName':
				$sort_sql = 'RealName '.$dir;
			break;

			case 'orders':
				$sort_sql = 'orders '.$dir;
			break;

			default:
				$dir = 'desc';
				$sort_sql = 'user_id '.$dir;
			break;
		}

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$criteria = "privs = 0";

		if ($search_method and $crit)
		{
			$crit_escaped = doSlash($crit);

			$critsql = array(
				'id'         => "ID = '$crit_escaped'",
				'title_body' => "Title rlike '$crit_escaped' or Body rlike '$crit_escaped'",
				'section'		 => "Section rlike '$crit_escaped'",
				'categories' => "Category1 rlike '$crit_escaped' or Category2 rlike '$crit_escaped'",
				'status'		 => "Status = '".(@$sesutats[gTxt($crit_escaped)])."'",
				'author'		 => "AuthorID rlike '$crit_escaped'",
			);

			if (array_key_exists($search_method, $critsql))
			{
				$criteria = $critsql[$search_method];
				$limit = 500;
			}

			else
			{
				$search_method = '';
				$crit = '';
			}
		}

		else
		{
			$search_method = '';
			$crit = '';
		}

		$total = safe_count('txp_users', "$criteria");

		if(isset($qty)){
			$customers_list_pageby = $qty;
		}else{
			$customers_list_pageby = 15;
		}

		$limit = max(@$customers_list_pageby, 15);

		list($page, $offset, $numPages) = pager($total, $limit, $page);

		$rs = safe_rows_start('*', 'txp_users',
			"$criteria order by $sort_sql limit $offset, $limit"
		);

		$customersOnPage = $offset+$limit;

		if ($rs)
		{

			echo n.n.'<form name="longform" method="post" action="index.php" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">'.

			n.startTable('list','','','','700').

				n.tr(
					n.tda("Displaying $offset - $customersOnPage of $total customers", ' colspan="2" style="border: none; padding-bottom: 15px;"').
					n.tda('<a href="?event=customers&step=edit_customer" class="navlink">Add a new customer</a> <a href="?event=customers&step=export_customer" class="navlink">Export customers</a>', ' colspan="2" style="text-align: right; border: none; padding-bottom: 15px;"')
				).

				n.tr(
					n.column_head('Customer', 'RealName', 'customers', true, $switch_dir, $crit, $search_method).
					column_head('Phone', 'shipping_phone', 'customers', true, $switch_dir, $crit, $search_method).
					column_head('Email', 'email', 'customers', true, $switch_dir, $crit, $search_method).
					column_head('Orders', 'order_num', 'customers', true, $switch_dir, $crit, $search_method)
				);

			include_once txpath.'/publish/taghandlers.php';

			while ($a = nextRow($rs))
			{
				extract($a);

				$order_num = safe_count("orders", "user_id = $user_id");

				$RealName = eLink('customers', 'edit_customer', 'user_id', $user_id, $RealName);

				$Orders = eLink('order', 'edit', 'customer', $name, $order_num);

				echo n.n.tr(

					n.td($RealName, "25%").

					td($billing_phone, "25%").

					td($email, "25%").

					td($Orders, "25%")
				);
			}

			echo n.endTable().
			n.'</form>'.

			n.nav_form('customers', $page, $numPages, $sort, $dir, $crit, $search_method).

			n.pageby_form('customers', $customers_list_pageby);
		}


	}//---- end product_list()

	function products_list($event='', $step='', $message=''){

		global $statuses, $comments_disabled_after, $step, $txp_user;
		$message = '';
		pagetop(gTxt('tab_list'), $message);

		echo poweredit_products(); //echo the poweredit js

		extract(get_prefs());

		extract(gpsa(array('page', 'sort', 'dir', 'crit', 'search_method')));

		$sesutats = array_flip($statuses);

		$dir = ($dir == 'desc') ? 'desc' : 'asc';

		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/prototype.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/scriptaculous.js"></script>';

		switch ($sort)
		{
			case 'id':
				$sort_sql = 'ID '.$dir;
			break;

			case 'posted':
				$sort_sql = 'Posted '.$dir;
			break;

			case 'title':
				$sort_sql = 'Title '.$dir.', Posted desc';
			break;

			case 'section':
				$sort_sql = 'Section '.$dir.', Posted desc';
			break;

			case 'category1':
				$sort_sql = 'Category1 '.$dir.', Posted desc';
			break;

			case 'category2':
				$sort_sql = 'Category2 '.$dir.', Posted desc';
			break;

			case 'status':
				$sort_sql = 'Status '.$dir.', Posted desc';
			break;

			case 'author':
				$sort_sql = 'AuthorID '.$dir.', Posted desc';
			break;

			case 'comments':
				$sort_sql = 'comments_count '.$dir.', Posted desc';
			break;

			default:
				$dir = 'desc';
				$sort_sql = 'Posted '.$dir;
			break;
		}

		$switch_dir = ($dir == 'desc') ? 'asc' : 'desc';

		$criteria = "section = 'store'";

		if ($search_method and $crit)
		{
			$crit_escaped = doSlash($crit);

			$critsql = array(
				'id'         => "ID = '$crit_escaped'",
				'title_body' => "Title rlike '$crit_escaped' or Body rlike '$crit_escaped'",
				'section'		 => "Section rlike '$crit_escaped'",
				'categories' => "Category1 rlike '$crit_escaped' or Category2 rlike '$crit_escaped'",
				'status'		 => "Status = '".(@$sesutats[gTxt($crit_escaped)])."'",
				'author'		 => "AuthorID rlike '$crit_escaped'",
			);

			if (array_key_exists($search_method, $critsql))
			{
				$criteria = $critsql[$search_method];
				$limit = 500;
			}

			else
			{
				$search_method = '';
				$crit = '';
			}
		}

		else
		{
			$search_method = '';
			$crit = '';
		}

		$total = safe_count('textpattern', "$criteria");

		if ($total < 1)
		{
			if ($criteria != 1)
			{
				echo n.list_search_form_products($crit, $search_method).
					n.graf("No products found", ' style="text-align: center;"');
			}

			else
			{
				echo graf("No products found", ' style="text-align: center;"');
			}

			return;
		}

		$limit = max(@$article_list_pageby, 15);

		list($page, $offset, $numPages) = pager($total, $limit, $page);
		
		
		echo n.list_search_form_products($crit, $search_method);

		$rs = safe_rows_start('*, unix_timestamp(Posted) as posted', 'textpattern',
			"$criteria order by $sort_sql limit $offset, $limit"
		);
		
		if ($rs)
		{
			$total_comments = array();

			// fetch true comment count, not the public comment count
			// maybe we should have another row in the db?
			$rs2 = safe_rows_start('parentid, count(*) as num', 'txp_discuss', "1 group by parentid order by parentid");

			if ($rs2)
			{
				while ($a = nextRow($rs2))
				{
					$pid = $a['parentid'];
					$num = $a['num'];

					$total_comments[$pid] = $num;
				}
			}

			echo n.n.'<form name="longform" method="post" action="index.php" onsubmit="return verify(\''.gTxt('are_you_sure').'\')">'.

				n.startTable('list','','','','700').
				n.tr(
					hCell().
					n.column_head('ID', 'id', 'products', true, $switch_dir, $crit, $search_method).
					column_head('title', 'title', 'products', true, $switch_dir, $crit, $search_method).
					column_head('category1', 'category1', 'products', true, $switch_dir, $crit, $search_method).
					column_head('category2', 'category2', 'products', true, $switch_dir, $crit, $search_method).
					column_head('status', 'status', 'products', true, $switch_dir, $crit, $search_method).
					hCell()
				);

			include_once txpath.'/publish/taghandlers.php';

			while ($a = nextRow($rs))
			{
				extract($a);

				if (empty($Title))
				{
					$Title = '<em>'.eLink('product', 'edit', 'ID', $ID, gTxt('untitled')).'</em>';
				}

				else
				{
					$Title = eLink('product', 'edit', 'ID', $ID, $Title);
				}
				if(!empty($Image)){
					$Image = "<img src='$Image' alt='Product Image' width='15' height='15'/>";
				}
				$Category1 = '<span title="'.htmlspecialchars(fetch_category_title($Category1)).'">'.$Category1.'&nbsp;</span>';
				$Category2 = '<span title="'.htmlspecialchars(fetch_category_title($Category2)).'">'.$Category2.'&nbsp;</span>';
				$manage = n.'<ul class="articles_detail">'.
						n.t.'<li>'.eLink('product', 'edit', 'ID', $ID, gTxt('edit')).'</li>'.
						( ($Status == 4 or $Status == 5) ? n.t.'<li><a href="'.permlinkurl($a).'">'.gTxt('view').'</a></li>' : '' ).
						n.'</ul>';

				$Status = !empty($Status) ? $statuses[$Status] : '';

				$comments = gTxt('none');

				if (isset($total_comments[$ID]) and $total_comments[$ID] > 0)
				{
					$comments = href(gTxt('manage'), 'index.php?event=discuss'.a.'step=list'.a.'search_method=parent'.a.'crit='.$ID).
						' ('.$total_comments[$ID].')';
				}

				$comment_status = ($Annotate) ? gTxt('on') : gTxt('off');

				if ($comments_disabled_after)
				{
					$lifespan = $comments_disabled_after * 86400;
					$time_since = time() - $posted;

					if ($time_since > $lifespan)
					{
						$comment_status = gTxt('expired');
					}
				}

				$comments = n.'<ul>'.
					n.t.'<li>'.$comment_status.'</li>'.
					n.t.'<li>'.$comments.'</li>'.
					n.'</ul>';

				echo n.n.tr(

					n.td($Image, 15).


					td(eLink('product', 'edit', 'ID', $ID, $ID).$manage).

					/*td(
						safe_strftime('%d %b %Y %I:%M %p', $posted)
					).*/

					td($Title).

					/*td(
						'<span title="'.htmlspecialchars(fetch_section_title($Section)).'">'.$Section.'&nbsp;</span>'
					, 75).*/

					td($Category1, 100).
					td($Category2, 100).
					td(($a['Status'] < 4 ? $Status : '<a href="'.permlinkurl($a).'">'.$Status.'</a>'), 50).

					/*td(
						'<span title="'.htmlspecialchars(get_author_name($AuthorID)).'">'.$AuthorID.'</span>'
					).*/

					td((
						(  ($a['Status'] >= 4 and has_privs('article.edit.published'))
						or ($a['Status'] >= 4 and $AuthorID == $txp_user
											     and has_privs('article.edit.own.published'))
						or ($a['Status'] < 4 and has_privs('article.edit'))
						or ($a['Status'] < 4 and $AuthorID == $txp_user and has_privs('article.edit.own'))
						)
						? fInput('checkbox', 'selected[]', $ID)
						: '&nbsp;'
					))
				);
			}

			echo n.n.tr(
				tda(
					toggle_box('articles_detail'),
					' colspan="2" style="text-align: left; border: none;"'
				).

				tda(
					select_buttons().
					product_multiedit_form($page, $sort, $dir, $crit, $search_method)
				,' colspan="5" style="text-align: right; border: none;"')
			).

			n.endTable().
			n.'</form>'.
			n.'<h4 style="font-weight:normal; text-align:center; width:100%;"><a href="#" class="navlink" onclick="if($(\'uploadCSV\').style.display == \'none\'){$(\'uploadCSV\').style.display = \'block\';}else{$(\'uploadCSV\').style.display = \'none\';}">Import Products</a>';
			//n.
			
			$instructions = tag(tag('<li>Using FTP, upload your product images to <pre>/txp_site_root/images/_import/</pre></li><li>Upload a correctly formatted CSV file using the form below. (CSV must be in UTF-8 character encoding with DOS or UNIX line breaks.)</li><li>Sit back and watch the magic</li>',"ol"), "div", ' id="instructions" style="display:none; width: 380px; text-align:left; margin:0 auto;"');
			
			echo tag('<h4 style="font-weight:normal; text-align:center; width:100%;"><small><a href="http://homeplatewp.com/TextCommerce/file_download/3">Download Example CSV</a> | <a href="javascript:void(0)" onclick="if($(\'instructions\').style.display == \'none\'){$(\'instructions\').style.display = \'block\';}else{$(\'instructions\').style.display = \'none\';}">Import Instructions</a></small></h4>'.$instructions.upload_form("Browse for CSV:", '', 'product_import', 'product'), 'div', ' id="uploadCSV" style="display:none;"');
			
			echo n.nav_form('list', $page, $numPages, $sort, $dir, $crit, $search_method).

			n.pageby_form('list', $article_list_pageby);
		}
		
		

	}//---- end product_list()
	
	function product_import(){
		global $txp_user, $textile;

		define("TEMP_IMPATH",'../images/_import/');

		if(isset($_FILES["thefile"])){
			$thefile = $_FILES["thefile"]["tmp_name"];
			move_uploaded_file($thefile, "../files/import.csv");
			chmod("../files/import.csv", 0666);
			$data = parse_csv("../files/import.csv", true);
			foreach($data as $row){
				$title = implode(",", $row);
				$title = explode(",", $title);
				$title = $title[0];
				extract($row);
				if($row['STATUS'] == 'Live'){
					$status = 4;
				}else if($row['STATUS'] == 'Hidden'){
					$status = 2;
				}else{
					$status = 3;
				}
				
				if(!empty($row['VENDOR'])){
					include_once txpath.'/lib/classTextile.php';
					$textile = new Textile();
					$vendor = dumbDown($textile->TextileThis(trim(doSlash($row['VENDOR'])),1));
					$vendor = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$row['VENDOR']));
				}
				if(!empty($CATEGORY_1)){
					$CATEGORY_1 = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$CATEGORY_1));
				}
				if(!empty($CATEGORY_2)){
					$CATEGORY_2 = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$CATEGORY_2));
				}
				safe_insert(
			   		"textpattern",
				   "Title           = '$title',
					Body            = '$DESCRIPTION',
					Status          =  $status,
					Posted          =  now(),
					LastMod         =  now(),
					AuthorID        = '$txp_user',
					Section         = 'store',
					Category1       = '$CATEGORY_1',
					Category2       = '$CATEGORY_2',
					custom_1        = '$PRICE',
					custom_2        = '$WEIGHT',
					custom_3        = '$SKU',
					custom_4        = '$ITEMS_IN_STOCK',
					custom_5        = '$vendor',
					uid				= '".md5(uniqid(rand(),true))."',
					feed_time		= now()"
				);

				
				//echo mysql_error();
				
				$ID = mysql_insert_id();
				
				//echo $ID; print_r($product); die();
				
				if(!empty($row['PRODUCT_IMAGE_1'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_1'];
					upload_image($img, '1', $ID);
				}
				if(!empty($row['PRODUCT_IMAGE_2'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_2'];
					upload_image($img, '2', $ID);
				}
				if(!empty($row['PRODUCT_IMAGE_3'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_3'];
					upload_image($img, '3', $ID);
				}
				if(!empty($row['PRODUCT_IMAGE_4'])){
					$img = TEMP_IMPATH.$row['PRODUCT_IMAGE_4'];
					upload_image($img, '4', $ID);
				}
				$customFields = '';
				if(!empty($row['CUSTOM_FIELD_LABEL_1'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_1'],
									'value' => $row['CUSTOM_FIELD_VALUE_1']);
					$customFields[] = $field;	
				}
				if(!empty($row['CUSTOM_FIELD_LABEL_2'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_2'],
									'value' => $row['CUSTOM_FIELD_VALUE_2']);
					$customFields[] = $field;	
				}
				if(!empty($row['CUSTOM_FIELD_LABEL_3'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_3'],
									'value' => $row['CUSTOM_FIELD_VALUE_3']);
					$customFields[] = $field;	
				}
				if(!empty($row['CUSTOM_FIELD_LABEL_4'])){
					$field = array(
									'label' => $row['CUSTOM_FIELD_LABEL_4'],
									'value' => $row['CUSTOM_FIELD_VALUE_4']);
					$customFields[] = $field;	
				}
				if(count($customFields) > 0){
					save_custom_fields($customFields, $ID);
				}
			}
			products_list('', '', "Products Imported");
		}else{
			products_list('','','Error: Couldn\'t Find Uploaded File!');
		}
		
	}
	function product_edit($event, $step, $message='') {

		global $vars, $txp_user, $comments_disabled_after, $txpcfg, $prefs;

		extract($prefs);

		define("IMPATH",$path_to_site.'/'.$img_dir.'/');


		extract(gpsa(array('view','from_view','step')));

		if(!empty($GLOBALS['ID'])) { // newly-saved article
			$ID = $GLOBALS['ID'];
			$step = 'edit';
		} else {
			$ID = gps('ID');
		}

		//GET ARTICLE DATA FOR 'EDIT' & 'SAVE' STEP
		//==================================
		if ($step == "edit" || $step=="save"
			&& !empty($ID)) {

			$pull = true;          //-- it's an existing article - off we go to the db
			$ID = assert_int($ID);

			$rs = safe_row(
				"*, unix_timestamp(Posted) as sPosted,
				unix_timestamp(LastMod) as sLastMod",
				"textpattern",
				"ID=$ID"
			);

			extract($rs);

			if ($AnnotateInvite!= $comments_default_invite) {
				$AnnotateInvite = $AnnotateInvite;
			} else {
				$AnnotateInvite = $comments_default_invite;
			}
			
			$custom_fields = safe_rows("*", "product_custom_fields", "articleID = $ID ORDER BY id DESC");
			
			$step = "save";
			//print_r($rs);

		}else if(empty($step)){
			$step = "create";
		}

		$textile = new Textile();

		$textile_body = $use_textile;
		$textile_excerpt = $use_textile;

		$page_title = "Add a new product";
		$message = "";

		pagetop($page_title, $message);
		
		if(count($custom_fields) > 0){
			$startFieldNum = ($custom_fields[0]['id']+1);
		}else{
			$startFieldNum = 0;
		}
		
		//JS INCLUDES
		//==================================
		//print_r($prefs);
		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/prototype.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/js/scriptaculous.js"></script>';

		//CSS FOR PRODUCT DISPLAY
		//==================================
		echo n.'<style type="text/css">'.
			 n.'h4.productHeading{'.
			 n.'	color:gray;'.
			 n.'	margin-bottom:0px;'.
			 n.'}'.

			 n.'h4.productHeading span{'.
			 n.'	float:right;'.
			 n.'	font-size:9px;'.
			 n.'	font-style:italic;'.
			 n.'	font-weight:normal;'.
			 n.'}'.

			 n.'.product-options-sub{'.
			 n.'	padding: 10px;'.
			 n.'}'.

			 n.'.product-options-sub em{'.
			 n.'	color:gray;'.
			 n.'}'.

			 n.'.product-options-sub legend{'.
			 n.'	font-size:11px;'.
			 n.'}'.
			 n.'.product-options-sub label{'.
			 n.'	font-size:11px;'.
			 n.'	padding-bottom: 5px;'.
			 n.'}'.

			 n.'#images ul.plain-list li{'.
			 n.'	float:left;'.
			 n.'	margin-right:10px;'.
			 n.'	margin-bottom:10px;'.
			 n.'	width:375px;'.
			 n.'}'.

			 n.'#images ul.plain-list li img{'.
			 n.'	border-width: 1px;'.
			 n.'	border-color:#BBBBBB;'.
			 n.'	border-style:solid;'.
			 n.'	padding:3px;'.
			 n.'	background-color: #E0E0E0;'.
			 n.'	float:left;'.
			 n.'}'.

			 n.'#images ul.plain-list li .imageEdit{'.
			 n.'	background-color:#E0E0E0;'.
			 n.'	height: 15px;'.
			 n.'	width: 250px;'.
			 n.'	text-align:left;'.
			 n.'	padding:3px 3px 3px 110px;'.
			 n.'	margin-top:0px;'.
			 n.'}'.

			 n.'#images ul.plain-list li .imageUpload{'.
			 n.'	margin-top: 5px;'.
			 n.'	padding:3px;'.
			 n.'	width: 275px;'.
			 n.'	border-width: 1px;'.
			 n.'	border-color:#E0E0E0;'.
			 n.'	border-style:solid;'.
			 n.'}'.

			 n.'</style>';
		echo n.'<script type="text/javascript">'.
			 n.' <!--'.
			 n.'	fieldNum = '.$startFieldNum.';'.
			 n.'	function addCustomField(index){'.
			 n.'		//$("custom_fields").innerHTML = $("custom_fields").innerHTML + getCustomField(fieldNum);'.
			 n.'		$("custom_fields").appendChild(getCustomField(fieldNum))'.
			 n.'		fieldNum = fieldNum+1;'.
			 n.'	}'.
			 
			 n.'	function deleteCustomField(index){'.
			 n.'		if(confirm("Are you sure?")){'.
			 n.'		$("custom_field_"+index).style.display = "none";'.
			 n.'		$("custom_fields["+index+"][value]").value = "delete";'.
			 n.'		$("custom_fields["+index+"][label]").value = "delete";'.
			 //n.'		$("custom_fields").appendChild(getCustomField(fieldNum))'.
			 //n.'		fieldNum = fieldNum+1;'.
			 n.'		}else{ return false; }'.
			 n.'	}'.
			 
			 n.'	function getCustomField(index){'.
			 n.'		var span = document.createElement("span");'.
			 n.'		span.setAttribute("id","custom_field_"+index);'.
			 n.'		var label = document.createElement("label");'.
			 n.'		label.setAttribute("for","custom_fields["+index+"][label]");'.
			 n.'		labelText = document.createTextNode("Label ");'.
			 n.'		var input = document.createElement("input");'.
			 n.'		input.setAttribute("id","custom_fields["+index+"][label]");'.
			 n.'		input.setAttribute("name","custom_fields["+index+"][label]");'.
			 
			 n.'		var labelValue = document.createElement("label");'.
			 n.'		labelValue.setAttribute("for","custom_fields["+index+"][value]");'.
			 n.'		labelTextValue = document.createTextNode(" Value ");'.
			 n.'		var inputValue = document.createElement("input");'.
			 n.'		inputValue.setAttribute("id","custom_fields["+index+"][value]");'.
			 n.'		inputValue.setAttribute("name","custom_fields["+index+"][value]");'.
			 
			 n.'		var deleteLink = document.createElement("a");'.
			 n.'		deleteLink.setAttribute("href","javascript:void(0)");'.
			 n.'		deleteLink.setAttribute("onclick","deleteCustomField("+index+");");'.
			 n.'		deleteLink.setAttribute("style","font-size: 11px;");'.
			 n.'		deleteText = document.createTextNode("Delete");'.

			 n.'		label.appendChild(labelText);'.
			 n.'		span.appendChild(label);'.
			 n.'		span.appendChild(input);'.
			 n.'		labelValue.appendChild(labelTextValue);'.
			 n.'		span.appendChild(labelValue);'.
			 n.'		span.appendChild(inputValue);'.
			  n.'		deleteLink.appendChild(deleteText);'.
			 n.'		span.appendChild(deleteLink);'.
			 n.'		span.appendChild(document.createElement("br"));'.
			 n.'		span.appendChild(document.createElement("br"));'.
			 
			 /*
			 n.'		var html = \'<span id="custom_field_\'+index+\'"><label for="custom_fields[\'+index+\'][label]">Label</label> <input name="custom_fields[\'+index+\'][label]" id="custom_fields[\'+index+\'][label]"/> &nbsp;<label for="custom_fields[\'+index+\'][value]">Value</label> <input name="custom_fields[\'+index+\'][value]" id="custom_fields[\'+index+\'][value]"/> <small><a href="javascript:void(0)" onclick="$(\\\'custom_field_\'+index+\'\\\').innerHTML = \\\'\\\';">Delete</a></small><br/><br/></span>\';'.
			 n.'		return html;'.
			 n.'		var html = \'<span id="custom_field_\'+index+\'"><label for="custom_fields[\'+index+\'][label]">Label</label> <input name="custom_fields[\'+index+\'][label]" id="custom_fields[\'+index+\'][label]"/> &nbsp;<label for="custom_fields[\'+index+\'][value]">Value</label> <input name="custom_fields[\'+index+\'][value]" id="custom_fields[\'+index+\'][value]"/> <small><a href="javascript:void(0)" onclick="$(\\\'custom_field_\'+index+\'\\\').innerHTML = \\\'\\\';">Delete</a></small><br/><br/></span>\';'.
			 n.'		return html;'.

			 n.'		var html = \'<span id="custom_field_\'+index+\'"><label for="custom_fields[\'+index+\'][label]">Label</label> <input name="custom_fields[\'+index+\'][label]" id="custom_fields[\'+index+\'][label]"/> &nbsp;<label for="custom_fields[\'+index+\'][value]">Value</label> <input name="custom_fields[\'+index+\'][value]" id="custom_fields[\'+index+\'][value]"/> <small><a href="javascript:void(0)" onclick="$(\\\'custom_field_\'+index+\'\\\').innerHTML = \\\'\\\';">Delete</a></small><br/><br/></span>\';'.
			 */
			 
			 n.'		return span;'.
			 n.'	}'.
			 
			 n.'//-->'.
			 n.'</script>';
		echo n.n.'<form name="product" method="post" action="index.php" enctype="multipart/form-data">';


		echo '<input type="hidden" name="textile_body" value="1"/>';
		echo '<input type="hidden" name="textile_excerpt" value="1"/>';
		echo '<input type="hidden" name="Section" value="store"/>';

		echo hInput('ID', $ID).
			eInput('product').
			sInput($step).
			'<input type="hidden" name="view" />'.

			startTable('edit').

  		'<tr>'.n;

		//if ($view == 'text')
		//{

					//-- markup help --------------



		echo '<td id="article-main">';
		echo "<h4 class='productHeading'>Product name</h4>";

		echo '<p><input type="text" id="title" name="Title" value="'.cleanfInput($Title).'" class="edit" size="40" tabindex="1" />';

		if ( ($Status == 4 or $Status == 5) and $step != 'create')
		{
			include_once txpath.'/publish/taghandlers.php';

			echo sp.sp.'<a href="'.permlinkurl_id($ID).'">'.gTxt('view').'</a>';
		}

		echo '</p>';
		echo "<h4 class='productHeading'><span>Textile &amp; HTML allowed</span> Describe the product</h4>";
		echo n.graf('<textarea id="body" name="Body" cols="55" rows="31" tabindex="2" style="height: 180px;">'.htmlspecialchars($Body).'</textarea>');

		//PRODUCT OPTIONS
		//================================

		//echo '<div id="productOptions" style="border-width:1px; border-color:#E1E1E1; border-style:solid; padding:5px; background-color:#F3F4F4;">';

			//PRODUCT & PRICE
			//================================

			echo n.n.'<fieldset class="product-options-sub" style="background-color:white">'.
			'<div style="float:left; margin-right: 40px;">'.
			n.graf('<label for="price">Price</label> '.br.

				n.'<input style="width: 50px;" type="text" name="custom_1" id="price" value="'.cleanfInput($custom_1).'"/> <em>usd</em>').n.
			'</div>'.
			n.graf('<label for="weight">Weight</label>'.br.
				n.'<input type="text" style="width: 50px;" name="custom_2" id="weight" value="'.cleanfInput($custom_2).'"/> <em>lbs</em>');
			echo n."</fieldset>";
			
			//CUSTOM FIELDS
			//================================
			
			if(isset($ID)){
				$custom_fields = get_custom_fields($ID);
			}else{
				$custom_fields = '';
			}
			
			echo n.n.'<fieldset class="product-options-sub" style="background-color:white" id="custom_fields">'.
			n.'<legend>Custom Fields <span>[<a href="javascript:addCustomField(0);">Add a new custom field</a>]</span></legend>';
			echo $custom_fields;
			echo n."</fieldset>";
				  
			//CATEGORIES
			//================================
			//if(!is_callable("rss_admin_catlist")){
				echo n.n.'<fieldset class="product-options-sub" style="background-color:white">'.
				n.'<legend>Categorize <small>[<a href="?event=category">edit</a>]</small></legend>'.
				'<div style="float:left; margin-right: 10px;">'.
				n.graf('<label for="category-1">'.gTxt('category1').'</label> '.
	
					n.'<select name="Category1" id="category-1"><option></option>'.n.product_cateogry_option_list($Category1).n.'</select>').n.
				'</div>'.
				n.graf('<label for="category-2">'.gTxt('category2').'</label>'.
					n.'<select name="Category2" id="category-2"><option></option>'.n.product_cateogry_option_list($Category2).n.'</select>');
				echo n."</fieldset>";
			//}
			//VENDORS
			//================================
			echo n.n.'<fieldset class="product-options-sub" style="background-color:white">'.
			n.'<legend>Vendor <em>The creator or manufacturer of the product. </em></legend>'.
			'<div style="float:left; width:50%;">'.
			n.'<label for="vendor">Select existing vendor </label> '.br.

				n.build_list("vendor", "txp_category", "name", "title", $custom_5, "parent='Vendors'", true).n.
			'</div>'.
				n.graf('<label for="new_vendor_name">Or create a new vendor</label>'.br.
				n.'<input id="new_vendor_name" type="text" name="new_vendor_name"/>');
			echo n."</fieldset>";

			//INVENTORY
			//================================
			echo n.'<fieldset class="product-options-sub" style="background-color:white">'.
			n.'<legend>Inventory</legend>';
			echo n.'<label for="sku">SKU <em>Stock keeping unit</em></label>'.n.br.
				   '<input type="text" name="custom_3" id="sku" value="'.$custom_3.'"/>'.br.br;
			if(isset($custom_4) && !empty($custom_4)){
				$showLevel = true;
				$levelSelected = "selected = 'true'";
				$levelStyle = 'display:block;';
			}else{
				$showLevel = false;
				$levelSelected = "";
				$levelStyle = 'display:none;';
			}
			
			echo n.'<select name="trackOptions" id="trackOptions" onchange="if(this.value == \'doTrack\'){$(\'stockLevel\').style.display = \'block\';}else{$(\'stockLevel\').style.display = \'none\';}">'.n.
				 n.'<option value="dontTrack">Don\'t track stock level</option>'.
				 n.'<option value="doTrack" '.$levelSelected.'>Keep track of stock level</option>'.
				 //n.'<option value="virtual">Product is virtual</option>'.
				 n.'</select>'.br.br;
			echo n.'<div id="stockLevel" style="'.$levelStyle.'">'.
				 n.'<label for="items_in_stock">Number of items in stock:</label>'.
				 n.'<input type="text" name="custom_4" id="items_in_stock" style="width: 20px;" value="'.cleanfInput($custom_4).'"/>'.
				 n.'</div><!--/stockLevel-->';
			
			echo n."</fieldset>";

			//IMAGES
			//================================

			echo '

					<script type="text/javascript">
					<!--
						function deleteImage(id){
							if(confirm("Are you sure?")){
								if(id == "1"){
									$("image1").style.display = "none";
									$("imageField1").value = "delete";
								}else if(id == "2"){
									$("image2").style.display = "none";
									$("imageField2").value = "delete";
								}else if(id == "3"){
									$("image3").style.display = "none";
									$("imageField3").value = "delete";
								}else if(id == "4"){
									$("image4").style.display = "none";
									$("imageField4").value = "delete";
								}
								document.forms["product"].submit();
							}
						}
						function updateImage(id){
							if(id == "1"){

								new Effect.SlideDown($("imageUpload1"));
								$("updateImage1").value = $("imageField1").value;
								$("imageField1").value = "delete";

							}else if(id == "2"){

								new Effect.SlideDown($("imageUpload2"));
								$("updateImage2").value = $("imageField2").value;
								$("imageField2").value = "delete";

							}else if(id == "3"){

								new Effect.SlideDown($("imageUpload3"));
								$("updateImage3").value = $("imageField3").value;
								$("imageField3").value = "delete";
							}else if(id == "4"){

								new Effect.SlideDown($("imageUpload4"));
								$("updateImage4").value = $("imageField4").value;
								$("imageField4").value = "delete";
							}
						}
						function cancelUpload(id){
							if(id == "1"){
								new Effect.SlideUp($("imageUpload1"));
								$("imageField1").value = $("updateImage1").value;

							}else if(id == "2"){
								new Effect.SlideUp($("imageUpload2"));
								$("imageField2").value = $("updateImage2").value;

							}else if(id == "3"){
								new Effect.SlideUp($("imageUpload3"));
								$("imageField3").value = $("updateImage3").value;

							}else if(id == "4"){
								new Effect.SlideUp($("imageUpload4"));
								$("imageField4").value = $("updateImage4").value;

							}
						}
					-->
					</script>

				';


			if($step == "create"){
				echo n.'<fieldset class="product-options-sub" style="background-color:white">'.
				n.'<legend>Product images</legend>';
				echo n.'<em>Allowed file types are JPG, GIF &amp; PNG</em>';
				echo n.'<input type="file" name="uploadFile"/>'.n;
				echo n."</fieldset>";
			}else if($step == "edit" || $step=="save" || empty($step)){
				echo n.'<fieldset class="product-options-sub" id="images" style="background-color:white">'.
				n.'<legend>Product images</legend>';
				echo n.'<ul class="plain-list" id="image_list">';
				
				if(isset($Image) && !empty($Image)){
					echo n.'<li id="image1">
							<img src="'.product_image_display($Image, "small").'" alt="Product Image"/> <div class="imageEdit" style="display:block;">
							<a href="javascript:deleteImage(\'1\')">Delete Image</a> | <a href="javascript:updateImage(\'1\')">Update Image</a>

							<div class="imageUpload" id="imageUpload1" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile1"/> and <input type="submit" value="Save"/>&nbsp; <a href="javascript:cancelUpload(\'1\')">Cancel</a>
								<input type="hidden" name="updateImage1" id="updateImage1" value="0"/>
							</div>
							<input id="imageField1" type="hidden" name="Image" value="'.$Image.'"/>

							</div>

							</li>';
				}
				if(isset($custom_6) && !empty($custom_6)){
					echo n.'<li id="image2"><img src="'.product_image_display($custom_6, "small").'" alt="Product Image"/>
						<div class="imageEdit" id="image1Control" style="display:block;">
							<a href="javascript:deleteImage(\'2\')">Delete Image</a> | <a href="javascript:updateImage(\'2\')">Update Image</a>

							<div class="imageUpload" id="imageUpload2" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile2"/> and <input type="submit" value="Save"/> &nbsp; <a href="javascript:cancelUpload(\'2\')">Cancel</a>
								<input type="hidden" name="updateImage2" id="updateImage2" value="0"/>
							</div>

						</div><input id="imageField2" type="hidden" name="custom_6" value="'.$custom_6.'"/></li>';
				}
				if(isset($custom_7) && !empty($custom_7)){
					echo n.'<li id="image3"><img src="'.product_image_display($custom_7, "small").'" alt="Product Image"/>
						<div class="imageEdit" id="image1Control" style="display:block;">
							<a href="javascript:deleteImage(\'3\')">Delete Image</a> | <a href="javascript:updateImage(\'3\')">Update Image</a>

							<div class="imageUpload" id="imageUpload3" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile3"/> and <input type="submit" value="Save"/> &nbsp; <a href="javascript:cancelUpload(\'3\')">Cancel</a>
								<input type="hidden" name="updateImage3" id="updateImage3" value="0"/>
							</div>

							</div>
							<input id="imageField3" type="hidden" name="custom_7" value="'.$custom_7.'"/></li>';
				}
				if(isset($custom_8) && !empty($custom_8)){
					echo n.'<li id="image4"><img src="'.product_image_display($custom_8, "small").'" alt="Product Image"/>
						<div class="imageEdit" id="image1Control" style="display:block;">
						<a href="javascript:deleteImage(\'4\')">Delete Image</a> | <a href="javascript:updateImage(\'4\')">Update Image</a>

							<div class="imageUpload" id="imageUpload4" style="display:none;">
								Browse for new image: <input type="file" name="uploadFile4"/> and <input type="submit" value="Save"/> &nbsp; <a href="javascript:cancelUpload(\'4\')">Cancel</a>
								<input type="hidden" name="updateImage4" id="updateImage4" value="0"/>
							</div>

						</div><input id="imageField4" type="hidden" name="custom_8" value="'.$custom_8.'"/></li>';
				}
				echo n."</ul><br style='clear:both;'/>";
				echo n.'<a href="javascript:void(0);" onclick="document.getElementById(\'otherImageUpload\').style.display = \'block\'">Add an image</a>'.n;
				echo n.'<div id="otherImageUpload" style="display:none; margin-top: 10px;">';
				echo n.'<em>Allowed file types are JPG, GIF &amp; PNG</em>';
				echo n.'<input type="file" name="uploadFile"/> and <input type="submit" value="upload"/>'.n;
				echo n.'</div>';
				echo n."</fieldset>";
				echo n.'<div id="data"></div>';
			}

		//echo '</div>'; // end productOptions

		// end left content area

		echo hInput('from_view',$view),
	'</td>';

	echo '<td id="article-col-2" style="padding-top: 75px;">'; //start article-col-2

		//PRODUCT STATUS
		//================================

		echo n.n.'<fieldset id="write-status">'.
				n.'<legend>'.gTxt('status').'</legend>'.
				n.status_radio_product($Status).
				n.'</fieldset>';





		//-- comments stuff --------------

		if($step=="create") {
			//Avoiding invite disappear when previewing
			$AnnotateInvite = (!empty($store_out['AnnotateInvite']))? $store_out['AnnotateInvite'] : $comments_default_invite;
			if ($comments_on_default==1) { $Annotate = 1; }
		}

		if ($use_comments == 1)
		{
			echo n.n.'<fieldset id="write-comments">'.
				n.'<legend>Allow product reviews</legend>';

			$comments_expired = false;

			if ($step != 'create' && $comments_disabled_after)
			{
				$lifespan = $comments_disabled_after * 86400;
				$time_since = time() - $sPosted;

				if ($time_since > $lifespan)
				{
					$comments_expired = true;
				}
			}

			if ($comments_expired)
			{
				echo n.n.graf(gTxt('expired'));
			}

			else
			{
				echo n.n.graf(
					onoffRadio('Annotate', $Annotate)
				).

				n.n.graf(
					'<label for="comment-invite">'.gTxt('comment_invitation').'</label>'.br.
					fInput('text', 'AnnotateInvite', $AnnotateInvite, 'edit', '', '', '', '', 'comment-invite')
				);
			}

			echo n.n.'</fieldset>';
		}
		/*if(is_callable("rss_admin_catlist")){
			echo "<fieldset id='write-sort'></fieldset>";
			echo rss_admin_catlist();
		}*/
		//wilshireone multipule categories
		
		
		
		//-- publish button --------------
		echo
		(has_privs('article.publish')) ?
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4) :
		fInput('submit','publish',gTxt('save'),"publish", '', '', '', 4);

	echo '</td> <!--/article-col-2-->'; //end article-col-2
	echo '</td></tr></table></form>';

	}

	function product_image_display($imageURL, $size){
		$str = str_replace("meduim", $size, $imageURL);
		return $str;
	}



	function product_save()
	{
		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);
		
		$incoming = psa($vars);
		define("IMPATH",$path_to_site.'/'.$img_dir.'/');

		$oldArticle = safe_row('Status, url_title, Title, Image, custom_6, custom_7, custom_8','textpattern','ID = '.(int)$incoming['ID']);

		if (! (    ($oldArticle['Status'] >= 4 and has_privs('article.edit.published'))
				or ($oldArticle['Status'] >= 4 and $incoming['AuthorID']==$txp_user and has_privs('article.edit.own.published'))
		    	or ($oldArticle['Status'] < 4 and has_privs('article.edit'))
				or ($oldArticle['Status'] < 4 and $incoming['AuthorID']==$txp_user and has_privs('article.edit.own'))))
		{
				// Not allowed, you silly rabbit, you shouldn't even be here.
				// Show default editing screen.
			product_edit();
			return;
		}
		$wrapper = new TXP_Wrapper();
		
		$incoming = $wrapper->textile_main_fields($incoming, $use_textile);

		extract(doSlash($incoming));
		extract(array_map('assert_int', psa(array('ID', 'Status', 'textile_body', 'textile_excerpt'))));
		$Annotate = ( ps( 'Annotate')) ? assert_int( ps( 'Annotate')) : 0;

		if (!has_privs('article.publish') && $Status>=4) $Status = 3;

		if($reset_time) {
			$whenposted = "Posted=now()";
		} else {
			$when = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second)-tz_offset();
			$when = "from_unixtime($when)";
			$whenposted = "Posted=$when";
		}
		
		if (isset($new_vendor_name) && !empty($new_vendor_name)){
			$custom_5 = $new_vendor_name;
			cat_vendor_category_create($custom_5);
		}else if (isset($vendor)){
			$custom_5 = $vendor;
		}
		
		//Auto-Update custom-titles according to Title, as long as unpublished and NOT customized
		if ( empty($url_title)
			  || ( ($oldArticle['Status'] < 4)
					&& ($oldArticle['url_title'] == $url_title )
					&& ($oldArticle['url_title'] == stripSpace($oldArticle['Title'],1))
					&& ($oldArticle['Title'] != $Title)
				 )
		   )
		{
			$url_title = stripSpace($Title_plain, 1);
		}
		if (!$Annotate) $Annotate = 0;

		//IMAGE DELETE/UPDATE FUNCTIONALITY
		//=======================================
		if(strtolower($Image) == "delete"){

			$delPath = str_replace("http://".$siteurl."/images/", IMPATH, $oldArticle['Image']);
			unlink($delPath);
			unlink(product_image_display($delPath, "small"));
			unlink(product_image_display(str_replace("gif", "jpg", $delPath), "large"));
			$Image = "";
		}
		if(strtolower($custom_6) == "delete"){

			$delPath = str_replace("http://".$siteurl."/images/", IMPATH, $oldArticle['custom_6']);
			unlink($delPath);
			unlink(product_image_display($delPath, "small"));
			unlink(product_image_display(str_replace("gif", "jpg", $delPath), "large"));

			$custom_6 = "";
		}
		if(strtolower($custom_7) == "delete"){

			$delPath = str_replace("http://".$siteurl."/images/", IMPATH, $oldArticle['custom_7']);
			unlink($delPath);
			unlink(product_image_display($delPath, "small"));
			unlink(product_image_display(str_replace("gif", "jpg", $delPath), "large"));
			$custom_7 = "";
		}
		if(strtolower($custom_8) == "delete"){

			$delPath = str_replace("http://".$siteurl."/images/", IMPATH, $oldArticle['custom_8']);
			unlink($delPath) or die("can't delete file: ".$delPath);
			unlink(product_image_display($delPath, "small"));
			unlink(product_image_display(str_replace("gif", "jpg", $delPath), "large"));

			$custom_8 = "";
		}
		
		//UPDATE ARTICLE
		//=======================================

		safe_update("textpattern",
		   "Title           = '$Title',
			Body            = '$Body',
			Body_html       = '$Body_html',
			Excerpt         = '$Excerpt',
			Excerpt_html    = '$Excerpt_html',
			Keywords        = '$Keywords',
			Image           = '$Image',
			Status          =  $Status,
			LastMod         =  now(),
			LastModID       = '$txp_user',
			Section         = '$Section',
			Category1       = '$Category1',
			Category2       = '$Category2',
			Annotate        =  $Annotate,
			textile_body    =  $textile_body,
			textile_excerpt =  $textile_excerpt,
			override_form   = '$override_form',
			url_title       = '$url_title',
			AnnotateInvite  = '$AnnotateInvite',
			custom_1        = '$custom_1',
			custom_2        = '$custom_2',
			custom_3        = '$custom_3',
			custom_4        = '$custom_4',
			custom_5        = '$custom_5',
			custom_6        = '$custom_6',
			custom_7        = '$custom_7',
			custom_8        = '$custom_8',
			custom_9        = '$custom_9',
			custom_10       = '$custom_10',
			$whenposted",
			"ID = $ID"
		);

		save_custom_fields($_REQUEST['custom_fields'], $ID);


		//IMAGE UPLOAD
		//=======================
		
		if($_FILES['uploadFile']['error'] == "0"){
			$file = $_FILES['uploadFile'];
		}else if($_FILES['uploadFile1']['error'] == "0"){
			$file = $_FILES['uploadFile1'];
		}else if($_FILES['uploadFile2']['error'] == "0"){
			$file = $_FILES['uploadFile2'];
		}else if($_FILES['uploadFile3']['error'] == "0"){
			$file = $_FILES['uploadFile3'];
		}else if($_FILES['uploadFile4']['error'] == "0"){
			$file = $_FILES['uploadFile4'];
		}
		
		if ($file["type"] == "image/gif" || $file["type"] == "image/jpeg" || $file["type"] == "image/png"){
			// prepare the image for insertion

			//we need to check what images have been uploaded already
			$article_updated = safe_row("Image, custom_6, custom_7, custom_8", "textpattern", "ID = $ID");

			extract($article_updated);

			if(empty($Image)){
				$image_num = "";

			}else if(empty($custom_6)){
				$image_num = "2";

			}else if(empty($custom_7)){
				$image_num = "3";

			}else if(empty($custom_8)){
				$image_num = "4";
			}
			echo "Uploading image: ".$file['name'];
			
			$img = $file['tmp_name'];
			upload_image($img, $image_num, $ID);
			//echo "here"; die();
		}
		//END IMAGE UPLOAD
		//=======================


		if($Status >= 4) {
			if ($oldArticle['Status'] < 4) {
				if(!function_exists("do_pings")){
					require_once(txpath.'/include/txp_article.php');
					do_pings();
				}
			}
			update_lastmod();
		}
		
		product_edit("","", "Product Saved");
		

	}

	function product_post()
	{
		global $txp_user, $vars, $txpcfg, $prefs;

		extract($prefs);

		define("IMPATH",$path_to_site.'/'.$img_dir.'/');

		$incoming = psa($vars);
		$import = false;
		
		$message='';
		
		$wrapper = new TXP_Wrapper();
		$incoming = $wrapper->textile_main_fields($incoming, $use_textile);

		extract(doSlash($incoming));
		
		extract(array_map('assert_int', psa(array( 'Status', 'textile_body', 'textile_excerpt'))));
		$Annotate = ( ps( 'Annotate')) ? assert_int( ps( 'Annotate')) : 0;
		
		if($import){
			$Status = $product['Status'];
		}
		
		$when = 'now()';
		
		if ($Title or $Body or $Excerpt) {

			if (!has_privs('article.publish') && $Status>=4) $Status = 3;
			if (empty($url_title)) $url_title = stripSpace($Title_plain, 1);
			if (!$Annotate) $Annotate = 0;

			
			if (isset($new_vendor_name) && !empty($new_vendor_name)){
				$custom_5 = $new_vendor_name;
				cat_vendor_category_create($custom_5);
			}else if (isset($vendor)){
				$custom_5 = $vendor;
			}

			safe_insert(
			   "textpattern",
			   "Title           = '$Title',
				Body            = '$Body',
				Body_html       = '$Body_html',
				Excerpt         = '$Excerpt',
				Excerpt_html    = '$Excerpt_html',
				Image           = '$Image',
				Keywords        = '$Keywords',
				Status          =  $Status,
				Posted          =  $when,
				LastMod         =  now(),
				AuthorID        = '$txp_user',
				Section         = '$Section',
				Category1       = '$Category1',
				Category2       = '$Category2',
				textile_body    =  $textile_body,
				textile_excerpt =  $textile_excerpt,
				Annotate        =  $Annotate,
				override_form   = '$override_form',
				url_title       = '$url_title',
				AnnotateInvite  = '$AnnotateInvite',
				custom_1        = '$custom_1',
				custom_2        = '$custom_2',
				custom_3        = '$custom_3',
				custom_4        = '$custom_4',
				custom_5        = '$custom_5',
				custom_6        = '$custom_6',
				custom_7        = '$custom_7',
				custom_8        = '$custom_8',
				custom_9        = '$custom_9',
				custom_10       = '$custom_10',
				uid				= '".md5(uniqid(rand(),true))."',
				feed_time		= now()"
			);

			$GLOBALS['ID'] = mysql_insert_id();
			$ID = $GLOBALS['ID'];
			//print_r($_FILES);
			
			//CUSTOM FIELDS
			
			save_custom_fields($_REQUEST['custom_fields'], $ID);
			
			//IMAGE UPLOAD
			//=======================
			if ($_FILES["uploadFile"]["type"] == "image/gif" || $_FILES["uploadFile"]["type"] == "image/jpeg" || $_FILES["uploadFile"]["type"] == "image/png"){
			// prepare the image for insertion
				$img = $_FILES['uploadFile']['tmp_name'];

				upload_image($img, 1, $ID);

			}
			//END IMAGE UPLOAD
			//=======================

			if ($Status>=4) {

				if(!function_exists("do_pings")){
					require_once(txpath.'/include/txp_article.php');
					do_pings();
				}

				update_lastmod();
			}
			product_edit(
				"","",
				"Product Saved"
			);
		} else product_edit();
	}

	function product_cateogry_option_list($Category){
		//$rows = product_cateogry_list();
		$options = "";
		$rows = getTree('Products','article');
		foreach($rows as $cat){
			if($Category == $cat['name']){
				$selected = "selected='true'";
			}else{
				$selected = "";
			}
			$options .= "<option value='".$cat['name']."' $selected>".str_repeat("&#160;",($cat['level']*2)).$cat['title']."</option>".n;
		}
		return $options;
	}

	function vendor_option_list(){
		$rows = vendor_list();
		$options = "";
		foreach($rows as $vendor){
			$options .= "<option value='".$vendor['name']."'>".$vendor['title']."</option>".n;
		}
		return $options;
	}
	function vendor_list(){
		$rows = safe_rows("*", "txp_category", "parent = 'Vendors'");
		return $rows;
	}

	function product_cateogry_list(){
		$rows = safe_rows("*", "txp_category", "type = 'Article' and name != 'root' parent='Product'");
		return $rows;
	}
	function poweredit_products(){

		return "	<script type=\"text/javascript\">
		<!--



			function poweredit(elm)
			{
				var something = elm.options[elm.selectedIndex].value;

				// Add another chunk of HTML
				var pjs = document.getElementById('js');

				if (pjs == null)
				{
					var br = document.createElement('br');
					elm.parentNode.appendChild(br);

					pjs = document.createElement('P');
					pjs.setAttribute('id','js');
					elm.parentNode.appendChild(pjs);
				}

				if (pjs.style.display == 'none' || pjs.style.display == '')
				{
					pjs.style.display = 'block';
				}

				if (something != '')
				{
					switch (something)
					{
						case 'changestatus':
							var statuses = '<select name=\"Status\" class=\"list\">	<option value=\"\" selected=\"selected\"></option>	<option value=\"1\">Draft</option>	<option value=\"2\">Hidden</option>	<option value=\"3\">Pending</option>	<option value=\"4\">Live</option>	<option value=\"5\">Sticky</option></select>';
							pjs.innerHTML = '<span>Status: '+statuses+'</span>';
						break;

						case 'changecomments':
							var comments = '<input type=\"radio\" id=\"Annotate-0\" name=\"Annotate\" value=\"0\" class=\"radio\" checked=\"checked\" /><label for=\"Annotate-0\">Off</label> <input type=\"radio\" id=\"Annotate-1\" name=\"Annotate\" value=\"1\" class=\"radio\" /><label for=\"Annotate-1\">On</label> ';
							pjs.innerHTML = '<span>Comments: '+comments+'</span>';
						break;

						case 'changeauthor':
							var authors = '<select name=\"AuthorID\" class=\"list\">	<option value=\"\" selected=\"selected\"></option>	<option value=\"a_band\">a_band</option></select>';
							pjs.innerHTML = '<span>Author: '+authors+'</span>';
						break;

						default:
							pjs.style.display = 'none';
						break;
					}
				}

				return false;
			}

			addEvent(window, 'load', cleanSelects);
		-->
		</script>";

	}

	function product_multiedit_form($page, $sort, $dir, $crit, $search_method)
	{
		$methods = array(
			'delete'          => gTxt('delete')
		);

		return event_multiedit_form('product', $methods, $page, $sort, $dir, $crit, $search_method);
	}
	
	
	function product_multi_edit() 
	{
		
		global $txp_user;

		$selected = ps('selected');

		if (!$selected)
		{
			return products_list();
		}

		$method = ps('edit_method');
		$changed = false;
		$ids = array();

		if ($method == 'delete')
		{
			if (!has_privs('article.delete'))
			{
				$allowed = array();

				if (has_privs('article.delete.own'))
				{
					foreach ($selected as $id)
					{
						$id = assert_int($id);
						$author = safe_field('AuthorID', 'textpattern', "ID = $id");

						if ($author == $txp_user)
						{
							$allowed[] = $id;
						}
					}
				}

				$selected = $allowed;
			}

			foreach ($selected as $id)
			{
				$id = assert_int($id);

				if (safe_delete('textpattern', "ID = $id"))
				{
					$ids[] = $id;
				}
			}

			$changed = join(', ', $ids);
		}

		

		if ($changed)
		{
			return products_list(
				messenger('Product', $changed, (($method == 'delete') ? 'deleted' : 'modified' ))
			);
		}

		return products_list();
	}
	
	function list_search_form_products($crit, $method)
	{
		$methods =	array(
			'title_body' => gTxt('title_body'),
			'categories' => gTxt('categories'),
			'status'		 => gTxt('status'),
			'id'				 => gTxt('ID'),
		);

		return search_form('products', 'list', $crit, $methods, $method, 'title_body');
	}
	
	
	
	/**
* generates an HTML select element for the month
*
* @param string $name The name of the HTML field
* @param int $this_month=-1 The month number to have selected. If not
supplied, current month is used. If 0, no month is selected.
* @return string
*/
function get_html_select_month($name='',$this_month=-1){
if(empty($name)) $name='month';
if($this_month==-1) $this_month=date('n');
$months=range(1,12);
$str='<select name="'.$name.'" id="'.$name.'">'."\n";
foreach($months as $month){
$str.=' <option value="'.$month.'"';
if($month==$this_month) $str.=' selected="selected"';
$str.='>'.date('F',mktime(0,0,0,$month,1,2006)).'</option>'."\n";
}
$str.='</select>'."\n";
return $str;
}


function listbox_year ($name, $start, $end, $default=0) {
    $result="<select name='".$name."' id='".$name."' size='1'>".n;
    for ($y=$start;$y<=$end;$y++) {
        if ($default  == $y) {$selected="selected='selected'";} else {$selected="";}
        $result.="<option value='".$y."' $selected>$y</option>".n;
    }
    $result.="</select>".n;
	return $result;
}

function country_list ($name, $selected) {

  	$countries = safe_rows("*", "countries", "name != ''");
	if(empty($selected)){
		$selected = "USA";
	}
  	foreach($countries as $country){
  		extract($country);
  		if($country_code == $selected){
  			$selectOption = "selected='true'";
  		}else{
  			$selectOption = "";
  		}
  		$return .= "<option value='$country_code' $selectOption>$name</option>".n;
  	}
  	return $return;

}

function currency_list ($name, $selected) {

  	$currencies = safe_rows("*", "currencies", "currency_name != ''");

  	$return = "<select name='$name' id='$name'>";
  	foreach($currencies as $currency){
  		extract($currency);
  		if($currency_code == $selected){
  			$selectOption = "selected='true'";
  		}
  		$return .= "<option value='$currency_code' $selectOption>$currency_name ($currency_code)</option>".n;
  	}
  	$return .= "</select>";
  	return $return;

}

function build_list ($name, $table, $valueCol, $displayCol, $selected='',$where='1', $leaveBlankOption = false, $order_by = ''){

	  	$returnData  = "<select name='$name' id='$name'>";
	  	if($leaveBlankOption){
	  		$returnData .= "<option></option>";
	  	}
		$returnData .= build_options($table, $valueCol, $displayCol, $selected, $where, $order_by);
		$returnData .= "</select>";
		return $returnData;
}
function build_options ($table, $valueCol, $displayCol, $selected='', $where='1' , $order_by = ''){
	  	$returnData = '';
	  	$datas = safe_rows("*", $table, $where." ".$order_by);
	  	foreach($datas as $data){
			if($data[$valueCol] == $selected){
				$selectOption = "selected='true'";
			}else{
				$selectOption = "";
			}
			$returnData .= "<option value='".$data[$valueCol]."' $selectOption>".$data[$displayCol]."</option>".n;
  		}
		return $returnData;
}

function send_customer_password($RealName, $name, $email, $password)
{
	global $sitename;

	$message = gTxt('greeting').' '.$RealName.','.

		"\r\n"."\r\n".gTxt('you_have_been_registered').' '.$sitename.

		"\r\n"."\r\n".gTxt('your_login_is').': '.$name.
		"\r\n".gTxt('your_password_is').': '.$password ."\r\n";

		//"\r\n"."\r\n".gTxt('log_in_at').': '.hu.'textpattern/index.php';

	return txpMail($email, "[$sitename] ".gTxt('your_login_info'), $message);
}

function addCountryForm(){
	  $return = n.'<div id="addCountry" style="display:none;">'.
	  n.'<label for="country_name">Country Name</label>'.
	  n.'<input type="text" class="text" id="country_name" name="country_name"/>'.br.
	  n.'<label for="country_code">Country Code</label>'.
	  n.'<input type="text" class="text" id="country_code" name="country_code" style="width:20px;" max="2"/>'.br.
	  n.'<p class="desc">(2 digits. e.g. us, ru...)</p>'.
	  n.'</div><!--/addCountry-->';

	  return $return;
}
function get_custom_fields($articleID){
	$returnHTML = '';
	$fields = safe_rows("*", "product_custom_fields", "article_id = $articleID");
	foreach($fields as $field){
		extract($field);
		$returnHTML .= '<span id="custom_field_'.$id.'">
		<input type="hidden" name="custom_fields['.$id.'][fieldID]" value="'.$id.'"/>
		<label for="custom_fields['.$id.'][label]">Label </label>
		<input id="custom_fields['.$id.'][label]" name="custom_fields['.$id.'][label]" value="'.$field_label.'"/>
		<label for="custom_fields['.$id.'][value]"> Value </label>
		<input id="custom_fields['.$id.'][value]" name="custom_fields['.$id.'][value]" value="'.$field_value.'"/>
		<a href="javascript:void(0)" onclick="deleteCustomField('.$id.');" style="font-size: 11px;">Delete</a>
		<br/><br/></span>';
	}
	return $returnHTML;
}
function save_custom_fields($fieldArray, $articleID){
	
	
	foreach($fieldArray as $field){
		extract($field);
		if(!isset($field['fieldID']) && !empty($field['label'])){
			$rs = safe_insert("product_custom_fields",
							  "article_id = $articleID,
							   field_label = '$label',
							   field_value = '$value'");
			if(!$rs){
				echo mysql_error();
				die();
			}
		}else if(isset($field['fieldID']) && $field['label'] == "delete" && $field['value'] == "delete"){
			
			$rs = safe_delete("product_custom_fields",
							  "id = $fieldID");
			if(!$rs){
				echo mysql_error();
				die();
			}
		
		}else if(isset($field['fieldID'])){
			$rs = safe_update("product_custom_fields",
							  "field_label = '$label',
							   field_value = '$value'",
							   "id = $fieldID");
			if(!$rs){
				echo mysql_error();
				die();
			}
		}
	}
}
function status_radio_product($Status)
	{
		global $statuses;

		$Status = (!$Status) ? 4 : $Status;

		foreach ($statuses as $a => $b)
		{
			$out[] = n.t.'<li>'.radio('Status', $a, ($Status == $a) ? 1 : 0, 'status-'.$a).
				'<label for="status-'.$a.'">'.$b.'</label></li>';
		}

		return '<ul class="plain-list">'.join('', $out).n.'</ul>';
}

function cat_vendor_category_create($name)
	{
		global $txpcfg;
		//Prevent non url chars on category names
		include_once txpath.'/lib/classTextile.php';
		$textile = new Textile();
		
		//$name = ps('name');		
		$title = doSlash($name);				
		$name = dumbDown($textile->TextileThis(trim(doSlash($name)),1));
		$name = preg_replace("/[^[:alnum:]\-_]/", "", str_replace(" ","-",$name));

		$check = safe_field("name", "txp_category", "name='$name' and type='article'");

		if (!$check) {
			if($name) {				
				$q = 
				safe_insert("txp_category", "name='$name', title='$title', type='article', parent='Vendors'");
				
				//rebuild_tree('Vendor', 2, "article");
				rebuild_tree('root', 1, "article");
			} 
		}
	}


function parse_csv($file, $columnheadings = false, $delimiter = ',', $enclosure = null) {
	
	
	
	$row = 1;
	$rows = array();
	$handle = fopen($file, 'r');
	ini_set('auto_detect_line_endings','On'); // Will fix detection of line endings automatically

	while (($data = fgetcsv($handle, 20000, $delimiter, $enclosure )) !== FALSE) {
		if ($columnheadings == true && $row == 1) {
			$headingTexts = $data;
		}
		elseif ($columnheadings == true) {
			foreach ($data as $key => $value) {
				unset($data[$key]);
				$data[$headingTexts[$key]] = $value;
		}
			$rows[] = $data;
		}
		else {
			$rows[] = $data;
		}
		$row++;
	}
	fclose($handle);
	return $rows;
}

function upload_image($image_tmp_path, $image_num, $ID){
	global $txp_user, $vars, $txpcfg, $prefs;
	extract($prefs);
	
	define("IMPATH",$path_to_site.'/'.$img_dir.'/');
	
	$img = $image_tmp_path;
	
	
	
	$constrain = true;
	$w = 100;
	$h = 100;

	// get image size of img
	$x = @getimagesize($img);
	// image width
	$sw = $x[0];
	// image height
	$sh = $x[1];

	if ($percent > 0) {
		// calculate resized height and width if percent is defined
		$percent = $percent * 0.01;
		$w = $sw * $percent;
		$h = $sh * $percent;
	} else {
		if (isset ($w) AND !isset ($h)) {
			// autocompute height if only width is set
			$h = (100 / ($sw / $w)) * .01;
			$h = @round ($sh * $h);
		} elseif (isset ($h) AND !isset ($w)) {
			// autocompute width if only height is set
			$w = (100 / ($sh / $h)) * .01;
			$w = @round ($sw * $w);
		} elseif (isset ($h) AND isset ($w) AND isset ($constrain)) {
			// get the smaller resulting image dimension if both height
			// and width are set and $constrain is also set
			$hx = (100 / ($sw / $w)) * .01;
			$hx = @round ($sh * $hx);

			$wx = (100 / ($sh / $h)) * .01;
			$wx = @round ($sw * $wx);

			if ($hx < $h) {
				$h = (100 / ($sw / $w)) * .01;
				$h = @round ($sh * $h);
			} else {
				$w = (100 / ($sh / $h)) * .01;
				$w = @round ($sw * $w);
			}
		}
	}

	$im = @ImageCreateFromJPEG ($img) or // Read JPEG Image
	$im = @ImageCreateFromPNG ($img) or // or PNG Image
	$im = @ImageCreateFromGIF ($img) or // or GIF Image
	$im = false; // If image is not JPEG, PNG, or GIF

	if ($im) {

		// Create the resized image destination
		$thumb = @ImageCreateTrueColor ($w, $h);
		// Copy from image source, resize it, and paste to image destination
		@ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
		// Output resized image
		imagegif ($thumb, IMPATH."product-".$ID."-small".$image_num.".gif");
		chmod(IMPATH."product-".$ID."-small".$image_num.".gif", 0777);
	}
	// now we need to make the medium thumbnail
	$w = 300;
	$h = 200;
// get image size of img
	$x = @getimagesize($img);
	// image width
	$sw = $x[0];
	// image height
	$sh = $x[1];

	if ($percent > 0) {
		// calculate resized height and width if percent is defined
		$percent = $percent * 0.01;
		$w = $sw * $percent;
		$h = $sh * $percent;
	} else {
		if (isset ($w) AND !isset ($h)) {
			// autocompute height if only width is set
			$h = (100 / ($sw / $w)) * .01;
			$h = @round ($sh * $h);
		} elseif (isset ($h) AND !isset ($w)) {
			// autocompute width if only height is set
			$w = (100 / ($sh / $h)) * .01;
			$w = @round ($sw * $w);
		} elseif (isset ($h) AND isset ($w) AND isset ($constrain)) {
			// get the smaller resulting image dimension if both height
			// and width are set and $constrain is also set
			$hx = (100 / ($sw / $w)) * .01;
			$hx = @round ($sh * $hx);

			$wx = (100 / ($sh / $h)) * .01;
			$wx = @round ($sw * $wx);

			if ($hx < $h) {
				$h = (100 / ($sw / $w)) * .01;
				$h = @round ($sh * $h);
			} else {
				$w = (100 / ($sh / $h)) * .01;
				$w = @round ($sw * $w);
			}
		}
	}

	$im = @ImageCreateFromJPEG ($img) or // Read JPEG Image
	$im = @ImageCreateFromPNG ($img) or // or PNG Image
	$im = @ImageCreateFromGIF ($img) or // or GIF Image
	$im = false; // If image is not JPEG, PNG, or GIF

	if ($im) {

		// Create the resized image destination
		$thumb = @ImageCreateTrueColor ($w, $h);
		// Copy from image source, resize it, and paste to image destination
		@ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
		// Output resized image
		$meduimImage =IMPATH."product-".$ID."-meduim".$image_num.".gif";
		imagegif ($thumb, $meduimImage);
		chmod($meduimImage, 0777);

	}
	
	imagejpeg ($im, IMPATH."product-".$ID."-large".$image_num.".jpg");
	chmod(IMPATH."product-".$ID."-large".$image_num.".jpg", 0777);
	
	if($image_num == "" || $image_num == 1){
		safe_update("textpattern", "Image = 'http://$siteurl/images/product-".$ID."-meduim".$image_num.".gif'", "ID=$ID");

	}else if($image_num == "2"){
		safe_update("textpattern", "custom_6 = 'http://$siteurl/images/product-".$ID."-meduim".$image_num.".gif'", "ID=$ID");

	}else if($image_num == "3"){
		safe_update("textpattern", "custom_7 = 'http://$siteurl/images/product-".$ID."-meduim".$image_num.".gif'", "ID=$ID");

	}else if($image_num == "4"){
		safe_update("textpattern", "custom_8 = 'http://$siteurl/images/product-".$ID."-meduim".$image_num.".gif'", "ID=$ID");
	}
	
}

# --- END PLUGIN CODE ---

?>
