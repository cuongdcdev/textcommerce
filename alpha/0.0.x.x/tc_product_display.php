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
$plugin['description'] = 'A collection of tags for displaying product information.';
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

function tc_price($atts) {
   global $thisarticle;
	
   $article_data = articleData();
   
   extract(lAtts(array(
      'currency'  => '',
   ),$atts));
   
   $result = safe_row("store_currency", "store_settings", "1");
   
   if(!empty($currency)){
   		$currency = safe_row("*", "currencies", "currency_code = '$currency'");
   }else if(count($result) == 0){
   		$currency = safe_row("*", "currencies", "currency_code = 'USD'");
   }else{
   		$currency = safe_row("*", "currencies", "currency_code = '".$result['store_currency']."'");
   }
   
   if(!empty($currency['currency_symbol'])){
   		$return .= $currency['currency_symbol'] . number_format(intval($article_data['custom_1']), 2);
   }else{
   		$return .= number_format(intval($article_data['custom_1']), 2). $currency['currency_code'];
   }
   
   // The returned value will replace the tag on the page
   return $return;
}

function tc_weight($atts) {
   global $thisarticle;
	
   $article_data = articleData();
   
   extract(lAtts(array(
      'units'  => '',
   ),$atts));
   
   if(empty($units)){
   		$result = safe_row("unit_system", "store_settings", "1");
   		if(count($result) > 0){
   			$units = $result['unit_system'];
   		}else{
   			$units = 'imperial';
   		}
   }
   $weight = intval($article_data['custom_2']);
   if($units == 'imperial'){
   		$unit = 'lb';
   		if($weight > 1){
   			$unit .= 's';
   		}
   }else{
   		$unit = 'kg';
   		if($weight > 1){
   			$unit .= 's';
   		}
   }
   
   // The returned value will replace the tag on the page
   return $weight." ".$unit;
}

function tc_sku() {
   global $thisarticle;
	
   $article_data = articleData();
   
   // The returned value will replace the tag on the page
   return $article_data['custom_3'];
}

function tc_stock() {
   global $thisarticle;
	
   $article_data = articleData();
   
   // The returned value will replace the tag on the page
   return $article_data['custom_4'];
}

function tc_vendor() {
   global $thisarticle;
	
   $article_data = articleData();
   $vendorname = $article_data['custom_5'];
   $vendor_data = safe_row("*", "txp_category", "name = '$vendorname'");
   
   // The returned value will replace the tag on the page
   return $vendor_data['title'];
}



function tc_product_image_1($atts) {
   global $thisarticle;
	
   $article_data = articleData();
   
   extract(lAtts(array(
      'size'  => 'meduim',
      'class' => '',
      'alt'   => $thisarticle['title'],
   ),$atts));
   
   $image = getImage($thisarticle['article_image'], $atts);   

   // The returned value will replace the tag on the page
   return $image;
}

function tc_product_image_2($atts) {
   global $thisarticle;
	
   $article_data = articleData();
   
   extract(lAtts(array(
      'size'  => 'meduim',
      'class' => '',
      'alt'   => $thisarticle['title'],
   ),$atts));
   
   $image = getImage($article_data['custom_6'], $atts);   

   // The returned value will replace the tag on the page
   return $image;
}

function tc_product_image_3($atts) {
   global $thisarticle;
	
   $article_data = articleData();
   
   extract(lAtts(array(
      'size'  => 'meduim',
      'class' => '',
      'alt'   => $thisarticle['title'],
   ),$atts));
   
   $image = getImage($article_data['custom_7'], $atts);   

   // The returned value will replace the tag on the page
   return $image;
}

function tc_product_image_4($atts) {
   global $thisarticle;
	
   $article_data = articleData();
   
   extract(lAtts(array(
      'size'  => 'meduim',
      'class' => '',
      'alt'   => $thisarticle['title'],
   ),$atts));
   
   $image = getImage($article_data['custom_8'], $atts);   

   // The returned value will replace the tag on the page
   return $image;
}


function getImage($url, $atts){
	
	extract(lAtts(array(
      'size'  => 'meduim',
      'class' => '',
      'alt'   => $thisarticle['title'],
   ),$atts));  
   if(!empty($url)){
   
	   $image = str_replace("meduim", $size, $url);
	   
	   $final_image = "<img src='$image' alt='$alt' class='$class'/>";
	   
	   return $final_image;
   }else{
   		return '';
   }
}

function articleData(){
	global $thisarticle;
	return safe_row("*", "textpattern", "ID = ".$thisarticle['thisid']);
}

// ----------------------------------------------------
// Example admin side plugin


# --- END PLUGIN CODE ---

?>
