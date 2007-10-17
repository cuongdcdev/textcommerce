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

		doJS();

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
	













?>
