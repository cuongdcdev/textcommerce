<?php

// This is a PLUGIN TEMPLATE.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Uncomment and edit this line to override:
# $plugin['name'] = 'abc_plugin';

$plugin['version'] = '0.0.1';
$plugin['author'] = 'Levi Nunnink';
$plugin['author_uri'] = 'http://homeplatewp.com/TextCommerce/';
$plugin['description'] = 'A collection of shopping cart tags.';
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



function tc_shopping_cart_add($atts) {
   session_start(); 
   extract(lAtts(array(
      'redirect_to_previous'  => false
   ),$atts));
   
   if(empty($_SESSION['cart'])){
   		$cart = new bckCart();
   }else{
   		$cart = $_SESSION['cart'];
   }
   $id = intval($_REQUEST['product_id']);
   $qty = intval($_REQUEST['qty']);
   if($qty > 0 and $id > 0){
   		$cart->add_item($id, $qty);
   		$_SESSION['cart']=$cart;
   }
   if($redirect_to_previous == "previous"){
   		$return = '<script type="text/javascript">
   				    	<!--
   				    		document.history.back();
   				    	//-->
   				    </script>';
   		return $return;
   }
   
}

function tc_shopping_cart_detail($atts) {
	session_start(); 
	
	$cart = $_SESSION['cart'];
	if(isset($_REQUEST['empty_cart'])){
		$cart->empty_cart();
		$_SESSION['cart']=$cart;
	}
	//print_r($cart);
	$cartHTML = startTable("shopping_cart").
				tr(
				n.hCell("Item").
				n.hCell("Quantity").
				n.hCell("Price").
				n.hCell("Subtotal"));
	foreach($cart->items as $product){
		$image = '<img src="'.product_image_display($product['image'], "small").'" alt="Product Detail" class="thumb"/>';
		$cartHTML .= tr(
						n.td($image.$product['name']).
						n.td($product['qty']).
						n.td(tc_price_format($product['price'])).
						n.td(tc_price_format($product['price']))
					);
	}
	$cartHTML .= endTable();
	$cartHTML .= tag(
				 	tag(tag("Empty Cart", "a", " href='?empty_cart=true' class='cartAction'"), "li").
				 	tag(tag("Continue Shopping", "a", " href='#' class='cartAction'"), "li").
				 	tag(tag("Update Cart", "a", " href='#' class='cartAction'"), "li").
				 	tag(tag(tc_price_format($cart->total), "span", " class='cartTotal'"), "li"), 
				 	"ul");
	return $cartHTML;
}

function tc_price_format($price) {
	
   $article_data = _articleData($ID);
   
   
   $result = safe_row("store_currency", "store_settings", "1");
   
  if(count($result) == 0){
   		$currency = safe_row("*", "currencies", "currency_code = 'USD'");
   }else{
   		$currency = safe_row("*", "currencies", "currency_code = '".$result['store_currency']."'");
   }
   
   $return = $currency['currency_symbol'].number_format(intval($price), 2);
   
   
   // The returned value will replace the tag on the page
   return $return;
}


class bckCart {

	var $total = 0;
	var $itemscount = 0;
	var $items = array();
	var $error = false;
	
	function get_contents() 
	{
		return $this->items;
	}


	function add_item($itemref, $qty = "1") 
	{
// the item is already in the cart..so we'll just increase the quantity
		if($this->items[$itemref]['qty'] > 0) {
			$this->items[$itemref]['qty'] += $qty;
			$this->_update_total();
// create the item otherwise
		} else {
			// initialize the item
			$this->items[$itemref] = array();
			// get item details from the db
			$item_details = $this->get_item_details($itemref);
			// add the details we've retrieved from the db
			if($item_details['stock'] < $qty){
				$this->error = "There are not enough items in stock. Please reduce your order quantity to:".$item_details['stock'];
			}
			foreach( $item_details as $key => $value )
			{
				$this->items[$itemref][$key] = $value;
			}
			// add quantity value
			$this->items[$itemref]['qty'] = $qty;
		}
		$this->_update_total();
	} 

	function del_item($itemref) 
	{
		foreach($this->items as $key => $item) {
			if($key == $itemref) {
				unset($this->items[$key]);
			}
		}
		$this->_update_total();
	}

	function update_cart($itemref, $qty) 
	{
		if($qty < 1) {
			$this->del_item($itemref);
		} else {
			$this->items[$itemref]['qty'] = $qty;
		}
		$this->_update_total();
	}


	function empty_cart() 
	{
		$this->total = 0;
		$this->itemscount = 0;
		$this->items = array();
	}


	function _update_total() 
	{
		$this->total = 0;
		$this->itemscount = 0;
		if(sizeof($this->items > 0)) {
			foreach($this->items as $item) {
				$this->total += ($item['price'] * $item['qty']);
				$this->itemscount += $item['qty'];
			}
		}
	}
	
	
	function get_item_details($itemref)
	{
		$query = '	SELECT 
						custom_4 AS stock,
						custom_3 AS sku,
						custom_1 AS price,
						Title AS name,
						Image AS image
						FROM textpattern WHERE 
						ID='.$itemref.'';
		$result = getRow($query);
		
		return $result;
	}

}

// ----------------------------------------------------
// Example admin side plugin


# --- END PLUGIN CODE ---

?>
