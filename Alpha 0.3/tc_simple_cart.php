<?php
$plugin['version'] = '0.1';
$plugin['author'] = 'culturezoo';
$plugin['author_uri'] = 'http://textcommerce.org';
$plugin['description'] = 'A simple session-based shopping cart frontend that integrates with TextCommerce.';
$plugin['type'] = 0; // 0 for regular plugin; 1 if it includes admin-side code


if (0) {
?>
# --- BEGIN PLUGIN HELP ---

For display information visit: 
http://homeplatewp.com/TextCommerce/article/78/tc_simple_cart-01-release

# --- END PLUGIN HELP ---
<?php
}

@include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---

// ----------------------------------------------------
// Example public side tags

function tc_shopping_cart_add($atts) {
  session_start(); 
  global $thisarticle;
  
  extract(lAtts(array(
      'redirect_section'  => false,
      'class' => 'tc_cart',
      'add_message' => 'Add to Cart'
   ),$atts));

  extract(doSlash($_POST));
  
  if(empty($_SESSION['cart'])){
  	$cart = new bckCart();
  }else{
    $cart = $_SESSION['cart'];
  }
  
  if(intval($qty) > 0 and intval($product_id) != 0 and $product_id == $thisarticle['thisid']){
    $cart->add_item($product_id, $qty);
  	$_SESSION['cart'] = $cart;
  }
  if(intval($qty) != 0 && $redirect_section){
		header("Location: /$redirect_section/");
  }
  
  $form = str_replace(
   	'action="index.php"', "",
   	form(
   		hInput("product_id",$GLOBALS['thisarticle']['thisid']).
   		hInput("qty",1).
   		fInput("submit", "submit",$add_message)
   	)
   );
   return $form;
}

function tc_shopping_cart_detail($atts) {
	session_start(); 
	
	 extract(lAtts(array(
      'checkout_section'  => 'checkout'    
      ),$atts));

	
	$cart = $_SESSION['cart'];
	if(empty($cart)){
		$cart = new bckCart();
	}
	if(isset($_REQUEST['empty_cart'])){
		$cart->empty_cart();
		$_SESSION['cart']=$cart;
	}
	$cartHTML = startTable("shopping_cart").
				tr(
				n.hCell("Item").
				n.hCell("Quantity").
				n.hCell("Price").
				n.hCell("Subtotal"));
	foreach($cart->items as $product){
		$image = '<img src="'.product_image_display($product['image'], "small").'" alt="Product Detail" class="thumb"/>';
		$link = "<a href='".permlinkurl_id($product['ID'])."' title='View product detail'>".$product['name']."</a>";
		$cartHTML .= tr(
						n.td($image.$link).
						n.td($product['qty']).
						n.td(_tc_price_format($product['price'])).
						n.td(_tc_price_format($product['price']))
					);
	}
	$cartHTML .= endTable();
	$cartHTML .= tag(
					tag(tag("Empty Cart", "a", " href='?empty_cart=true' class='cartAction' id='emptyCart'"), "li").
				 	tag(tag("Continue Shopping", "a", " href='#' class='cartAction' id='continueShopping'"), "li").
				 	tag(tag("Checkout", "a", " href='/$checkout_section/' class='cartAction' id='checkout'"), "li").
				 	tag(tag(_tc_price_format($cart->total), "span", " class='cartTotal'"), "li"), 
				 	"ul"); 	
				 	
	return $cartHTML;
}

function _tc_price_format($price) {

  $result = safe_row("store_currency", "store_settings", "1");

  if(count($result) == 0){
  $currency = safe_row("*", "currencies", "currency_code = 'USD'");
  }else{
   		$currency = safe_row("*", "currencies", "currency_code = '".$result['store_currency']."'");
  }
  $return = $currency['currency_symbol'].number_format(intval($price), 2);
   
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


	function add_item($itemref, $qty = 1) 
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
		
      if(is_numeric($item_details['stock'])){
  			if($item_details['stock'] < $qty){
    				$this->error = "There are not enough items in stock. Please reduce your order quantity to:".$item_details['stock'];
        }
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
		$query = 'SELECT 
						custom_4 AS stock,
						custom_3 AS sku,
						custom_1 AS price,
						Title AS name,
						Image AS image,
						url_title AS url,
						ID,
						Section AS section
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
