<?php
$plugin['version'] = ' Alpha 0.2';
$plugin['author'] = 'Levi Nunnink, JR Chew';
$plugin['author_uri'] = 'http://homeplatewp.com/TextCommerce/';
$plugin['description'] = 'Required core settings library.';
$plugin['type'] = 1; // 0 for regular plugin; 1 if it includes admin-side code

@include_once('zem_tpl.php');

if (0) {
?>
# --- BEGIN PLUGIN HELP ---

For display information visit: 
http://homeplatewp.com/TextCommerce/article/67/tc_product_display-001

# --- END PLUGIN HELP ---
<?php
}


// TextCommerce Version alpha 0.0.6.8
// Culturezoo LLC - www.culturezoo.com

function settings_edit($event, $step, $message='', $show_panel=''){
		global $txp_user, $vars, $txpcfg, $prefs, $general_settings;

		extract($prefs);

		pagetop("Store Settings", $message);

		//JS INCLUDES
		//==================================
		//print_r($prefs);

		echo '<script type="text/javascript" src="http://'.$siteurl.'/jquery.js"></script>';
		echo '<script type="text/javascript" src="http://'.$siteurl.'/jquery.js"></script>';

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

	doJS();

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

	        echo '<h4 class="zoneName">'.$name.' <small>[<a href="#" onclick="$(\'#addNewShippingRate_'.$id.'\').show();">Add new shipping rate</a>]</small></h4>';
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
									 n.'<td valign="center" style="width:100px;padding:0;"><input type="text" value="'.number_format($rate['rate'], 2).'" name="rate" style="width:25px;"/> '.$general_settings['store_currency'].'</td>'.
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
									 n.'<td valign="center"><input type="text" value="" name="rate" style="width:25px;"/> '.$general_settings['store_currency'].'</td>'.
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

	




?>
