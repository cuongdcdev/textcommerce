<?php
$plugin['version'] = ' Alpha 0.3';
$plugin['author'] = 'culturezoo';
$plugin['author_uri'] = 'http://textcommerce.org';
$plugin['description'] = 'Required core library.';
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

        doJS();

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

			 doJS();

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


	}//---- end customers_list()
	
	

	
	
	
	
	
	
	
?>
