<?php
$plugin['version'] = ' Alpha 0.2';
$plugin['author'] = 'Levi Nunnink, JR Chew';
$plugin['author_uri'] = 'http://homeplatewp.com/TextCommerce/';
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


// TextCommerce Version alpha 0.0.6.8
// Culturezoo LLC - www.culturezoo.com


// uses "function ln_txp_commerce_install" to check for existing database tables 
// and make sure everything is set up properly
ln_txp_commerce_install();  


error_reporting(E_ERROR);

global $vars, $statuses, $general_settings, $currency;

$vars = array(
	'ID','Title','Title_html','Body','Body_html','Excerpt','textile_excerpt','Image',
	'textile_body', 'Keywords','Status','Posted','Section','Category1','Category2',
	'Annotate','AnnotateInvite','publish_now','reset_time','AuthorID','sPosted',
	'LastModID','sLastMod','override_form','from_view','year','month','day','hour',
	'minute','second','url_title','custom_1','custom_2','custom_3','custom_4','custom_5',
	'custom_6','custom_7','custom_8','custom_9','custom_10', 'new_category_name', 'category',
	'new_vendor_name', 'vendor'
);

// we want to use this across multiple functions
$general_settings = safe_row("*", "store_settings", "1");
// we are using this in a few places...
$currency = safe_row("currency_code as code,currency_symbol as symbol", "currencies", "currency_code='{$general_settings['store_currency']}'");
$currency = ($currency['symbol']) ? $currency['symbol'] : $currency['code'];




// tabs function
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
		
		// add our tab
		ob_start('ln_txp_commerce_tab');
		
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


function ln_txp_commerce_tab($buffer){
	$find = '<td class="tabdown" onclick="window.location.href=\'?event=page\'">';
	$class = ( in_array($GLOBALS['event'], array('store_categories', 'product', 'products', 'customer', 'customers', 'orders', 'settings', 'store')) ) ? 'tabup' : 'tabdown';
	$replace = '<td class="'.$class.'" onclick="window.location.href=\'?event=store\'"><a href="?event=store" class="plain">Store</a></td>'.$find;

	return str_replace($find, $replace, $buffer);
}




//called by ln_txp_commerce_install(); 
function ln_txp_commerce_install(){
	global $prefs;
	
	$sql_query = ''; // initializing...
	
	// if we don't have the orders table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."orders'") )
	{
		$sql_query[] = "
		
			CREATE TABLE `".PFX."orders` (
			
				`id` int(11) NOT NULL auto_increment,

				`subtotal` double default NULL,

				`user_id` int(11) default NULL,

				`tax` double default NULL,

				`transaction_id` varchar(200) default NULL,

				`tracking_number` varchar(200) default NULL,

				`last_updated` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,

				`order_status` varchar(80) NOT NULL default '',

				`ship_date` date NOT NULL default '0000-00-00',

				`ship_method` varchar(80) NOT NULL default '',

				`memo` varchar(250) NOT NULL default '',

				`note` text NOT NULL,

				`shipping_handling` double NOT NULL default '0',

				`discount` double NOT NULL default '0',

				`total` double NOT NULL default '0',

				`date_created` datetime NOT NULL default '0000-00-00 00:00:00',

				PRIMARY KEY  (`id`)
				
			)";
	}
	
	// if we don't have the orders_articles table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."orders_articles'") )
	{
		$sql_query[] = "
		
			CREATE TABLE `".PFX."orders_articles` (
			
				`order_id` int(11) default NULL,

				`article_id` int(11) default NULL
				
			)";
	}
	
	// if txp_users doesn't have the columns we need, let's create them.
	if ( !getRows("SELECT billing_company FROM `txp_users`") )
	{
		$sql_query[] = "
		
			ALTER TABLE `txp_users` 

				ADD `billing_company` varchar(200) NOT NULL default '',

				ADD `billing_address1` varchar(200) NOT NULL default '',

				ADD `billing_address2` varchar(200) NOT NULL default '',

				ADD `billing_city` varchar(200) NOT NULL default '',

				ADD `billing_state` varchar(50) NOT NULL default '',

				ADD `billing_zip` varchar(14) NOT NULL default '',
					
				ADD `billing_country` varchar(100) NOT NULL default '',

				ADD `billing_fax` varchar(14) NOT NULL default '',

				ADD `billing_phone` varchar(14) NOT NULL default '',

				ADD `shipping_same_as_billing` tinyint(1) NOT NULL default '0',

				ADD `shipping_company` varchar(200) NOT NULL default '',

				ADD `shipping_address1` varchar(200) NOT NULL default '',

				ADD `shipping_address2` varchar(200) NOT NULL default '',

				ADD `shipping_city` varchar(100) NOT NULL default '',

				ADD `shipping_state` varchar(100) NOT NULL default '',

				ADD `shipping_zip` varchar(14) NOT NULL default '',

				ADD `shipping_country` varchar(100) NOT NULL default '',

				ADD `shipping_fax` varchar(20) NOT NULL default '',

				ADD `shipping_phone` varchar(20) NOT NULL default '',

				ADD `shipping_firstname` varchar(100) NOT NULL default '',

				ADD `shipping_lastname` varchar(100) NOT NULL default '',

				ADD `billing_firstname` varchar(100) NOT NULL default '',

				ADD `billing_lastname` varchar(100) NOT NULL default '';";
	}
	
	//DEBUG
	//let's have countries nicely ordered...
	//dmp(getRows("SELECT * FROM countries ORDER BY name",1));
	/*
	foreach ( getRows("SELECT * FROM countries ORDER BY name",1) as $result )
	{
		echo "\$sql_query[] = \"INSERT INTO countries VALUES ('', '".$result['name']."', '".$result['country_code']."')\";<br /><br />";
	}
	*/
	
	// if we don't have the countries table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."countries'") )
	{
		$sql_query[] = "
			
			CREATE TABLE countries (

			  id int(11) NOT NULL auto_increment,

			  name varchar(200) NOT NULL default '',

			  country_code varchar(10) NOT NULL default '',

			  PRIMARY KEY  (id)

			)";
		
		$sql_query[] = "INSERT INTO countries VALUES ('', 'Afghanistan', 'AF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Albania', 'AL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Algeria', 'DZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'American Samoa', 'AS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Andorra', 'AD')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Angola', 'AO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Anguilla', 'AI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Antarctica', 'AQ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Antigua and Barbuda', 'AG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Argentina', 'AR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Armenia', 'AM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Aruba', 'AW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Australia', 'AU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Austria', 'AT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Azerbaijan', 'AZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bahamas', 'BS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bahrain', 'BH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bangladesh', 'BD')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Barbados', 'BB')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Belarus', 'BY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Belgium', 'BE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Belize', 'BZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Benin', 'BJ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bermuda', 'BM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bhutan', 'BT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bolivia', 'BO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bosnia and Herzegowina', 'BA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Botswana', 'BW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bouvet Island', 'BV')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Brazil', 'BR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'British Indian Ocean Territory', 'IO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Brunei Darussalam', 'BN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Bulgaria', 'BG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Burkina Faso', 'BF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Burundi', 'BI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cambodia', 'KH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cameroon', 'CM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Canada', 'CA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cape Verde', 'CV')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cayman Islands', 'KY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Central African Republic', 'CF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Chad', 'TD')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Chile', 'CL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'China', 'CN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Christmas Island', 'CX')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cocos (Keeling) Islands', 'CC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Colombia', 'CO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Comoros', 'KM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Congo', 'CG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cook Islands', 'CK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Costa Rica', 'CR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cote D\'Ivoire', 'CI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Croatia', 'HR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cuba', 'CU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Cyprus', 'CY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Czech Republic', 'CZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Denmark', 'DK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Djibouti', 'DJ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Dominica', 'DM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Dominican Republic', 'DO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'East Timor', 'TP')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Ecuador', 'EC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Egypt', 'EG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'El Salvador', 'SV')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Equatorial Guinea', 'GQ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Eritrea', 'ER')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Estonia', 'EE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Ethiopia', 'ET')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Falkland Islands (Malvinas)', 'FK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Faroe Islands', 'FO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Fiji', 'FJ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Finland', 'FI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'France', 'FR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'France, Metropolitan', 'FX')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'French Guiana', 'GF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'French Polynesia', 'PF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'French Southern Territories', 'TF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Gabon', 'GA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Gambia', 'GM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Georgia', 'GE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Germany', 'DE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Ghana', 'GH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Gibraltar', 'GI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Greece', 'GR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Greenland', 'GL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Grenada', 'GD')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Guadeloupe', 'GP')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Guam', 'GU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Guatemala', 'GT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Guinea', 'GN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Guinea-bissau', 'GW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Guyana', 'GY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Haiti', 'HT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Heard and Mc Donald Islands', 'HM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Honduras', 'HN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Hong Kong', 'HK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Hungary', 'HU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Iceland', 'IS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'India', 'IN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Indonesia', 'ID')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Iran (Islamic Republic of)', 'IR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Iraq', 'IQ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Ireland', 'IE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Israel', 'IL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Italy', 'IT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Jamaica', 'JM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Japan', 'JP')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Jordan', 'JO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Kazakhstan', 'KZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Kenya', 'KE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Kiribati', 'KI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Korea, Democratic People\'s Republic of', 'KP')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Korea, Republic of', 'KR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Kuwait', 'KW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Kyrgyzstan', 'KG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Lao People\'s Democratic Republic', 'LA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Latvia', 'LV')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Lebanon', 'LB')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Lesotho', 'LS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Liberia', 'LR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Libyan Arab Jamahiriya', 'LY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Liechtenstein', 'LI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Lithuania', 'LT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Luxembourg', 'LU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Macau', 'MO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Macedonia, The Former Yugoslav Republic of', 'MK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Madagascar', 'MG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Malawi', 'MW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Malaysia', 'MY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Maldives', 'MV')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mali', 'ML')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Malta', 'MT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Marshall Islands', 'MH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Martinique', 'MQ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mauritania', 'MR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mauritius', 'MU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mayotte', 'YT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mexico', 'MX')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Micronesia, Federated States of', 'FM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Moldova, Republic of', 'MD')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Monaco', 'MC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mongolia', 'MN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Montserrat', 'MS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Morocco', 'MA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Mozambique', 'MZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Myanmar', 'MM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Namibia', 'NA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Nauru', 'NR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Nepal', 'NP')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Netherlands', 'NL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Netherlands Antilles', 'AN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'New Caledonia', 'NC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'New Zealand', 'NZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Nicaragua', 'NI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Niger', 'NE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Nigeria', 'NG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Niue', 'NU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Norfolk Island', 'NF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Northern Mariana Islands', 'MP')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Norway', 'NO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Oman', 'OM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Pakistan', 'PK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Palau', 'PW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Panama', 'PA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Papua New Guinea', 'PG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Paraguay', 'PY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Peru', 'PE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Philippines', 'PH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Pitcairn', 'PN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Poland', 'PL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Portugal', 'PT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Puerto Rico', 'PR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Qatar', 'QA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Reunion', 'RE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Romania', 'RO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Russian Federation', 'RU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Rwanda', 'RW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Saint Kitts and Nevis', 'KN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Saint Lucia', 'LC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Saint Vincent and the Grenadines', 'VC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Samoa', 'WS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'San Marino', 'SM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Sao Tome and Principe', 'ST')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Saudi Arabia', 'SA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Senegal', 'SN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Seychelles', 'SC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Sierra Leone', 'SL')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Singapore', 'SG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Slovakia (Slovak Republic)', 'SK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Slovenia', 'SI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Solomon Islands', 'SB')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Somalia', 'SO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'South Africa', 'ZA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'South Georgia and the South Sandwich Islands', 'GS')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Spain', 'ES')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Sri Lanka', 'LK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'St. Helena', 'SH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'St. Pierre and Miquelon', 'PM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Sudan', 'SD')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Suriname', 'SR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Svalbard and Jan Mayen Islands', 'SJ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Swaziland', 'SZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Sweden', 'SE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Switzerland', 'CH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Syrian Arab Republic', 'SY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Taiwan', 'TW')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Tajikistan', 'TJ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Tanzania, United Republic of', 'TZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Thailand', 'TH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Togo', 'TG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Tokelau', 'TK')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Tonga', 'TO')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Trinidad and Tobago', 'TT')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Tunisia', 'TN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Turkey', 'TR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Turkmenistan', 'TM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Turks and Caicos Islands', 'TC')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Tuvalu', 'TV')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Uganda', 'UG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Ukraine', 'UA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'United Arab Emirates', 'AE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'United Kingdom', 'GB')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'United States', 'USA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'United States Minor Outlying Islands', 'UM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Uruguay', 'UY')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Uzbekistan', 'UZ')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Vanuatu', 'VU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Vatican City State (Holy See)', 'VA')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Venezuela', 'VE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Viet Nam', 'VN')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Virgin Islands (British)', 'VG')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Virgin Islands (U.S.)', 'VI')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Wallis and Futuna Islands', 'WF')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Western Sahara', 'EH')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Yemen', 'YE')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Yugoslavia', 'YU')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Zaire', 'ZR')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Zambia', 'ZM')";

		$sql_query[] = "INSERT INTO countries VALUES ('', 'Zimbabwe', 'ZW')";
		
	}
	
	// if we don't have the currencies table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."currencies'") )
	{
		$sql_query[] = "
		
			CREATE TABLE currencies (

			  id int(11) NOT NULL auto_increment,

			  currency_name varchar(100) NOT NULL default '',

			  currency_code varchar(5) NOT NULL default '',

			  currency_symbol varchar(20) NOT NULL default '',

			  PRIMARY KEY  (id)

			)";
			
		$sql_query[] = "INSERT INTO currencies VALUES (1, 'United States Dollars (USD)', 'USD', '$')";

		$sql_query[] = "INSERT INTO currencies VALUES (2, 'Euro (EUR)', 'EUR', '&euro;')";

		$sql_query[] = "INSERT INTO currencies VALUES (3, 'United Kingdom Pounds (GBP)', 'GBP', '&pound;')";

		$sql_query[] = "INSERT INTO currencies VALUES (4, 'Canada Dollars (CAD)', 'CAD', '$')";

		$sql_query[] = "INSERT INTO currencies VALUES (5, '----------------', '', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (6, 'Argentina Pesos (ARS)', 'ARS', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (7, 'Australia Dollars (AUD)', 'AUD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (8, 'Bahamas (BSD)', 'BSD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (9, 'Brazil Reais (BRL)', 'BRL', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (10, 'Chile Pesos (CLP)', 'CLP', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (11, 'China Yuan Renminbi (CNY)', 'CNY', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (12, 'Costa Rica Colones (CRC)', 'CRC', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (13, 'Cyprus Pounds (CYP)', 'CYP', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (14, 'Czech Republic Koruny (CZK)', 'CZK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (15, 'Denmark Kroner (DKK)', 'DKK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (16, 'Estonia (EEK)', 'EEK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (17, 'Hong Kong Dollars (HKD)', 'HKD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (18, 'Hungary Forint (HUF)', 'HUF', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (19, 'Iceland Krona (ISK)', 'ISK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (20, 'India Rupees (INR)', 'INR', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (21, 'Jamaica Dollars (JMD)', 'JMD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (22, 'Japan Yen (JPY)', 'JPY', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (23, 'Latvia Lati (LVL)', 'LVL', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (24, 'Lithuania Litai (LTL)', 'LTL', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (25, 'Malta Liri (MTL)', 'MTL', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (26, 'Mexico Pesos (MXN)', 'MXN', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (27, 'Malaysia (MYR)', 'MYR', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (28, 'New Zealand Dollars (NZD)', 'NZD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (29, 'Norway Kroner (NOK)', 'NOK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (30, 'Philippine Peso (PHP)', 'PHP', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (31, 'Poland Zlotych (PLN)', 'PLN', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (32, 'Romania New Leu (RON)', 'RON', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (33, 'Singapore Dollars (SGD)', 'SGD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (34, 'Slovakia Koruny (SKK)', 'SKK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (35, 'Slovenia Tolars (SIT)', 'SIT', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (36, 'South Africa Rand (ZAR)', 'ZAR', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (37, 'South Korea Won (KRW)', 'KRW', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (38, 'Sweden Kronor (SEK)', 'SEK', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (39, 'Switzerland Francs (CHF)', 'CHF', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (40, 'Taiwan New Dollars (TWD)', 'TWD', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (41, 'United Arab Emirates Dirham (AED)', 'AED', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (42, 'Uruguay Pesos (UYU)', 'UYU', '')";

		$sql_query[] = "INSERT INTO currencies VALUES (43, 'Venezuela Bolivar (VEB)', 'VEB', '')";
	
	}
	
	// if we don't have the shipping_rates table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."shipping_rates'") )
	{
		$sql_query[] = "
		
			CREATE TABLE shipping_rates (

			  id int(11) NOT NULL auto_increment,

			  title varchar(100) NOT NULL default '',

			  rate decimal(10,0) NOT NULL default '0',

			  start_weight decimal(10,0) NOT NULL default '0',

			  end_weight decimal(10,0) NOT NULL default '0',

			  PRIMARY KEY  (id)

			)";
		
		$sql_query[] = "INSERT INTO shipping_rates VALUES (1, 'Light Product Weight', 10, 0, 5)";

		$sql_query[] = "INSERT INTO shipping_rates VALUES (2, 'Heavy Product Weight', 20, 5, 10)";
	}
	
	// if we don't have the shipping_zones table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."shipping_zones'") )
	{
		$sql_query[] = "
		
			CREATE TABLE shipping_zones (

			  id int(11) NOT NULL auto_increment,

			  name varchar(200) NOT NULL default '',

			  country_id int(11) NOT NULL default '0',

			  parent_zone varchar(200) NOT NULL default '',

			  tax_rate decimal(10,0) NOT NULL default '0',

			  shipping_rate_id int(11) NULL default '0',

			  PRIMARY KEY  (id)

			)";
		
		$sql_query[] = "INSERT INTO shipping_zones VALUES (1, 'United States', 1, '', 0, 1)";
	}
	
	// if we don't have the store_settings table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."store_settings'") )
	{
		$sql_query[] = "
			
			CREATE TABLE store_settings (

			  ssl_url int(11) default NULL,

			  add_to_cart_behavior int(11) default NULL,

			  add_to_cart_section varchar(200) default NULL,

			  cutomer_login_required tinyint(4) default NULL,

			  inventory_management_on tinyint(4) default NULL,

			  hide_inventory_when_depleted tinyint(4) default NULL,

			  show_message_when_depleted tinyint(4) default NULL,

			  depleted_inventory_message varchar(250) default NULL,

			  send_low_inventory_email_notification tinyint(4) default NULL,

			  store_address varchar(150) NOT NULL default '',

			  store_city varchar(200) NOT NULL default '',

			  store_state varchar(100) NOT NULL default '',

			  store_zip varchar(50) NOT NULL default '',

			  store_country varchar(100) NOT NULL default '',

			  owner_email varchar(100) NOT NULL default '',

			  unit_system varchar(100) NOT NULL default '',

			  store_currency varchar(5) NOT NULL default ''

			)";
	}
	
	// if we don't have the zones_rates table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."zones_rates'") )
	{
		$sql_query[] = "
		
			CREATE TABLE zones_rates (

			  shipping_rate_id int(11) NOT NULL default '0',

			  shipping_zone_id int(11) NOT NULL default '0'

			)";
			
		$sql_query[] = "INSERT INTO zones_rates VALUES (1, 1)";

		$sql_query[] = "INSERT INTO zones_rates VALUES (2, 1)";
	}
	
	// if we don't have the product_custom_fields table, let's create it
	if ( !getRows("SHOW TABLES LIKE '".PFX."product_custom_fields'") )
	{
		$sql_query[] = "
		
			CREATE TABLE product_custom_fields (

			  id int(11) NOT NULL auto_increment,

			  article_id int(11) NOT NULL default '0',

			  field_label varchar(200) NOT NULL default '',

			  field_value varchar(200) NOT NULL default '',

			  PRIMARY KEY  (id)

			)";
	}
	
	// if txp_lang doesn't hold the values we need, let's update/create them
	if ( !getRows("SELECT * FROM `".PFX."txp_lang` WHERE `data` LIKE '%Stock%'") )
	{
		$arr_modifications = array (
			'category1' 	=> "Category",
			'category2' 	=> "Vendor"
		);
		while ( list($key, $value) = each($arr_modifications) ) {
			$sql_query[] = "UPDATE `txp_lang` SET `data`='$value' WHERE `name`='$key'";
		}
		$sql_query[] = "INSERT INTO `txp_lang` (`id`,`lang`,`name`,`event`,`data`,`lastmod`) VALUES
						(NULL,'en-gb','price','common','Price',CURRENT_TIMESTAMP)";
		$sql_query[] = "INSERT INTO `txp_lang` (`id`,`lang`,`name`,`event`,`data`,`lastmod`) VALUES
						(NULL,'en-gb','stock','common','Stock',CURRENT_TIMESTAMP)";
	}
	
		//DEBUG
		//if ( $sql_query ) dmp($sql_query);
	
	// if we have any queries, let's run them
	if ( is_array($sql_query) )
	{
		foreach ($sql_query as $query)
		{
			$result = safe_query($query);
			if (!$result) echo "<h3>There was an error with ln_txp_commerce install. Please check the log for more details.</h3>";
		}
	}

}





function show_dashboard($event, $step){

		global $statuses, $comments_disabled_after, $step, $txp_user, $prefs, $general_settings, $currency;

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
					tda($currency.number_format($total,2), ' style="background-color:'.$background.';"')
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
			tda("<strong>Total: $currency".number_format($order_total['total'],2)."</strong>", ' colspan="4" style="background-color:#E8E8EC; text-align:right; border-style:solid; border-color:#CACAD2; border-width:1px 0 0 0;"')
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
function vendor_option_list(){
		$rows = vendor_list();
		$options = "";
		foreach($rows as $vendor){
			$options .= "<option value=\"".$vendor['name']."\">".$vendor['title']."</option>".n;
		}
		return $options;
	}
function vendor_list(){
		$rows = safe_rows("*", "txp_category", "parent = 'Vendors'");
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
function list_search_form_products($crit, $method){
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

function build_list ($name, $table, $valueCol, $displayCol, $selected='', $where='1', $leaveBlankOption = false, $order_by = ''){

	  	$returnData  = "<select name=\"$name\" id=\"$name\">";
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
			if($data[$valueCol] == sanitizeForUrl($selected)){
				$selectOption = " selected=\"selected\"";
			}else{
				$selectOption = "";
			}
			$returnData .= "<option value=\"".$data[$valueCol]."\"$selectOption>".$data[$displayCol]."</option>".n;
  		}
		return $returnData;
}

function send_customer_password($RealName, $name, $email, $password){
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
function status_radio_product($Status){
		global $statuses;

		$Status = (!$Status) ? 4 : $Status;

		foreach ($statuses as $a => $b)
		{
			$out[] = n.t.'<li>'.radio('Status', $a, ($Status == $a) ? 1 : 0, 'status-'.$a).
				'<label for="status-'.$a.'">'.$b.'</label></li>';
		}

		return '<ul class="plain-list">'.join('', $out).n.'</ul>';
}

function category_create($name, $parent='root'){
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
				$q = safe_insert("txp_category", "name='$name', title='$title', type='article', parent='$parent'");
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
		$mediumImage =IMPATH."product-".$ID."-medium".$image_num.".gif";
		imagegif ($thumb, $mediumImage);
		chmod($mediumImage, 0777);

	}
	
	imagejpeg ($im, IMPATH."product-".$ID."-large".$image_num.".jpg");
	chmod(IMPATH."product-".$ID."-large".$image_num.".jpg", 0777);
	
	if($image_num == "" || $image_num == 1){
		safe_update("textpattern", "Image = 'product-".$ID."-medium".$image_num.".gif'", "ID=$ID");

	}else if($image_num == "2"){
		safe_update("textpattern", "custom_6 = 'product-".$ID."-medium".$image_num.".gif'", "ID=$ID");

	}else if($image_num == "3"){
		safe_update("textpattern", "custom_7 = 'images/product-".$ID."-medium".$image_num.".gif'", "ID=$ID");

	}else if($image_num == "4"){
		safe_update("textpattern", "custom_8 = 'images/product-".$ID."-medium".$image_num.".gif'", "ID=$ID");
	}
	
}




function doJS(){


echo n.'<script type="text/javascript">'.
			  n.'<!--'.

			 n.'function deleteCustomer(){'.
			 n.'	if(confirm("Are you sure?")){'.
			 n.'		document.getElementById("customerDelete").submit(); '.
			 n.'	}'.
			 n.'}'.
			 n.'//-->'.
			 n.'</script>';


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







echo n.'<script type="text/javascript">'.
			  n.'<!--'.

			 n.'function deleteCustomer(){'.
			 n.'	if(confirm("Are you sure?")){'.
			 n.'		document.getElementById("customerDelete").submit(); '.
			 n.'	}'.
			 n.'}'.
			 n.'//-->'.
			 n.'</script>';
			 
			 
			 
	echo n.'<script type="text/javascript">'.
			 n.'<!--'.
			 //n.'	fieldNum = '.$startFieldNum.';'.
			 n.'	function addCustomField(index){'.
			 n.'		$("#custom_fields").append(getCustomField(fieldNum))'.
			 n.'		fieldNum = fieldNum+1;'.
			 n.'	}'.

			 n.'	function deleteCustomField(index){'.
			 n.'		if(confirm("Are you sure?")){'.
			 n.'			$("#custom_field_"+index).hide();'.
			 n.'			$("#custom_fields["+index+"][value]").val("delete");'.
			 n.'			$("#custom_fields["+index+"][label]").val("delete");'.
			 n.'		}else{'.
			 n.'			return false;'.
			 n.'		}'.
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
			 n.'		deleteLink.setAttribute("style","font-size:11px;");'.
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

echo '

					<script type="text/javascript">
					<!--
						function deleteImage(id){
							if(confirm("Are you sure?")){
								$("#image"+id).hide();
								$("#imageField"+id).val("delete");
								document.forms["product"].submit();
							}
						}
						function updateImage(id){
							$("#imageUpload"+id).slideDown();
							$("#updateImage"+id).val($("#imageField"+id).val());
							$("#imageField"+id).val("delete");
						}
						function cancelUpload(id){
							$("#imageUpload"+id).slideUp();
							$("#imageField"+id).val($("#updateImage"+id).val());
						}
					-->
					</script>

				';


			 	echo n.'<script type="text/javascript">'.
			  n.'<!--'.

				 n.'function showTab(tabID, fieldsetID){'.
  			 n.'	$("#subNav li").each(function(){'.
  			 n.'		if(this.id == tabID){'.
  			 n.'			$(this).addClass("selected");'.
  			 n.'		}else{'.
  			 n.'			$(this).removeClass("selected");'.
  			 n.'		}'.
  			 n.'	});'.
  
  			 n.'	$(".settingsRegion").each(function(){'.
  			 n.'		if(this.id == fieldsetID){'.
  			 n.'			$(this).show();'.
  			 n.'		}else{'.
  			 n.'			$(this).hide();'.
  			 n.'		}'.
  			 n.'	});'.
  			 n.'}'.
  
  			 n.'function checkCountry(){'.
  			 n.'	if($("#store_country").value == "add_new"){'.
  			 n.'		$("#addCountry").show();'.
  			 n.'	} else {'.
  			 n.'		$("#addCountry").hide();'.
  			 n.'	}'.
  			 n.'}'.
  
  			 n.'function deleteZone(id){'.
  			 n.'	if(confirm("Are you sure?")){'.
  			 n.'		$("#zone_"+id+"_action").val("delete_zone");'.
  			 n.'		$("#zone_"+id+"_form").submit()'.
  			 n.'	}'.
  			 n.'}'.
  
  			 n.'function deleteRate(id){'.
  			 n.'	if(confirm("Are you sure?")){'.
  			 n.'		$("#rates_"+id+"_action").val("delete_rate");'.
  			 n.'		$("#rates_"+id+"_form").submit()'.
  			 n.'	}'.
  			 n.'}'.

			 n.'//-->'.
			 n.'</script>';
			 
			 

}

?>
